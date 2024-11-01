<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 11/12/2017
 * Time: 13:56
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $controller;
$wpdb = $controller->twizo_getWpdb();
$charset_collate = $wpdb->get_charset_collate();
$table_name = $controller->twizo_getDatabaseHelper()->twizo_getTableNameUsers();
$table_name_settings = $controller->twizo_getDatabaseHelper()->twizo_getTableNameSettings();
$table_name_trusted = $controller->twizo_getDatabaseHelper()->twizo_getTableNameTrusted();

//User database
if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
		user_id mediumint(9) NOT NULL,
		enabled_2fa boolean NOT NULL,
          phone_number BIGINT,
          country_number int,
          preferred_type varchar(50),
		UNIQUE KEY user_id (user_id)
	) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

//Settings database
if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name_settings}'") != $table_name_settings) {
    $sql_settings = "CREATE TABLE $table_name_settings (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		host varchar(50),
		api varchar(50),
		issuer varchar(64),
		preferred_type varchar(50),
		img_url varchar(255),
		sender varchar(18),
		UNIQUE KEY id (id)
	) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_settings);
}

//Trusted database
if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name_trusted}'") != $table_name_trusted) {
    $sql_trusted = "CREATE TABLE $table_name_trusted (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NOT NULL ,
		hash text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_trusted);
}