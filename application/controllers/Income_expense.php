<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_expense extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function saveIncomeExp()
	{
        if($this->app_users->authenticate())
		{
			$inexData = $this->input->post();
	
			$id = isset($inexData['id']) ? $inexData['id'] : null;
	
			$response = $this->acc_income_expense->save($inexData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function getIncomeExpList()
	{
		if($this->app_users->authenticate())
		{
			$inexData = $this->acc_income_expense->get();
            
			foreach($inexData as $inex)
			{
				$inex->account_type = $this->db->query("select name from acc_account_type where id = $inex->acc_type_id")->row()->name;
				$inex->cash_purpose = $this->db->query("select name from acc_cash_purpose where id = $inex->cash_pur_id")->row()->name;
			}
			
			$this->loader->sendresponse($inexData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
	
	public function getIncomeExp($id)
    {
        if($this->app_users->authenticate())
        {
			$inexData = $this->acc_income_expense->get($id);
			$this->loader->sendresponse($inexData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteIncomeExp($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_income_expense->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

}
