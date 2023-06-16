<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex CRM Powerful Chat
Description: Chat Module for Perfex CRM
Author: Aleksandar Stojanov
Author URI: https://idevalex.com
*/

class Prchat_Controller extends AdminController
{

    /**
     * Stores the pusher options.
     *
     * @var array
     */
    protected $pusher_options = [];

    /**
     * Hold Pusher instance.
     *
     * @var object
     */
    protected $pusher;

    /**
     * Class constructor / Pusher logic
     */
    public function __construct()
    {
        parent::__construct();

        if ( ! get_option('pusher_chat_enabled') == '1') {
            redirect('admin');
        }

        if ( ! defined('PR_CHAT_MODULE_NAME')) {
            show_404();
        }

        if ( ! staff_can('view', PR_CHAT_MODULE_NAME)) {
            access_denied(_l('chat_access_label'));
        }

        $this->load->model('prchat_model', 'chat_model');

        $this->pusher_options['app_key'] = get_option('pusher_app_key');
        $this->pusher_options['app_secret'] = get_option('pusher_app_secret');
        $this->pusher_options['app_id'] = get_option('pusher_app_id');

        if (
            get_option('pusher_app_key') == '' ||
            get_option('pusher_app_secret') == '' ||
            get_option('pusher_app_id') == '' ||
            get_option('pusher_cluster') == ''
        ) {
            echo '<h1>Seems that your Pusher account it is not setup correctly.</h1>';
            echo '<h4>Setup Pusher now: <a href="' . site_url('admin/settings?group=pusher') . '">Perfex CRM Settings->Pusher.com</a></h4>';
            echo '<h4>Tutorial: <a target="blank" href="https://help.perfexcrm.com/setup-realtime-notifications-with-pusher-com/">See example how to setup Pusher from Perfex CRM documentation</a>';
            die;
        }

        if (get_option('pusher_cluster') != '') {
            $this->pusher_options['cluster'] = get_option('pusher_cluster');
        }
        $this->pusher = new Pusher\Pusher(
            $this->pusher_options['app_key'],
            $this->pusher_options['app_secret'],
            $this->pusher_options['app_id'],
            ['cluster' => $this->pusher_options['cluster']]
        );
    }

    /**
     * Messaging events
     *
     * @return void
     */
    public function initiateChat()
    {
        if ($this->input->post()) {
            if ($this->input->post('typing') == 'false') {
                $imageData['sender_image'] = $this->chat_model->getUserImage(get_staff_user_id());
                $imageData['receiver_image'] = $this->chat_model->getUserImage(str_replace('#', '', $this->input->post('to')));

                $from = $this->input->post('from');
                $receiver = str_replace('#', '', $this->input->post('to'));

                if (trim($this->input->post('msg')) !== '') {
                    $message_data = [
                        'sender_id'   => $this->input->post('from'),
                        'reciever_id' => str_replace('#', '', $this->input->post('to')),
                        'message'     => htmlentities($this->input->post('msg')),
                        'viewed'      => 0,
                        'time_sent'   => date("Y-m-d H:i:s"),
                    ];

                    $last_id = $this->chat_model->createMessage($message_data, db_prefix() . 'chatmessages');

                    $this->pusher->trigger('presence-mychanel', 'send-event', [
                        'message'        => pr_chat_convertLinkImageToString($this->input->post('msg')),
                        'from'           => $from,
                        'to'             => $receiver,
                        'from_name'      => get_staff_full_name($from),
                        'last_insert_id' => $last_id,
                        'sender_image'   => $imageData['sender_image'],
                        'receiver_image' => $imageData['receiver_image'],
                    ]);

                    $this->pusher->trigger(
                        'presence-mychanel',
                        'notify-event',
                        [
                            'from'         => $this->input->post('from'),
                            'to'           => str_replace('#', '', $this->input->post('to')),
                            'from_name'    => get_staff_full_name($from),
                            'sender_image' => $imageData['sender_image'],
                            'message'      => pr_chat_convertLinkImageToString($this->input->post('msg')),
                        ]
                    );
                }
            } else if ($this->input->post('typing') == 'true') {
                $this->pusher->trigger(
                    'presence-mychanel',
                    'typing-event',
                    [
                        'message' => $this->input->post('typing'),
                        'from'    => $this->input->post('from'),
                        'to'      => str_replace('#', '', $this->input->post('to')),
                    ]
                );
            } else {
                $this->pusher->trigger(
                    'presence-mychanel',
                    'typing-event',
                    [
                        'message' => 'null',
                        'from'    => $this->input->post('from'),
                        'to'      => str_replace('#', '', $this->input->post('to')),
                    ]
                );
            }
        }
    }


