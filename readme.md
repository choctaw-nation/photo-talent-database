# Quick Start

This template is meant to get CNO devs a quick start for how we build sites, with a focus on OOP, WordPress-like APIs, and structured extendability.

[**Check the wiki to learn more!**](https://github.com/choctaw-nation/cno-template-theme/wiki)

1. Download the zip
2. Drag all the files (\_including the hidden files like `.gitignore`) into either your `public` folder or `wp-content/themes`
3. Rename the theme folder!
4. Update paths that reference the theme folder: `/webpack.config.js`, `/composer.json`, `.github/workflows/deploy.yml`
5. Configure Bootstrap JS & SCSS
6. Configure `inc/theme/class-theme-init.php` and `functions.php` instantiation of Theme_Init class
7. run `npm i && composer update && npm run start`
8. Update the `deploy.yml` file to link to the appropriate WP Engine environments.
9. Init CNO Github Repo with 3 branches: `main`, `stg` and `dev`
10. Get to code-slinging!

# Changelog

## v4.0.0

Version 4 now supports the Block Editor, based on what's being set up in the `bootstrap.scss` file.

## v3.7.0

- Added: `ACF_Handler` class to manage ACF fields programmatically!

## v3.6.0

-   Added: `Post_Override` class
-   Updated: `phpcs.xml.dist` file now includes proper excludes to not breaking file formatting for latest package versions
-   Chore: Updated packages

## v3.5.0

-   Added: `Theme_Init` class now declares support for allowed hosts for `wp_safe_redirects()` call
-   Added: Gravity Forms Handler is now added to use PHP to add Bootstrap classes to submit buttons (extensible, obviously, to include other changes)
-   Updated: `Bootstrap_Pagination` now properly handles the `mid size` parameter
-   Chore: Update Packages

## v3.4.0

-   Added default page template "Privacy Policy" that allows privacy policy to be federated from Choctaw Nation main site or allow custom content
    -   ACF JSON file is also included for simple initialization and customization
-   Added `cno_read_svg` and `cno_echo_svg` global functions for a simple way to use SVGs from within the theme directory
-   Added Bootstrap_Pagination and Social_Link_Generator classes when sites need to use custom pagination (e.g. with Search and Filter) or social links (e.g. "Share to Facebook")
    -   `Social_Link_Generator` class needs the [Facebook Share Plugin](https://github.com/choctaw-nation/cno-plugin-facebook-share) to work properly.
-   Updated packages.
-   Updated `bs_breakpoints.scss` and `bootstrap.scss` to use sass modules with `@use "sass:map"` and `@use "sass:list"` respectively

## v3.3.0

-   Introduces theme.json to the theme. This allows plugins like Search and Filter to use theme styles by rendering the Bootstrap theme color CSS variables to the WP admin screens.

## v3.2.1

-   SCSS function `transition` now accepts an arbitrary number of arguments instead of requiring a list, and it now also uses the `--transition-standard` CSS var instead of the hard-coded/interpolated scss variable

## v3.2.0

-   Added 'interaction' mixin to handle a writing media queries for user interaction (hover, active and focus states)
-   Updated `package.json` and `webpack.config.js` to latest package versions (removes `overrides` args) and to strip empty `.js` files from build process ([according to this topic in the WordPress Theme Handbook](https://developer.wordpress.org/themes/advanced-topics/build-process/#configuring-webpack))
-   Updated the `wp-config-sample.php` file with WP 6.7.1 version
-   Updated `_bs_breakpoints.scss` to use the Sass Map package ([Sass 3.0.0 deprecations](https://sass-lang.com/documentation/breaking-changes/import/))
-   Updated heading's `text-wrap:balance` declaration to use `:where()` instead of SCSS list + loop to set the specificity to 0
-   Updated theme header (tested up to, requires, etc)
-   Removed dead/overly-opinionated code

## v3.1.1

-   Update path to PHP utility files

## v3.1.0

-   Add new roles to Editor capability to allow Content Team to do their job.
-   ACF Hero fields are now opt-in by default (instead of opt-out).
-   Update `@wordpress/scripts` package

## v3.0.4

-   PHPCS check now fails when appropriate

## v3.0.3

-   Update PHPCS workflow to only run when php files are edited
-   Added hints for playwright testing in GH Action

## v3.0.2

-   Only run PHPCS if php files changed, otherwise use the previous status check

## v3.0.1

-   Fixed PHPCS errors for passing checks

## v3.0.0

-   **BREAKING FOR REACT PACKAGES** Updated minimum required WordPress version to 6.6.0
-   Updated minimum PHP version to 8.2
-   New Github Actions that make use of the [Shared Github Actions](https://github.com/choctaw-nation/shared-github-actions) repo
-   Update packages and patch npm security issues
-   Further reset Bootstrap to use `$font-size-base:1rem` (Bootstrap's default behavior) to get rid of KJ's dumb opinions and make the rest of Bootstrap's components work as expected.

## v2.5.0

-   The Navwalker class automatically switches the nav item from an anchor with a split dropdown to a dropdown-toggle button based on the href value

## v2.4.4

-   Added docs and deploys
-   Added Bootstrap Button Loop override to handle custom hover/active effects
-   Reduced custom variables and cleaned up the `src` files
-   Added starter `_utilities.scss` file

## v2.4.3

-   Update packages
-   Update `phpcs` Github Action to use composer script
-   Removed `.lock` files for composer & npm
-   Migrated `AOS` from index.js for cleaner dependencies
-   Removed unnecessary storing of classes in variables (e.g. `$svg = new Allow_SVG()`)
-   Cleaned up `theme-functions.php`

## v2.4.2

-   Removes the `fontawesome` bundle step in favor of using the plugin
-   Adds `phpcs` checks as a github action to roll with every new repo
-   Updates dependencies for `composer` and `npm`

## v2.4.1

-   Added comments and commented out code (where able) in `Theme_Init` to be more clear about what file requirements are and why they included.
-   Removed Google Tag Manager manual enqueue (in favor of Site Kit Plugin integration)

## v2.4.0

-   Added a flag to `Theme_Init` constructor to load Nation/Commerce favicon package
-   **Removed** FontAwesome from enqueue scripts in favor of FontAwesome WordPress plugin (although the vendor files still exist if one _wanted_ to load that way)
-   **Updated Bootstrap Build:** Headings are now set to use [Minor Third Type Scale](https://typescale.com/) with Bootstrap's responsive-font-sizing.

## v2.3.1

-   Update Swiper defaults and other bug fixes

## v2.3.0

-   Loaded the Great Seal as an svg in the header by default.
-   Extended/Overwrote Bootstrap's `$theme-colors` map to allow for quicker customization and "native", "bootstrap-y" color classes
-   Updated packages

## v2.2.0

-   Create new Class (Allow_SVG) that....allows WordPress to use SVG
-   Include standard filter closure that sets Yoast to bottom of Custom Fields
-   Update style.css to include the theme's version as its version

## v2.1.0

-   Load Fontawesome CSS (with option to use JS if desired)
-   Better Header/Footer!
    -   Fixes Accessibility UX / UI
-   Better Asset_Loader class handling style/script dependencies
-   More Bootstrap partials loaded where needed

## v2.0.0

-   Update classes to simplify names and use `ChoctawNation` namespace (and sub-namespace).
-   Update bootstrap & scss to use bootstrap variable overrides to reduce the amount of custom css needed to build a site (hopefully)
-   Reduce the dependencies in `composer.json`
-   Update versions in `package.json`
-   Add extra logic to `Theme_Init` to handle Google Tag Manager in head via hooks (instead of defining directly in `header.php`)

## v1.9.0

-   Moved "Before you begin" to the Wiki
-   Updated the Hero Section to use a template part & ACF class instead of the previous component classes for a more traditional WordPress markup experience.
-   Updated Bootstrap Custom file to handle what the base, reset and typography files used to handle.

## v1.8.0

### Vendor Updates

-   Updated Fontawesome to use `i2svg` method instead of `watch` (to simply hot-swap `<i>` for `<svg` instead of initializing a Mutation Observer)
-   Updated Bootstrap imports to use the individual UMD files for optimal tree-shaking
    -   Updated Bootstrap JS to be registered, rather than enqueued

### Codebase Updates

-   Updated `enqueue scripts` method to add the main stylesheet (`style.css`) as a quick fix.
-   Updated Webpack to output page files into `dist/pages/*`
-   **Breaking Change** **Requires PHP ^8.1** Refactored `cno_enqueue` functions into a class (`Asset_Loader`) with an Enum (`Enqueue_Type`)
-   Added `CNO Mega Menu` and `CNO Navwalker` classes
-   Added `acf-classes` folder with `ACF Generator` and `ACF Image` classes
-   Refactored SCSS variables to use CSS custom properties where able and appropriate.

### Misc. Updates

-   Updated phpcs.xml.dist to require `@package` tags
-   Update Packages

## v1.7.2

-   Updated ruleset for WordPress sniff to use minimum WP version 6

## v1.7.1

-   Hollow out starter pages
-   Add better error handling when theme is initialized
-   Refactored Theme PHP to be better organized in folders

## v1.7.0

-   Ready for WordPress 6.3!
-   Fixed a bug that would cause menus to fail when no menu was present
-   Init FontAwesome with DOM Mutation API
-   Update Packages (package.json & composer.json)
-   Update `Content_Sections` class API (specifically, the `two_col_text_and_media` API)

## v1.6.1

-   Better default theme supports
-   Added react-app eslint config
-   Escaped inputs
-   Update packages

## v1.6.0

-   Added "ACF-Pro" to version-control and placed in MU-Plugins folder.
-   Updated packages.
-   Updated repo visibility to **PRIVATE** which messes up 'release' versions, but since ACF-Pro is under version control, it should not be open to the public.

## v1.5.0

-   Created a "vendors.css" file that gets loaded before "main" css to conform with new [http/2 best practices](https://blog.cloudflare.com/http-2-for-web-developers/). Essentially, this keeps Bootstrap's core css (~20kB) seperate from other sitewide global declarations.
-   Updated packages
-   Updated Webpack for tree-shaking
-   Updated package.json for [WordPress Browserslist](https://make.wordpress.org/core/handbook/best-practices/browser-support/) config by default.

## v1.4.2

-   Fix the new enqueue scripts/styles
-   Tie assets (scripts/styles) to associated `asset.php` files

## v1.4.1

-   Updated the enqueue scripts/styles functions in `inc/theme-functions.php` to use the `cno` prefix and to automatically include the {$id}.asset.php generated by webpack.
-   Updated "before you begin" section above

## v1.4

-   Added fadeIn function and style for simple fade-in-on-scroll effect
-   Added Nav Walker for submenus

## v1.3

-   Added Hamburger styles to the nav by default
-   Added Swiper JS as common slider
-   Added extra styles and sample js(x) items

## v1.2

A better Webpack!

New entry function to programmatically generate files. All you have to do is add a snake-case string to the proper array!

```js
const THEME_NAME = 'starter-theme';
const THEME_DIR = `/wp-content/themes/${THEME_NAME}/src`;

function snakeToCamel(str) {
return str.replace(/([-\_][a-z])/g, (group) =>
group.toUpperCase().replace('-', '').replace('\_', ''),
);
}

/**
* For JSX folders (located `~/src/js/folder-name/App.jsx)`)
* Array of strings modeled after folder names (e.g. 'about-choctaw')
*/
const appNames = [];

/**
 * For SCSS files (no leading `_`)
 * Array of strings modeled after scss names (e.g. 'we-are-choctaw')
 */
const styleSheets = []; // for scss only

module.exports = {
	...defaultConfig,
	...{
		entry: function () {
			const entries = {
				global: `.${THEME_DIR}/index.js`,
			};
    		if (appNames.length > 0) {
    			appNames.forEach((appName) => {
    				const appNameOutput = snakeToCamel(appName);
    				entries[
    					appNameOutput
    				] = `.${THEME_DIR}/js/${appName}/App.jsx`;
    			});
    		}
    		if (styleSheets.length > 0) {
    			styleSheets.forEach((styleSheet) => {
    				const styleSheetOutput = snakeToCamel(styleSheet);
    				entries[
    					styleSheetOutput
    				] = `.${THEME_DIR}/styles/pages/${styleSheet}.scss`;
    			});
    		}
    		return entries;
    	},
    },
```

## v1.1.1

Minor bug fixes & additional templating

## v1.1

### Updated SCSS Styles:

-   `/vendor/_bs-custom.scss` now contains better list of bootstrap elements
-   `/abstracts/_variables.scss` now starts with CNO style declarations
-   `/base/_reset.scss` includes `section` `padding:3rem 0` (based on Bootstrap `py-5`)
-   `/base/_typography.scss` is created with basic styles

### Updated `webpack.config`

-   Making use of `const`ants to allow quicker config to custom path/theme name.

### New Package: Bundle Analyzer!

-   Updated `package.json` to include new package
-   Updated `webpack.config` to generate a `report.html` file when `npm run build` script is fired that outputs to `/bundle-analyzer` to analyze JS modules

### New Theme Functions!

-   `remove_wordpress_styles()` method accepts an array of `$handles` to remove (i.e. `classic-theme-styles`,`wp-block-library`,`dashicons`, etc.)
-   `enqueue_page_assets()` (and dependent methods) added to enqueue js/scss assets _per page_ (so we can only call the assets we need when we need them) with a simple script
