<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 12/12/2017
 * Time: 16:31
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
//Get the controller
require_once(__DIR__ . '/../controllers/twizo_AdminPageController.php');
$adminPageController = new twizo_AdminPageController();
?>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo plugin_dir_url(__FILE__) ?>/css/admin-page.css">
</head>
<body>
<div id="header">
    <img class="logo" id="header-logo" src="<?php echo plugin_dir_url($adminPageController->twizo_getController()->twizo_getTwizoPluginRoot()) ?>/img/twizo.png">
    <div id="header-text"><h1>Twizo</h1></div>
</div>
<div id="page-title"><h1><?php echo $adminPageController->twizo_getTitle(); ?></h1></div>
<div id="box">
    <?php
    $results = $adminPageController->twizo_getResults();
    if($adminPageController->twizo_isInSetup()) {
        if (isset($_POST['adminStep'])) {
            switch ($_POST['adminStep']) {
                case 1:
                    require_once(__DIR__ . '/admin-pages/setup-step-1-host.php');
                    break;
                case 2:
                    require_once(__DIR__ . '/admin-pages/setup-step-2-api-key.php');
                    break;
                case 3:
                    require_once(__DIR__ . '/admin-pages/setup-step-3-sender-issuer.php');
                    break;
                default:
                    ?>
                    <h3> Something went wrong. Please try again.</h3>
                    <?php
                    break;
            }
        } else {
            require_once(__DIR__ . '/admin-pages/welcome-setup-page.php');
        }
    }else{
        require_once(__DIR__ . '/admin-pages/admin-settings-page-body.php');
    }
    ?>
</div>
</body>

