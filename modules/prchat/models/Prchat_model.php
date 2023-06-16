<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex CRM Chat
Description: Chat Module for Perfex CRM
Author: Aleksandar Stojanov
Author URI: https://idevalex.com
*/

class Prchat_model extends App_Model
{
    /**
     * Get chat staff members with messages.
     *
     * @return mixed
     */
    public function getUsers()
    {

        $this->db->select('staffid, firstname, lastname, profile_image, last_login, last_activity, facebook, linkedin, skype, admin, role');
        $this->db->where('active', 1);
        $users = $this->db->get(db_prefix() . 'staff')->result_array();

        foreach ($users as $key => $user) {

            if (get_option('chat_show_only_users_with_chat_permissions') == 1) {
                if (!staff_can('view', PR_CHAT_MODULE_NAME, $user['staffid'])) {
                    unset($users[$key]);
                    continue;
                }
            }

            $sql = 'SELECT message,sender_id,time_sent FROM ' . db_prefix() . 'chatmessages WHERE (sender_id = ' . get_staff_user_id() . " AND reciever_id = {$user['staffid']}) OR (sender_id = {$user['staffid']} AND reciever_id = " . get_staff_user_id() . ') ORDER BY id DESC LIMIT 0, 1';

            $query = $this->db->query($sql)->result();

            if ($user['role']) {
                $users[$key]['role'] = get_staff_userrole($user['role']);

                if (!$users[$key]['role']) {
                    if ($user['admin']) {
                        $users[$key]['role'] = ' ' . _l('chat_role_administrator');
                    } else {
                        $users[$key]['role'] = ' ' . _l('chat_role_staff');
                    }
                } else if ($users[$key]['role'] && $user['admin']) {
                    $users[$key]['role'] = ' ' . _l('chat_role_administrator') . ' / ' . $users[$key]['role'];
                } else {
                    $users[$key]['role'] = " " . $users[$key]['role'];
                }
            }

            foreach ($query as &$chat) {
                $users[$key]['time_sent_formatted'] = $chat->time_sent_formatted = time_ago($chat->time_sent);

                $render_message = preg_match('~\b(src|audio|controls|ogg)\b~i', $chat->message);

                $render_message = ($render_message) ? _l('chat_new_audio_message_sent') : $chat->message;

                if ($user['staffid'] !== $chat->sender_id) {
                    $users[$key]['message'] = _l('chat_message_you') . ' ' . $render_message;
                } else {
                    $users[$key]['message'] = $render_message;
                }
                $users[$key]['status'] = ($this->_get_chat_status($user['staffid'])) ? $this->_get_chat_status($user['staffid']) : 'online';
            }
        }

        if ($users) {
            return $users;
        }

        return false;
    }


    /**
     * Get chat staff members.
     *
     * @return mixed
     */
    public function getStaffForForward()
    {

        $this->db->select('staffid, firstname, lastname, profile_image');
        $this->db->where('active', 1);
        $this->db->limit(20);

        $users = $this->db->get(db_prefix() . 'staff')->result_array();

        foreach ($users as $key => $user) {

            if (get_option('chat_show_only_users_with_chat_permissions') == 1) {
                if (!staff_can('view', PR_CHAT_MODULE_NAME, $user['staffid'])) {
                    unset($users[$key]);
                    continue;
                }
            }
        }

        if ($users) {
            return $users;
        }

        return false;
    }

    /**
     * Get users in json depending on group id
     *
     * @param string @group_id
     *
     * @return mixed
     */
    public function getUsersInJsonFormat($group_id)
    {
        $this->db->select('staffid as id, CONCAT(firstname, " ", lastname) as name, profile_image as avatar');
        $this->db->where('active', 1);
        $this->db->where('staffid !=', get_staff_user_id());
        $this->db->where('staffid IN (SELECT member_id FROM ' . db_prefix() . 'chatgroupmembers WHERE member_id=' . db_prefix() . 'staff.staffid AND group_id = ' . $group_id . ')');

        $users = $this->db->get(db_prefix() . 'staff')->result_array();

        foreach ($users as &$user) {
            $user['type'] = 'staff';
            $user['avatar'] = staff_profile_image_url($user['id']);
        }

        if ($users) {
            return $users;
        }

        return false;
    }

    /**
     * Get Current user chat status
     *
     * @param bool $id
     *
     * @return string
     */
    public function _get_chat_status($id = false)
    {
        $this->db->where('user_id', ($id) ? $id : get_staff_user_id());
        $this->db->where('name', 'chat_status');
        $result = $this->db->get(db_prefix() . 'chatsettings')->row_array();
        if ($result !== null) {
            return $result['value'];
        }
    }

    /**
     * Get logged in staff profile image.
     *
     * @param string @id
     *
     * @return mixed
     */
    public function getUserImage($id)
    {
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('staffid', $id);
        $data = $this->db->get()->row('profile_image');

        if ($data) {
            return $data;
        }

        return false;
    }

