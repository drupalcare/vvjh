/**
 * @file
 * VVJH compat shim. Behavior lives in `<vvjh-hero>` custom element.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

// phpcs:disable Drupal.NamingConventions.UpperCaseConstant
// phpcs:disable Generic.PHP.UpperCaseConstant
// phpcs:disable Squiz.WhiteSpace.OperatorSpacing

((Drupal, once) => {
  Drupal.behaviors.VVJHero = {
    attach(context) {
      once('vvjh-hero-marker', 'vvjh-hero', context);
    },
    detach() {},
  };
})(Drupal, once);
