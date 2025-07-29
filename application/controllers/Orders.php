<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '../vendor/autoload.php'); // Composer autoload

use PhpOffice\PhpSpreadsheet\IOFactory;

class Orders extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function OrderExcelFile()
	{
		$codeData = $this->input->post();

		if(!is_dir('./uploads/order_excel/'))
		{
			mkdir('./uploads/order_excel/', 0777, TRUE);
		}

		if (!empty($_FILES['order_excel']['name'])) 
		{
			$config['upload_path'] = './uploads/order_excel';
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|csv';
			$this->load->library('upload', $config);

			$this->upload->initialize($config);
			if ($this->upload->do_upload('order_excel')) {
				$fileData = $this->upload->data();
				$response = [
					'status' => 'success',
					'file_name' => $fileData['file_name'],
					'file_path' => base_url('uploads/order_excel/' . $fileData['file_name']),
				];
			} else {
				$error = $this->upload->display_errors();
			}
		}
		else {
			echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
		}

		$order_excel_data = $this->upload_excel($response['file_path']);

		foreach($order_excel_data as $ind => $ord_det)
		{
			if($ind > 0)
			{
				$saveData = array();
				$saveData['reason'] = $ord_det[0];
				$saveData['sub_order_id'] = $ord_det[1];
				$saveData['order_date'] = $ord_det[2];
				$saveData['state'] = $ord_det[3];
				$saveData['product_name'] = $ord_det[4];
				$saveData['sku'] = $ord_det[5];
				$saveData['size'] = $ord_det[6];
				$saveData['qty'] = $ord_det[7];
				$saveData['list_price'] = $ord_det[8];
				$saveData['discount_price'] = $ord_det[9];

				$response = $this->acc_orders->save($saveData, null);
			}
		}


        $this->loader->sendresponse($response);

	}
	
	 public function upload_excel($file) {
		$relativePath = str_replace(base_url(), '', $file);
		$fullPath = FCPATH . $relativePath;

        try {
            $spreadsheet = IOFactory::load($fullPath);
			$sheet = $spreadsheet->getActiveSheet()->toArray();

			return $sheet;

        } catch (Exception $e) {
            echo 'Error loading file: ' . $e->getMessage();
        }
    }

	public function getOrderList()
	{
        if($this->app_users->authenticate())
		{
			$data = new StdClass;

			$stateData = new StdClass;

			$stateData->series = array();
			$stateData->labels = array();

			$statewise = $this->db->query("select count(id) as count_id, state from acc_orders group by state")->result();

			foreach($statewise as $state)
			{
				$stateData->series[] =  (int)$state->count_id;
				$stateData->labels[] =  $state->state;
			}

			$this->loader->sendresponse($stateData);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

}
