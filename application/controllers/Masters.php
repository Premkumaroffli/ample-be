<?php

class Masters extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

    public function OldgetSidenav()
    {
        if($this->app_users->authenticate())
        {
            $current_user = $this->app_users->getCurrentUser();
            $where = array();
            $where['app_user_id'] = $current_user->id;
            // $where['company_id'] = $current_user->company_id;
            $app_access = $this->app_users_list->get_by($where);
            if(sizeof($app_access) > 0)
            {
                $data = new stdClass;
                $data->nav_data = array();
                $app_access = $app_access[0];
                $data->admin = $app_access->admin;
                $data->nav_data = $this->db->query("select * from (select b.*, ifnull(a.id, 0) as ml_id, ifnull(b.id, 0) as mh_id from modellist a left join modelheader b on a.id = b.modellist_id)final where ml_id in($app_access->app_access_list) and mh_id in($app_access->model_head_access);")->result();
                $d = $this->db->last_query();
                foreach($data->nav_data as $d)
                {
                    $d->modelset = $this->db->query("select * from modelset where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and id in($app_access->model_set_access);")->result();
    
                    foreach($d->modelset as $set)
                    {
                        if($set->model_type == 'collapse')
                        {
                            $set->modelinner = $this->db->query("select * from modelinner where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and modelset_id = $set->id and id in($app_access->model_inner_access);")->result();
                        }
                    }
                }
            }
            else
            {
                $data = new stdClass;
            }

            $this->loader->sendresponse($data);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getSidenav()
    {
        if($this->app_users->authenticate())
        {
            $current_user = $this->app_users->getCurrentUser();
            $where = array();
            $where['email'] = $current_user->email;
            $where['company_id'] = $current_user->company_id;
            $app_access = $this->companies_license_users->get_by($where);
            $access_model = unserialize($app_access[0]->access_blob)->value;
            if(sizeof($app_access) > 0)
            {
                $data = new stdClass;
                $data->admin = $current_user->email == 'prem@gmail.dex' ? 1 : 0;
                $data->nav_data = array();
                $data->nav_data = $this->db->query("select * from (select b.*, ifnull(a.id, 0) as ml_id, ifnull(b.id, 0) as mh_id from modellist a left join modelheader b on a.id = b.modellist_id)final where ml_id in($access_model->modellist_ids) and mh_id in($access_model->modelheader_ids);")->result();
                $d = $this->db->last_query();
                foreach($data->nav_data as $d)
                {
                    $d->modelset = $this->db->query("select * from modelset where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and id in($access_model->modelset_ids);")->result();
    
                    foreach($d->modelset as $set)
                    {
                        if($set->model_type == 'collapse')
                        {
                            $set->modelinner = $this->db->query("select * from modelinner where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and modelset_id = $set->id and id in($access_model->modelinner_ids);")->result();
                        }
                    }
                }
            }
            else
            {
                $data = new stdClass;
            }

            $this->loader->sendresponse($data);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function newgetSidenav()
    {
        if($this->app_users->authenticate())
        {
            $current_user = $this->app_users->getCurrentUser();
            $where = array();
            $where['email'] = $current_user->email;
            $where['company_id'] = $current_user->company_id;
            $app_access = $this->companies_license_users->get_by($where);
            $access_model = unserialize($app_access[0]->access_blob)->value;
            if(sizeof($app_access) > 0)
            {
                $data = new stdClass;
                $data->admin = $current_user->email == 'prem@gmail.dex' ? 1 : 0;
                $data->nav_data = array();
                $data->nav_data = $this->db->query("select * from (select b.*, ifnull(a.id, 0) as ml_id, ifnull(b.id, 0) as mh_id from modellist a left join modelheader b on a.id = b.modellist_id)final where ml_id in($access_model->modellist_ids) and mh_id in($access_model->modelheader_ids);")->result();
                foreach($data->nav_data as $d)
                {
                    $d->modelset = $this->db->query("select * from modelset where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and id in($access_model->modelset_ids);")->result();
    
                    foreach($d->modelset as $set)
                    {
                        if($set->model_type == 'collapse')
                        {
                            $set->modelinner = $this->db->query("select * from modelinner where modelheader_id = $d->mh_id and modellist_id = $d->ml_id and modelset_id = $set->id and id in($access_model->modelinner_ids);")->result();
                        }
                    }
                }
            }
            else
            {
                $data = new stdClass;
            }

            $this->loader->sendresponse($data);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

    public function getModelsList()
    {
        if($this->app_users->authenticate())
        {
            $models_list = $this->modellist->get();

            foreach($models_list as $model)
            {
                $model->value = $model->id;
            }

            $this->loader->sendresponse($models_list);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function getStatesList()
    {
        if($this->app_users->authenticate())
        {
            $states = $this->states->get();

            foreach($states as $state)
            {
                $state->value = $state->id;
            }

            $this->loader->sendresponse($states);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

	public function saveCustomer()
	{
        if($this->app_users->authenticate())
		{
			$customerData = $this->input->post();
	
			$id = isset($customerData['id']) ? $customerData['id'] : null;
	
			$response = $this->customers->save($customerData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}
    
	public function getCustomerslist()
	{
		$response = $this->customers->get();

        $this->loader->sendresponse($response);
	}
    
	public function CustomersSB()
	{
		$response = $this->customers->get();

        foreach($response as $res)
        {
            $res->value = $res->id;
        }

        $this->loader->sendresponse($response);
	}

	public function saveService()
	{
        if($this->app_users->authenticate())
		{
			$serviceData = $this->input->post();
	
			$id = isset($serviceData['id']) ? $serviceData['id'] : null;
	
			$response = $this->services->save($serviceData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}
    
	public function getServiceslist()
	{
		$response = $this->services->get();

        $this->loader->sendresponse($response);
	}
    
	public function ServicesSB()
	{
		$response = $this->services->get();

        foreach($response as $res)
        {
            $res->value = $res->id;
        }

        $this->loader->sendresponse($response);
	}

    public function saveItems()
	{
        if($this->app_users->authenticate())
		{
			$itemData = $this->input->post();
	
			$id = isset($itemData['id']) ? $itemData['id'] : null;
	
			$response = $this->items->save($itemData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}
    
	public function getItemslist()
	{
		$response = $this->items->get();

        $this->loader->sendresponse($response);
	}
    
	public function ItemsSB()
	{
		$response = $this->items->get();

        foreach($response as $res)
        {
            $res->value = $res->id;
        }

        $this->loader->sendresponse($response);
	}
    
    public function saveCatagory()
	{
        if($this->app_users->authenticate())
		{
			$itemData = $this->input->post();
	
			$id = isset($itemData['id']) ? $itemData['id'] : null;
	
			$response = $this->catagory->save($itemData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}
    
	public function getCatagorylist()
	{
		$response = $this->catagory->get();

        $this->loader->sendresponse($response);
	}
    
	public function CatagorySB($trans_type)
	{
		$response = $this->db->query("select id, name from catagory where trans_type = '$trans_type' and status = 1")->result();

        foreach($response as $res)
        {
            $res->value = $res->id;
        }

        $this->loader->sendresponse($response);
	}
    
	public function NewCatagorySB()
	{
		$response = $this->db->query("select id, name from catagory where status = 1")->result();

        foreach($response as $res)
        {
            $res->value = $res->id;
        }

        $this->loader->sendresponse($response);
	}

}