    /**
     * Main function that handles, sending messages, notify events, typing events and inserts message data in database.
     *
     * @return websocket event
     */
    public function initiateGroupChat()
    {
        if ($this->input->post()) {
            $from = $this->input->post('from');
            $group_id = $this->input->post('group_id');
            $group_name = $this->db->get_where(TABLE_CHATGROUPS, ['id' => $group_id])->row('group_name');

            if ($this->input->post('typing') == 'false') {
                $imageData['sender_image'] = $this->chat_model->getUserImage(get_staff_user_id());

                $message_data = [
                    'sender_id' => $this->input->post('from'),
                    'group_id'  => $this->input->post('group_id'),
                    'message'   => htmlspecialchars($this->input->post('g_message')),
                    'time_sent' => date("Y-m-d H:i:s")
                ];

                $last_id = $this->chat_model->createGroupMessage($message_data);

                $this->pusher->trigger($group_name, 'group-send-event', [
                    'message'        => pr_chat_convertLinkImageToString($this->input->post('g_message')),
                    'from'           => $from,
                    'to_group'       => $group_id,
                    'from_name'      => get_staff_full_name($this->input->post('from')),
                    'group_name'     => $group_name,
                    'last_insert_id' => $last_id,
                    'sender_image'   => $imageData['sender_image'],
                ]);

                $this->pusher->trigger($group_name, 'group-notify-event', [
                    'from'         => $this->input->post('from'),
                    'from_name'    => get_staff_full_name($this->input->post('from')),
                    'to_group'     => $group_id,
                    'group_name'   => $group_name,
                    'sender_image' => $imageData['sender_image'],
                    'message'      => pr_chat_convertLinkImageToString($this->input->post('g_message')),
                ]);
            } else if ($this->input->post('typing') == 'true') {
                $this->pusher->trigger(
                    $group_name,
                    'group-typing-event',
                    [
                        'message'    => $this->input->post('typing'),
                        'from'       => $this->input->post('from'),
                        'to_group'   => $group_id,
                        'group_name' => $group_name,
                    ]
                );
            } else {
                $this->pusher->trigger(
                    $group_name,
                    'group-typing-event',
                    [
                        'message'    => 'test',
                        'from'       => $this->input->post('from'),
                        'to_group'   => $group_id,
                        'group_name' => $group_name,
                    ]
                );
            }
        }
    }

    /**
     * Get staff members for chat.
     *
     * @return void
     */
    public function users()
    {
        if ( ! $this->input->is_ajax_request()) {
            show_404();
        }
        $users = $this->chat_model->getUsers();
        if ($users) {
            echo json_encode($users, true);
        } else {
            die(_l('chat_error_table'));
        }
    }


    /**
     * Get staff members in json format
     *
     * @return void
     */
    public function getUsersInJsonFormat()
    {
        if ( ! $this->input->is_ajax_request()) {
            show_404();
        }

        $group_id = $this->input->get('group_id');

        if ($group_id) {
            $jsonFormattedUsers = $this->chat_model->getUsersInJsonFormat($group_id);
            header('Content-Type: application/json');

            if ($jsonFormattedUsers) {
                echo json_encode($jsonFormattedUsers, true);
            } else {
                die(_l('chat_error_table'));
            }
        }
    }

