<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 14/12/2017
 * Time: 10:06
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_LoginController
{

    /**
     * The main controller.
     * @var twizo_Controller
     */
    private $controller;

    public function __construct()
    {
        global $controller;
        $this->controller = $controller;
    }

    /**
     * Logs a user in.
     * @param $username
     * @param $password
     */
    public function twizo_twizoLogin($username, $password)
    {
        //Set the credentials
        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $password;
        $creds['remember'] = true;
        //Sign the user in
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            //Wrong user information
            echo $user->get_error_message();
        } else {
            //Redirect the user if a page is set, else refresh.
            if (isset($_POST['redirect_to'])) {
                if ($_POST['redirect_to'] != 'refresh') {
                    wp_redirect($_POST['redirect_to']);
                    exit;
                }
            }
            header("Refresh:0");

        }
    }

    /**
     * Checks if the device is trusted based on the user.
     * @param $user
     * @return bool
     */
    public function twizo_isTrustedDevice($user)
    {
        if (isset($_COOKIE['TwizoVerification'])) {
            $cookie = $_COOKIE['TwizoVerification'];
            $results = $this->controller->twizo_getDatabaseHelper()->twizo_getTrustedDevice($user, $cookie);
            if (count($results) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set's a cookie to have the device trusted for a certain user.
     * @param $user
     */
    public function twizo_setTrustedDevice($user)
    {
        $recipient = $user->country_number . $user->phone_number;
        $cookie_hash = hash('ripemd160', $recipient . bin2hex(random_bytes(20)));
        $this->controller->twizo_getDatabaseHelper()->twizo_setTrustedDevice($user,$cookie_hash);
        setcookie("TwizoVerification", $cookie_hash, strtotime('30 days'));
    }
}