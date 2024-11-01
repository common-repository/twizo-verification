<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 06/12/2017
 * Time: 17:41
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register new end point for woocommerce my account page
 */
function add_twizo_verification_endpoint() {
    add_rewrite_endpoint( 'twizo-verification', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'add_twizo_verification_endpoint' );

/**
 * Flush rewrite rules on plugin activation.
 */
function twizo_verification_rewrite_rules() {
    add_rewrite_endpoint( 'twizo-verification', EP_ROOT | EP_PAGES );
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'twizo_verification_rewrite_rules' );
register_deactivation_hook( __FILE__, 'twizo_verification_rewrite_rules' );

/**
 * Add query parameters
 * @param $vars variables
 * @return array variables
 */
function twizo_verification_query_vars( $vars ) {
    $vars[] = 'twizo-verification';
    return $vars;
}

add_filter( 'query_vars', 'twizo_verification_query_vars', 0 );

/**
 * Insert endpoint to my account menu
 * @param $items
 * @return mixed
 */
function twizo_verification_link_my_account( $items ) {
    $items['twizo-verification'] = __('2FA settings','twizo-verification');
    return $items;
}


add_filter( 'woocommerce_account_menu_items', 'twizo_verification_link_my_account' );

/**
 * Add content to the menu
 */
function twizo_verification_content() {
    require(__DIR__.'/templates/tfa-settings-body.php');
}

add_action( 'woocommerce_account_twizo-verification_endpoint', 'twizo_verification_content' );