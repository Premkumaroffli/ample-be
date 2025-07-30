<?php

class Acc_income_expense_model extends MY_Model {
    
    public $table = 'acc_income_expense';
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

        $quries[] = "CREATE TABLE `acc_income_expense` ( `id` int NOT NULL AUTO_INCREMENT, `trans_date` date NOT NULL, `acc_type_id` int NOT NULL, `cash_pur_id` int not null, `reason` varchar(255) NOT NULL, `cash` int not null, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ;";
    }

}
