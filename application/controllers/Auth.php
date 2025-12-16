<?php

class Auth extends CI_Controller
{

    private $key = "ample&$@";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Load_model', 'loader');
        $this->loader->loadModels();
        $this->load->helper('jwt_helper');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function checkDuplicate()
    {
        $getData = json_decode(file_get_contents('php://input'), true);

        $oldUserData = array();

        $newUser[$getData['type']] = $getData['value'];

        $oldUserData = $this->app_users->get_by($newUser);

        if (sizeof($oldUserData) == 0) {
            $response['status'] = 'New Mail log';
            $response['log'] = true;
        } else {
            $response['status'] = 'Email Already Exist';
            $response['log'] = false;
        }

        $this->loader->sendresponse($response);

    }

    public function saveSignUp()
    {
        $userData = json_decode(file_get_contents('php://input'));

        if (!$userData || !isset($userData->email) || !isset($userData->username) || !isset($userData->phone_no)) {
            $this->loader->sendresponse([
                'log' => false,
                'status' => 'Signup Error',
                'result' => 'Missing required fields'
            ]);
            return;
        }

        // Check for existing user with any of the unique fields (OR logic)
        $this->db->group_start();
        $this->db->where('email', $userData->email);
        $this->db->or_where('username', $userData->username);
        $this->db->or_where('phone_no', $userData->phone_no);
        $this->db->group_end();
        $existingUsers = $this->app_users->get(); // Uses the where clauses set above

        if (!empty($existingUsers)) {
            $this->loader->sendresponse([
                'log' => false,
                'status' => 'Signup Error',
                'result' => 'Email, Username or Phone No Already Exists'
            ]);
            return;
        }

        $this->db->trans_start();

        // Prepare initial user data
        $newUser = [
            'email' => $userData->email,
            'username' => $userData->username,
            'phone_no' => $userData->phone_no,
            'password' => isset($userData->password) ? $userData->password : ''
        ];

        // Pass object to saveAppUsers as it expects an object
        $id = $this->app_users->saveAppUsers((object) $newUser);

        if ($id) {
            $newUserList = [
                'email' => $userData->email,
                'username' => $userData->username,
                'phone_no' => $userData->phone_no,
                'app_user_id' => $id
            ];

            $this->app_users_list->save($newUserList);
            $this->app_users_details->save($newUserList);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $response['log'] = false;
            $response['status'] = 'Signup Error';
            $response['result'] = 'Database transaction failed';
        } else {
            $response['log'] = true;
            $response['status'] = 'Registration Successfully';
            $response['result'] = $id;
        }

        $this->loader->sendresponse($response);
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'));

        if (!$input) {
            $this->loader->sendresponse([
                'log' => false,
                'status' => 'Login Error',
                'result' => 'No data provided'
            ]);
            return;
        }

        $loginValue = isset($input->username) ? $input->username : (isset($input->email) ? $input->email : (isset($input->phone_no) ? $input->phone_no : null));
        $password = isset($input->password) ? $input->password : null;

        if (!$loginValue || !$password) {
            $this->loader->sendresponse([
                'log' => false,
                'status' => 'Login Error',
                'result' => 'Username/Email and Password are required'
            ]);
            return;
        }

        // 1. Find the user (Securely)
        $this->db->group_start();
        $this->db->where('email', $loginValue);
        $this->db->or_where('username', $loginValue);
        $this->db->or_where('phone_no', $loginValue);
        $this->db->group_end();

        $users = $this->app_users->get(); // This returns an array of objects

        if (!empty($users)) {
            $user = $users[0];

            // 2. Verify Password
            // Generate hash using the User's stored SALT and the INPUT password
            $calculatedHash = $this->app_users->hash_password($password, $user->salt);

            if ($calculatedHash === $user->hpassword) {
                // Password Match - Generate Token
                $jwt = new JWT();
                $tokenPayload = array(
                    'id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone_no' => $user->phone_no,
                    'exp' => time() + 3600 * 24 // 24 Hours expiry
                );

                $token = $jwt->encode($tokenPayload, $this->key, 'HS256');

                $response['log'] = true;
                $response['status'] = 'Login Successfully';
                $response['jwt'] = $token;

                // Optional: Return user info (excluding secrets)
                $response['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email
                ];
            } else {
                $response['log'] = false;
                $response['status'] = 'Login Error';
                $response['result'] = 'Invalid Password'; // In production, maybe just say "Invalid Credentials"
            }
        } else {
            $response['log'] = false;
            $response['status'] = 'Login Error';
            $response['result'] = 'User not found';
        }

        $this->loader->sendresponse($response);
    }

