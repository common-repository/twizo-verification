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

global $wpdb;
global $controller;

require (__DIR__ ."/../lib/autoload.php");

//Includes
require_once __DIR__ . '/controllers/twizo_Controller.php';
$controller = new twizo_Controller(plugin_dir_url(__FILE__));
require_once __DIR__ . '/twizo-admin.php';
require_once __DIR__ . '/twizo-database-installer.php';


//Get the results.
$results = $controller->twizo_getDatabaseHelper()->twizo_getSettings();

//Include only after setup
if(count($results)>0 && !empty($results[0]->api) && !empty($results[0]->host)) {
    require_once __DIR__ . '/twizo-woocommerce-customer.php';
    require_once __DIR__ . '/twizo_TwizoLogin.php';
    new twizo_TwizoLogin();
}