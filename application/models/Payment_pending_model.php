<?php

class Payment_pending_model extends MY_Model
{

    public $table = 'payment_pending';
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

        $quries[] = "CREATE TABLE `payment_pending` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `date` date NOT NULL,
        `category_id` INT NOT NULL,
        `amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
        `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
        `reference` VARCHAR(255) DEFAULT NULL,
        `remarks` TEXT DEFAULT NULL,
        `created_by` varchar(255) NOT NULL,
        `updated_by` varchar(255) DEFAULT NULL,
        `created_time` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_time` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB;";
    }

    public function getPaymentPendingList($isCount, $postData, $pageIndex, $offset, $pageSize, $isMobile)
    {
        if ($isCount) {
            $this->db->select("count(id) as id");
        } else {
            $this->db->select("*");
        }

        // Search Filter
        if (isset($postData->search) && $postData->search !== '' && $postData->search !== 'null' && $postData->search !== 'undefined') {
            $this->db->group_start();
            $this->db->like('remarks', $postData->search);
            $this->db->or_like('amount', $postData->search);
            $this->db->or_like('reference', $postData->search);
            $this->db->group_end();
        }

        if (isset($postData->from_date) && $postData->from_date !== 'null' && $postData->from_date !== 'undefined' && isset($postData->to_date) && $postData->to_date !== 'null' && $postData->to_date !== 'undefined') {
            $this->db->where("date >=", $postData->from_date);
            $this->db->where("date <=", $postData->to_date);
        }

        if (isset($postData->status) && $postData->status !== 'null' && $postData->status !== 'undefined') {
            $this->db->where("status", $postData->status);
        }
        // $this->db->where('description is NOT NULL', NULL, FALSE);

        $this->db->from('payment_pending');

        $this->db->order_by('id', 'desc');
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
