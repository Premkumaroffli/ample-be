<?php

class Invoice_items_model extends MY_Model {
    
    public $table = 'invoice_items';
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

        $quries[] = "CREATE TABLE income_expense (
            id INT PRIMARY KEY AUTO_INCREMENT,
            date DATE NOT NULL,
            type VARCHAR(10) NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            category_id INT NOT NULL,
            description VARCHAR(255) NULL,
            payment_method VARCHAR(50) NULL,
            created_by VARCHAR(255) NOT NULL,
            updated_by VARCHAR(255) NULL,
            created_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_time TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            session_id VARCHAR(255) NOT NULL
        );";
    }

}
