### 2.6.1 - 2025-01-20
* Fixed throwing deprecation errors in PHP 8.2+.

### 2.6.0 - 2025-01-08

* The minimum required WordPress version is now 5.5.
* The minimum required PHP version is now 5.6.
* Security fixes

### 2.5.2 - 2019-07-26

* Improved security of notfication emails.

### 2.5.1 - 2019-01-17

* Remove third party library not needed, from composer (mailin-api).

### 2.5.0 - 2018-09-18

* <a href="https://wpforms.com/wpforms-has-acquired-pirate-forms/" rel="friend">Pirate Forms was acquired by WPForms</a>.
* We're retiring Pirate Forms in favor of the modern form builder by WPForms, so users can have access to best user experience and more powerful WordPress form features.
* Added migration wizard to move forms to WPForms.

### v2.4.4 - 2018-07-13

* Fixed compatibility with All in One SEO Pack plugin
* Fixed problem with form not working with the ajax option
* Option to save the attachments files
* Improved recaptcha button layout on mobile devices

### v2.4.3 - 2018-06-25

* New Gutenberg block for the default Pirate Forms form
* Made the checkbox field to store in the database for GDPR compliance
* New option to disable tracking of the IP for GDPR compliance
* Replaced subscription form with Sendinblue form

### v2.4.2 - 2018-06-07

* NEW support for submitting Ajax forms with [pirate_forms ajax="yes"]
* Added compatibility with WordPress 4.9.6 Export and Erase Personal Data options
* Fixed issue with form caused by the reCaptcha
* Fixed compatibility issues with the wpDataTables Lite plugin

### v2.4.1 - 2018-05-07

* GDPR compliance

### v2.4.0 - 2018-04-02

* Improves compatibility with various themes.
* Adds support for two new custom fields.
* Adds filter for custom classes into form fields.
* Adds visual/text switch to form wyiwyg editor.

### v2.3.5 - 2018-03-05

* Fix characters encoding issue in the subject field.
* Fix issue with spam label with two forms on the page.
* Allows zip files to be attached in forms.
* Adds a filter to dynamically change the subject.
* Adds options to send a copy of the email to the sender.

### v2.3.4 - 2018-02-15

* Added missing Loader.gif file
* Fixed undefined notice
* Fix submit button leaving form when ReCaptcha is enabled

### v2.3.3 - 2018-01-06

* Fix double reCAPTCHA box bug.
* Fix custom spam trap alignement error.

### v2.3.2 - 2017-12-28

* Fix for tooltip admin behavior.

### v2.3.1 - 2017-12-28

* Improves layout and compatibility with various themes.
* Improves form default email format.
* Fix issues with various special characters in the magic tag fields.

### v2.3.0 - 2017-11-27

* Adds email content wysiwyg editor.
* Improves layout for custom spam trap.

### v2.2.5 - 2017-11-16

* Adds compatibility with WordPress 4.9
* Minor improvement for toggle the password in the admin form fields.

### v2.2.4 - 2017-11-13

* Improved assets loading, loading them only they are necessary.
* Remove hide/show effect for reCaptcha.
* Add toggle for password field.
* Add new docs, keeping them in sync with HelpScout .
* Adds more integration with the pro version.

### v2.2.3 - 2017-10-24

* Improves compatibility with Hestia theme.
* Adds option to set form label classes.

### v2.2.2 - 2017-10-20

* Hide email entries in frontend queries.
* Adds filter for form attributes.
* Fix issue with attachment fields not working when spam trap is active.
* Adds support for more integrations in the pro version.

### v2.2.1 - 2017-10-10

* Fix issue for multiple forms on the same page.
* Fix issue with reCAPTCHA keys.
* Capture failure reasons and corrected email status.

### v2.2.0 - 2017-09-27

* Adds integration with Akismet for spam block.
* Adds another spam tramp mechanism, independent from Google reCAPTCHA.
* Adds filter for customizing the email body.
* Improvements for compatibility with the pro version.

### v2.1.0 - 2017-08-26

* Improved compatibility with the pro version.
* Fixed broken form layout on certain themes.
* Improved security.
* Added test email functionality.

### v2.0.5 - 2017-08-16

* Fixed compatibility with the pro version for multiple fields.
* Fixed default consistency between forms.

### v2.0.4 - 2017-08-14

* All fields are now optional.
* Fixed redirect after form submission.
* Added more flexibility for changing the layout via dynamic CSS classes.

### v2.0.3 - 2017-08-10

* Fixed fatal errors on some environments because of anonymous functions usage.
* Fixed thank you message when nonces are disabled.
* Added compatibility with pro version.

### v2.0.2 - 2017-08-07

* Fixed none option for thank you page.
* Fixed various issues with form layout.
* Added support for future pro version.

### v2.0.1 - 2017-08-01

* Fixed backwards compatibility with Zerif themes

### v2.0.0 - 2017-08-01

* Major code refactor ( Please TEST BEFORE updating).
* Added multiple filters and hooks to be easily extended by developers.
* Fixed some issues with attachment fields.
* Added support for TLS.
* Added support to change browser required messages.

### v1.2.5 - 2017-05-31

* Added themeisle-sdk.
* Added new deployment stack.

### 1.2.0 - 19/01/2017

* Fixed security error for file field.
* Added dashboard widget.

### 1.1.3 - 11/01/2017

* Update readme.txt

### 1.1.3 - 20/12/2016

* Sync with wp.org

### 1.1.2 - 20/12/2016

* Added upsell for custom emails plugin
* Fixed text domains errors
* Added travis and grunt

### 1.1.1 - 19/12/2016

* Update changelog

### 1.1.0 - 19/12/2016

* Escape form fields

### 1.0.18 - 07/11/2016

* Fixed php strict standards error
* Update tags
* Tested up to WordPress 4.6

### 1.0.17 - 25/07/2016

* Development

### 1.0.16 - 13/07/2016

* Fixed IP issue when using web server behind a reverse proxy
* Fixed W3C compatibility issues
* Remove pcf=1#contact from url when theme is different then Zerif
* Removed blacklist option and made it default set to true
* Display site key and secret key fields only if recaptcha option is selected
* New attachment option
* New thank you URL option
* New option to make the nonce optional

### 1.0.16 - 20/06/2016

* #89 textarea field not saving

### 1.0.15 - 10/06/2016

* Update screenshots
* Added a clearfix after the Pirate Forms widget to avoid messed layout
* Update compatible WordPress version number

### 1.0.14 - 23/05/2016

* Reorganize backend content
* Fixed issue with checkbox not saving

### 1.0.13 - 12/05/2016

* Update readme.txt

### 1.0.13 - 05/05/2016

* Update readme.txt

### 1.0.13 - 01/04/2016

* fix issue with multiple forms on same page

### 1.0.12 - 21/03/2016

* Option to change recaptcha language

### 1.0.11 - 14/03/2016

* Fixed #55 Recaptcha too down

### 1.0.9 - 10/03/2016

* Fixed layout issues
* Update readme.txt

### 1.0.8 - 09/03/2016

* Update readme.txt
* Update translations files
* translation issues fixed #42
