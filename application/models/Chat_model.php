<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create_thread($user_id, $title = null)
    {
        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('chat_threads', $data);
        return $this->db->insert_id();
    }

    public function get_threads($user_id, $limit = 20)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('updated_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('chat_threads')->result_array();
    }

    public function get_thread($thread_id)
    {
        return $this->db->get_where('chat_threads', ['id' => $thread_id])->row_array();
    }

    public function touch_thread($thread_id)
    {
        $this->db->where('id', $thread_id);
        return $this->db->update('chat_threads', ['updated_at' => date('Y-m-d H:i:s')]);
    }

    public function add_message($thread_id, $role, $content, $meta = null)
    {
        $data = [
            'thread_id' => $thread_id,
            'role' => $role,
            'content' => $content,
            'meta_json' => $meta ? json_encode($meta) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('chat_messages', $data);
        $this->touch_thread($thread_id);
        return $this->db->insert_id();
    }

    public function get_messages($thread_id, $limit = 100)
    {
        $this->db->where('thread_id', $thread_id);
        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit);
        return $this->db->get('chat_messages')->result_array();
    }

    public function delete_threads_for_user($user_id)
    {
        // Foreign keys should cascade from chat_threads -> chat_messages.
        $this->db->where('user_id', $user_id);
        return $this->db->delete('chat_threads');
    }
}