    /**
     * Get pusher key
     *
     * @return mixed
     */
    public function getKey()
    {
        if (isset($this->pusher_options['app_key']) && ! empty($this->pusher_options['app_key'])) {
            echo json_encode($this->pusher_options['app_key']);
        } else {
            die(_l('chat_app_key_not_found'));
        }
    }

    /**
     * Get staff that will be used for the chat window.
     *
     * @return json|false
     */
    public function getStaffInfo()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $response = $this->chat_model->getStaffInfo($id);

            if ($response) {
                echo json_encode($response);
            }
        }

        return false;
    }


    /**
     * Get logged in user messages sent to other user
     *
     * @return void
     */
    public function getMessages()
    {
        $limit = $this->input->get('limit');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        ($limit)
            ? $limit
            : $limit = 10;

        $offset = 0;
        $message = '';

        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }

        $response = $this->chat_model->getMessages($from, $to, $limit, $offset);

        if ($response) {
            echo json_encode($response);
        } else {
            $message = _l('chat_no_more_messages_in_database');
            echo json_encode($message);
        }
    }


    /**
     *  Get group messages.
     *
     * @return void
     */
    public function getGroupMessages()
    {
        $limit = $this->input->get('limit');
        $group_id = $this->input->get('group_id');
        $message = '';

        ($limit)
            ? $limit
            : $limit = 10;

        $offset = 0;

        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }

        $response = $this->chat_model->getGroupMessages($group_id, $limit, $offset);

        if ($response) {
            echo json_encode($response);
        } else {
            $message = _l('chat_no_more_messages_in_database');
            echo json_encode($message);
        }
    }


    /**
     * Get group messages history.
     *
     * @return void
     */
    public function getGroupMessagesHistory()
    {
        $limit = $this->input->get('limit');
        $group_id = $this->input->get('group_id');

        ($limit)
            ? $limit
            : $limit = 10;

        $offset = 0;
        $message = '';

        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }

        $response = $this->chat_model->getGroupMessagesHistory($group_id, $limit, $offset);

        if ($response) {
            echo json_encode($response);
        } else {
            $message = _l('chat_no_more_messages_in_database');
            echo json_encode($message);
        }
    }

    /**
     * Get unread messages, used when somebody sent a message while the user is offline.
     *
     * @param bool
     *
     * @return mixed
     */
    public function getUnread($return = false)
    {
        $result = $this->chat_model->getUnread();

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false]);
        }

        return false;
    }


    /**
     * Updated unread messages to read.
     *
     * @return void
     */
    public function updateUnread()
    {
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $result = $this->chat_model->updateUnread($this->pusher, $id);

            echo json_encode($result);
        }
    }


    /**
     * Pusher authentication.
     *
     * @return mixed
     * @throws \Pusher\PusherException
     */
    public function pusher_auth()
    {
        if ($this->input->get()) {
            $name = get_staff_full_name();
            $user_id = get_staff_user_id();
            $channel_name = $this->input->get('channel_name');
            $socket_id = $this->input->get('socket_id');

            if ( ! $channel_name) {
                exit('channel_name must be supplied');
            }

            if ( ! $socket_id) {
                exit('socket_id must be supplied');
            }

            if (
                ! empty($this->pusher_options['app_key'])
                && ! empty($this->pusher_options['app_secret'])
                && ! empty($this->pusher_options['app_id'])
            ) {
                $justLoggedIn = false;

                if ($this->session->has_userdata('prchat_user_before_login')) {
                    $this->session->unset_userdata('prchat_user_before_login');

                    $justLoggedIn = true;
                }

                $presence_data = [
                    'name'         => $name,
                    'justLoggedIn' => $justLoggedIn,
                    'status'       => '' . $this->chat_model->_get_chat_status() . ''
                ];

                $auth = $this->pusher->presence_auth($channel_name, $socket_id, $user_id, $presence_data);
                $callback = str_replace('\\', '', $this->input->get('callback'));
                header('Content-Type: application/javascript');
                echo $callback . '(' . $auth . ');';
            } else {
                exit('Appkey, secret or appid is missing');
            }
        }
    }


    /**
     * Upload method for files
     *
     * @return json
     */
    public function uploadMethod()
    {
        $allowedFiles = get_option('allowed_files');
        $allowedFiles = str_replace(',', '|', $allowedFiles);
        $allowedFiles = str_replace('.', '', $allowedFiles);

        $config = [
            'upload_path'   => PR_CHAT_MODULE_UPLOAD_FOLDER,
            'allowed_types' => $allowedFiles,
            'max_size'      => '9048000',
        ];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload()) {
            $from = $this->input->post()['send_from'];
            $to = str_replace('id_', '', $this->input->post()['send_to']);

            if (is_numeric($from) && is_numeric($to)) {
                $this->db->insert(
                    'tblchatsharedfiles',
                    [
                        'sender_id'   => $from,
                        'reciever_id' => $to,
                        'file_name'   => $this->upload->data('file_name'),
                    ]
                );
            }

            echo json_encode(['upload_data' => $this->upload->data()]);
        } else {
            echo json_encode(['error' => $this->upload->display_errors()]);
        }
    }


    /**
     * Uploads method for chat group files
     *
     * @return json
     */
    public function groupUploadMethod()
    {
        $allowedFiles = get_option('allowed_files');
        $allowedFiles = str_replace(',', '|', $allowedFiles);
        $allowedFiles = str_replace('.', '', $allowedFiles);

        $config = [
            'upload_path'   => PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER,
            'allowed_types' => $allowedFiles,
            'max_size'      => '9048000',
        ];

        $this->load->library('upload', $config);
        if ($this->upload->do_upload()) {
            $from = $this->input->post()['send_from'];
            $to_group = $this->input->post()['to_group'];

            $this->db->insert(
                'tblchatgroupsharedfiles',
                [
                    'sender_id' => $from,
                    'group_id'  => $to_group,
                    'file_name' => $this->upload->data('file_name'),
                ]
            );

            echo json_encode(['upload_data' => $this->upload->data()]);
        } else {
            echo json_encode(['error' => $this->upload->display_errors()]);
        }
    }


    /**
     * Resets toggled chat theme colors
     *
     * @return mixed
     */
    public function resetChatColors()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $user_id = get_staff_user_id();
        echo json_encode($this->chat_model->resetChatColors($user_id));
    }


    /**
     * Handles chat color change request.
     *
     * @return json
     */
    public function colorchange()
    {
        $id = get_staff_user_id();
        $color = trim($this->input->post('color'));

        if ($this->input->post('get_chat_color')) {
            echo json_encode(pr_get_chat_color($id));
        }

        if ($this->input->post('color')) {
            echo json_encode($this->chat_model->setChatColor($color));
        }
    }


    /**
     * Delete chat message
     *
     * @return json
     */
    public function deleteMessage()
    {
        if ( ! chatStaffCanDelete()) {
            access_denied();
        }

        $id = $this->input->post('id');
        $contact_id = $this->input->post('contact_id');

        if ($this->input->post('group_id')) {
            $group_id = $this->input->post('group_id');

            echo json_encode($this->chat_model->deleteMessage($id, 'group_id' . $group_id));
        } else {
            echo json_encode($this->chat_model->deleteMessage($id, $contact_id));
        }
    }


    /**
     * Delete chat client message
     *
     * @return mixed
     */
    public function deleteClientMessage()
    {
        if ( ! chatStaffCanDelete() || ! $this->input->is_ajax_request()) {
            access_denied();
        }

        $message_id = $this->input->post('message_id');

        if ($message_id) {
            echo json_encode($this->chat_model->deleteClientMessage($message_id));
        }
    }


    /**
     * Delete chat conversation
     *
     * @return mixed
     */
    public function deleteChatConversation()
    {
        if ( ! chatStaffCanDelete()) access_denied();

        if ($this->input->post('id')) {
            $id = $this->input->post('id');
            $table = $this->input->post('table');
            header('Content-Type: application/json');
            echo json_encode($this->chat_model->deleteMutualConversation($id, $table));
        }
    }


    /**
     * Switch user theme
     * Light or Dark.
     *
     * @return json
     */
    public function switchTheme()
    {
        $id = get_staff_user_id();
        $theme_name = $this->input->post('theme_name');

        echo json_encode($this->chat_model->updateChatTheme($id, $theme_name));
    }


    /**
     * Loads user full chat browser view.
     *
     * @return view
     */
    public function chat_full_view()
    {
        $result = $this->chat_model->getUnread();
        $this->load->view('prchat/chat_full_view', ['unreadMessages' => $result]);
    }


    /**
     * Handles shared files between two users.
     *
     * @return json
     */
    public function getSharedFiles()
    {
        if ($this->input->post()) {
            $own_id = $this->input->post('own_id');
            $contact_id = $this->input->post('contact_id');

            $html = $this->chat_model->get_shared_files_and_create_template($own_id, $contact_id);

            if ($html) {
                echo json_encode($html);
            }
        }
    }


    /**
     * Handles shared files between users in group.
     *
     * @return json
     */
    public function getGroupSharedFiles()
    {
        if ($this->input->post()) {
            $group_id = $this->input->post('group_id');

            $html = $this->chat_model->get_group_shared_files_and_create_template($group_id);

            if ($html) {
                echo json_encode($html);
            }
        }
    }


    /**
     *  Handles staff announcement modal view.
     *
     * @return view modal
     */
    public function staff_announcement()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $data['title'] = _l('chat_announcement_modal_text');
        $data['staff'] = $this->chat_model->getUsers();

        $this->load->view('prchat/includes/modal', $data);
    }


    /**
     *  Handles clients mass message modal view.
     *
     * @return view modal
     */
    public function clients_announcement_message()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $data['title'] = _l('chat_client_announcement_title');
        $data['clients'] = get_staff_customers(5000, 0, true);

        $this->load->view('prchat/includes/client_announcment_modal', $data);
    }


    /**
     * Handles data inserting for global message to selected clients.
     *
     * @return json
     */
    public function clients_announcement()
    {
        if ($this->input->post()) {
            $members = $this->input->post('clients');
            $message = $this->input->post('message');

            echo json_encode($this->chat_model->announcementToClients($members, $message, $this->pusher));
        }
    }


    /**
     *  Handles staff announcement modal view.
     *
     * @return view modal
     */
    public function quick_mentions($id = '')
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ( ! has_permission('tasks', '', 'edit') && ! has_permission('tasks', '', 'create')) {
            ajax_access_denied();
        }

        $data = [];

        $data['milestones'] = [];
        $data['checklistTemplates'] = [];
        $data['project_end_date_attrs'] = [];


        $this->load->view('prchat/includes/quick_mentions_modal', $data);
    }


    /**
     * Handles data inserting for global message to selected members.
     *
     * @return json
     */
    public function staff_get_selected_members()
    {
        if ($this->input->post()) {
            $members = $this->input->post('members');
            $message = $this->input->post('message');

            echo json_encode($this->chat_model->globalMessage($members, $message, $this->pusher));
        }
    }


    /**
     * Fetch chat groups
     *
     * @return view
     */
    public function chatGroups()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $data['title'] = _l('chat_group_modal_title');
        $data['staff'] = $this->chat_model->getUsers();

        $this->load->view('prchat/includes/groups_modal', $data);
    }


    /**
     * Loads new modal for creating new chat group.
     *
     * @return view
     */
    public function addNewChatGroupMembersModal()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $data['title'] = _l('chat_group_modal_add_title');
        $users = $this->chat_model->getUsers();
        $data['staff'] = [];
        $group_id = $this->input->get('group_id');
        $currentUsers = $this->getCurrentGroupUsers($group_id);

        foreach ($users as $selector => $staff) {
            foreach ($currentUsers as $currentUser) {
                if ($currentUser['member_id'] == $staff['staffid']) {
                    unset($users[$selector]);
                }
            }
        }

        $data['staff'] = $users;

        $this->load->view('prchat/includes/add_modal', $data);
    }


    /**
     * Adds new chat members to specific group.
     *
     * @return json
     */
    public function addChatGroupMembers()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ( ! empty($this->input->post('group_name'))) {
            $group_name = $this->input->post('group_name');
            $members = $this->input->post('members');
            $group_id = $this->input->post('group_id');

            return $this->chat_model->addChatGroupMembers($group_name, $group_id, $members, $this->pusher);
        }
    }


    /**
     * Create new chat group
     *
     * @return mixed
     */
    public function addChatGroup()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ($this->input->post('group_name')) {
            $data = [];

            $data['group_name'] = 'presence-' . slugifyGroupName($this->input->post('group_name'));

            $data['members'] = $this->input->post('members');

            $own_id = $this->session->userdata('staff_user_id');

            if (empty($data['members'])) {
                return false;
            }

            if ( ! in_array($own_id, $data['members'])) {
                array_push($data['members'], $own_id);
            }

            $insertData = [
                'created_by_id' => $own_id,
                'group_name'    => $data['group_name'],
            ];

            return $this->chat_model->addChatGroup($insertData, $data, $this->pusher);
        }
    }

    public function renameChatGroup()
    {
        $groupId = $this->input->post('groupId');
        $newName = $this->input->post('groupName');

        try {
            if ($groupId) {
                $this->db->where('id', $groupId)->update(db_prefix() . 'chatgroups', ['group_name' => 'presence-' . slugifyGroupName($newName)]);
                $this->db->where('group_id', $groupId)->update(db_prefix() . 'chatgroupmembers', ['group_name' => 'presence-' . slugifyGroupName($newName)]);
            }

            $this->pusher->trigger(
                'group-chat',
                'group-renamed',
                [
                    'group_id' => $groupId,
                    'newName'  => $newName,
                ]
            );
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            die;
        }

        echo json_encode(['success' => true]);
    }


    /**
     * Fetches all groups linked to current logged in user
     *
     * @return json
     */
    public function getMyGroups()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        return $this->chat_model->getMyGroups();
    }


    /**
     * Delete chat group
     *
     * @return json
     */
    public function deleteGroup()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ($this->input->post('group_id')) {
            $group_id = $this->input->post('group_id');
            $group_name = $this->input->post('group_name');

            return $this->chat_model->deleteGroup($group_id, $group_name, $this->pusher);
        }
    }


    /**
     * Get all group members
     *
     * @return json
     */
    public function getGroupUsers()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ($this->input->post('group_id') !== '') {
            $group_id = $this->input->post('group_id');

            return $this->chat_model->getGroupUsers($group_id);
        }
    }


    /**
     * Backup function that fetches all group members.
     *
     * @return mixed
     */
    public function getCurrentGroupUsers()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ($this->input->post('group_id') !== '') {
            $group_id = $this->input->post('group_id');
            $users = $this->chat_model->getCurrentGroupUsers($group_id);
            if (is_array($users) && ! empty($users)) {
                return $users;
            } else {
                return false;
            }
        }
    }


    /**
     * Remove user from group
     *
     * @return mixed
     */
    public function removeChatGroupUser()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $own_id = get_staff_user_id();

        if ($this->input->post('id')) {
            $group_name = $this->input->post('group_name');
            $user_id = $this->input->post('id');
            $group_id = $this->input->post('group_id');

            return $this->chat_model->removeChatGroupUser($group_name, $group_id, $user_id, $own_id, $this->pusher);
        } else {
            return false;
        }
    }


    /**
     * Chat members leaves group event
     *
     * @return mixed
     */
    public function chatMemberLeaveGroup()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        if ($this->input->post('group_id')) {
            $group_id = $this->input->post('group_id');
            $member_id = $this->input->post('member_id');

            return $this->chat_model->chatMemberLeaveGroup($group_id, $member_id, $this->pusher);
        }
    }


    /**
     * Downloads CSV file of exported messages from database between two users staff or clients
     *
     * @return void
     */
    public function exportCSV()
    {
        if ( ! is_admin()) {
            access_denied();
        }

        $to = $this->input->get('user');

        $this->chat_model->initiateExportToCSV($to);
    }


    /**
     * Conver to ticket load model view
     *
     * @return view
     */
    public function convertToTicket()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $id = $this->input->post('id');
        $table = 'chatclientmessages';

        $name = (strpos($id, 'client') !== false)
            ? get_contact_full_name(str_replace('client_', '', $id))
            : get_staff_full_name(get_staff_user_id());

        $data = [
            'id'             => $id,
            'user_full_name' => $name,
            'messages'       => $this->chat_model->getMessagesForTicketConversion($id, $table),
        ];

        $this->load->view('prchat/includes/convert_to_ticket_modal', $data);
    }


    /**
     * Create new support ticket
     *
     * @return string
     */
    public function createNewSupportTicket()
    {
        $data = [];

        $data = $this->input->post('content');
        $assigned = $this->input->post('assigned');
        $subject = $this->input->post('subject');
        $department = $this->input->post('department');

        return $this->chat_model->chatHandleSupportTicketCreation($data, $subject, $department, $assigned);
    }


    /**
     * Chat status update
     *
     * @return mixed
     */
    public function handleChatStatus()
    {
        $status = $this->input->post('status');

        if ( ! $status || ! $this->input->is_ajax_request()) {
            show_404();
        }

        $response = $this->chat_model->handleChatStatus($status);

        if ( ! empty($response)) {
            $this->pusher->trigger(
                'user_changed_chat_status',
                'status-changed-event',
                [
                    'user_id' => $response['user_id'],
                    'status'  => $response['status'],
                ]
            );
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }


    /**
     * Mentions
     *
     * @return void
     */
    public function pusherMentionEvent()
    {
        $data = $this->input->post();

        if ( ! $data || ! $this->input->is_ajax_request()) {
            show_404();
        }
        if ($data) {
            $this->chat_model->handleMentionEvent($data, $this->pusher);
        }
    }


    /**
     * Load modal view for staff users for message forwarding
     *
     * @return void modal
     */
    public function getForwardUsersData()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $data['title'] = _l('chat_forward_message_title');
        $data['staff'] = $this->chat_model->getStaffForForward();
        $data['groups'] = $this->chat_model->getChatGroups();

        $this->load->view('prchat/includes/forward_to_modal', $data);
    }


    /**
     * Live Search staff.
     *
     * @return void
     */
    public function searchStaffForForward()
    {
        $search = $this->input->get('search');
        $staff = $this->chat_model->searchStaff($search);
        echo json_encode($staff);
    }


    /**
     * Loading more clients from database on click Load more button.
     *
     * @return void
     */
    public function appendMoreStaff()
    {
        $offset = $this->input->get('offset');
        echo json_encode($this->chat_model->appendMoreStaff($offset));
    }


    /**
     * Renders to file
     *
     * @return json
     */
    public function handleAudio()
    {
        $audioBase64Data = $this->input->post('audio');

        if ($audioBase64Data) {
            header('Content-Type: application/json');
            return $this->chat_model->handleAudioData($audioBase64Data);
        }
    }

    /**
     * Live ajax search for chat messages for staff to staff and staff to client.
     *
     * @return void
     */
    public function searchMessages()
    {
        if ( ! $this->input->is_ajax_request()) {
            redirect('admin/prchat/Prchat_Controller/chat_full_view', 'refresh');
        }

        $id = $this->input->post('id');
        $table = $this->input->post('table');

        $name = (strpos($id, 'client') !== false)
            ? get_contact_full_name(str_replace('client_', '', $id))
            : get_staff_full_name($id);

        $data = [
            'id'             => $id,
            'user_full_name' => $name,
            'messages'       => json_encode($this->chat_model->getMessagesHistoryBetween($id, $table)),
        ];

        $this->load->view('prchat/includes/search_messages_modal', $data);
    }


    /**
     * Deletes conversation history from staff, clients or groups with all uploads
     */
    public function purgeConversations()
    {
        if ( ! chatStaffCanDelete()) {
            access_denied();
        }

        $type = $this->input->post('type');

        if ($type) {
            header('Content-Type: application/json');
            echo json_encode($this->chat_model->purgeConversations($type));
        }
    }

}