    /**
     * Create message.
     *
     * @param $data
     * @param $table
     *
     * @return mixed
     */
    public function createMessage($data, $table)
    {
        if ($this->db->insert($table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Create group message.
     *
     * @param array @data
     *
     * @return mixed
     */
    public function createGroupMessage($data)
    {
        if ($this->db->insert(db_prefix() . 'chatgroupmessages', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Get staff firstname and lastname.
     *
     * @param string @id
     *
     * @return mixed
     */
    public function getStaffInfo($id)
    {
        $this->db->select('firstname,lastname');
        $this->db->where('staffid', $id);
        $result = $this->db->get(db_prefix() . 'staff')->row();
        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get messages
     *
     * @param string $from sender
     * @param string $to receiver
     * @param string $limit limit messages
     * @param string $offset offset
     *
     * @return mixed
     */
    public function getMessages($from, $to, $limit, $offset)
    {
        $sql = 'SELECT * FROM ' . db_prefix() . "chatmessages WHERE (sender_id = {$to} AND reciever_id = {$from}) OR (sender_id = {$from} AND reciever_id = {$to}) ORDER BY id DESC LIMIT {$offset}, {$limit}";

        $query = $this->db->query($sql)->result();

        foreach ($query as &$chat) {
            $chat->message = pr_chat_convertLinkImageToString($chat->message);
            $chat->message = check_for_links_lity($chat->message);
            $this->checkMessageForAudio($chat);
            $chat->user_image = $this->getUserImage($chat->sender_id);
            $chat->sender_fullname = get_staff_full_name($chat->sender_id);
            $chat->time_sent_formatted = _dt($chat->time_sent);
        }

        if ($query) {
            return $query;
        }

        return false;
    }

    /**
     * Get client messages
     *
     * @param string $from sender
     * @param string $to receiver
     * @param string $limit limit messages
     * @param string $offset offset
     *
     * @return mixed
     */
    public function getMutualMessages($from, $to, $limit, $offset)
    {
        $sql = 'SELECT * FROM ' . db_prefix() . "chatclientmessages WHERE (sender_id = '{$to}' AND reciever_id = '{$from}') OR (sender_id = '{$from}' AND reciever_id = '{$to}') ORDER BY id DESC LIMIT {$offset}, {$limit}";

        $query = $this->db->query($sql)->result();

        foreach ($query as &$chat) {
            $chat->message = html_entity_decode($chat->message);
            $chat->message = pr_chat_convertLinkImageToString($chat->message);
            $chat->message = check_for_links_lity($chat->message);
            $chat->sender_fullname = get_staff_full_name(str_replace('staff_', '', $chat->sender_id));
            $chat->client_image_path = contact_profile_image_url(str_replace('client_', '', $chat->sender_id));
            $chat->time_sent_formatted = _dt($chat->time_sent);
        }

        if ($query) {
            return $query;
        }

        return false;
    }

    /**
     * Get group messages
     *
     * @param string $group_id group_id
     * @param string $limit limit messages
     * @param string $offset offset
     *
     * @return mixed
     */
    public function getGroupMessages($group_id, $limit, $offset)
    {
        if ($group_id !== null) {
            $sql = 'SELECT * FROM ' . db_prefix() . "chatgroupmessages WHERE group_id = {$group_id} ORDER BY id DESC LIMIT {$offset}, {$limit}";

            $query = $this->db->query($sql)->result();
            $created_by = $this->db->get_where(db_prefix() . 'chatgroups', ['id' => $group_id])->row('created_by_id');

            foreach ($query as &$chat) {
                /**
                 *  If message not contains user_mentioned class and emoji class convert image link data-lity
                 *  This gives an incorrect image path when you have @firstname lastname mention and :D  (emoji )inside a message
                 */
                if (strpos($chat->message, 'user_mentioned') != true && strpos($chat->message, '"emoji"') != true) {
                    $chat->message = pr_chat_convertLinkImageToString($chat->message);
                }

                $chat->message = check_for_links_lity($chat->message);

                $this->checkMessageForAudio($chat);

                $chat->user_image = $this->getUserImage($chat->sender_id);
                $chat->sender_fullname = get_staff_full_name($chat->sender_id);
                $chat->time_sent_formatted = _dt($chat->time_sent);
                $chat->created_by_id = $created_by;
            }

            $newQuery['messages'] = $query;

            $this->db->select('member_id,  group_id, lastname, firstname, created_by_id');
            $this->db->from(TABLE_CHATGROUPMEMBERS);
            $this->db->where('group_id', $group_id);
            $this->db->join(TABLE_STAFF, '' . TABLE_STAFF . '.staffid=' . TABLE_CHATGROUPMEMBERS . '.member_id');
            $this->db->join(TABLE_CHATGROUPS, '' . TABLE_CHATGROUPS . '.id=' . TABLE_CHATGROUPMEMBERS . '.group_id');
            $result = $this->db->get();
            $newQuery['users'] = $result->result_array();

            $group_name = $this->db->get_where(TABLE_CHATGROUPS, ['id' => $group_id])->row('group_name');

            $newQuery['separete_group_id'] = $group_id;
            $newQuery['separete_group_name'] = $group_name;

            if ($newQuery) {
                return $newQuery;
            }
        } else {
            return false;
        }

        return false;
    }

    private function checkMessageForAudio($chat)
    {
        if (preg_match('~\b(src|audio|controls|ogg)\b~i', $chat->message)) {
            $chat->message = html_entity_decode($chat->message);
        }
    }

    /**
     * Groups history for messages
     *
     * @param string $group_id group_id
     * @param string $limit limit messages
     * @param string $offset offset
     *
     * @return mixed
     */
    public function getGroupMessagesHistory($group_id, $limit, $offset)
    {
        $sql = 'SELECT * FROM ' . db_prefix() . "chatgroupmessages WHERE group_id = {$group_id} ORDER BY id DESC LIMIT {$offset}, {$limit}";

        $query = $this->db->query($sql)->result();
        $created_by = $this->db->get_where(db_prefix() . 'chatgroups', ['id' => $group_id])->row('created_by_id');

        foreach ($query as &$chat) {
            $chat->message = pr_chat_convertLinkImageToString($chat->message);
            $chat->message = check_for_links_lity($chat->message);
            $chat->user_image = $this->getUserImage($chat->sender_id);
            $chat->sender_fullname = get_staff_full_name($chat->sender_id);
            $chat->time_sent_formatted = _dt($chat->time_sent);
            $chat->created_by_id = $created_by;
        }

        if ($query) {
            return $query;
        }

        return false;
    }

    /**
     * Get unread messages for the logged in user.
     *
     * @return mixed
     */
    public function getUnread()
    {
        $unreadMessages = [];

        $staff_id = get_staff_user_id();

        $sql = 'SELECT sender_id FROM ' . db_prefix() . "chatmessages WHERE(reciever_id = $staff_id AND viewed = 0)";

        $query = $this->db->query($sql);

        if ($query) {
            $result = $query->result_array();
            foreach ($result as $sender) {
                $sender_id = 'sender_id_' . $sender['sender_id'];
                if (array_key_exists($sender_id, $unreadMessages)) {
                    $unreadMessages['' . $sender_id . '']['count_messages'] = $unreadMessages['' . $sender_id . '']['count_messages'] + 1;
                } else {
                    $unreadMessages['' . $sender_id . ''] = ['sender_id' => $sender['sender_id'], 'count_messages' => 1];
                }
            }
            if ($result) {
                return $unreadMessages;
            }
        }

        return false;
    }

    /**
     * Get client unread messages for the logged in user / admin.
     *
     * @return array
     */
    public function getClientUnreadMessages()
    {
        $unreadMessages = [];

        $staff_id = 'staff_' . get_staff_user_id();

        $sql = 'SELECT sender_id FROM ' . db_prefix() . "chatclientmessages WHERE(reciever_id = '{$staff_id}' AND viewed = 0)";

        $query = $this->db->query($sql);

        if ($query) {
            $result = $query->result_array();

            foreach ($result as $sender) {
                $sender_id = '_' . $sender['sender_id'];

                $contact_id = str_replace('client_', '', $sender['sender_id']);

                if (array_key_exists($sender_id, $unreadMessages)) {
                    $unreadMessages['' . $sender_id . '']['count_messages'] = $unreadMessages['' . $sender_id . '']['count_messages'] + 1;
                } else {
                    $unreadMessages['' . $sender_id . ''] = ['sender_id' => $sender['sender_id'], 'count_messages' => 1];
                    $unreadMessages['' . $sender_id . '']['client_data'] = getOwnClient($contact_id);
                }
            }

            if ($result) {
                return $unreadMessages;
            } else {
                return ['result' => false];
            }
        }

        return false;
    }

    /**
     * Get client unread messages for the logged in user / admin.
     *
     * @return mixed
     */
    public function getStaffUnreadMessages($pusher)
    {
        $unreadMessages = [];

        $contact_id = 'client_' . get_contact_user_id();

        $sql = 'SELECT sender_id, reciever_id, id AS msg_id FROM ' . db_prefix() . "chatclientmessages WHERE(reciever_id = '{$contact_id}' AND viewed = 0)";

        $query = $this->db->query($sql);

        if ($query) {
            $result = $query->result_array();

            foreach ($result as $sender) {
                $sender_id = '_' . $sender['sender_id'];

                if (array_key_exists($sender_id, $unreadMessages)) {
                    $unreadMessages['' . $sender_id . '']['count_messages'] = $unreadMessages['' . $sender_id . '']['count_messages'] + 1;
                } else {
                    $unreadMessages['' . $sender_id . ''] = ['sender_id' => $sender['sender_id'], 'count_messages' => 1];
                }
            }
            if ($result) {
                return $unreadMessages;
            }
        }

        return false;
    }

    /**
     * Update unread for sender.
     *
     * @param string $id sender id
     *
     * @return mixed
     */
    public function updateUnread($pusher, $id)
    {
        $staff_id = get_staff_user_id();

        $this->db->select('id as msg_id,sender_id, reciever_id, viewed_at');
        $this->db->from(db_prefix() . "chatmessages");
        $this->db->where("viewed = 0 AND reciever_id = {$staff_id} AND sender_id = {$id} OR reciever_id = {$id} AND sender_id = {$staff_id} AND viewed = 0");
        $messages = $this->db->get()->result();

        if ($messages) {
            foreach ($messages as $message) {
                $sql = 'UPDATE ' . db_prefix() . "chatmessages SET viewed = 1, viewed_at = '" . date('Y-m-d H:i:s') . "' WHERE (reciever_id = {$staff_id} AND sender_id = {$id}) AND id = '" . $message->msg_id . "'";
                $query = $this->db->query($sql);
            }

            if ($query) {
                $this->setMessageAsViewed($pusher, $messages);
                return true;
            }
        } else {
            return false;
        }


        return false;
    }

    /**
     * Update unread for client sender.
     *
     * @param string $id sender id
     * @param        $pusher
     * @param null   $to_client
     *
     * @return mixed
     */
    public function updateClientUnreadMessages($id, $pusher,$to_client = null)
    {
        $contact_id = get_contact_user_id();
        $query = '';
        $messages = '';

        $this->db->select('id as msg_id,sender_id, reciever_id, viewed_at, viewed');
        $this->db->from(db_prefix() . "chatclientmessages");

        if ($to_client && $contact_id) {
            $staff_id = str_replace('staff_', '', $id);

            $this->db->where("sender_id = 'staff_{$staff_id}' AND reciever_id = 'client_{$contact_id}' AND viewed = 0");
            $messages = $this->db->get()->result();
        } else {
            $staff_id = get_staff_user_id();
            $contact_id = str_replace('client_', '', $id);

            $this->db->where("sender_id = 'client_{$contact_id}' AND reciever_id = 'staff_{$staff_id}' AND viewed = 0");
            $messages = $this->db->get()->result();
        }

        if ($messages) {
            foreach ($messages as $message) {

                if ($to_client != null) {
                    /** From staff to client */
                    $sql = 'UPDATE ' . db_prefix() . "chatclientmessages SET viewed = 1, viewed_at = '" . date('Y-m-d H:i:s') . "' WHERE (sender_id = 'staff_{$staff_id}' AND reciever_id = 'client_{$contact_id}' AND id = " . $message->msg_id . " AND viewed = 0)";
                    $query = $this->db->query($sql);
                } else {
                    /** From client to staff */
                    $sql = 'UPDATE ' . db_prefix() . "chatclientmessages SET viewed = 1, viewed_at = '" . date('Y-m-d H:i:s') . "' WHERE (reciever_id = 'staff_{$staff_id}' AND sender_id = 'client_{$contact_id}' AND id = " . $message->msg_id . " AND viewed = 0)";
                    $query = $this->db->query($sql);
                }
            }


            if ($query) {
                $this->setMessageAsViewed($pusher, $messages);
                return true;
            }
        } else {
            return false;
        }


        return false;
    }

    /**
     * Set current chat theme.
     *
     * @param string $id the staff id
     * @param string $theme_name 1 or 0 light or dark
     */
    public function updateChatTheme($id, $theme_name)
    {
        $name = 'current_theme';
        $this->db->where('user_id', $id);
        $this->db->where('name', $name);

        $exsists = $this->db->get(db_prefix() . 'chatsettings')->row();

        if (!$exsists == null) {
            $this->db->where('user_id', $id);
            $this->db->where('name', $name);
            $this->db->update(db_prefix() . 'chatsettings', ['name' => $name, 'value' => $theme_name]);
        } else {
            $this->db->insert(db_prefix() . 'chatsettings', ['name' => $name, 'value' => $theme_name, 'user_id' => $id]);
        }
        if ($this->db->affected_rows() != 0) {
            return $theme_name;
        }

        return $theme_name;
    }

    /**
     * Reset toggled chat theme colors.
     *
     * @param string $id user id
     *
     * @return boolean
     */
    public function resetChatColors($id)
    {
        $this->db->where('user_id', $id);
        $this->db->update(db_prefix() . 'chatsettings', [
            'name'  => 'chat_color',
            'value' => '#546bf1',
        ]);

        return true;
    }

    /**
     * Set the chat color.
     *
     * @param string $id the staff id
     * @param string $color the color to set
     */
    public function setChatColor($color)
    {
        $id = get_staff_user_id();
        $name = 'chat_color';

        $color = validateChatColorBeforeApply($color, true);

        if ($color === 'unknownColor') {
            $message['success'] = $color;

            return $message;
        }

        if ($this->db->field_exists('value', db_prefix() . 'chatsettings')) {
            $this->db->where('user_id', $id);
            $this->db->where('name', $name);
            $exsists = $this->db->get(db_prefix() . 'chatsettings')->row();
            if (!$exsists == null) {
                $this->db->where('user_id', $id);
                $this->db->where('name', $name);
                $this->db->update(db_prefix() . 'chatsettings', ['name' => $name, 'value' => $color]);
            } else {
                $this->db->insert(db_prefix() . 'chatsettings', ['name' => $name, 'value' => $color, 'user_id' => $id]);
            }
            if ($this->db->affected_rows() != 0) {
                $message['success'] = $color;

                return $message;
            }
            $message['success'] = false;

            return $message;
        } else {
            $this->db->where('user_id', $id);
            $this->db->where('name', $name);
            $exsists = $this->db->get(db_prefix() . 'chatsettings')->row();
            if (!$exsists == null) {
                $this->db->where('user_id', $id);
                $this->db->where('name', $name);
                $this->db->update(db_prefix() . 'chatsettings', ['chat_color' => $color]);
            } else {
                $this->db->insert(db_prefix() . 'chatsettings', ['chat_color' => $color, 'user_id' => $id]);
            }
            if ($this->db->affected_rows() != 0) {
                $message['success'] = $color;

                return $message;
            }
            $message['success'] = false;

            return $message;
        }
    }

    /**
     * Delete group shared files and audio from folder.
     *
     * @param string $group_id
     *
     * @return boolean
     */
    public function deleteGroupSharedFiles($group_id)
    {
        $files = $this->db->select('file_name')->where('group_id', $group_id)->get(db_prefix() . 'chatgroupsharedfiles')->result_array();
        $audio_files = $this->db->query("SELECT message FROM " . db_prefix() . "chatgroupmessages WHERE message LIKE '%.ogg%' AND group_id = $group_id")->result_array();

        if (count($audio_files) > 0) {
            foreach ($audio_files as $file) {
                $this->handleAudioDeleteFile($file['message']);
            }
        }

        if (!empty($files) && is_array($files)) {
            foreach ($files as $file) {
                if (is_dir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER)) {
                    unlink(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER . '/' . $file['file_name']);
                }
            }
            $this->db->where('group_id', $group_id);
            $result = $this->db->delete(TABLE_CHATGROUPSHAREDFILES);
            if ($result) {
                return true;
            }
        } else {
            return false;
        }

        return false;
    }


    public function deleteClientMessage($message_id)
    {
        $table = db_prefix() . 'chatclientmessages';
        $possible_file = $this->db->select('id, message')->where("id", $message_id)->get($table)->row();

        $this->handleAudioDeleteFile($possible_file);

        if (prchat_checkMessageIfFileExists($possible_file->message)) {
            $file_name = getImageFullName($possible_file->message);
            if ($file_name == 'audio&amp;gt;') {
                $file_name = str_replace('&amp;lt;audio controls src=&quot;', "", $file_name);
                $file_name = str_replace("&quot; type=&quot;audio/ogg&quot;&amp;gt;&amp;lt;/audio&amp;gt;", "", $file_name);
                $file_name = pathinfo($file_name, PATHINFO_BASENAME);
                if (strlen($file_name)) {
                    if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio')) {
                        @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio/' . $file_name);
                    }
                }
            } else {
                if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
                    unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $file_name);
                }
            }
        }
        $this->db->where('id', $message_id);
        return $this->db->delete($table, ['id' => $message_id]);
    }


    /**
     * Delete chat messages including pictures and files.
     *
     * @param string $id the staff id
     * @param string $contact_id the contact_id
     *
     * @return boolean
     */
    public function deleteMessage($id, $mixed_id)
    {

        if ($mixed_id && strpos($mixed_id, 'group_id') !== false) {
            $mixed_id = str_replace('group_id', '', $mixed_id);
            $possible_file = $this->db->select('message')->where('group_id', $mixed_id)->where('id', $id)->get(db_prefix() . 'chatgroupmessages')->row();

            $this->handleAudioDeleteFile($possible_file);

            if (prchat_checkMessageIfFileExists($possible_file->message)) {
                $file_name = getImageFullName($possible_file->message);

                if (is_dir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER)) {
                    unlink(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER . '/' . $file_name);
                }
                $this->db->delete(db_prefix() . 'chatgroupsharedfiles', ['group_id' => $mixed_id, 'file_name' => $file_name]);
            }

            $this->db->where('id', $id);
            return $this->db->delete(db_prefix() . 'chatgroupmessages');
        } else {

            $possible_file = $this->db->select()->where('id', $id)->get(db_prefix() . 'chatmessages')->row()->message;

            $this->handleAudioDeleteFile($possible_file);

            if (prchat_checkMessageIfFileExists($possible_file)) {
                $file_name = getImageFullName($possible_file);

                if ($file_name == 'audio&amp;gt;') {
                    $file_name = str_replace('&amp;lt;audio controls src=&quot;', "", $file_name);
                    $file_name = str_replace("&quot; type=&quot;audio/ogg&quot;&amp;gt;&amp;lt;/audio&amp;gt;", "", $file_name);
                    $file_name = pathinfo($file_name, PATHINFO_BASENAME);
                    if (strlen($file_name)) {
                        if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio')) {
                            @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio/' . $file_name);
                        }
                    }
                } else {
                    if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
                        unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $file_name);
                    }
                }

                $this->db->delete(db_prefix() . 'chatsharedfiles', ['sender_id' => get_staff_user_id(), 'reciever_id' => $mixed_id, 'file_name' => $file_name]);
                $this->db->delete(db_prefix() . 'chatsharedfiles', ['sender_id' => $mixed_id, 'reciever_id' => get_staff_user_id(), 'file_name' => $file_name]);
            }

            $this->db->where('id', $id);
            return $this->db->delete(db_prefix() . 'chatmessages');
        }

