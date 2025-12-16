<?php

class Income_expense_model extends MY_Model
{

    public $table = 'income_expense';
    public $primary_key = 'id';

    private $key = "ample&$@";

    public function __construct()
    {
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
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        session_id VARCHAR(255) NOT NULL
        );";

        $quries[] = "ALTER TABLE `income_expense` ADD `trans_no` INT NOT NULL AFTER `date`;";
    }

    public function getIncomeExpList($isCount, $postData, $pageIndex, $offset, $pageSize, $isMobile)
    {
        if ($isCount) {
            $this->db->select("count(id) as id");
        } else {
            $this->db->select("*");
        }

        // Search Filter
        if (isset($postData->search) && $postData->search !== '' && $postData->search !== 'null' && $postData->search !== 'undefined') {
            $this->db->group_start();
            $this->db->like('description', $postData->search);
            $this->db->or_like('amount', $postData->search);
            $this->db->or_like('trans_no', $postData->search);
            $this->db->group_end();
        }

        if (isset($postData->from_date) && $postData->from_date !== 'null' && $postData->from_date !== 'undefined' && isset($postData->to_date) && $postData->to_date !== 'null' && $postData->to_date !== 'undefined') {
            $this->db->where("date >=", $postData->from_date);
            $this->db->where("date <=", $postData->to_date);
        }

        if (isset($postData->type) && $postData->type !== 'null' && $postData->type !== 'undefined') {
            $this->db->where("type", $postData->type);
        }

        if (isset($postData->category_id) && $postData->category_id !== 'null' && $postData->category_id !== 'undefined') {
            $this->db->where("category_id", $postData->category_id);
        }
        // $this->db->where('description is NOT NULL', NULL, FALSE);

        if (isset($postData->payment_method) && $postData->payment_method !== 'null' && $postData->payment_method !== 'undefined') {
            $this->db->where("payment_method", $postData->payment_method);
        }

        $this->db->from('income_expense');

        $this->db->order_by('trans_no', 'desc');
        // $this->db->order_by('date','desc');

        if ($isCount) {
            $data = $this->db->count_all_results();
        } else {
            // Apply pagination if not mobile (or if you want pagination on mobile too, remove the check)
            // if(!$isMobile) 
            {
                $this->db->limit($pageSize, $offset);
            }

            $data = $this->db->get()->result();
        }

        return $data;
    }

}
