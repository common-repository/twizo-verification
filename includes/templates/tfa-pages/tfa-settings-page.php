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

<br>
<hr>
<h3><?php _e("Phone number", "twizo-verification"); ?></h3>

<?php _e("For Two-Factor Authentication, your phone number is needed. When you update your phone number, a SMS will be send to you with a token to verify if the number is correct.", "twizo-verification"); ?>
<h3><?php _e("Phone number:", "twizo-verification"); ?></h3>
<form action="" method="POST">
    <div style="float:left;">
        <?php
        $userCountryNumber = $settings->twizo_getResults()[0]->country_number;
        require(__DIR__ . '/country-number.php');
        ?>
    </div>
    <div style="float:left;">
        <input type='text' name='phone_number' id="phone_number" style="margin-left:10px;width:190px;"
               placeholder='<?php _e("Phone number", "twizo-verification"); ?>'
               value="<?php echo htmlspecialchars($settings->twizo_getResults()[0]->phone_number); ?>">
    </div>
    <div style="clear:both;"></div>
    <input type="hidden" name="is2FA" value="in2FA"/>
    <input type="submit" class="button-twizo" id="verify" style="margin-top:20px;" value="<?php _e("Verify", "twizo-verification"); ?>"/>
</form>
<br>
<hr>
<h3><?php _e("Register", "twizo-verification"); ?></h3>
<?php _e("For some verification types you first need to register. For SMS and Voice call this is not needed. Click on the 'Register' button to easily register for the different verification types.", "twizo-verification"); ?>

<form action="" method="POST" style="margin-top:20px;">
    <input type="hidden" name="register" value="true"/>
    <input type="submit" class="button-twizo" id="submitRegister" value="<?php _e("Register 2FA", "twizo-verification"); ?>"/>
</form>
<br>
<hr>
<h3><?php _e("Preferred verification type", "twizo-verification"); ?></h3>
<?php _e("You can configure you preferred verification type. When you login and a 2FA is required, this preferred verification type will be used, if you are registered for it.
", "twizo-verification"); ?>
    <br><br>
<form action="" method="POST">
    <select name="preferred_type" style="width:250px;">
    <?php
        $array = array('default' => 'Default') + $controller->twizo_getTwizoHelper()->twizo_getAllowedTypes(true);

        foreach ($array as $type => $typeName) {
            //skip backupcode as you cannot set it as preferred
            if($type == 'backupcode') {
                continue;
            } else {
                echo '<option value="' . (($type!='default')? $type : null) . '" ' . ($type == $settings->twizo_getResults()[0]->preferred_type ? 'selected' : '') . '>' . $typeName . '</option>';
            }
        }
    ?>
    </select><br><br>
    <input type="submit" class="button-twizo" value="<?php _e("Save", "twizo-verification"); ?>"/>
</form>
<input type="hidden" id="country_number" value="<?php echo htmlspecialchars($settings->twizo_getCountryNumber()); ?>"/>
<input type="hidden" id="sessionToken" value="<?php echo htmlspecialchars($settings->twizo_getSessionToken()); ?>"/>
<input type="hidden" id="registrationSessionToken" value="<?php echo htmlspecialchars($settings->twizo_getRegistrationSessionToken()); ?>"/>
<input type="hidden" id="image_url" value="<?php echo htmlspecialchars($settings->twizo_getImageUrl()); ?>"/>