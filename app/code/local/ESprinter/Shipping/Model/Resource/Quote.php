<?php

/**
 * @author Intelipost (it@intelipost.com.br)
 */
class Intelipost_Model_Request_Quote {
    public $origin_zip_code;
    public $destination_zip_code;
    /**
     * @var Intelipost_Model_Request_Volume[]
     */
    public $volumes = array();
} 