<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_tracker extends CI_Controller {

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
		$response = $this->expense_list->get();

        $this->loader->sendresponse($response);
	}

	public function getExpense()
	{
		$response = $this->expense->get();

		foreach($response as $r)
		{
			$r->expense_name = $this->db->query("select title from expense_list where id = $r->ex_name")->row()->title;
		}

        $this->loader->sendresponse($response);
	}

	public function getExpenselistSB()
	{
		$response = $this->expense_list->get();

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
}
