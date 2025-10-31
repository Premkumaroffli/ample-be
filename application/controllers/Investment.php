<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investment extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveInvest()
	{
        if($this->app_users->authenticate())
		{
			$inexData = $this->input->post();
	
			$id = isset($inexData['id']) ? $inexData['id'] : null;
	
			$inv_id = $this->acc_investment->save($inexData, $id);
			
			$cash = isset($inexData['pre_cash']) ? ( $inexData['cash'] - isset($inexData['pre_cash'])) : $inexData['cash'];

			$this->acc_investment_calculate->AddInv($inv_id, $cash);
	
			$this->loader->sendresponse($inv_id);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function getInvestList()
	{
		if($this->app_users->authenticate())
		{
			$inexData = $this->acc_investment->get();

			foreach($inexData as $i)
			{
				$i->pre_cash = $i->cash;
			}
			
			$this->loader->sendresponse($inexData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}

	public function getMaxinv()
	{
		if($this->app_users->authenticate())
		{
			$inv_no = $this->db->query("select max(inv_no) as inv_no from acc_investment")->row()->inv_no;

            $invest_no = (int)$inv_no + 1;
			
			$this->loader->sendresponse($invest_no);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
    
	public function getInvest($id)
    {
        if($this->app_users->authenticate())
        {
			$inexData = $this->acc_investment->get($id);
			$inexData->pre_cash = $inexData->cash;
			$this->loader->sendresponse($inexData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteInvest($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_investment->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

}
