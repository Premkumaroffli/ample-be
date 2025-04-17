<?php

class Modelset extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveModelset()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $modelset_id = $this->modelset->save($postData, $id);
            $this->loader->sendresponse($modelset_id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteModelset()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->modelset->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getModelset($list_id, $head_id)
    {
        $where = array();
        $where['modellist_id'] = $list_id;
        $where['modelheader_id'] = $head_id;
        $modelset_id = $this->modelset->get_by($where);

        $this->loader->sendresponse($modelset_id);
    }

    public function getModelsetForUser()
    {
        $modelset_id = $this->modelset->get();

        foreach($modelset_id as $m)
        {
            $m->label = $m->title;
            $m->icon = $m->icon_id;
            $m->router = $m->link;
        }

        $this->loader->sendresponse($modelset_id);
    }

    public function getModelsetbyId($id)
    {
        $modelset_id = $this->modelset->get($id);
        $this->loader->sendresponse($modelset_id);
    }
}
