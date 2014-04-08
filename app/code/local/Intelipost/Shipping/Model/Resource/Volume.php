<?php
/**
 * @author eSprinter (it@e-sprinter.com.br)
 */

class Intelipost_Model_Request_Volume {

    /**
     * @var double
     */
    public $weight;

    /**
     * @var string[BOX|ENVELOPE]
     */
    public $volume_type_code;

    /**
     * @var double
     */
    public $cost_of_goods;

    /**
     * @var double
     */
    public $width;

    /**
     * @var double
     */
    public $height;

    /**
     * @var double
     */
    public $length;

    /**
     * @var integer
     */
    public $shipment_order_volume_number;

    /**
     * @var string
     */
    public $products_nature;

    /**
     * @var integer
     */
    public $products_quantity;

    /**
     * @var boolean
     */
    public $is_icms_exempt;

}