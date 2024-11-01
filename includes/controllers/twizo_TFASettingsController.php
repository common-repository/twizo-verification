<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 11/12/2017
 * Time: 16:44
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_TFASettingsController
{

    //Controllers

    /**
     * The main controller.
     * @var twizo_Controller
     */
    private $controller;

    /**
     * The Twizo API interface
     * @var \Twizo\Api\TwizoInterface
     */
    private $twizo;

    /**
     * The wordpress database
     * @var object
     */
    private $wpdb;

    //POST variables

    /**
     * phone number from post variables.
     * @var string
     */
    private $phone_number = "";

    /**
     * country number from post variables.
     * @var string
     */
    private $country_number = "";

    /**
     * If it is the first time opening 2fa settings.
     * @var bool
     */
    private $first_time = false;

    /**
     * The step, which will define which page is shown.
     * @var int
     */
    private $step = 1;

    /**
     * The current user.
     * @var WP_USER
     */
    private $current_user;

    /**
     * The url to the image.
     * @var string
     */
    private $image_url = "";

    /**
     * The results from the database.
     * @var array|null|object
     */
    private $results;

    /**
     * The message to show the users.
     * @var string
     */
    private $message = "";

    /**
     * The sessionToken.
     * @var string
     */
    private $sessionToken = "";

    /**
     * The registrationSessionToken.
     * @var string
     */
    private $registrationSessionToken = "";

    /**
     * The text in the button.
     * @var string
     */
    private $button_text = "";

    /**
     * The alert css class
     * @var string
     */
    private $alert_class = "success";

    /**
     * if the alert is hidden
     * @var string
     */
    private $hidden = "hidden";

    /**
     * TFASettings constructor.
     */
    public function __construct()
    {
        //Get the globals
        global $controller;
        $this->controller = $controller;
        $this->twizo = $controller->twizo_getTwizo();
        $this->wpdb = $controller->twizo_getWpdb();

        //Get the current user.
        $this->current_user = wp_get_current_user();

        //Get the image url for the widget
        if (!empty($controller->twizo_getDatabaseHelper()->twizo_getImageUrl())) {
            $this->image_url = $controller->twizo_getDatabaseHelper()->twizo_getImageUrl();
        }

        //Get the record if there is any.
        $this->results = $controller->twizo_getDatabaseHelper()->twizo_getUserResult($this->current_user);

        //If not registered, register user
        if (count($this->results) == 0) {
            $controller->twizo_getDatabaseHelper()->twizo_createUser($this->current_user, false);
        };

        if (isset($_POST)) {
            $error = $this->twizo_getPOST();

            if(!$error) {
                //Check to verify the phone number.
                $this->twizo_verifyPhoneNumber();
            }
        }

        //Refreshes the results and checks for statuses.
        $this->twizo_refreshResultsCheckUpdates();
    }

    /**
     * Checks for all the possible post values on page load.
     */
    public function twizo_getPOST()
    {
        $error = false;

        //Set the country number if it is given
        if (isset($_POST['country_number'])) {
            $this->country_number = sanitize_text_field($_POST['country_number']);

            if($this->country_number == '') {
                $error = true;
                $this->twizo_setMessage(__("Please select a country", "twizo-verification"), "alert");
            }
        }

        //Set the phone number if it is given
        if (isset($_POST['phone_number'])) {
            $this->phone_number =  sanitize_text_field($_POST['phone_number']);
            $this->phone_number = ltrim($this->phone_number, '0');

            if($this->phone_number == '') {
                $error = true;
                $this->twizo_setMessage(__("Please enter a phone number", "twizo-verification"), "alert");
            } elseif(preg_match("/^\d+$/", $this->phone_number) == false) {
                $error = true;
                $this->twizo_setMessage(__("Please enter a valid phone number", "twizo-verification"), "alert");
            } elseif((strlen($this->phone_number) + strlen($this->country_number)) < 8) {
                $error = true;
                $this->twizo_setMessage(__("The country code and phone number must be at least 8 digits", "twizo-verification"), "alert");
            } elseif((strlen($this->phone_number) + strlen($this->country_number)) > 17) {
                $error = true;
                $this->twizo_setMessage(__("The country code and phone number cannot be more than 17 digits", "twizo-verification"), "alert");
            }
        }

        //Check if this is the first time someone enables, and needs to set up the two factor authorization.
        if (isset($_POST['first_time'])) {
            if ($_POST['first_time'] == 'true') {
                $this->first_time = true;
            }
        }

        //Set the set-up step.
        if (isset($_POST['step2FA'])) {
            $this->step = sanitize_text_field($_POST['step2FA']);
        }

        //Start registration session
        if (isset($_POST['register'])) {
            $this->registrationSessionToken = $this->twizo_createRegistrationSession();
        }

        //Set the preffered_type
        if (isset($_POST['preferred_type'])) {
            $selectedType = sanitize_text_field($_POST['preferred_type']);
            if($selectedType == 'default') {
                $selectedType = null;
            }

            $this->controller->twizo_getDatabaseHelper()->twizo_setPreferredType($this->current_user, $selectedType);
        }

        //Change 2FA
        //Check if the url contains information to enable or disable 2fa
        if (isset($_POST['enable'])) {
            $this->twizo_change2FA($_POST['enable']);
        }

        return $error;
    }

    /**
     * Create Registration Session
     */
    private function twizo_createRegistrationSession()
    {
        try {
            $widgetRegisterSession = $this->twizo->createWidgetRegisterSession(
                null,
                $this->results[0]->country_number . $this->results[0]->phone_number,
                $this->current_user->user_email,
                $this->current_user->user_email,
                $this->controller->twizo_getDatabaseHelper()->twizo_getIssuer()
            );

            $widgetRegisterSession->create();

            return $widgetRegisterSession->getSessionToken();
        } catch (Twizo\Api\Entity\Exception $e) {
            _e('Could not start registration session'.$e->getMessage(), "twizo-verification");
            return;
        }
    }

    /**
     * Changes if the user has 2FA enabled
     * @param $enabled
     */
    private function twizo_change2FA($enabled)
    {
        if ($enabled == "Enable") {
            //Enable 2fa
            if (count($this->results) > 0 && !empty($this->results[0]->phone_number)) {
                $this->controller->twizo_getDatabaseHelper()->twizo_set2FA($this->current_user, true);
            }
        } elseif ($enabled == "Disable") {
            //Disable 2fa
            $this->controller->twizo_getDatabaseHelper()->twizo_set2FA($this->current_user, false);
        }
    }

    /**
     * Verifies a phone number of an user.
     */
    private function twizo_verifyPhoneNumber()
    {
        //If there is a call with a phone number and country number.
        if (!empty($this->phone_number) && !empty($this->country_number)) {
            $verified = false;
            //And there is an sessionToken.
            if (isset($_POST['sessionToken'])) {
                //Verify the sessionToken.
                try {
                    $rest = $this->twizo->getWidgetSession(sanitize_text_field($_POST['sessionToken']), $this->country_number . $this->phone_number, $this->current_user->user_email, $this->current_user->user_email);

                    if ($rest->getStatus() == 'success') {
                        //Sessiontoken verified, enable 2fa and go to next step.
                        $this->controller->twizo_getDatabaseHelper()->twizo_set2FAPhoneNumber($this->current_user, true, $this->country_number, $this->phone_number);
                        $verified = true;
                        $this->step = 2;
                    }
                } catch (Twizo\Api\Entity\Exception $e) {
                    _e('Error sessionTokens did not match', "twizo-verification");
                    return;
                }
            }
            if (!$verified) {
                //Not verified, create sessionToken for widget to open.
                $this->sessionToken = $this->controller->twizo_getVerifyNumberSession($this->results[0], $this->country_number, $this->phone_number);
            }
        }
    }

    /**
     * Refreshes the results and checks for status updates and final calls.
     */
    private function twizo_refreshResultsCheckUpdates()
    {
        //Refresh results after all changes
        $this->results = $this->controller->twizo_getDatabaseHelper()->twizo_getUserResult($this->current_user);

        //Check if there is a result
        if (count($this->results) == 0) {
            _e('Error occurred, could not create record for user', "twizo-verification");
        }

        //If this is the third step, finish set-up
        if ($this->step == 3) {
            $this->first_time = false;
        }

        //If there is no phone number set, run set-up
        if (empty($this->results[0]->phone_number)) {
            $this->first_time = true;
        }

        //Set the text for the button
        $this->button_text = ($this->results[0]->enabled_2fa) ? "Disable" : "Enable";
    }

    /**
     * This function will return the step you are in when opening the page
     * 0 is no setup
     * 1 and 2 are the setup steps
     * @return int
     */
    public function twizo_getStep()
    {
        if ($this->results[0]->enabled_2fa && !$this->first_time) {
            return 0;
        } else if (isset($_POST['enable']) && $_POST['enable'] == "Enable" && $this->first_time && $this->step == 1) {
            return 1;
        } else if (isset($_POST['enable']) && $_POST['enable'] == "Enable" && $this->first_time && $this->step == 2) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * Defines if the widget needs to be loaded in.
     * @return bool
     */
    public function twizo_needsToLoadInWidget()
    {
        if ($this->results[0]->enabled_2fa || (isset($_POST['enable']) && $_POST['enable'] == "Enable")) {
            if ($this->first_time) {
                if ($this->step == 1) {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }
    /**
     * Set's the message
     * @param $message string
     * @param $class string
     */
    private function twizo_setMessage($message, $class)
    {
        $this->hidden = "";
        $this->message = $message;
        $this->alert_class = $class;
    }

    /**
     * @return string
     */
    public function twizo_getButtonText()
    {
        return $this->button_text;
    }

    /**
     * @return string
     */
    public function twizo_getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function twizo_getHidden()
    {
        return $this->hidden;
    }

    /**
     * @return array|null|object
     */
    public function twizo_getResults()
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function twizo_getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @return string
     */
    public function twizo_getCountryNumber()
    {
        return $this->country_number;
    }

    /**
     * @return string
     */
    public function twizo_getSessionToken()
    {
        return $this->sessionToken;
    }

    /**
     * @return string
     */
    public function twizo_getRegistrationSessionToken()
    {
        return $this->registrationSessionToken;
    }

    /**
     * @return string
     */
    public function twizo_getAlertClass()
    {
        return $this->alert_class;
    }

    /**
     * @return string
     */
    public function twizo_getImageUrl()
    {
        return $this->image_url;
    }
}

