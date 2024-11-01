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
    <h3 style="padding:0;"><?php _e("First time set-up", "twizo-verification"); ?></h3>
<?php _e("Let's setup Two-Factor Authentication (2FA) for yor account. We will need to verify you phone number for 2FA. We will do that by sending you a token via SMS which you will need to enter.", "twizo-verification"); ?>
<br><br>
<?php _e("So select your country, enter your phone number and click the Verify button.", "twizo-verification"); ?>
<br>
<div class="<?php echo htmlspecialchars($settings->twizo_getAlertClass()); ?>" <?php echo htmlspecialchars($settings->twizo_getHidden()); ?>>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php echo htmlspecialchars($settings->twizo_getMessage()); ?>
</div>
    <br>
    <form action="" method="POST">
        <div style="float:left;">
        <?php
        $userCountryNumber = $settings->twizo_getCountryNumber();
        require(__DIR__ . '/country-number.php');
        ?>
        </div>
        <div style="float:left;">
            <input type='text' name='phone_number' id="phone_number" style="margin-left:10px;width:190px;"
                   placeholder='<?php _e("Phone number", "twizo-verification"); ?>'
                   value="<?php echo htmlspecialchars($settings->twizo_getPhoneNumber()); ?>">
        </div>
        <div style="clear:both;"></div>

        <input type="hidden" name="step2FA" value="1"/>
        <input type="hidden" name="first_time" value="true"/>
        <input type="hidden" name="enable" value="Enable"/>
        <input type="submit" class="button-twizo" id="verify" style="margin-top:20px;" value="<?php _e("Verify", "twizo-verification"); ?>"/>
    </form>
    <!-- Needed variables for verification -->
    <input type="hidden" id="country_number" value="<?php echo htmlspecialchars($settings->twizo_getCountryNumber()); ?>"/>
    <input type="hidden" id="sessionToken" value="<?php echo htmlspecialchars($settings->twizo_getSessionToken()); ?>"/>
    <input type="hidden" id="image_url" value="<?php echo htmlspecialchars($settings->twizo_getImageUrl()) ?>"/>
    <input type="hidden" id="first_time" value="<?php echo $_POST['first_time']; ?>"/>
