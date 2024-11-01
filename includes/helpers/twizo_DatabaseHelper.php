<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 18/12/2017
 * Time: 10:01
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_DatabaseHelper
{

    /**
     * The wordpress database
     * @var object
     */
    private $wpdb;

    /**
     * The table name for the user table.
     * @var string
     */
    private $table_name_users;

    /**
     * The table name for the settings table.
     * @var string
     */
    private $table_name_settings;


    /**
     * The table name for the trusted devices table.
     * @var string
     */
    private $table_name_trusted;

    /**
     * twizoDatabaseHelper constructor.
     * @param $wpdb wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
        $this->table_name_users = $wpdb->prefix . 'twizo_users';
        $this->table_name_settings = $wpdb->prefix . 'twizo_settings';
        $this->table_name_trusted = $wpdb->prefix . 'twizo_trusted_devices';
    }

    /**
     * Function to return the user results
     * @param $user WP_USER
     * @return array|null|object
     */
    public function twizo_getUserResult($user)
    {
        $query = $this->wpdb->prepare(
            'SELECT * FROM ' . $this->table_name_users . ' WHERE user_id = %d',
            $user->ID
        );
        
        return $this->wpdb->get_results($query, OBJECT);
    }

    /**
     * Get user settings
     * @return array|null|object
     */
    public function twizo_getSettings()
    { 
        return $this->wpdb->get_results('SELECT * FROM ' . $this->table_name_settings, OBJECT);
    }

    /**
     * Set the host
     * @param $host
     */
    public function twizo_setTwizoHost($host)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('host' => $host));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('host' => $host), array('id' => $results[0]->id));
        }
    }

    /**
     * Set the preferred type
     * @param $type
     */
    public function twizo_setAdminPreferredType($type)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('preferred_type' => $type));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('preferred_type' => $type), array('id' => $results[0]->id));
        }
    }

    /**
     * Get the preferred type
     */
    public function twizo_getAdminPreferredType()
    {
        return $this->twizo_getSettings()[0]->preferred_type;
    }

    /**
     * Set the image url
     * @param $url
     */
    public function twizo_setImageUrl($url)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('img_url' => $url));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('img_url' => $url), array('id' => $results[0]->id));
        }
    }

    /**
     * Get the image url
     */
    public function twizo_getImageUrl()
    {
        return $this->twizo_getSettings()[0]->img_url;
    }

    /**
     * Set the issuer
     * @param $issuer
     */
    public function twizo_setIssuer($issuer)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('issuer' => $issuer));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('issuer' => $issuer), array('id' => $results[0]->id));
        }
    }

    /**
     * Get the issuer
     */
    public function twizo_getIssuer()
    {
        return $this->twizo_getSettings()[0]->issuer;
    }

    /**
     * Set the sender
     * @param $sender
     */
    public function twizo_setSender($sender)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('sender' => $sender));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('sender' => $sender), array('id' => $results[0]->id));
        }
    }

    /**
     * Get the sender
     */
    public function twizo_getSender()
    {
        return $this->twizo_getSettings()[0]->sender;
    }

    /**
     * Set the api key
     * @param $key
     */
    public function twizo_setAPIkey($key)
    {
        $results = $this->twizo_getSettings();
        if (count($results) == 0) {
            $this->wpdb->insert($this->twizo_getTableNameSettings(), array('api' => $key));
        } else {
            $this->wpdb->update($this->twizo_getTableNameSettings(), array('api' => $key), array('id' => $results[0]->id));
        }
    }

    /**
     * Set preferred type to the user
     * @param $user WP_USER
     * @param $type
     */
    public function twizo_setPreferredType($user, $type)
    {
        $this->wpdb->update($this->twizo_getTableNameUsers(), array('preferred_type' => $type), array('user_id' => $user->ID));
    }

    /**
     * @return string
     */
    public function twizo_getTableNameUsers()
    {
        return $this->table_name_users;
    }

    /**
     * @return string
     */
    public function twizo_getTableNameSettings()
    {
        return $this->table_name_settings;
    }

    /**
     * @return string
     */
    public function twizo_getTableNameTrusted()
    {
        return $this->table_name_trusted;
    }

    /**
     * Check if the user has 2FA enabled
     * @param $user WP_USER
     * @return bool
     */
    public function twizo_is2FA($user)
    {
        $results = $this->twizo_getUserResult($user);
        if (count($results) > 0) {
            if (($results[0]->enabled_2fa) && !empty($results[0]->phone_number) && !empty($results[0]->country_number)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get if a trusted device exists or not
     * @param $user
     * @param $cookie
     * @return mixed
     */
    public function twizo_getTrustedDevice($user, $cookie){
        $query = $this->wpdb->prepare(
            'SELECT * FROM ' . $this->twizo_getTableNameTrusted() . ' WHERE user_id = %d AND hash = %s',
            $user->ID,
            $cookie
        );
        
        return $this->wpdb->get_results($query, OBJECT);
    }

    /**
     * Set a Trusted device for a user.
     * @param $user
     * @param $cookie_hash
     */
    public function twizo_setTrustedDevice($user, $cookie_hash){
        $this->wpdb->insert($this->twizo_getTableNameTrusted(), array('user_id' => $user->ID, 'hash' => $cookie_hash));
    }

    /**
     * Set the 2FA authentication on or off for a user
     * @param $user
     * @param $is2FA boolean
     */
    public function twizo_set2FA($user, $is2FA){
        $this->wpdb->update($this->twizo_getTableNameUsers(), array('enabled_2fa' => $is2FA), array('user_id' => $user->ID));
    }

    /**
     * Set the 2FA authentication on or off for a user with phone and country number
     * @param $user
     * @param $is2FA boolean
     */
    public function twizo_set2FAPhoneNumber($user, $is2FA, $country_number, $phone_number)
    {
        $this->wpdb->update($this->twizo_getTableNameUsers(), array('enabled_2fa' => $is2FA, 'country_number' => $country_number, 'phone_number' => $phone_number), array('user_id' => $user->ID));
    }

    /**
     * Creates a user in the Twizo Verification plugin database
     * @param $user
     * @param $is2FA
     */
    public function twizo_createUser($user, $is2FA)
    {
        $this->wpdb->insert($this->twizo_getTableNameUsers(), array('enabled_2fa' => $is2FA, 'user_id' => $user->ID));
    }
}