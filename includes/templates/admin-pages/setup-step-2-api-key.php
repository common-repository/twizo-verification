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
<h3><?php _e("Step (2/3) - API key","twizo-verification"); ?></h3>
<?php _e("Please enter your Twizo API key. You can get your API key <a href=\"".esc_url( 'https://portal.twizo.com/applications/' )."\" style=\"color: #FF952E;font-weight: bold;\" target=\"_blank\">here</a>. Troubles finding your API key? Checkout this <a href=\"".esc_url( 'https://www.twizo.com/help/can-find-api-key/' )."\" style=\"color: #FF952E;font-weight: bold;\" target=\"_blank\">video</a>.","twizo-verification"); ?><br>
<br><br>
<div class="<?php echo htmlspecialchars($adminPageController->twizo_getAlertClass()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getHidden()); ?>>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php echo htmlspecialchars($adminPageController->twizo_getMessage()); ?>
</div>
<form action="" method="POST">
    <input type="hidden" name="adminStep" value="<?php echo htmlspecialchars($adminPageController->twizo_getStep()); ?>">
    <input type="text" class="api-input" name="api_key" placeholder="<?php _e("Enter your Twizo API key here", "twizo-verification"); ?>"
           value="<?php echo htmlspecialchars($adminPageController->twizo_getApiKey()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getDisabledInput()); ?>>
    <input type="submit" class="button-twizo" style="margin-top:10px;" value="<?php echo htmlspecialchars($adminPageController->twizo_getButtonText()); ?>">
</form>
