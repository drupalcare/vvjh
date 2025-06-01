# Views Vanilla JavaScript Hero

## Overview
The **Views Vanilla JavaScript Hero** module (machine name: `vvjh`) is a 
versatile and dynamic Views style plugin designed to render content items in 
an engaging hero format using vanilla JavaScript. This module enhances your 
site by adding a visually appealing, interactive hero effect to content 
displayed through Drupal Views.

## Features
- **Interactive Hero Boxes**: Display content items with hero animations 
  for a dynamic user experience.
- **Customizable Options**: Adjustable settings for hero style, speed, 
  background colors, and more.
- **Responsive Design**: Built-in breakpoints to ensure the hero adapts 
  seamlessly to different screen sizes.
- **Hover or Click Activation**: Choose whether the hero occurs on hover 
  or click.

## Configuration
After installing and enabling the module, you can configure it through the 
Views UI:

1. **Hero Style**: Choose the hero animation style (e.g., Fade, Zoom, 
   Top, Right, Bottom, Left).
2. **Hero Speed**: Set the speed for the hero animation (e.g., 0.1 for 
   fast, 2 for slow).
3. **Background Colors**: Customize the background colors for the front and 
   heroed sides of the box.
4. **Available Breakpoints**: Select the breakpoints to ensure the hero is 
   responsive (e.g., 576px, 768px, 992px, etc.).

## Token Support in Views Text Areas

In Views headers, footers, or empty text areas—when using *Global: Text area* or
*Global: Unfiltered text*—there is an option called **"Use replacement tokens
from the first row."**

The default Twig-style tokens (e.g., `{{ title }}` or `{{ field_image }}`)
**will not work** with the VVJH style. Instead, use the custom tokens provided
by VVJH:

**Examples:**

- `{{ title }}` → `[vvjh:title]`  
- `{{ field_image }}` → `[vvjh:field_image]`

To strip any HTML from the output, you can append `:plain` to the token:

- `[vvjh:title:plain]`

These tokens pull data from the **first row** of the View result and are
designed to work seamlessly with the VVJH rendering system.

## Example Usage
1. Create a new view or edit an existing view.
2. Under the "Format" section, select "Views Vanilla JavaScript Hero" as 
   the style.
3. Configure the hero style, speed, background colors, and other settings 
   as needed.
4. Save the view and see the hero in action on the front end.

## Additional Information
- **Dependencies**: This module does not have any dependencies on other 
  contributed modules.
- **Compatibility**: Fully compatible with Drupal 10 and follows Drupal best 
  practices.

## Installation
1. Download and install the module from 
   [Drupal.org](https://www.drupal.org/project/vvjh).
2. Enable the module through the Drupal admin interface or using Drush: 
   `drush en vvjh`
3. Clear caches to ensure the new plugin is available: `drush cr`

## Troubleshooting
If you encounter any issues or have suggestions for improvements, please open 
an issue in the module's issue queue on Drupal.org.

## Maintainers
- [Alaa Haddad](https://www.drupal.org/u/flashwebcenter)

## Support
For issues, feature requests, and general support, please use the 
[issue queue](https://www.drupal.org/project/issues/vvjh).

## License
This project is licensed under the 
[GNU General Public License, version 2 or later](http://www.gnu.org/licenses/gpl-2.0.html).
