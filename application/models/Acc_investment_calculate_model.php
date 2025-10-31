<?php

class Acc_investment_calculate_model extends MY_Model {
    
    public $table = 'acc_investment_calculate';
    public $primary_key = 'id';

    private $key = "ample&$@";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

     public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `acc_investment_calculate` ( `id` int NOT NULL AUTO_INCREMENT, `inv_id` int NOT NULL, `inv_amt` int NOT NULL, `credit` int NOT NULL, `debit` int NOT NULL, s_debtor int NOT NULL, s_creditor int NOT NULL, inv_cal_amt int NOT NULL, `created_by` varchar(255) NOT NULL, `updated_by` varchar(255) NOT NULL, `created_time` varchar(255) NOT NULL, `updated_time` varchar(255) NOT NULL, `session_id` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB ;";
    }

    public function updateTrigger($id)
    {
        $table = $this->acc_investment_calculate->get($id);
    }

    public function AddInv($inv_id, $qty)
    {
        $where = array();

        $where['inv_id'] = $inv_id;
        
        $oldData = $this->acc_investment_calculate->get_by($where);
        
        $data = array();

        if(sizeof($oldData) > 0)
        {
            $data['inv_id'] = $inv_id;
            $data['inv_cal_amt'] = $oldData[0]->inv_cal_amt + $qty;
            $this->acc_investment_calculate->check_save_by($where, $data);
        }
        else
        {
            $data['inv_id'] = $inv_id;
            $data['inv_cal_amt'] = $qty;
            $this->acc_investment_calculate->save($where);
        }
    }



}
