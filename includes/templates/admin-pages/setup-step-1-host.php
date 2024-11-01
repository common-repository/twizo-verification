<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 13/12/2017
 * Time: 13:44
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>

<h3><?php _e("Step (1/3) - Host","twizo-verification"); ?></h3>
<?php _e("To provide the best experience to your customers, please select the host closest to your location from the list below.","twizo-verification"); ?><br><br>
<div class="success" <?php echo htmlspecialchars($adminPageController->twizo_getHidden()); ?>>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php echo htmlspecialchars($adminPageController->twizo_getMessage()); ?>
</div>
<form action="" method="POST">
    <input type="hidden" name="adminStep" value="<?php echo htmlspecialchars($adminPageController->twizo_getStep()); ?>">
    <select name="host" <?php echo htmlspecialchars($adminPageController->twizo_getHiddenHost()); ?>>
        <?php
        foreach($adminPageController->twizo_getController()->twizo_getTwizoHosts() as $host) {
            echo '<option value="'.$host['host'].'" ' . ($host['host'] == $adminPageController->twizo_getHostCurrent() ? 'selected' : '') . '>'.$host['host'] . ' - ' .  $host['location'].'</option>';
        }
        ?>
    </select>
    <input type="submit" class="button-twizo" value="<?php echo htmlspecialchars($adminPageController->twizo_getButtonText()); ?>">
</form>

