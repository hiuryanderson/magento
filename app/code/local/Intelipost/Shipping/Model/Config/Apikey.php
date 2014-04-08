<?php 

require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Quote.php';
require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Volume.php';

class Intelipost_Shipping_Model_Config_Apikey
  extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

    /**
     * @return Mage_Core_Model_Abstract|void
     */
    public function save()
    {
        $api_key    = $this->getValue();

        $responseJson = $this->testQuote($api_key);

        if (!isset($responseJson->status)) {
            $helper = Mage::helper('intelipost');

           // Mage::getSingleton('core/session')->addWarning($helper->__('Invalid ApiUrl/ApiKey combination'));
            Mage::throwException($helper->__('Invalid ApiUrl/ApiKey combination'));
        }

        parent::save();
    }

    private function testQuote($api_key)
    {
        $api_url    = $this->getData('groups/intelipost/fields/apiurl/value');
        $zipcode    = $this->getData('groups/intelipost/fields/zipcode/value');

        $volume = new Intelipost_Model_Request_Volume();
        $volume->weight        = 10.2;
        $volume->volume_type   = 'BOX';
        $volume->cost_of_goods = "10";
        $volume->width         = 20.5;
        $volume->height        = 10.25;
        $volume->length        = 5.0;

        $quote = new Intelipost_Model_Request_Quote();
        $quote->origin_zip_code      = $zipcode;
        $quote->destination_zip_code = $zipcode;

        array_push($quote->volumes, $volume);

        $request = json_encode($quote);

        Mage::log("\nREQUEST: ".$request, null, "intelipost.log", true);

        $response = $this->intelipostRequest($api_url, $api_key, "/quote", $request);

        Mage::log("\nRESPONSE: ".$response, null, "intelipost.log", true);

        return json_decode($response);
    }

    /**
     * @param $api_url
     * @param $api_key
     * @param bool $body
     * @return mixed
     */
    private function intelipostRequest($api_url, $api_key, $entity_action, $request=false)
    {
        $s = curl_init();

        curl_setopt($s, CURLOPT_TIMEOUT, 5000);
        curl_setopt($s, CURLOPT_URL, $api_url.$entity_action);
        curl_setopt($s, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json", "api_key: $api_key"));
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_ENCODING , "");
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($s, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($s);

        curl_close($s);

        return $response;
    }

}
