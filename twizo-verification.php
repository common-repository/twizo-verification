<?php
/*
Plugin Name: Twizo Verification
Plugin URI: http://www.twizo.com
Description: Twizo Verification is a 2FA plugin made for WordPress and WooCommerce.
Author: Twizo
Version: 3.2
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


// Include the main plugin class.
if ( ! class_exists( 'Twizo' ) ) {
    include_once dirname( __FILE__ ) . '/includes/twizo.php';
}

add_action('plugins_loaded', 'twizo_load_textdomain');
function twizo_load_textdomain() {
    load_plugin_textdomain( 'twizo-verification', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

add_action('admin_enqueue_scripts', 'twizo_admin_scripts');

function twizo_admin_scripts($hook)
{
    global $controller;
    wp_register_script(
        'my-upload',
        $controller->twizo_getPluginDirUrl() . 'templates/scripts/uploader.js',
        array( 'jquery', 'media-upload', 'thickbox' )
    );

    wp_enqueue_script('my-upload');
    wp_enqueue_style('thickbox');

    wp_enqueue_script('media-upload');
    wp_enqueue_style('thickbox');
}
