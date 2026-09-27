<?php

declare(strict_types=1);

namespace Drupal\vvjh\Plugin\views\style;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views\Attribute\ViewsStyle;
use Drupal\views\Plugin\views\field\EntityField;
use Drupal\vvj_core\Constants\AnimationDirection;
use Drupal\vvj_core\Plugin\views\style\VvjStylePluginBase;
use Drupal\vvjh\VvjhConstants;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders Views rows as a vanilla-JavaScript hero section.
 *
 * @ingroup views_style_plugins
 */
#[ViewsStyle(
  id: 'views_vvjh',
  title: new TranslatableMarkup('Views Vanilla JavaScript Hero'),
  help: new TranslatableMarkup('Render items in a Hero using vanilla JavaScript.'),
  theme: 'views_view_vvjh',
  display_types: ['normal'],
)]
class Hero extends VvjStylePluginBase {

  /**
   * {@inheritdoc}
   *
   * In vvjh 1.0, `min_height` was a bare number, rendered as
   * `{{ min_height }}vw`. From late 1.x it is `{value, unit}`, and v2 treats a
   * bare number as missing and falls back to its default height, losing the
   * editor's value. Convert it with the unit 1.0 always used.
   */
  public static function upgradeLegacyOptions(array $options): array {
    $min_height = $options['min_height'] ?? NULL;
    if (is_scalar($min_height) && is_numeric($min_height)) {
      $options['min_height'] = [
        'value' => (int) $min_height,
        'unit' => VvjhConstants::UNIT_VW,
      ];
    }
    return $options;
  }

  /**
   * Field type lookups for validate() (first field must be an image field).
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
    $entityFieldManager = $container->get('entity_field.manager');
    assert($entityFieldManager instanceof EntityFieldManagerInterface);
    $instance->entityFieldManager = $entityFieldManager;
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getModuleSlug(): string {
    return 'vvjh';
  }

  /**
   * {@inheritdoc}
   */
  public function getCustomElementTag(): string {
    return 'vvjh-hero';
  }

  /**
   * Restore v1's pattern-specific "Enable CSS Library" help text.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The description.
   */
  protected function getEnableCssDescription(): TranslatableMarkup {
    return $this->t(
      'Check this box to include the CSS library for styling the hero elements.',
    );
  }

