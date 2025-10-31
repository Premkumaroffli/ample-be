<?php

class Acc_investment_model extends MY_Model {
    
    public $table = 'acc_investment';
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

        $quries[] = "CREATE TABLE `acc_investment` ( `id` int NOT NULL AUTO_INCREMENT, `inv_date` date NOT NULL, `inv_no` int NOT NULL, `reason` varchar(255) NOT NULL, `amount` int NOT NULL, `qty` int NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB ;";
    }

}
