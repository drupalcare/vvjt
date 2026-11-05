# Views Vanilla JavaScript Tabs (VVJT)

A vanilla-JavaScript tab group **Views format** for Drupal. Renders
Views rows as a tabbed interface: top, bottom, left, or right placement, with
W3C ARIA APG keyboard navigation, animation presets, configurable backgrounds,
deep-linking, and responsive vertical-to-horizontal collapse.

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjt-tabs>` custom element extending `VvjElementBase`.

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

- Four position options: top / right / bottom / left
- Wrap-when-exceeded vs auto-scroll for tabs
- Configurable max width / max height
- Seven animation presets (none / fade / zoom / 4 slide directions / opacity)
- Configurable button + pane background colors (with disable toggle)
- Vertical-tabs collapse breakpoint (576 / 768 / 992 / 1200 / 1400 px)
- Optional CSS library
- W3C ARIA APG tab pattern: ArrowLeft/ArrowRight, Home, End, Enter/Space —
  automatic activation on arrow key
- Deep linking: shareable URL fragments per tab (`#tabs-products-3`)
- `[vvjt:field]` and `[vvjt:field:plain]` Views tokens for the **first row** in
  header/footer/empty text when *Use replacement tokens from the first row* is
  enabled
- Lazy IntersectionObserver hydration
- AbortController-tracked listeners
- View Transitions wrapping tab swaps (graceful fallback)
- Re-attaches Drupal.behaviors on newly-visible pane. Keeps nested views /
  field-group tabs working
- **Drupal admin help**: `/admin/help/vvjt`

## Requirements

- Drupal core `^11.3 || ^12`
- PHP 8.3 or later
- Drupal core modules Views (`drupal:views`) and Filter (`drupal:filter`)
- [VVJ Core](https://www.drupal.org/project/vvj_core) (`drupal/vvj_core` `^2.0.1`), which composer installs for you

Optional: the `vvj_patterns` sub-module of `drupal/vvj_core` adds the Pattern select to the format settings (see [Patterns](#patterns)), and `vvj_patterns_demo` adds example content.

## Installation

```bash
composer require drupal/vvjt:^2.0
drush en vvjt
```

### Upgrading from 1.x

`composer update drupal/vvjt && drush updb && drush cr` is sufficient. v2
preserves:

- Plugin ID `views_vvjt`, theme hook `views_view_vvjt`
- Twig template names
- All option keys
- Library names (`vvjt`, `vvjt-style`, `vvjt-horizontal`, `vvjt-vertical`,
  `vvjt-admin`, `vvjt__576/768/992/1200/1400`)
- JS behavior key `Drupal.behaviors.VVJTabs`
- CSS class names

Outer rendered tag changed from `<div>` to `<vvjt-tabs>`. Theme selectors
targeting `.vvjt` keep matching.

`drush updb` enables `vvj_core` (`vvjt_update_10001`) and converts existing views to the v2 option shape (`vvjt_update_10002`). Without `vvj_core` on disk the update stops with a message telling you to download `drupal/vvj_core` and run `drush updb` again.

## Configuration

All settings live in the format settings of the *Views Vanilla JavaScript Tabs* style on a View. They are grouped into layout, animation, styling and responsive sections, and a **Patterns** section when `vvj_patterns` is enabled. The extended help topic is at `/admin/help/vvjt`.

Settings are stored in the View itself, as options of the `views_vvjt` style plugin; their types are declared in `config/schema/vvjt.schema.yml`.

## Patterns

A Pattern is a ready-made design for this format. VVJT ships 5. Enable the `vvj_patterns` sub-module of `drupal/vvj_core` (`drush en vvj_patterns`), open the format settings and choose one in **Patterns** > **Pattern**.

| Pattern | Look |
|---|---|
| Dock | Full-width photo stage with the words bottom-left and the tabs in a floating dock at the bottom. |
| Contents | Numbered contents list on the left with a progress rail, the open chapter on the right. |
| Swatch | Photo thumbnails as tabs in a rail on the right; the picture opens as a circle with a words card over it. |
| Folder | Tabs joined to a raised card like folder tabs, with a square picture and shareable links to each tab. |
| Glossary | A scrolling strip of terms with scroll buttons and a counter over a wide cinema picture. |

When you choose a different Pattern and save, its settings replace the layout, animation, styling and deep linking settings of the view; you can still change them afterwards. Choose **None** to keep your own settings. Drupal then shows a message naming the settings the Pattern set.

Each Pattern also lists what the view still needs, for example the fields to add, their order and the CSS classes to give them. The list appears under **To finish the Pattern** in the Patterns section and as a warning after you save. The `vvj_patterns_demo` sub-module shows a working setup: it adds the "VVJ Slide" content type (fields `vvj_nav_label`, `vvj_image`, `vvj_kicker`, `vvj_summary` and `vvj_link`, besides the title), five demo slides and, as optional config, an example view for each VVJ module named `vvj_demo_<module>`.

## Usage

1. Create or edit a View.
2. Set **Format** to *Views Vanilla JavaScript Tabs*.
3. Set **Show** to *Fields*.
4. The first field becomes the tab button; remaining fields render in the tab
   pane.
5. Configure layout / animation / styling / responsive in the format settings.
6. Save.

**Sample view:** Optional config `views.view.vvjt_example` (`config/optional/`)
installs when there is no ID conflict.

### Tokens in Views text areas

In Views headers, footers, or empty text with *Use replacement tokens from the
first row*, default Twig tokens (`{{ title }}`) do not work. Use VVJT tokens
instead:

- `{{ title }}` → `[vvjt:title]`
- `{{ field_image }}` → `[vvjt:field_image]`
- Append `:plain` for plain text: `[vvjt:title:plain]`

v2 resolves tokens through the shared `vvj_core.token_resolver` service; syntax
is unchanged from v1. Tokens read from the **first row** of rendered View
fields. Complex field rewrites are not supported.

### Public API

External code can drive tab groups via `Drupal.vvjt.*`. Pass the deep link
identifier, a CSS selector, or an element reference:

```js
Drupal.vvjt.goToTab('features', 3);
Drupal.vvjt.getCurrentTab('features');
Drupal.vvjt.getTotalTabs('features');
Drupal.vvjt.getInstance('#vvjt-12345');        // → <vvjt-tabs> element
Drupal.vvjt.getAllInstances();
```

## Troubleshooting

- **There is no Patterns section.** The section appears only while `vvj_patterns` is enabled. Run `drush en vvj_patterns`.
- **Saving the view changed my settings.** Choosing a different Pattern replaces the layout, animation, styling and deep linking settings. Choose **None** to keep your own.
- **A Pattern looks unfinished.** Follow the list under **To finish the Pattern**: the fields, their order and the CSS classes the Pattern needs.
- **After upgrading from 1.x the view fails or `vvj_core` is not enabled.** Run `drush updb`; update `vvjt_update_10001` enables `vvj_core`. Composer installs it automatically, and a manual install needs `drupal/vvj_core` in `modules/contrib/vvj_core` first.
- **Test errors that name missing infrastructure.** See *Running the tests* in [CONTRIBUTING.md](./CONTRIBUTING.md).
- **Anything else.** Search or open an issue in the [issue queue](https://www.drupal.org/project/issues/vvjt).

## Changelog

See [CHANGELOG.md](./CHANGELOG.md).

## Maintainers

- Alaa Haddad - [flashwebcenter](https://www.drupal.org/u/flashwebcenter)

## License

GPL-2.0-or-later.
