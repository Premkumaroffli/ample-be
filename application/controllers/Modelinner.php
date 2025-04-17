<?php

class Modelinner extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveModelinner()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $modelinner_id = $this->modelinner->save($postData, $id);
            $this->loader->sendresponse($modelinner_id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteModelinner()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->modelinner->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getModelinner($list_id, $head_id, $set_id)
    {
        $where = array();
        $where['modellist_id'] = $list_id;
        $where['modelheader_id'] = $head_id;
        $where['modelset_id'] = $set_id;
        $modelinner_id = $this->modelinner->get_by($where);

        $this->loader->sendresponse($modelinner_id);
    }

    public function getModelinnerForUser()
    {
        $modelinner_id = $this->modelinner->get();

        foreach($modelinner_id as $m)
        {
            $m->label = $m->title;
            $m->icon = $m->icon_id;
            $m->router = $m->link;
        }

        $this->loader->sendresponse($modelinner_id);
    }

    public function getModelinnerbyId($id)
    {
        $modelinner_id = $this->modelinner->get($id);
        $this->loader->sendresponse($modelinner_id);
    }
}