    public function loginByCompany()
    {
        $loginData = json_decode(file_get_contents('php://input'), true);
        $fetchUser = $this->app_users->get_by(array('email' => $loginData['email']));
        $fetch = $this->app_users_list->get_by(array('app_user_id' => $fetchUser[0]->id));
        $datafetchUser = $this->app_users_details->get_by(array('app_user_id' => $fetchUser[0]->id));
        $newdatafetchUser = (object) $datafetchUser[0];
        $database = $fetch[0]->data_base;
        $fetchUser = (object) ($fetchUser[0]);
        $newdatafetchUser->email = $fetchUser->email;
        $newdatafetchUser->photoURL = base_url() . ltrim($newdatafetchUser->profile_url, './');
        $newdatafetchUser->displayName = $newdatafetchUser->display_name;
        $jwt = new JWT();
        $token = array(
            'id' => $fetchUser->id,
            'email' => $fetchUser->email,
            'username' => $fetchUser->username,
            'phone_no' => $fetchUser->phone_no,
            'company_id' => $loginData['company_id'],
            'database' => $database,
            'exp' => time() + 5000  // Token expiry
        );

        $jwt = $jwt->encode($token, $this->key, 'HS256');

        $response['log'] = true;
        $response['status'] = 'Login with company Successfully';
        $response['jwt'] = $jwt;
        $response['user'] = $newdatafetchUser;

        $this->loader->sendresponse($response);
    }

    public function get_user()
    {
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

    public function profile()
    {
        // 1. Get the Authorization Header
        $headers = $this->input->request_headers();
        $token = null;
        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) { // Check lowercase just in case
            $token = $headers['authorization'];
        }
        // Remove "Bearer " prefix if present
        if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            $token = $matches[1];
        }
        if (!$token) {
            // No token provided
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(['status' => false, 'message' => 'Unauthorized']));
        }
        try {
            // 2. Decode & Validate Token (Using your JWT library)
            // This key MUST match the one used to generate the token
            $secret_key = $this->config->item('jwt_key');
            $decoded = JWT::decode($token, $secret_key, array('HS256'));
            // 3. Fetch User from Database (using the ID from the token)
            // Assuming your token payload has an 'id' or 'user_id' field
            $user_id = $decoded->id;

            // Call your User Model
            $user = $this->app_users_details->get_by(array('app_user_id' => $user_id));
            if ($user) {
                // 4. Return Success Response (Matches the User interface in Angular)
                $response = [
                    'id' => $user[0]->app_user_id,
                    'displayName' => $user[0]->display_name, // or $user[0]->full_name
                    'email' => $user[0]->email,
                    'photoURL' => $user[0]->profile_url,  // Make sure this path is accessible
                    'role' => $user[0]->user_role // Optional
                ];
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($response));
            } else {
                // Token valid but user not found
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['status' => false, 'message' => 'User not found']));
            }
        } catch (Exception $e) {
            // Token Invalid or Expired
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401) // 401 triggers the signout() in your Angular catchError
                ->set_output(json_encode(['status' => false, 'message' => 'Invalid Token']));
        }
    }

    public function update_profile()
    {
        if ($this->app_users->authenticate()) {
            // Verify JWT Token here (Middleware logic)
            // separate logic for brevity...
            $user_id = $this->input->post('id'); // Or get from JWT
            $display_name = $this->input->post('displayName');

            $current_db = $this->db->database;
            $current_date = date('Y-m-d');
            // 2. Define Path (e.g., uploads/Site-Visit-2025)
            $upload_path = './uploads/' . $current_db . '/' . 'users/';

            $update_data = array(
                'display_name' => $display_name
            );

            // Handle File Upload
            if (!empty($_FILES['file']['name'])) {
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['file_name'] = 'user_' . $user_id . '_' . time();

                // Create folder if not exists
                if (!is_dir($config['upload_path'])) {
                    if (!mkdir($config['upload_path'], 0777, true)) {
                        $response['upload_error'] = "Failed to create directory: " . $config['upload_path'];
                    }
                }

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $update_data['profile_url'] = $upload_path . $uploadData['file_name'];
                } else {
                    $response['upload_error'] = $this->upload->display_errors('', '');
                }
            }


            // Fetch the correct ID (Primary Key) for app_users_details using app_user_id
            $existing_details = $this->app_users_details->get_by(['app_user_id' => $user_id]);

            if (!empty($existing_details)) {
                $details_id = $existing_details[0]->id;
                // Update existing record
                $this->app_users_details->save($update_data, $details_id);

                // Fetch updated data for response
                $det = $this->app_users_details->get($details_id);

                $response['id'] = $details_id; // Returning details ID, or maybe we should return user_id?
                $response['displayName'] = $det[0]->display_name;
                $response['photoURL'] = base_url() . ltrim($det[0]->profile_url, './');
            } else {
                // Handle case where details don't exist (optional, but good practice)
                $update_data['app_user_id'] = $user_id;
                $details_id = $this->app_users_details->save($update_data);
                // ... construct response for new record
                $response['id'] = $details_id;
                $response['displayName'] = $display_name;
                $response['photoURL'] = isset($update_data['profile_url']) ? base_url() . ltrim($update_data['profile_url'], './') : '';
            }

            $response['log'] = true;
            $response['status'] = 'Profile Updated Successfully'; // Better status message

            $this->loader->sendresponse($response);
        } else {
            $this->loader->sendresponse();
        }
    }

}