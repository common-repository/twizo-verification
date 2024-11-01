<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 18/12/2017
 * Time: 16:29
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/** @var twizo_Controller $controller */
?>
<div class="<?php echo htmlspecialchars($adminPageController->twizo_getAlertClass()); ?>" <?php echo htmlspecialchars($adminPageController->twizo_getHidden()); ?>>
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <?php echo htmlspecialchars($adminPageController->twizo_getMessage()); ?>
</div>
<div class="left-settings">
    <h2><?php _e("API settings", "twizo-verification"); ?></h2>
    <table>
        <tr>
            <form action="" method="POST">
            <td width="150">
                <?php _e("Host", "twizo-verification"); ?>
            </td>
            <td>
                <select class="api-input" name="host">
                    <?php
                    foreach ($adminPageController->twizo_getController()->twizo_getTwizoHosts() as $host) {
                        $selected = ($host['host'] == $adminPageController->twizo_getHostCurrent()) ? "selected" : "";
                        echo '<option value="' . $host['host'] . '" ' . $selected . '>' . $host['host'] . ' - ' . $host['location'] . '</option>';
                    }
                    ?>
                </select>
            </td>
                <td>&nbsp;</td>
            <td>
                <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
            </td>
            </form>
        </tr>

        <tr>
            <form action="" method="POST">
            <td>
                <?php _e("API key", "twizo-verification"); ?>
            </td>
            <td>
                <input type="text" class="api-input" name="api_key" placeholder="<?php _e("API KEY", "twizo-verification"); ?>" value="<?php echo $adminPageController->twizo_getApiKey(); ?>">
            </td>
            <td>&nbsp;</td>
            <td>
                <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
            </td>
            </form>
        </tr>

        <tr>
            <form action="" method="POST">
            <td>
                <?php _e("Preferred verification", "twizo-verification");?>
            </td>
            <td>
                <select class="api-input" name="preferred_type">
                    <?php
                    echo '<option value="default" ' . (is_null($adminPageController->twizo_getSelectedType()) ? 'selected' : '') . '>Default</option>';

                    foreach ($adminPageController->twizo_getController()->twizo_getTwizoHelper()->twizo_getAllowedTypes(true) as $type => $typeName) {
                        if($type != 'backupcode') {
                            $selected = ($type === $adminPageController->twizo_getSelectedType()) ? "selected" : "";

                            echo '<option value="' . $type . '" ' . $selected . '>' . $typeName . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
                <td>&nbsp;</td>
            <td>
                <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
            </td>
            </form>
        </tr>

        <tr>
            <form action="" method="POST">
            <td>
                <?php _e("Sender", "twizo-verification"); ?>
            </td>
            <td>
                <input type="text" class="api-input" name="sender" placeholder="<?php _e("Set your sender", "twizo-verification"); ?>" value="<?php echo $adminPageController->twizo_getSender(); ?>">
            </td>
                <td>&nbsp;</td>
            <td>
                <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
            </td>
            </form>
        </tr>
        <tr>
            <form action="" method="POST">
                <td>
                    &nbsp;
                </td>
                <td>
                    <?php _e("This text will be displayed as sender ID for SMS verifications. See our tutorial '<a href=\"https://www.twizo.com/developers/tutorials/#sender\" style=\"color: #FF952E;\" target=\"_blank\">Sender ID</a>' for more information.","twizo-verification"); ?>
                </td>
                <td>
                    &nbsp;
                </td>
            </form>
        </tr>

        <tr>
            <form action="" method="POST">
            <td>
                <?php _e("Issuer", "twizo-verification"); ?>
            </td>
            <td>
                <input type="text" class="api-input" name="issuer" placeholder="<?php _e("Set your issuer", "twizo-verification"); ?>" value="<?php echo $adminPageController->twizo_getIssuer(); ?>">
            </td>
                <td>&nbsp;</td>
            <td>
                <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
            </td>
            </form>
        </tr>
        <tr>
            <form action="" method="POST">
                <td>
                    &nbsp;
                </td>
                <td>
                    <?php _e("The issuer is the name of your website which will be shown for some of our verification types. See our help topic '<a href=\"https://www.twizo.com/help/what-is-an-issuer/\" style=\"color: #FF952E;\" target=\"_blank\">What is an issuer</a>' for more information.","twizo-verification"); ?>
                </td>
                <td>
                    &nbsp;
                </td>
            </form>
        </tr>

        <tr>
            <form action="" method="POST">
                <td>
                    <?php _e("Logo URL", "twizo-verification"); ?>
                </td>
                <td>
                    <input type="text" class="api-input" name="image_url" placeholder="<?php _e("Set your logo URL", "twizo-verification"); ?>" value="<?php echo $adminPageController->twizo_getImageUrl(); ?>">
                </td>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" class="button-twizo" value="<?php _e("Save","twizo-verification"); ?>">
                </td>
        <tr>
            <form action="" method="POST">
                <td>
                    &nbsp;
                </td>
                <td>
                    <?php _e("Enter a logo URL (HTTPS only) to show your logo in the widget.","twizo-verification"); ?>
                </td>
                <td>
                    &nbsp;
                </td>
            </form>
        </tr>
            </form>
        </tr>
    </table>
</div>

<div class="right-settings">
    <h2><?php _e("Two-factor authentication", "twizo-verification")?></h2>
    <?php 
        require_once(__DIR__ . '../../tfa-settings-body.php');
    ?>
</div>