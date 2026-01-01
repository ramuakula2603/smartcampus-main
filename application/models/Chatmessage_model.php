<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Chatmessage_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save chat message to database
     * @param string      $user_message
     * @param string      $ai_reply
     * @param string|int  $conversation_id
     * @return int Insert ID
     */
    public function save_message($user_message, $ai_reply, $conversation_id = 'default')
    {
        $data = array(
            'user_message'    => $user_message,
            'ai_reply'        => $ai_reply,
            'conversation_id' => $conversation_id,
            'created_at'      => date('Y-m-d H:i:s')
        );

        $this->db->insert('chat_messages_gp', $data);
        return $this->db->insert_id();
    }

    /**
     * Get all chat messages
     * @param int $limit
     * @return array
     */
    public function get_all_messages($limit = 100)
    {
        $this->db->select('*');
        $this->db->from('chat_messages_gp');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get distinct conversations with latest message info
     * @param int $limit
     * @return array
     */
    public function get_conversations($limit = 50)
    {
        $this->db->select('conversation_id, MAX(created_at) as last_time, MAX(id) as last_id');
        $this->db->from('chat_messages_gp');
        $this->db->group_by('conversation_id');
        $this->db->order_by('last_time', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        $rows = $query->result_array();

        $conversations = array();
        foreach ($rows as $row) {
            $conversation_id = $row['conversation_id'];

            $this->db->select('user_message, ai_reply');
            $this->db->from('chat_messages_gp');
            $this->db->where('id', $row['last_id']);
            $last_row = $this->db->get()->row_array();

            $preview_source = '';
            if (!empty($last_row['user_message'])) {
                $preview_source = $last_row['user_message'];
            } elseif (!empty($last_row['ai_reply'])) {
                $preview_source = $last_row['ai_reply'];
            }

            $conversations[] = array(
                'conversation_id' => $conversation_id,
                'last_time'       => $row['last_time'],
                'last_id'         => (int) $row['last_id'],
                'preview'         => $preview_source,
            );
        }

        return $conversations;
    }

    /**
     * Get all messages for a given conversation
     * @param string|int $conversation_id
     * @return array
     */
    public function get_messages_by_conversation($conversation_id)
    {
        $this->db->select('*');
        $this->db->from('chat_messages_gp');
        $this->db->where('conversation_id', $conversation_id);
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get message by ID
     * @param int $id
     * @return array
     */
    public function get_message($id)
    {
        $this->db->select('*');
        $this->db->from('chat_messages_gp');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Delete message
     * @param int $id
     * @return bool
     */
    public function delete_message($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('chat_messages_gp');
    }
}

