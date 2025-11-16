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

      // Initialize deep linking if enabled - this may activate a tab from URL hash
      const deepLinkActivated = this.initializeDeepLinking(container, tabButtons, tabPanes, settings);

      // Show first tab only if deep linking didn't activate a tab
      if (!deepLinkActivated && tabPanes[0]) {
        this.showTab(container, tabButtons[0], tabPanes[0], settings, false);
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

      // Check if deep linking is enabled
      const deeplinkEnabled = container.getAttribute('data-deeplink-enabled') === 'true';
      const deeplinkId = container.getAttribute('data-deeplink-id');

      // If deep linking is enabled and button is an anchor, let it update the hash naturally
      if (deeplinkEnabled && deeplinkId && button.tagName === 'A' && button.href) {
        // Don't prevent default - let the anchor update the URL hash
        // The hash change will be handled by the hashchange listener
      } else {
        // Prevent default for buttons
        event.preventDefault();
      }

      // Parse the button ID to get the corresponding pane ID
      // Button ID format: vvjt-button-{unique_id}-{key}
      // Pane ID format: vvjt-pane-{unique_id}-{key}
      const btnIdParts = button.id.split('-');
      const rawId = btnIdParts.slice(2).join('-'); // Get everything after 'vvjt-button-'
      const paneId = `vvjt-pane-${rawId}`;
      const currentPane = container.querySelector(`#${paneId}`);

      if (currentPane) {
        this.showTab(container, button, currentPane, settings, true);
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
     * @param {boolean} moveFocus
     *   Whether to move focus to the active button. Default false.
     */
    showTab: function(container, activeButton, activePane, settings, moveFocus = false) {
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
      activeButton.setAttribute('tabindex', '0');
      activeButton.setAttribute('aria-selected', 'true');
      activeButton.setAttribute('aria-expanded', 'true');

      // Only move focus when called from user interaction
      if (moveFocus) {
        activeButton.focus();
      }

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
        // Parse IDs to find corresponding pane
        const btnIdParts = newButton.id.split('-');
        const rawId = btnIdParts.slice(2).join('-');
        const paneId = `vvjt-pane-${rawId}`;
        const pane = container.querySelector(`#${paneId}`);

        if (pane) {
          this.showTab(container, newButton, pane, settings, true);
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
    },

    /**
     * Initialize deep linking functionality.
     *
     * @param {HTMLElement} container
     *   The tab container.
     * @param {NodeList} buttons
     *   Tab button elements.
     * @param {NodeList} panes
     *   Tab pane elements.
     * @param {object} settings
     *   The drupalSettings object.
     * 
     * @return {boolean}
     *   True if a tab was activated from the URL hash, false otherwise.
     */
    initializeDeepLinking: function(container, buttons, panes, settings) {
      const deeplinkEnabled = container.getAttribute('data-deeplink-enabled') === 'true';
      const deeplinkId = container.getAttribute('data-deeplink-id');

      if (!deeplinkEnabled || !deeplinkId) {
        return false;
      }

      // Store for external access
      container.deeplinkConfig = {
        enabled: true,
        identifier: deeplinkId
      };

      // Check URL hash on page load
      let tabActivated = false;
      const hash = window.location.hash;
      if (hash && hash.startsWith(`#tabs-${deeplinkId}-`)) {
        const tabNumber = parseInt(hash.split('-').pop(), 10);
        if (tabNumber >= 1 && tabNumber <= buttons.length) {
          const button = buttons[tabNumber - 1];
          const pane = panes[tabNumber - 1];
          if (button && pane) {
            this.showTab(container, button, pane, settings, false);
            tabActivated = true;
          }
        }
      }

      // Listen for hash changes (browser back/forward and anchor clicks)
      const self = this;
      const hashChangeHandler = function() {
        const currentHash = window.location.hash;
        // Only respond to hash changes for this specific tab group
        if (currentHash && currentHash.startsWith(`#tabs-${deeplinkId}-`)) {
          const tabNumber = parseInt(currentHash.split('-').pop(), 10);
          if (tabNumber >= 1 && tabNumber <= buttons.length) {
            const button = buttons[tabNumber - 1];
            const pane = panes[tabNumber - 1];
            if (button && pane) {
              self.showTab(container, button, pane, settings, false);
            }
          }
        }
      };

      // Store the handler on the container so we can remove it later if needed
      container.vvjtHashChangeHandler = hashChangeHandler;
      window.addEventListener('hashchange', hashChangeHandler);

      return tabActivated;
    }
  };

  /**
   * Global utility functions for external access.
   */
  Drupal.vvjt = Drupal.vvjt || {};

  /**
   * Helper function to get tab container by identifier.
   */
  function getContainerByIdentifier(identifier) {
    let container;

    // Try deep link identifier first (if not a CSS selector)
    if (!identifier.startsWith('.') && !identifier.startsWith('#')) {
      container = document.querySelector(`[data-deeplink-id="${identifier}"]`);
    }

    // Fallback to CSS selector
    if (!container) {
      container = document.querySelector(identifier);
    }

    return container;
  }

  /**
   * Get tab container instance.
   */
  Drupal.vvjt.getInstance = function(containerOrSelector) {
    let container;

    if (typeof containerOrSelector === 'string') {
      container = getContainerByIdentifier(containerOrSelector);
    } else {
      container = containerOrSelector;
    }

    if (!container) {
      return null;
    }

    // Find the .vvjt-inner element
    const inner = container.classList.contains('vvjt-inner')
      ? container
      : container.querySelector('.vvjt-inner');

    return inner || null;
  };

  /**
   * Navigate to a specific tab.
   */
  Drupal.vvjt.goToTab = function(identifier, tabIndex) {
    const container = getContainerByIdentifier(identifier);

    if (!container) {
      if (typeof console !== 'undefined' && console.warn) {
        console.warn(`VVJT: Tabs "${identifier}" not found`);
      }
      return false;
    }

    const buttons = container.querySelectorAll('.vvjt-button');
    const panes = container.querySelectorAll('.vvjt-pane');

    if (tabIndex < 1 || tabIndex > buttons.length) {
      if (typeof console !== 'undefined' && console.warn) {
        console.warn(`VVJT: Invalid tab index ${tabIndex}. Must be between 1 and ${buttons.length}`);
      }
      return false;
    }

    const button = buttons[tabIndex - 1];
    const pane = panes[tabIndex - 1];

    if (button && pane) {
      Drupal.behaviors.VVJTabs.showTab(container, button, pane, drupalSettings, true);
      
      // Update hash if deep linking is enabled
      const deeplinkConfig = container.deeplinkConfig;
      if (deeplinkConfig && deeplinkConfig.enabled) {
        const newHash = `#tabs-${deeplinkConfig.identifier}-${tabIndex}`;
        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, '', newHash);
        } else {
          window.location.hash = newHash;
        }
      }
      
      return true;
    }

    return false;
  };

  /**
   * Get current active tab index.
   */
  Drupal.vvjt.getCurrentTab = function(identifier) {
    const container = getContainerByIdentifier(identifier);

    if (!container) {
      return null;
    }

    const buttons = container.querySelectorAll('.vvjt-button');
    for (let i = 0; i < buttons.length; i++) {
      if (buttons[i].getAttribute('aria-selected') === 'true') {
        return i + 1;
      }
    }

    return null;
  };

  /**
   * Get total number of tabs.
   */
  Drupal.vvjt.getTotalTabs = function(identifier) {
    const container = getContainerByIdentifier(identifier);

    if (!container) {
      return null;
    }

    const buttons = container.querySelectorAll('.vvjt-button');
    return buttons.length;
  };

})(Drupal, drupalSettings, once);
