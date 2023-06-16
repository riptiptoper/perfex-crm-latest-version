<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Perfex CRM Powerful Chat
Description: Chat Module for Perfex CRM
Author: Aleksandar Stojanov
Author URI: https://idevalex.com
Requires at least: 2.3.2
*/

class Prchat_ClientsController extends ClientsController
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
     * @var [object]
     */
    protected $pusher;

    /**
     * Controler __construct function to initialize options.
     */
    public function __construct()
    {
        parent::__construct();

        if (!get_option('pusher_chat_enabled') == '1') {
            redirect('admin');
        }

        $this->load->model('prchat_model', 'chat_model');

        $this->pusher_options['app_key'] = get_option('pusher_app_key');
        $this->pusher_options['app_secret'] = get_option('pusher_app_secret');
        $this->pusher_options['app_id'] = get_option('pusher_app_id');

        if (!isset($this->pusher_options['cluster']) && get_option('pusher_cluster') != '') {
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
     * Pusher authentication.
     *
     * @return void
     */
    public function pusherCustomersAuth()
    {
        if ($this->input->get()) {
            $user_id = 'client_' . get_contact_user_id();
            $name = get_contact_full_name(get_contact_user_id());
            $channel_name = 'presence-clients';
            $socket_id = $this->input->get('socket_id');

            if (!$channel_name) {
                exit('channel_name must be supplied');
            }

            if (!$socket_id) {
                exit('socket_id must be supplied');
            }

            if (
                !empty($this->pusher_options['app_key'])
                && !empty($this->pusher_options['app_secret'])
                && !empty($this->pusher_options['app_id'])
            ) {
                $justLoggedIn = false;

                if ($this->session->has_userdata('prchat_client_before_login')) {
                    $this->session->unset_userdata('prchat_client_before_login');

                    $justLoggedIn = true;
                }

                $presence_data = [
                    'name'         => $name,
                    'justLoggedIn' => $justLoggedIn,
                    'contact_id'   => get_contact_user_id(),
                    'client_id'    => get_client_user_id(),
                    'company'      => $this->chat_model->getClientCompanyName(get_client_user_id()),
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
     * Main function that handles, sending messages, notify events, typing events and inserts message data in database.
     *
     * @throws \Pusher\PusherException
     */
    public function initClientChat()
    {
        if ($this->input->post()) {
            $from = $this->input->post('from');
            $receiver = $this->input->post('to');
            $client_id = $this->input->post('client_id');
            $contact_full_name = $this->input->post('contact_full_name');
            $contact_company_name = $this->input->post('company');

            if ($this->input->post('typing') == 'false') {
                $message = $this->input->post('client_message');

                $message_data = [
                    'sender_id'   => $from,
                    'reciever_id' => $receiver,
                    'message'     => htmlentities($message),
                    'viewed'      => 0,
                    'time_sent'   => date("Y-m-d H:i:s"),
                ];

                $lastInsertId = $this->chat_model->recordClientMessage($message_data);

                $this->pusher->trigger(
                    'presence-clients',
                    'send-event',
                    [
                        'message'           => pr_chat_convertLinkImageToString($message),
                        'from'              => $from,
                        'to'                => $receiver,
                        'client_id'         => $client_id,
                        'company'           => $contact_company_name,
                        'contact_full_name' => $contact_full_name,
                        'client_image_path' => contact_profile_image_url(str_replace('client_', '', $from)),
                        'from_name'         => get_staff_full_name(str_replace('staff_', '', $from)),
                        'last_insert_id'    => $lastInsertId,
                    ]
                );

                $this->pusher->trigger(
                    'presence-clients',
                    'notify-event',
                    [
                        'from'      => $from,
                        'to'        => $receiver,
                        'from_name' => get_staff_full_name($from),
                    ]
                );
            } else if ($this->input->post('typing') == 'true') {
                $this->pusher->trigger(
                    'presence-clients',
                    'typing-event',
                    [
                        'message' => $this->input->post('typing'),
                        'from'    => $from,
                        'to'      => $receiver,
                    ]
                );
            } else {
                $this->pusher->trigger(
                    'presence-clients',
                    'typing-event',
                    [
                        'message' => 'null',
                        'from'    => $from,
                        'to'      => $receiver,
                    ]
                );
            }
        }
    }

    /**
     * Get logged in user messages sent to other user.
     */
    public function getMutualMessages()
    {
        $limit = $this->input->get('limit');
        $to = $this->input->get('sender_id');
        $from = $this->input->get('reciever_id');

        ($limit)
            ? $limit
            : $limit = 10;

        $offset = 0;

        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }
        $response = $this->chat_model->getMutualMessages($from, $to, $limit, $offset);

        if ($response) {
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('Content-Type: application/json');
            $message = _l('chat_no_more_messages_in_database');
            echo json_encode($message);
        }
    }

    /**
     * Get unread messages, used when somebody sent a message while the user is offline.
     */
    public function getClientUnreadMessages()
    {
        $result = $this->chat_model->getClientUnreadMessages();
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }

    /**
     * Get unread messages, used when somebody sent a message while the user is offline.
     */
    public function getStaffUnreadMessages()
    {
        $result = $this->chat_model->getStaffUnreadMessages($this->pusher);

        if ($result) {
            header('Content-Type', 'application/json');
            echo json_encode($result);
        } else {
            echo json_encode(['null' => true]);
        }

        return false;
    }

    /**
     *  Uploads chat files.
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
            echo json_encode(['upload_data' => $this->upload->data()]);
        } else {
            echo json_encode(['error' => $this->upload->display_errors()]);
        }
    }


    /**
     * Update unread messages.
     */
    public function updateClientUnreadMessages()
    {
        $id = $this->input->post('id');
        $client = $this->input->post('client');
        $pusher = $this->pusher;
        echo json_encode($this->chat_model->updateClientUnreadMessages($id, $pusher, ($client) ? $client : null));
    }


    /**
     * Loading more clients from database on click Load more button.
     */
    public function loadMoreClients()
    {
        $limit = 50;
        $offset = 0;

        if ($this->input->get('offset')) {
            $offset = $this->input->get('offset');
        }

        $staffCanViewAllClients = staff_can('view', 'customers');

        $this->db->select('firstname, lastname, ' . db_prefix() . 'contacts.id as contact_id, ' . get_sql_select_client_company());
        $this->db->where(db_prefix() . 'clients.active', '1'); // get only active clients
        $this->db->join('clients', db_prefix() . 'clients.userid=' . db_prefix() . 'contacts.userid', 'left');
        $this->db->select(db_prefix() . 'clients.userid as client_id');

        if (!$staffCanViewAllClients) {
            $this->db->where('(' . db_prefix() . 'clients.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . '))');
        }

        $this->db->limit($limit, $offset);
        $result = $this->db->get('contacts')->result_array();

        if ($this->db->affected_rows() !== 0) {
            echo json_encode(['customers' => $result]);
        } else {
            echo json_encode(['customers' => []]);
        }
    }

    /**
     * Live Search clients.
     *
     * @return void
     */
    public function searchClients()
    {
        $search = $this->input->post('search');
        $query = $this->chat_model->searchClients($search);
        echo json_encode($query);
    }


    /**
     * @throws \Pusher\PusherException
     */
    public function trigger_ticket_event()
    {
        $trigger = $this->pusher->trigger(
            'presence-clients',
            'chat-ticket-event',
            [
                'ticket_id' => $this->input->post('ticket_id'),
                'client_id' => $this->input->post('client_id'),
            ]
        );

        header('Content-Type: application/json');
        if ($trigger) {
            echo json_encode(
                [
                    'result'    => 'success',
                    'ticket_id' => $this->input->post('ticket_id')
                ]
            );
        } else {
            echo json_encode(['result' => 'error']);
        }
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

}
