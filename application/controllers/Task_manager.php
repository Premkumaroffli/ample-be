<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_manager extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
	}

	// Save or Update a Task
	public function save()
	{
		if ($this->app_users->authenticate()) {
			$data = $this->input->post();

			$id = isset($data['id']) ? $data['id'] : null;

			// Set basic fields if new
			if (!$id) {
				$data['created_by'] = $this->session->userdata('id'); // Assuming session set by auth
				$data['created_time'] = date('Y-m-d H:i:s');
				$data['status'] = isset($data['status']) ? $data['status'] : 0; // Default pending
			}

			$data['updated_by'] = $this->session->userdata('id');
			$data['updated_time'] = date('Y-m-d H:i:s');

			$response = $this->task_manager->save($data, $id);

			$this->loader->sendresponse($response);
		} else {
			$this->loader->sendresponse();
		}
	}

	public function saveTask()
	{
		if ($this->app_users->authenticate()) {
			$inexData = $this->input->post();

			$id = isset($inexData['id']) ? $inexData['id'] : null;

			$response = $this->task_manager->save($inexData, $id);

			$this->loader->sendresponse($response);
		} else {
			$this->loader->sendresponse();
		}

	}
	public function getTaskListdata()
	{
		if ($this->app_users->authenticate()) {
			$data = new StdClass;

			$postData = (object) $this->input->post();

			$this->checkAndRunDailyUpdate();

			$task_data = $this->db->query("select * from task_manager")->result();

			foreach ($task_data as $task) {
				$task->status = intval($task->status);
			}

			$data->task_data = $task_data;

			$this->loader->sendresponse($data);

		} else {
			$this->loader->sendresponse();
		}
	}




	// LIST Tasks - Support filtering
	// Automation Logic
	// Automation Logic
	// CRON Endpoint for Hostinger
	// Call via: curl "http://yourdomain.com/index.php/task_manager/run_cron_updates?key=SecureCronKey2025"
	public function run_cron_updates()
	{
		$key = $this->input->get('key');
		$valid_key = "SecureCronKey2025"; // Change this to something secret

		if ($key !== $valid_key) {
			show_error("Unauthorized Access", 401);
			return;
		}

		$today = date('Y-m-d');

		// Find ALL users who have daily tasks
		$users = $this->db->distinct()
			->select('created_by')
			->from('task_manager')
			->where('type', 'daily')
			->get()->result();

		$count = 0;
		foreach ($users as $u) {
			// Re-use the generation logic for each user
			if (!empty($u->created_by)) {
				$this->checkAndRunDailyUpdateForUser($u->created_by, $today);
				$count++;
			}
		}

		echo json_encode(["status" => "success", "message" => "Cron executed. Checked $count users."]);
	}

	// Automation Logic
	public function checkAndRunDailyUpdate()
	{
		$today = date('Y-m-d');
		$currentUser = $this->get_current_user_name();

		if (!$currentUser)
			return; // Cannot generate tasks for unknown user

		$this->checkAndRunDailyUpdateForUser($currentUser, $today);
	}

	private function checkAndRunDailyUpdateForUser($user, $date)
	{
		// 1. Check if we have already run updates for THIS user today? 
		$todays_daily_tasks = $this->db->like('created_time', $date)
			->where('type', 'daily')
			->where('created_by', $user)
			->count_all_results('task_manager');

		if ($todays_daily_tasks == 0) {
			$this->generateDailyTasks($date, $user);
		}
	}

	private function generateDailyTasks($date, $currentUser)
	{
		// Logic: specific to "Option A" (Recurring) 
		// Find all distinct task templates (tasks marked as daily) from the past
		// And create new copies for today.

		// This is a naive implementation; strictly we should have a 'task_templates' table,
		// but often in simple apps the "last entry" serves as the template.

		// Strategy: Get the MOST RECENT entry for each 'daily' task name.
		$sql = "SELECT * FROM daily_task WHERE created_by = '$currentUser'";
		$templates = $this->db->query($sql)->result();


		foreach ($templates as $tpl) {
			// Double check we haven't already created this task today (in case of partial runs)
			$exists = $this->db->like('created_time', $date)
				->where('task', $tpl->task)
				->where('type', 'Daily')
				->where('created_by', $currentUser)
				->count_all_results('task_manager');

			if ($exists == 0) {
				$new_task = array(
					'task' => $tpl->task,
					'type' => 'Daily',
					'status' => 0, // Reset to pending
					'created_by' => $currentUser, // Explicitly set for Cron usage
					'start_time' => '', // Copy schedule
					'end_time' => '',
					'session_id' => '' // Carry over
				);

				$this->task_manager->save($new_task);
			}
		}
	}

	private function get_current_user_name()
	{
		$headers = $this->input->request_headers();
		if (isset($headers['Authorization'])) {
			$token = str_replace('Bearer ', '', $headers['Authorization']);
			// MY_Model uses "new JWT()" so we assume it's available.
			if (class_exists('JWT')) {
				try {
					$jwt = new JWT();
					$key = "ample&$@"; // Hardcoded key from MY_Model
					$decoded = $jwt->decode($token, $key, true);
					return isset($decoded->username) ? $decoded->username : null;
				} catch (Exception $e) {
					return null;
				}
			}
		}
		return null; // OR return $this->session->userdata('username') if available as fallback
	}

}
