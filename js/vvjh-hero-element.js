/**
 * @file
 * VvjhHeroElement — `<vvjh-hero>` custom element.
 *
 * Extends `Drupal.Vvj.ElementBase` from `vvj_core`. The hero animation
 * is gated by viewport width: above the configured breakpoint, every
 * `.hero-wrapper` gets `.active` (the CSS keyframes activate on this
 * class). Below it, the class is removed so the hero renders without
 * animation. Resize listener stays in sync with viewport changes. The
 * base class is pulled from the `Drupal.Vvj` namespace — Drupal core's
 * cross-file sharing pattern — with load order guaranteed by the `vvjh`
 * library's `dependencies:`.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

// phpcs:disable Drupal.NamingConventions.UpperCaseConstant
// phpcs:disable Generic.PHP.UpperCaseConstant
// phpcs:disable Squiz.WhiteSpace.OperatorSpacing

((Drupal) => {
  'use strict';

  const { ElementBase } = Drupal.Vvj;

  class VvjhHeroElement extends ElementBase {

    static get patternSlug() {
 return 'vvjh'; }

    onHydrate() {
      this._wrappers = this.querySelectorAll('.hero-wrapper');
      if (!this._wrappers.length) { return;
      }

      const breakpoint = parseInt(this.getAttribute('data-breakpoints'), 10);
      this._breakpoint = Number.isFinite(breakpoint) && breakpoint > 0 ? breakpoint : 576;

      this._toggleActive();
      window.addEventListener('resize', () => this._toggleActive(), { signal: this.signal });
    }

    _toggleActive() {
      const isActive = window.innerWidth >= this._breakpoint;
      this._wrappers.forEach((wrapper) => {
        wrapper.classList.toggle('active', isActive);
      });
    }

  }

  if (!customElements.get('vvjh-hero')) {
    customElements.define('vvjh-hero', VvjhHeroElement);
  }

})(Drupal);
