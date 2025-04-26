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

    public function deleteCompanies($id)
    {
        if($this->app_users->authenticate())
        {
            $this->companies->delete($id);
            $where['company_id'] = $id;
            $this->companies_license_users->delete_by($where);
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

            $this->loader->sendresponse($data);
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

            $company = $this->companies->get($postData['company_id']);

            $model_ids = $company[0]->model_ids;

            if($id == null)
            {
                $accessdata = new StdClass;
                $accessdata->modellist_ids = '';
                $accessdata->modelheader_ids = '';
                $accessdata->modelset_ids = '';
                $accessdata->modelinner_ids = '';
            }
            else
            {
                $accessdata = (object)$postData['access_data'];
            }

            $access_data = new StdClass;
    
            $access_data->model_ids = $model_ids;

            $access_data->value = $accessdata;

            $postData['access_blob'] = serialize($access_data);

            $this->companies_license_users->save($postData, $id);

            $this->loader->sendresponse($postData);

        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function usersModelConfig($user_id)
    {
        if($this->app_users->authenticate())
        {
            $access_data = $this->companies_license_users->get($user_id);

            $model_right = unserialize($access_data[0]->access_blob)->value;

            $company = $this->companies->get($access_data[0]->company_id);

            $model_ids = $company[0]->model_ids;

            $accessdata = $this->db->query("select * from modellist where id in($model_ids);")->result();
    
            foreach($accessdata as $d)
            {
                $d->expand = false;
                $d->modelheader = $this->db->query("select * from modelheader where modellist_id = $d->id")->result();
                $d->task = false;

                foreach($d->modelheader as $hd)
                {
                    $hd->modelset = $this->db->query("select * from modelset where modellist_id = $d->id and modelheader_id = $hd->id")->result();

                    $hd->task = false;

                    foreach($hd->modelset as $ms)
                    {
                        $ms->task = false;

                        $ms->modelinner = $this->db->query("select * from modelinner where modellist_id = $d->id and modelheader_id = $hd->id and modelset_id = $ms->id")->result();

                        $ms->check_inner = 0;

                        foreach($ms->modelinner as $mi)
                        {
                            $mi->task = false;

                            $modelinner_ids = explode(',', $model_right->modelinner_ids);

                            foreach($modelinner_ids as $inner_id)
                            {
                                if($mi->id == $inner_id)
                                {
                                    $mi->task = true;
                                    $ms->check_inner +=1;
                                }

                                if(sizeof($modelinner_ids) == sizeof($ms->modelinner) && sizeof($modelinner_ids) == $ms->check_inner)
                                {
                                    $ms->task = true;
                                    $hd->task = true;
                                    $d->task = true;
                                }
                            }

                        }

                    }

                }

            }

            $data = $accessdata;

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
            foreach($companies_list as $cmp)
            {
                $cmp->model_ids = $cmp->model_ids !== '' ?  explode(',', $cmp->model_ids) : '';
            }
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
            foreach($companies_list as $cmp)
            {
                $cmp->access_blob = unserialize($cmp->access_blob);
            }
            $this->loader->sendresponse($companies_list);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function getCompaniesCurrentUser()
    {
        $postData = json_decode(file_get_contents('php://input'), true);

        $user = $this->app_users->get_by(array('email' => $postData['email']));

        $user = (object)($user[0]);

        $companies_list = $this->db->query("select company_id, (select company from companies where id = company_id) as company_name from companies_license_users where email = '$user->email'")->result();
        // foreach($companies_list as $cmp)
        // {
        //     $cmp->access_blob = unserialize($cmp->access_blob);
        // }
        $this->loader->sendresponse($companies_list);
       
    }
}
