<?php
/**
 * @author eSprinter (it@e-sprinter.com.br)
 */

class Intelipost_Model_Request_Invoice {

    /**
     * @var string
     */
    public $order_number;

    /**
     * @var string
     */
    public $shipment_order_volume_number;

    /**
     * @var string
     */
    public $invoice_series;

    /**
     * @var string
     */
    public $invoice_number;

    /**
     * @var string
     */
    public $invoice_key;

    /**
     * @var date
     */
    public $invoice_date;

    /**
     * @var double
     */
    public $invoice_total_value;

    /**
     * @var double
     */
    public $invoice_products_value;

    /**
     * @var double
     */
    public $invoice_cfop;

} 