<?php


class Intelipost_Shipping_Model_Resource_Setup
    extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities() {
        return array(
            // 'catalog_product' => array(
            // 'catalog/product' => array(
            Mage_Catalog_Model_Product::ENTITY => array(
                'entity_model'                  => 'catalog/product',
                'attribute_model'               => 'catalog/resource_eav_attribute',
                'table'                         => 'catalog/product',

                // INFO: these two entries bellow are needed for magento 1.4, they do not exist on magento 1.3!
                'additional_attribute_table'    => 'catalog/eav_attribute',
                'entity_attribute_collection'   => 'catalog/product_attribute_collection',

                // this seems to break the shopping cart (http://www.magentocommerce.com/boards/viewthread/176130/#t232548)
                // 'entity_attribute_collection' => 'catalog/category_attribute_collection',
                
                // 'frontend_prefix'               => '',
                // 'backend_prefix'                => '',
                // 'source_prefix'                 => '',

                'attributes' => array(
                    // attributes are called 'volume_altura', 'volume_largura', and 'volume_comprimento' so that this plugin is compatible with
                    // the Correios plugin
                    // 'height'    => array(
                    'volume_altura'    => array(
                        'group'                     => 'General',   // tab
                        'type'                      => 'int',       // magento attribute type
                        'backend'                   => '',
                        'frontend'                  => '',
                        'label'                     => 'Height',
                        'input'                     => 'text',
                        'class'                     => '',          // input validation class
                        'source'                    => '',          // options for the select
                        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'                   => true, 
                        'required'                  => false, 
                        'user_defined'              => false,       // true => user attribute, false => system attribute
                        'default'                   => '',
                        'searchable'                => false,
                        'filterable'                => false,
                        'comparable'                => false,
                        'visible_on_front'          => false,
                        'used_in_product_listing'   => true,       // needed to load this EAV attribute on collectRates()
                        'unique'                    => false,
                        'apply_to'                  => array('bundle,simple'), //array('bundle,virtual,simple'),
                        'is_configurable'           => false
                    ),
                    // 'width'     => array(
                    'volume_largura'     => array(
                        'group'                     => 'General',   // tab
                        'type'                      => 'int',       // magento attribute type
                        'backend'                   => '',
                        'frontend'                  => '',
                        'label'                     => 'Width',
                        'input'                     => 'text',
                        'class'                     => '',          // input validation class
                        'source'                    => '',          // options for the select
                        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'                   => true, 
                        'required'                  => false, 
                        'user_defined'              => false,       // true => user attribute, false => system attribute
                        'default'                   => '',
                        'searchable'                => false,
                        'filterable'                => false,
                        'comparable'                => false,
                        'visible_on_front'          => false,
                        'used_in_product_listing'   => true,       // needed to load this EAV attribute on collectRates()
                        'unique'                    => false,
                        'apply_to'                  => array('bundle,simple'), //array('bundle,virtual,simple'),
                        'is_configurable'           => false
                    ),
                    // 'length'    => array(
                    'volume_comprimento'    => array(
                        'group'                     => 'General',   // tab
                        'type'                      => 'int',       // magento attribute type
                        'backend'                   => '',
                        'frontend'                  => '',
                        'label'                     => 'Length',
                        'input'                     => 'text',
                        'class'                     => '',          // input validation class
                        'source'                    => '',          // options for the select
                        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'                   => true, 
                        'required'                  => false, 
                        'user_defined'              => false,       // true => user attribute, false => system attribute
                        'default'                   => '',
                        'searchable'                => false,
                        'filterable'                => false,
                        'comparable'                => false,
                        'visible_on_front'          => false,
                        'used_in_product_listing'   => true,       // needed to load this EAV attribute on collectRates()
                        'unique'                    => false,
                        'apply_to'                  => array('bundle,simple'), //array('bundle,virtual,simple'),
                        'is_configurable'           => false
                    )
                )
            )
        );
    }
}
