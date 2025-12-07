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

	public function SaveReturnFolder() {
		
        if($this->app_users->authenticate())
		{
        	// 1. Get Folder Name from POST
			$folder_name = $this->input->post('folder_name');
			
			// Basic sanitization (remove special chars to prevent path injection)
			$clean_folder_name = preg_replace('/[^A-Za-z0-9_\-]/', '', $folder_name);
			
			if (empty($clean_folder_name)) {
				$clean_folder_name = "default_uploads";
			}

			$current_db = $this->db->database;
			$current_date = date('Y-m-d');
			// 2. Define Path (e.g., uploads/Site-Visit-2025)
			$upload_path = './uploads/'.$current_db.'/'.'return_claims/'.$current_date.'/'. $clean_folder_name . '/';

			// 3. Create Directory if it doesn't exist
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true); // 0777 permissions, recursive = true
			}

			// 4. Configure Upload Library
			$config['upload_path']   = $upload_path;
			$config['allowed_types'] = 'gif|jpg|png|jpeg|mp4|mov|avi';
			$config['max_size']      = 0; // 0 = no limit (be careful with php.ini limits)
			
			$this->load->library('upload', $config);

			// 5. Handle Multiple Files
			// $_FILES['files'] comes as an array structure that CI3 doesn't handle natively 
			// in a loop easily without this trick:
			
			$files = $_FILES['files'];
			$count = count($files['name']);
			$success_count = 0;
			$errors = [];

			for ($i = 0; $i < $count; $i++) {
				// Setup $_FILES entry for a single file so CI library can process it
				$_FILES['single_file']['name']     = $files['name'][$i];
				$_FILES['single_file']['type']     = $files['type'][$i];
				$_FILES['single_file']['tmp_name'] = $files['tmp_name'][$i];
				$_FILES['single_file']['error']    = $files['error'][$i];
				$_FILES['single_file']['size']     = $files['size'][$i];

				// Initialize with config (re-init is safer in loops)
				$this->upload->initialize($config);

				if ($this->upload->do_upload('single_file')) {
					$success_count++;
				} else {
					$errors[] = $this->upload->display_errors();
				}
			}

			$db_data = [
			'folder_name' => $clean_folder_name,
			'upload_path' => $upload_path, // Store relative path (better for portability)
			];

			// Make sure to load database library in constructor or autoload

			$id = isset($db_data['id']) ? $db_data['id'] : null;

			$check_data = $this->return_claim_folders->get();

			$check_status = false;

			foreach($check_data as $chk)
			{
				if($chk->folder_name === $db_data['folder_name'] && $chk->upload_path === $db_data['upload_path'] )
				{
					$check_status = true;
				}
			}

			$response = 1;

			if(!$check_status)
			{
				$response = $this->return_claim_folders->save($db_data, $id);
			}
	
			$this->loader->sendresponse($response);
			
		}
		else
        {
            $this->loader->sendresponse();
        }

    }

	public function GetReturnFolders() {
        
        if($this->app_users->authenticate())
        {
            // 1. Get all folder records from Database
            // Assuming your model has a standard 'get' or 'get_all' method
            $folders = $this->return_claim_folders->get(); 

			$folders = $this->db->query("select *, date(created_time) as create_date from return_claim_folders order by id desc")->result();
            
            $response_data = [];

            if (!empty($folders)) {
                foreach ($folders as $folder) {
                    
                    // Convert Object to Array (easier to manipulate)
                    $folder_item = (array) $folder;
                    
                    // 2. Get the physical path (e.g., ./uploads/mydb/return_claims/...)
                    $dir_path = $folder_item['upload_path'];
					$folder_date = $folder_item['create_date'];
                    
                    $file_list = [];

                    // 3. Scan the directory for files
                    if (file_exists($dir_path) && is_dir($dir_path)) {
                        
                        $files = scandir($dir_path);
                        
                        foreach ($files as $file) {
                            // Skip system dots (.) and (..)
                            if ($file === '.' || $file === '..') continue;

                            // 4. Generate Public URL
                            // Remove the dot from './uploads' -> 'uploads/...'
                            $clean_path = ltrim($dir_path, './'); 
                            
                            // Create full URL (e.g., http://yoursite.com/uploads/...)
                            // Ensure 'url' helper is loaded in autoload.php
                            $file_url = base_url($clean_path . $file);
                            
                            // 5. Detect Type (Image or Video)
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $type = in_array($ext, ['mp4', 'mov', 'avi']) ? 'video' : 'image';

                            $file_list[] = [
                                'name' => $file,
                                'url'  => $file_url,
                                'type' => $type
                            ];
                        }
                    }

                    // Attach the files list to the folder data
                    $folder_item['files'] = $file_list;
					$folder_item['created_date'] = $folder_date;
                    $folder_item['file_count'] = count($file_list);
                    
                    $response_data[] = $folder_item;
                }
            }

            // 6. Send JSON Response
            $this->loader->sendresponse($response_data);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

	public function DownloadFolderZip($id) {

    if($this->app_users->authenticate()) {
        
        $folder_id = $id; // Get ID from URL
        
        // 1. Get folder details from DB to find the path
        // Assuming you have a 'get_by_id' method
        $folder = $this->return_claim_folders->get($folder_id);
        
        if (empty($folder)) {
            show_404();
            return;
        }

        $folder_path = $folder[0]->upload_path; // e.g., ./uploads/.../Site-Visit-A/

		print_r($folder_path);

        // 2. Check if folder exists on server
        if (!is_dir($folder_path)) {
            echo "Folder not found on server.";
            return;
        }

        // 3. Load Zip Library
        $this->load->library('zip');

        // 4. Read the directory
        // FALSE = Do not maintain the full directory structure inside the zip
        // TRUE = Maintain structure. Usually, for downloads, you want FALSE so files are at the root of the zip.
        $this->zip->read_dir($folder_path, FALSE); 

        // 5. Download
        // This automatically sets headers and stops script execution
        $zip_name = $folder->folder_name . '.zip';

		print_r($zip_name);
        $this->zip->download($zip_name);
    }

}

}
