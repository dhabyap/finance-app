<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        } else {
            redirect('auth/login');
        }
    }

    public function login()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/auth_header');
            $this->load->view('auth/login');
            $this->load->view('templates/footer');
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->User_model->get_user_by_username($username);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $this->session->set_userdata([
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'name' => $user['name']
                    ]);
                    redirect('dashboard');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Wrong password!</div>');
                    redirect('auth/login');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">User not found!</div>');
                redirect('auth/login');
            }
        }
    }

    public function register()
    {
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[3]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/auth_header');
            $this->load->view('auth/register');
            $this->load->view('templates/footer');
        } else {
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'username' => htmlspecialchars($this->input->post('username', true)),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            ];

            $this->User_model->register($data);
            $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-green text-black" role="alert">Registration successful! Please login.</div>');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('name');
        $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-blue text-black" role="alert">You have been logged out!</div>');
        redirect('auth/login');
    }
}
