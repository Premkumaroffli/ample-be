<?php

class Daily_task_model extends MY_Model
{

    public $table = 'daily_task';
    public $primary_key = 'id';

    private $key = "ample&$@";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('jwt_helper');
    }

    public function quries()
    {
        $quries = array();

        $quries[] = "CREATE TABLE `daily_task` (
        id INT(11) NOT NULL AUTO_INCREMENT,
        `task` VARCHAR(255) DEFAULT NULL,
        `status` INT(11) NOT NULL,
        `created_by` varchar(255) NOT NULL,
        `updated_by` varchar(255) NOT NULL,
        `created_time` varchar(255) NOT NULL,
        `updated_time` varchar(255) NOT NULL,
        `session_id` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ;";

        return $quries;
    }

}
