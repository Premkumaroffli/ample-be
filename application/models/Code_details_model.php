<?php

class Code_details_model extends MY_Model {
    
    public $table = 'code_details';

    private $key = "ample&$@";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `code_details` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `desc` varchar(255) NOT NULL, `image` varchar(255) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
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
