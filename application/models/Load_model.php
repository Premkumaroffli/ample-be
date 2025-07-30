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
            'acc_income_expense'

        ];

        foreach($models as $m)
        {
            $this->load->model($m.'_model', $m);
        }
    }

    public function sendresponse($data)
    { 
        $response['status'] = 'success';
        $response['result'] = $data;
        print_r(json_encode($response));
       
    }

}
