<?php

class Acc_orders_model extends MY_Model {
    
    public $table = 'acc_orders';
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

        $quries[] = "CREATE TABLE `acc_orders` ( `id` int NOT NULL AUTO_INCREMENT, `order_date` date NOT NULL, `sub_order_id` varchar(255) NOT NULL, `reason` varchar(255) NOT NULL, `state` varchar(255) NOT NULL, `product_name` varchar(255) NOT NULL, `sku` varchar(255) NOT NULL, `size` varchar(255) NOT NULL, `qty` int NOT NULL, `list_price` int NOT NULL, `discount_price` int NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ;";
    }

}
