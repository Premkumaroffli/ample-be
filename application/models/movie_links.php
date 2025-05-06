<?php

class Expense_model extends MY_Model {
    
    public $table = 'expense';
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

        $quries[] = "CREATE TABLE `movie_links` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `short_code` VARCHAR(10) NOT NULL,
        `original_url` TEXT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        );";
    }

}
