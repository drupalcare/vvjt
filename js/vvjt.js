/**
 * @file
 *
 * Filename:     vvjt.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  /**
   * VVJ Tabs behavior.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the VVJ Tabs functionality.
   * @prop {Drupal~behaviorDetach} detach
   *   Detaches the VVJ Tabs functionality.
   */
  Drupal.behaviors.VVJTabs = {
    attach: function (context, settings) {
      // Process each tab container only once
      const tabContainers = once('vvj-tabs-init', '.vvjt-inner', context);

      tabContainers.forEach(container => {
        this.initializeContainer(container, settings);
      });
    },

    detach: function (context, settings, trigger) {
      // Clean up when behaviors are detached
      if (trigger === 'unload') {
        once.remove('vvj-tabs-init', '.vvjt-inner', context);
      }
    },

    /**
     * Initialize a tab container.
     *
     * @param {HTMLElement} container
     *   The tab container element.
     * @param {object} settings
     *   The drupalSettings object.
     */
    initializeContainer: function(container, settings) {
      const tabButtons = container.querySelectorAll('.vvjt-button');
      const tabPanes = container.querySelectorAll('.vvjt-pane');

      // Initialize ARIA attributes
      this.initializeAria(tabButtons, tabPanes);

      // Use event delegation for better performance
      container.addEventListener('click', (event) => {
        this.handleTabClick(event, container, settings);
      });

      container.addEventListener('keydown', (event) => {
        this.handleKeyboardNavigation(event, container, settings);
      });

      // Update aria-labels
      this.updateAriaLabels(tabButtons);

      // Disable links within buttons
      this.disableButtonLinks(tabButtons);

      // Show first tab and attach behaviors to it
      if (tabPanes[0]) {
        this.showTab(container, tabButtons[0], tabPanes[0], settings);
      }
    },

    /**
     * Initialize ARIA attributes for accessibility.
     *
     * @param {NodeList} buttons
     *   Tab button elements.
     * @param {NodeList} panes
     *   Tab pane elements.
     */
    initializeAria: function(buttons, panes) {
      buttons.forEach((button, index) => {
        const isFirst = index === 0;
        button.setAttribute('aria-selected', isFirst ? 'true' : 'false');
        button.setAttribute('aria-expanded', isFirst ? 'true' : 'false');
        button.setAttribute('tabindex', isFirst ? '0' : '-1');

        if (isFirst) {
          button.classList.add('active');
        }
      });

      panes.forEach((pane, index) => {
        const isFirst = index === 0;
        pane.style.display = isFirst ? 'block' : 'none';
        pane.setAttribute('aria-hidden', isFirst ? 'false' : 'true');
      });
    },

    /**
     * Handle tab click events.
     *
     * @param {Event} event
     *   The click event.
     * @param {HTMLElement} container
     *   The tab container.
     * @param {object} settings
     *   The drupalSettings object.
     */
    handleTabClick: function(event, container, settings) {
      const button = event.target.closest('.vvjt-button');
      if (!button) return;

      event.preventDefault();

      // Parse the button ID to get the corresponding pane ID
      // Button ID format: vvjt-button-{unique_id}-{key}
      // Pane ID format: vvjt-pane-{unique_id}-{key}
      const btnIdParts = button.id.split('-');
      const rawId = btnIdParts.slice(2).join('-'); // Get everything after 'vvjt-button-'
      const paneId = `vvjt-pane-${rawId}`;
      const currentPane = container.querySelector(`#${paneId}`);

      if (currentPane) {
        this.showTab(container, button, currentPane, settings);
      } else {
        console.warn(`Pane with ID ${paneId} not found.`);
      }
    },

    /**
     * Show a specific tab and trigger behaviors on its content.
     *
     * @param {HTMLElement} container
     *   The tab container.
     * @param {HTMLElement} activeButton
     *   The button to activate.
     * @param {HTMLElement} activePane
     *   The pane to show.
     * @param {object} settings
     *   The drupalSettings object.
     */
    showTab: function(container, activeButton, activePane, settings) {
      const buttons = container.querySelectorAll('.vvjt-button');
      const panes = container.querySelectorAll('.vvjt-pane');

      // Hide all panes
      panes.forEach(pane => {
        pane.style.display = 'none';
        pane.setAttribute('aria-hidden', 'true');
      });

      // Remove active state from all buttons
      buttons.forEach(button => {
        button.classList.remove('active');
        button.setAttribute('tabindex', '-1');
        button.setAttribute('aria-selected', 'false');
        button.setAttribute('aria-expanded', 'false');
      });

      // Show the active pane
      activePane.style.display = 'block';
      activePane.setAttribute('aria-hidden', 'false');

      // Activate the button
      activeButton.classList.add('active');
      activeButton.removeAttribute('tabindex');
      activeButton.setAttribute('aria-selected', 'true');
      activeButton.setAttribute('aria-expanded', 'true');
      activeButton.focus();

      // CRITICAL FIX: Re-attach behaviors to newly visible content
      // This fixes the Field Group tabs and nested views issue
      requestAnimationFrame(() => {
        Drupal.attachBehaviors(activePane, settings || drupalSettings);
      });
    },

    /**
     * Handle keyboard navigation for accessibility.
     *
     * @param {KeyboardEvent} event
     *   The keyboard event.
     * @param {HTMLElement} container
     *   The tab container.
     * @param {object} settings
     *   The drupalSettings object.
     */
    handleKeyboardNavigation: function(event, container, settings) {
      const button = event.target.closest('.vvjt-button');
      if (!button) return;

      const buttons = Array.from(container.querySelectorAll('.vvjt-button'));
      const currentIndex = buttons.indexOf(button);
      let newIndex = currentIndex;

      switch (event.key) {
        case 'ArrowRight':
          newIndex = (currentIndex + 1) % buttons.length;
          break;
        case 'ArrowLeft':
          newIndex = (currentIndex - 1 + buttons.length) % buttons.length;
          break;
        case 'Home':
          event.preventDefault();
          newIndex = 0;
          break;
        case 'End':
          event.preventDefault();
          newIndex = buttons.length - 1;
          break;
        case 'Enter':
        case ' ':
          event.preventDefault();
          // Trigger click on current button
          button.click();
          return;
        default:
          return;
      }

      event.preventDefault();

      // Focus and click the new button
      const newButton = buttons[newIndex];
      if (newButton) {
        newButton.focus();
        // Parse IDs to find corresponding pane
        const btnIdParts = newButton.id.split('-');
        const rawId = btnIdParts.slice(2).join('-');
        const paneId = `vvjt-pane-${rawId}`;
        const pane = container.querySelector(`#${paneId}`);

        if (pane) {
          this.showTab(container, newButton, pane, settings);
        }
      }
    },

    /**
     * Update ARIA labels based on button content.
     *
     * @param {NodeList} buttons
     *   Tab button elements.
     */
    updateAriaLabels: function(buttons) {
      buttons.forEach(button => {
        const img = button.querySelector('img');
        const label = img?.alt || button.textContent.trim() || 'Tab';
        button.setAttribute('aria-label', label);
      });
    },

    /**
     * Disable links within tab buttons.
     *
     * @param {NodeList} buttons
     *   Tab button elements.
     */
    disableButtonLinks: function(buttons) {
      buttons.forEach(button => {
        const links = button.querySelectorAll('a');
        links.forEach(link => {
          link.setAttribute('data-original-href', link.getAttribute('href'));
          link.removeAttribute('href');
          link.style.cursor = 'default';
          link.addEventListener('click', function(event) {
            event.preventDefault();
          });
        });
      });
    }
  };

})(Drupal, drupalSettings, once);
