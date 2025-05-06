<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shortener extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Redirect movie page like /m/abc123
    public function redirect($code = '') {
        $query = $this->db->get_where('movie_links', ['short_code' => $code]);
        if ($query->num_rows() > 0) {
            $data['movie'] = $query->row();
            $this->load->view('movie_redirect', $data);
        } else {
            show_404();
        }
    }

    // Function to generate short code and store
    public function create_link() {
        $originalUrl = $this->input->post('url');
        $title = $this->input->post('title');
        $code = substr(md5(time()), 0, 6); // simple random code

        $data = [
            'short_code' => $code,
            'original_url' => $originalUrl,
            'title' => $title
        ];

        $this->db->insert('movie_links', $data);

        echo base_url('m/' . $code);
    }
}
