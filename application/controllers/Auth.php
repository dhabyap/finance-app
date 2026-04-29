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

        $max_attempts = 5;
        $lockout_seconds = 300;
        $session_key = 'login_attempts';
        $lockout_key = 'login_locked_until';

        $failed_attempts = $this->session->userdata($session_key) ?: 0;
        $locked_until = $this->session->userdata($lockout_key) ?: 0;

        if ($locked_until > time()) {
            $remaining = $locked_until - time();
            $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Too many login attempts. Try again in ' . ceil($remaining / 60) . ' minute(s).</div>');
            $this->load->view('templates/auth_header');
            $this->load->view('auth/login');
            $this->load->view('templates/footer');
            return;
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
                    $this->session->unset_userdata($session_key);
                    $this->session->unset_userdata($lockout_key);
                    $this->session->sess_regenerate(true);
                    $this->session->set_userdata([
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'name' => $user['name'],
                        'role' => $user['role']
                    ]);
                    redirect('dashboard');
                } else {
                    $failed_attempts++;
                    $this->session->set_userdata($session_key, $failed_attempts);

                    if ($failed_attempts >= $max_attempts) {
                        $this->session->set_userdata($lockout_key, time() + $lockout_seconds);
                        $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Too many failed attempts. Locked for 5 minutes.</div>');
                    } else {
                        $remaining = $max_attempts - $failed_attempts;
                        $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Wrong password! ' . $remaining . ' attempt(s) remaining.</div>');
                    }
                    redirect('auth/login');
                }
            } else {
                $failed_attempts++;
                $this->session->set_userdata($session_key, $failed_attempts);

                if ($failed_attempts >= $max_attempts) {
                    $this->session->set_userdata($lockout_key, time() + $lockout_seconds);
                    $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Too many failed attempts. Locked for 5 minutes.</div>');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">User not found!</div>');
                }
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
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[72]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/auth_header');
            $this->load->view('auth/register');
            $this->load->view('templates/footer');
        } else {
            $password = $this->input->post('password');

            if (strlen($password) < 8) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger border-brutal" role="alert">Password must be at least 8 characters.</div>');
                redirect('auth/register');
                return;
            }

            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'username' => htmlspecialchars($this->input->post('username', true)),
                'password' => password_hash($password, PASSWORD_DEFAULT)
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
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('admin_authorized');
        $this->session->sess_destroy();
        $this->session->set_flashdata('message', '<div class="alert alert-success border-brutal bg-pastel-blue text-black" role="alert">You have been logged out!</div>');
        redirect('auth/login');
    }
}
