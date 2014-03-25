<?php

/**
 * @author eSprinter (it@e-sprinter.com.br)
 */
class ESprinter_Model_Request_Quote {
    public $origin_zip_code;
    public $destination_zip_code;
    /**
     * @var ESprinter_Model_Request_Volume[]
     */
    public $volumes = array();
} 