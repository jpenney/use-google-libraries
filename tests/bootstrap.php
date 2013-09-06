<?php

/**
 * Bootstrap the testing environment
 * Uses wordpress tests (http://github.com/nb/wordpress-tests/) which uses PHPUnit
 * @package wordpress-plugin-tests
 *
 * Usage: change the below array to any plugin(s) you want activated during the tests
 *        value should be the path to the plugin relative to /wp-content/
 *
 * Note: Do note change the name of this file. PHPUnit will automatically fire this file when run.
 *
 */

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( 'use-google-libraries/use-google-libraries.php' ),
);

$wp_test_dir = getenv( 'WP_TEST_DIR' );
if ( !$wp_test_dir ) {
	$wp_test_dir = dirname( __FILE__ );
}

require_once $wp_test_dir . '/includes/functions.php';
require $wp_test_dir . '/includes/bootstrap.php';
require_once dirname( __FILE__ ) . '/testcase.php';
