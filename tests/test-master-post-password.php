<?php

class Master_Post_Password_Test extends WP_UnitTestCase {

	private static $master_pw_via_constant = 'constantmasterpw';

	function setUp() {
		parent::setUp();
	}

	function tearDown() {
		wp_reset_postdata();
		unset( $GLOBALS['_COOKIE'] );
		parent::tearDown();
		c2c_MasterPostPassword::set_master_password( '' );
	}

	/**
	 * Loads post as if in loop.
	 *
	 * @param int $post_id Post ID.
	 */
	function load_post( $post_id ) {
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
	function submit_post_password( $password ) {
		global $_COOKIE;
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$hasher = new PasswordHash( 8, true );
		$_COOKIE[ 'wp-postpass_' . COOKIEHASH ] = $hasher->HashPassword( wp_unslash( $password ) );
	}

	/**
	 *
	 * Start by testing core WP handling of post passwords since there are no
	 * existing tests for them.
	 *
	 */

	function test_passworded_post_returns_password_form_as_content() {
		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => 'Protected content' ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	function test_providing_valid_post_password_makes_content_available() {
		$pw = 'abcabc';
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => $pw, 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( $pw );

		$this->assertEquals( $content, get_the_content() );
	}

	function test_providing_invalid_post_password_doesnt_make_content_available() {
		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => 'Protected content' ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( 'badpass' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	function test_post_password_for_one_post_doesnt_allow_access_to_another_passworded_post() {
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

	function test_changing_post_password_invalidates_older_password() {
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

	function test_providing_valid_master_post_password_makes_content_available() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'abcabc2' );
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content, get_the_content() );
	}

	function test_providing_valid_master_post_password_makes_all_passworded_content_available() {
		// Run test to set up creating a post and using the master password.
		$this->test_providing_valid_master_post_password_makes_content_available();

		$content = 'More protected content.';

		$post_id = $this->factory->post->create( array( 'post_password' => 'cdecde', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( $content, get_the_content() );
	}

	function test_providing_invalid_master_post_password_doesnt_make_content_available() {
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( 'badpassword' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	function test_changing_master_post_password_invalidates_older_password() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'masterpw' );
		$content = 'Protected content';

		$post_id = $this->factory->post->create( array( 'post_password' => 'abcabc', 'post_content' => $content ) );
		$post = $this->load_post( $post_id );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content, get_the_content() );

		$master_pw = c2c_MasterPostPassword::set_master_password( 'newpassword' );

		$this->assertEquals( get_the_password_form( $post ), get_the_content() );
	}

	function test_master_password_usage_seemlessly_takes_over_for_change_in_post_password() {
		$master_pw = c2c_MasterPostPassword::set_master_password( 'masterpw' );
		$post_pw = 'postpw';
		$content1 = 'Protected content';
		$content2 = 'More protected content.';

		$post_id1 = $this->factory->post->create( array( 'post_password' => $post_pw, 'post_content' => $content1 ) );
		$post1 = $this->load_post( $post_id1 );

		$this->assertEquals( get_the_password_form( $post1 ), get_the_content() );

		// Set post pasword first so it'll take effect for the post
		$this->submit_post_password( $post_pw );

		$this->assertEquals( $content1, get_the_content() );

		// Check post2
		$post_id2 = $this->factory->post->create( array( 'post_password' => 'different_post_pw', 'post_content' => $content2 ) );
		$post2 = $this->load_post( $post_id2 );

		$this->assertEquals( get_the_password_form( $post2 ), get_the_content() );

		$this->submit_post_password( $master_pw );

		$this->assertEquals( $content2, get_the_content() );
	}

	/**
	 *
	 * Test use of constant. Due to nature of constants, this grouping of tests should always be last.
	 *
	 */

	function test_master_password_can_be_set_via_constant() {
		define( 'C2C_MASTER_POST_PASSWORD', self::$master_pw_via_constant );

		$this->assertEquals( self::$master_pw_via_constant, c2c_MasterPostPassword::get_master_password() );
	}

	function test_master_password_set_via_constant_cannot_be_overridden() {
		c2c_MasterPostPassword::set_master_password( 'different_password' );

		$this->assertEquals( self::$master_pw_via_constant, c2c_MasterPostPassword::get_master_password() );
	}
}
