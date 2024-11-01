<?php
/**
 * Created by IntelliJ IDEA.
 * User: sordelman
 * Date: 18/12/2017
 * Time: 09:46
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class twizo_TwizoHelper
{

    /**
     * The Twizo API interface.
     * @var \Twizo\Api\TwizoInterface
     */
    private $twizo;

    /**
     * The main twizoController.
     * @var twizo_Controller
     */
    private $controller;

    /**
     * TwizoHelper constructor.
     * @param $twizo Twizo\Api\TwizoInterface
     * @param $controller twizo_Controller
     */
    public function __construct($twizo, $controller)
    {
        $this->twizo = $twizo;
        $this->controller = $controller;
    }

    /**
     * Get the allowedTypes
     * @param $isLogin bool if the verification is for login or not
     * @return array|\Twizo\Api\Entity\Application\VerificationTypes
     */
    public function twizo_getAllowedTypes($isLogin)
    {
        $verificationTypes = array();
        if($isLogin) {
            $allowedTypes = $this->twizo->getVerificationTypes()->getVerificationTypes();

            $types = json_decode(file_get_contents('https://cdn.twizo.com/information.json'), true)["verificationTypes"];

            foreach($types as $typeCode => $type) {
                if(in_array($typeCode, $allowedTypes)) {
                    $verificationTypes[$typeCode] = (isset($type['translations']) && isset($type['translations']['en'])) ? $type['translations']['en'] : $typeCode;
                }
            }

        } else {
            $verificationTypes = array('sms' => 'SMS');
        }

        return $verificationTypes;
    }

}