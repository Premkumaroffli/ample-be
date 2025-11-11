<?php

class Fabric_color_model extends MY_Model {
    
    public $table = 'fabric_color';
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

        $quries[] = "CREATE TABLE fabric_color ( 
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL, 
        status tinyint NOT NULL, 
        created_by varchar(255) NOT NULL,
        updated_by varchar(255) NOT NULL,
        created_time varchar(255) NOT NULL,
        updated_time varchar(255) NOT NULL,
        session_id varchar(255) NOT NULL);";
    }

    
    public function getFabricColorlist($type, $postData, $pageIndex, $offset, $pageSize, $isMobile)
    {

        if($type)
        {
            $this->db->select("count(id) as id");
        }
        else
        {
            $this->db->select("*");
        }
        
        if(isset($postData->fabric_color_id) && $postData->fabric_color_id !== 'null'  &&  $postData->fabric_color_id !== 'undefined')
        {
            $this->db->where("id = '$postData->fabric_color_id'");
        }
        
        if(isset($postData->status) && $postData->status !== 'null'  &&  $postData->status !== 'undefined')
        {
            $this->db->where("status = '$postData->status'");
        }

        $this->db->from('fabric_color');

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
