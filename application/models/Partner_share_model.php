<?php

class Partner_share_model extends MY_Model {
    
    public $table = 'partner_share';
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

        $quries[] = "CREATE TABLE partner_share ( 
        id INT AUTO_INCREMENT PRIMARY KEY,
        partner_id int NOT NULL, 
        amount decimal(65,3) NOT NULL, 
        created_by varchar(255) NOT NULL,
        updated_by varchar(255) NOT NULL,
        created_time varchar(255) NOT NULL,
        updated_time varchar(255) NOT NULL,
        session_id varchar(255) NOT NULL);";
    }

}
