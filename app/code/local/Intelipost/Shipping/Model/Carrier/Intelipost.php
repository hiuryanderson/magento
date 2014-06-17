<?php

require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Quote.php';
require_once Mage::getBaseDir('code') . '/local/Intelipost/Shipping/Model/Resource/Volume.php';

class Intelipost_Shipping_Model_Carrier_Intelipost
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'intelipost';
    protected $_helper = null;

    private $_zipCodeRegex = '/[0-9]{2}\.?[0-9]{3}-?[0-9]{3}/';

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|false|Mage_Core_Model_Abstract|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_helper         = Mage::helper('intelipost');

        if (!$this->getConfigFlag('active')) {
            Mage::log('Intelipost is inactive', null, 'intelipost.log', true);
            return false;
        }

        $originZipCode      = $this->getConfigData('zipcode');
        $destinationZipCode = $request->getDestPostcode();

        if (!preg_match($this->_zipCodeRegex, $originZipCode) || !preg_match($this->_zipCodeRegex, $destinationZipCode)) {
            Mage::log('Invalid zip code ' . $originZipCode . ' or ' . $destinationZipCode, null, 'intelipost.log', true);
            return false;
        }

        // only numbers allowed
        $originZipCode      = preg_replace('/[^0-9]/', '', $originZipCode);
        $destinationZipCode = preg_replace('/[^0-9]/', '', $destinationZipCode);

        $weight = $request->getPackageWeight();

        if ($weight <= 0) {
            $this->_throwError('weightzeroerror', 'Weight zero', __LINE__);
        }        

        // weight must be in Kg
        if($this->getConfigData('weight_type') == 'gr') {
            $weight = number_format($weight/1000, 2, '.', '');
        } else {
            $weight = number_format($weight, 2, '.', '');
        }

        $price      = $request->getPackageValue();

        $encryption = Mage::getSingleton('core/encryption');
        $api_key    = $encryption->decrypt($this->getConfigData('apikey'));
        $api_url    = $encryption->decrypt($this->getConfigData('apiurl'));

        if(!isset($api_key) || !is_string($api_key)) {
            Mage::log('Intelipost not configured', null, 'intelipost.log', true);
            return false;
        }

        $item_list      = Mage::getModel('checkout/cart')->getQuote()->getAllVisibleItems();
        $productsCount  = count($item_list);

        $quote = new Intelipost_Model_Request_Quote();
        $quote->origin_zip_code = $originZipCode;
        $quote->destination_zip_code = $destinationZipCode;

        if (!$request->getAllItems()) {
            Mage::log('Cart is empty', null, 'intelipost.log, true');
            return false;
        }

        // if notification is disabled we send the request anyways (changed on version 0.0.2)
        $dimension_check = $this->getConfigData('notification');

        $i=0;
        foreach ($item_list as $item) {
            $product = $item->getProduct();

            $volume = new Intelipost_Model_Request_Volume();
            $volume->volume_type = 'BOX';

            if (!$this->isDimensionSet($product)) {
                Mage::log('Product does not have dimensions set', null, 'intelipost.log', true);

                if ($dimension_check) {
                    $this->notifyProductsDimension($item_list);
                    return false;
                }
            } else {
                $volume->width = $product->getVolumeLargura();
                $volume->height = $product->getVolumeAltura();
                $volume->length = $product->getVolumeComprimento();
            }

            $volume->weight = number_format(floatval($item->getWeight()), 2, ',', '') * $item->getQty();
            $volume->cost_of_goods = number_format(floatval($item->getPrice()), 2, ',', '') * $item->getQty();

            array_push($quote->volumes, $volume);
        }

        $request = json_encode($quote);

        // INTELIPOST QUOTE
        $responseBody = $this->intelipostRequest($api_url, $api_key, "/quote", $request);
        $response = json_decode($responseBody);
        $result = Mage::getModel('shipping/rate_result');

        foreach ($response->content->delivery_options as $deliveryOption) { 
            $method = Mage::getModel('shipping/rate_result_method'); 
            $method_deadline = $this->formatDeadline((int)$deliveryOption->delivery_estimate_business_days);
			$method_description = $deliveryOption->description." ".$method_deadline;
            $method->setCarrier     ('intelipost'); 
            $method->setCarrierTitle('Intelipost'); 
            $method->setMethod      ($deliveryOption->description); 
            $method->setMethodTitle ($method_description); 
            $method->setPrice       ($deliveryOption->final_shipping_cost); 
            $method->setCost        ($deliveryOption->provider_shipping_cost); 

            $result->append($method); 
        } 

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    /**
     * @param $product
     * @return bool
     */
    private function isDimensionSet($product)
    {
        $volume_altura      = $product->getData('volume_altura');
        $volume_largura     = $product->getData('volume_largura');
        $volume_comprimento = $product->getData('volume_comprimento');

        if ($volume_comprimento == ''   || (int)$volume_comprimento == 0
            || $volume_largura == ''    || (int)$volume_largura == 0
            || $volume_altura == ''     || (int)$volume_altura == 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $item_list
     */
    private function notifyProductsDimension($item_list)
    {
        $notification   = Mage::getSingleton('adminnotification/inbox');
        $message        = $this->_helper->__('The following products do not have dimension set:');

        $message .= '<ul>';
        foreach ($item_list as $item) {
            $product = $item->getProduct();

            if (!$this->isDimensionSet($product)) {
                $message .= '<li>';
                $message .= sprintf(
                    '<a href="%s">',
                    Mage::helper('adminhtml')->getUrl(
                        'adminhtml/catalog_product/edit',
                        array( 'id' => $product->getId())
                    )
                );
                $message .= $item->getName();
                $message .= '</a>';
                $message .= '</li>';
            }
        }
        $message .= '</ul>';

        $message .= $this->_helper->__('<small>Disable these notifications in System > Configuration > Shipping Methods > Intelipost</small>');

        $notification->add(
            Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR,
            $this->_helper->__('Product missing dimensions'),
            $message
        );
    }

    /**
     * @param $days
     * @return string
     */
    private function formatDeadline($days)
    {
        if ($days == 0) {
            return $this->_helper->__('(same day)');
        }

        if ($days == 1) {
            return $this->_helper->__('(1 day)');
        }

        if ($days == 101) {
            return $this->_helper->__('(On acknowledgment)');
        }

        return sprintf($this->_helper->__('(%s days)'), $days);
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

