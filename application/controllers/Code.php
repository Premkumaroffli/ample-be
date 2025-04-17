<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code extends CI_Controller {

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

	public function getCodelist()
	{
		$response = $this->code_details->get();

        $this->loader->sendresponse($response);
	}

	public function getCodeDet($code_id)
	{
		$where = array();
		
	}

	public function saveCodeDetails()
	{
		$codeData = $this->input->post();

		print_r($_FILES);

		if(!is_dir('./uploads/images/'))
		{
			mkdir('./uploads/images/', 0777, TRUE);
		}

		if (!empty($_FILES['image']['name'])) 
		{
			$config['upload_path'] = './uploads/images';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx';
			$this->load->library('upload', $config);

			$this->upload->initialize($config);
			if ($this->upload->do_upload('image')) {
				$fileData = $this->upload->data();
				$response = [
					'status' => 'success',
					'file_name' => $fileData['file_name'],
					'file_path' => base_url('uploads/images/' . $fileData['file_name']),
				];
			} else {
				$error = $this->upload->display_errors();
			}
		}
		else {
			echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
		}

		$codeData['image'] = $response['file_path'];
		$response = $this->code_details->save(null, $codeData);

        $this->loader->sendresponse($response);

	}
}
