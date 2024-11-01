<?php
/**
 * User: sordelman, michiel
 * Date: 14/12/2017
 * Time: 10:33
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

//When importing this file make sure all the variables are set in the parent file.
$twizo_widget_cdn = $this->controller->twizo_getTwizoWidgetCdn();

//We are going to use 2 factor authorization. Set the body for the widget.
echo '<body></body>';

//Redirect is refresh on default. Redirect is used in loginWidget.php
$redirect = "refresh";
$image_url = "";
if (!empty($this->controller->twizo_getDatabaseHelper()->twizo_getImageUrl())) {
    $image_url = $this->controller->twizo_getDatabaseHelper()->twizo_getImageUrl();
}

?>
<script src="<?php echo $twizo_widget_cdn; ?>"></script>
<script>
    const handler = TwizoWidget.configure({
        askTrusted: true,
        trustedDays: 30,
        logoUrl: "<?php echo htmlspecialchars($image_url); ?>",
        sessionToken: "<?php echo htmlspecialchars($twizoFormData['session']['token']); ?>"
    });

    handler.open(function (sessionToken, isError, errorCode, isTrusted) {
        if (isError) {
            //verification failed, user should not be logged in.
            location.reload();
        } else {
            //verification success, user can continue to login.
            //Send a from with all the information needed, this form is used to login.

	        // we try very hard to "reuse" the original wp-login form, to prevent application
            // intrusion from going off, yes some do this.
	        document.body.insertAdjacentHTML(
    		    'beforeend',
	    	    '<form action="<?php echo esc_url($twizoFormData['url']); ?>" method="POST" id="twizo_2fa_form" hidden>' +
                	'<input type="hidden" name="log" value="<?php echo htmlspecialchars($twizoFormData['username']); ?>"/>' +
                	'<input type="hidden" name="pwd" value="<?php echo htmlspecialchars($twizoFormData['session']['nonce']); ?>"/>' +
                	'<input type="hidden" name="rememberme" value="' + isTrusted.isTrusted + '"/>' +
			        '<input type="hidden" name="wp-submit" value="Log In"/>' +
			        '<input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($twizoFormData['session']['redirect_to']); ?> "/>' +	
                '</form>'
            );
	        document.forms.twizo_2fa_form.submit();
        }
    });
</script>
