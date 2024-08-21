<?php

namespace Drupal\vvjh\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\style\StylePluginBase;

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
   * Sets default options.
   *
   * @return array
   *   The default options array.
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['unique_id'] = ['default' => $this->generateUniqueId()];
    $options['overlay_position'] = ['default' => 'd-middle'];
    $options['hero_style'] = ['default' => 'a-fade'];
    $options['hero_speed'] = ['default' => '0.9'];
    $options['overlay_bg_color'] = ['default' => '#ffffff'];
    $options['overlay_bg_opacity'] = ['default' => '0.7'];
    $options['available_breakpoints'] = ['default' => '576'];
    $options['animation_easing'] = ['default' => 'ease'];
    $options['enable_css'] = ['default' => TRUE];
    $options['min_height'] = [
      'default' => [
        'value' => 50,
        'unit' => 'vw',
      ],
    ];
    $options['max_content_width'] = ['default' => 60];
    $options['max_width'] = ['default' => 1200];
    $options['hero_item_width'] = ['default' => 0];
    return $options;
  }

  /**
   * Builds the options form for the view style plugin.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    $form['warning_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="messages messages--warning">' . $this->t(
          'Note: The hero component requires the first field to be an image. When the layout is open, the image will automatically expand to 100 percent of the available width, ensuring full coverage of the hero section. The remaining fields will be used as the hero content. To see an example, check the vvjh_example view by clicking <a href="@url">here</a> to edit it.', [
            '@url' => Url::fromRoute('entity.view.edit_form', ['view' => 'vvjh_example'])->toString(),
          ]
      ) . '</div>',
    ];

    $form['available_breakpoints'] = [
      '#type' => 'select',
      '#title' => $this->t('Available Breakpoints for Hero'),
      '#options' => [
        '576' => $this->t('576px / 36rem'),
        '768' => $this->t('768px / 48rem'),
        '992' => $this->t('992px / 62rem'),
        '1200' => $this->t('1200px / 75rem'),
        '1400' => $this->t('1400px / 87.5rem'),
      ],
      '#default_value' => $this->options['available_breakpoints'],
      '#description' => $this->t('Select the maximum screen width (in pixels) at which the Hero should be disabled.'),
      '#required' => TRUE,
    ];

    $form['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Width (px)'),
      '#default_value' => $this->options['max_width'],
      '#description' => $this->t('Defines the maximum width for the main container of the hero content, typically set in pixels. For example, setting this value to 1200px will ensure that the hero content does not exceed this width, regardless of the screen size.'),
      '#step' => 1,
      '#min' => 1,
      '#required' => TRUE,
    ];

    $form['min_height'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Minimum Height'),
      '#description' => $this->t('Define the minimum height of the hero container by entering a value and selecting a unit. Choose from the following units:
        <ul>
          <li><strong>Viewport Width (vw):</strong> 1vw is equal to 1 percent of the viewport’s width. For example, if the viewport is 1000px wide, 10vw will equal 100px.</li>
          <li><strong>Viewport Height (vh):</strong> 1vh is equal to 1 percent of the viewport’s height. For example, if the viewport is 1000px high, 10vh will equal 100px.</li>
          <li><strong>Pixels (px):</strong> Specifies an exact number of pixels for the height, regardless of the viewport size.</li>
          <li><strong>Percentage (%):</strong> Specifies a percentage of the parent container’s height.</li>
          <li><strong>Ems (em):</strong> Relative to the font size of the element. For example, if the font size is 16px, 1em equals 16px.</li>
          <li><strong>Root Ems (rem):</strong> Relative to the font size of the root element (typically <code>&lt;html&gt;</code>). For example, if the root font size is 16px, 1rem equals 16px.</li>
        </ul>'
      ),
      '#attributes' => [
        'class' => ['min-height-wrapper'],
      ],
    ];

    $min_height = $this->options['min_height'] ?? ['value' => 50, 'unit' => 'vw'];

    $form['min_height']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Height Value'),
      '#default_value' => $min_height['value'] ?? 50,
      '#description' => $this->t('Enter the numeric value for the minimum height.'),
      '#attributes' => [
        'style' => 'width: 150px; display: inline-block;',
      ],
    ];

    $form['min_height']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Height Unit (vw, vh, px, %, em, rem)'),
      '#default_value' => $min_height['unit'] ?? 'vw',
      '#options' => [
        'vw' => $this->t('Viewport Width (vw)'),
        'vh' => $this->t('Viewport Height (vh)'),
        'px' => $this->t('Pixels (px)'),
        '%' => $this->t('Percentage (%)'),
        'em' => $this->t('Ems (em)'),
        'rem' => $this->t('Root Ems (rem)'),
      ],
      '#attributes' => [
        'style' => 'width: 150px; display: inline-block;',
      ],
      '#description' => $this->t('Select the unit for the minimum height.'),
    ];

    $form['max_content_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Content Width (%)'),
      '#default_value' => $this->options['max_content_width'],
      '#description' => $this->t("Determines the maximum width for the remaining fields (content) within the hero section, expressed as a percentage of the main layout's width. For example, setting this to 50 percent means that the content will occupy up to half of the hero's main container width."),
      '#step' => 1,
      '#min' => 1,
      '#required' => TRUE,
    ];

    $form['overlay_position'] = [
      '#type' => 'select',
      '#title' => $this->t('Overlay Position'),
      '#options' => [
        'd-middle' => $this->t('Middle'),
        'd-left' => $this->t('Left'),
        'd-right' => $this->t('Right'),
        'd-top' => $this->t('Top'),
        'd-bottom' => $this->t('Bottom'),
        'd-top-left' => $this->t('Top Left'),
        'd-top-right' => $this->t('Top Right'),
        'd-bottom-left' => $this->t('Bottom Left'),
        'd-bottom-right' => $this->t('Bottom Right'),
        'd-top-middle' => $this->t('Top Middle'),
        'd-bottom-middle' => $this->t('Bottom Middle'),
      ],
      '#default_value' => $this->options['overlay_position'],
      '#description' => $this->t('Select the position where the content overlay will appear within the hero section. This controls where text and other elements will be placed on top of the background image. <strong>Due to limitations in CSS, sliding animations may not center content as expected. Consider using alternative animations or positioning methods for better results. Middle positioning may not work correctly with slide animations.</strong>'),
      '#required' => TRUE,
    ];

    $form['hero_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Hero Style'),
      '#options' => [
        'a-fade' => $this->t('Fade'),
        'a-zoom' => $this->t('Zoom'),
        'a-top' => $this->t('Slide from Top'),
        'a-right' => $this->t('Slide from Right'),
        'a-bottom' => $this->t('Slide from Bottom'),
        'a-left' => $this->t('Slide from Left'),
      ],
      '#default_value' => $this->options['hero_style'],
      '#description' => $this->t('Choose the animation style for how the hero section content will appear. Different styles include fading, zooming, or sliding in from various directions.'),
      '#required' => TRUE,
    ];

    $form['hero_speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Hero Animation Speed'),
      '#default_value' => $this->options['hero_speed'],
      '#description' => $this->t('Set the speed at which the hero section content animation occurs. Enter a value between 0.1 (fast) and 2 (slow) to control the animation speed.'),
      '#step' => 0.1,
      '#min' => 0.1,
      '#max' => 2,
      '#required' => TRUE,
    ];

    $form['animation_easing'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation Easing'),
      '#options' => [
        'ease' => $this->t('Ease'),
        'linear' => $this->t('Linear'),
        'ease-in' => $this->t('Ease-in'),
        'ease-out' => $this->t('Ease-out'),
        'ease-in-out' => $this->t('Ease-in-out'),
      ],
      '#default_value' => $this->options['animation_easing'],
      '#description' => $this->t('Choose the easing function for the hero section content animation. Easing controls the acceleration and deceleration of the animation for a smoother effect.'),
      '#required' => TRUE,
    ];

    $form['overlay_bg_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Overlay Background Color'),
      '#default_value' => $this->options['overlay_bg_color'],
      '#description' => $this->t('Choose the background color for the overlay that appears behind the content within the hero section. This helps improve the readability of the overlay content.'),
    ];

    $form['overlay_bg_opacity'] = [
      '#type' => 'range',
      '#title' => $this->t('Overlay Background Opacity'),
      '#default_value' => $this->options['overlay_bg_opacity'],
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.1,
      '#description' => $this->t('Adjust the opacity of the overlay background color for the hero section content. A lower value makes the background more transparent, while a higher value makes it more opaque.'),
      '#suffix' => '<span id="background-opacity-value">' . $this->options['overlay_bg_opacity'] . '</span>',
      '#attributes' => [
        'oninput' => 'document.getElementById("background-opacity-value").innerText = this.value;',
      ],
    ];

    $form['hero_item_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Hero Card Width (px)'),
      '#default_value' => $this->options['hero_item_width'],
      '#description' => $this->t('Set the width of each Hero Card in pixels. Use this setting to display hero items as Hero Cards or a list. If set to zero, each Hero Card will expand to the full available width, creating a list layout. Otherwise, specifying a width will display each item as a Hero Card with the specified width. Note: To use this feature, you must have more than one item.'),
      '#step' => 1,
      '#min' => 0,
      '#required' => TRUE,
    ];

    $form['enable_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Library'),
      '#default_value' => $this->options['enable_css'],
      '#description' => $this->t('Check this box to include the CSS library for styling the hero elements.'),
    ];
  }

  /**
   * Generates a unique numeric ID for the view display.
   *
   * @return int
   *   The generated unique ID.
   */
  protected function generateUniqueId(): int {
    // 8-digit unique ID.
    return random_int(10000000, 99999999);
  }

  /**
   * Renders the view with the Hero style.
   *
   * @return array
   *   A render array for the hero.
   */
  public function render(): array {
    $rows = [];
    foreach ($this->view->result as $row) {
      $rows[] = $this->view->rowPlugin->render($row);
    }

    $libraries = [
      'vvjh/vvjh',
    ];

    // Conditionally include the CSS library based on the option.
    if ($this->options['enable_css']) {
      $libraries[] = 'vvjh/vvjh-style';
    }

    // Conditionally include a CSS breakpoint library based on the option.
    if ($this->options['available_breakpoints']) {
      // Include the vvjh-style breakpoint-specific library.
      $libraries[] = 'vvjh/vvjh__' . $this->options['available_breakpoints'];
    }

    return [
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
      '#unique_id' => $this->options['unique_id'],
      '#attached' => [
        'library' => $libraries,
      ],
    ];
  }

  /**
   * Validates the view plugin settings.
   *
   * @return array
   *   An array of validation error messages.
   */
  public function validate(): array {
    $errors = parent::validate();
    if (!$this->usesFields()) {
      $errors[] = $this->t('Views Hero requires Fields as row style and an image as the first field.');
    }
    return $errors;
  }

}
