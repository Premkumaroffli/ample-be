<?php

class States_model extends MY_Model {
    
    public $table = 'states';
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

        $quries[] = "CREATE TABLE states ( id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL );";

        $quries[] = "INSERT INTO states (name) VALUES ('Andhra Pradesh'), ('Arunachal Pradesh'), ('Assam'), ('Bihar'), ('Chhattisgarh'), ('Goa'), ('Gujarat'), ('Haryana'), ('Himachal Pradesh'), ('Jharkhand'), ('Karnataka'), ('Kerala'), ('Madhya Pradesh'), ('Maharashtra'), ('Manipur'), ('Meghalaya'), ('Mizoram'), ('Nagaland'), ('Odisha'), ('Punjab'), ('Rajasthan'), ('Sikkim'), ('Tamil Nadu'), ('Telangana'), ('Tripura'), ('Uttar Pradesh'), ('Uttarakhand'), ('West Bengal'), ('Andaman and Nicobar Islands'), ('Chandigarh'), ('Dadra and Nagar Haveli and Daman and Diu'), ('Delhi'), ('Jammu and Kashmir'), ('Ladakh'), ('Lakshadweep'), ('Puducherry');";
    }

}
