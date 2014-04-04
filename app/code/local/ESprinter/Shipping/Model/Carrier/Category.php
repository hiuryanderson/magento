<?php

class Intelipost_Shipping_Model_Carrier_Category {
    public function toOptionArray() {
        return array(
            array('value' => '2', 'label' =>  Mage::helper('catalog')->__('Machinery and/or parts')),
            array('value' => '5', 'label' =>  Mage::helper('catalog')->__('Eletronics')),
            array('value' => '6', 'label' =>  Mage::helper('catalog')->__('Fragile products')),
            array('value' => '7', 'label' =>  Mage::helper('catalog')->__('Cosmetics')),
            array('value' => '10', 'label' => Mage::helper('catalog')->__('Office supplies and furniture')),
            array('value' => '12', 'label' => Mage::helper('catalog')->__('Pharmacy')),
            array('value' => '13', 'label' => Mage::helper('catalog')->__('Books and/or graphical material')),
            array('value' => '14', 'label' => Mage::helper('catalog')->__('Floriculture')),
            array('value' => '19', 'label' => Mage::helper('catalog')->__('Beverages')),
            array('value' => '20', 'label' => Mage::helper('catalog')->__('Alcoholic')),
            array('value' => '25', 'label' => Mage::helper('catalog')->__('Toys')),
            array('value' => '22', 'label' => Mage::helper('catalog')->__('Food')),
            array('value' => '26', 'label' => Mage::helper('catalog')->__('Documents')),
            array('value' => '23', 'label' => Mage::helper('catalog')->__('High value products')),
            array('value' => '24', 'label' => Mage::helper('catalog')->__('Clothing'))
        );
    }
}
