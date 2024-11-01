<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 12/12/2017
 * Time: 15:06
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $controller;
$twizo_widget_cdn = $controller->twizo_getTwizoWidgetCdn();
require_once(__DIR__ . '/../controllers/twizo_TFASettingsController.php');
$settings = new twizo_TFASettingsController();
?>
<head>
    <?php
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
           echo '<link rel="stylesheet" type="text/css" href="' .  plugin_dir_url(__FILE__) . '/css/tfa-page.css' . '"';
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' .  plugin_dir_url(__FILE__) . '/css/admin-page.css' . '"';
        }
    ?>
</head>
<body>

<!-- Load in libraries -->
<script src="<?php echo $twizo_widget_cdn; ?>"></script>
<!-- End of load in libraries -->


<!-- Standard top information -->
<?php require_once(__DIR__ . '/tfa-pages/tfa-header.php'); ?>
<!-- End of standard top information -->

<!-- Settings page -->
<?php
if ($settings->twizo_getStep() === 0) {
    require_once(__DIR__ . '/tfa-pages/tfa-settings-page.php');
}
?>
<!-- End of settings page -->

<!-- SETUP -->
<!-- First setup step -->
<?php
if ($settings->twizo_getStep() === 1) {
    require_once(__DIR__ . '/tfa-pages/tfa-setup-step-1.php');
}
?>
<!-- End of first setup step -->

<!-- Second setup step -->
<?php
if ($settings->twizo_getStep() === 2) {
    require_once(__DIR__ . '/tfa-pages/tfa-setup-step-2.php');
}
?>
<!-- End of second setup step -->
<!-- End of setup -->

<!-- Load in javascript when necessary -->
<?php if ($settings->twizo_needsToLoadInWidget()) { ?>
    <script src="<?php echo plugin_dir_url(__FILE__) ?>/scripts/widget-settings.js"></script>
<?php } ?>

<!-- End of load in javascript -->

</body>