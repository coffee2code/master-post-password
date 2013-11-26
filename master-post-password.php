<?php
/**
 * @package Master_Post_Password
 * @author Scott Reilly
 * @version 1.0
 */
/*
Plugin Name: Master Post Password
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/master-post-password/
Author: Scott Reilly
Author URI: http://coffee2code.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /lang/
Description: Define a master password that works for any passworded post. The original post password still works as well.

Compatible with WordPress 3.6+ through 3.7+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/master-post-password/
*/

/*
	Copyright (c) 2013 by Scott Reilly (aka coffee2code)

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
	 * @var c2c_MasterPostPassword
	 */
	private static $instance;

	/**
	 * The meta key for storing the original slug a trashed post had before
	 * being taken over by a new post.
	 *
	 * @var string
	 */
	static $setting_name = 'c2c_master_post_password';

	/**
	 * Gets singleton instance, creating it if necessary.
	 */
	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * The constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * Initializes the plugin.
	 */
	public function plugins_loaded() {
		// Load textdomain
		load_plugin_textdomain( 'c2cmpp', false, basename( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'lang' );

		add_filter( 'the_password_form', array( $this, 'check_master_password' ) );

		if ( ! defined( 'C2C_MASTER_POST_PASSWORD' ) || ! C2C_MASTER_POST_PASSWORD )
			add_action( 'admin_init', array( $this, 'initialize_setting' ) );

	}

	/**
	 * Initializes setting.
	 */
	public function initialize_setting() {
		if ( ! current_user_can( 'manage_options' ) )
			return;

		register_setting( 'reading', self::$setting_name );
		add_settings_field( self::$setting_name, __( 'Master Post Password', 'c2cmpp' ), array( $this, 'display_option' ), 'reading' );
	}

	/**
	 * Displays admin setting field.
	 *
	 * @param array $args
	 */
	public function display_option( $args ) {
		echo '<input type="text" name="' . self::$setting_name . '" value="' . $this->get_master_password() . '"/>';
		echo '<p class="description">' . __( 'A password that can be used to access any passworded post.', 'c2cmpp' ) . '</p>';
		echo '<p class="description">' . __( "<strong>NOTE:</strong> Each passworded post's original post password will continue to work as well.", 'c2cwpp' ) . '</p>';
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
		if ( defined( 'C2C_MASTER_POST_PASSWORD' ) && C2C_MASTER_POST_PASSWORD )
			return C2C_MASTER_POST_PASSWORD;
		else
			return get_option( self::$setting_name );
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
		if ( defined( 'C2C_MASTER_POST_PASSWORD' ) && C2C_MASTER_POST_PASSWORD )
			self::get_master_password();
		else
			update_option( self::$setting_name, $password );

		return $password;
	}

	/**
	 * Checks the master password to see if the post content can be returned
	 * instead of the password form.
	 *
	 * NOTE: See core #XXX for ticket requesting the value of $post be sent
	 * to functions hooking 'post_password_form'.
	 *
	 * @param string $text The password form markup.
	 * @return string The post content (if the master password matches) or the
	 * password form.
	 */
	public function check_master_password( $text ) {
		// If the master post password was provided, return post content
		if ( $this->post_master_password_provided() ) {
			// Ideally, eventually we could do the following:
			/*
			$post = get_post();
			$post->post_password = '';
			return get_the_content( null, false, $post );
			*/
			return $this->get_the_content();
		// Else return password form
		} else {
			return $text;
		}
	}

	/**
	 * Determines if submitted post password matches the master password.
	 *
	 * @return bool True == master password provided and matches.
	 */
	private function post_master_password_provided() {
		if ( ! isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH] ) )
			return false;

		$master_password = self::get_master_password();

		// If no master password was defined, then no reason to check if it was
		// provided.
		if ( empty( $master_password ) )
			return false;

		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$hasher = new PasswordHash( 8, true );

		$hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
		if ( 0 !== strpos( $hash, '$P$B' ) )
			return false;

		return $hasher->CheckPassword( $master_password, $hash );
	}

	/**
	 * Returns the content for a post, disregarding any post password
	 * considerations.
	 *
	 * NOTE: This is lame. This function is basically a copy of WP's
	 * get_the_content() with the post_password_required() check removed.
	 * Unfortunately, there is currently no other way to get the content
	 * for a passworded post if the original password isn't provided.
	 *
	 * NOTE: Because this is being called indirectly, values for its
	 * arguments (which match WP's get_the_content()) will not have been
	 * passed along. Nor are they accessible. So for passworded posts
	 * unlocked with the master password, those values will be ignored.
	 *
	 * @param string $more_link_text. Optional. Content for when there is more text.
	 * @param bool   $stripteaser     Optional. Strip teaser content before the more text. Default is false.
	 * @return string
	 */
	private function get_the_content( $more_link_text = null, $strip_teaser = false ) {
		global $page, $more, $preview, $pages, $multipage;

		$post = get_post();

		if ( null === $more_link_text )
			$more_link_text = __( '(more&hellip;)' );

		$output = '';
		$has_teaser = false;

		if ( $page > count( $pages ) ) // if the requested page doesn't exist
			$page = count( $pages ); // give them the highest numbered page that DOES exist

		$content = $pages[$page - 1];
		if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
			$content = explode( $matches[0], $content, 2 );
			if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
				$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );

			$has_teaser = true;
		} else {
			$content = array( $content );
		}

		if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) )
			$strip_teaser = true;

		$teaser = $content[0];

		if ( $more && $strip_teaser && $has_teaser )
			$teaser = '';

		$output .= $teaser;

		if ( count( $content ) > 1 ) {
			if ( $more ) {
				$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
			} else {
				if ( ! empty( $more_link_text ) )
					$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
				$output = force_balance_tags( $output );
			}
		}

		if ( $preview ) // preview fix for javascript bug with foreign languages
			$output = preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );

		return $output;
	}

}

c2c_MasterPostPassword::get_instance();

endif;

