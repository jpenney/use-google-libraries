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

$wp_test_inc = getenv( 'WP_TESTS_DIR' );
if ( ! $wp_test_inc ) {
	$wp_test_inc = dirname( __FILE__ ) . '/lib';
} else {
	$wp_test_inc = $wp_test_inc . '/includes';
}

require_once $wp_test_inc . '/functions.php';
require $wp_test_inc . '/bootstrap.php';
require_once dirname( __FILE__ ) . '/testcase.php';
