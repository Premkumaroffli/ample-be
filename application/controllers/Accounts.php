<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function checkDbConnection()
	{
		if($this->db->conn_id)
		{
			echo "Database Connection Successfully";
		}
		else
		{
			echo "Database Not Connected";
		}
	}

	public function getExpenselist()
	{
		$where = array();
		$current_user_name = $this->app_users->getCurrentUserName();
		$where['created_by'] = $current_user_name;
		$response = $this->expense_list->get_by($where);

        $this->loader->sendresponse($response);
	}

	public function getExpense()
	{
		$where = array();
		$current_user_name = $this->app_users->getCurrentUserName();
		$where['created_by'] = $current_user_name;
		$response = $this->expense->get_by($where);

		foreach($response as $r)
		{
			$r->expense_name = $this->db->query("select title from expense_list where id = $r->ex_name")->row()->title;
		}

        $this->loader->sendresponse($response);
	}

	public function getExpenselistSB()
	{
		$current_user = $this->app_users->getCurrentUser();
		$where = array();
		$where['created_by'] = $current_user->username;
		$response = $this->expense_list->get_by($where);

		foreach($response as $r)
		{
			$r->name = $r->title;
			$r->value = $r->id;
		}

        $this->loader->sendresponse($response);
	}

	public function getExpenseDet($code_id)
	{
		$where = array();
		
	}

    public function deleteExpenseList()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->expense_list->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

	public function saveExpenseDetails()
	{
        if($this->app_users->authenticate())
		{
			$codeData = $this->input->post();

			// print_r($_FILES);

			// if(!is_dir('./uploads/images/'))
			// {
			// 	mkdir('./uploads/images/', 0777, TRUE);
			// }

			// if (!empty($_FILES['image']['name'])) 
			// {
			// 	$config['upload_path'] = './uploads/images';
			// 	$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx';
			// 	$this->load->library('upload', $config);

			// 	$this->upload->initialize($config);
			// 	if ($this->upload->do_upload('image')) {
			// 		$fileData = $this->upload->data();
			// 		$response = [
			// 			'status' => 'success',
			// 			'file_name' => $fileData['file_name'],
			// 			'file_path' => base_url('uploads/images/' . $fileData['file_name']),
			// 		];
			// 	} else {
			// 		$error = $this->upload->display_errors();
			// 	}
			// }
			// else {
			// 	echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
			// }

			// $codeData['image'] = $response['file_path'];

			$id = isset($codeData['id']) ? $codeData['id'] : null;

			$response = $this->expense_list->save($codeData, $id);

			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function saveExpense()
	{
        if($this->app_users->authenticate())
		{
			$codeData = $this->input->post();
	
			$id = isset($codeData['id']) ? $codeData['id'] : null;
	
			$response = $this->expense->save($codeData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function deleteExpense()
    {
        if($this->app_users->authenticate())
        {
            $postData = json_decode(file_get_contents('php://input'), true);
            $id = isset($postData['id']) ? $postData['id'] : null;
            $this->expense->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }

    }

	// account type

	public function saveAccountType()
	{
        if($this->app_users->authenticate())
		{
			$accData = $this->input->post();
	
			$id = isset($accData['id']) ? $accData['id'] : null;
	
			$response = $this->acc_account_type->save($accData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function getAccountTypeList()
	{
		if($this->app_users->authenticate())
		{
			$accData = $this->acc_account_type->get();
			
			$this->loader->sendresponse($accData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
	
	public function getAccountType($id)
    {
        if($this->app_users->authenticate())
        {
			$accData = $this->acc_account_type->get($id);
			$this->loader->sendresponse($accData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteAccountType($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_account_type->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

	// account type

	// cash purpose

	public function saveCashPurpose()
	{
        if($this->app_users->authenticate())
		{
			$accData = $this->input->post();
	
			$id = isset($accData['id']) ? $accData['id'] : null;
	
			$response = $this->acc_cash_purpose->save($accData, $id);
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function getCashPurposeList()
	{
		if($this->app_users->authenticate())
		{
			$accData = $this->acc_cash_purpose->get();

			foreach($accData as $acc)
			{
				$acc->account_type = $this->db->query("select name from acc_account_type where id = $acc->account_type_id")->row()->name;
			}
			
			$this->loader->sendresponse($accData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}

	public function getCashPurposeSB($id)
	{
		if($this->app_users->authenticate())
		{
			$filter['account_type_id'] = $id;

			$accData = $this->acc_cash_purpose->get_by($filter);

			foreach($accData as $acc)
			{
				$acc->account_type = $this->db->query("select name from acc_account_type where id = $acc->account_type_id")->row()->name;
			}
			
			$this->loader->sendresponse($accData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
	
	public function getCashPurpose($id)
    {
        if($this->app_users->authenticate())
        {
			$accData = $this->acc_cash_purpose->get($id);
			$this->loader->sendresponse($accData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteCashPurpose($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_cash_purpose->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

	// cash purpose


}
