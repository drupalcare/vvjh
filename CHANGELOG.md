# Changelog

## [2.0.0] (UNRELEASED)

- Accessibility: the hero is a named region only when a Pattern gives it a name (an unnamed region is not a landmark); the hero text no longer slides, zooms or fades in when the visitor asks for reduced motion; without JavaScript the hero text is visible (it is hidden for the entrance effect only after the script runs).

### Fixed

- The 992 px breakpoint's base hero rule used the class `br-990`, so its position, padding and opacity never applied; it now uses `br-992`.
- Accessibility: in Windows high contrast (forced colors) the Pattern icons stay visible; they are masks painted with the text color, which the system used to replace.

**Drop-in upgrade for 1.x sites.** v2 modernizes internals: extends `vvj_core`
foundation, ships `<vvjh-hero>` custom element extending `VvjElementBase`,
AbortController-tracked listeners, while preserving every CSS rule, library
name, option key, plugin ID, theme hook, JS behavior key from 1.x.

### Added

- Help page: follows the drupal.org help text standard (translatable About and Uses sections) instead of printing README.md as raw text.
- Pattern colors: the photo scrims and the Stack shadow are module tokens in `vvjh-pattern-base.css`; new `pattern-bridge-solo` and `pattern-bridge-apex` libraries (`patterns/bridge/`) map them to a Solo or APEX theme. The black-and-white look is unchanged.

### Fixed

- Patterns: Stack uses the shared monospace variable; the focus outline reads a declared variable.
- Patterns: spacing, corner radius, type size, shadow and motion read shared `--vvj-*` variables with fallbacks, so a site or theme can adjust them in one place; a few odd values moved to the nearest step, under 10%.
- Patterns: each Pattern states its stage, ground, placement and the kinds of site it serves best in `pattern.yml`.
- Patterns: sizes that followed the screen width (`vw`) now follow the Pattern's own width (`cqi`), so a Pattern in a sidebar or narrow column keeps its proportions.
- Patterns: Panels keeps a closed strip's title inside the strip (it wraps into a second column instead of running out of the top) and hides the closed strip's link until the strip opens.
- Patterns: the breakpoint files (`css/vvjh-NNN.css`) no longer match Pattern markup; the placement, entrance and narrow resets Patterns used from them now live in `vvjh-pattern-base.css` and follow the Pattern's container, so Panels in a narrow column on a wide screen shows its phone layout with readable words.
- Accessibility: Aperture, Bento and Panels put a soft dark scrim under the words that sit on a photo, so the text keeps its contrast on a light photo.
- Accessibility: the hero no longer adds four empty tab stops per item or `aria-labelledby` references to ids that do not exist (the `role="img"` wrapper hid the real image alt). Panels: a closed strip's summary is hidden visually instead of with `display: none`, so screen readers reach it; focusing the link opens the strip.
- Panels: an opening panel no longer squeezes its words; they keep the open width and the title turns from upright to level. Stack: the photo fills the whole card height (the module centered grid items, leaving the top half empty).
- Update 10002 converts v1 views to the v2 option shape. A 1.0-style
  `min_height` stored as a bare number becomes `{value, unit: vw}`; before
  this, v2 ignored it and rendered the fallback height.
- Removed the phantom "Active on all screens" (`all`) option from the Hero
  breakpoint select. A PHP array-union (`+`) bug discarded the intended 5-entry
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

- Text follows the drupal.org standards: the module description begins with a verb, "JavaScript" is spelled as the language spells it, settings titles are in sentence case, and the README has its project and issue queue links.
- Plugin extends `VvjStylePluginBase`. Implements four small subclass hooks
  (`getModuleSlug`, `getCustomElementTag`, `definePatternOptions`,
  `buildPatternSections`); standard sections (warning message, advanced, token
  doc) come from the base.
- `getAnimationPresets()` returns 6 presets (omits "None", heroes always
  animate).
- `supportsDeeplinking()` returns FALSE (no panels/slides to deep-link to).
- Token resolution delegates to `vvj_core.token_resolver`.
- Hooks moved to OOP `#[Hook]` classes.
- `Drupal\vvjh\Twig\VVJHTwigExtension` removed: vvj_core ships the shared
  `safe_html` filter.
- Code style: JavaScript and CSS follow Drupal core's ESLint, Stylelint and Prettier rules, and the spell-check job passes with a project word file. Rendering and behavior are unchanged.
- Layout: Pattern layout now follows the width the Pattern itself gets (container queries on `vvj-region`) instead of the screen width, so Patterns lay out correctly in a narrow content column; overlays and the Pattern root keep screen-width queries.

### Drupal core / PHP

- Requires Drupal `^11.3 || ^12`, PHP `>=8.3`.
