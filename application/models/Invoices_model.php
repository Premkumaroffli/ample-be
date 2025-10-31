<?php

class Invoices_model extends MY_Model {
    
    public $table = 'invoices';
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

        $quries[] = "CREATE TABLE invoices (
        id INT PRIMARY KEY AUTO_INCREMENT,
        invoice_no INT NOT NULL,
        customer_id INT NOT NULL,
        invoice_date DATE NOT NULL,
        delivery_date DATE,
        place_of_supply VARCHAR(100) NOT NULL,
        subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        discount_percent DECIMAL(5, 2) DEFAULT 0.00,
        discount_amount DECIMAL(10, 2) DEFAULT 0.00,
        taxable_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        sgst DECIMAL(5, 2) DEFAULT 0.00,
        sgst_value DECIMAL(10, 2) DEFAULT 0.00,
        cgst DECIMAL(5, 2) DEFAULT 0.00,
        cgst_value DECIMAL(10, 2) DEFAULT 0.00,
        payable_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        created_by varchar(255) NOT NULL,
        updated_by varchar(255) NOT NULL,
        created_time varchar(255) NOT NULL,
        updated_time varchar(255) NOT NULL,
        session_id varchar(255) NOT NULL
        );";
    }

}
