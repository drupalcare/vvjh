<?php

/**
 * @file
 * Contains the hook implementations for the custom Views style plugin.
 */

/**
 * Implements hook_views_data_alter().
 */
function vvjh_views_data_alter(array &$data): void {
  // Define the style plugin schema.
  $data['views_style_plugin']['views_vvjh'] = [
    'type' => 'views_style',
    'label' => t('Views Vanilla JavaScript Hero'),
    'mapping' => [
      'unique_id' => [
        'type' => 'string',
        'label' => t('Unique ID'),
        'description' => t('A unique numeric ID used to identify this view display.'),
      ],
      'hero_style' => [
        'type' => 'string',
        'label' => t('Hero Style'),
        'description' => t('The style of the hero animation (e.g., fade, zoom, slide from top, right, bottom, left).'),
      ],
      'max_width' => [
        'type' => 'numeric',
        'label' => t('Max Width Main Container'),
        'description' => t('The max width for the hero main container.'),
      ],
      'max_content_width' => [
        'type' => 'numeric',
        'label' => t('Max Width'),
        'description' => t('The max width for the hero content.'),
      ],
      'min_height' => [
        'title' => t('Min Height'),
        'help' => t('Define the height of the hero section.'),
        'field' => [
          'id' => 'array',
          'schema' => [
            'value' => [
              'type' => 'numeric',
              'description' => t('The numeric value for the height.'),
            ],
            'unit' => [
              'type' => 'string',
              'description' => t('The unit for the height (vw, vh, px, em, rem).'),
            ],
          ],
        ],
      ],
      'overlay_position' => [
        'type' => 'string',
        'label' => t('Overlay Position'),
        'description' => t('The position of the overlay content (center, left, right, top, bottom).'),
      ],
      'overlay_bg_color' => [
        'type' => 'string',
        'label' => t('Overlay Background Color'),
        'description' => t('The background color for the overlay content.'),
      ],
      'overlay_bg_opacity' => [
        'type' => 'float',
        'label' => t('Overlay Background Opacity'),
        'description' => t('The opacity level for the overlay background color, ranging from 0 to 1.'),
      ],
      'hero_speed' => [
        'type' => 'numeric',
        'label' => t('Hero Animation Speed'),
        'description' => t('The speed of the hero animation (e.g., 0.9 for fast, 2 for slow).'),
      ],
      'animation_easing' => [
        'type' => 'string',
        'label' => t('Animation Easing'),
        'description' => t('The easing function applied to the hero animation (ease, linear, ease-in, ease-out, ease-in-out).'),
      ],
      'enable_css' => [
        'type' => 'boolean',
        'label' => t('Enable CSS Library'),
        'description' => t('Include the CSS library for styling the hero elements.'),
      ],
      'available_breakpoints' => [
        'type' => 'string',
        'label' => t('Responsive Breakpoints'),
        'description' => t('Defines the breakpoints at which the hero will be disabled (e.g., 576px, 768px, 992px, 1200px, 1400px).'),
      ],
      'hero_item_width' => [
        'type' => 'numeric',
        'label' => t('Accordion Item Width'),
      ],
    ],
  ];
}
