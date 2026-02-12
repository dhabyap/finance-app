<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaction_model');
        $this->load->model('User_model');
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);

        $data['total_income'] = $this->Transaction_model->get_total_income($user_id);
        $data['total_expense'] = $this->Transaction_model->get_total_expense($user_id);
        $data['balance'] = $data['total_income'] - $data['total_expense'];
        $data['recent_transactions'] = $this->Transaction_model->get_transactions($user_id, 5);
        $data['goal_amount'] = $user['goal_amount'];

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/main_footer');
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('goal_amount', 'Financial Freedom Goal', 'numeric');

        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'matches[password]');
        }

        if ($this->input->post()) {
            $_POST['goal_amount'] = str_replace('.', '', $this->input->post('goal_amount'));
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/profile', $data);
            $this->load->view('templates/main_footer');
        } else {
            $goal_amount = $this->input->post('goal_amount');

            $update_data = [
                'name' => $this->input->post('name'),
                'goal_amount' => $goal_amount ? $goal_amount : 0
            ];

            if ($this->input->post('password')) {
                $update_data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
            }

            $this->User_model->update_user($user_id, $update_data);

            // Update session if name changed
            $this->session->set_userdata('name', $update_data['name']);

            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green text-black" role="alert">Profile updated successfully!</div>');
            redirect('dashboard/profile');
        }
    }

    public function transactions()
    {
        $user_id = $this->session->userdata('user_id');
        $limit = 20;
        $offset = $this->input->get('offset') ? $this->input->get('offset') : 0;

        $month = $this->input->get('month');
        $year = $this->input->get('year');

        // If month is selected but year is not, default to current year to avoid mixing years
        if (!empty($month) && empty($year)) {
            $year = date('Y');
        }

        $filter = [
            'date' => $this->input->get('date'),
            'month' => $month,
            'year' => $year,
        ];

        $data['transactions'] = $this->Transaction_model->get_transactions($user_id, $limit, $filter, $offset);
        $data['filter'] = $filter;
        $data['offset'] = $offset;
        $data['limit'] = $limit;

        if ($this->input->is_ajax_request()) {
            // Return only the list items for AJAX "Load More"
            $this->load->view('dashboard/transaction_list_partial', $data);
        } else {
            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/transactions', $data);
            $this->load->view('templates/main_footer');
        }
    }

    public function detail($id = null)
    {
        if (!$id)
            redirect('dashboard/transactions');
        $transaction = $this->Transaction_model->get_transaction($id);
        if (!$transaction || $transaction['user_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Transaction not found!</div>');
            redirect('dashboard/transactions');
        }
        $data['transaction'] = $transaction;

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/detail', $data);
        $this->load->view('templates/main_footer');
    }

    public function add()
    {
        if ($this->input->post()) {
            $_POST['amount'] = str_replace('.', '', $this->input->post('amount'));
        }

        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('type', 'Type', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }
            $user_id = $this->session->userdata('user_id');
            // get categories for dropdown
            // Using get_categories_by_user to support both global and user specific
            $data['categories'] = $this->Transaction_model->get_categories_by_user(null, $user_id);

            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/add', $data);
            $this->load->view('templates/main_footer');
        } else {
            $user_id = $this->session->userdata('user_id');
            $type = $this->input->post('type');
            $cat_name = $this->input->post('category');
            $amount = $this->input->post('amount');

            // Auto-create category if needed
            $final_category = $this->Transaction_model->get_or_create_category($cat_name, $type, $user_id);

            $data = [
                'user_id' => $user_id,
                'title' => $this->input->post('title'),
                'amount' => $amount,
                'type' => $type,
                'category' => $final_category,
                'payee' => $this->input->post('payee'),
                'transaction_date' => $this->input->post('date')
            ];
            $this->Transaction_model->add_transaction($data);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Transaction added successfully!']);
                return;
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green text-black" role="alert">Transaction added!</div>');
            redirect('dashboard/transactions');
        }
    }

    public function delete($id)
    {
        $transaction = $this->Transaction_model->get_transaction($id);
        if ($transaction && $transaction['user_id'] == $this->session->userdata('user_id')) {
            $this->Transaction_model->delete_transaction($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-red text-black" role="alert">Transaction deleted!</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Unauthorized!</div>');
        }
        redirect('dashboard/transactions');
    }

    public function stats()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_user_by_id($user_id);

        $month = $this->input->get('month') ? $this->input->get('month') : date('n');
        $year = $this->input->get('year') ? $this->input->get('year') : date('Y');

        $filter = ['month' => $month, 'year' => $year];

        $data['total_income'] = $this->Transaction_model->get_total_income($user_id, $filter);
        $data['total_expense'] = $this->Transaction_model->get_total_expense($user_id, $filter);
        $data['category_expense'] = $this->Transaction_model->expense_by_category($user_id, $filter);
        $data['category_income'] = $this->Transaction_model->income_by_category($user_id, $filter);
        $data['goal_amount'] = $user['goal_amount'];
        $data['filter'] = $filter;

        // Overall balance for goal tracking (not filtered by month for the overall goal)
        $full_income = $this->Transaction_model->get_total_income($user_id);
        $full_expense = $this->Transaction_model->get_total_expense($user_id);
        $data['overall_balance'] = $full_income - $full_expense;

        // --- ADVANCED FINANCE METRICS ---
        $summary = $this->Transaction_model->get_monthly_summary($user_id, 6);
        $data['monthly_summary'] = array_reverse($summary); // For chronological trend

        // Calculate averages for forecasting (last 6 months)
        $avg_income = 0;
        $avg_expense = 0;
        if (!empty($summary)) {
            $sum_inc = array_sum(array_column($summary, 'total_income'));
            $sum_exp = array_sum(array_column($summary, 'total_expense'));
            $count = count($summary);
            $avg_income = $sum_inc / $count;
            $avg_expense = $sum_exp / $count;
        }

        $data['avg_income'] = $avg_income;
        $data['avg_expense'] = $avg_expense;
        $data['avg_savings'] = $avg_income - $avg_expense;

        // Saving Rate (Current Filtered Month)
        $monthly_savings = $data['total_income'] - $data['total_expense'];
        $data['saving_rate'] = $data['total_income'] > 0 ? ($monthly_savings / $data['total_income']) * 100 : 0;

        // Emergency Fund Goal (6x avg expenses)
        $data['emergency_fund_goal'] = $avg_expense * 6;
        $data['ef_progress'] = $data['emergency_fund_goal'] > 0 ? min(100, ($data['overall_balance'] / $data['emergency_fund_goal']) * 100) : 0;

        // Freedom Timeline Forecast
        $remaining_needed = $data['goal_amount'] - $data['overall_balance'];
        if ($data['avg_savings'] > 0 && $remaining_needed > 0) {
            $data['months_to_freedom'] = ceil($remaining_needed / $data['avg_savings']);
        } else {
            $data['months_to_freedom'] = ($remaining_needed <= 0) ? 0 : null;
        }

        // --- SMART INSIGHTS ---
        $insights = [];
        if ($data['saving_rate'] < 10) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'lucide:alert-triangle',
                'text' => 'Your saving rate is below 10%. Try cutting non-essential expenses this month.'
            ];
        } elseif ($data['saving_rate'] >= 30) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'lucide:trending-up',
                'text' => 'Excellent saving rate! You are building wealth much faster than average.'
            ];
        }

        if ($data['ef_progress'] < 100) {
            $months_saved = $data['avg_expense'] > 0 ? $data['overall_balance'] / $data['avg_expense'] : 0;
            $insights[] = [
                'type' => 'info',
                'icon' => 'lucide:shield',
                'text' => 'Your safe zone is at ' . round($months_saved, 1) . ' months. Aim for 6 months to be fully secure.'
            ];
        }

        if (!empty($data['category_expense'])) {
            $highest = $data['category_expense'][0];
            $insights[] = [
                'type' => 'dark',
                'icon' => 'lucide:pie-chart',
                'text' => $highest->category . " is your biggest expense this month (Rp " . number_format($highest->total, 0, ',', '.') . ")."
            ];
        }

        $data['insights'] = $insights;

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/stats', $data);
        $this->load->view('templates/main_footer');
    }

}
