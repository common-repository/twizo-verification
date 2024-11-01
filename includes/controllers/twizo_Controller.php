<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 14/12/2017
 * Time: 09:55
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_Controller
{

    /**
     * The wordpress database.
     * @var object
     */
    private $wpdb;

    /**
     * The Twizo API Interface.
     * @var \Twizo\Api\TwizoInterface
     */
    private $twizo;

    /**
     * The CDN of the Twizo widget.
     * @var string
     */
    private $twizo_widget_cdn;

    /**
     * The plugin root directory.
     * @var string
     */
    private $twizo_plugin_root;

    /**
     * The host to the information.json.
     * @var string
     */
    private $twizo_host;

    /**
     * The tag given with creating a session.
     * @var string
     */
    private $tag;

    /**
     * The url to the plugin directory.
     * @var string
     */
    private $plugin_dir_url;

    /**
     * The TwizoHelper.
     * @var twizo_TwizoHelper
     */
    private $twizo_helper;

    /**
     * The twizoDatabaseHelper.
     * @var twizo_DatabaseHelper
     */
    private $database_helper;

    public function __construct($dir_plugin)
    {
        $this->twizo_widget_cdn = "https://cdn.twizo.com/widget.js";
        $this->twizo_plugin_root = dirname(__FILE__);
        $this->twizo_host = "https://cdn.twizo.com/information.json";
        global $wpdb;
        $this->wpdb = $wpdb;

        //Set the database helper
        include_once(dirname(__FILE__) . '/../helpers/twizo_DatabaseHelper.php');
        $this->database_helper = new twizo_DatabaseHelper($wpdb);

        //Get the settings.
        $results = $this->database_helper->twizo_getSettings();
        //If api key and host is installed.
        if (count($results) > 0 && !empty($results[0]->api) && !empty($results[0]->host) && !empty($results[0]->sender) && !empty($results[0]->issuer)) {
            $this->twizo = Twizo\Api\Twizo::getInstance($results[0]->api, $results[0]->host);
            //Set the Twizo helper
            include_once(dirname(__FILE__) . '/../helpers/twizo_TwizoHelper.php');
            $this->twizo_helper = new twizo_TwizoHelper($this->twizo, $this);
        }

        $this->plugin_dir_url = $dir_plugin;
        $this->tag = "WooCommerce";
    }

    /**
     * Get twizo session by twizo user
     * @param $user
     * @param $userTwizo
     * @param $isLogin bool
     * @return string json string with sessionToken
     */
    public function twizo_getTwizoSession($user,$userTwizo, $isLogin)
    {
        //Get the twizo session and return json
        if (!empty($userTwizo->country_number) && !empty($userTwizo->phone_number) && !empty($userTwizo->user_id)) {

            try {
                $array = $this->twizo_helper->twizo_getAllowedTypes($isLogin);
                $widgetSession = $this->twizo->createWidgetSession(array_keys($array));
                $widgetSession->setRecipient($userTwizo->country_number . $userTwizo->phone_number);
                $widgetSession->setSender($this->database_helper->twizo_getSender());

                $widgetSession->setBackupCodeIdentifier($user->user_email);
                $widgetSession->setTotpIdentifier($user->user_email);
                $widgetSession->setTag($this->tag);
                $widgetSession->setIssuer($this->database_helper->twizo_getIssuer());

                if (!empty($userTwizo->preferred_type)) {
                    $widgetSession->setPreferredType($userTwizo->preferred_type);
                } else if (!empty($this->database_helper->twizo_getAdminPreferredType())) {
                    $widgetSession->setPreferredType($this->database_helper->twizo_getAdminPreferredType());
                }
                $widgetSession->create();
                return $widgetSession->getSessionToken();
            } catch (Exception $e) {
                echo '<div style="color:red;">' .
                    $e->getMessage() .
                    '</div>';
            }

        }
        return null;
    }

    /**
     * Get the sessiontoken to verify a number
     * @param $user
     * @param $country_number
     * @param $phone_number
     * @return null|string
     */
    public function twizo_getVerifyNumberSession($user, $country_number, $phone_number)
    {
        try {
            //Create widget session
            $widgetSession = $this->twizo_getTwizo()->createWidgetSession(array_keys($this->twizo_getTwizoHelper()->twizo_getAllowedTypes(false)));

            //Set recipient and sender and tag
            $widgetSession->setRecipient($country_number . $phone_number);
            $widgetSession->setSender($this->twizo_getDatabaseHelper()->twizo_getSender());
            $widgetSession->setTag($this->twizo_getTag());

            //Create the session and return the token
            $widgetSession->create();
            return $widgetSession->getSessionToken();
        } catch (Exception $e) {
            echo '<div style="color:red;">' .
                $e->getMessage() .
                '</div>';
        }
    }

    /**
     * @return string
     */
    public function twizo_getTwizoHost()
    {
        return $this->twizo_host;
    }

    /**
     * @return string
     */
    public function twizo_getTwizoPluginRoot()
    {
        return $this->twizo_plugin_root;
    }

    /**
     * @return string
     */
    public function twizo_getTwizoWidgetCdn()
    {
        return $this->twizo_widget_cdn;
    }

    /**
     * @return \Twizo\Api\TwizoInterface
     */
    public function twizo_getTwizo()
    {
        return $this->twizo;
    }

    /**
     * @return wpdb
     */
    public function twizo_getWpdb()
    {
        return $this->wpdb;
    }

    /**
     * @return string
     */
    public function twizo_getTag()
    {
        return $this->tag;
    }

    /**
     * @return mixed
     */
    public function twizo_getPluginDirUrl()
    {
        return $this->plugin_dir_url;
    }

    /**
     * @return twizo_TwizoHelper
     */
    public function twizo_getTwizoHelper()
    {
        return $this->twizo_helper;
    }

    /**
     * @return twizo_DatabaseHelper
     */
    public function twizo_getDatabaseHelper()
    {
        return $this->database_helper;
    }

    /**
     * Get the hosts
     * @return mixed
     */
    public function twizo_getTwizoHosts()
    {
        $json = json_decode(file_get_contents($this->twizo_getTwizoHost()), true);
        return $json['hosts'];
    }
}