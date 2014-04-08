<?php

/**
 * @author eSprinter (it@e-sprinter.com.br)
 */
class Intelipost_Model_Request_ShipmentOrder {

    /**
     * @var integer
     */
    public $quote_id;

    /**
     * @var integer
     */
    public $deliver_method_id;

    /**
     * @var string
     */
    public $order_number;

    /**
     * @var datetime
     */
    public $estimated_delivery_date;

    /**
     * @var Intelipost_Model_Request_EndCustomer[]
     */
    public $end_customer = false;
}