# Views Vanilla JavaScript Tabs (VVJT)

A vanilla-JavaScript tab group **Views format** for Drupal 11.3+ / 12. Renders
Views rows as a tabbed interface — top, bottom, left, or right placement — with
W3C ARIA APG keyboard navigation, animation presets, configurable backgrounds,
deep-linking, and responsive vertical-to-horizontal collapse.

v2.0 builds on the **`drupal/vvj_core` foundation** (composer auto-installs it).
Behavior runs in a `<vvjt-tabs>` custom element extending `VvjElementBase`.

## Features

- Four position options: top / right / bottom / left
- Wrap-when-exceeded vs auto-scroll for tabs
- Configurable max width / max height
- Seven animation presets (none / fade / zoom / 4 slide directions / opacity)
- Configurable button + pane background colors (with disable toggle)
- Vertical-tabs collapse breakpoint (576 / 768 / 992 / 1200 / 1400 px)
- Optional CSS library
- W3C ARIA APG tab pattern: ArrowLeft/ArrowRight, Home, End, Enter/Space —
  automatic activation on arrow key
- Deep linking — shareable URL fragments per tab (`#tabs-products-3`)
- `[vvjt:field]` and `[vvjt:field:plain]` Views tokens for the **first row** in
  header/footer/empty text when *Use replacement tokens from the first row* is
  enabled
- Lazy IntersectionObserver hydration
- AbortController-tracked listeners
- View Transitions wrapping tab swaps (graceful fallback)
- Re-attaches Drupal.behaviors on newly-visible pane — keeps nested views /
  field-group tabs working
- **Drupal admin help** — `/admin/help/vvjt`

## Installation

```bash
composer require drupal/vvjt:^2.0
drush en vvjt
```

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

## Drop-in upgrade from 1.x

`composer update drupal/vvjt && drush cache:rebuild` is sufficient. v2
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

## License

GPL-2.0-or-later.
