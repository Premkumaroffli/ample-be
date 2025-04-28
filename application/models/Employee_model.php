<?php

class Employee_model extends MY_Model {
    
    public $table = 'employee';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `employee` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `status` int(11) NOT NULL,
                    `created_by` varchar(255) NOT NULL,
                    `updated_by` varchar(255) NOT NULL,
                    `created_time` varchar(255) NOT NULL,
                    `updated_time` varchar(255) NOT NULL,
                    `session_id` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB;";
    }

    public function checkUserData($type = '', $filter)
    {
        $user_data = $this->db->get_where($this->table, array($type => $filter))->row_array();
        
        if($user_data && sizeof($user_data) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
       
    }

    public function getUserData($type = '', $filter)
    {
        $user_data = $this->db->get_where($this->table, array($type => $filter))->row();
        if($user_data)
        {
            return $user_data;
        }
        else
        {
            return '';
        }
    }

    public function checkUser()
    {
        $getAuthUser = $this->authenticate();
        $checkEmail = $this->app_users->getUserData('email', $getAuthUser->email);

        if(isset($checkEmail->email))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function deleteExpenseList($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
        
    }
   
    public function saveAppUsers($userData='') 
    {
        $salt = $this->generate_salt();
        $hash_password = $this->hash_password($userData->password, $salt);
        $userData->salt = $salt;
        $userData->hpassword = $hash_password;
        
        $id = $this->db->insert($this->table, $userData);

        return $id;
    }

    public function checkLoggedUser($loginData='')
    {
        $login_email = $loginData->email;
        $login_password = $loginData->password;
        $userData = $this->getUserData('email', $login_email);
        $check_email = $this->checkUserData('email', $login_email);

        if($userData !== '')
        {
            $salt = $userData->salt;
            $password = $userData->hpassword;
    
            $login_hpassword = $this->hash_password($login_password, $salt);
    
            if($check_email && $login_hpassword === $password)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }

    }

    public function authenticate() {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) 
        {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $jwt = new JWT();
            try {
                $decoded = $jwt->decode($token, $this->key, true);
                return (object) $decoded;
            } catch (Exception $e) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_status_header(401)
                     ->set_output(json_encode(array('error' => 'Unauthorized')));
                return false;
            }
        }
        else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_status_header(401)
                 ->set_output(json_encode(array('error' => 'Unauthorized')));
            return false;
        }
    }

    public function validate_jwt($token) {

        $jwt = new JWT();
        try {
            $decoded = $jwt->decode($token, $this->key, true);
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public function test()
    {
        echo $this->generate_salt();
    }

}
