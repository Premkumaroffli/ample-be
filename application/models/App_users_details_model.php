<?php

class App_users_details_model extends MY_Model {
    
    public $table = 'app_users_details';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
        $this->load->library('upload');
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `app_users_details` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `app_user_id` varchar(255) NOT NULL,
                `user_role` varchar(255) NOT NULL,
                `display_name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone_no` varchar(255) NOT NULL,
                `profile_url` varchar(255) NOT NULL,
                `user_theme` varchar(255) NOT NULL,
                `created_by` varchar(255) NOT NULL,
                `updated_by` varchar(255) NOT NULL,
                `created_time` varchar(255) NOT NULL,
                `updated_time` varchar(255) NOT NULL,
                `session_id` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB;"
            ;
    }

    public function test()
    {
        echo $this->generate_salt();
    }

    // public function upload() {
    //     $config['upload_path'] = './uploads/';
    //     $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx';
    //     $config['max_size'] = 2048; // 2MB

    //     $this->upload->initialize($config);

    //     if (!$this->upload->do_upload('image')) {
    //         $response = ['status' => 'error', 'message' => $this->upload->display_errors()];
    //         echo json_encode($response);
    //     } else {
    //         $fileData = $this->upload->data();

    //         // Save to database
    //         $data = [
    //             'file_name' => $fileData['file'],
    //             'file_path' => base_url('uploads/' . $fileData['file']),
    //             'uploaded_at' => date('Y-m-d H:i:s'),
    //         ];

    //         echo json_encode($response);
    //     }
    // }

}
