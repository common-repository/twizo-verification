<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 19/12/2017
 * Time: 12:15
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/** @var twizo_TFASettingsController $settings */
if($settings->twizo_getStep() !== 1 && $settings->twizo_getStep() !== 2){ ?>
    <div class="<?php echo htmlspecialchars($settings->twizo_getAlertClass()); ?>" <?php echo htmlspecialchars($settings->twizo_getHidden()); ?>>
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <?php echo htmlspecialchars($settings->twizo_getMessage()); ?>
    </div>

<h3 style="padding:0"><?php _e("Two-Factor Authentication (2FA)", "twizo-verification"); ?></h3>
<p><?php _e("Welcome to your Two-Factor Authentication (2FA) settings. By enabling the 2FA, a new layer of security
    will be added to your account. When logging in you will be prompted for 2FA.", "twizo-verification"); ?></p>

    <form action="" method="POST">
    <input type="hidden" name="enable" value="<?php echo htmlspecialchars($settings->twizo_getButtonText()) ?>"/>
    <input type="submit" class="button-twizo"
           value="<?php echo htmlspecialchars($settings->twizo_getButtonText()) ?> <?php _e("Two-Factor Authentication", "twizo-verification"); ?>"/>
</form>
<?php } ?>