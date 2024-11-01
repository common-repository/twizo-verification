<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 06/12/2017
 * Time: 17:31
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

add_action( 'admin_menu', 'twizo_plugin_menu' );

/**
 * Add the plugin page to the menu
 */
function twizo_plugin_menu() {
    add_menu_page( 'Twizo Verification', 'Twizo Verification', 'manage_options', 'twizo-verification-menu',
        'twizo_plugin_page', plugin_dir_url(__FILE__).'/img/twizo.ico' );
}

/**
 * Set the plugin page
 */
function twizo_plugin_page() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require_once(__DIR__.'/templates/admin-page.php');
}