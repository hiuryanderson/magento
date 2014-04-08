<?php 

class Intelipost_Shipping_Model_Config_Password
    extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

    public function save() {

        $password = $this->getValue();
        parent::save();

    }

    
}
