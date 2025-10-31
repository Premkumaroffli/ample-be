<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
    }

	public function getMaxinv()
	{
		if($this->app_users->authenticate())
		{
			$invoice_no = $this->db->query("select max(invoice_no) as invoice_no from invoices")->row()->invoice_no;

            $invoice_no = (int)$invoice_no + 1;
			
			$this->loader->sendresponse($invoice_no);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
	
	public function saveInvoice()
	{
        if($this->app_users->authenticate())
		{
			$invoiceData = $this->input->post();
	
			$id = isset($invoiceData['id']) ? $invoiceData['id'] : null;
	
			$invId = $this->invoices->save($invoiceData, $id);

			$invoiceData['items'] = json_decode($invoiceData['items']);

			foreach($invoiceData['items'] as $item)
			{
				$itemId = isset($item->id) ? $item->id : null;
				$item->invoice_id = $invId;
	
				$response = $this->invoice_items->save($item, $itemId);

			}
	
			$this->loader->sendresponse($response);
		}
		else
        {
            $this->loader->sendresponse();
        }

	}

	public function getInvoiceList()
	{
		if($this->app_users->authenticate())
		{
			$invData = $this->invoices->get();

			foreach($invData as $i)
			{
				$customer_info = $this->db->query("select name, address from customers where id = $i->customer_id")->row();

				$i->customer_name = $customer_info->name;

				$i->customer_address = $customer_info->address;

				$i->items = $this->db->query("select * from invoice_items where invoice_id = $i->id")->result();

				foreach($i->items as $item)
				{
					$item->service = $this->db->query("select name from services where id = $item->service_id")->row()->name;
				}
			}
			
			$this->loader->sendresponse($invData);

		}
		else
		{
            $this->loader->sendresponse();
		}
	}
    
	public function getInvoice($id)
    {
        if($this->app_users->authenticate())
        {
			$invData = $this->db->query("select * from invoices where id = $id")->row();

			$customer_info = $this->db->query("select name, address from customers where id = $invData->customer_id")->row();

			$invData->customer_name = $customer_info->name;

			$invData->customer_address = $customer_info->address;

			$invData->items = $this->db->query("select * from invoice_items where invoice_id = $invData->id")->result();

			foreach($invData->items as $item)
			{
				$item->service = $this->db->query("select name from services where id = $item->service_id")->row()->name;
			}

			$this->loader->sendresponse($invData);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

    public function deleteInvest($id)
    {
        if($this->app_users->authenticate())
        {
            $this->acc_investment->delete($id);
            $this->loader->sendresponse($id);
        }
        else
        {
            $this->loader->sendresponse();
        }
    }

}
