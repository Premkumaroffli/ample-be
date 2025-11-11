<?php

class Customers_model extends MY_Model {
    
    public $table = 'customers';
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

        $quries[] = "CREATE TABLE customers (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `phone_no` VARCHAR(20) NOT NULL,
    `address` TEXT,
    `location` VARCHAR(255) NOT NULL,
    `state` INT NOT NULL,
    `pin_code` VARCHAR(10) NOT NULL,
    `email` VARCHAR(255),
    `gstin` VARCHAR(15) NOT NULL,
    `birthday` DATE NULL,
    `anniversary_date` DATE NULL,
    `created_by` varchar(255) NOT NULL,
    `updated_by` varchar(255) NOT NULL,
    `created_time` varchar(255) NOT NULL,
    `updated_time` varchar(255) NOT NULL,
    `session_id` varchar(255) NOT NULL
    );";
    }

    public function getcustomerList($type, $postData, $pageIndex, $offset, $pageSize, $isMobile)
    {

        if($type)
        {
            $this->db->select("count(id) as id");
        }
        else
        {
            $this->db->select("*");
        }
        
        if(isset($postData->customer_id) && $postData->customer_id !== 'null'  &&  $postData->customer_id !== 'undefined')
        {
            $this->db->where("id = '$postData->customer_id'");
        }
        
        // if(isset($postData->status) && $postData->status !== 'null'  &&  $postData->status !== 'undefined')
        // {
        //     $this->db->where("status = '$postData->status'");
        // }

        $this->db->from('customers');

        $this->db->order_by('id', 'desc');
 
        if($type)
        {
           $data = $this->db->count_all_results();
        }
        else
        {
            if($isMobile == false)
            {
                $this->db->limit($pageSize, $offset);
            }

            $data = $this->db->get()->result();
        }

        return $data;
    }

}
