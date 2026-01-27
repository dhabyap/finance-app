<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_transactions($user_id, $limit = null)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('transaction_date', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get('transactions')->result_array();
    }

    public function add_transaction($data)
    {
        return $this->db->insert('transactions', $data);
    }

    public function get_transaction($id)
    {
        return $this->db->get_where('transactions', ['id' => $id])->row_array();
    }

    public function update_transaction($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('transactions', $data);
    }

    public function delete_transaction($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('transactions');
    }

    public function get_total_income($user_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'income');
        $result = $this->db->get('transactions')->row();
        return ($result && isset($result->amount)) ? $result->amount : 0;
    }

    public function get_total_expense($user_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'expense');
        $result = $this->db->get('transactions')->row();
        return ($result && isset($result->amount)) ? $result->amount : 0;
    }

    // Dynamic Categories
    public function get_categories($type = null)
    {
        if ($type) {
            $this->db->where('type', $type);
        }
        // Fetch global categories (user_id IS NULL) OR user specific ones
        // Since we don't have user specific ones in the seed yet, we just grab all or filter by type
        // To support user specific: $this->db->group_start()->where('user_id', NULL)->or_where('user_id', $current_user_id)->group_end();
        // But the model method signature doesn't have user_id. I'll stick to simple global for now as per plan
        // Update: The requirement says "can be added", so let's allow fetching global checks.
        $this->db->order_by('name', 'ASC');
        return $this->db->get('categories')->result_array();
    }

    public function get_categories_by_user($type = null, $user_id = null)
    {
        if ($type) {
            $this->db->where('type', $type);
        }
        $this->db->group_start();
        $this->db->where('user_id', NULL); // Global
        if ($user_id) {
            $this->db->or_where('user_id', $user_id);
        }
        $this->db->group_end();
        $this->db->order_by('name', 'ASC');
        return $this->db->get('categories')->result_array();
    }

    public function get_category($id)
    {
        return $this->db->get_where('categories', ['id' => $id])->row_array();
    }
}
