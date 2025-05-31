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
