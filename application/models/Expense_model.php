<?php

class Expense_model extends MY_Model {
    
    public $table = 'expense';
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

        $quries[] = "CREATE TABLE `expense` ( `id` int(11) NOT NULL AUTO_INCREMENT, `ex_name` varchar(255) NOT NULL, `expense` int NOT NULL, `status` int(11) NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB;
";
        $quries[] = "alter table expense add exp_type varchar(255) not null after ex_name;";
    }

}
