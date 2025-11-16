# Views Vanilla JavaScript Tabs

## Overview
The **Views Vanilla JavaScript Tabs** module (machine name: `vvjt`) is 
a powerful 
and flexible Views style plugin designed to render items in tabs using vanilla 
JavaScript. This module enhances the user experience by providing smooth tabbed 
navigation for content displayed through Drupal Views.

## Features
- **Animation Types**: Choose from various animation types such as Top, Bottom, 
  Left, Right, Zoom, and Opacity to create engaging transitions between tabs.
- **Unique ID Generation**: Automatically generates a unique numeric ID for each 
  view display to ensure consistency and prevent conflicts.
- **Vertical Tabs**: Option to enable vertical tabs with additional 
 configurations 
  for maximum width and responsive breakpoints.
- **Custom Breakpoints**: Define custom breakpoints for when the vertical tabs 
  should collapse into horizontal tabs, enhancing mobile and responsive design 
  support.

## Configuration
After installing and enabling the module, you can configure it through the Views 
UI:

1. **Animation Type**: Select the type of animation for tab transitions.
2. **Enable Vertical Tabs**: Check this option to enable vertical tabs. When 
   enabled, additional settings for maximum width and breakpoints will be 
   available.
3. **Max Width**: Define the maximum width for the left buttons when using 
   vertical tabs. Options include 300px, 400px, 500px, and 600px.
4. **Available Breakpoints**: Choose the breakpoint at which the vertical tabs 
   will collapse and become horizontal. Options include 576px, 768px, 992px, 
   1200px, and 1400px.

## Token Support in Views Text Areas

In Views headers, footers, or empty text areas—when using *Global: Text area* or
*Global: Unfiltered text*—there is an option called **"Use replacement tokens
from the first row."**

The default Twig-style tokens (e.g., `{{ title }}` or `{{ field_image }}`)
**will not work** with the VVJT style. Instead, use the custom tokens provided
by VVJT:

**Examples:**

- `{{ title }}` → `[vvjt:title]`  
- `{{ field_image }}` → `[vvjt:field_image]`

To strip any HTML from the output, you can append `:plain` to the token:

- `[vvjt:title:plain]`

These tokens pull data from the **first row** of the View result and are
designed to work seamlessly with the VVJT rendering system.

## Deep Linking

Enable deep linking to create shareable URLs that link directly to specific 
tabs in your interface.

### Setup

1. Navigate to your view's **Format Settings**
2. Expand the **Deep Linking Settings** section
3. Check **Enable Deep Linking**
4. Enter a **URL Identifier** (e.g., "products", "services", "team")
   - The identifier will be automatically cleaned: converted to lowercase, 
     spaces replaced with hyphens, special characters removed
   - Must start with a letter and contain only lowercase letters, numbers, and 
     hyphens

### URL Format

When deep linking is enabled, URLs are generated in the following format:

```
#tabs-identifier-N
```

**Examples:**
- `#tabs-products-1` (first tab)
- `#tabs-services-3` (third tab)
- `#tabs-team-2` (second tab)

### Features

- **Shareable Links**: Users can copy the URL to share a specific tab
- **Browser Navigation**: Back/forward buttons work with tab navigation
- **Initial Load**: Page loads directly to the specified tab when URL hash is 
  present
- **Multiple Tab Groups**: Each tab group on a page can have its own unique 
  identifier

## JavaScript API

Control VVJT tabs programmatically from your own JavaScript code.

### Navigation Methods

```javascript
// Navigate to a specific tab (1-based index)
Drupal.vvjt.goToTab('products', 2);
```

### Information Methods

```javascript
// Get current active tab number (1-based)
const current = Drupal.vvjt.getCurrentTab('products');
// Returns: 2

// Get total number of tabs
const total = Drupal.vvjt.getTotalTabs('products');
// Returns: 5
```

### Instance Access

```javascript
// Get tab container instance by identifier or selector
const tabs = Drupal.vvjt.getInstance('products');
// or
const tabs = Drupal.vvjt.getInstance('.vvjt-inner');
```

### Complete Example: Custom Tab Navigation

```html
<!-- Custom tab selector -->
<div class="custom-tab-selector">
  <button onclick="Drupal.vvjt.goToTab('products', 1)">Overview</button>
  <button onclick="Drupal.vvjt.goToTab('products', 2)">Features</button>
  <button onclick="Drupal.vvjt.goToTab('products', 3)">Pricing</button>
</div>

<script>
(function(Drupal) {
  'use strict';
  
  Drupal.behaviors.customTabControls = {
    attach: function(context, settings) {
      // Get current tab on page load
      const currentTab = Drupal.vvjt.getCurrentTab('products');
      console.log('Current tab:', currentTab);
      
      // Total tabs
      const totalTabs = Drupal.vvjt.getTotalTabs('products');
      console.log('Total tabs:', totalTabs);
      
      // Custom next button
      document.querySelector('.next-tab-button').addEventListener('click', function() {
        const current = Drupal.vvjt.getCurrentTab('products');
        const total = Drupal.vvjt.getTotalTabs('products');
        const next = current < total ? current + 1 : 1;
        Drupal.vvjt.goToTab('products', next);
      });
      
      // Custom previous button  
      document.querySelector('.prev-tab-button').addEventListener('click', function() {
        const current = Drupal.vvjt.getCurrentTab('products');
        const total = Drupal.vvjt.getTotalTabs('products');
        const prev = current > 1 ? current - 1 : total;
        Drupal.vvjt.goToTab('products', prev);
      });
    }
  };
})(Drupal);
</script>
```

### Using with Deep Link Identifiers

The API methods accept either:
- **Deep link identifier**: `'products'`, `'services'`, `'team'`
- **CSS selector**: `'.vvjt-inner'`, `'#vvjt-inner-12345678'`

When using deep link identifiers, make sure the identifier matches what you 
configured in the View's Format Settings.

### Return Values

- `goToTab()` returns `true` on success, `false` on failure
- `getCurrentTab()` returns the tab index (1-based) or `null` if not found
- `getTotalTabs()` returns the number of tabs or `null` if not found
- All methods provide helpful console warnings for debugging when errors occur

## Example Usage
1. Create a new view or edit an existing view.
2. Under the "Format" section, select "Views Vanilla JavaScript Tabs" as the 
   style.
3. Configure the animation type, vertical tabs settings, and responsive 
   breakpoints as needed.
4. Save the view and see the tabbed navigation in action on the front end.

## Additional Information
- **Dependencies**: This module does not have any dependencies on other 
  contributed modules.
- **Compatibility**: Fully compatible with Drupal 10 and follows Drupal best 
  practices.

## Installation
1. Download and install the module from 
   [Drupal.org](https://www.drupal.org/project/vvjt).
2. Enable the module through the Drupal admin interface or using Drush: 
   `drush en vvjt`
3. Clear caches to ensure the new plugin is available: `drush cr`

## Troubleshooting
If you encounter any issues or have suggestions for improvements, please open 
an issue in the module's issue queue on Drupal.org.

## Maintainers
- [Alaa Haddad](https://www.drupal.org/u/flashwebcenter)

## Support
For issues, feature requests, and general support, please use the 
[issue queue](https://www.drupal.org/project/issues/vvjt).

## License
This project is licensed under the 
[GNU General Public License, version 2 or later](http://www.gnu.org/licenses/gpl-2.0.html).

---

This file follows the Drupal best practices for module documentation, ensuring 
that users have a clear understanding of the module's purpose, features, and 
usage. It also includes important notes on pagination to guide users in setting 
up the module correctly.
