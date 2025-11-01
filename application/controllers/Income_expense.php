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
    
    
	public function getMaxTrans()
	{
		if($this->app_users->authenticate())
		{
			$trans_no = $this->db->query("select max(trans_no) as trans_no from income_expense")->row()->trans_no;

            $trans_no = (int)$trans_no + 1;
			
			$this->loader->sendresponse($trans_no);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}

	public function saveIncomeExp()
	{
        if($this->app_users->authenticate())
		{
			$inexData = $this->input->post();
	
			$id = isset($inexData['id']) ? $inexData['id'] : null;
	
			$response = $this->income_expense->save($inexData, $id);
	
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
			$postData = (object)$this->input->post();

            $data = new StdClass;

            $data->total_length = $this->income_expense->getIncomeExpList(true, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize),  $postData->pageSize);

			$data->income_expense_data = $this->income_expense->getIncomeExpList(false, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize), $postData->pageSize);
            
			foreach($data->income_expense_data as $inex)
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
