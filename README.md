# Fictioneer Minimalist

<p>
  <a href="https://github.com/Tetrakern/fictioneer"><img alt="Theme: 1.0" src="https://img.shields.io/badge/theme-1.0-blue?style=flat" /></a>
  <a href="LICENSE.md"><img alt="License: GPL v3" src="https://img.shields.io/badge/license-GPL%20v3-blue?style=flat" /></a>
  <a href="https://github.com/Tetrakern/fictioneer"><img alt="Parent Theme: 5.22.2+" src="https://img.shields.io/badge/parent-%3E%3D5.22.1-blue?style=flat" /></a>
  <a href="https://wordpress.org/download/"><img alt="WordPress 6.1+" src="https://img.shields.io/badge/WordPress-%3E%3D6.1-blue?style=flat" /></a>
  <a href="https://www.php.net/"><img alt="PHP: 7.4+" src="https://img.shields.io/badge/php-%3E%3D7.4-blue?logoColor=white&style=flat" /></a>
  <a href="https://github.com/sponsors/Tetrakern"><img alt="GitHub Sponsors" src="https://img.shields.io/github/sponsors/tetrakern" /></a>
  <a href="https://ko-fi.com/tetrakern"><img alt="Support me on Ko-fi" src="https://img.shields.io/badge/-Ko--fi-FF5E5B?logo=kofi&logoColor=white&style=flat&labelColor=434B57" /></a>
</p>

This is a minimalist WordPress child theme for [Fictioneer](https://github.com/Tetrakern/fictioneer). To use this theme, ensure that the parent theme is also installed and do not rename any folders. Always download the zip file from the [Releases](https://github.com/Tetrakern/fictioneer-minimalist/releases) page. Requires Fictioneer 5.27.0 or higher.

Fictioneer Minimalist is open source and completely free. If you enjoy the theme, please consider supporting me on [Patreon](https://www.patreon.com/tetrakern), [Ko-fi](https://ko-fi.com/tetrakern), or [GitHub Sponsors](https://github.com/sponsors/Tetrakern).

[Demo Page](https://minimalist.fictioneer-theme.com/)

## Recommended Settings

As the child theme builds upon the main theme, some settings and options work better than others or have been removed. The following recommendations should be applied under **Appearance > Customize**; feel free to make changes as you see fit.

**SITE IDENTITY:**
* **Site Title - Minimum Size:** 32
* **Site Title - Maximum Size:** 32
* **Tagline Title - Minimum Size:** 14
* **Tagline Title - Maximum Size:** 14

**HEADER IMAGE (IF ANY):**
* **Show header image shadow:** OFF
* **Minimum Height:** 150
* **Maximum Height:** 300

**LAYOUT:**
* **Site Width:** 896 without sidebar, 1036 with sidebar
* **Mobile Menu Style:** Slide in from left
* **Story Page Cover Position:** Floating Top-Left
* **Story Page Cover Shadow:** No Shadow
* **Content List Style:** Lines

**CARDS:**
* **Card Image Style:** Seamless
* **Card Footer Style:** Combined
* **Card Shadow:** Shadow Small

## Customization

You cannot make a child theme of a child theme, which can pose a challenge for further customization. The cleanest way is to add your own must-use plugin to the `/wp-content/mu-plugins/` directory (create one if it does not exist). You can copy the provided [customize-mu-plugin.php](customize-mu-plugin.php) as example — it’s really just a single PHP file.

The plugin file includes an example filter to change the post meta row, removing icons and separating the items with a pipe. You can use this by uncommenting the `add_filter()` line in `custom_initialize()` simply take it as a guide on how to add actions and filters yourself. Note that mu-plugins are executes **before** normal plugins and the theme; unless you delay functions, as shown in `custom_initialize()`, nothing of the plugins and themes will be available.

## Screenshots

![Screenshot Collage](repo/assets/fictioneer_minimalist.jpg?raw=true)
