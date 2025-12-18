<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Code extends CI_Controller
{

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
		if ($this->db->conn_id) {
			echo "Database Connection Successfully";
		} else {
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

		if (!is_dir('./uploads/images/')) {
			mkdir('./uploads/images/', 0777, TRUE);
		}

		if (!empty($_FILES['image']['name'])) {
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
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
		}

		$codeData['image'] = $response['file_path'];
		$response = $this->code_details->save(null, $codeData);

		$this->loader->sendresponse($response);

	}
	public function update_db()
	{
		// Iterate through all models loaded by Load_model
		// Note: $this->loader refers to Load_model instance

		$models = $this->loader->models; // Access public property we just added

		echo "<h1>Starting Database Update...</h1>";

		foreach ($models as $model_name) {
			// Check if model property exists on controller
			if (isset($this->$model_name)) {
				if (method_exists($this->$model_name, 'quries')) {
					echo "Checking model: <strong>$model_name</strong><br>";

					$sql_list = $this->$model_name->quries();

					if (is_array($sql_list)) {
						foreach ($sql_list as $sql) {
							$this->db->db_debug = FALSE; // Disable auto-error
							if ($this->db->query($sql)) {
								echo "Executed: " . htmlspecialchars(substr($sql, 0, 50)) . "... <span style='color:green'>SUCCESS</span><br>";
							} else {
								$err = $this->db->error();
								if (strpos($err['message'], 'already exists') !== false) {
									echo "Skipped (Exists): " . htmlspecialchars(substr($sql, 0, 30)) . "...<br>";
								} else {
									echo "Error: " . $err['message'] . " <span style='color:red'>FAILED</span><br>";
								}
							}
							$this->db->db_debug = TRUE;
						}
					}
				}
			}
		}
		echo "<h2>Update Complete.</h2>";
	}
}
