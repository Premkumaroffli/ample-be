<?php

class Return_claim_folders_model extends MY_Model {
    
    public $table = 'return_claim_folders';
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

        $quries[] = "CREATE TABLE `return_claim_folders` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `folder_name` varchar(255) NOT NULL,
        `upload_path` varchar(255) DEFAULT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `created_by` varchar(255) NOT NULL,
        `updated_by` varchar(255) DEFAULT NULL,
        `created_time` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_time` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
        `session_id` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB;";
    }

}
