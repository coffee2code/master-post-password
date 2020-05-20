# Changelog

## _(in-progress)_
* New: Add TODO.md and move existing TODO list from top of main plugin file into it (and add to it)
* Change: Use HTTPS for link to WP SVN repository in bin script for configuring unit tests
* Change: Note compatibility through WP 5.4+
* Change: Update links to coffee2code.com to be HTTPS
* New: Unit tests: Add tests for registering of hooks

## 1.3.2 _(2019-11-28)_
* Change: Note compatibility through WP 5.3+
* Change: Use full URL for readme.txt link to full changelog
* Change: Update copyright date (2020)

## 1.3.1 _(2019-06-07)_
* New: Add CHANGELOG.md and move all but most recent changelog entries into it
* Change: Update unit test install script and bootstrap to use latest WP unit test repo
* Change: Add link to plugin's page in Plugin Directory to README.md
* Change: Note compatibility through WP 5.2+

## 1.3 _(2019-02-04)_
* Change: Initialize plugin on 'plugins_loaded' action instead of on load
* Change: Merge `do_init()` into constructor
* Change: Add an FAQ item
* Change: Tweak some inline documentation
* Change: Note compatibility through WP 5.1+
* Change: Update copyright date (2019)
* Change: Update License URI to be HTTPS

## 1.2.1 _(2018-04-25)_
* New: Add README.md
* New: Add FAQ indicating that the plugin is GDPR-compliant
* Unit tests:
    * Fix: Explicitly require `class-phpass.php` to get `PasswordHash` class
    * Change: Minor whitespace tweaks to bootstrap
* Change: Add GitHub link to readme
* Change: Note compatibility through WP 4.9+
* Change: Update copyright date (2018)

## 1.2 _(2017-01-15)_
* Change: Implement new post password handling approach to replace previous hacky approach.
    * Utilize new `post_password_required` filter to enable use of WP's `get_the_content()` to get content
    * Add `post_password_required()` to check if master post password has been provided, thus negating the need for the post password form
    * Remove plugin's `get_the_content()` now that WP's version permits the post password check to be suppressed
    * Remove `check_master_password()`
* Change: Discontinue efforts to `require_once()` for class-phpass.php, which WP is now certainly sourcing.
* Change: Enable more error output for unit tests.
* Change: Default `WP_TESTS_DIR` to `/tmp/wordpress-tests-lib` rather than erroring out if not defined via environment variable.
* Change: Note compatibility through WP 4.7+.
* Change: Remove support for WordPress older than 4.7 (definitely won't work for earlier versions)
* Change: Documentation improvements.
* Change: Update copyright date (2017).

## 1.1.1 _(2016-05-22)_
* Change: Prevent web invocation of unit test bootstrap.php.
* Change: Tweak plugin description.
* Change: Note compatibility through WP 4.5+.

## 1.1 _(2016-03-17)_
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

## 1.0.3 _(2015-02-19)_
* Escape values in some attributes for added precaution
* Add more unit tests
* Note compatibility through WP 4.1+
* Update copyright date (2015)

## 1.0.2 _(2014-08-25)_
* Add `version()` to return current plugin version
* Minor plugin header reformatting
* Minor code reformatting (spacing, bracing)
* Change documentation links to wp.org to be https
* Note compatibility through WP 4.0+
* Add plugin icon

## 1.0.1
* Tweak description
* Note compatibility through WP 3.8+
* Update copyright date (2014)
* Change donate link

## 1.0
* Initial public release
