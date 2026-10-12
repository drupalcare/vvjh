# Views Vanilla JavaScript Hero (VVJH)

A vanilla-JavaScript hero-section **Views format** for Drupal 11.3+ / 12.
Renders the first row of a View as a hero (image + overlay content) with
breakpoint-driven activation, six animation styles, eleven overlay positions,
configurable overlay color/opacity, and AAA-conformant semantic markup.

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjh-hero>` custom element extending `VvjElementBase`.

## Features

- Six animation styles: fade / zoom / slide-from-top / slide-from-right / slide-
  from-bottom / slide-from-left
- Eleven overlay positions: middle / top / bottom / left / right / four corners
  / top-middle / bottom-middle
- Configurable max width (px), max content width (%), min height (vw / vh / px /
  em / rem)
- Configurable hero-card width for multi-item heroes (0 = full-width list)
- Hex + opacity → rgba overlay background
- Five animation easing curves: ease / linear / ease-in / ease-out / ease-in-out
- Configurable hero animation speed (0.1 – 2.0 s)
- 5 responsive breakpoints (576 / 768 / 992 / 1200 / 1400 px). Hero animation
  activates above the breakpoint
- ARIA `role="region"`, labeled groups per row, accessible image and content
  overlays
- Lazy IntersectionObserver hydration; AbortController-tracked listeners
- **Views tokens**: `[vvjh:field]` / `[vvjh:field:plain]` in Views text areas
  with *Use replacement tokens from the first row*
- **Drupal admin help**: `/admin/help/vvjh`

## Token Support in Views Text Areas

In Views headers, footers, or empty text with *Use replacement tokens from the
first row*, default Twig tokens (`{{ title }}`) do not work. Use VVJH tokens
instead:

- `{{ title }}` → `[vvjh:title]`
- `{{ field_image }}` → `[vvjh:field_image]`
- Append `:plain` for plain text: `[vvjh:title:plain]`

v2 resolves tokens through the shared `vvj_core.token_resolver` service; syntax
is unchanged from v1. Tokens read from the **first row** of rendered View
fields. Complex field rewrites are not supported.

## Installation

```bash
composer require drupal/vvjh:^2.0
drush en vvjh
```

## Usage

1. Create or edit a View.
2. Set **Format** to *Views Vanilla JavaScript Hero*.
3. Set **Show** to *Fields*.
4. The first field MUST be an image. It becomes the hero background.
5. Remaining fields render as the hero content overlay.
6. Configure layout / position / animation / styling / responsive in the format
   settings.
7. Save.

**Sample view:** Optional config `views.view.vvjh_example` (`config/optional/`)
installs when there is no ID conflict.

## Drop-in upgrade from 1.x

`composer update drupal/vvjh && drush updb && drush cr` is sufficient. v2
preserves:

- Plugin ID `views_vvjh`, theme hook `views_view_vvjh`
- Twig template names
- All option keys (12 total, including the `min_height` array shape)
- Library names (`vvjh`, `vvjh-style`, `vvjh__576/768/992/1200/1400`)
- JS behavior key `Drupal.behaviors.VVJHero`
- All CSS class names (`.vvjh`, `.vvjh-inner`, `.hero-wrapper`, `.hero-image`,
  `.hero-content`, position classes, animation classes)

Outer rendered tag changed from `<div>` to `<vvjh-hero>`. Theme selectors
targeting `.vvjh` keep matching.

The vvj_core foundation module ships a shared `safe_html` Twig filter, so v2
drops the per-module `Drupal\vvjh\Twig\VVJHTwigExtension`: templates that use
`|safe_html` keep working unchanged.

## Running the tests

This module ships no `phpunit.xml` of its own, deliberately: its suites are
Kernel, Functional and FunctionalJavascript, which need a real Drupal, so a
module-local config would have to hard-code a relative path to `core` and would
break the moment the module is not physically inside a docroot (a symlinked
checkout, for instance). Drupal core's runner already does the job.

Run the fast suites from the docroot:

```bash
cd /path/to/drupal/web
export SIMPLETEST_DB="mysql://user:pass@localhost/dbname"
../vendor/bin/phpunit -c core modules/contrib/vvjh/tests/src/Unit
../vendor/bin/phpunit -c core modules/contrib/vvjh/tests/src/Kernel
```

The **Functional** and **FunctionalJavascript** suites also need
`SIMPLETEST_BASE_URL` pointing at a running web server, and the JavaScript ones
need chromedriver. Without those they error rather than fail, and the errors
name missing infrastructure rather than anything about this module, so don't
read them as test failures. The drupal.org pipeline provides both.

## License

GPL-2.0-or-later.
