=== Master Post Password ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: post password, password, post, passworded, privacy, coffee2code
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 4.7
Tested up to: 5.4
Stable tag: 1.3.2

Define a master post password that works for all passworded posts, while permitting the original post passwords to also work.

== Description ==

Define a master post password that works for all passworded posts, while permitting the original post passwords to also work.

Once the master post password has been provided by a visitor for any passworded post on the site, it applies to unlock all other passworded posts (without needing to provide the master post password again for each such post) until the site's cookies expire in the browser.

There are two means by which the master post password can be defined:

a.) As a constant, `C2C_MASTER_POST_PASSWORD`. This is typically done in wp-config.php like so:

  `define( 'C2C_MASTER_POST_PASSWORD', 'your_master_post_password' );`

b.) Via the settings field labeled "Master Post Password" found on the `Settings` -> `Reading` admin page.

If the constant is defined, it takes precedence and the settings field is *NOT* displayed.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/master-post-password/) | [Plugin Directory Page](https://wordpress.org/plugins/master-post-password/) | [GitHub](https://github.com/coffee2code/master-post-password/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Install via the built-in WordPress plugin installer. Or unzip `master-post-password.zip` inside the plugins directory for your site (typically `/wp-content/plugins/`).
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

= Why can't I see the setting on the "Reading Settings" admin page? =

Are you logged in as an administrative user who can access the "Settings" -> "Reading" admin page? Is the plugin installed and activated?

Assuming those are true, have you set a master post password via the C2C_MASTER_POST_PASSWORD constant? If so, the admin setting will not be displayed.

= Does this plugin include unit tests? =

Yes.

= Is this plugin localizable? =

Yes.

= Is this plugin GDPR-compliant? =

Yes. This plugin does not collect, store, or disseminate any information from any users or site visitors.


== Changelog ==

= 1.3.2 (2019-11-28) =
* Change: Note compatibility through WP 5.3+
* Change: Use full URL for readme.txt link to full changelog
* Change: Update copyright date (2020)

= 1.3.1 (2019-06-07) =
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Add link to plugin's page in Plugin Directory to README.md
* Change: Note compatibility through WP 5.2+

= 1.3 (2019-02-04) =
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Merge `do_init()` into constructor
* Change: Add an FAQ item
* Change: Tweak some inline documentation
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

_Full changelog is available in [CHANGELOG.md](https://github.com/coffee2code/master-post-password/blob/master/CHANGELOG.md)._


== Upgrade Notice ==

= 1.3.2 =
Trivial update: noted compatibility through WP 5.3+ and updated copyright date (2020)

= 1.3.1 =
Trivial update: modernized unit tests, created CHANGELOG.md to store historical changelog outside of readme.txt, noted compatibility through WP 5.2+

= 1.3 =
Minor update: tweaked plugin initialization process, aded more inline documentation, noted compatibility through WP 5.1+, updated copyright date (2019)

= 1.2.1 =
Minor update: fixed unit tests, added README.md, noted GDPR compliance, noted compatibility through WP 4.9+. and updated copyright date (2018)

= 1.2 =
Recommended update: some back-end reimplementation to take advantage of WP 4.7 changes, compatibility is now WP 4.7+ (this version won't work for earlier versions of WP), updated copyright date (2017), and other minor improvements.

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
