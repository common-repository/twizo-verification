<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Twizo_Verification
 */

//Redirect to directory: $ cd /locationofwordpress/wp-content/plugins/twizo-verification
//Set up test suite: $ bin/install-wp-tests.sh wordpress_test root root localhost:8889 latest
//Run tests: $ phpunit


$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}


if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	throw new Exception( "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/twizo-verification.php';
	require dirname(dirname(__FILE__)) . '/includes/controllers/twizo_CustomerSettingsController.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

global $controller;
$controller->getWpdb()->query("DELETE FROM " . $controller->twizo_getDatabaseHelper()->twizo_getTableNameUsers());
$controller->getWpdb()->query("DELETE FROM " . $controller->twizo_getDatabaseHelper()->twizo_getTableNameSettings());
$controller->getWpdb()->query("DELETE FROM " . $controller->twizo_getDatabaseHelper()->twizo_getTableNameSettings());

