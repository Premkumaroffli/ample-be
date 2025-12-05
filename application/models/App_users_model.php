<?php

class App_users_model extends MY_Model {
    
    public $table = 'app_users';

    private $key = "ample&$@";

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `app_users` (`id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , `password` VARCHAR(255) NOT NULL , `hpassword` VARCHAR(255) NOT NULL , `salt` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `phone_no` VARCHAR(12) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";
    }

    public function generate_salt($length = 32) 
    {
        return bin2hex(random_bytes($length));
    }

    public function hash_password($password ='', $salt) 
    {
        return hash('sha256', $salt . $password);
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
                $decoded =  (object) $decoded;
                $this->switch_db($decoded->database);
                return $decoded;
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

    public function switch_db($db_name) {
        // Get Global Instance (Crucial Step)
        $CI =& get_instance();

        // Close current connection
        if (isset($CI->db)) {
            $CI->db->close();
        }

        // New Config
        $config['hostname'] = 'localhost';
        $config['username'] = 'root';
        $config['password'] = '';
        $config['database'] = $db_name;
        $config['dbdriver'] = 'mysqli';
        $config['pconnect'] = FALSE; // Must be FALSE
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';

        // Connect
        $new_db = $CI->load->database($config, TRUE);

        // Update Controller's DB
        $CI->db = $new_db;

        // Update ALL loaded Models
        foreach ($CI as $key => $object) {
            if (is_object($object) && isset($object->db)) {
                $CI->$key->db = $new_db;
            }
        }
        
        // Update THIS model
        $this->db = $new_db;
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

    public function getCurrentUser() {
        $headers = $this->input->request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $userData = $this->validate_jwt($token);
        return $userData;
    }

    public function getCurrentUserName() {
      $user_name =  $this->getCurrentUser();
      return $user_name->username;
    }

    public function test()
    {
        echo $this->generate_salt();
    }

}
