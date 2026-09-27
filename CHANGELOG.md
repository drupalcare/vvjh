# Changelog

## [2.0.0] — UNRELEASED

**Drop-in upgrade for 1.x sites.** v2 modernizes internals — extends `vvj_core`
foundation, ships `<vvjh-hero>` custom element extending `VvjElementBase`,
AbortController-tracked listeners — while preserving every CSS rule, library
name, option key, plugin ID, theme hook, JS behavior key from 1.x.

### Fixed

- Update 10002 converts v1 views to the v2 option shape. A 1.0-style
  `min_height` stored as a bare number becomes `{value, unit: vw}`; before
  this, v2 ignored it and rendered the fallback height.
- Removed the phantom "Active on all screens" (`all`) option from the Hero
  breakpoint select — a PHP array-union (`+`) bug discarded the intended 5-entry
  list and leaked the base registry's options; v1 offered exactly
  576/768/992/1200/1400, and `all` mapped to a non-existent library.
- Restored v1's `40vw` fallback for the `--min-height` CSS variable when the
  min-height value or unit is empty (v2 was emitting `0vw` / the `50vw`
  default).

### Added

- `drupal/vvj_core` dependency (auto-installed).
- `vvjh.install` with `vvjh_update_10001()` to auto-enable vvj_core during
  `drush updb`.
- `<vvjh-hero>` custom element. Lazy IntersectionObserver hydration;
  AbortController-tracked resize listener; viewport-driven `.active` class
  toggle on `.hero-wrapper`.

### Changed

- Plugin extends `VvjStylePluginBase`. Implements four small subclass hooks
  (`getModuleSlug`, `getCustomElementTag`, `definePatternOptions`,
  `buildPatternSections`); standard sections (warning message, advanced, token
  doc) come from the base.
- `getAnimationPresets()` returns 6 presets (omits "None" — heroes always
  animate).
- `supportsDeeplinking()` returns FALSE (no panels/slides to deep-link to).
- Token resolution delegates to `vvj_core.token_resolver`.
- Hooks moved to OOP `#[Hook]` classes.
- `Drupal\vvjh\Twig\VVJHTwigExtension` removed — vvj_core ships the shared
  `safe_html` filter.

### Drupal core / PHP

- Requires Drupal `^11.3 || ^12`, PHP `>=8.3`.
