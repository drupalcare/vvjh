<?php

declare(strict_types=1);

namespace Drupal\vvjh\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\vvjh\VvjhConstants;

/**
 * Style plugin to render items in a Hero using vanilla JavaScript.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_vvjh",
 *   title = @Translation("Views Vanilla JavaScript Hero"),
 *   help = @Translation("Render items in a Hero using vanilla JavaScript."),
 *   theme = "views_view_vvjh",
 *   display_types = { "normal" }
 * )
 */
class ViewsVanillaJavascriptHero extends StylePluginBase {

  /**
   * Indicates if the style plugin uses a row plugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * {@inheritdoc}
   */
  protected $usesRowClass = TRUE;

  /**
   * Cached unique ID for this view display.
   *
   * @var int|null
   */
  protected ?int $cachedUniqueId = NULL;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['overlay_position'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_POSITION];
    $options['hero_style'] = ['default' => VvjhConstants::DEFAULT_HERO_STYLE];
    $options['hero_speed'] = ['default' => VvjhConstants::DEFAULT_HERO_SPEED];
    $options['overlay_bg_color'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_BG_COLOR];
    $options['overlay_bg_opacity'] = ['default' => VvjhConstants::DEFAULT_OVERLAY_BG_OPACITY];
    $options['available_breakpoints'] = ['default' => VvjhConstants::DEFAULT_BREAKPOINT];
    $options['animation_easing'] = ['default' => VvjhConstants::DEFAULT_ANIMATION_EASING];
    $options['enable_css'] = ['default' => VvjhConstants::DEFAULT_ENABLE_CSS];
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
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $this->setDefaultElementWeights($form);
    $this->buildWarningMessage($form);
    $this->buildLayoutSection($form);
    $this->buildPositionSection($form);
    $this->buildAnimationSection($form);
    $this->buildStyleSection($form);
    $this->buildResponsiveSection($form);
    $this->buildAdvancedSection($form);
    $this->buildTokenDocumentation($form);
  }

  /**
   * Set weights for default Drupal form elements.
   */
  protected function setDefaultElementWeights(array &$form): void {
    $default_elements = [
      'grouping' => -100,
      'row_class' => -90,
      'default_row_class' => -85,
      'uses_fields' => -80,
      'class' => -75,
      'wrapper_class' => -70,
    ];

    foreach ($default_elements as $element_key => $weight) {
      if (isset($form[$element_key])) {
        $form[$element_key]['#weight'] = $weight;
      }
    }
  }

  /**
   * Build warning message section.
   */
  protected function buildWarningMessage(array &$form): void {
    if ($this->view->storage->id() === 'vvjh_example') {
      return;
    }
    $form['warning_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="messages messages--warning">' . $this->t(
          'Note: The hero component requires the first field to be an image. When the layout is open, the image will automatically expand to 100 percent of the available width, ensuring full coverage of the hero section. The remaining fields will be used as the hero content. To see an example, check the vvjh_example view by clicking <a href="@url">here</a> to edit it.', [
            '@url' => Url::fromRoute('entity.view.edit_form', ['view' => 'vvjh_example'])->toString(),
          ]
      ) . '</div>',
      '#weight' => -50,
    ];
  }

  /**
   * Build layout configuration section.
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

    $form["layout_section"]["min_height"] = [
      "#type" => "fieldset",
      "#title" => $this->t("Minimum Height"),
      "#description" => $this->t("Define the minimum height of the hero container by entering a value and selecting a unit. Choose from the following units:
        <ul>
          <li><strong>Viewport Width (vw):</strong> 1vw is equal to 1 percent of the viewport's width. For example, if the viewport is 1000px wide, 10vw will equal 100px.</li>
          <li><strong>Viewport Height (vh):</strong> 1vh is equal to 1 percent of the viewport's height. For example, if the viewport is 1000px high, 10vh will equal 100px.</li>
          <li><strong>Pixels (px):</strong> Specifies an exact number of pixels for the height, regardless of the viewport size.</li>
          <li><strong>Ems (em):</strong> Relative to the font size of the element. For example, if the font size is 16px, 1em equals 16px.</li>
          <li><strong>Root Ems (rem):</strong> Relative to the font size of the root element (typically <code>&lt;html&gt;</code>). For example, if the root font size is 16px, 1rem equals 16px.</li>
        </ul>"),
      "#attributes" => [
        "class" => ["min-height-wrapper"],
      ],
    ];

    $min_height = $this->options['min_height'] ?? [
      'value' => VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
      'unit' => VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
    ];

    $form['layout_section']['min_height']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $min_height['value'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
      '#description' => $this->t('Enter the numeric value for the minimum height.'),
      '#attributes' => [
        'style' => 'width: 150px; display: inline-block;',
      ],
    ];

    $form['layout_section']['min_height']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit (vw, vh, px, em, rem)'),
      '#default_value' => $min_height['unit'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
      '#options' => [
        VvjhConstants::UNIT_VW => $this->t('Viewport Width (vw)'),
        VvjhConstants::UNIT_VH => $this->t('Viewport Height (vh)'),
        VvjhConstants::UNIT_PX => $this->t('Pixels (px)'),
        VvjhConstants::UNIT_EM => $this->t('Ems (em)'),
        VvjhConstants::UNIT_REM => $this->t('Root Ems (rem)'),
      ],
      '#attributes' => [
        'style' => 'width: 150px; display: inline-block;',
      ],
      '#description' => $this->t('Select the unit for the minimum height.'),
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
   * Build position configuration section.
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
   * Build animation configuration section.
   */
  protected function buildAnimationSection(array &$form): void {
    $form['animation_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation Settings'),
      '#open' => TRUE,
      '#weight' => -20,
    ];

    $form['animation_section']['hero_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Hero Style'),
      '#options' => [
        VvjhConstants::STYLE_FADE => $this->t('Fade'),
        VvjhConstants::STYLE_ZOOM => $this->t('Zoom'),
        VvjhConstants::STYLE_TOP => $this->t('Slide from Top'),
        VvjhConstants::STYLE_RIGHT => $this->t('Slide from Right'),
        VvjhConstants::STYLE_BOTTOM => $this->t('Slide from Bottom'),
        VvjhConstants::STYLE_LEFT => $this->t('Slide from Left'),
      ],
      '#default_value' => $this->options['hero_style'],
      '#description' => $this->t('Choose the animation style for how the hero section content will appear. Different styles include fading, zooming, or sliding in from various directions.'),
      '#required' => TRUE,
    ];

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
   * Build style configuration section.
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

    $form['style_section']['overlay_bg_opacity'] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Background Opacity'),
      '#default_value' => $this->options['overlay_bg_opacity'],
      '#min' => VvjhConstants::MIN_OPACITY,
      '#max' => VvjhConstants::MAX_OPACITY,
      '#step' => VvjhConstants::OPACITY_STEP,
      '#description' => $this->t('Adjust the opacity of the overlay background color for the hero section content. A lower value makes the background more transparent, while a higher value makes it more opaque.'),
      '#suffix' => '<span id="background-opacity-value">' . $this->options['overlay_bg_opacity'] . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("background-opacity-value").innerText = this.value;',
      ],
    ];
  }

  /**
   * Build responsive configuration section.
   */
  protected function buildResponsiveSection(array &$form): void {
    $form['responsive_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive Settings'),
      '#open' => FALSE,
      '#weight' => 0,
    ];

    $form['responsive_section']['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Hero'),
      '#options' => [
        VvjhConstants::BREAKPOINT_576 => $this->t('576px / 36rem'),
        VvjhConstants::BREAKPOINT_768 => $this->t('768px / 48rem'),
        VvjhConstants::BREAKPOINT_992 => $this->t('992px / 62rem'),
        VvjhConstants::BREAKPOINT_1200 => $this->t('1200px / 75rem'),
        VvjhConstants::BREAKPOINT_1400 => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the Hero should be disabled.'),
      '#required' => TRUE,
    ];
  }

  /**
   * Build advanced options section.
   */
  protected function buildAdvancedSection(array &$form): void {
    $form['advanced_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced Options'),
      '#open' => FALSE,
      '#weight' => 10,
    ];

    $form['advanced_section']['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the hero elements.'),
    ];
  }

  /**
   * Build token documentation section.
   */
  protected function buildTokenDocumentation(array &$form): void {
    $form['token_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Token Documentation'),
      '#open' => FALSE,
      '#weight' => 100,
    ];

    $form['token_section']['description'] = [
      '#markup' => $this->t('<p>When using <em>Global: Text area</em> or <em>Global: Unfiltered text</em> in the Views header, footer, or empty text areas, the default Twig-style tokens (e.g., <code>{{ title }}</code>) will not work with the VVJH style plugin.</p>
        <p>Instead, use the custom VVJH token format to access field values from the <strong>first row</strong> of the View result:</p>
        <ul>
          <li><code>[vvjh:field_name]</code> — The rendered output of the field (e.g., linked title, image, formatted text).</li>
          <li><code>[vvjh:field_name:plain]</code> — A plain-text version of the field, with all HTML stripped.</li>
        </ul>
        <p>Examples:</p>
        <ul>
          <li><code>{{ title }}</code> ➜ <code>[vvjh:title]</code></li>
          <li><code>{{ field_image }}</code> ➜ <code>[vvjh:field_image]</code></li>
          <li><code>{{ body }}</code> ➜ <code>[vvjh:body:plain]</code></li>
        </ul>
        <p>These tokens offer safe and flexible field output for dynamic headings, summaries, and fallback messages in VVJH-enabled Views.</p>'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state): void {
    $values = $form_state->getValue('style_options');
    $flattened_values = $this->flattenFormValues($values);
    $form_state->setValue('style_options', $flattened_values);

    parent::submitOptionsForm($form, $form_state);
  }

  /**
   * Flatten nested form values to match original structure.
   */
  protected function flattenFormValues(array $values): array {
    $flattened = [];

    if (isset($values['layout_section'])) {
      $flattened['max_width'] = $values['layout_section']['max_width'] ?? VvjhConstants::DEFAULT_MAX_WIDTH;
      $flattened['max_content_width'] = $values['layout_section']['max_content_width'] ?? VvjhConstants::DEFAULT_MAX_CONTENT_WIDTH;
      $flattened['hero_item_width'] = $values['layout_section']['hero_item_width'] ?? VvjhConstants::DEFAULT_HERO_ITEM_WIDTH;

      if (isset($values['layout_section']['min_height'])) {
        $flattened['min_height'] = [
          'value' => $values['layout_section']['min_height']['value'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_VALUE,
          'unit' => $values['layout_section']['min_height']['unit'] ?? VvjhConstants::DEFAULT_MIN_HEIGHT_UNIT,
        ];
      }
    }

    if (isset($values['position_section'])) {
      $flattened['overlay_position'] = $values['position_section']['overlay_position'] ?? VvjhConstants::DEFAULT_OVERLAY_POSITION;
    }

    if (isset($values['animation_section'])) {
      $flattened['hero_style'] = $values['animation_section']['hero_style'] ?? VvjhConstants::DEFAULT_HERO_STYLE;
      $flattened['hero_speed'] = $values['animation_section']['hero_speed'] ?? VvjhConstants::DEFAULT_HERO_SPEED;
      $flattened['animation_easing'] = $values['animation_section']['animation_easing'] ?? VvjhConstants::DEFAULT_ANIMATION_EASING;
    }

    if (isset($values['style_section'])) {
      $flattened['overlay_bg_color'] = $values['style_section']['overlay_bg_color'] ?? VvjhConstants::DEFAULT_OVERLAY_BG_COLOR;
      $flattened['overlay_bg_opacity'] = $values['style_section']['overlay_bg_opacity'] ?? VvjhConstants::DEFAULT_OVERLAY_BG_OPACITY;
    }

    if (isset($values['responsive_section'])) {
      $flattened['available_breakpoints'] = $values['responsive_section']['available_breakpoints'] ?? VvjhConstants::DEFAULT_BREAKPOINT;
    }

    if (isset($values['advanced_section'])) {
      $flattened['enable_css'] = $values['advanced_section']['enable_css'] ?? VvjhConstants::DEFAULT_ENABLE_CSS;
    }

    $flattened['unique_id'] = $this->options['unique_id'] ?? $this->generateUniqueId();

    return $flattened;
  }

  /**
   * Generates a unique numeric ID for the view display.
   *
   * @return int
   *   A unique ID between 10000000 and 99999999.
   *
   * @throws \Exception
   *   If an appropriate source of randomness cannot be found.
   */
  protected function generateUniqueId(): int {
    if ($this->cachedUniqueId !== NULL) {
      return $this->cachedUniqueId;
    }

    $this->cachedUniqueId = random_int(VvjhConstants::MIN_UNIQUE_ID, VvjhConstants::MAX_UNIQUE_ID);

    if ($this->cachedUniqueId < VvjhConstants::MIN_UNIQUE_ID) {
      $this->cachedUniqueId += VvjhConstants::MIN_UNIQUE_ID;
    }
    if ($this->cachedUniqueId > VvjhConstants::MAX_UNIQUE_ID) {
      $range = VvjhConstants::MAX_UNIQUE_ID - VvjhConstants::MIN_UNIQUE_ID + 1;
      $this->cachedUniqueId = $this->cachedUniqueId % $range + VvjhConstants::MIN_UNIQUE_ID;
    }

    return $this->cachedUniqueId;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $rows = [];

    if (!empty($this->view->result)) {
      foreach ($this->view->result as $row) {
        $rendered_row = $this->view->rowPlugin->render($row);
        if ($rendered_row !== NULL) {
          $rows[] = $rendered_row;
        }
      }
    }

    $libraries = $this->buildLibraryList();

    $build = [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'] ?? $this->generateUniqueId(),
      '#attached' => [
        'library' => $libraries,
      ],
    ];

    return $build;
  }

  /**
   * Build the list of libraries to attach.
   *
   * @return array
   *   An array of library names to attach.
   */
  protected function buildLibraryList(): array {
    $libraries = ['vvjh/vvjh'];

    if (!empty($this->options['enable_css'])) {
      $libraries[] = 'vvjh/vvjh-style';
    }

    if (!empty($this->options['available_breakpoints'])) {
      $libraries[] = 'vvjh/vvjh__' . $this->options['available_breakpoints'];
    }

    return $libraries;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(): array {
    $errors = parent::validate();

    // Check if using fields.
    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Hero requires Fields as row style.');
    }
    else {
      // Check if first field is an image.
      $fields = $this->view->display_handler->getHandlers('field');

      if (empty($fields)) {
        $errors[] = $this->t('Views Hero requires at least one field to be configured.');
      }
      else {
        $first_field = reset($fields);
        $is_image = FALSE;

        // Check if it's an EntityField (not a global or custom field).
        if ($first_field instanceof \Drupal\views\Plugin\views\field\EntityField) {
          // Get field name from the field's definition.
          $field_name = $first_field->definition['field_name'] ?? NULL;

          if ($field_name) {
            // Get the entity type from the view.
            $entity_type_id = $this->view->getBaseEntityType()->id();

            // Use entity field manager service to get field storage definitions.
            $entity_field_manager = \Drupal::service('entity_field.manager');
            $field_storage_definitions = $entity_field_manager->getFieldStorageDefinitions($entity_type_id);

            // Check if this field exists and is an image type.
            if (isset($field_storage_definitions[$field_name])) {
              $field_type = $field_storage_definitions[$field_name]->getType();
              $is_image = ($field_type === 'image');
            }
          }
        }

        if (!$is_image) {
          $errors[] = $this->t('Views Hero requires the first field to be an Image field. Please add an image field as the first field in your Fields configuration.');
        }
      }
    }

    return $errors;
  }

}
