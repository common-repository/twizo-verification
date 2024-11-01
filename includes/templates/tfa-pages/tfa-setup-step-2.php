<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 19/12/2017
 * Time: 12:11
 */
/** @var twizo_TFASettingsController $settings */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>
    <h3 style="padding:0"><?php _e("First time set-up", "twizo-verification"); ?></h3>

<?php
_e("Two-Factor Authentication (2FA) is now enabled. Don't forget to generate your backup codes. You can do that easily by clicking on the next page on the button 'Register 2FA' and then select 'Backup codes'. You can also register there for other verification types.", 'twizo-verification'); ?>
    <br><br>
    <form action="" method="POST">
        <input type="hidden" name="step2FA" value="3"/>
        <input type="hidden" name="enable" value="Enable"/>
        <input type="submit" class="button-twizo" value="<?php _e("Finish", "twizo-verification"); ?>"/>
    </form>
