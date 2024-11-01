<?php
/**
 * Class SystemTest
 *
 * @package Twizo_Verification
 */

/**
 * Sample test case.
 */
class SystemTest extends WP_UnitTestCase
{

    /**
     * @var twizo_Controller $controller
     */
    private $controller;

    /**
     * @var int
     */
    private $user_id = 1;

    /**
     * @var string
     */
    private $api_key = "2cWvqgf_rfHptgDPwRbrvtivpAYu2ZMppkwJYEUYP_L00ep1";

    /**
     * @var WP_User
     */
    private $user;

    /**
     * @var string
     */
    private $twizo_user;

    function setUp()
    {
        global $controller;
        $this->controller = $controller;
        $id = 1;
        $this->user = new WP_User($id, "twizo", 1);
        $this->twizo_user = new stdClass();
        $this->twizo_user->user_id = $id;
    }

    /**
     * TESTING BASIC CONTROLLER FUNCTIONS.
     */

    /**
     * Test if the Twizo Parameters are set.
     */
    function testTwizoParams()
    {
        //Checks if the Twizohost is set.
        $this->assertNotEmpty($this->controller->twizo_getTwizoHost());
        //Check if the plugin root is set.
        $this->assertNotEmpty($this->controller->twizo_getTwizoPluginRoot());
        //Checks if the Twizo cdn is set.
        $this->assertNotEmpty($this->controller->twizo_getTwizoWidgetCdn());
        //Check if the tag is set for sending with the session.
        $this->assertNotEmpty($this->controller->twizo_getTag());
        //Check if the plugin directory url is set.
        $this->assertNotEmpty($this->controller->twizo_getPluginDirUrl());
        //Check if the wordpress database is set.
        $this->assertNotEmpty($this->controller->twizo_getWpdb());
        //Check if the TwizoHosts are being able to be get.
        $this->assertNotEmpty($this->controller->twizo_getTwizoHosts());
    }

    /**
     * Test if the helpers are set
     */
    function testHelpers()
    {
        $this->assertInstanceOf(twizo_DatabaseHelper::class, $this->controller->twizo_getDatabaseHelper());
    }

    /**
     * Check if the database is empty, and create the settings
     */
    function testDatabaseEmpty()
    {
        //Check if the settings database is empty.
        $this->assertEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getSettings());
    }


    /**
     * Test admin settings
     */
    function testAdminSettingsSetup()
    {
        //Setup the needed variables
        $this->controller->twizo_getDatabaseHelper()->twizo_setAPIkey($this->api_key);
        $host = "";
        foreach ($this->controller->twizo_getTwizoHosts() as $host) {
            $host = $host['host'];
            continue;
        }
        $this->controller->twizo_getDatabaseHelper()->twizo_setTwizoHost($host);
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getSettings());

        //Create new controller to instantiate the TwizoHelper
        $this->controller = new twizo_Controller($this->controller->twizo_getTwizoPluginRoot());
        //Test if the new controller is created, by testing if the TwizoHelper exists now.
        $this->assertInstanceOf(twizo_TwizoHelper::class, $this->controller->twizo_getTwizoHelper());
    }

    /**
     * Test admin settings
     */
    function testAllAdminSettings()
    {
        $this->controller->twizo_getDatabaseHelper()->twizo_setImageUrl('http://google.com');
        $this->controller->twizo_getDatabaseHelper()->twizo_setAdminPreferredType('sms');
        $this->controller->twizo_getDatabaseHelper()->twizo_setSender('Twizo');
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getImageUrl());
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getAdminPreferredType());
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getSender());
    }

    /**
     * Test the user settings
     */
    function testUserSettings()
    {
        //Create the user.
        $this->controller->twizo_getDatabaseHelper()->twizo_createUser($this->user, false);
        //Check if the user exists.
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getUserResult($this->user));
        //Set the 2FA.
        $this->controller->twizo_getDatabaseHelper()->twizo_set2FAPhoneNumber($this->user, true, 60, 1234500000);
        //Check if 2FA is enabled.
        $this->assertTrue($this->controller->twizo_getDatabaseHelper()->twizo_is2FA($this->user));
        //Disable 2FA.
        $this->controller->twizo_getDatabaseHelper()->twizo_set2FA($this->user, false);
        //Check if 2FA is disabled.
        $this->assertFalse($this->controller->twizo_getDatabaseHelper()->twizo_is2FA($this->user));
        //Enable 2FA.
        $this->controller->twizo_getDatabaseHelper()->twizo_set2FA($this->user, true);
        //Check if 2FA is enabled.
        $this->assertTrue($this->controller->twizo_getDatabaseHelper()->twizo_is2FA($this->user));
        //Set the preferred type.
        $this->controller->twizo_getDatabaseHelper()->twizo_setPreferredType($this->user, 'sms');
        //Check if the preferred type is set.
        $this->assertNotEmpty($this->controller->twizo_getDatabaseHelper()->twizo_getUserResult($this->user)[0]->preferred_type);
    }

}
