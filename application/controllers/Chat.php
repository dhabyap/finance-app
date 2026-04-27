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
        $this->load->library('TransactionDraftValidator');
        $this->config->load('ai', true);

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

    private function default_thread_id()
    {
        $user_id = (int)$this->session->userdata('user_id');
        return (int)$this->Chat_model->get_or_create_default_thread($user_id);
    }

    public function index()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        redirect('chat/thread/' . $this->default_thread_id());
    }

    public function thread($thread_id)
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $user_id = $this->session->userdata('user_id');
        // Use a single implicit conversation per user.
        $thread_id = $this->default_thread_id();

        $thread = $this->Chat_model->get_thread($thread_id);
        if (!$thread || (int)$thread['user_id'] !== (int)$user_id) {
            show_error('Thread not found', 404);
            return;
        }

        $data = [];
        $data['thread'] = $thread;
        $data['messages'] = $this->Chat_model->get_messages($thread_id, 200);

        $this->load->view('templates/header', $data);
        $this->load->view('chat/index', $data);
        $this->load->view('templates/main_footer');
    }

    public function new_thread()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        // Threads are not exposed in the UI; just redirect to default conversation.
        redirect('chat/thread/' . $this->default_thread_id());
    }

    public function clear_history()
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        $thread_id = $this->default_thread_id();
        $this->Chat_model->delete_messages($thread_id);
        redirect('chat/thread/' . (int)$thread_id);
    }

    public function send($thread_id)
    {
        if (!$this->ensure_tables_exist_or_show_help()) return;
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->session->userdata('user_id');
        // Ignore URL thread_id; always use default conversation.
        $thread_id = $this->default_thread_id();
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

        // Optional LLM fallback: only when enabled and regex parser is uncertain.
        $aiCfg = $this->config->item('ai');
        if (!empty($aiCfg['ai_enabled']) && ($result['intent'] === 'clarify' || ($result['confidence'] ?? 0) < 0.7)) {
            if ($this->rate_limit_ok($aiCfg, (int)$user_id)) {
                $llm = $this->extract_with_llm($message, $today, $aiCfg, (int)$user_id);
                if ($llm) {
                    $result = $llm;
                }
            }
        }

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
        // Ignore URL thread_id; always use default conversation.
        $thread_id = $this->default_thread_id();
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

    public function ai_status()
    {
        // Lightweight JSON endpoint for verifying AI configuration.
        // Requires auth via constructor.
        $aiCfg = $this->config->item('ai');
        if (!is_array($aiCfg)) $aiCfg = [];

        $out = [
            'ai_enabled' => (bool)($aiCfg['ai_enabled'] ?? false),
            'primary_provider' => (string)($aiCfg['ai_provider_primary'] ?? ''),
            'fallback_provider' => (string)($aiCfg['ai_provider_fallback'] ?? ''),
            'primary_model' => (string)($aiCfg['ai_model_primary'] ?? ''),
            'fallback_model' => (string)($aiCfg['ai_model_fallback'] ?? ''),
            'timeout_seconds' => (int)($aiCfg['ai_timeout_seconds'] ?? 0),
            'rate_limit_per_minute' => (int)($aiCfg['ai_rate_limit_per_minute'] ?? 0),
            'has_gemini_key' => !empty($aiCfg['gemini_api_key']),
            'has_groq_key' => !empty($aiCfg['groq_api_key']),
            'probe' => null,
        ];

        $probe = $this->input->get('probe');
        if ($probe === '1') {
            if (empty($out['ai_enabled'])) {
                $out['probe'] = ['ok' => false, 'error' => 'AI is disabled (set AI_ENABLED=true)'];
            } elseif (!$this->rate_limit_ok($aiCfg, (int)$this->session->userdata('user_id'))) {
                $out['probe'] = ['ok' => false, 'error' => 'Rate limited'];
            } else {
                $today = date('Y-m-d');
                $system = "Output JSON only: {\"ok\":true,\"today\":\"{$today}\"}. No markdown.";
                $messages = [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => 'ping'],
                ];

                require_once APPPATH . 'libraries/ai/AiRouter.php';
                $router = new AiRouter($aiCfg);
                $res = $router->chat($messages);
                if (!empty($res['ok'])) {
                    $out['probe'] = [
                        'ok' => true,
                        'provider' => $res['meta']['provider'] ?? null,
                        'model' => $res['meta']['model'] ?? null,
                    ];
                } else {
                    $out['probe'] = [
                        'ok' => false,
                        'status' => (int)($res['status'] ?? 0),
                        'error' => (string)($res['error'] ?? 'Unknown'),
                    ];
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($out));
    }

    private function rate_limit_ok($aiCfg, $user_id)
    {
        $limit = (int)($aiCfg['ai_rate_limit_per_minute'] ?? 15);
        if ($limit <= 0) return true;

        $key = 'ai_rate_' . $user_id;
        $now = time();
        $windowStart = $now - 60;

        $arr = $this->session->userdata($key);
        if (!is_array($arr)) $arr = [];

        $arr = array_values(array_filter($arr, function ($t) use ($windowStart) {
            return is_int($t) && $t >= $windowStart;
        }));

        if (count($arr) >= $limit) {
            return false;
        }

        $arr[] = $now;
        $this->session->set_userdata($key, $arr);
        return true;
    }

    private function extract_with_llm($message, $today, $aiCfg, $user_id)
    {
        // Build prompt with strict JSON contract. Keep it short for cheaper models.
        $categories = $this->Transaction_model->get_categories_by_user(null, $user_id);
        $catNames = array_map(function ($c) { return $c['name']; }, $categories ?: []);

        $system = "You are a transaction extraction engine. Output JSON only. No markdown.\n"
            . "Contract:\n"
            . "{ \"intent\": \"create_transaction\"|\"clarify\"|\"unknown\", \"draft\": {\"type\":\"income|expense\",\"amount\":int,\"transaction_date\":\"YYYY-MM-DD\",\"category\":string,\"title\":string,\"payee\":string}, \"missing\":[], \"questions\":[], \"confidence\": number }\n"
            . "Rules:\n"
            . "- Do NOT invent amounts/dates.\n"
            . "- If missing required info, set intent=clarify, include missing fields, ask exactly 1 question.\n"
            . "- Use category from allowed list when possible; otherwise set category=\"Uncategorized\".\n"
            . "Today: {$today}\n"
            . "Allowed categories: " . implode(', ', array_slice($catNames, 0, 50));

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $message],
        ];

        require_once APPPATH . 'libraries/ai/AiRouter.php';
        $router = new AiRouter($aiCfg);
        $res = $router->chat($messages);
        if (empty($res['ok'])) {
            return null;
        }

        $json = $this->transactiondraftvalidator->parse_json_from_text($res['text'] ?? '');
        if (!$json) return null;

        $validated = $this->transactiondraftvalidator->validate($json);
        if (!$validated['ok']) return null;

        return $validated['payload'];
    }
}
