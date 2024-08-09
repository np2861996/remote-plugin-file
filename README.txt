=== Remote Plugin for WordPress ===
Contributors: Nikhil Patel
Tags: Remote Plugin
Requires at least: 5.0
Tested up to: 6.1
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allow activate and deactivate plugins using API.

The Remote Plugins plugin allows you to manage WordPress plugins remotely using a REST API. You can list available plugins, activate a plugin, and deactivate a plugin through specified endpoints.

Requirements

WordPress site

Knowledge of installing and activating WordPress plugins

Postman or cURL for testing

Endpoints

1. List Available Plugins

Endpoint: /wp-json/kd/v1/listplugins
Method: GET
Description: This endpoint returns a list of all available plugins in the WordPress installation.

2. Activate a Plugin

Endpoint: /wp-json/kd/v1/activate?plugin=<name>
Method: POST
Description: This endpoint activates the specified plugin.
Parameters:
plugin: The slug name of the plugin to be activated.
Restrictions: The plugin name must be validated before attempting activation.

3. Deactivate a Plugin

Endpoint: /wp-json/kd/v1/deactivate?plugin=<name>
Method: POST
Description: This endpoint deactivates the specified plugin.
Parameters:
plugin: The slug name of the plugin to be deactivated.
Restrictions: The plugin name must be validated before attempting deactivation.

Security
To access these endpoints, you must use the bearer token for verification. Only requests with this token will be authenticated. The token is:

Copy code
WxScDzJOYXEAcCmDSSDuieM48WpTeZGC

Error Handling
Invalid plugin names will return a clear error message.
If the plugin is already activated or deactivated, an appropriate message will be returned.

Testing
Use tools like Postman or cURL to test your endpoints. No code for testing should be added to the plugin itself.

Example Endpoints
List Plugins: https://your-site.com/wp-json/kd/v1/listplugins
Activate Plugin: https://your-site.com/wp-json/kd/v1/activate?plugin=plugin-slug
Deactivate Plugin: https://your-site.com/wp-json/kd/v1/deactivate?plugin=plugin-slug

== Installation ==
1. Upload the entire `woo-double` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).

== Frequently Asked Questions ==

= How to use plugin? =

You just have to install. Plugin will take care of all the required things.


== Screenshots ==

1. You just have to install.

== Changelog ==

= 1.0.0 =
* Initial version of the plugin.

== Upgrade Notice ==

= 1.0 =
Just released into the wild.