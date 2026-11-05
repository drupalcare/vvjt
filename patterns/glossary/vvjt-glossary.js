/**
 * @file
 * Pattern Glossary: scroll buttons, fade edges and the counter for the strip.
 *
 * Built on the shared helpers: Drupal.Vvj.ScrollStrip (vvj_patterns/
 * scroll-strip) for the strip and its buttons, Drupal.Vvj.onState
 * (vvj_patterns/state) to follow the active tab the element writes through
 * ElementBase.writeState(). Only the strip scrolls when a tab opens; the
 * page never moves.
 *
 * @license SPDX-License-Identifier: GPL-2.0-or-later
 */

((Drupal, once) => {
  /** @type {WeakMap<HTMLElement, AbortController>} */
  const controllers = new WeakMap();

  /**
   * Wires the strip of one Glossary instance.
   *
   * @param {HTMLElement} root
   *   The vvjt-tabs element carrying the Pattern class.
   */
  function wire(root) {
    const stripEl = root.querySelector('.tab-buttons');
    if (!stripEl) {
      return;
    }
    const controller = new AbortController();
    controllers.set(root, controller);
    const { signal } = controller;

    const strip = new Drupal.Vvj.ScrollStrip(stripEl, {
      prev: root.querySelector('.vvjt-scroll-prev'),
      next: root.querySelector('.vvjt-scroll-next'),
      root,
      signal,
    });
    const buttons = stripEl.querySelectorAll('.vvjt-button');
    const now = root.querySelector('.vvjt-count-now');

    Drupal.Vvj.onState(
      root,
      ({ index, total }) => {
        if (now) {
          now.textContent = Drupal.Vvj.formatCount(index + 1, total, {
            pad: true,
          });
        }
        strip.scrollToItem(index, buttons);
      },
      signal,
    );
  }

  Drupal.behaviors.vvjPatternVvjtGlossary = {
    attach(context) {
      once(
        'vvj-pattern-vvjt-glossary',
        '.vvj-pattern--vvjt-glossary',
        context,
      ).forEach(wire);
    },
    detach(context, settings, trigger) {
      if (trigger !== 'unload') {
        return;
      }
      once
        .remove(
          'vvj-pattern-vvjt-glossary',
          '.vvj-pattern--vvjt-glossary',
          context,
        )
        .forEach((root) => {
          controllers.get(root)?.abort();
          controllers.delete(root);
        });
    },
  };
})(Drupal, once);
