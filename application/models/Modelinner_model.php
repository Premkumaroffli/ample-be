<?php

class Modelinner_model extends MY_Model {
    
    public $table = 'modelinner';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();
        $quries[] = "CREATE TABLE `modelinner` ( `id` int(11) NOT NULL AUTO_INCREMENT, `modellist_id` int(11) NOT NULL, `modelheader_id` int(11) NOT NULL, `modelset_id` int(11) NOT NULL, `title` varchar(255) NOT NULL, `model_id` varchar(255) NOT NULL, `model_icon` varchar(255) NOT NULL, `model_type` varchar(255) NOT NULL, `model_url` varchar(255) NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB ;";
    }

    public function saveChildModelset($saveData=null)
    {
        $save = new StdClass;
        $save->name = $saveData->name;
        $save->status = $saveData->status['code'];
        $id = $this->db->insert($this->table, $save);
        return $id;
    }

    public function updateChildModelset($updateData=null)
    {
        $updateData->status = $updateData->status['code'];
        $id = $this->db->update($this->table, $updateData, 'id='.$updateData->id);
        return $id;
    }

    public function deleteChildModelset($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
        
    }

}
