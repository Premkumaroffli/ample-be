<?php

class Acc_account_type_model extends MY_Model {
    
    public $table = 'acc_account_type';
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

        $quries[] = "CREATE TABLE `acc_account_type` ( `id` int NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `status` int NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB ;";
    }

}
