=== Redirection for Contact Form 7 ===
Tags: CF7 redirect, CF7 thank you page, redirect cf7, redirect CF7, CF7 success page, cf7 redirect, registration form, mailchimp, login form, conditional redirect, cms integration, conversions, save leads, paypal
Contributors: codeinwp, themeisle, yuvalsabar, regevlio
Requires at least: 5.2.0
Tested up to: 6.5
Stable tag: 3.1.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The ultimate add-on for CF7 - redirect to any page you choose after mail sent successfully, firing scripts after submission, save submissions in database, and much more options to make CF7 poweful then ever.

== Description ==

The ultimate add-on for CF7 - redirect to any page you choose after mail sent successfully, firing scripts after submission, save submissions in database, and much more options to make CF7 poweful then ever.
NOTE: This plugin requires CF7 version 4.8 or later.

== Usage ==

Simply go to your form settings, choose the "Redirect Settings" tab and set the page you want to be redirected to.

== Features ==

* Redirect to any URL
* Open page in a new tab
* Run JavaScript after form submission (great for conversion management)
* Pass fields from the form as URL query parameters
* Add Honeypot to minimize spam
* Save form submissions to your database
* GDPR create erase personal data request
* GDPR create export personal data request

== Our Extensions ==

*** Free Trial ***

* **[Extension]** [Conditional logic for each action]
* **[Extension]** [Integrate with monday]
* **[Extension]** [Multi step form]
* **[Extension]** [Send SMS messages with twilio]
* **[Extension]** [Integrate with Salesforce]
* **[Extension]** [Integrate with Hubspot CRM]
* **[Extension]** [Frontend Publishing - Allow your visitors to submit post types]
* **[Extension]** [Integrate with Mailchimp - Automatically add form submissions to your predefined list]
* **[Extension]** [Frontend Registration - Use CF7 as a registration form]
* **[Extension]** [Frontend Login - Use CF7 to login users to your website]
* **[Extension]** [Conditional form validations (custom error messages)]
* **[Extension]** [Manage email notifications by conditional logic]
* **[Extension]** [Fire custom JavaScript events by conditional logic]
* **[Extension]** [Send data to remote servers (3rd-party integration)]
* **[Extension]** [Send submissions to API Json/XML to remote servers]
* **[Extension]** [Send submissions to API POST/GET to remote servers]
* **[Extension]** [Integrate with paypal]
* **[Extension]** [Integrate with stripe]
* **[Extension]** [Create and send PDF]
* **[Extension]** [Send Slack Message]
* **[Extension]** [Eliminate Duplicates]
* **[Extension]** [Thank You Popup Message]

> Note: some features are availible only as an extension. Which means you need an extension to unlock those features.

