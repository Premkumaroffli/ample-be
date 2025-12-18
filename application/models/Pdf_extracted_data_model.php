<?php

class pdf_extracted_data_model extends MY_Model
{

    public $table = 'pdf_extracted_data';
    public $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('upload');
        $this->load->database();
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `pdf_extracted_data` (
        id INT(11) NOT NULL AUTO_INCREMENT,
        `file_name` VARCHAR(255) DEFAULT NULL,
        `page_num` INT(11) NOT NULL,
        `sku` VARCHAR(100) DEFAULT NULL,
        `size` VARCHAR(50) DEFAULT NULL,
        `order_id` VARCHAR(100) DEFAULT NULL,
        `raw_text` TEXT DEFAULT NULL,
        `created_at` DATETIME DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ;";
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
