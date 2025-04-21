<?php

class Companies_license_users_model extends MY_Model {
    
    public $table = 'companies_license_users';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->library('upload');
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `companies_license_users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `company_id` varchar(255) NOT NULL, `email` varchar(255) NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB;";
    
        $quries[] = "alter table companies_license_users add access_blob LONGBLOB after email;";
    }

    public function test()
    {
        echo $this->generate_salt();
    }

}
