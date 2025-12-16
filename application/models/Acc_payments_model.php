<?php

class Acc_payments_model extends MY_Model {
    
    public $table = 'acc_payments';
    public $primary_key = 'id';

    private $key = "ample&$@";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `acc_payments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `order_date` date NOT NULL,
        `dispatch_date` date NOT NULL,
        `sub_order_id` varchar(255) NOT NULL,
        `product_name` varchar(255) NOT NULL,
        `live_status` varchar(255) NOT NULL,
        `prod_gst` int NOT NULL,
        `list_price` decimal(65,3) NOT NULL,
        `qty` int NOT NULL,
        `trans_id` varchar(255) NOT NULL,
        `trans_date` date NOT NULL,
        `settle_price` decimal(65,3) NOT NULL,
        `price_type` varchar(255) NOT NULL,
        `sale_price` decimal(65,3) NOT NULL,
        `return_price` decimal(65,3) NOT NULL,
        `ship_charge` decimal(65,3) NOT NULL,
        `tcs` decimal(65,3) NOT NULL,
        `tds` decimal(65,3) NOT NULL,
        `created_by` varchar(255) NOT NULL,
        `updated_by` varchar(255) NOT NULL,
        `created_time` varchar(255) NOT NULL,
        `updated_time` varchar(255) NOT NULL,
        `session_id` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ;";
    }

}
