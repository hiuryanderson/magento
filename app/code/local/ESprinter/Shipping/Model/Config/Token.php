<?php 

class ESprinter_Shipping_Model_Config_Token
  extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

  public function save() {

    $token = $this->getValue();
    parent::save();
    
  }
}
