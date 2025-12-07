<?php

class Load_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        
    }
   
    public function loadModels() {
         
        $models = array();

        $models = [
            'app_users',
            'app_users_list',
            'app_users_details',
            'code_details',
            'modellist',
            'modelset',
            'modelheader',
            'modelinner',
            'expense_list',
            'expense',
            'companies',
            'companies_license_users',
            'employee',
            'acc_account_type',
            'acc_cash_purpose',
            'acc_orders',
            'acc_income_expense',
            'acc_investment',
            'acc_investment_calculate',
            'states',
            'customers',
            'invoices',
            'invoice_items',
            'services',
            'items',
            'catagory',
            'income_expense',
            'fabric_po',
            'fabric',
            'fabric_color',
            'partner_share',
            'return_claim_folders',
        ];


//   `created_by` varchar(255) NOT NULL,
//   `updated_by` varchar(255) NOT NULL,
//   `created_time` varchar(255) NOT NULL,
//   `updated_time` varchar(255) NOT NULL,
//   `session_id` varchar(255) NOT NULL

        foreach($models as $m)
        {
            $this->load->model($m.'_model', $m);
        }
    }

    public function sendresponse($data=null)
    { 
        $response['status'] = 'success';
        $response['result'] = $data;
        print_r(json_encode($response));
       
    }

}
