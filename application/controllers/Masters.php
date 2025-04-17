<?php

class Masters extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

    public function getSidenav()
    {
        if($this->app_users->authenticate())
        {
            $current_user = $this->app_users->getCurrentUser();
            $where = array();
            $where['app_user_id'] = $current_user->id;
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
}
