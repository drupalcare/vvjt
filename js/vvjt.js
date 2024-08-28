/**
 * @file
 * VVJ Tabs.
 *
 * Filename:     vvjt.js
 * Website:      https://www.flashwebcenter.com
 * Developer:    Alaa Haddad https://www.alaahaddad.com.
 */
((Drupal, drupalSettings, once) => {
  'use strict';

  Drupal.behaviors.VVJTabs = {
    attach: function (context, settings) {
      // Function to hide all panes
      function hideAllPanes(panes) {
        panes.forEach(pane => {
          if (pane) {
            pane.style.display = 'none';
            pane.setAttribute('aria-hidden', 'true');
          }
        });
      }

      // Function to remove active class from buttons
      function removeActiveClassFromButtons(buttons) {
        buttons.forEach(button => {
          if (button) {
            button.classList.remove('active');
            button.setAttribute('tabindex', '-1');
            button.setAttribute('aria-selected', 'false');
            button.setAttribute('aria-expanded', 'false');
          }
        });
      }

      // Function to handle tab clicks
      function handleTabClicks(tabContainer, event) {
        const button = event.target.closest('.vvjt-button');
        if (!button) return;

        const btnIdParts = button.id.split('-');
        const raw_id = btnIdParts.slice(2).join('-');
        const paneId = `vvjt-pane-${raw_id}`;
        const currentPane = tabContainer.querySelector(`#${paneId}`);
        const tabPanes = tabContainer.querySelectorAll('.vvjt-pane');
        const tabButtons = tabContainer.querySelectorAll('.vvjt-button');

        if (currentPane) {
          hideAllPanes(tabPanes);
          removeActiveClassFromButtons(tabButtons);
          currentPane.style.display = 'block';
          currentPane.setAttribute('aria-hidden', 'false');
          button.classList.add('active');
          button.removeAttribute('tabindex');
          button.setAttribute('aria-selected', 'true');
          button.setAttribute('aria-expanded', 'true');
          button.focus();
        } else {
          console.warn(`Pane with ID ${paneId} not found.`);
        }
      }

      // Function to handle keyboard navigation
      function handleKeyboardNavigation(tabButtons) {
        tabButtons.forEach((tab, index) => {
          tab.addEventListener('keydown', (event) => {
            const currentIndex = index;
            let newIndex;

            switch (event.key) {
              case 'ArrowRight':
                newIndex = (currentIndex + 1) % tabButtons.length;
                break;
              case 'ArrowLeft':
                newIndex = (currentIndex - 1 + tabButtons.length) % tabButtons.length;
                break;
              case 'Home':
                newIndex = 0;
                break;
              case 'End':
                newIndex = tabButtons.length - 1;
                break;
              default:
                return;
            }

            tabButtons.forEach(t => t.setAttribute('tabindex', '-1'));
            tabButtons[newIndex].setAttribute('tabindex', '0');
            tabButtons[newIndex].focus();
            event.preventDefault();
          });
        });
      }

      // Function to update aria-labels for tab buttons based on image alt text
      function updateAriaLabelsForButtons(tabButtons) {
        tabButtons.forEach(button => {
          const img = button.querySelector('img');
          if (img && img.alt) {
            button.setAttribute('aria-label', img.alt);
          } else {
            button.setAttribute('aria-label', button.textContent.trim() || 'Tab');
          }
        });
      }

      // Function to disable links within tab buttons
      function disableLinksInTabButtons(tabButtons) {
        tabButtons.forEach(button => {
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

      // Scoped selection using `once` to ensure the behavior is only applied once per element
      const tabContainers = once('vvjTabs', '.vvjt-inner', context);
      tabContainers.forEach(container => {
        const tabButtons = container.querySelectorAll('.vvjt-button');
        const tabPanes = container.querySelectorAll('.vvjt-pane');

        // Initialize ARIA attributes and tabindex on page load
        tabButtons.forEach((button, index) => {
          if (index === 0) {
            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');
            button.setAttribute('aria-expanded', 'true');
            button.setAttribute('tabindex', '0');
            if (tabPanes[index]) {
              tabPanes[index].style.display = 'block';
              tabPanes[index].setAttribute('aria-hidden', 'false');
            }
          } else {
            button.setAttribute('aria-selected', 'false');
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('tabindex', '-1');
            if (tabPanes[index]) {
              tabPanes[index].style.display = 'none';
              tabPanes[index].setAttribute('aria-hidden', 'true');
            }
          }

          // Attach click event listener scoped to the current container
          button.addEventListener('click', handleTabClicks.bind(null, container));
        });

        // Handle keyboard navigation scoped to the current container
        handleKeyboardNavigation(tabButtons);

        // Update aria-labels for buttons based on content scoped to the current container
        updateAriaLabelsForButtons(tabButtons);

        // Disable links within tab buttons scoped to the current container
        disableLinksInTabButtons(tabButtons);
      });
    }
  };
})(Drupal, drupalSettings, once);



