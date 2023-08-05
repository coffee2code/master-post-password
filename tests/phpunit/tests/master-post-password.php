<?php

defined( 'ABSPATH' ) or die();

class Master_Post_Password_Test extends WP_UnitTestCase {

	private static $master_pw_via_constant = 'constantmasterpw';

	public function setUp() {
		parent::setUp();

		$this->obj = c2c_MasterPostPassword::get_instance();
	}

	public function tearDown() {
		wp_reset_postdata();
		unset( $GLOBALS['_COOKIE'] );
		parent::tearDown();
		c2c_MasterPostPassword::set_master_password( '' );

		unset( $GLOBALS['wp_settings_fields'] );
		unset( $GLOBALS['wp_registered_settings'] );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	/**
	 * Loads post as if in loop.
	 *
	 * @param int $post_id Post ID.
	 */
	protected function load_post( $post_id ) {
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );
		return $post;
	}

	/**
	 * Simulates submitting post password, namely to set a cookie.
	 *
	 * Mostly cribbed from wp-login.php's 'postpass' submit handler.
	 *
	 * @param string $password The password to set.
	 */
	protected function submit_post_password( $password ) {
		global $_COOKIE;
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher = new PasswordHash( 8, true );
		$_COOKIE[ 'wp-postpass_' . COOKIEHASH ] = $hasher->HashPassword( wp_unslash( $password ) );
	}


