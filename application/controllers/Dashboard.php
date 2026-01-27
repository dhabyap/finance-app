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

        $data['total_income'] = $this->Transaction_model->get_total_income($user_id);
        $data['total_expense'] = $this->Transaction_model->get_total_expense($user_id);
        $data['balance'] = $data['total_income'] - $data['total_expense'];
        $data['recent_transactions'] = $this->Transaction_model->get_transactions($user_id, 5);

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/main_footer');
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->User_model->get_user_by_id($user_id);

        $this->form_validation->set_rules('name', 'Name', 'required|trim');

        if ($this->input->post('password')) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Password Confirmation', 'matches[password]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/profile', $data);
            $this->load->view('templates/main_footer');
        } else {
            $update_data = [
                'name' => $this->input->post('name')
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
        $data['transactions'] = $this->Transaction_model->get_transactions($user_id);

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/transactions', $data);
        $this->load->view('templates/main_footer');
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
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('type', 'Type', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');

        // Validation for dynamic category? 
        // For now, allow any string as current DB column is varchar(100)
        // If we strictly link to category ID, we'd need to change 'category' column in transactions table to INT and FK.
        // User didn't ask for that schema change yet, just "add to database".
        // The current 'category' column stores the NAME. I will stick to storing the Name for now to avoid migration headache on `transactions` table.
        // So the dropdown will populate values as Names.

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
            $data = [
                'user_id' => $this->session->userdata('user_id'),
                'title' => $this->input->post('title'),
                'amount' => $this->input->post('amount'),
                'type' => $this->input->post('type'),
                'category' => $this->input->post('category'), // This will be the name string from select value
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
        $data['total_income'] = $this->Transaction_model->get_total_income($user_id);
        $data['total_expense'] = $this->Transaction_model->get_total_expense($user_id);

        $this->load->view('templates/header');
        $this->load->view('dashboard/stats', $data);
        $this->load->view('templates/main_footer');
    }
}
