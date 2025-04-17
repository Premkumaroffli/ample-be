<?php

class Modellist_model extends MY_Model {
    
    public $table = 'modellist';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();
        $quries[] = "CREATE TABLE `modellist` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `description` varchar(255) NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    }

    public function saveModellist($saveData=null)
    {
        $save = new StdClass;
        $save->name = $saveData->name;
        $save->status = $saveData->status['code'];
        $id = $this->db->insert($this->table, $save);
        return $id;
    }

    public function updateModellist($updateData=null)
    {
        $updateData->status = $updateData->status['code'];
        $id = $this->db->update($this->table, $updateData, 'id='.$updateData->id);
        return $id;
    }

    public function deleteModellist($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
        
    }

}
