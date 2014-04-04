<?php
/**
 * @author Intelipost (it@intelipost.com.br)
 */

class Intelipost_Model_Request_Volume {
    /**
     * @var double
     */
    public $weight;
    /**
     * @var string[BOX|ENVELOPE]
     */
    public $volume_type;
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

} 