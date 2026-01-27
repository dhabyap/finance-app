<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Transaction_model');

        // 1. Basic Check: Logged in?
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }

        // 2. Role Check: Admin?
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Access Denied! Admin only.</div>');
            redirect('dashboard');
        }

        // 3. Secret Key Check: Authorized session?
        // Skip check for the login method itself
        if ($this->router->fetch_method() !== 'login' && !$this->session->userdata('admin_authorized')) {
            redirect('admin/login');
        }
    }

    public function index()
    {
        $data['title'] = 'Admin Dashboard';
        $data['total_users'] = $this->db->count_all('users');
        $data['total_transactions'] = $this->db->count_all('transactions');
        $data['total_categories'] = $this->db->count_all('categories');

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/admin_footer');
    }

    public function login()
    {
        if ($this->session->userdata('admin_authorized')) {
            redirect('admin');
        }

        $this->form_validation->set_rules('secret_key', 'Secret Key', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Admin Challenge';
            $this->load->view('templates/admin_header', $data);
            $this->load->view('admin/login', $data);
            $this->load->view('templates/admin_footer');
        } else {
            $input_key = $this->input->post('secret_key');
            $actual_key = $this->config->item('admin_secret_key');

            if ($input_key === $actual_key) {
                $this->session->set_userdata('admin_authorized', true);
                redirect('admin');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Invalid Secret Key!</div>');
                redirect('admin/login');
            }
        }
    }

    public function users()
    {
        $data['title'] = 'User Management';
        $data['users'] = $this->db->get('users')->result_array();

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users', $data);
        $this->load->view('templates/admin_footer');
    }

    public function categories()
    {
        $data['title'] = 'Category Management';
        $data['categories'] = $this->Transaction_model->get_categories();

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/categories', $data);
        $this->load->view('templates/admin_footer');
    }

    public function add_category()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->categories();
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'type' => $this->input->post('type'),
                'user_id' => NULL // Global category
            ];
            $this->db->insert('categories', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green text-black" role="alert">Category added!</div>');
            redirect('admin/categories');
        }
    }

    public function edit_category($id)
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->categories();
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'type' => $this->input->post('type')
            ];
            $this->db->where('id', $id);
            $this->db->update('categories', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green text-black" role="alert">Category updated!</div>');
            redirect('admin/categories');
        }
    }

    public function delete_category($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('categories');
        $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-red text-black" role="alert">Category deleted!</div>');
        redirect('admin/categories');
    }

    public function logout()
    {
        $this->session->unset_userdata('admin_authorized');
        redirect('dashboard');
    }
}
