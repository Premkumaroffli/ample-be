<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '../vendor/autoload.php'); // Composer autoload

use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;

class Orders extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
	}


	private function get_specific_sheet_data($filepath, $targetSheetName)
	{
		// Determine file type
		$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filepath);
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

		// If it's a CSV, we can't select sheets by name, we just read it.
		// If it's Excel (Xlsx), we try to load the specific sheet.
		if ($inputFileType == 'Csv') {
			$spreadsheet = $reader->load($filepath);
		} else {
			// For Excel, we list sheet names first to check if ours exists
			$sheetNames = $reader->listWorksheetNames($filepath);

			if (in_array($targetSheetName, $sheetNames)) {
				// Found it! Load ONLY this sheet
				$reader->setLoadSheetsOnly($targetSheetName);
				$spreadsheet = $reader->load($filepath);
			} else {
				// Strategy B: If exact name not found, load the first sheet
				// and we will validate headers later.
				$spreadsheet = $reader->load($filepath);
			}
		}

		$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, false); // Indexed array

		// VALIDATION: Check if this is actually the right file
		// We check Row 1 (Header) for a known column unique to Order Payments
		// Row 1, Column 0 should be "Sub Order No"

		// Sometimes headers are on row 0, sometimes row 1. Let's check the first 5 rows.
		$found = false;
		foreach ($sheetData as $row) {
			// We check if the row contains "Sub Order No" and "Final Settlement Amount"
			if (in_array('Sub Order No', $row) && in_array('Final Settlement Amount', $row)) {
				$found = true;
				break;
			}
		}

		if (!$found) {
			return false; // This is not the correct file/sheet
		}

		return $sheetData;
	}

	public function OrderExcelFile()
	{
		$codeData = $this->input->post();

		$current_db = $this->db->database;
		$current_date = date('Y-m-d');
		// 2. Define Path (e.g., uploads/Site-Visit-2025)
		$upload_path = './uploads/' . $current_db . '/' . 'order_excel/' . $current_date;

		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true); // 0777 permissions, recursive = true
		}

		if (!empty($_FILES['order_excel']['name'])) {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'csv|xls|xlsx';
			$this->load->library('upload', $config);

			$this->upload->initialize($config);
			if ($this->upload->do_upload('order_excel')) {
				$fileData = $this->upload->data();
				$response = [
					'status' => 'success',
					'file_name' => $fileData['file_name'],
					'file_path' => base_url($upload_path . '/' . $fileData['file_name']),
				];
			} else {
				$error = $this->upload->display_errors();
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
		}

		$order_excel_data = $this->upload_excel($response['file_path']);

		foreach ($order_excel_data as $ind => $ord_det) {
			if ($ind > 0) {
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

				$sub_order_id = $ord_det[1];

				$check_sub_order = $this->db->query("select id from acc_orders where sub_order_id = '$sub_order_id '")->row();

				$id = isset($check_sub_order->id) ? $check_sub_order->id : null;

				$response = $this->acc_orders->save($saveData, $id);
			}
		}


		$this->loader->sendresponse($response);

	}

	public function PaymentExcelFile()
	{
		$codeData = $this->input->post();

		$current_db = $this->db->database;
		$current_date = date('Y-m-d');
		// 2. Define Path (e.g., uploads/Site-Visit-2025)
		$relative_path = 'uploads/' . $current_db . '/payment_excel/' . $current_date . '/';
		$server_path = FCPATH . $relative_path;

		if (!is_dir($server_path)) {
			mkdir($server_path, 0755, true);
		}

		if (!empty($_FILES['payment_excel']['name'])) {
			$config['upload_path'] = $server_path;
			$config['allowed_types'] = 'csv|xls|xlsx';
			$this->load->library('upload', $config);

			$this->upload->initialize($config);
			var_dump($_FILES);
			if ($this->upload->do_upload('payment_excel')) {
				$fileData = $this->upload->data();
				$response = [
					'status' => 'success',
					'file_name' => $fileData['file_name'],
					'file_path' => base_url($server_path . '/' . $fileData['file_name']),
				];
			} else {
				$error = $this->upload->display_errors();
			}
			$full_physical_path = $fileData['full_path'];
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
		}

		$payment_excel_data = $this->get_specific_sheet_data($full_physical_path, 'Order Payments');

		if ($payment_excel_data === false) {
			unlink($full_physical_path); // Delete invalid file
			echo json_encode(['status' => 'error', 'message' => 'Sheet "Order Payments" not found in file.']);
			return;
		}

		foreach ($payment_excel_data as $ind => $ord_det) {
			if ($ind > 2) {
				$saveData = array();
				$saveData['sub_order_id'] = $ord_det[0];
				$saveData['order_date'] = $ord_det[1];
				$saveData['dispatch_date'] = $ord_det[2];
				$saveData['product_name'] = $ord_det[3];
				$saveData['sku'] = $ord_det[4];
				$saveData['live_status'] = $ord_det[5];
				$saveData['prod_gst'] = $ord_det[6];
				$saveData['list_price'] = $ord_det[7];
				$saveData['qty'] = $ord_det[8];
				$saveData['trans_id'] = $ord_det[9];
				$saveData['trans_date'] = $ord_det[10];
				$saveData['settle_price'] = $ord_det[11];
				$saveData['price_type'] = $ord_det[12];
				$saveData['sale_price'] = $ord_det[13];
				$saveData['return_price'] = $ord_det[14];
				$saveData['ship_charge'] = $ord_det[27];
				$saveData['tcs'] = $ord_det[32];
				$saveData['tds'] = $ord_det[34];

				$sub_order_id = $ord_det[0];

				$check_sub_order = $this->db->query("select id from acc_payments where sub_order_id = '$sub_order_id '")->row();

				$id = isset($check_sub_order->id) ? $check_sub_order->id : null;

				$response = $this->acc_payments->save($saveData, $id);
			}
		}


		$this->loader->sendresponse($response);

	}

	public function upload_excel($file)
	{
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


	public function getOrderListNew()
	{
		if ($this->app_users->authenticate()) {
			$postData = (object) $this->input->post();

			$data = new StdClass;

			// $data->total_length = $this->income_expense->getIncomeExpList(true, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize),  $postData->pageSize, $postData->isMobile);

			$data->order_list = $this->db->query("select * from acc_orders order by order_date desc limit 10")->result();

			foreach ($data->order_list as $inex) {
				// $inex->category_name = $this->db->query("select name from catagory where id = $inex->category_id")->row()->name;
				// $inex->t_type = ucfirst($inex->type);
			}

			$data->others_data = $this->db->query("select count(id) as value, reason as name from acc_orders group by reason")->result();

			foreach ($data->others_data as $d) {
				$d->name = $d->name . ' - ' . $d->value;
			}

			$this->loader->sendresponse($data);

		} else {
			$this->loader->sendresponse();
		}
	}

	public function getOrderList()
	{
		if ($this->app_users->authenticate()) {
			$data = new StdClass;

			$stateData = new StdClass;

			$stateData->series = array();
			$stateData->labels = array();

			$statewise = $this->db->query("select count(id) as count_id, state from acc_orders group by state")->result();

			foreach ($statewise as $state) {
				$stateData->series[] = (int) $state->count_id;
				$stateData->labels[] = $state->state;
			}

			$this->loader->sendresponse($stateData);
		} else {
			$this->loader->sendresponse();
		}

	}

	public function SaveReturnFolder()
	{

		if ($this->app_users->authenticate()) {
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
			$upload_path = './uploads/' . $current_db . '/' . 'return_claims/' . $current_date . '/' . $clean_folder_name . '/';

			// 3. Create Directory if it doesn't exist
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true); // 0777 permissions, recursive = true
			}

			// 4. Configure Upload Library
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'gif|jpg|png|jpeg|mp4|mov|avi';
			$config['max_size'] = 0; // 0 = no limit (be careful with php.ini limits)

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
				$_FILES['single_file']['name'] = $files['name'][$i];
				$_FILES['single_file']['type'] = $files['type'][$i];
				$_FILES['single_file']['tmp_name'] = $files['tmp_name'][$i];
				$_FILES['single_file']['error'] = $files['error'][$i];
				$_FILES['single_file']['size'] = $files['size'][$i];

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

			foreach ($check_data as $chk) {
				if ($chk->folder_name === $db_data['folder_name'] && $chk->upload_path === $db_data['upload_path']) {
					$check_status = true;
				}
			}

			$response = 1;

			if (!$check_status) {
				$response = $this->return_claim_folders->save($db_data, $id);
			}

			$this->loader->sendresponse($response);

		} else {
			$this->loader->sendresponse();
		}

	}

	public function GetReturnFolders()
	{

		if ($this->app_users->authenticate()) {
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
							if ($file === '.' || $file === '..')
								continue;

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
								'url' => $file_url,
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
		} else {
			$this->loader->sendresponse();
		}
	}

	public function DownloadFolderZip($id)
	{

		if ($this->app_users->authenticate()) {

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

	public function DeleteReturnClaim($folder_id)
	{
		if ($this->app_users->authenticate()) {
			$folder = $this->return_claim_folders->get($folder_id);

			if (empty($folder)) {
				$this->loader->sendresponse(['status' => 'error', 'message' => 'Folder not found']);
				return;
			}

			$dir_path = $folder[0]->upload_path;

			// 3. SAFETY CHECK: Ensure path is within 'uploads'
			// This prevents hackers from deleting system files like '../../index.php'
			if (strpos($dir_path, './uploads/') !== 0) {
				$this->loader->sendresponse(['status' => 'error', 'message' => 'Invalid path security check']);
				return;
			}

			// 4. Delete Physical Files & Directory
			if (is_dir($dir_path)) {
				// CodeIgniter Helper: 'delete_files'
				// Param 2 (true) = Recursive (delete everything inside)
				$this->load->helper('file');
				delete_files($dir_path, true);

				// Remove the now-empty directory
				rmdir($dir_path);
			}

			// 5. Delete Database Record
			$this->return_claim_folders->delete($folder_id);

			$response = 'success';

			$this->loader->sendresponse($response);
		} else {

		}
	}

	public function SplitPdfByContent()
	{
		// 0. Configuration
		$upload_path = './uploads/temp_pdf/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		// 1. Upload File
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'pdf';
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('pdf_file')) {
			echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
			return;
		}

		$file_data = $this->upload->data();
		$file_path = $file_data['full_path'];
		$keywords = $this->input->post('keywords'); // Comma separated
		$is_extract = $this->input->post('extract_data') === 'true';
		$keywords_arr = array_map('trim', explode(',', $keywords));

		// 2. Parse PDF Text
		$parser = new Parser();
		$pdf = $parser->parseFile($file_path);
		$pages = $pdf->getPages();
		$extracted_data = [];
		$split_groups = []; // [ 'keyword' => [page_num, page_num] ]

		// 3. Analyze Pages
		foreach ($pages as $page_index => $page) {
			$page_num = $page_index + 1;
			$text = $page->getText();

			// A. Extraction Logic
			if ($is_extract) {
				// Regex for Meesho Labels (Adjust patterns as needed)
				$sku = '';
				$size = '';
				$order_id = '';

				// Capture SKU (Looking for SKU: or Product Code:)
				if (preg_match('/(?:SKU|Style Code)[:\s]+([A-Z0-9\-_]+)/i', $text, $matches)) {
					$sku = trim($matches[1]);
				}

				// Capture Size (Looking for Size: X)
				if (preg_match('/(?:Size)[:\s]+([0-9A-Z]+)/i', $text, $matches)) {
					$size = trim($matches[1]);
				}

				// Capture Order ID
				if (preg_match('/(?:Order ID)[:\s]+([0-9]+)/i', $text, $matches)) {
					$order_id = trim($matches[1]);
				}

				// Always add page data to JSON
				$extracted_data[] = [
					'page' => $page_num,
					'sku' => $sku,
					'size' => $size,
					'order_id' => $order_id,
					'raw_text' => trim($text) // Include full text
				];

			}

			// B. Splitting Logic
			foreach ($keywords_arr as $kw) {
				if (stripos($text, $kw) !== false) {
					if (!isset($split_groups[$kw])) {
						$split_groups[$kw] = [];
					}
					$split_groups[$kw][] = $page_num;
				}
			}
		}

		// 4. Return Data OR Generate Split PDFs
		if ($is_extract) {
			// 4a. Save to Database
			$tableName = 'pdf_extracted_data';

			// Check if table exists, create if not
			if (!$this->db->table_exists($tableName)) {
				$this->load->dbforge();
				$this->dbforge->add_field([
					'id' => [
						'type' => 'INT',
						'constraint' => 11,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					],
					'file_name' => [
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => TRUE
					],
					'page_num' => [
						'type' => 'INT',
						'constraint' => 11
					],
					'sku' => [
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => TRUE
					],
					'size' => [
						'type' => 'VARCHAR',
						'constraint' => '50',
						'null' => TRUE
					],
					'order_id' => [
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => TRUE
					],
					'raw_text' => [
						'type' => 'TEXT',
						'null' => TRUE
					],
					'created_at' => [
						'type' => 'DATETIME',
						'null' => TRUE
					]
				]);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table($tableName);
			}

			// Batch Insert
			if (!empty($extracted_data)) {
				$insertInfo = [];
				$current_time = date('Y-m-d H:i:s');
				$orig_file_name = $_FILES['pdf_file']['name'];

				foreach ($extracted_data as $row) {
					$insertInfo[] = [
						'file_name' => $orig_file_name,
						'page_num' => $row['page'],
						'sku' => $row['sku'],
						'size' => $row['size'],
						'order_id' => $row['order_id'],
						'raw_text' => $row['raw_text'],
						'created_at' => $current_time
					];
				}

				$this->pdf_extracted_data->save($insertInfo, null);
				// $this->db->insert_batch($tableName, $insertInfo);
			}

			// Return JSON directly
			echo json_encode([
				'status' => 'success',
				'result' => [
					'extracted_data' => $extracted_data
				]
			]);

			// Cleanup
			unlink($file_path);
			return;
		}

		// 5. Generate PDFs for Split Groups (FPDI)
		$zip_files = [];
		foreach ($split_groups as $kw => $page_nums) {
			$new_pdf = new Fpdi();
			$page_count = $new_pdf->setSourceFile($file_path);

			foreach ($page_nums as $p_num) {
				$tpl = $new_pdf->importPage($p_num);

				// Get size of imported page to maintain orientation
				$size = $new_pdf->getTemplateSize($tpl);
				$new_pdf->addPage($size['orientation'], [$size['width'], $size['height']]);

				$new_pdf->useTemplate($tpl);
			}

			$safe_kw = preg_replace('/[^A-Za-z0-9\-]/', '', $kw);
			$out_name = "Split_{$safe_kw}.pdf";
			$out_path = $upload_path . $out_name;
			$new_pdf->Output($out_path, 'F');
			$zip_files[] = $out_path;
		}

		// 6. Create ZIP
		$zip = new ZipArchive();
		$zip_name = 'Split_Files_' . time() . '.zip';
		$zip_path = $upload_path . $zip_name;

		if ($zip->open($zip_path, ZipArchive::CREATE) === TRUE) {
			foreach ($zip_files as $file) {
				$zip->addFile($file, basename($file));
			}
			$zip->close();
		}

		// 7. Return Download URL
		// Relative path from base_url needs to match the upload path
		$download_url = base_url('uploads/temp_pdf/' . $zip_name);

		echo json_encode([
			'status' => 'success',
			'result' => [
				'download_url' => $download_url
			]
		]);

		// Cleanup original PDF (optional, keeping zip for download)
		// unlink($file_path); 
		// Note: You might want a cron job to clean up temp_pdf later
	}

}
