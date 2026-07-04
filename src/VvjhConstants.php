<?php

declare(strict_types=1);

namespace Drupal\vvjh;

/**
 * Vvjh-specific constants — hero pattern-only values.
 *
 * Cross-cutting constants live in vvj_core's `ValidationBounds` and
 * `AnimationDirection`.
 *
 * @internal Final, non-instantiable.
 */
final class VvjhConstants {

  /**
   * Overlay position values.
   */
  public const string POSITION_MIDDLE = 'd-middle';
  public const string POSITION_LEFT = 'd-left';
  public const string POSITION_RIGHT = 'd-right';
  public const string POSITION_TOP = 'd-top';
  public const string POSITION_BOTTOM = 'd-bottom';
  public const string POSITION_TOP_LEFT = 'd-top-left';
  public const string POSITION_TOP_RIGHT = 'd-top-right';
  public const string POSITION_BOTTOM_LEFT = 'd-bottom-left';
  public const string POSITION_BOTTOM_RIGHT = 'd-bottom-right';
  public const string POSITION_TOP_MIDDLE = 'd-top-middle';
  public const string POSITION_BOTTOM_MIDDLE = 'd-bottom-middle';
  public const string DEFAULT_OVERLAY_POSITION = self::POSITION_MIDDLE;

  /**
   * Min-height units.
   */
  public const string UNIT_VW = 'vw';
  public const string UNIT_VH = 'vh';
  public const string UNIT_PX = 'px';
  public const string UNIT_EM = 'em';
  public const string UNIT_REM = 'rem';

  /**
   * Animation easing options.
   */
  public const string EASING_EASE = 'ease';
  public const string EASING_LINEAR = 'linear';
  public const string EASING_EASE_IN = 'ease-in';
  public const string EASING_EASE_OUT = 'ease-out';
  public const string EASING_EASE_IN_OUT = 'ease-in-out';
  public const string DEFAULT_ANIMATION_EASING = self::EASING_EASE;

  /**
   * Defaults.
   */
  public const string DEFAULT_HERO_STYLE = 'a-fade';
  public const float DEFAULT_HERO_SPEED = 0.9;
  public const string DEFAULT_OVERLAY_BG_COLOR = '#ffffff';
  public const float DEFAULT_OVERLAY_BG_OPACITY = 0.7;
  public const string DEFAULT_BREAKPOINT = '576';
  public const int DEFAULT_MIN_HEIGHT_VALUE = 50;
  public const string DEFAULT_MIN_HEIGHT_UNIT = self::UNIT_VW;
  public const int DEFAULT_MAX_CONTENT_WIDTH = 60;
  public const int DEFAULT_MAX_WIDTH = 1200;
  public const int DEFAULT_HERO_ITEM_WIDTH = 0;

  /**
   * Bounds.
   */
  public const float MIN_HERO_SPEED = 0.1;
  public const float MAX_HERO_SPEED = 2.0;
  public const float HERO_SPEED_STEP = 0.1;
  public const float MIN_OPACITY = 0.0;
  public const float MAX_OPACITY = 1.0;
  public const float OPACITY_STEP = 0.1;
  public const int MIN_MAX_WIDTH = 1;
  public const int MIN_MAX_CONTENT_WIDTH = 1;
  public const int MIN_HERO_ITEM_WIDTH = 0;

  /**
   * Prevent instantiation.
   */
  private function __construct() {
  }

}
