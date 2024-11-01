<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 21/12/2017
 * Time: 14:07
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_AdminPageController
{
    /**
     * @var twizo_Controller
     */
    private $controller;

    /**
     * if the alert is hidden
     * @var string
     */
    private $hidden = "hidden";

    /**
     * The alert message
     * @var string
     */
    private $message = "";

    /**
     * The alert css class
     * @var string
     */
    private $alert_class = "success";

    /**
     * The results of the database
     * @var array|null|object
     */
    private $results;

    /**
     * The API key for the input box
     * @var string
     */
    private $api_key = "";

    /**
     * The current selected host
     * @var string
     */
    private $host_current = "";

    /**
     * The set or selected preferred type
     * @var string
     */
    private $selected_type = "";

    /**
     * The page title
     * @var string
     */
    private $title = "";

    /**
     * The sender
     * @var string
     */
    private $sender;

    /**
     * The issuer
     * @var string
     */
    private $issuer;

    /**
     * The issuer
     * @var string
     */
    private $image_url;

    /**
     * The current setup step
     * @var int
     */
    private $step;

    /**
     * If the select box is hidden or not
     * @var string
     */
    private $hidden_host = "";

    /**
     * The text for a button
     * @var string
     */
    private $button_text = "";

    /**
     * If the input is disabled
     * @var string
     */
    private $disabled_input = "";

    /**
     * If the setup is running
     * @var bool
     */
    private $inSetup = false;

    public function __construct()
    {
        global $controller;
        $this->controller = $controller;
        $this->results = $this->controller->twizo_getDatabaseHelper()->twizo_getSettings();

        //Set the page title
        $this->title = __('Twizo - Settings', 'twizo-verification');
        if (count($this->results) == 0 || empty($this->results[0]->api) || empty($this->results[0]->host) || empty($this->results[0]->sender) || empty($this->results[0]->issuer) || (isset($_POST['adminStep']) && isset($_POST['sessionToken']))) {
            $this->title = __('Twizo - Setup', 'twizo-verification');
            $this->inSetup = true;
        }
        
        if(isset($_POST['adminStep'])){
            
            $this->step = intval($_POST['adminStep']);
        }

        //The text for the form button when setup step is 1.
        if ($this->step == 1) {
            $this->button_text = __("Save", "twizo-verification");
        }

        //The text for the form button when setup step is 2.
        if ($this->step == 2) {
            $this->button_text = __("Verify API key", "twizo-verification");
        }

        //The text for the form button when setup step is 3.
        if ($this->step == 3) {
            $this->button_text = __("Save", "twizo-verification");
        }

        //Only possible if there are settings registered
        if (count($this->results) > 0) {
            //Set the api key to the variable.
            $this->api_key = $this->results[0]->api;
            //Set the host to the variable.
            $this->host_current = $this->results[0]->host;
            //Set the selected_type to the variable.
            $this->selected_type = $this->results[0]->preferred_type;
            //Set the sender to the variable.
            $this->sender = $this->results[0]->sender;
            //Set the issuer to the variable.
            $this->issuer = $this->results[0]->issuer;
            //Set the image_url to the variable.
            $this->image_url = $this->results[0]->img_url;
            //Check all the post variables.
        }

        //If there is an post, parse the post variables
        if (isset($_POST)) {
            $this->twizo_getPOST();
        }
    }

    function debug_to_console( $data ) {
        $output = $data;
        if ( is_array( $output ) )
            $output = implode( ',', $output);
    
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
    }
    
    

    /**
     * Checks for all the possible post variables.
     */
    private function twizo_getPOST()
    {
        //If it is a post with a host, save the host.
        if (isset($_POST['host'])) {
            $this->controller->twizo_getDatabaseHelper()->twizo_setTwizoHost($_POST['host']);
            //Set the posted host to the variable.
            $this->host_current = sanitize_text_field($_POST['host']);
            if ($this->step == 0 || $this->step == 4) {
                //Set the alert
                $this->twizo_setMessage(__("The host is updated.", "twizo-verification"), "success");
            } else if ($this->step == 1) {
                $this->step = 2;
                $this->hidden_host = "hidden";
                $this->button_text = __("Next step", "twizo-verification");
                $this->twizo_setMessage(__("The host have been set, proceed to the next step.", "twizo-verification"), "success");
            }
        }

        //If it is an post with an api key, save the api key after verification
        if (isset($_POST['api_key'])) {
            //Set the api key to the variable
            $this->api_key = sanitize_text_field($_POST['api_key']);

            if($this->api_key == '') {
                $this->twizo_setMessage(__("Please enter an API key", "twizo-verification"), "alert");
            } else {
                try {
                    //Verify the key
                    $twizo = Twizo\Api\Twizo::getInstance($this->api_key, $this->results[0]->host);
                    $twizo->verifyCredentials();
                    //Update the key
                    $this->controller->twizo_getDatabaseHelper()->twizo_setAPIkey($this->api_key);
                    if ($this->step == 0 || $this->step == 4) {
                        //Set the alert
                        $this->twizo_setMessage(__("API key is verified and updated.", "twizo-verification"), "success");
                    } else if ($this->step == 2) {
                        $this->step = 3;
                        $this->button_text = __("Next step", "twizo-verification");
                        $this->twizo_setMessage(__("API key is verified, proceed to the next step", "twizo-verification"), "success");
                        $this->disabled_input = "disabled";
                    }
                } catch (Twizo\Api\Entity\Exception $e) {
                    //Catch the exception and give the error message.
                    $this->twizo_setMessage($e->getMessage(), "alert");
                }
            }
        }

        //If it is an post with a preferred_type save the preferred type.
        if (isset($_POST['preferred_type'])) {
            //Set the alert
            $this->twizo_setMessage(__("The default preferred type is updated.", "twizo-verification"), "success");
            //Set the api key to the variable
            $this->selected_type = sanitize_text_field($_POST['preferred_type']);
            if($this->selected_type == 'default') {
                $this->selected_type = null;
            }
            $this->controller->twizo_getDatabaseHelper()->twizo_setAdminPreferredType($this->selected_type);
        }

        $step3ValidationError = false;

        //If it is an post with a sender, save the sender
        if (isset($_POST['sender'])) {
            //Set the sender to the variable
            $this->sender = sanitize_text_field($_POST['sender']);

            if ($this->sender == '') {
                $step3ValidationError = true;
                $this->twizo_setMessage(__("Please enter a Sender", "twizo-verification"), "alert");
            } elseif (preg_match("/^\d+$/", $this->sender) && strlen($this->sender) > 17) {
                $step3ValidationError = true;
                $this->twizo_setMessage(__("The maximum length of a numeric sender is 17 digits", "twizo-verification"), "alert");
            } elseif(strlen($this->sender) > 11) {
                $step3ValidationError = true;
                $this->twizo_setMessage(__("The maximum length of a alphanumeric sender is 11 characters", "twizo-verification"), "alert");
            } else {
                if ($this->step == 0 || $this->step == 4) {
                    $this->controller->twizo_getDatabaseHelper()->twizo_setSender($this->sender);
                    $this->twizo_setMessage(__("The sender is updated.", "twizo-verification"), "success");
                }
            }
        }

        //If it is an post with a issuer, save the issuer
        if(!$step3ValidationError && isset($_POST['issuer'])) {
            //Set the api key to the variable
            $this->issuer = sanitize_text_field($_POST['issuer']);

            if($this->issuer == '') {
                $step3ValidationError = true;
                $this->twizo_setMessage(__("Please enter an Issuer", "twizo-verification"), "alert");
            } elseif(strlen($this->issuer) > 64) {
                $step3ValidationError = true;
                $this->twizo_setMessage(__("The maximum length of the Issuer is 64 characters", "twizo-verification"), "alert");
            } else {
                if ($this->step == 0 || $this->step == 4) {
                    $this->controller->twizo_getDatabaseHelper()->twizo_setIssuer($this->issuer);
                    $this->twizo_setMessage(__("The issuer is updated.", "twizo-verification"), "success");
                }
            }
        }

        //save sender and issuer
        if(!$step3ValidationError && isset($_POST['sender']) && isset($_POST['issuer']) && $this->step == 3) {
            $this->controller->twizo_getDatabaseHelper()->twizo_setSender($this->sender);
            $this->controller->twizo_getDatabaseHelper()->twizo_setIssuer($this->issuer);
            $this->twizo_setMessage(__("The Sender and Issuer are updated.", "twizo-verification"), "success");
            $this->button_text = __("Finish", "twizo-verification");
            $this->disabled_input = "disabled";

        }

        //Set the image url
        if (isset($_POST['image_url'])) {
            $this->image_url = sanitize_text_field($_POST['image_url']);

            if(substr($this->image_url, 0, 8) != 'https://') {
                $this->twizo_setMessage(__("The logo URL must be a HTTPS URL", "twizo-verification"), "alert");
            } else {
                //Set the image_url
                $this->controller->twizo_getDatabaseHelper()->twizo_setImageUrl($this->image_url);
                $this->twizo_setMessage(__("The logo URL is updated.", "twizo-verification"), "success");
            }
        }
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
     * @return bool
     */
    public function twizo_isInSetup()
    {
        return $this->inSetup;
    }

    /**
     * @return twizo_Controller
     */
    public function twizo_getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function twizo_getTitle()
    {
        return $this->title;
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
    public function twizo_getAlertClass()
    {
        return $this->alert_class;
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
     * @return string
     */
    public function twizo_getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @return int
     */
    public function twizo_getStep()
    {
        return $this->step;
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
    public function twizo_getDisabledInput()
    {
        return $this->disabled_input;
    }

    /**
     * @return string
     */
    public function twizo_getHiddenHost()
    {
        return $this->hidden_host;
    }

    /**
     * @return string
     */
    public function twizo_getHostCurrent()
    {
        return $this->host_current;
    }

    /**
     * @return string
     */
    public function twizo_getSelectedType()
    {
        return $this->selected_type;
    }

    /**
     * @return mixed
     */
    public function twizo_getSender()
    {
        return $this->sender;
    }

    /**
     * @return mixed
     */
    public function twizo_getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @return mixed
     */
    public function twizo_getImageUrl()
    {
        return $this->image_url;
    }

}