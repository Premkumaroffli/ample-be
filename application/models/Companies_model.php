<?php

class Companies_model extends MY_Model {
    
    public $table = 'companies';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->library('upload');
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `companies` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `company` varchar(255) NOT NULL,
        `database` varchar(255) NOT NULL,
        `created_by` varchar(255) NOT NULL,
        `updated_by` varchar(255) NOT NULL,
        `created_time` varchar(255) NOT NULL,
        `updated_time` varchar(255) NOT NULL,
        `session_id` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB";

        $quries[] = "alter table companies add model_ids varchar(255) after company";
    }

    public function test()
    {
        echo $this->generate_salt();
    }

}