	/*
	 *
	 * Start by testing core WP handling of post passwords since there are no
	 * existing tests for them.
	 *
	 */


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_MasterPostPassword' ) );
	}

	public function test_version() {
		$this->assertEquals( '1.3.7', c2c_MasterPostPassword::version() );
	}

	public function test_hooks_plugins_loaded() {
		$this->assertEquals( 10, has_filter( 'plugins_loaded', array( 'c2c_MasterPostPassword', 'get_instance' ) ) );
	}

	public function test_hooks_post_password_required() {
		$this->assertEquals( 10, has_filter( 'post_password_required', array( $this->obj, 'post_password_required' ) ) );
	}

	public function test_hooks_admin_init() {
		$this->assertEquals( 10, has_action( 'admin_init', array( $this->obj, 'initialize_setting' ) ) );
	}

	public function test_setting_name() {
		$this->assertEquals( 'c2c_master_post_password', c2c_MasterPostPassword::$setting_name );
	}

	/*
	 * initialize_setting()
	 */

	public function test_setting_is_registered_for_authorized_user() {
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$this->obj->initialize_setting();

		$this->assertArrayHasKey( 'c2c_master_post_password', get_registered_settings() );
	}

	public function test_setting_is_not_registered_for_unauthorized_user() {
		$user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		$this->obj->initialize_setting();

		$this->assertArrayNotHasKey( 'c2c_master_post_password', get_registered_settings() );
	}

	/*
	 * display_option()
	 */

	public function test_display_option() {
		$password = $this->obj->set_master_password( 'somepassword' );
		$expected = <<<HTML
<input type="text" name="c2c_master_post_password" value="somepassword"/>
<p class="description">A password that can be used to access any passworded post.</p>
<p class="description"><strong>NOTE:</strong> Each passworded post's original post password will continue to work as well.</p>
HTML;

		$this->expectOutputRegex( '~^' . preg_quote( $expected ) . '?~', $this->obj->display_option( array() ) );
	}

	public function test_passworded_post_returns_password_form_as_content() {
		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => 'Protected content' ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	public function test_providing_valid_post_password_makes_content_available() {
		$pw = 'abcabc';
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => $pw, 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( $pw );

		$this->assertEquals( $content, get_the_content() );
	}

	public function test_providing_invalid_post_password_doesnt_make_content_available() {
		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => 'Protected content' ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( 'badpass' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	public function test_post_password_for_one_post_doesnt_allow_access_to_another_passworded_post() {
		$post_pw = 'postpw';
		$content1 = 'Protected content';
		$content2 = 'More protected content.';

		$post_id1 = $this->factory->post->create( array( 'post_password' => $post_pw, 'post_content' => $content1 ) );
		$post1 = $this->load_post( $post_id1 );

		$this->assertEquals( get_the_password_form( $post1 ), get_the_content() );

		$this->submit_post_password( $post_pw );

		$this->assertEquals( $content1, get_the_content() );

		// Check post2
		$post_pw = 'different_post_pw';
		$post_id2 = $this->factory->post->create( array( 'post_password' => $post_pw, 'post_content' => $content2 ) );
		$post2 = $this->load_post( $post_id2 );

		$this->assertEquals( get_the_password_form( $post2 ), get_the_content() );

		$this->submit_post_password( $post_pw );

		$this->assertEquals( $content2, get_the_content() );
	}

	public function test_changing_post_password_invalidates_older_password() {
		$post_pw = 'abcabc';
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => $post_pw, 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( $post_pw );

		$this->assertEquals( $content, get_the_content() );

		// Now change post pw
		$post_pw = 'newpass';
		$post->post_password = $post_pw;
		wp_update_post( $post );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}


	/**
	 *
	 * Now start with the plugin-specific tests.
	 *
	 */

	public function test_providing_valid_master_post_password_makes_content_available() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'abcabc2' );
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content, get_the_content() );
		$this->assertFalse( $this->obj->post_password_required( true, null ) );
	}

	public function test_providing_valid_master_post_password_makes_all_passworded_content_available() {
		// Run test to set up creating a post and using the master password.
		$this->test_providing_valid_master_post_password_makes_content_available();

		$content = 'More protected content.';

		$post_id = $this->factory->post->create( array( 'post_password' => 'cdecde', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( $content, get_the_content() );
		$this->assertFalse( $this->obj->post_password_required( true, null ) );
	}

	public function test_providing_invalid_master_post_password_doesnt_make_content_available() {
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );

		$this->submit_post_password( 'badpassword' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );
	}

	public function test_changing_master_post_password_invalidates_older_password() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'masterpw' );
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content, get_the_content() );
		$this->assertFalse( $this->obj->post_password_required( true, null ) );

		$master_pw = c2c_MasterPostPassword::set_master_password( 'newpassword' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );
	}

	public function test_master_password_usage_seemlessly_takes_over_for_change_in_post_password() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'masterpw' );
		$post_pw = 'postpw';
		$content1 = 'Protected content';
		$content2 = 'More protected content.';

		$post_id1 = $this->factory->post->create( array( 'post_password' => $post_pw, 'post_content' => $content1 ) );
		$post1 = $this->load_post( $post_id1 );

		$this->assertEquals( get_the_password_form( $post1 ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );

		// Set post pasword first so it'll take effect for the post
		$this->submit_post_password( $post_pw );

		$this->assertEquals( $content1, get_the_content() );
		$this->assertFalse( $this->obj->post_password_required( false, null ) );

		// Check post2
		$post_id2 = $this->factory->post->create( array( 'post_password' => 'different_post_pw', 'post_content' => $content2 ) );
		$post2 = $this->load_post( $post_id2 );

		$this->assertEquals( get_the_password_form( $post2 ), get_the_content() );
		$this->assertTrue( $this->obj->post_password_required( true, null ) );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content2, get_the_content() );
		$this->assertFalse( $this->obj->post_password_required( true, null ) );
	}

	/**
	 *
	 * Test use of constant. Due to nature of constants, this grouping of tests should always be last.
	 *
	 */

	public function test_master_password_can_be_set_via_constant() {
		define( 'C2C_MASTER_POST_PASSWORD', self::$master_pw_via_constant );

		$this->assertEquals( self::$master_pw_via_constant, c2c_MasterPostPassword::get_master_password() );
	}

	public function test_master_password_set_via_constant_cannot_be_overridden() {
		c2c_MasterPostPassword::set_master_password( 'different_password' );

		$this->assertEquals( self::$master_pw_via_constant, c2c_MasterPostPassword::get_master_password() );
	}

	public function test_set_master_password_returns_constant() {
		$this->assertEquals( self::$master_pw_via_constant, c2c_MasterPostPassword::set_master_password( 'different_password' ) );
	}

}
