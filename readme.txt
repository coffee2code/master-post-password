=== Master Post Password ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: post password, password, post, passworded, privacy, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.6
Tested up to: 4.5
Stable tag: 1.1.1

Define a master post password that works for all passworded posts, while permitting the original post passwords to also work.

== Description ==

Define a master post password that works for all passworded posts, while permitting the original post passwords to also work.

Once the master post password has been provided by a visitor for any passworded post on the site, it applies to unlock all other passworded posts (without needing to provide the master post password again for each such post) until the site's cookies expire in the browser.

There are two means by which the master post password can be defined:

a.) As a constant, `C2C_MASTER_POST_PASSWORD`. This is typically done in wp-config.php like so:

  `define( 'C2C_MASTER_POST_PASSWORD', 'your_master_post_password' );`

b.) Via the settings field labeled "Master Post Password" found on the `Settings` -> `Reading` admin page.

If the constant is defined, it takes precedence and the settings field is *NOT* displayed.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/master-post-password/) | [Plugin Directory Page](https://wordpress.org/plugins/master-post-password/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `master-post-password.zip` inside the plugins directory for your site (typically `/wp-content/plugins/`). Or install via the built-in WordPress plugin installer)
2. Activate the plugin through the 'Plugins' admin menu in WordPress
3. Set a master post password
 a. via the 'Settings' -> 'Reading' admin menu
 b. (optional; advanced) Set the master post password via `C2C_MASTER_POST_PASSWORD` constant. This also serves to prevent the admin option from being displayed.


== Screenshots ==

1. A screenshot of the `Settings` -> `Reading` admin page showing the "Master Post Password" input field.


== Frequently Asked Questions ==

= Does this work for *all* passworded posts, regardless of their explicitly set post password? =

Yes.

= Does the explicitly set post password for a given post still work? =

Yes. A visitor can supply either the post's password or the master post password to access the content.

= Will this require a password for posts that didn't already have a post password configured? =

No. It only affects posts that already have a post password.

= Does this remove or change the password for passworded posts to the master post password? =

No. Any explicitly set post password remains unchanged and functional. The master post password is saved separately from posts.

= If I have multiple password protected posts listed, do I need to provide the master post password for each one to view them all (a bit redundant since I obviously know the master post password and it applies to all of them)? =

No, if you provide the master post password for one post, it'll automatically be applied to all passworded posts until the site's browser cookies expire.

= What happens if I change the master post password? =

Other than the obvious (the master post password has a new value), all existing users of the old master post password will expire when a browser attempts to view a passworded post. The visitor will have to provide the original post password(s) or the new master post password. If a visitor accessed a passworded post using the post's explicitly set post password, then they will not be affected by a master post password change.

= Is the master post password stored securely? =

No. As is the case for post passwords in WordPress, the master post password is stored in the database as plaintext. That is, unless the master post password is set by a constant, in which case it is never stored in the database and only in the given .php file (typically wp-config.php, where other site passwords are defined).

= Why is my custom `$more_link_text` argument value to `the_content()`/`get_the_content()` being ignored for posts unlocked using the master post password? =

Unfortunately, due to limitations within WordPress, for posts that are unlocked using the master post password, the `$more_link_text` argument used for `the_content()`/`get_the_content()` is not taken into consideration.

= Does this plugin include unit tests? =

Yes.

= Is this plugin localizable? =

Yes.


== Changelog ==

= () =
* Change: Enable more error output for unit tests.
* Change: Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable.

= 1.1.1 (2016-05-22) =
* Change: Prevent web invocation of unit test bootstrap.php.
* Change: Tweak plugin description.
* Change: Note compatibility through WP 4.5+.

= 1.1 (2016-03-17) =
* Bugfix (minor): If constanct is used to set master password, ensure `set_master_password()` returns its value instead of attempted password.
* Change: Add support for language packs:
    * Change text domain from 'c2cmpp' to 'master-post-password'.
    * Don't load textdomain from file.
    * Add 'Text Domain' to plugin header.
* New: Add LICENSE file.
* New: Add empty index.php to prevent files from being listed if web server has enabled directory listings.
* Change: Explicitly declare methods in unit tests as public or protected.
* Change: Tweak description.
* Change: Note compatibility through WP 4.4+.
* Change: Update copyright date (2016).

= 1.0.3 (2015-02-19) =
* Escape values in some attributes for added precaution
* Add more unit tests
* Note compatibility through WP 4.1+
* Update copyright date (2015)

= 1.0.2 (2014-08-25) =
* Add version() to return current plugin version
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Note compatibility through WP 4.0+
* Add plugin icon

= 1.0.1 =
* Tweak description
* Note compatibility through WP 3.8+
* Update copyright date (2014)
* Change donate link

= 1.0 =
* Initial public release


== Upgrade Notice ==

= 1.1.1 =
Trivial update: verified compatibility through WP 4.5.

= 1.1 =
Minor update: improved support for localization; verified compatibility through WP 4.4; updated copyright date (2016).

= 1.0.3 =
Trivial update: added more unit tests; noted compatibility through WP 4.1+; updated copyright date

= 1.0.2 =
Trivial update: noted compatibility through WP 4.0+; added plugin icon.

= 1.0.1 =
Trivial update: noted compatibility through WP 3.8+

= 1.0 =
Initial public release.
