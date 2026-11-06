# Changelog

## [2.0.0] (UNRELEASED)

- Patterns: the bridge reads the shared `--vvj-theme-*` roles instead of spelling theme names.
- Patterns: under Solo or APEX the Pattern controls take the theme's button and menu colors; black and white stays the default on any other theme.
- Layout: the breakpoint stylesheets (`vvjt-576` to `vvjt-1400`) no longer match a Pattern, so a Pattern's layout comes from its own CSS only. Classic tabs render as before.
- Accessibility: long Glossary terms now wrap inside their chip instead of being cut with an ellipsis.
- Accessibility: the active pane is no longer a live region; Contents shows a visible ink focus ring on the active chip; the Contents and Glossary strips no longer clip the focus ring; the Glossary parallax runs only when motion is allowed.

**Drop-in upgrade for 1.x sites.** v2 modernizes internals: extends `vvj_core`
foundation, ships `<vvjt-tabs>` custom element extending `VvjElementBase`, W3C
ARIA APG keyboard navigation, View Transitions wrapping tab swaps, while
preserving every CSS rule, library name (incl. all 5 breakpoint libraries +
horizontal/vertical/style/admin), option key, plugin ID, theme hook, JS behavior
key from 1.x.

### Added

- Help page: follows the drupal.org help text standard (translatable About and Uses sections) instead of printing README.md as raw text.
- Patterns: 5 ready-made designs (Dock, Contents, Swatch, Folder, Glossary) chosen in the format settings. They need the `vvj_patterns` sub-module of `drupal/vvj_core`.
- `drupal/vvj_core` dependency.
- `<vvjt-tabs>` custom element. Lazy IntersectionObserver hydration;
  AbortController-tracked listeners; ARIA APG tab pattern conformance;
  `Drupal.attachBehaviors()` re-attached on the newly-visible pane (preserves
  nested-views + field-group fix from v1).

### Changed

- Text follows the drupal.org standards: the module description begins with a verb, "JavaScript" is spelled as the language spells it, settings titles are in sentence case, and the README has its project and issue queue links.
- Plugin extends `VvjStylePluginBase`. `buildLibraryList()` overridden to add
  `vvjt-horizontal` / `vvjt-vertical` + `vvjt__BP` breakpoint variant
  conditionally, same as v1.
- Token resolution delegates to `vvj_core.token_resolver`.
- Hooks moved to OOP `#[Hook]` classes.
- Code style: JavaScript and CSS follow Drupal core's ESLint, Stylelint and Prettier rules, and the spell-check job passes with a project word file. Rendering and behavior are unchanged.
- Layout: Pattern layout now follows the width the Pattern itself gets (container queries on `vvj-region`) instead of the screen width, so Patterns lay out correctly in a narrow content column; overlays and the Pattern root keep screen-width queries.
- Pattern Contents: its layout now follows the width the view gets, not the screen width, so in a content column beside a sidebar the chapter no longer squeezes into a thin strip (rail from 768px, picture beside text from 1100px of its own width).

### Fixed

- Vertical tabs now turn into a horizontal row below the chosen 576, 768, 992 or 1200 breakpoint; the narrow rules had targeted the 1400 class only.
- Hover rules that must beat a rest rule are flattened with `:where()` so no new Pattern selector exceeds 0,2,0; the look is unchanged (check-vvj-cascade: winners changed 0).
- Patterns: Dock tabs scroll inside the dock when they are wider than the stage, instead of spilling outside it.
- Update 10002 converts v1 views to the v2 option shape (value types:
  strings and integers stored where v2 declares integer, boolean and float).
- Non-navigation keys (Tab, Shift+Tab, letters) on a focused tab button are no-
  ops again (v1 behavior). A missing guard in the custom element re-activated
  the current tab on every keystroke, causing a transition flash, focus
  fighting the browser's Tab, and (for deep-link tabs) a spurious URL-hash
  change.
- Restored the public `Drupal.vvjt.*` JS API (`getInstance`, `goToTab`,
  `getCurrentTab`, `getTotalTabs`) that the v2 rewrite had dropped. Now
  delegating to methods on `<vvjt-tabs>`.
- Deep-link identifier reserved words back to the tab-specific list (`tabs`,
  `tab`, `vvjt`, `vvj`) so identifiers can no longer collide with the
  `#tabs-<id>-<n>` fragment scheme (was wrongly using the base's accordion
  list).
- Deep-link "URL Identifier" field show/hide + required toggle now works (fixed
  in `vvj_core` base. The toggle attributes are slug-derived).
- Removed the phantom "Active on all screens" (`all`) option from the responsive
  breakpoint select (PHP array-union bug leaked the base registry list; v1
  offered exactly 576/768/992/1200/1400).

### Drupal core / PHP

- Requires Drupal `^11.3 || ^12`, PHP `>=8.3`.
