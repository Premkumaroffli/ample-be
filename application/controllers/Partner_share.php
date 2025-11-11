<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner_share extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }
    
    
	public function getMaxTrans()
	{
		if($this->app_users->authenticate())
		{
			$trans_no = $this->db->query("select max(trans_no) as trans_no from partner_share")->row()->trans_no;

            $trans_no = (int)$trans_no + 1;
			
			$this->loader->sendresponse($trans_no);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}

	public function savePartnerShare()
	{
        if($this->app_users->authenticate())
		{
			$partnerShareData = $this->input->post();
	
			$id = isset($partnerShareData['id']) ? $partnerShareData['id'] : null;
	
			$response = $this->partner_share->save($partnerShareData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}
    
	public function getPartnerSharedata()
	{
        $data = new StdClass;

        $postData = (object)$this->input->post();

        $inc_exp_data = $this->db->query("select (select name from customers where id = partner_id) as name, amount from partner_share order by id desc limit 4")->result();

		foreach($inc_exp_data as $d)
		{
			$d->amount = (int)($d->amount);
		}

        $this->loader->sendresponse($inc_exp_data);
	}
    
	public function getPartnerSharedataList()
	{
        $data = new StdClass;

        $postData = (object)$this->input->post();

		$data->partner_list = $this->db->query("select (select name from customers where id = partner_id) as name, partner_id as value, sum(amount) as total_amount from partner_share group by partner_id order by id desc")->result();

		foreach($data->partner_list as $d)
		{
			$d->name = $d->name.' - '.(int)($d->total_amount);
		}

        $data->inc_exp_data = $this->db->query("select (select name from customers where id = partner_id) as name, partner_id, amount, created_time from partner_share order by id desc")->result();

		foreach($data->inc_exp_data as $d)
		{
			$d->amount = (int)($d->amount);
		}

        $this->loader->sendresponse($data);
	}

	public function getPartnerShareList()
	{
		if($this->app_users->authenticate())
		{
			$postData = (object)$this->input->post();

            $data = new StdClass;

            $data->total_length = $this->partner_share->getPartnerShareList(true, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize),  $postData->pageSize, $postData->isMobile);

			$data->partner_share_data = $this->partner_share->getPartnerShareList(false, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize), $postData->pageSize, $postData->isMobile);
            
			foreach($data->partner_share_data as $inex)
			{
				$inex->category_name = $this->db->query("select name from catagory where id = $inex->category_id")->row()->name;
				$inex->t_type = ucfirst($inex->type);
			}
			
			$this->loader->sendresponse($data);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}

	public function getPartnerShareEdit($id)
	{
		if($this->app_users->authenticate())
		{
			// $postData = (object)$this->input->post();

            $data = $this->db->query("select *, (select name from catagory where id = category_id) as category_name from partner_share where id = $id")->row();
            
			// foreach($data->partner_share_data as $inex)
			// {
			// 	$inex->category_name = $this->db->query("select name from catagory where id = $inex->category_id")->row()->name;
			// 	$inex->t_type = ucfirst($inex->type);
			// }
			
			$this->loader->sendresponse($data);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
	
	public function getPartnerShare($id)
    {
        if($this->app_users->authenticate())
        {
			$partnerShareData = $this->acc_partner_share->get($id);
			$this->loader->sendresponse($partnerShareData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deletePartnerShare($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_partner_share->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

}
