<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_pending extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
	}

	public function getMaxTrans()
	{
		if ($this->app_users->authenticate()) {
			$trans_no = $this->db->query("select max(trans_no) as trans_no from income_expense")->row()->trans_no;

			$trans_no = (int) $trans_no + 1;

			$this->loader->sendresponse($trans_no);

		} else {
			$this->loader->sendresponse();
		}
	}

	public function savePaymentPending()
	{
		if ($this->app_users->authenticate()) {
			$inexData = $this->input->post();

			$id = isset($inexData['id']) ? $inexData['id'] : null;

			$response = $this->payment_pending->save($inexData, $id);

			$this->loader->sendresponse($response);
		} else {
			$this->loader->sendresponse();
		}

	}


	// public function getPaymentPendingList()
	// {
	// 	if ($this->app_users->authenticate()) {
	// 		$data = new StdClass;

	// 		$postData = (object) $this->input->post();

	// 		$inc_exp_data = $this->db->query("select sum(amount) as amount, status from payment_pending group by status")->result();

	// 		$data->total_income = 0;
	// 		$data->total_expense = 0;
	// 		$data->total_available = 0;

	// 		foreach ($inc_exp_data as $inc_exp) {
	// 			if ($inc_exp->status == 'Pending') {
	// 				$data->total_income += $inc_exp->amount;
	// 			} else {
	// 				$data->total_expense += $inc_exp->amount;
	// 			}
	// 		}

	// 		$data->total_available = $data->total_income - $data->total_expense;

	// 		$this->loader->sendresponse($data);

	// 	} else {
	// 		$this->loader->sendresponse();
	// 	}
	// }

	public function getPaymentPendingList()
	{
		if ($this->app_users->authenticate()) {
			$postData = (object) $this->input->post();

			$data = new StdClass;

			$data->total_length = $this->payment_pending->getPaymentPendingList(true, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize), $postData->pageSize, $postData->isMobile);

			$data->payment_pending_data = $this->payment_pending->getPaymentPendingList(false, $postData, $postData->pageIndex, ($postData->pageIndex * $postData->pageSize), $postData->pageSize, $postData->isMobile);

			foreach ($data->payment_pending_data as $inex) {
				$inex->category_name = $this->db->query("select name from catagory where id = $inex->category_id")->row()->name;
				// $inex->t_type = ucfirst($inex->type);
			}

			$data->db = $this->db->database;

			$this->loader->sendresponse($data);

		} else {
			$this->loader->sendresponse();
		}
	}

	public function getPaymentPendingEdit($id)
	{
		if ($this->app_users->authenticate()) {
			// $postData = (object)$this->input->post();

			$data = $this->db->query("select * from payment_pending where id = $id")->row();

			$this->loader->sendresponse($data);

		} else {
			$this->loader->sendresponse();
		}
	}

	public function getIncomeExp($id)
	{
		if ($this->app_users->authenticate()) {
			$inexData = $this->acc_income_expense->get($id);
			$this->loader->sendresponse($inexData);
		} else {
			$this->loader->sendresponse();
		}
	}

	public function deleteIncomeExp($id)
	{
		if ($this->app_users->authenticate()) {
			$this->acc_income_expense->delete($id);
			$this->loader->sendresponse($id);
		} else {
			$this->loader->sendresponse();
		}
	}

}
