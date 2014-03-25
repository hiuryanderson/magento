<?php 

class ESprinter_Shipping_Model_Config_Useraccount
  extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

  public function save() {

    $useraccount = $this->getValue();
    parent::save();
    
  }
}
