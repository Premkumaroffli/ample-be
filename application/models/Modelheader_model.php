<?php

class Modelheader_model extends MY_Model {
    
    public $table = 'modelheader';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();
        $quries[] = "";
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
