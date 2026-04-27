<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Chat_model');
        $this->load->model('Transaction_model');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->library('TransactionExtractor');

        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }

    private function ensure_tables_exist_or_show_help()
    {
        // Avoid raw DB errors for older installs that haven't applied the SQL patch yet.
        if (!$this->db->table_exists('chat_threads') || !$this->db->table_exists('chat_messages')) {
            $data = [
                'missing' => [],
            ];
            if (!$this->db->table_exists('chat_threads')) $data['missing'][] = 'chat_threads';
            if (!$this->db->table_exists('chat_messages')) $data['missing'][] = 'chat_messages';

            $this->output->set_status_header(500);
            $this->load->view('templates/header', $data);
            $this->load->view('chat/missing_tables', $data);
            $this->load->view('templates/main_footer');
            return false;
        }
        return true;
    }

    public function index()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $user_id = $this->session->userdata('user_id');

        $thread_id = $this->input->get('thread');
        if ($thread_id) {
            redirect('chat/thread/' . (int)$thread_id);
        }

        $threads = $this->Chat_model->get_threads($user_id);
        if (empty($threads)) {
            $newId = $this->Chat_model->create_thread($user_id, 'New chat');
            redirect('chat/thread/' . (int)$newId);
        }

        redirect('chat/thread/' . (int)$threads[0]['id']);
    }

    public function thread($thread_id)
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $user_id = $this->session->userdata('user_id');
        $thread_id = (int)$thread_id;

        $thread = $this->Chat_model->get_thread($thread_id);
        if (!$thread || (int)$thread['user_id'] !== (int)$user_id) {
            show_error('Thread not found', 404);
            return;
        }

        $data = [];
        $data['threads'] = $this->Chat_model->get_threads($user_id, 50);
        $data['thread'] = $thread;
        $data['messages'] = $this->Chat_model->get_messages($thread_id, 200);

        $this->load->view('templates/header', $data);
        $this->load->view('chat/index', $data);
        $this->load->view('templates/main_footer');
    }

    public function new_thread()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $user_id = $this->session->userdata('user_id');
        $id = $this->Chat_model->create_thread($user_id, 'New chat');
        redirect('chat/thread/' . (int)$id);
    }

    public function clear_history()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $user_id = $this->session->userdata('user_id');
        $this->Chat_model->delete_threads_for_user($user_id);
        $id = $this->Chat_model->create_thread($user_id, 'New chat');
        redirect('chat/thread/' . (int)$id);
    }

    public function send($thread_id)
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $thread_id = (int)$thread_id;
        $thread = $this->Chat_model->get_thread($thread_id);
        if (!$thread || (int)$thread['user_id'] !== (int)$user_id) {
            $this->output->set_status_header(404);
            echo json_encode(['status' => 'error', 'message' => 'Thread not found']);
            return;
        }

        $message = trim((string)$this->input->post('message'));
        if ($message === '') {
            echo json_encode(['status' => 'error', 'message' => 'Message is empty']);
            return;
        }

        $this->Chat_model->add_message($thread_id, 'user', $message);

        $today = date('Y-m-d');
        $result = $this->transactionextractor->extract($message, $today);

        if ($result['intent'] === 'create_transaction') {
            $assistantText = "Aku buat draft transaksi. Cek dulu ya, lalu klik Confirm.";
        } elseif ($result['intent'] === 'clarify') {
            $assistantText = $result['questions'][0] ?? 'Bisa jelasin sedikit lagi?';
        } else {
            $assistantText = 'Aku belum yakin ini transaksi. Bisa jelaskan lagi?';
        }

        $this->Chat_model->add_message($thread_id, 'assistant', $assistantText, $result);

        echo json_encode([
            'status' => 'success',
            'assistant' => [
                'content' => $assistantText,
                'meta' => $result,
            ],
        ]);
    }

    public function confirm($thread_id)
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $thread_id = (int)$thread_id;
        $thread = $this->Chat_model->get_thread($thread_id);
        if (!$thread || (int)$thread['user_id'] !== (int)$user_id) {
            $this->output->set_status_header(404);
            echo json_encode(['status' => 'error', 'message' => 'Thread not found']);
            return;
        }

        // Validate draft payload similar to Dashboard::add
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[income,expense]');
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('category', 'Category', 'required|trim');
        $this->form_validation->set_rules('transaction_date', 'Date', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => strip_tags(validation_errors())]);
            return;
        }

        $type = $this->input->post('type');
        $title = $this->input->post('title');
        $amount = $this->input->post('amount');
        $category = $this->input->post('category');
        $payee = $this->input->post('payee');
        $date = $this->input->post('transaction_date');

        $final_category = $this->Transaction_model->get_or_create_category($category, $type, $user_id);

        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'amount' => $amount,
            'type' => $type,
            'category' => $final_category,
            'payee' => $payee,
            'transaction_date' => $date,
        ];
        $this->Transaction_model->add_transaction($data);

        $this->Chat_model->add_message($thread_id, 'assistant', 'Sip, transaksinya sudah disimpan.');

        echo json_encode(['status' => 'success', 'message' => 'Transaction saved']);
    }
}
