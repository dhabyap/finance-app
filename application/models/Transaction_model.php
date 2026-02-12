<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_transactions($user_id, $limit = null, $filter = [], $offset = 0)
    {
        $this->db->where('user_id', $user_id);

        if (!empty($filter['date'])) {
            $this->db->where('transaction_date', $filter['date']);
        }

        if (!empty($filter['month'])) {
            $this->db->where('MONTH(transaction_date)', $filter['month']);
        }

        if (!empty($filter['year'])) {
            $this->db->where('YEAR(transaction_date)', $filter['year']);
        }

        $this->db->order_by('transaction_date', 'DESC');
        $this->db->order_by('created_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
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

    public function get_total_income($user_id, $filter = [])
    {
        $this->db->select_sum('amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'income');

        if (!empty($filter['month'])) {
            $this->db->where('MONTH(transaction_date)', $filter['month']);
        }
        if (!empty($filter['year'])) {
            $this->db->where('YEAR(transaction_date)', $filter['year']);
        }

        $result = $this->db->get('transactions')->row();
        return ($result && isset($result->amount)) ? $result->amount : 0;
    }

    public function get_total_expense($user_id, $filter = [])
    {
        $this->db->select_sum('amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'expense');

        if (!empty($filter['month'])) {
            $this->db->where('MONTH(transaction_date)', $filter['month']);
        }
        if (!empty($filter['year'])) {
            $this->db->where('YEAR(transaction_date)', $filter['year']);
        }

        $result = $this->db->get('transactions')->row();
        return ($result && isset($result->amount)) ? $result->amount : 0;
    }

    public function expense_by_category($user_id, $filter = [])
    {
        $this->db->select('category, SUM(amount) as total');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'expense');

        if (!empty($filter['month'])) {
            $this->db->where('MONTH(transaction_date)', $filter['month']);
        }
        if (!empty($filter['year'])) {
            $this->db->where('YEAR(transaction_date)', $filter['year']);
        }

        $this->db->group_by('category');
        return $this->db->get('transactions')->result();
    }
    public function income_by_category($user_id, $filter = [])
    {
        $this->db->select('category, SUM(amount) as total');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', 'income');

        if (!empty($filter['month'])) {
            $this->db->where('MONTH(transaction_date)', $filter['month']);
        }
        if (!empty($filter['year'])) {
            $this->db->where('YEAR(transaction_date)', $filter['year']);
        }

        $this->db->group_by('category');
        return $this->db->get('transactions')->result();
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

    public function add_batch_transactions($data)
    {
        return $this->db->insert_batch('transactions', $data);
    }

    /**
     * Finds a category by name and type for a user, or creates it if it doesn't exist.
     */
    public function get_or_create_category($name, $type, $user_id)
    {
        $name = trim($name);
        // Check if category exists (Global or User Specific)
        $this->db->group_start();
        $this->db->where('user_id', NULL);
        $this->db->or_where('user_id', $user_id);
        $this->db->group_end();
        $this->db->where('LOWER(name)', strtolower($name));
        $this->db->where('type', $type);
        $category = $this->db->get('categories')->row_array();

        if ($category) {
            return $category['name']; // Returning name because transactions table stores name
        }

        // Create new category as User Specific
        $new_category = [
            'name' => $name,
            'type' => $type,
            'user_id' => $user_id
        ];
        $this->db->insert('categories', $new_category);
        return $name;
    }

    public function get_monthly_summary($user_id, $limit = 6)
    {
        $this->db->select("
            DATE_FORMAT(transaction_date, '%Y-%m') as month_year,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
        ");
        $this->db->where('user_id', $user_id);
        $this->db->group_by("DATE_FORMAT(transaction_date, '%Y-%m')");
        $this->db->order_by('month_year', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('transactions')->result_array();
    }
}
