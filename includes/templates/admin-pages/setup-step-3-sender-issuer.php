<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 13/12/2017
 * Time: 11:31
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/** @var twizo_AdminPageController $adminPageController */

?>
<h3><?php _e("Step (3/3) - API key","twizo-verification"); ?></h3>
<?php _e("For some of the verifications we need a Sender and Issuer.","twizo-verification"); ?><br>
<br>
<div class="<?php echo htmlspecialchars($adminPageController->twizo_getAlertClass()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getHidden()); ?>>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php echo htmlspecialchars($adminPageController->twizo_getMessage()); ?>
</div>
<form action="" method="POST">
    <input type="hidden" name="adminStep" value="<?php echo htmlspecialchars($adminPageController->twizo_getStep()); ?>">
    <?php _e("The sender will be displayed as sender ID for SMS verifications. See our tutorial '<a href=\"https://www.twizo.com/developers/tutorials/#sender\" style=\"color: #FF952E;\" target=\"_blank\">Sender ID</a>' for more information.","twizo-verification"); ?><br>
    <input type="text" class="api-input" name="sender" placeholder="<?php _e("Enter your Sender here", "twizo-verification"); ?>"
           value="<?php echo htmlspecialchars($adminPageController->twizo_getSender()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getDisabledInput()); ?>>
    <br><br>
    <?php _e("The issuer is the name of your website which will be shown for some of our verification types. See our help topic '<a href=\"https://www.twizo.com/help/what-is-an-issuer/\" style=\"color: #FF952E;\" target=\"_blank\">What is an issuer</a>' for more information.","twizo-verification"); ?><br>
    <input type="text" class="api-input" name="issuer" placeholder="<?php _e("Enter your Issuer here", "twizo-verification"); ?>"
           value="<?php echo htmlspecialchars($adminPageController->twizo_getIssuer()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getDisabledInput()); ?>>
    <input type="submit" class="button-twizo" style="margin-top:10px;" value="<?php echo htmlspecialchars($adminPageController->twizo_getButtonText()); ?>">
</form>
