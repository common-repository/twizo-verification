<?php
/**
 * User: michiel
 * Date: 06/12/2017
 * Time: 17:46
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class twizo_TwizoLogin
{

    private $loginController;
    private $controller;
       
    private $credentials = null;
    /**
     * Twizo_login constructor.
     */
    public function __construct()
    {
        global $controller;
        $this->controller = $controller;

        require_once __DIR__ . '/controllers/twizo_LoginController.php';
        $this->loginController = new twizo_LoginController();
        // Hook our login
        add_action('login_form_twizo_validate', array($this, 'twizo_2fa_verify'));
        add_action('wp_login', array($this, 'twizo_wp_login'), 30, 2);
    }

    /**
     * Function to verify the token in the metadata, using data the client sent us.
     */
    public function twizo_2fa_verify()
    {
        $data = array();
        if (!array_key_exists('log', $_POST) || !preg_match('/^id(?<user_id>\d+)$/', $_POST['log'], $data)) {
            // log not set or not valid return
            return;
        }
        if (!array_key_exists('pwd', $_POST) || !strlen($_POST['pwd'])) {
            // pwd not set or empty return
            return;
        }
        $data['nonce'] = $_POST['pwd'];

        // retrieve meta for user
        $meta = get_user_meta($data['user_id'], 'twizoSession', true);
        if (!is_array($meta)) {
            // meta was not set for this user (or user does not exist)
            return;
        }
        if ($meta['nonce'] != $data['nonce']) {
            // nonce does not match 
            return;
        }
        // delete meta here, the user matched the nonce
        delete_user_meta($data['user_id'], 'twizoSession');
        if ($meta['expire'] > time() + 3600) {
            // token we generated has expired
            return;
        }
        $user = get_userdata($data['user_id']);
	
        $recipient = $this->controller->twizo_getDatabaseHelper()->twizo_getUserResult($user);
        if (count($recipient) != 1) {
            // strange no user found or way too many
            return;
        }
        $twizo_user = $recipient[0];
        // ask the twizo api server for a session token.
        $result = $this->controller->twizo_getTwizo()->getWidgetSession(
            $meta['token'],
            $twizo_user->country_number . $twizo_user->phone_number,
            $user->user_email, /* $backupCodeIdentifier */
            $user->user_email  /* $totpIdentifier */
        );
        if ($result->getStatus() == 'success') {
            // all was well .. login in the user
            wp_set_auth_cookie($user->ID, $meta['remember_me']);
            // set trusted device if needed
            if (array_key_exists('rememberme', $_POST) && $_POST['rememberme'] == 'true' ) {
                $this->loginController->twizo_setTrustedDevice($user);
            }
        }

        $redirect_to = apply_filters( 'login_redirect', $meta['redirect_to'], $meta['redirect_to'], $user );
        wp_safe_redirect($redirect_to);
        exit();	
    }

    /**
     * Function fires after the user logs in, it will clear the user authentication if the 
     * user needs to complete 2fa and will send the user to the 2fs screen.
     */
    public function twizo_wp_login($user_login, $user) 
    {
    	if (!$this->controller->twizo_getDatabaseHelper()->twizo_is2FA($user)) {
            // user not enroled in two factor
            return;
        } 
        if ($this->loginController->twizo_isTrustedDevice($user)) {
            // user trusted
            return;
        }
        $recipient = $this->controller->twizo_getDatabaseHelper()->twizo_getUserResult($user);
        $user_twizo = $recipient[0];
        // chear out the authentication wp "just" added
        wp_clear_auth_cookie();
        // save data we need
        $redirect_to = array_key_exists('redirect_to', $_POST) ? $_POST['redirect_to'] : null;
        // we need to be carefull and keep all the query strings we received .. some might have been 
        // injected for firewalling purposes.
        $args = array();
        parse_str(array_key_exists('QUERY_STRING', $_SERVER) ? $_SERVER['QUERY_STRING'] : '', $args);
        $args['action'] = 'twizo_validate';

        $twizoFormData = array(
            'session' => array(
                'token'  => $this->controller->twizo_getTwizoSession($user, $user_twizo, true),
                'nonce'  => wp_create_nonce(),
                'expire' => time() + 3600,
                'remember_me' => array_key_exists('rememberme', $_POST) ? ($_POST['rememberme'] ? true : false) : false,
                'redirect_to' => $redirect_to,
            ),
		    // make sure the "username" is a none numeric string
            'username' => sprintf('id%d', $user->ID),
		    'url' => apply_filters('login_url', add_query_arg($args, network_site_url('wp-login.php', 'login')), $redirect_to),	
        );
        
        // store the data for use in twizo_2fa_verify
        update_user_meta($user->ID, 'twizoSession', $twizoFormData['session']);	        
        require_once($this->controller->twizo_getTwizoPluginRoot() . '/../templates/loginWidget.php');
        exit();
    }
}
