/**
 * @file
 * Pattern Aperture: split the title into words (Drupal.Vvj.splitText) so
 * each word rises on its own. The full title stays the accessible name.
 * Without this script the title rises as one line with the rest.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

((Drupal, once) => {
  Drupal.behaviors.vvjPatternVvjhAperture = {
    attach(context) {
      once(
        'vvj-pattern-vvjh-aperture',
        '.vvj-pattern--vvjh-aperture',
        context,
      ).forEach((root) => {
        root
          .querySelectorAll('.vvj-title')
          .forEach((title) => Drupal.Vvj.splitText(title));
      });
    },
  };
})(Drupal, once);