        return false;
    }

    /**
     * Handle deleting audio files for staff and staff groups
     *
     * @param object $possible_file
     *
     * @return string
     */
    private function handleAudioDeleteFile($possible_file)
    {
        /**
         * This is when deleting from groups it returns object
         */
        if (isset($possible_file->message)) {
            $possible_file = $possible_file->message;
        }

        $finallyFileToDelete = '';

        if (preg_match('~\b(src|audio|controls|.ogg)\b~i', $possible_file)) {
            $parsedUrl = '';
            $file_to_delete = html_entity_decode($possible_file);
            $regex = '/https?\:\/\/[^\",]+/i';

            preg_match_all($regex, $file_to_delete, $matches);

            if (isset($matches[0]) && !empty($matches[0])) {
                // Parse url conver to url
                $parsedUrl = parse_url($matches[0][0]);
                // Get path host scheme path ...
                $pathFragments = explode('/', $parsedUrl['path']);

                // Last path of url hash-247823849729bsd9f823dbn2378db283db82d.ogg
                $finallyFileToDelete = end($pathFragments);

                if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio')) {
                    @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio/' . $finallyFileToDelete);
                }
            }
        }

        return $finallyFileToDelete;
    }

    /**
     * Delete chat conversation including pictures and files (works for clients and staff members).
     *
     * @param string $id staff or client id
     * @param string $table name for deletion directions
     *
     * @return json|array
     */
    public function deleteMutualConversation($id, $table)
    {
        $response = [];

        $response['success'] = false;

        if ($table == 'chatmessages') {
            $staff_id = get_staff_user_id();
        } else {
            $staff_id = '"staff_' . get_staff_user_id() . '"';
            $id = '"client_' . $id . '"';
        }

        $this->db->select('message');
        $this->db->where('sender_id = ' . $id . ' AND reciever_id = ' . $staff_id . '');
        $this->db->or_where('reciever_id = ' . $id . ' AND sender_id = ' . $staff_id . '');

        $possible_files = $this->db->get(db_prefix() . $table)->result_array();

        if ($possible_files) {
            foreach ($possible_files as $file) {
                if (prchat_checkMessageIfFileExists($file['message'])) {
                    $file_name = getImageFullName($file['message']);

                    if ($file_name == 'audio&amp;gt;') {
                        $file['message'] = str_replace('&amp;lt;audio controls src=&quot;', "", $file['message']);
                        $file['message'] = str_replace("&quot; type=&quot;audio/ogg&quot;&amp;gt;&amp;lt;/audio&amp;gt;", "", $file['message']);
                        $file_name = pathinfo($file['message'], PATHINFO_BASENAME);
                        if (strlen($file_name)) {
                            if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio')) {
                                @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio/' . $file_name);
                            }
                        }
                    }

                    if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
                        @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $file_name);
                    }
                    $this->db->delete(db_prefix() . 'chatsharedfiles', ['sender_id' => get_staff_user_id(), 'reciever_id' => $id, 'file_name' => $file_name]);
                    $this->db->delete(db_prefix() . 'chatsharedfiles', ['sender_id' => $id, 'reciever_id' => get_staff_user_id(), 'file_name' => $file_name]);
                }
            }

            $this->db->where('sender_id = ' . $id . ' AND reciever_id = ' . $staff_id . '');
            $this->db->or_where('reciever_id = ' . $id . ' AND sender_id = ' . $staff_id . '');

            $files_deleted = $this->db->delete(db_prefix() . $table);
        } else {
            return $response;
        }

        if ($files_deleted) {
            $response['success'] = true;

            return $response;
        }

        return $response;
    }


    /**
     * Delete chat conversation including pictures and files (works for clients and staff members and groups).
     *
     * @param $type 'staff, clients, groups or all'
     *
     * @return bool
     */
    public function purgeConversations($type)
    {
        $response['success'] = false;

        if ($type == 'all') {
            try {
                $this->db->truncate(db_prefix() . 'chatmessages');
                $this->db->truncate(db_prefix() . 'chatclientmessages');
                $this->db->truncate(db_prefix() . 'chatgroupmessages');
                $this->db->truncate(db_prefix() . 'chatgroupsharedfiles');
                $this->db->truncate(db_prefix() . 'chatsharedfiles');

                $folder_path = PR_CHAT_MODULE_UPLOAD_FOLDER;

                $folders = glob($folder_path . '/*');

                foreach ($folders as $folder) {

                    if (is_dir($folder)) {
                        $files = list_files($folder);

                        foreach ($files as $fileTodelete) {
                            if (strpos($folder, 'index.html') !== true) {
                                @unlink($folder . '/' . $fileTodelete);
                            }
                        }
                    }
                    if (strpos($folder, 'index.html') != true) {
                        {
                            @unlink($folder);
                        }
                    }
                }
            } catch (Exception $e) {
                return $response['error'] = $e->getMessage();
            }

            return $response['success'] = true;
        }

        if ($type == 'staff') $table = db_prefix() . 'chatmessages';
        if ($type == 'clients') $table = db_prefix() . 'chatclientmessages';
        if ($type == 'groups') $table = db_prefix() . 'chatgroupmessages';

        $sharedFilesTable = db_prefix() . 'chatsharedfiles';

        if ($type == 'groups') {
            $sharedFilesTable = db_prefix() . 'chatgroupsharedfiles';

        }

        $where = "time_sent < DATE_SUB(NOW(), INTERVAL 3 MONTH)";

        $this->db->select('*');
        $this->db->from($table);

        $this->db->where($where);

        $possible_files = $this->db->get()->result_array();

        if ($possible_files) {
            foreach ($possible_files as $file) {
                if (prchat_checkMessageIfFileExists($file['message'])) {

                    $file_name = getImageFullName($file['message']);

                    if ($file_name == 'audio&amp;gt;') {
                        $file['message'] = str_replace('&amp;lt;audio controls src=&quot;', "", $file['message']);
                        $file['message'] = str_replace("&quot; type=&quot;audio/ogg&quot;&amp;gt;&amp;lt;/audio&amp;gt;", "", $file['message']);
                        $file_name = pathinfo($file['message'], PATHINFO_BASENAME);

                        if (strlen($file_name)) {
                            if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio')) {
                                @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/audio/' . $file_name);
                            }
                        }
                    }

                    if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
                        @unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $file_name);
                    }

                    if ($type == 'groups') {
                        $this->db->delete($sharedFilesTable, ['sender_id' => $file['sender_id'], 'file_name' => $file_name]);
                    } else {
                        $this->db->delete($sharedFilesTable, ['sender_id' => $file['reciever_id'], 'reciever_id' => $file['sender_id'], 'file_name' => $file_name]);
                        $this->db->delete($sharedFilesTable, ['sender_id' => $file['sender_id'], 'reciever_id' => $file['reciever_id'], 'file_name' => $file_name]);
                    }
                }
            }

            $this->db->select('*');
            $this->db->from($table);
            $this->db->where($where);


            $response['success'] = $this->db->delete();

        } else {
            return $response;
        }

        return $response;

    }

    /**
     * Handles shared files between two users.
     *
     * @param string $own_id session id
     * @param        $contact_id
     *
     * @return string
     */
    public function get_shared_files_and_create_template($own_id, $contact_id)
    {
        $files = [];
        $data_lity = ' ';
        $allFiles = 'unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css|jpeg|jpg|png|swf|PNG|JPG|JPEG';
        $photoExtensions = 'unknown|jpeg|jpg|png|gif|swf|PNG|JPG|JPEG|';
        $docFiles = 'unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css';

        $dir = list_files(PR_CHAT_MODULE_UPLOAD_FOLDER);

        $from_messages_table = $this->db->query('SELECT file_name FROM ' . db_prefix() . 'chatsharedfiles' . " WHERE file_name REGEXP '^.*\.(" . $allFiles . ")$' AND sender_id  = '" . $own_id . "' AND reciever_id = '" . $contact_id . "' OR sender_id = '" . $contact_id . "' AND reciever_id = '" . $own_id . "'");
        if ($from_messages_table) {
            $from_messages_table = $from_messages_table->result_array();
        } else {
            return false;
        }
        foreach ($dir as $file_name) {
            foreach ($from_messages_table as $value) {
                if (strpos($file_name, $value['file_name']) !== false) {
                    if (!in_array($file_name, $files)) {
                        array_push($files, $file_name);
                    }
                }
            }
        }

        $html = '';
        $html .= '<ul class="nav nav-tabs" role="tablist">';
        $html .= '<li class="active"><a href="#photos" role="tab" data-toggle="tab"><i class="fa-regular fa-file-image icon_shared_files" aria-hidden="true"></i>' . _l('chat_photos_text') . '</a></li>';
        $html .= '<li><a href="#files" role="tab" data-toggle="tab"><i class="fa fa-file-o icon_shared_files" aria-hidden="true"></i>' . _l('chat_files_text') . '</a></li>';
        $html .= '</ul>';

        $html .= '<div class="tab-content">';
        $html .= '<div class="tab-pane active" id="photos">';
        $html .= '<span class="text-center shared_items_span">' . _l('chat_shared_photos_text') . '</span>';

        foreach ($files as $file) {
            if (preg_match("/^[^\?]+\.('" . $photoExtensions . "')$/", $file)) {
                $html .= "<a data-lity href='" . base_url('modules/prchat/uploads/' . $file) . "'>
               <div class='col-xs-3 shared_files_ahref' style='background-image:url(" . base_url('modules/prchat/uploads/' . $file) . ");'></div></a>";
            }
        }
        $html .= '</div>';
        $html .= '<div class="tab-pane" id="files">';
        $html .= '<span class="text-center shared_items_span">' . _l('chat_shared_files_text') . '</span>';

        foreach ($files as $file) {
            if (strpos($file, '.pdf')) {
                $data_lity = 'data-lity';
            }
            if (preg_match("/^[^\?]+\.('" . $docFiles . "')$/", $file)) {
                $html .= "<div class='col-md-12'><a target='_blank' " . $data_lity . " href ='" . base_url('modules/prchat/uploads/' . $file) . "'><i class='fa fa-file-o icon_shared_files' aria-hidden='true'></i>" . $file . '</a></div>';
            }
        }
        $html .= '</div></div>';

        return $html;
    }

    /**
     * Handles shared files between usersin group.
     *
     * @param string $group_id
     *
     * @return string
     */
    public function get_group_shared_files_and_create_template($group_id)
    {
        $files = [];
        $data_lity = ' ';
        $allFiles = 'unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css|jpeg|jpg|png|swf|PNG|JPG|JPEG';
        $photoExtensions = 'unknown|jpeg|jpg|png|gif|swf|PNG|JPG|JPEG|';
        $docFiles = 'unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css';

        $dir = list_files(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER);

        $from_messages_table = $this->db->query('SELECT file_name FROM ' . db_prefix() . 'chatgroupsharedfiles' . " WHERE file_name REGEXP '^.*\.(" . $allFiles . ")$' AND group_id = '" . $group_id . "'");

        if ($from_messages_table) {
            $from_messages_table = $from_messages_table->result_array();
        } else {
            return false;
        }
        foreach ($dir as $file_name) {
            foreach ($from_messages_table as $value) {
                if (strpos($file_name, $value['file_name']) !== false) {
                    if (!in_array($file_name, $files)) {
                        array_push($files, $file_name);
                    }
                }
            }
        }

        $html = '';
        $html .= '<ul class="nav nav-tabs" role="tablist">';
        $html .= '<li class="active"><a href="#group_photos" role="tab" data-toggle="tab"><i class="fa-regular fa-file-image icon_shared_files" aria-hidden="true"></i>' . _l('chat_photos_text') . '</a></li>';
        $html .= '<li><a href="#group_files" role="tab" data-toggle="tab"><i class="fa fa-file-o icon_shared_files" aria-hidden="true"></i>' . _l('chat_files_text') . '</a></li>';
        $html .= '</ul>';

        $html .= '<div class="tab-content">';
        $html .= '<div class="tab-pane active" id="group_photos">';
        $html .= '<span class="text-center shared_items_span">' . _l('chat_shared_photos_text') . '</span>';

        foreach ($files as $file) {
            if (preg_match("/^[^\?]+\.('" . $photoExtensions . "')$/", $file)) {
                $html .= "<a data-lity href='" . base_url('modules/prchat/uploads/groups/' . $file) . "'>
               <div class='col-md-3 shared_files_ahref' style='background-image:url(" . base_url('modules/prchat/uploads/groups/' . $file) . ");'></div></a>";
            }
        }
        $html .= '</div>';
        $html .= '<div class="tab-pane" id="group_files">';
        $html .= '<span class="text-center shared_items_span">' . _l('chat_shared_files_text') . '</span>';

        foreach ($files as $file) {
            if (strpos($file, '.pdf')) {
                $data_lity = 'data-lity';
            }
            if (preg_match("/^[^\?]+\.('" . $docFiles . "')$/", $file)) {
                $html .= "<div class='col-md-12'><a target='_blank' " . $data_lity . " href ='" . base_url('modules/prchat/uploads/groups/' . $file) . "'><i class='fa fa-file-o icon_shared_files' aria-hidden='true'></i>" . $file . '</a></div>';
            }
        }
        $html .= '</div></div>';

        return $html;
    }

    /**
     * globalMessage Sends global message to selected members
     *
     * @param array    $members
     * @param string   $message
     * @param instance $pusher
     *
     * @return boolean
     */
    public function globalMessage($members, $message, $pusher)
    {
        if ($message == '' || empty($members)) {
            return false;
        }

        if (isset($members) && (is_array($members) && !empty($members))) {

            $message = _l('chat_message_announce') . $message;

            foreach ($members as $member_id) {

                $this->chat_model->createMessage(
                    [
                        'sender_id'   => get_staff_user_id(),
                        'reciever_id' => $member_id,
                        'message'     => htmlspecialchars($message),
                        'time_sent'   => date('Y-m-d H:i:s'),
                        'viewed'      => 0,
                    ],
                    db_prefix() . 'chatmessages'
                );

                $pusher->trigger(
                    'presence-mychanel',
                    'send-event',
                    [
                        'message' => pr_chat_convertLinkImageToString($message),
                        'from'    => get_staff_user_id(),
                        'to'      => $member_id,
                        'global'  => true,
                    ]
                );
            }

            return true;
        }
    }


    /**
     * Announcement Event to clients
     *
     * @param array    $clients
     * @param string   $message
     * @param instance $pusher
     *
     * @return boolean
     */
    public function announcementToClients($clients, $message, $pusher)
    {
        if ($message == '' || empty($clients)) {
            return false;
        }

        $staffUserId = get_staff_user_id();

        if (isset($clients) && (is_array($clients) && !empty($clients))) {

            foreach ($clients as $client_id) {

                $this->chat_model->createMessage(
                    [
                        'sender_id'   => 'staff_' . $staffUserId,
                        'reciever_id' => 'client_' . $client_id,
                        'message'     => htmlspecialchars($message),
                        'time_sent'   => date('Y-m-d H:i:s')
                    ],
                    db_prefix() . 'chatclientmessages'
                );

                $pusher->trigger(
                    'presence-clients',
                    'send-event',
                    [
                        'message'     => pr_chat_convertLinkImageToString($message),
                        'from'        => 'staff_' . $staffUserId,
                        'from_name'   => get_staff_full_name($staffUserId),
                        'to'          => 'client_' . $client_id,
                        'massMessage' => true
                    ]
                );
            }
            return true;
        }
    }


    /**
     * Function that handles new chat group creation.
     *
     * @param array    $insertData
     * @param array    $data
     * @param instance $pusher
     */
    public function addChatGroup($insertData, $data, $pusher)
    {
        $own_id = $this->session->userdata('staff_user_id');

        $this->db->insert(TABLE_CHATGROUPS, $insertData);

        $insert_id = $this->db->insert_id();

        $presence_data = $data;

        foreach ($data['members'] as $member_id) {
            $this->db->insert(TABLE_CHATGROUPMEMBERS, ['group_name' => $data['group_name'], 'member_id' => $member_id, 'group_id' => $insert_id]);
        }

        $presence_data['group_id'] = $insert_id;
        $presence_data['result'] = 'success';
        $presence_data['created_by_id'] = $own_id;
        $presence_data['message'] = _l('chat_new_group_created_text');

        if ($pusher->trigger('group-chat', 'group-chat', $presence_data)) {
            echo json_encode(['data' => $presence_data]);
        } else {
            echo json_encode(['result' => 'error']);
        }
    }

    /**
     * Function that fetches all logged in user chat groups
     *
     * @return json
     */
    public function getMyGroups()
    {
        $id = get_staff_user_id();

        $groups = $this->db->query('SELECT * from ' . TABLE_CHATGROUPS . ' ORDER BY id ASC')->result_array();

        $this->db->trans_start();

        foreach ($groups as $key => $group) {
            $groups[$key]['members'] = $this->db->query('SELECT member_id, firstname, lastname, group_id FROM ' . TABLE_CHATGROUPMEMBERS . ' JOIN ' . TABLE_STAFF . ' ON ' . TABLE_STAFF . '.staffid=' . TABLE_CHATGROUPMEMBERS . '.member_id WHERE group_id=' . $group['id'] . ' AND member_id=' . $id . '')->result_array();
        }

        if ($this->db->trans_complete()) {
            if (!empty($groups)) {
                echo json_encode(['groups' => $groups]);
            } else {
                echo json_encode(['noChannels' => true]);
            }
        }
    }

    /**
     * Function that fetches all logged in user chat groups
     *
     * @return json
     */
    public function getChatGroups()
    {
        $id = get_staff_user_id();

        $this->db->where('member_id', $id);
        $groups = $this->db->get(db_prefix() . 'chatgroupmembers')->result_array();

        if ($groups) {
            foreach ($groups as &$group) {
                $group['group_name'] = str_replace('presence-', '', $group['group_name']);
            }
        }

        return $groups;
    }

    /**
     * Function that is responsible when deleting a selected group.
     *
     * @param string   $group_id
     * @param string   $group_name
     * @param instance $pusher
     *
     * @return void
     */
    public function deleteGroup($group_id, $group_name, $pusher)
    {
        $group_members = $this->db->get(TABLE_CHATGROUPMEMBERS)->result_array();

        $this->db->trans_start();
        foreach ($group_members as $member) {
            if ($member['group_id'] == $group_id) {

                $this->chat_model->deleteGroupSharedFiles($group_id);

                $this->db->where('group_id', $group_id);
                $this->db->delete(TABLE_CHATGROUPMEMBERS);

                $this->db->where('group_id', $group_id);
                $this->db->delete(TABLE_CHATGROUPMESSAGES);
            }
        }

        if ($this->db->trans_complete()) {
            $this->db->where('id', $group_id);
            $this->db->delete(TABLE_CHATGROUPS);

            $presence_data = ['result' => 'true', 'group_name' => $group_name, 'group_id' => $group_id];
            $pusher->trigger($group_name, 'group-deleted', $presence_data);

            if ($this->db->affected_rows() !== 0) {
                echo json_encode(['result' => 'success']);
            } else {
                echo json_encode(['error' => 'nomore']);
            }
        } else {
            echo json_encode(['result' => 'failed']);
        }
    }

    /**
     * Function that handles when adding new members to chat groups.
     *
     * @param string   $group_name
     * @param string   $group_id
     * @param array    $members
     * @param instance $pusher
     */
    public function addChatGroupMembers($group_name, $group_id, $members, $pusher)
    {
        $member_ids = [];

        foreach ($members as $member_id) {
            $this->db->where('group_name', $group_name);
            $this->db->where('group_id', $group_id);
            $this->db->insert(TABLE_CHATGROUPMEMBERS, ['group_name' => $group_name, 'member_id' => $member_id, 'group_id' => $group_id]);
            array_push($member_ids, $member_id);
        }

        $presence_data = [
            'group_name' => $group_name,
            'result'     => 'success',
            'group_id'   => $group_id,
            'user_ids'   => $member_ids,
        ];

        if ($this->db->affected_rows() != 0) {
            if ($pusher->trigger('group-chat', 'added-to-channel', $presence_data)) {
                echo json_encode(['data' => $presence_data]);
            }
        } else {
            echo json_encode(['data' => 'failed']);
        }
    }

    /**
     * Function that fetches all chat group members.
     *
     * @param string @group_id
     *
     * @return void
     */
    public function getGroupUsers($group_id)
    {
        $group_name = $this->db->get_where(TABLE_CHATGROUPS, ['id' => $group_id])->row('group_name');

        $this->db->select('member_id,  group_id, lastname, firstname, created_by_id');
        $this->db->from(TABLE_CHATGROUPMEMBERS);
        $this->db->where('group_id', $group_id);
        $this->db->join(TABLE_STAFF, '' . TABLE_STAFF . '.staffid=' . TABLE_CHATGROUPMEMBERS . '.member_id');
        $this->db->join(TABLE_CHATGROUPS, '' . TABLE_CHATGROUPS . '.id=' . TABLE_CHATGROUPMEMBERS . '.group_id');
        $query = $this->db->get();
        $groupUsers = $query->result_array();

        echo json_encode(['result' => 'success', 'users' => $groupUsers]);
    }

    /**
     * Function that fetches all members connected with specific group.
     *
     * @param string $group_id
     *
     * @return array
     */
    public function getCurrentGroupUsers($group_id)
    {

        $group_id = $this->input->post('group_id');
        $this->db->select('member_id, lastname, firstname');
        $this->db->from(TABLE_CHATGROUPMEMBERS);
        $this->db->where('group_id', $group_id);
        $this->db->join(TABLE_STAFF, TABLE_STAFF . '.staffid=' . TABLE_CHATGROUPMEMBERS . '.member_id');
        $query = $this->db->get();
        $users = $query->result_array();

        if ($this->db->affected_rows() !== 0) {
            return $users;
        }

        return [];
    }

    /**
     * Private function that checks if specific group is created by logged in user.
     *
     * @param string $group_id
     * @param string $user_id
     *
     * @return mixed
     */
    private function isGroupCreatedBy($group_id, $user_id)
    {
        $result = $this->db->get_where(TABLE_CHATGROUPS, ['id' => $group_id, 'created_by_id' => $user_id])->row('created_by_id');

        if ($result !== null) {
            return $result;
        }

        return false;
    }

    /**
     * Function that removes user from specific group
     *
     * @param string   $group_name
     * @param string   $group_id
     * @param string   $user_id
     * @param string   $own_id
     * @param object $pusher
     *
     * @return void
     */
    public function removeChatGroupUser($group_name, $group_id, $user_id, $own_id, $pusher)
    {
        $groupCreatedBy = $this->isGroupCreatedBy($group_id, $user_id);

        // This means that an admin has removed the creator of the group and group is assigned to this admin automatically
        if ($groupCreatedBy !== $own_id) {
            $this->db->where('created_by_id', $user_id);
            $this->db->update(TABLE_CHATGROUPS, ['created_by_id' => $own_id]);
        }

        $this->db->where('group_id', $group_id);
        $this->db->where('group_name', $group_name);
        $this->db->where('member_id', $user_id);
        $query = $this->db->delete(TABLE_CHATGROUPMEMBERS);

        $presence_data['group_id'] = $group_id;
        $presence_data['group_name'] = $group_name;
        $presence_data['user_id'] = $user_id;

        if ($query) {
            $presence_data['created_by_me'] = $this->isGroupCreatedBy($group_id, $own_id);
            $pusher->trigger($group_name, 'removed-from-channel', $presence_data);
            echo json_encode(['response' => 'success', 'data' => $presence_data]);
        } else {
            echo json_encode(['response' => 'error']);
        }
    }

    /**
     * Function that handles when user leaves chat group.
     *
     * @param string   $group_id
     * @param string   $member_id
     * @param instance $pusher
     *
     * @return void
     */
    public function chatMemberLeaveGroup($group_id, $member_id, $pusher)
    {
        $userFullName = get_staff_full_name();

        $group_name = $this->db->get_where(TABLE_CHATGROUPS, ['id' => $group_id])->row('group_name');

        $this->db->where('group_id', $group_id);
        $this->db->where('member_id', $member_id);
        $deleted = $this->db->delete(TABLE_CHATGROUPMEMBERS);

        $presence_data = [
            'group_name'     => $group_name,
            'group_id'       => $group_id,
            'member_id'      => $member_id,
            'user_full_name' => $userFullName,
        ];

        if ($deleted) {
            $pusher->trigger('group-chat', 'member-left-channel', $presence_data);
            echo json_encode(['message' => 'deleted']);
        }
    }

    /**
     * Record clients and customer admins message into database.
     *
     * @param array @message_data
     *
     * @return bool
     */
    public function recordClientMessage($message_data)
    {
        if ($this->db->insert(db_prefix() . 'chatclientmessages', $message_data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Live ajax search for clients
     *
     * @param string @search
     *
     * @return array
     */
    public function searchClients($search)
    {
        $this->db->select(db_prefix() . 'clients.userid as client_id, ' . db_prefix() . 'contacts.id as contact_id, company, firstname, lastname, title');

        $this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid');

        $whereCondition = "company LIKE '%" . $search . "%'  OR firstname LIKE '%" . $search . "%' OR lastname LIKE '%" . $search . "%' ";

        $this->db->where($whereCondition);

        $this->db->from(db_prefix() . 'clients');

        $query = $this->db->get()->result_array();

        if (count($query) > 0) {
            foreach ($query as $key => $contact) {
                $query[$key]['profile_image_url'] = contact_profile_image_url($contact['contact_id']);
            }
        }

        return $query;
    }

    /**
     * Live ajax search for staff
     *
     * @param string @search
     *
     * @return array
     */
    public function searchStaff($search)
    {
        $this->db->where("firstname LIKE '%" . $search . "%' OR lastname LIKE '%" . $search . "%' ");
        $this->db->from(db_prefix() . 'staff');

        $query = $this->db->get()->result_array();

        if (count($query) > 0) {
            foreach ($query as $key => $staff) {
                $query[$key]['profile_image_url'] = staff_profile_image_url($staff['staffid']);
            }
        }

        return $query;
    }

    public function appendMoreStaff($offset)
    {
        $this->db->select('staffid, firstname, lastname, profile_image');

        return $this->db->get(db_prefix() . 'staff', 10, $offset)->result_array();
    }

    /**
     * Get customer  company name
     *
     * @param string @client_id
     *
     * @return object
     */
    public function getClientCompanyName($client_id)
    {
        $this->db->select('company as name');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where('userid', $client_id);

        return $this->db->get()->row();
    }


    /**
     * Get staff message history (client ? staff )
     *
     * @param string @user_id
     * @param string @table
     *
     * @return array
     */
    public function getMessagesHistoryBetween($user_id, $table)
    {
        $to = get_staff_user_id();
        $contact_full_name = '';

        if ($table === 'chatclientmessages') {
            $to = 'staff_' . $to;
            $contact_full_name = get_contact_full_name(str_replace('client_', '', $user_id));
        }

        $sql = 'SELECT * FROM ' . db_prefix() . '' . $table . " WHERE (sender_id = '{$to}' AND reciever_id = '{$user_id}') OR (sender_id = '{$user_id}' AND reciever_id = '{$to}') ORDER BY id DESC";

        $query = $this->db->query($sql)->result_array();

        foreach ($query as &$chat) {
            $chat['message'] = pr_chat_convertLinkImageToString($chat['message']);
            $chat['message'] = stripslashes(htmlentities($chat['message'], ENT_QUOTES));

            $chat['message'] = str_replace("&amp;lt;a class=&amp;quot;chat_ticket_link&amp;quot; href=&amp;quot;", "", $chat['message']);
            $chat['message'] = str_replace("&amp;quot; target=&amp;quot;_blank&amp;quot;&amp;gt;", " - ", $chat['message']);
            $chat['message'] = str_replace("&amp;lt;/a&amp;gt;", "", $chat['message']);
            $chat['message'] = str_replace("&amp;amp;lt;audio controls src=&amp;quot;", "", $chat['message']);
            $chat['message'] = str_replace("&amp;quot; type=&amp;quot;audio/ogg&amp;quot;&amp;amp;gt;&amp;amp;lt;/audio&amp;amp;gt;", "", $chat['message']);


            /**
             * New line break blows return json so need to replace with spaces
             */
            if (strpos($chat['message'], "\n") !== false) {
                $chat['message'] = preg_replace('/\s+/', ' ', trim($chat['message']));
            }

            $chat['sender_fullname'] = get_staff_full_name(str_replace('staff_', '', $chat['sender_id']));
            ($contact_full_name !== '') ? $chat['contact_fullname'] = $contact_full_name : '';
            $chat['user_image_path'] = contact_profile_image_url(str_replace('staff_', '', $chat['sender_id']));
        }

        // Must put in session because it messes up everything down when searching, this way works perfect
        $this->session->set_userdata('chat_messages_count', sizeof($query));

        if ($query) {
            return $query;
        }
    }

    /**
     * Export messages to CSV file
     *
     * @param string $to
     *
     * @return void
     */
    public function initiateExportToCSV($to)
    {
        $table = 'chatmessages';

        $staff_id = get_staff_user_id();

        if (strpos($to, 'client') !== false) {
            $staff_id = 'staff_' . get_staff_user_id() . '';
            $table = 'chatclientmessages';
        } else {
            $staff_id = get_staff_user_id();
        }

        $filename = 'messages_' . date('Ymd') . '.csv';

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");


        $sql = 'SELECT * FROM ' . db_prefix() . "" . $table . " WHERE (sender_id = '{$to}' AND reciever_id = '{$staff_id}') OR (sender_id = '{$staff_id}' AND reciever_id = '{$to}') ORDER BY id DESC";


        $user_messages = $this->db->query($sql)->result_array();


        foreach ($user_messages as &$user) {
            // Get system default time format
            $user['time_sent'] = _dt($user['time_sent']);

            if ($table == 'chatclientmessages') {
                (strpos($user['sender_id'], 'client') !== false)
                    ? $user['sender_id'] = get_contact_full_name(str_replace('client_', '', $user['sender_id']))
                    : $user['sender_id'] = get_staff_full_name(str_replace('staff_', '', $user['sender_id']));

                (strpos($user['reciever_id'], 'client') !== false)
                    ? $user['reciever_id'] = get_contact_full_name(str_replace('client_', '', $user['reciever_id']))
                    : $user['reciever_id'] = get_staff_full_name(str_replace('staff_', '', $user['reciever_id']));
            } else {
                $user['sender_id'] = get_staff_full_name($user['sender_id']);
                $user['reciever_id'] = get_staff_full_name($user['reciever_id']);
            }
        }

        // Create new csv file
        $file = fopen('php://output', 'w');

        $header = [
            "" . _l('chat_header_message_id') . "",
            "" . _l('chat_header_from') . "",
            "" . _l('chat_header_send_to') . "",
            "" . _l('chat_header_message') . "",
            "" . _l('chat_header_is_read') . "",
            "" . _l('chat_header_deleted') . "",
            "" . _l('chat_viewed_at_datetime') . "",
            "" . _l('chat_header_datetime') . "",
        ];


        if ($table == 'chatclientmessages') {
            unset($header[5]);
        }

        fputcsv($file, $header);

        foreach ($user_messages as $line) {
            $line['message'] = '[ ' . _l("chat_export_message") . $line["message"] . ' ]';
            $line['message'] = str_replace("&lt;a class=&quot;chat_ticket_link&quot; href=&quot;", "", $line['message']);
            $line['message'] = str_replace("&quot; target=&quot;_blank&quot;&gt;", " - ", $line['message']);
            $line['message'] = str_replace("&lt;/a&gt;", "", $line['message']);
            $line['message'] = str_replace("&amp;lt;audio controls src=&quot;", "", $line['message']);
            $line['message'] = str_replace("&quot; type=&quot;audio/ogg&quot;&amp;gt;&amp;lt;/audio&amp;gt;", "", $line['message']);
            fputcsv($file, $line);
        }

        fclose($file);
        exit;
    }


    /**
     * Handle Messages and get ready for support ticket conversation
     *
     * @param $user_id
     *
     * @return array
     */
    public function getMessagesForTicketConversion($user_id)
    {
        $to = get_staff_user_id();
        $contact_full_name = '';

        $table = 'chatclientmessages';
        $to = 'staff_' . $to;
        $contact_full_name = get_contact_full_name(str_replace('client_', '', $user_id));


        $sql = 'SELECT id, message, sender_id, reciever_id, DATE_FORMAT(time_sent, "%Y-%m-%d") as time_sent FROM ' . db_prefix() . '' . $table . "  WHERE time_sent > NOW() - INTERVAL 48 HOUR AND (sender_id = '{$to}' AND reciever_id = '{$user_id}') OR (sender_id = '{$user_id}' AND reciever_id = '{$to}') AND time_sent > NOW() - INTERVAL 48 HOUR ORDER BY time_sent DESC";


        $query = $this->db->query($sql)->result_array();

        foreach ($query as &$chat) {
            $chat['message'] = stripslashes(htmlentities($chat['message'], ENT_QUOTES));
            $chat['sender_fullname'] = get_staff_full_name(str_replace('staff_', '', $chat['sender_id']));
            ($contact_full_name !== '') ? $chat['contact_fullname'] = $contact_full_name : '';

            if (strpos($chat['message'], "audio controls src=") != false) {
                $chat['message'] = str_replace("&amp;amp;lt;audio controls src=&amp;quot;", "", $chat['message']);
                $chat['message'] = str_replace("&amp;quot; type=&amp;quot;audio/ogg&amp;quot;&amp;amp;gt;&amp;amp;lt;/audio&amp;amp;gt;", "", $chat['message']);
            }
        }

        $this->session->set_userdata('chat_support_ticket_messages_count', sizeof($query));

        if ($query) {
            return $query;
        }
    }

    /**
     * Handle chat support ticket conversation
     *
     * @param array  $data
     * @param string $subject
     * @param string $department
     * @param string $assigned
     *
     * @return bool|\json
     */
    public function chatHandleSupportTicketCreation($data, $subject, $department, $assigned)
    {
        $html = '';

        if ($data === null) {
            echo json_encode(['message' => 'no_message']);
            return false;
        }

        foreach ($data as &$content) {
            $class = '';
            if (strpos($content['user_id'], 'client') !== false) {
                $class = 'chat_client_msg_style';
                $content['user_name'] = '<strong class="text-muted">' . _l('chat_lang_contact') . '</strong> ' . $content['user_name'];
            } else {
                $class = 'chat_staff_msg_style';
                $content['user_name'] = '<strong class="text-primary">' . _l('chat_staff_member') . '</strong> ' . $content['user_name'];
            }
            $html .= "<p class=" . $class . ">" . $content['user_name'] . " \n <strong>Message:</strong> " . $content['message'] . "</p>";
        }

        $client_id = str_replace('client_', '', $data[0]['client_id']);

        $update_data = [
            'subject'    => $subject,
            'admin'      => get_staff_user_id(),
            'message'    => $html,
            'userid'     => get_contact_customer_user_id($client_id),
            'contactid'  => $client_id,
            'department' => $department,
        ];

        if ($assigned !== 0) {
            $update_data['assigned'] = get_staff_user_id();
        }

        $this->load->model('tickets_model');

        if ($this->tickets_model->add($update_data)) {
            echo json_encode(
                [
                    'message'   => 'success',
                    'client_id' => $client_id,
                    'ticket_id' => chat_get_tickets_last_inserted_row()
                ]
            );
        } else {
            echo json_encode(['success' => 'error']);
        }
    }

    /**
     * Update chat status
     *
     * @param string
     *
     * @return array
     */
    public function handleChatStatus($status)
    {
        $table = db_prefix() . 'chatsettings';
        $user_id = get_staff_user_id();
        $current_status = $this->db->get_where($table, ['user_id' => $user_id, 'name' => 'chat_status'])->result();

        if (empty($current_status)) {
            $this->db->insert($table, ['user_id' => $user_id, 'name' => 'chat_status', 'value' => $status]);
        } else {
            $this->db->where('user_id', $user_id);
            $this->db->where('name', 'chat_status');
            $this->db->update($table, ['value' => $status]);
        }
        if ($this->db->affected_rows() != 0) {
            return [
                'status'  => $status,
                'user_id' => $user_id
            ];
        }

        return [
            'status'  => 'same',
            'user_id' => $user_id
        ];
    }

    /**
     * Handles mention event in groups chat
     *
     * @param array @data
     * @param object Pusher
     *
     * @return void
     */
    public function handleMentionEvent($data, $pusher)
    {
        $userImage = $this->chat_model->getUserImage($data['from']);
        $staff_fullname = get_staff_full_name($data['from']);

        $notify_users = [];

        foreach ($data['users'] as $user) {
            $notify_users[] = $user['user_id'];

            add_notification([
                'description' => _l('chat_in_group') . ' ' . $data['channel'] . ' ' . $staff_fullname . ' ' . _l('chat_mentioned_you'),
                'touserid'    => $user['user_id'],
                'fromuserid'  => $data['from'],
                'fromcompany' => true,
                'link'        => 'prchat/Prchat_Controller/chat_full_view',
            ]);
        }
        pusher_trigger_notification(array_unique($notify_users));

        $pusher->trigger('presence-' . $data['channel'], 'mention-event', [
            'group_name' => $data['channel'],
            'from'       => $data['from'],
            'users'      => $data['users'],
            'userImage'  => $userImage,
            'name'       => $staff_fullname
        ]);
    }


    /**
     * Handle base64 audio data from recording
     *
     * @param $audioB64Data
     *
     * @return void
     */
    public function handleAudioData($audioB64Data)
    {

        $audioB64Data = str_replace('data:audio/ogg;base64,', '', str_replace('[removed]', 'data:audio/ogg;base64, ', $audioB64Data));

        $decodedAudio = base64_decode($audioB64Data);

        $hashFilename = app_generate_hash() . '-' . app_generate_hash();

        $fileSaveLocation = PR_CHAT_MODULE_UPLOAD_FOLDER . "/audio/{$hashFilename}.ogg";

        try {
            if (file_put_contents($fileSaveLocation, $decodedAudio)) {
                echo json_encode(['filename' => $hashFilename . '.ogg']);
            }
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Whoops! Something went wrong... and the message is: ' . $e->getMessage() . '']);
        }
    }


    /**
     * Trigger new message seen event
     *
     * @param object $pusher
     * @param array  $messages
     *
     * @return void
     */
    public function setMessageAsViewed($pusher, $messages)
    {
        $pusher->trigger('user_messages', 'message_seen', $messages);
    }

}
