# Views Vanilla JavaScript Hero (VVJH)

A vanilla-JavaScript hero-section **Views format** for Drupal.
Renders the first row of a View as a hero (image + overlay content) with
breakpoint-driven activation, six animation styles, eleven overlay positions,
configurable overlay color/opacity, and AAA-conformant semantic markup.

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjh-hero>` custom element extending `VvjElementBase`.

## Table of contents

- Overview
- Requirements
- Installation
- Configuration
- Patterns
- Usage
- Troubleshooting
- Changelog
- Maintainers
- License

## Overview

### Features

- Six animation styles: fade / zoom / slide-from-top / slide-from-right / slide-from-bottom / slide-from-left
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

## Requirements

- Drupal core `^11.3 || ^12`
- PHP 8.3 or later
- Drupal core modules Views (`drupal:views`) and Filter (`drupal:filter`)
- [VVJ Core](https://www.drupal.org/project/vvj_core) (`drupal/vvj_core` `^2.0.1`), which composer installs for you

Optional: the `vvj_patterns` sub-module of `drupal/vvj_core` adds the Pattern select to the format settings (see [Patterns](#patterns)), and `vvj_patterns_demo` adds example content.

## Installation

```bash
composer require drupal/vvjh:^2.0
drush en vvjh
```

### Upgrading from 1.x

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

`drush updb` enables `vvj_core` (`vvjh_update_10001`) and converts existing views to the v2 option shape (`vvjh_update_10002`). Without `vvj_core` on disk the update stops with a message telling you to download `drupal/vvj_core` and run `drush updb` again.

## Configuration

All settings live in the format settings of the *Views Vanilla JavaScript Hero* style on a View. They are grouped into layout, position, animation, styling and responsive sections, and a **Patterns** section when `vvj_patterns` is enabled. The extended help topic is at `/admin/help/vvjh`.

Settings are stored in the View itself, as options of the `views_vvjh` style plugin; their types are declared in `config/schema/vvjh.schema.yml`.

## Patterns

A Pattern is a ready-made design for this format. VVJH ships 4. Enable the `vvj_patterns` sub-module of `drupal/vvj_core` (`drush en vvj_patterns`), open the format settings and choose one in **Patterns** > **Pattern**.

| Pattern | Look |
|---|---|
| Aperture | One full-bleed hero: the photo opens as a circle from the text corner, then the title rises word by word. |
| Bento | Four hero cards in a bento grid: the first is large; cards zoom in one after another and open their summary on hover or focus. |
| Panels | A row of vertical strips: the hovered or focused strip opens wide, its photo regains color and its words rise in. |
| Stack | Split cards, photo and words, that stick and pile up as the page scrolls; the card below shrinks and dims. |

When you choose a different Pattern and save, its settings replace the layout, animation, styling and deep linking settings of the view; you can still change them afterwards. Choose **None** to keep your own settings. Drupal then shows a message naming the settings the Pattern set.

Each Pattern also lists what the view still needs, for example the fields to add, their order and the CSS classes to give them. The list appears under **To finish the Pattern** in the Patterns section and as a warning after you save. The `vvj_patterns_demo` sub-module shows a working setup: it adds the "VVJ Slide" content type (fields `vvj_nav_label`, `vvj_image`, `vvj_kicker`, `vvj_summary` and `vvj_link`, besides the title), five demo slides and, as optional config, an example view for each VVJ module named `vvj_demo_<module>`.

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

### Tokens in Views text areas

In Views headers, footers, or empty text with *Use replacement tokens from the
first row*, default Twig tokens (`{{ title }}`) do not work. Use VVJH tokens
instead:

- `{{ title }}` → `[vvjh:title]`
- `{{ field_image }}` → `[vvjh:field_image]`
- Append `:plain` for plain text: `[vvjh:title:plain]`

v2 resolves tokens through the shared `vvj_core.token_resolver` service; syntax
is unchanged from v1. Tokens read from the **first row** of rendered View
fields. Complex field rewrites are not supported.

## Troubleshooting

- **There is no Patterns section.** The section appears only while `vvj_patterns` is enabled. Run `drush en vvj_patterns`.
- **Saving the view changed my settings.** Choosing a different Pattern replaces the layout, animation, styling and deep linking settings. Choose **None** to keep your own.
- **A Pattern looks unfinished.** Follow the list under **To finish the Pattern**: the fields, their order and the CSS classes the Pattern needs.
- **After upgrading from 1.x the view fails or `vvj_core` is not enabled.** Run `drush updb`; update `vvjh_update_10001` enables `vvj_core`. Composer installs it automatically, and a manual install needs `drupal/vvj_core` in `modules/contrib/vvj_core` first.
- **Test errors that name missing infrastructure.** See *Running the tests* in [CONTRIBUTING.md](./CONTRIBUTING.md).
- **Anything else.** Search or open an issue in the [issue queue](https://www.drupal.org/project/issues/vvjh).

## Changelog

See [CHANGELOG.md](./CHANGELOG.md).

## Maintainers

- Alaa Haddad - [flashwebcenter](https://www.drupal.org/u/flashwebcenter)

## License

GPL-2.0-or-later.
