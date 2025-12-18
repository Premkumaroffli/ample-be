<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{

    protected $primary_key;
    protected $table;  // The table name
    protected $before_insert = ['set_created_data'];
    protected $before_update = ['set_updated_data'];
    private $key = "ample&$@";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    protected function set_created_data($data)
    {
        $data['created_time'] = date('Y-m-d H:i:s');
        $data['created_by'] = $this->get_current_user();
        $data['updated_time'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $this->get_current_user();
        return $data;
    }

    protected function set_updated_data($data)
    {
        $data['updated_time'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $this->get_current_user();
        return $data;
    }

    protected function get_current_user()
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $jwt = new JWT();
            try {
                $decoded = $jwt->decode($token, $this->key, true);
                return isset($decoded->username) ? $decoded->username : 'Unknown User';
            } catch (Exception $e) {
                return null;
            }
        }
    }

    private function _run_before_callbacks($type, $data)
    {
        $callbacks = ($type === 'insert') ? $this->before_insert : $this->before_update;
        foreach ($callbacks as $callback) {
            if (method_exists($this, $callback)) {
                $data = call_user_func([$this, $callback], $data);
            }
        }
        return $data;
    }

    public function save($data, $id = NULL, $insert_data = true)
    {
        $data = (array) $data;
        $table_column = $this->db->list_fields($this->table);
        $filter_data = array_intersect_key($data, array_flip($table_column));

        if ($id == NULL) {
            if ($insert_data) {
                $filter_data = $this->_run_before_callbacks('insert', $filter_data);
            }
            $this->db->set($filter_data);
            $this->db->insert($this->table, $filter_data);
            return $this->db->insert_id();
        } else {
            if ($insert_data) {
                $filter_data = $this->_run_before_callbacks('update', $filter_data);
            }
            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table, $filter_data);
            return $id;
        }
    }

    public function check_save_by($filter_array = [], $insert_data = [])
    {
        $table_column = $this->db->list_fields($this->table);

        $filter_data = array_intersect_key($filter_array, array_flip($table_column));
        $insert_filter_data = array_intersect_key($insert_data, array_flip($table_column));

        foreach ($filter_data as $k => $v) {
            $this->db->where($k, $v);
        }

        $count = $this->db->get($this->table)->num_rows();

        if ($count > 0) {
            $this->db->query("SET SQL_SAFE_UPDATES = 0");
            foreach ($filter_data as $k => $v) {
                $this->db->where($k, $v);
            }
            $this->db->update($this->table, $insert_filter_data);
            $this->db->query("SET SQL_SAFE_UPDATES = 1");
            return $this->db->affected_rows();
        } else {
            $this->db->set($insert_filter_data);
            $this->db->insert($this->table, $insert_filter_data);
            return $this->db->insert_id();
        }
    }

    public function select_column($column = '')
    {
        if (!empty($column)) {
            $this->db->select($column);
            $column_data = $this->db->get($this->table)->result();
            return $column_data;
        }
    }

    public function get($id = 0)
    {
        if ($id > 0) {
            $this->db->where('id', $id);
            $query = $this->db->get($this->table)->result();
        } else {
            $query = $this->db->get($this->table)->result();
        }
        return $query;
    }

    public function get_by($array = [])
    {
        foreach ($array as $k => $v) {
            if (!empty($k) && !empty($v)) {
                $this->db->where($k, $v);
            }
        }
        $query = $this->db->get($this->table)->result();

        return $query;
    }

    public function delete($id = 0)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return true;
    }

    public function delete_by($array = [])
    {
        foreach ($array as $k => $v) {
            if (!empty($k) && !empty($v)) {
                $this->db->where($k, $v);
            }
        }
        $query = $this->db->delete($this->table);

        return true;
    }
}
