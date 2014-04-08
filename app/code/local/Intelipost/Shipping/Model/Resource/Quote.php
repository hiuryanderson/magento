<?php

/**
 * @author eSprinter (it@e-sprinter.com.br)
 */
class Intelipost_Model_Request_Quote {

    /**
     * @var string
     */
    public $origin_zip_code;

    /**
     * @var string
     */
    public $destination_zip_code;

    /**
     * @var ESprinter_Model_Request_Volume[]
     */
    public $volumes = array();
} 