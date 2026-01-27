<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function register($data)
    {
        return $this->db->insert('users', $data);
    }

    public function get_user_by_username($username)
    {
        return $this->db->get_where('users', ['username' => $username])->row_array();
    }

    public function get_user_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row_array();
    }

    public function update_user($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
}
