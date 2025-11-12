<?php

declare(strict_types=1);

namespace Drupal\vvjh;

/**
 * Defines constants for the VVJH module.
 */
final class VvjhConstants {

  public const POSITION_MIDDLE = 'd-middle';
  public const POSITION_LEFT = 'd-left';
  public const POSITION_RIGHT = 'd-right';
  public const POSITION_TOP = 'd-top';
  public const POSITION_BOTTOM = 'd-bottom';
  public const POSITION_TOP_LEFT = 'd-top-left';
  public const POSITION_TOP_RIGHT = 'd-top-right';
  public const POSITION_BOTTOM_LEFT = 'd-bottom-left';
  public const POSITION_BOTTOM_RIGHT = 'd-bottom-right';
  public const POSITION_TOP_MIDDLE = 'd-top-middle';
  public const POSITION_BOTTOM_MIDDLE = 'd-bottom-middle';

  public const STYLE_FADE = 'a-fade';
  public const STYLE_ZOOM = 'a-zoom';
  public const STYLE_TOP = 'a-top';
  public const STYLE_RIGHT = 'a-right';
  public const STYLE_BOTTOM = 'a-bottom';
  public const STYLE_LEFT = 'a-left';

  public const UNIT_VW = 'vw';
  public const UNIT_VH = 'vh';
  public const UNIT_PX = 'px';
  public const UNIT_EM = 'em';
  public const UNIT_REM = 'rem';

  public const EASING_EASE = 'ease';
  public const EASING_LINEAR = 'linear';
  public const EASING_EASE_IN = 'ease-in';
  public const EASING_EASE_OUT = 'ease-out';
  public const EASING_EASE_IN_OUT = 'ease-in-out';

  public const BREAKPOINT_576 = '576';
  public const BREAKPOINT_768 = '768';
  public const BREAKPOINT_992 = '992';
  public const BREAKPOINT_1200 = '1200';
  public const BREAKPOINT_1400 = '1400';

  public const DEFAULT_OVERLAY_POSITION = self::POSITION_MIDDLE;
  public const DEFAULT_HERO_STYLE = self::STYLE_FADE;
  public const DEFAULT_HERO_SPEED = 0.9;
  public const DEFAULT_OVERLAY_BG_COLOR = '#ffffff';
  public const DEFAULT_OVERLAY_BG_OPACITY = 0.7;
  public const DEFAULT_BREAKPOINT = self::BREAKPOINT_576;
  public const DEFAULT_ANIMATION_EASING = self::EASING_EASE;
  public const DEFAULT_ENABLE_CSS = TRUE;
  public const DEFAULT_MIN_HEIGHT_VALUE = 50;
  public const DEFAULT_MIN_HEIGHT_UNIT = self::UNIT_VW;
  public const DEFAULT_MAX_CONTENT_WIDTH = 60;
  public const DEFAULT_MAX_WIDTH = 1200;
  public const DEFAULT_HERO_ITEM_WIDTH = 0;

  public const MIN_HERO_SPEED = 0.1;
  public const MAX_HERO_SPEED = 2.0;
  public const HERO_SPEED_STEP = 0.1;

  public const MIN_OPACITY = 0;
  public const MAX_OPACITY = 1;
  public const OPACITY_STEP = 0.1;

  public const MIN_MAX_WIDTH = 1;
  public const MIN_MAX_CONTENT_WIDTH = 1;
  public const MIN_HERO_ITEM_WIDTH = 0;

  public const MIN_UNIQUE_ID = 10000000;
  public const MAX_UNIQUE_ID = 99999999;

  public const TOKEN_NAMESPACE = 'vvjh';
  public const TOKEN_PLAIN_SUFFIX = ':plain';
  public const TOKEN_PATTERN = '/^[a-zA-Z0-9_]+(:plain)?$/';

  public const DATA_ATTRIBUTE_MAP = [
    'overlay_position' => 'overlay-position',
    'hero_style' => 'hero-style',
    'hero_speed' => 'hero-speed',
    'animation_easing' => 'animation-easing',
    'unique_id' => 'unique-id',
    'available_breakpoints' => 'available-breakpoints',
    'min_height' => 'min-height',
    'max_content_width' => 'max-content-width',
    'max_width' => 'max-width',
    'hero_item_width' => 'hero-item-width',
  ];

  public const BOOLEAN_ATTRIBUTE_MAP = [
    'enable_css' => 'enable-css',
  ];

  public const VIEWS_TYPE_INTEGER = 'integer';
  public const VIEWS_TYPE_STRING = 'string';
  public const VIEWS_TYPE_BOOLEAN = 'boolean';
  public const VIEWS_TYPE_NUMERIC = 'numeric';
  public const VIEWS_TYPE_FLOAT = 'float';
  public const VIEWS_TYPE_ARRAY = 'array';

  private function __construct() {
  }

}
