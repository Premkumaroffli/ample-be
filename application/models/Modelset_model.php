<?php

class Modelset_model extends MY_Model {

    protected $table = 'modelset';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->_session = true;
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();
        $quries[] = "CREATE TABLE `modelset` (`id` INT NOT NULL AUTO_INCREMENT , `header` VARCHAR(255) NOT NULL , `title` VARCHAR(255) NOT NULL , `model_id` VARCHAR(255) NOT NULL , `model_icon` VARCHAR(255) NOT NULL , `model_type` VARCHAR(255) NOT NULL , `model_url` VARCHAR(255) NOT NULL , `created_by` VARCHAR(255) NOT NULL , `updated_by` VARCHAR(255) NOT NULL , `created_time` VARCHAR(255) NOT NULL , `updated_time` VARCHAR(255) NOT NULL , `session_id` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;;
";
        $quries[] = "ALTER TABLE `modelset` ADD `modellist_id` INT NOT NULL AFTER `id`;";
    }

    public function saveModelset($saveData=null)
    {
        print_r($saveData);
        $id = $this->db->insert($this->table, $save);
        print_r($this->appDB->last_query());
        return $id;
    }

    public function updateModelset($updateData=null)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function deleteModelset($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
        
    }

}
