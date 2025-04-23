# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Add a filter at the point at which a master password is being authenticated for use. Would facilitate the ability to log who, when, and where a master password is being used.
* Support multiple master passwords
  * Allows for multiple master passwords to be in use at any one time (e.g. given to different users or groups of users) and then revoked without needing everyone to be notified of a change to the password.
  * Via repeatable password fields or maybe just use a textarea as the input field
  * Could benefit from some way of leaving a note for context about given password e.g. "pw given to john", "pw given to the 2020 training class".
    * Each password could be an input with associated optional comment input field.
    * Or in the texarea approach, could separate password from comment, e.g. "this-is-the-password # And this is comment" or "universal-password2 // Note about password"
  * If entered and shown discretely, then could also have a "Share to/with" button.
    * Makes that particular password available to a given user account, who can see if in their profile (and maybe alerted by email that such a password was shared with them and how to access it)
    * Can track who a password was shared with.
      * Each password can show a list of users for whom the password was shared with
      * If user is deleted or demoted, the admin can be warned that the password may still be known by the person
      * Admin can see who would be affected by removal or changing of the password
      * Admin can replace the password, and users who would be affected would have the new password available in their profile (+ optional email)
    * Then again, this is going afield of this plugin's scope and venturing in a round about way into post access control whereby users can just selectively be given access and post passwords need not be involved.
* Support custom per-user master post passwords
  * Only admins can set it (via the user's profile page?).
  * The field should (optionally?) appear in their profile for users who have a passsword defined, obscured by default with ability to reveal password.
  * Add filter and UI toggle to disable per-user master post password
  * Might then need a custom user listing column to indicate which users have a custom master password defined
  * ...and/or list users with custom mpp (and the passwords) in a table on the plugin's settings page
  * Bulk set/unset mpp for users?
  * Definitely need a way clear mpp for all users
* Add helper to autogenerate a random password
* Add a filter in `get_master_password()` to allow programmatically overriding master post password?
  * If so, should that also override constant, or just the value from the setting? (Leaning towards constant reigning supreme)
* Checkbox to configure whether existing post passwords should be ignored, making the master post password the only acceptable password for unlocking passworded posts.
* Consider renaming plugin to not have "master" in its name. "Universal Post Password", "Post Password Override", "Primary Post Password", "Skeleton Key", "Post Password Skeleton Key"
  * Obviously, this requires renaming: functions, constant, files, class. setting name(?)
  * Update all references in docs
  * Back-compat for rename of constant and setting
* If a master post password is set via constant, still add a placeholder setting of sorts (which doesn't actually allow input) to indicate a password was set via constant and thus cannot be set via settings.
* Add setting to disable existing post passwords, making the master password the only password that will work.
* Hash password?
  * This precludes being able to show users/admins what the existing password is. Those security-minded enough can just use the constant.
  * WordPress doesn't hash post passwords.
  * Could offer it as an option.
    * If already hashed at the time, then choosing to not hash it should force the password to be reset (or re-entered).
    * If not hashed at the time, the existing password can be hashed.
    * Complicates things if multiple or per-user master passwords are implemented

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/master-post-password/) or on [GitHub](https://github.com/coffee2code/master-post-password/) as an issue or PR).
