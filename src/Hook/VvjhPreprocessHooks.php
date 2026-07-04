<?php

declare(strict_types=1);

namespace Drupal\vvjh\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Template\Attribute;
use Drupal\views\Hook\ViewsThemeHooks;
use Drupal\views\ViewExecutable;
use Drupal\vvjh\Plugin\views\style\Hero;

/**
 * Preprocess hooks for VVJH hero.
 *
 * Builds the data-* attribute bag, derives the rgba overlay color
 * from hex+opacity, formats min_height as `{value}{unit}`, rewrites
 * row theme suggestions, and adds the legacy `vvj-hero` class on the
 * views_view wrapper for v1 contract preservation.
 */
final class VvjhPreprocessHooks {

  /**
   * Constructor.
   *
   * @param \Drupal\views\Hook\ViewsThemeHooks $viewsThemeHooks
   *   Views row / field preprocess (replaces removed procedural
   *   template_preprocess_* in Drupal 12).
   */
  public function __construct(
    private readonly ViewsThemeHooks $viewsThemeHooks,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for views_view_vvjh.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view_vvjh')]
  public function preprocessViewsViewVvjh(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!($view instanceof ViewExecutable)) {
      return;
    }
    $handler = $view->style_plugin;
    if (!($handler instanceof Hero)) {
      return;
    }
    $options = $handler->options;
    $variables['options'] = $options;

    $list_attributes = [
      'data-overlay-position' => $options['overlay_position'] ?? '',
      'data-hero-style' => $options['hero_style'] ?? '',
      'data-hero-speed' => $options['hero_speed'] ?? '',
      'data-animation-easing' => $options['animation_easing'] ?? '',
      'data-unique-id' => $options['unique_id'] ?? '',
      'data-available-breakpoints' => $options['available_breakpoints'] ?? '',
      'data-enable-css' => isset($options['enable_css']) ? (!empty($options['enable_css']) ? 'true' : 'false') : '',
      'data-max-content-width' => $options['max_content_width'] ?? '',
      'data-max-width' => $options['max_width'] ?? '',
      'data-hero-item-width' => $options['hero_item_width'] ?? '',
    ];

    $variables['list_attributes'] = new Attribute(array_filter($list_attributes, static fn ($v): bool => $v !== ''));

    // Derive RGBA overlay from hex color + opacity.
    $overlay_color = $options['overlay_bg_color'] ?? '';
    $color_string = is_scalar($overlay_color) ? (string) $overlay_color : '';
    if ($color_string !== '') {
      $rgb = self::hexToRgb($color_string);
      $raw_opacity = $options['overlay_bg_opacity'] ?? 1;
      $opacity = is_numeric($raw_opacity) ? (float) $raw_opacity : 1.0;
      $variables['background_rgb'] = sprintf('rgba(%d, %d, %d, %s)', $rgb['r'], $rgb['g'], $rgb['b'], $opacity);
    }
    else {
      $variables['background_rgb'] = NULL;
    }

    // Format min_height as `{value}{unit}`. v1 parity: emit the concatenation
    // only when BOTH value and unit are non-empty (empty(0)/empty('0')/
    // empty('') are all TRUE); otherwise fall back to the hardcoded '40vw'.
    $min_height = $options['min_height'] ?? NULL;
    $raw_value = is_array($min_height) ? ($min_height['value'] ?? NULL) : NULL;
    $raw_unit = is_array($min_height) ? ($min_height['unit'] ?? NULL) : NULL;
    if (!empty($raw_value) && !empty($raw_unit)) {
      $value_string = is_scalar($raw_value) ? (string) $raw_value : '';
      $unit_string = is_scalar($raw_unit) ? (string) $raw_unit : '';
      $variables['min_height'] = $value_string . $unit_string;
    }
    else {
      $variables['min_height'] = '40vw';
    }

    // Rewrite row theme suggestions to vvjh-namespaced ones.
    $rows = $variables['rows'] ?? NULL;
    if (is_array($rows)) {
      foreach ($rows as $key => $row) {
        if (!is_array($row) || !isset($row['#theme']) || !is_array($row['#theme'])) {
          continue;
        }
        foreach ($row['#theme'] as $idx => $hook) {
          if (is_string($hook)) {
            $row['#theme'][$idx] = str_replace(
              'views_view_fields',
              'views_view_vvjh_fields',
              $hook,
            );
          }
        }
        $rows[$key] = $row;
      }
      $variables['rows'] = $rows;
    }

    $this->viewsThemeHooks->preprocessViewsViewUnformatted($variables);
  }

  /**
   * Implements hook_preprocess_HOOK() for views_view_vvjh_fields.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view_vvjh_fields')]
  public function preprocessViewsViewVvjhFields(array &$variables): void {
    $this->viewsThemeHooks->preprocessViewsViewFields($variables);
  }

  /**
   * Implements hook_preprocess_HOOK() for views_view.
   *
   * Adds the legacy `vvj-hero` class for v1 contract preservation.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!($view instanceof ViewExecutable)) {
      return;
    }
    if (!($view->style_plugin instanceof Hero)) {
      return;
    }
    $attributes = $variables['attributes'] ?? NULL;
    if (is_array($attributes)) {
      $classes = $attributes['class'] ?? [];
      if (!is_array($classes)) {
        $classes = [];
      }
      $classes[] = 'vvj-hero';
      $attributes['class'] = $classes;
      $variables['attributes'] = $attributes;
    }
  }

  /**
   * Convert a hex color to an RGB triplet.
   *
   * @param string $hex
   *   Hex color, with or without leading `#`. Accepts 3 or 6 chars.
   *
   * @return array{r: int, g: int, b: int}
   *   Decimal RGB components.
   */
  private static function hexToRgb(string $hex): array {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    return [
      'r' => (int) hexdec(substr($hex, 0, 2)),
      'g' => (int) hexdec(substr($hex, 2, 2)),
      'b' => (int) hexdec(substr($hex, 4, 2)),
    ];
  }

}
