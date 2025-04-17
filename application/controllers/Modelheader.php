<?php

class Modelheader extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveModelheader()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $modelset_id = $this->modelheader->save($postData, $id);
            $this->loader->sendresponse($modelset_id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteModelheader()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->modelheader->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getModelheader($list_id)
    {
        $where = array();
        $where['modellist_id'] = $list_id;
        $modelheader_id = $this->modelheader->get_by($where);

        $this->loader->sendresponse($modelheader_id);
    }

    public function getModelheaderForUser()
    {
        $modelheader_id = $this->modelheader->get();

        foreach($modelheader_id as $m)
        {
            $m->label = $m->title;
            $m->icon = $m->icon_id;
            $m->router = $m->link;
        }

        $this->loader->sendresponse($modelheader_id);
    }

    public function getModelheaderbyId($id)
    {
        $modelheader_id = $this->modelheader->get($id);
        $this->loader->sendresponse($modelheader_id);
    }
}
