<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 13/12/2017
 * Time: 11:28
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>
<h3><?php _e("Welcome to Twizo - Two-Factor Authentication for WordPress and WooCommerce.","twizo-verification"); ?></h3>
<?php _e("Since this is the first time you have installed Twizo Verification on this Wordpress installation, we will help you
install Twizo within a few steps.","twizo-verification"); ?>
<br>
<br>
<h2><?php _e("Do you already have a Twizo account?","twizo-verification"); ?></h2>
<a class="float-left" style="margin-top:1px;margin-right:10px" href="https://register.twizo.com/" target="_blank">
    <button class="button-twizo"><?php _e("No, create one","twizo-verification"); ?></button>
</a>
<form class="float-left" action="" method="POST">
    <input type="hidden" name="adminStep" value="1">
    <input type="submit" class="button-twizo" value="<?php _e("Yes, start the installation","twizo-verification"); ?>">
</form>
