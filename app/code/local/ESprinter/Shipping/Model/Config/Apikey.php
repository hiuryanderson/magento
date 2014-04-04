<?php 

class Intelipost_Shipping_Model_Config_Apikey
  extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

  public function save() {

    $apikey = $this->getValue();
    parent::save();
    
  }
}
