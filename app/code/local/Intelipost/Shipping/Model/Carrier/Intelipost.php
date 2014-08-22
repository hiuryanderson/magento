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

        $i = 0;
		$total_weight = 0;

        // Volume calculation for a package
        foreach ( $request->getAllItems() as $item) {

              $product = $item->getProduct();
              if($product->isConfigurable()== true){
              //$teste=$product->isConfigurable();
              //Mage::log('configuravel: '.$product->isConfigurable() , null, 'intelipost.log', true);
              continue;
              }
              $prod_width= $item->getProduct()->getVolumeLargura();
              $prod_length= $item->getProduct()->getVolumeComprimento();
              $prod_height= $item->getProduct()->getVolumeAltura();


            $total_volume += $prod_width * $prod_length * $prod_height *  $item->getQty();
            //Mage::log('volume: '.$total_volume , null, 'intelipost.log', true);
            //Mage::log('volume: '.$volumecubic , null, 'intelipost.log', true);
            //$volume->weight = number_format(floatval($item->getWeight()), 2, ',', '') * $item->getQty();
        }

           if ($total_volume ==0 ) {
            //   Mage::log('Product does not have dimensions set', null, 'intelipost.log', true);

                $pack_width = $this->getConfigData('largura_padrao'); // putting default config values here if product width is empty
                $pack_height = $this->getConfigData('altura_padrao'); // putting default config values here if product height is empty
                $pack_length = $this->getConfigData('comprimento_padrao'); // putting default config values here if product length is empty


            } else {
                $volume_bycubic = pow($total_volume, 1/3);
                $volume_bycubic = number_format($volume_bycubic, 2, '.', '');
                //Mage::log('volume: '.$volume_bycubic , null, 'intelipost.log', true);
                $pack_width = $volume_bycubic;
                $pack_height = $volume_bycubic;
                $pack_length = $volume_bycubic;
            }


        $volume = new Intelipost_Model_Request_Volume();
        $volume->volume_type = 'BOX';
        $volume->weight = $weight;
        $volume->width = $pack_width;
        $volume->height = $pack_height;
        $volume->length = $pack_length;
        $volume->cost_of_goods = floatval($price);
        array_push($quote->volumes, $volume);
        $request = json_encode($quote);

        // INTELIPOST QUOTE
        $responseBody = $this->intelipostRequest($api_url, $api_key, "/quote", $request);
        $response = json_decode($responseBody);
        $result = Mage::getModel('shipping/rate_result');

		if($response->status == "OK") { // if api responds fine and not an error
			foreach ($response->content->delivery_options as $deliveryOption) { 
				$method = Mage::getModel('shipping/rate_result_method'); 
				
				//$method_deadline = $this->formatDeadline((int)$deliveryOption->delivery_estimate_business_days);
				//$method_description = $deliveryOption->description." ".$method_deadline;
				
				$custom_title = $this->getConfigData('customizetitle'); // new way of creating shipping labels point 3
				$method_description = sprintf($custom_title, $deliveryOption->description, (int)$deliveryOption->delivery_estimate_business_days); // new way of creating shipping labels point 3
				$method->setCarrier     ('intelipost'); 
				$method->setCarrierTitle($this->getConfigData('title')); 
				$method->setMethod      ($deliveryOption->description); 
				$method->setMethodTitle ($method_description); 
				$method->setPrice       ($deliveryOption->final_shipping_cost); 
				$method->setCost        ($deliveryOption->provider_shipping_cost); 

				$result->append($method); 
			}
		}else{ // else if API call fails or reponse in more then 3 seconds
			
			$shipping_price = ''; // defining shipping price
			$number_of_days = ''; // defining number_of_days to deliver

			$root_dir_path = Mage::getBaseDir();
			$media_dir_path = $root_dir_path.DIRECTORY_SEPARATOR.'media';

			$intelipost_dir_path = $media_dir_path.DIRECTORY_SEPARATOR.'intelipost';
			$filepath = $intelipost_dir_path.DIRECTORY_SEPARATOR."state_codification.json";
			
			if (file_exists($filepath)) { // check if file exists locally
				
				$c_state = ''; $c_type = ''; // defining empty variables for state and type

				$intZipCode = (int)$destinationZipCode; // Transform ZIP code from string => numeric

				$c_weight = $total_weight*1000; // converting total weight of quote into grams

				$state_codification = json_decode(file_get_contents($filepath)); // load state_codification.json as array
				
				$intArray = array(); // Defining a new array for integer values
				foreach($state_codification[0] as $key => $value) {
					$intArray[(int)$key] = $value; // Transform keys of array from string => numeric
				}
				asort($intArray); // Sort the keys of the array ascending
				
				foreach($intArray as $key => $value) {
					if(($intZipCode > $key) && ($intZipCode < (int)$value->cep_end)) {
						$c_state = trim($value->state); // assigning value of state here if found
						$c_type = ucfirst(strtolower($value->type)); // assigning value of type here if found
						break;
					}
				}

				if($c_state != '' && $c_type != '') { // if state and type are found
					
					$filepath = $intelipost_dir_path.DIRECTORY_SEPARATOR."esedex.sp.json";
					if (file_exists($filepath)) { // check if file exists locally
						$esedex = json_decode(file_get_contents($filepath)); // Load configured backup table: e.g. esedex.sp.json
						
						$number_of_days = $esedex->$c_state->$c_type->delivery_estimate_business_days;
						foreach($esedex->$c_state->$c_type->final_shipping_cost as $key => $value) {
							if(($key > $c_weight) && !isset($last_v)) {
								$shipping_price = $value;
								break;
							}

							if($key > $c_weight) {
								$shipping_price = $last_v;
								break;
							}

							$last_k = $key; // saving -1 key
							$last_v = $value; // saving value for -1 key
						}
					}

				}

			}
			if($shipping_price != '' && $number_of_days != '') {
				$method = Mage::getModel('shipping/rate_result_method'); 
				
				$custom_title = $this->getConfigData('customizetitle'); // new way of creating shipping labels point 3
				$method_description = sprintf($custom_title, "Entrega", (int)$number_of_days); // new way of creating shipping labels point 3
				
				$method->setCarrier     ('intelipost'); 
				$method->setCarrierTitle('Intelipost'); 
				$method->setMethod      ("Entrega"); 
				$method->setMethodTitle ($method_description); 
				$method->setPrice       ($shipping_price); 
				$method->setCost        ($shipping_price); 

				$result->append($method);
			}
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
		$mgversion = Mage::getEdition()." ".Mage::getVersion();
        $s = curl_init();

        curl_setopt($s, CURLOPT_TIMEOUT, 3); // maximum time allowed to call API is 3 seconds
        curl_setopt($s, CURLOPT_URL, $api_url.$entity_action);
        curl_setopt($s, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json", "api_key: $api_key", "platform: Magento $mgversion", "plugin: 1.1.0"));
        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_ENCODING , "");
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($s, CURLOPT_POSTFIELDS, $request);

        $response = curl_exec($s);

        curl_close($s);

        return $response;
    }

}

