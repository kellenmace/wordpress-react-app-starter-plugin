# WordPress React App Starter Plugin #
**Contributors:**      Kellen Mace
**Donate link:**       https://kellenmace.com
**Requires at least:** 4.4
**Tested up to:**      4.8.2
**Stable tag:**        1.0.0
**License:**           GPLv2
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html

## Description ##

Starter plugin with these features:

* Registers a new custom post type
* Sets up WP REST API endpoints to get, create, modify and delete posts for that post type from the app
* Adds a custom page template to WordPress that outputs the React app on the page it's applied to
* Comes with Webpack pre-configured to compile the React app into a single JS file and the SASS styles into a single CSS file
* Enqueues the app's JS and CSS files on the page set to use the custom page template
* Uses wp_localize_script() to send initial app data to the front end
* Renders the React app to the page

## Getting Started ##

1. Clone the repo into the `/plugins/` directory of a WordPress site and activate the plugin.
2. Create a new page in WordPress and assign the `React App` page template to it.
3. Visit that page on the front end.

## Development ##

`cd` into this plugin's directory and run `yarn` to install node modules.

Run `yarn dev` for a development build.

Run `yarn watch` to watch files and trigger a development build when any are changed.

Run `yarn prod` for a production build.

## Changelog ##

### 1.0.0 ###
* First release