  /**
   * {@inheritdoc}
   *
   * Hero is a static decorative element — no panels/slides to deep-link to.
   */
  protected function supportsDeeplinking(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   *
   * Hero offers six animation styles (omits "None" — heroes always animate).
   *
   * @return array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   *   Preset key → translatable label.
   */
  protected function getAnimationPresets(): array {
    return [
      AnimationDirection::FADE => $this->t('Fade'),
      AnimationDirection::ZOOM => $this->t('Zoom'),
      AnimationDirection::TOP => $this->t('Slide from Top'),
      AnimationDirection::RIGHT => $this->t('Slide from Right'),
      AnimationDirection::BOTTOM => $this->t('Slide from Bottom'),
      AnimationDirection::LEFT => $this->t('Slide from Left'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function definePatternOptions(array $options): array {
    $options['overlay_position'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_POSITION];
    $options['hero_style'] = ['default' => VvjhConstants::DEFAULT_HERO_STYLE];
    $options['hero_speed'] = ['default' => VvjhConstants::DEFAULT_HERO_SPEED];
    $options['animation_easing'] = ['default' => VvjhConstants::DEFAULT_ANIMATION_EASING];
    $options['overlay_bg_color'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_BG_COLOR];
    $options['overlay_bg_opacity'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_BG_OPACITY];
    $options['available_breakpoints'] = ['default' => VvjhConstants::DEFAULT_BREAKPOINT];
    $options['min_height'] = [
      'default' => [
        'value' => VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
        'unit' => VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
      ],
    ];
    $options['max_content_width'] = ['default' => VvjhConstants::DEFAULT_MAX_CONTENT_WIDTH];
    $options['max_width'] = ['default' => VvjhConstants::DEFAULT_MAX_WIDTH];
    $options['hero_item_width'] = ['default' => VvjhConstants::DEFAULT_HERO_ITEM_WIDTH];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildPatternSections(array &$form): void {
    $this->buildLayoutSection($form);
    $this->buildPositionSection($form);
    $this->buildAnimationSection($form);
    $this->buildStyleSection($form);
    $this->buildResponsiveSection($form);
  }

  /**
   * Layout section — max width, min height, content width, item width.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildLayoutSection(array &$form): void {
    $form['layout_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Layout Settings'),
      '#open' => TRUE,
      '#weight' => -40,
    ];

    $form['layout_section']['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Width (px)'),
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('Defines the maximum width for the main container of the hero content, typically set in pixels. For example, setting this value to 1200px will ensure that the hero content does not exceed this width, regardless of the screen size.'),
      '#step' => 1,
      '#min' => VvjhConstants::MIN_MAX_WIDTH,
      '#required' => TRUE,
    ];

    $form['layout_section']['min_height'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Minimum Height'),
      '#description' => $this->t("Define the minimum height of the hero container by entering a value and selecting a unit. Choose from the following units:
        <ul>
          <li><strong>Viewport Width (vw):</strong> 1vw is equal to 1 percent of the viewport's width. For example, if the viewport is 1000px wide, 10vw will equal 100px.</li>
          <li><strong>Viewport Height (vh):</strong> 1vh is equal to 1 percent of the viewport's height. For example, if the viewport is 1000px high, 10vh will equal 100px.</li>
          <li><strong>Pixels (px):</strong> Specifies an exact number of pixels for the height, regardless of the viewport size.</li>
          <li><strong>Ems (em):</strong> Relative to the font size of the element. For example, if the font size is 16px, 1em equals 16px.</li>
          <li><strong>Root Ems (rem):</strong> Relative to the font size of the root element (typically <code>&lt;html&gt;</code>). For example, if the root font size is 16px, 1rem equals 16px.</li>
        </ul>"),
      '#attributes' => ['class' => ['min-height-wrapper']],
    ];

    $raw_min_height = $this->options['min_height'] ?? NULL;
    $min_height = is_array($raw_min_height) ? $raw_min_height : [
      'value' => VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
      'unit' => VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
    ];

    $form['layout_section']['min_height']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $min_height['value'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
      '#description' => $this->t('Enter the numeric value for the minimum height.'),
      '#attributes' => ['style' => 'width: 150px; display: inline-block;'],
    ];

    $form['layout_section']['min_height']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit (vw, vh, px, em, rem)'),
      '#description' => $this->t('Select the unit for the minimum height.'),
      '#default_value' => $min_height['unit'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
      '#options' => [
        VvjhConstants::UNIT_VW => $this->t('Viewport Width (vw)'),
        VvjhConstants::UNIT_VH => $this->t('Viewport Height (vh)'),
        VvjhConstants::UNIT_PX => $this->t('Pixels (px)'),
        VvjhConstants::UNIT_EM => $this->t('Ems (em)'),
        VvjhConstants::UNIT_REM => $this->t('Root Ems (rem)'),
      ],
      '#attributes' => ['style' => 'width: 150px; display: inline-block;'],
    ];

    $form['layout_section']['max_content_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Content Width (%)'),
      '#default_value' => $this->options['max_content_width'],
      '#description' => $this->t("Determines the maximum width for the remaining fields (content) within the hero section, expressed as a percentage of the main layout's width. For example, setting this to 50 percent means that the content will occupy up to half of the hero's main container width."),
      '#step' => 1,
      '#min' => VvjhConstants::MIN_MAX_CONTENT_WIDTH,
      '#required' => TRUE,
    ];

    $form['layout_section']['hero_item_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Hero Card Width (px)'),
      '#default_value' => $this->options['hero_item_width'],
      '#description' => $this->t('Set the width of each Hero Card in pixels. Use this setting to display hero items as Hero Cards or a list. If set to zero, each Hero Card will expand to the full available width, creating a list layout. Otherwise, specifying a width will display each item as a Hero Card with the specified width. Note: To use this feature, you must have more than one item.'),
      '#step' => 1,
      '#min' => VvjhConstants::MIN_HERO_ITEM_WIDTH,
      '#required' => TRUE,
    ];
  }

  /**
   * Position section — overlay placement.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildPositionSection(array &$form): void {
    $form['position_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Position Settings'),
      '#open' => TRUE,
      '#weight' => -30,
    ];

    $form['position_section']['overlay_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Overlay Position'),
      '#options' => [
        VvjhConstants::POSITION_MIDDLE => $this->t('Middle'),
        VvjhConstants::POSITION_LEFT => $this->t('Left'),
        VvjhConstants::POSITION_RIGHT => $this->t('Right'),
        VvjhConstants::POSITION_TOP => $this->t('Top'),
        VvjhConstants::POSITION_BOTTOM => $this->t('Bottom'),
        VvjhConstants::POSITION_TOP_LEFT => $this->t('Top Left'),
        VvjhConstants::POSITION_TOP_RIGHT => $this->t('Top Right'),
        VvjhConstants::POSITION_BOTTOM_LEFT => $this->t('Bottom Left'),
        VvjhConstants::POSITION_BOTTOM_RIGHT => $this->t('Bottom Right'),
        VvjhConstants::POSITION_TOP_MIDDLE => $this->t('Top Middle'),
        VvjhConstants::POSITION_BOTTOM_MIDDLE => $this->t('Bottom Middle'),
      ],
      '#default_value' => $this->options['overlay_position'],
      '#description' => $this->t('Select the position where the content overlay will appear within the hero section. This controls where text and other elements will be placed on top of the background image. <strong>Due to limitations in CSS, sliding animations may not center content as expected. Consider using alternative animations or positioning methods for better results. Middle positioning may not work correctly with slide animations.</strong>'),
      '#required' => TRUE,
    ];
  }

  /**
   * Animation section — style, speed, easing.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildAnimationSection(array &$form): void {
    $form['animation_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation Settings'),
      '#open' => TRUE,
      '#weight' => -20,
    ];

    // Assign (do not `+`-union) the pattern-specific keys: array union keeps
    // the LEFT operand's value for shared keys, so the base select's generic
    // '#title'/'#description' would win and this pattern's help text would be
    // silently discarded.
    $hero_style = $this->buildAnimationSelect('hero_style');
    $hero_style['#title'] = $this->t('Hero Style');
    $hero_style['#description'] = $this->t('Choose the animation style for how the hero section content will appear. Different styles include fading, zooming, or sliding in from various directions. Reduced-motion users see no animation regardless.');
    $hero_style['#required'] = TRUE;
    $form['animation_section']['hero_style'] = $hero_style;

    $form['animation_section']['hero_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Hero Animation Speed'),
      '#default_value' => $this->options['hero_speed'],
      '#description' => $this->t('Set the speed at which the hero section content animation occurs. Enter a value between 0.1 (fast) and 2 (slow) to control the animation speed.'),
      '#step' => VvjhConstants::HERO_SPEED_STEP,
      '#min' => VvjhConstants::MIN_HERO_SPEED,
      '#max' => VvjhConstants::MAX_HERO_SPEED,
      '#required' => TRUE,
    ];

    $form['animation_section']['animation_easing'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation Easing'),
      '#options' => [
        VvjhConstants::EASING_EASE => $this->t('Ease'),
        VvjhConstants::EASING_LINEAR => $this->t('Linear'),
        VvjhConstants::EASING_EASE_IN => $this->t('Ease-in'),
        VvjhConstants::EASING_EASE_OUT => $this->t('Ease-out'),
        VvjhConstants::EASING_EASE_IN_OUT => $this->t('Ease-in-out'),
      ],
      '#default_value' => $this->options['animation_easing'],
      '#description' => $this->t('Choose the easing function for the hero section content animation. Easing controls the acceleration and deceleration of the animation for a smoother effect.'),
      '#required' => TRUE,
    ];
  }

  /**
   * Style section — overlay background color and opacity.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildStyleSection(array &$form): void {
    $form['style_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Style Settings'),
      '#open' => TRUE,
      '#weight' => -10,
    ];

    $form['style_section']['overlay_bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Overlay Background Color'),
      '#default_value' => $this->options['overlay_bg_color'],
      '#description' => $this->t('Choose the background color for the overlay that appears behind the content within the hero section. This helps improve the readability of the overlay content.'),
    ];

    $raw_opacity = $this->options['overlay_bg_opacity'] ?? VvjhConstants::DEFAULT_OVERLAY_BG_OPACITY;
    $opacity_string = is_scalar($raw_opacity) ? (string) $raw_opacity : '';
    $form['style_section']['overlay_bg_opacity'] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Background Opacity'),
      '#default_value' => $this->options['overlay_bg_opacity'],
      '#min' => VvjhConstants::MIN_OPACITY,
      '#max' => VvjhConstants::MAX_OPACITY,
      '#step' => VvjhConstants::OPACITY_STEP,
      '#description' => $this->t('Adjust the opacity of the overlay background color for the hero section content. A lower value makes the background more transparent, while a higher value makes it more opaque.'),
      '#suffix' => '<span id="background-opacity-value">' . $opacity_string . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("background-opacity-value").innerText = this.value;',
      ],
    ];
  }

  /**
   * Responsive section — breakpoint at which hero animation activates.
   *
   * @param array<int|string, mixed> $form
   *   The form, modified in place.
   */
  protected function buildResponsiveSection(array &$form): void {
    $form['responsive_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Settings'),
      '#open' => FALSE,
      '#weight' => 0,
    ];

    // Assign (do not `+`-union) the pattern-specific keys: array union keeps
    // the LEFT operand's value for shared keys, so `+ ['#options' => …]`
    // would be silently discarded and the base registry's options (incl. an
    // 'all' value with no backing vvjh library) would leak. v1 offered
    // exactly these 5 breakpoints.
    $breakpoint_select = $this->buildBreakpointSelect(
      'available_breakpoints',
      VvjhConstants::DEFAULT_BREAKPOINT,
    );
    $breakpoint_select['#title'] = $this->t('Available Breakpoints for Hero');
    $breakpoint_select['#description'] = $this->t('Select the maximum screen width (in pixels) at which the Hero should be disabled.');
    $breakpoint_select['#options'] = [
      '576' => $this->t('576 px / 36 rem'),
      '768' => $this->t('768 px / 48 rem'),
      '992' => $this->t('992 px / 62 rem'),
      '1200' => $this->t('1200 px / 75 rem'),
      '1400' => $this->t('1400 px / 87.5 rem'),
    ];
    $breakpoint_select['#required'] = TRUE;
    $form['responsive_section']['available_breakpoints'] = $breakpoint_select;
  }

  /**
   * {@inheritdoc}
   *
   * Add the breakpoint-specific responsive library on top of the
   * shared library list.
   *
   * @return list<string>
   *   Library identifiers.
   */
  protected function buildLibraryList(): array {
    $libraries = parent::buildLibraryList();
    $raw_breakpoint = $this->options['available_breakpoints'] ?? VvjhConstants::DEFAULT_BREAKPOINT;
    $breakpoint = is_scalar($raw_breakpoint)
      ? (string) $raw_breakpoint
      : VvjhConstants::DEFAULT_BREAKPOINT;
    $libraries[] = 'vvjh/vvjh__' . $breakpoint;
    return $libraries;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<int|string, mixed> $form
   *   The Views style options form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state): void {
    $values = $form_state->getValue('style_options');
    if (!is_array($values)) {
      $values = [];
    }
    $form_state->setValue('style_options', $this->flattenFormValues($values));
    parent::submitOptionsForm($form, $form_state);
  }

  /**
   * Flatten the sectioned form values back into the persisted shape.
   *
   * @param array<int|string, mixed> $values
   *   Submitted form values keyed by section then by option name.
   *
   * @return array<string, mixed>
   *   Flattened option array.
   */
  protected function flattenFormValues(array $values): array {
    $flattened = [];

    $layout = $values['layout_section'] ?? NULL;
    if (is_array($layout)) {
      $flattened['max_width'] = $layout['max_width'] ?? VvjhConstants::DEFAULT_MAX_WIDTH;
      $flattened['max_content_width'] = $layout['max_content_width'] ?? VvjhConstants::DEFAULT_MAX_CONTENT_WIDTH;
      $flattened['hero_item_width'] = $layout['hero_item_width'] ?? VvjhConstants::DEFAULT_HERO_ITEM_WIDTH;
      $min_height = $layout['min_height'] ?? NULL;
      if (is_array($min_height)) {
        $flattened['min_height'] = [
          'value' => $min_height['value'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
          'unit' => $min_height['unit'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
        ];
      }
    }

    $position = $values['position_section'] ?? NULL;
    if (is_array($position)) {
      $flattened['overlay_position'] = $position['overlay_position'] ?? VvjhConstants::DEFAULT_OVERLAY_POSITION;
    }

    $animation = $values['animation_section'] ?? NULL;
    if (is_array($animation)) {
      $flattened['hero_style'] = $animation['hero_style'] ?? VvjhConstants::DEFAULT_HERO_STYLE;
      $flattened['hero_speed'] = $animation['hero_speed'] ?? VvjhConstants::DEFAULT_HERO_SPEED;
      $flattened['animation_easing'] = $animation['animation_easing'] ?? VvjhConstants::DEFAULT_ANIMATION_EASING;
    }

    $style = $values['style_section'] ?? NULL;
    if (is_array($style)) {
      $flattened['overlay_bg_color'] = $style['overlay_bg_color'] ?? VvjhConstants::DEFAULT_OVERLAY_BG_COLOR;
      $flattened['overlay_bg_opacity'] = $style['overlay_bg_opacity'] ?? VvjhConstants::DEFAULT_OVERLAY_BG_OPACITY;
    }

    $responsive = $values['responsive_section'] ?? NULL;
    if (is_array($responsive)) {
      $flattened['available_breakpoints'] = $responsive['available_breakpoints'] ?? VvjhConstants::DEFAULT_BREAKPOINT;
    }

    $advanced = $values['advanced_section'] ?? NULL;
    if (is_array($advanced)) {
      $flattened['enable_css'] = $advanced['enable_css'] ?? TRUE;
    }

    $flattened['unique_id'] = $this->options['unique_id'] ?? $this->generateUniqueId();

    return $flattened;
  }

  /**
   * {@inheritdoc}
   *
   * Hero requires Fields row style AND the first field must be an image.
   *
   * @return list<string|\Drupal\Core\StringTranslation\TranslatableMarkup>
   *   Validation errors. Empty list = valid.
   */
  public function validate(): array {
    $errors = parent::validate();

    if (!$this->usesFields()) {
      return $errors;
    }

    $fields = $this->view->display_handler->getHandlers('field');
    if (empty($fields)) {
      $errors[] = (string) $this->t('Views Hero requires at least one field to be configured.');
      return $errors;
    }

    $first_field = reset($fields);
    if (!($first_field instanceof EntityField)) {
      $errors[] = (string) $this->t('Views Hero requires the first field to be an Image field.');
      return $errors;
    }

    $raw_field_name = $first_field->definition['field_name'] ?? NULL;
    $field_name = is_string($raw_field_name) ? $raw_field_name : NULL;
    if ($field_name === NULL) {
      $errors[] = (string) $this->t('Views Hero requires the first field to be an Image field.');
      return $errors;
    }

    $base_entity_type = $this->view->getBaseEntityType();
    if (!$base_entity_type) {
      return $errors;
    }
    $entity_type_id = $base_entity_type->id();
    $field_storage_definitions = $this->entityFieldManager->getFieldStorageDefinitions(
      $entity_type_id,
    );
    $is_image = isset($field_storage_definitions[$field_name])
      && $field_storage_definitions[$field_name]->getType() === 'image';

    if (!$is_image) {
      $errors[] = (string) $this->t('Views Hero requires the first field to be an Image field. Please add an image field as the first field in your Fields configuration.');
    }

    return $errors;
  }

}
