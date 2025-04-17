<?php

class Companies extends CI_Controller {


	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveCompanies()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);

            $id = isset($postData['id']) ? $postData['id'] : null;

            $this->companies->save($postData, $id);

            $this->loader->sendresponse($postData);

        }
        else
        {
            $this->loader->sendresponse();
        }
    }

	public function saveCompanyUser()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);

            $id = isset($postData['id']) ? $postData['id'] : null;

            $this->companies_license_users->save($postData, $id);

            $this->loader->sendresponse($postData);

        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteModelset($id)
    {
        if($this->app_users->authenticate())
        {
            $this->modelset->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function getUserConfig()
    {
        $users = $this->db->query("select email, username from app_users")->result();

        foreach($modelset_id as $m)
        {
            $m->icon = $this->db->query("select * from app_users")->result();
        }
        
        $this->loader->sendresponse($error);
    }

    public function usersConfig()
    {
        if($this->app_users->authenticate())
        {
            $getData =(object)$this->input->get();
            $data = $this->db->query("select *, (select email from app_users where id = app_user_id) as email from app_users_list;")->result();

            foreach($data as $d)
            {
            }

            $this->loader->sendresponse($data);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function usersModelConfig($id)
    {
        if($this->app_users->authenticate())
        {
            $access = $this->app_users_list->get($id);

            $model_inner_access = explode(',', $access[0]->model_inner_access);

            $data = $this->db->query("select * from modellist;")->result();

            foreach($data as $d)
            {
                $d->expand = false;
                $d->modelheader = $this->db->query("select * from modelheader where modellist_id = $d->id")->result();
                $d->task = false;
                $head_check = [];
                foreach($d->modelheader as $hd)
                {
                    $hd->modelset = $this->db->query("select * from modelset where modellist_id = $d->id and modelheader_id = $hd->id")->result();

                    $hd->task = false;

                    $set_check = [];

                    foreach($hd->modelset as $ms)
                    {
                        $inner_check = [];

                        $ms->task = false;

                        $ms->modelinner = $this->db->query("select * from modelinner where modellist_id = $d->id and modelheader_id = $hd->id and modelset_id = $ms->id")->result();

                        foreach($ms->modelinner as $mi)
                        {
                            $mi->task = false;

                            if(in_array($mi->id, $model_inner_access))
                            {
                                $mi->task = true;
                                array_push($inner_check, $mi);
                            }
                        }

                        if(sizeof($inner_check) === sizeof($ms->modelinner))
                        {
                            $ms->task = true;
                        }

                        if($ms->task)
                        {
                            array_push($set_check, $ms);
                        }

                    }

                    if(sizeof($set_check) === sizeof($hd->modelset))
                    {
                        $hd->task = true;
                    }

                    if($hd->task)
                    {
                        array_push($head_check, $hd);
                    }

                }

                if(sizeof($head_check) === sizeof($d->modelheader))
                {
                    $d->task = true;
                }
            }
            $this->loader->sendresponse($data);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function getCompanies()
    {
        if($this->app_users->authenticate())
        {
            $companies_list = $this->companies->get();
            $this->loader->sendresponse($companies_list);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function getCompaniesUser($company_id)
    {
        if($this->app_users->authenticate())
        {
            $companies_list = $this->db->query("select * from companies_license_users where company_id = $company_id")->result();
            $this->loader->sendresponse($companies_list);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }
}
