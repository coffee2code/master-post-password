<?php
/**
 * Plugin Name: Master Post Password
 * Version:     1.3.7
 * Plugin URI:  https://coffee2code.com/wp-plugins/master-post-password/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * Text Domain: master-post-password
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Define a master post password that works for all passworded posts, while permitting the original post passwords to also work.
 *
 * Compatible with WordPress 4.7 through 6.3+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/extend/plugins/master-post-password/
 *
 * @package Master_Post_Password
 * @author  Scott Reilly
 * @version 1.3.7
 */

/*
	Copyright (c) 2013-2023 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_MasterPostPassword' ) ) :

class c2c_MasterPostPassword {

	/**
	 * The singleton instance of this class.
	 *
	 * @access private
	 * @var c2c_MasterPostPassword
	 */
	private static $instance;

	/**
	 * The meta key for storing the original slug a trashed post had before
	 * being taken over by a new post.
	 *
	 * @var string
	 */
	public static $setting_name = 'c2c_master_post_password';

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public static function version() {
		return '1.3.7';
	}

	/**
	 * Gets singleton instance, creating it if necessary.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * The constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		// Load textdomain.
		load_plugin_textdomain( 'master-post-password' );

		add_filter( 'post_password_required', array( $this, 'post_password_required' ), 10, 2 );

		if ( ! defined( 'C2C_MASTER_POST_PASSWORD' ) || ! C2C_MASTER_POST_PASSWORD ) {
			add_action( 'admin_init', array( $this, 'initialize_setting' ) );
		}

	}

	/**
	 * Initializes setting.
	 */
	public function initialize_setting() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		register_setting( 'reading', self::$setting_name );
		add_settings_field( self::$setting_name, __( 'Master Post Password', 'master-post-password' ), array( $this, 'display_option' ), 'reading' );
	}

	/**
	 * Displays admin setting field.
	 *
	 * @param array $args
	 */
	public function display_option( $args ) {
		echo '<input type="text" name="' . esc_attr( self::$setting_name ) . '" value="' . esc_attr( $this->get_master_password() ) . '"/>' . "\n";
		echo '<p class="description">' . __( 'A password that can be used to access any passworded post.', 'master-post-password' ) . "</p>\n";
		echo '<p class="description">' . __( "<strong>NOTE:</strong> Each passworded post's original post password will continue to work as well.", 'master-post-password' ) . "</p>\n";
	}

	/**
	 * Gets the master password.
	 *
	 * Initially check for constant C2C_MASTER_POST_PASSWORD. Otherwise,
	 * retrieve the setting value.
	 *
	 * @return string The master post password.
	 */
	public static function get_master_password() {
		if ( defined( 'C2C_MASTER_POST_PASSWORD' ) && C2C_MASTER_POST_PASSWORD ) {
			return C2C_MASTER_POST_PASSWORD;
		} else {
			return get_option( self::$setting_name );
		}
	}

	/**
	 * Sets the master password.
	 *
	 * If the master password was set via constant, it cannot be changed.
	 *
	 * @param string $password The master password.
	 * @return string The current master post password. Either the value of the constant (unchanged from this attempt), or the new value.
	 */
	public static function set_master_password( $password ) {
		if ( defined( 'C2C_MASTER_POST_PASSWORD' ) && C2C_MASTER_POST_PASSWORD ) {
			$password = self::get_master_password();
		} else {
			update_option( self::$setting_name, $password );
		}

		return $password;
	}

	/**
	 * Checks the master password to see if the post content can be returned
	 * instead of the password form.
	 *
	 * @since 1.2
	 *
	 * @param bool    $required Whether the user needs to supply a password. True if password has not been
	 *                          provided or is incorrect, false if password has been supplied or is not required.
	 * @param WP_Post $post     Post data.
	 * @return bool   True if the post passowrd form is still required, false if not.
	 */
	public function post_password_required( $required, $post ) {
		// Only check for master post password if the password form is required.
		if ( $required ) {
			$required = ! $this->post_master_password_provided();
		}

		return $required;
	}

	/**
	 * Determines if submitted post password matches the master password.
	 *
	 * @access private
	 * @see post_password_required()
	 *
	 * @return bool True if master password is provided and matches, false if not.
	 */
	private function post_master_password_provided() {
		if ( ! isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
			return false;
		}

		$master_password = self::get_master_password();

		// If no master password was defined, then no reason to check if it was
		// provided.
		if ( ! $master_password ) {
			return false;
		}

		$hasher = new PasswordHash( 8, true );

		$hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
		if ( 0 !== strpos( $hash, '$P$B' ) ) {
			return false;
		}

		return $hasher->CheckPassword( $master_password, $hash );
	}

}

add_action( 'plugins_loaded', array( 'c2c_MasterPostPassword', 'get_instance' ) );

endif;