More info and documentation on [https://docs.themeisle.com/collection/2014-redirection-for-contact-form-7](https://docs.themeisle.com/collection/2014-redirection-for-contact-form-7)

== Installation ==

Installing Redirection for CF7 can be done either by searching for "Redirection for CF7" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the "Plugins > Add New > Upload" screen in your WordPress dashboard.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Visit the settings screen and configure, as desired.

== Frequently Asked Questions ==

= Does the plugin disables CF7 Ajax? =

No, it doesn't. The plugin does not disables any of CF7 normal behavior, unlike all other plugins that do the same.

= Does this plugin uses "on_sent_ok" additional setting? =

No. One of the reasons we developed this plugin, is because on_send_ok is now deprecated, and is going to be abolished by the end of 2017. This plugin is the only redirect plugin for CF7 that has been updated to use [DOM events](https://contactform7.com/dom-events/) to perform redirect, as CF7 developer Takayuki Miyoshi recommends.

= How to use files shortcodes =
[{field_name}-filename] - will replace the shortcode with the file name
[{field_name}-base_64_file] - will replace the shortcode with a base64 representation of a file
[{field_name}-path] - will replace the shortcode with the file path on the server

== Screenshots ==

1. Actions tab
2. Redirect Action
3. Fire JavaScript Action
4. Save Lead Actions
5. Extensions tab

== Changelog ==

#####   Version 3.1.7 (2024-06-20)

- Fix compatibility with Freemius




#####   Version 3.1.6 (2024-06-20)

- Update internal dependencies
- Improve compatibility for Add-ons




##### Version 3.1.5 (2024-06-04)

- Internal updates

##### Version 3.1.4 (2024-06-04)

- Ownership change to Themeisle

= 3.1.3 =
* Fix honeypot breaks validation messages

= 3.1.2 =
* Conditional logic support fixed

= 3.1.1 =
* Fixes to comply with wordpress repository guidelines (changed functions prefix)
* Additional Fixes for PHP Ver 8.2 deprecation notices.
* Fix conditional logic error

= 3.1.0 =
* Upgrade freemius version
* Fixed action duplication with form duplication

= 3.0.1 =
* Fix access to leads manager

= 3.0.0 =
* Fix PHP Ver 8.2 deprecation notices.
* Removed unused functions and files
* Tested for wordpress 6.3

= 2.9.2 =
* Fix javascript error on admin panel prevents validating salesforce extension connection.

= 2.9.0 =
* Added new actions info (monday integration/eliminate duplicates)
* Added eliminate duplicates addon
* Added "cc" "bcc" "additional headers" to send mail action
* Disabled default CF7 mail when send mail action is Activate
* Freemius SDK update to 2.5.10
* Removed unused JavaScript
* Fix debug output

= 2.8.0 =
* Fixed repeater wrong numbering
* Fixed conditional logic support when a tag was removed
* Patched security issue involving "registration add-on"
* Updated Freemius SDK to 2.5.3

= 2.7.0 =
* Fixed incorrect checkbox/selectbox values when exporting leads to csv
* Removed old plugin updates check
* Fixed several minor php notice error messages on PHP8

= 2.6.0 =
* Added new free action - erase data request
* Fixed PHP8 notice messages
* Completely removed accessiBe addon
* Added compatability for slack message extension

= 2.5.0 =
* Fixed PHP8 notice messages
* Update freemius SDK (security patch)

= 2.4.0 =
* Fixed PHP8 notice messages
* Added Support for base64 files on api calls
* Updated front end script name because of avast false positive notice
* Fix close popup
* Bumped plugin version to 2.4.0

= 2.3.7 =
* Added Tel field template file
* Added support for twilio sms extension
* Added freemius support for plugin extensions

= 2.3.6 =
* Fix missing definition for stripe integration extension
* Fix notice message on WP-CLI (HTTP_HOST);
* Security updates
* Ui fixes on extensions list

= 2.3.5 =
* Fix preview of checkbox fields on leads list
* Fix nonce issues on extensions page
* Fix send file to api [{fieldname}-filename] [{fieldname}-base_64_file] [{fieldname}-path]

= 2.3.4 =
* Security updates
* Fixed jQuery error when adding an action with wysiwyg Editor
* Fixed duplicate post functionality
* Added support for pdf create action

= 2.3.3 =
* Fixed undefined $_SERVER['HTTP_HOST'] on CLI calls

= 2.3.2 =
* Added columns on actions list (debug mode)
* Added compatibility for CF7 Redirection Pro migrations
* Fixed extensions download process.
* Moved Mailchimp dependencies to Mailchimp action

= 2.3.1 =
* Added index.php to directories to disable directory browsing.
* Fixed typo in popup action class name for receiving updates.
* Fixed extensions update process.

= 2.2.9 =
* Added Export leads to csv option.
* Added Duplicate action button.
* Added Preview data on leads table (Defined by marking which fields to display on the action settings).
* Added urlencode passed parameters option on redirect action.
* Fixed duplicate actions on contact form duplication.


= 2.2.8 =
* Added html support to Send Email action.
* Added file attachments support to Send Email action.
* Added reset settings button to debug tools.
* Fixed a bug: radio buttons and checkboxes are now passed correctly as url parameters.
* Fixed a bug: "Changes you made may not be saved" pop-up no longer appears when no changes have been made.

= 2.2.7 =
* Fixed extensions update check interval.

= 2.2.6 =
* Fixed support for non-ajax redirection action.
* Minor styling changes.

= 2.2.5 =
* Fixed compatibility issues with "CF7 - Conditional Fields" Plugin.

= 2.2.4 =
* Fixed a bug with jQuery.noConflict()

= 2.2.3 =
* Fixed compatability issue with "CF7 - Conditional Fields" Plugin.

= 2.2.2 =
* Fixed a bug with jQuery.noConflict()
* jQuery migrate compatibility changes
* Added debug options

= 2.2.1 =
* Fixed a bug in extension class
* Fixed a bug - accessiBe turned off by default

= 2.2.0 =
* New feature: Saving form leads in database.
* New actions system.
* Easy installation of plugin extensions.
* Complete code refactoring.

= 1.3.7 =
* Show pages hierarchy in page select dropdown.

= 1.3.6 =
* Fixed a bug: Redirection for legacy browsers (non-ajax) not working when using external url.

= 1.3.4 =
* Fixed a bug: "Changes you made may not be saved" pop-up no longer appears when no changes have been made.
* Fixed a bug: When passing all fields as parameters, "+" sign is now replaced with "%20".
* Minor code styling changes to fully meet WordPress standards.

= 1.3.3 =
* Fixed a bug: URL query parameters are now properly decoded.

= 1.3.2 =
* New feature: delay redirection in milliseconds.

= 1.3.1 =
* Fixed a bug in legacy browsers: the Pro message keep showing.

= 1.3.0 =
* Minor dev improvements.

= 1.2.9 =
* Fixed a bug: when passing specific fields as URL query parameters, not all the fields were passed.

= 1.2.8 =
* New feature: Pass specific fields from the form as URL query parameters.
* Minor dev improvements.

= 1.2.7 =
* Script field now accepts special characters, such as < and >.

= 1.2.6 =
* Added support for browsers that don't support AJAX.
* Minor CSS changes.

= 1.2.5 =
* Added error message if CF7 version is earlier than 4.8.

= 1.2.4 =
* Fixed a bug regarding sanitizing URL, causing & to change to #038;
* Unnecessary variables removed.

= 1.2.2 =
* New feature: Pass all fields from the form as URL query parameters.
* Minor CSS changes.
* Dev improvements.

= 1.2 =
* New feature: add script after the form has been sent successfully.

= 1.0.2 =
* Added full support for form duplication.
* New feature: open page in a new tab.
* Added plugin class CF7_Redirect.

= 1.0.0 =
* Initial release.
