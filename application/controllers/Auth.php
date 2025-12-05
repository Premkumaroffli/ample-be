<?php

class Auth extends CI_Controller {

    private $key = "ample&$@";

	public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
		$this->loader->loadModels();
        $this->load->helper('jwt_helper');
        $this->load->library('session');
    }

    public function checkDuplicate()
    {
        $getData = json_decode(file_get_contents('php://input'), true);

        $oldUserData = array();

        $newUser[$getData['type']] = $getData['value'];

        $oldUserData = $this->app_users->get_by($newUser);

        if(sizeof($oldUserData) == 0)
        {
            $response['status'] = 'New Mail log';
            $response['log'] = true;
        }
        else
        {
            $response['status'] = 'Email Already Exist';
            $response['log'] = false;
        }

        $this->loader->sendresponse($response);

    }

    public function saveSignUp()
    {
        $userData = json_decode(file_get_contents('php://input'));

        $oldUserData = new stdClass;

        $newUser['email'] = $userData->email;
        $newUser['username'] = $userData->username;
        $newUser['phone_no'] = $userData->phone_no;

        $oldUserData = $this->app_users->get_by($newUser);

        if($userData){
            if(sizeof($oldUserData) == 0)
            {
                $id = $this->app_users->saveAppUsers($userData);
                $newUser['app_user_id'] = $id;
                $this->app_users_list->save($newUser);
                $this->app_users_details->save($newUser);
                $response['log'] = true;
                $response['status'] = 'Registration SucessFully';
                $response['result'] = $id;
            }
            else
            {
                $response['log'] = false;
                $response['status'] = 'Signup Error';
                $response['result'] = 'Email or Phone No Already Exits';
            }
        }
        else
        {
            $response['status'] = 'Signup Error';
            $response['result'] = 'Retry the login';
        }

        $this->loader->sendresponse($response);
    }

    public function login()
    {
        $loginData = json_decode(file_get_contents('php://input'), true);

        if ($loginData) {
            $fetchUser = $this->app_users->get_by($loginData);

            if (!empty($fetchUser)) {
                $jwt = new JWT();
                $token = array(
                    'id' => $fetchUser[0]->id,
                    'email' => $fetchUser[0]->email,
                    'username' => $fetchUser[0]->username,
                    'phone_no' => $fetchUser[0]->phone_no,
                    'exp' => time() + 3600  // Token expiry
                );

                $jwt = $jwt->encode($token, $this->key, 'HS256');

                $value =  array(
                    'id' => $fetchUser[0]->id,
                    'email' => $fetchUser[0]->email,
                    'username' => $fetchUser[0]->username,
                    'phone_no' => $fetchUser[0]->phone_no
                );
                
                $response['log'] = true;
                $response['status'] = 'Login Successfully';
                $response['jwt'] = $jwt;
            } else {
                $response['log'] = false;
                $response['status'] = 'Signin Error';
            }
        } else {
            $response['log'] = false;
            $response['status'] = 'Login Error';
        }

        log_message('debug', 'Login function triggered');
        
        $this->loader->sendresponse($response);
    }

    public function loginByCompany()
    {
        $loginData = json_decode(file_get_contents('php://input'), true);
        $fetchUser = $this->app_users->get_by(array('email' => $loginData['email']));
        $datafetchUser = $this->app_users_list->get_by(array('app_user_id' => $fetchUser[0]->id));
        $database = $datafetchUser[0]->data_base;
        $fetchUser = (object)($fetchUser[0]);
        $jwt = new JWT();
        $token = array(
            'id' => $fetchUser->id,
            'email' => $fetchUser->email,
            'username' => $fetchUser->username,
            'phone_no' => $fetchUser->phone_no,
            'company_id' => $loginData['company_id'],
            'database' =>  $database,
            'exp' => time() + 5000  // Token expiry
        );

        $jwt = $jwt->encode($token, $this->key, 'HS256');

        $response['log'] = true;
        $response['status'] = 'Login with company Successfully';
        $response['jwt'] = $jwt;

        $this->loader->sendresponse($response);
    }

    public function get_user() {
        $headers = $this->input->request_headers();
        if (!isset($headers['Authorization'])) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);

        $userData = $this->app_users->validate_jwt($token);

        $user_details = $this->db->query("select * from app_users_details where id = $userData")->result();

        if ($userData) {
            $this->loader->sendresponse($userData);
        } else {
            $this->loader->sendresponse($userData);
        }
    }
    

}