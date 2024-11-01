jQuery(document).ready(function () {
    //open register widget when registerSessionToken is set
    if(jQuery('#registrationSessionToken').val() !== undefined && jQuery('#registrationSessionToken').val() != '') {
        const handler = TwizoRegisterWidget.configure({
            sessionToken: jQuery('#registrationSessionToken').val(),
            logoUrl: jQuery('#image_url').val()
        });

        handler.open(function (sessionToken, isError, errorCode, registeredTypes) {
            if(isError) {
            }
        });

    }

    if (jQuery('#country_number').val().length > 0) {
        jQuery('#country_select').val(jQuery('#country_number').val());
    }

    var sessionToken = jQuery('#sessionToken').val();
    var country = jQuery('#country_number').val();
    var image_url = jQuery('#image_url').val();
    var phone = jQuery('#phone_number').val();
    var first_time = jQuery('#first_time').val();

    if (sessionToken !== undefined && sessionToken !== "") {
        const handler = TwizoWidget.configure({
            sessionToken: sessionToken,
            logoUrl: image_url,
            recipient : country + phone
        });

        handler.open(function (sessionToken, isError, errorCode) {
            if (isError) {
                //verification failed, user should not be logged in
                // jQuery('#verify').val('Error, try again.');
            } else {
                jQuery('body').append('<form action="" method="POST" id="formTwizo" hidden>' +
                    '<input type="hidden" name="sessionToken" value="' + sessionToken + '"/>' +
                    '<input type="hidden" name="enable" value="Enable"/>' +
                    '<input type="hidden" name="phone_number" value="' + phone + '"/>' +
                    '<input type="hidden" name="country_number" value="' + country + '"/>' +
                    '<input type="hidden" name="first_time" value="' + first_time + '"/>' +
                    '<input type="hidden" name="step2FA" value="1"/>' +
                    '</form>');
                jQuery('#formTwizo').submit();
            }
        });
    }
});


