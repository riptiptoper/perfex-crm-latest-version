<?php
/*
Module Name: Perfex CRM Chat
Description: Chat Module for Perfex CRM
Author: Aleksandar Stojanov
Author URI: https://idevalex.com
*/

defined('BASEPATH') or exit('No direct script access allowed');
define('CHAT_CURRENT_URI', isset($_SERVER['REQUEST_URI']) ? strtolower($_SERVER['REQUEST_URI']) : null);
define('VERSIONING', get_instance()->app_scripts->core_version());

if (staff_can('view', PR_CHAT_MODULE_NAME) && get_option('pusher_chat_enabled') == '1') {
  hooks()->add_action('before_staff_login', 'prchat_set_session_variable_before_login_for_notification');
  hooks()->add_action('app_admin_head', 'pr_chat_add_head_components');
  hooks()->add_action('app_admin_footer', 'pr_chat_init_checkView');
  hooks()->add_action('app_admin_footer', 'pr_chat_load_js');
  hooks()->add_action('app_admin_head', 'pr_chat_add_js_before_admin_render');
  hooks()->add_filter('migration_tables_to_replace_old_links', 'pr_chat_migration_tables_to_replace_old_links');
  hooks()->add_action('staff_member_deleted', 'pr_chat_staff_member_data_transfer');
  hooks()->add_action('admin_navbar_start', 'chat_insert_chat_statuses');
}

// Check if clients view is enabled in Setup->Settings->Chat Settings
if (isClientsEnabled() && get_option('pusher_chat_enabled') == '1') {
  hooks()->add_action('before_client_login', 'prchat_set_session_variable_before_login_for_notification_client');
  hooks()->add_action('app_customers_head', 'handle_clients_css_styles');
  hooks()->add_action('app_customers_footer', 'pr_chat_init_checkViewClients');
}

/**
 * Function that handles css files for customers view.
 *
 * @return void
 */
function handle_clients_css_styles()
{
  echo '<link href="' . base_url('modules/prchat/assets/clients/styles.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" >';
  echo '<link href="' . base_url('modules/prchat/assets/css/lity.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
}

/*
 * Check if can have permissions then apply new tab in settings
 */
if (staff_can('view', 'settings')) {
  hooks()->add_action('admin_init', 'prchat_add_settings_tab');
}

/**
 * [prchat_add_settings_tab net menu item in setup->settings].
 *
 * @return void
 */
function prchat_add_settings_tab()
{
  $CI = &get_instance();
  $CI->app_tabs->add_settings_tab('prchat-settings', [
    'name'     => '' . _l('chat_settings_name') . '',
    'view'     => 'prchat/prchat_settings',
    'position' => 36,
  ]);
}

/**
 * [Set session variable before login > this is for html5 live desktop notifications].
 *
 * @return void
 */
function prchat_set_session_variable_before_login_for_notification()
{
  get_instance()->session->set_userdata('prchat_user_before_login', true);
}

/**
 * [Set session variable before login > this is for html5 live desktop notifications].
 *
 * @return void
 */
function prchat_set_session_variable_before_login_for_notification_client()
{
  get_instance()->session->set_userdata('prchat_client_before_login', true);
}

/**
 * [pr_chat_load_js inject javascript files].
 *
 * @return void
 */
function pr_chat_load_js()
{
  if ( ! strpos($_SERVER['REQUEST_URI'], 'chat_full_view') !== false) {
    echo '<script src="' . module_dir_url('prchat', 'assets/js/pr-chat.js' . '?v=' . VERSIONING . '') . '"></script>';
  }
  /**
   * Mentions js
   */
  echo '<script src="' . base_url('modules/prchat/assets/js/mentions/underscore.js' . '?v=' . VERSIONING . '') . '"></script>';
  echo '<script src="' . base_url('modules/prchat/assets/js/mentions/jquery-elastic.js' . '?v=' . VERSIONING . '') . '"></script>';
  echo '<script src="' . base_url('modules/prchat/assets/js/mentions/mentions.js' . '?v=' . VERSIONING . '') . '"></script>';

  if (strpos(CHAT_CURRENT_URI, '/prchat/prchat_controller/chat_full_view')) {
    /**
     * Audio record js
     */
    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
      echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/WebAudioRecorder.min.js' . '?v=' . VERSIONING . '') . '"></script>';
      echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/WebAudioRecorderOgg.min.js' . '?v=' . VERSIONING . '') . '"></script>';
      echo '<script src="' . module_dir_url('prchat', 'assets/js/audio/sound_app.js' . '?v=' . VERSIONING . '') . '"></script>';
    }
  }
}

/**
 * Function that will inject the chat messages tables when user changing domain and need to replace old links.
 *
 * @param array $tables
 *
 * @return array
 */
function pr_chat_migration_tables_to_replace_old_links($tables)
{
  $tables[] = [
    'table' => db_prefix() . 'chatmessages',
    'field' => 'message',
  ];

  return $tables;
}

/**
 * Injects chat CSS.
 *
 * @return null
 */
function pr_chat_add_head_components()
{
  if ( ! strpos($_SERVER['REQUEST_URI'], 'chat_full_view') !== false) {
    echo '<link href="' . base_url('modules/prchat/assets/css/chat_styles.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" >';
  } else {
    chat_check_theme_options();
  }
  // Mutual files for both chat views
  echo '<link href="' . base_url('modules/prchat/assets/css/lity.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
  echo '<link href="' . base_url('modules/prchat/assets/css/chat_statuses.css') . '" rel="stylesheet" type="text/css"/>';
  echo '<link href="' . base_url('modules/prchat/assets/css/mentions.css') . '" rel="stylesheet" type="text/css"/>';
}

/**
 * Inject chat JS plugins.
 */
function pr_chat_add_js_before_admin_render()
{
  if ( ! strpos($_SERVER['REQUEST_URI'], 'chat_full_view') !== false) {
    echo '<script src="' . base_url('modules/prchat/assets/js/jscolor.js' . '?v=' . VERSIONING . '') . '"></script>';
  }
  echo '<script src="' . base_url('modules/prchat/assets/js/emoparser.js' . '?v=' . VERSIONING . '') . '"></script>';
}

/**
 * Theme options.
 *
 * @return load css file
 */
function chat_check_theme_options()
{
  if (get_chat_theme_option() == 'light' || get_chat_theme_option() == null) {
    echo '<link href="' . base_url('modules/prchat/assets/css/chat_full_view.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
  } else {
    echo '<link href="' . base_url('modules/prchat/assets/css/chat_full_dark_view.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
  }
  echo '<link href="' . base_url('modules/prchat/assets/css/lity.css' . '?v=' . VERSIONING . '') . '"  rel="stylesheet" type="text/css" />';
}

/**
 * Loads the chat view.
 *
 * @return null
 */
function pr_chat_init_checkView()
{
  $CI = &get_instance();
  $CI->load->model('prchat/prchat_model', 'chat_model');
  $unreadMessages = $CI->chat_model->getUnread();

  echo '<script src="' . base_url('modules/prchat/assets/js/lity.min.js' . '?v=' . VERSIONING . '') . '"></script>';
  echo $CI->load->view('prchat/initViewCheck', ['unreadMessages' => $unreadMessages], true);
}

/**
 * Loads the chat view.
 *
 * @return null
 */
function pr_chat_init_checkViewClients()
{
  $CI = &get_instance();
  echo $CI->load->view('prchat/initViewCheckClients');
}

/*
    * Function that will convert links to iamges if meets the regex
 */
function pr_chat_convertLinkImageToString($string)
{
  $regexImg = '~(http.*\.)(jpe?g|png|gif|[tg]iff?|svg)~i';

  if (preg_match_all($regexImg, $string)) {
    $string = preg_replace($regexImg, '<a href="' . htmlspecialchars('$0') . '" data-lity><img class="prchat_convertedImage" src="' . htmlspecialchars('$0') . '"/></a>', $string);
  }

  return $string;
}

/**
 * Get chat color by user id.
 *
 * @param mixed $id
 * @param mixed $name
 *
 * @return mixed
 */
function pr_get_chat_color($id, $name = '')
{
  $CI = &get_instance();

  if ($CI->db->field_exists('value', db_prefix() . 'chatsettings')) {
    return pr_get_chat_option($id, $name);
  } else {
    $CI->db->select('chat_color');
    $CI->db->where('user_id', $id);
  }
  $result = $CI->db->get(db_prefix() . 'chatsettings')->row();

  if ( ! $result) {
    return '';
  }

  return $result->chat_color;
}

/**
 * Get chat get chat color on subscribe.
 *
 * @param mixed $id
 * @param mixed $name
 *
 * @return mixed
 */
function pr_get_chat_option($id, $name)
{
  $CI = &get_instance();
  $CI->db->select('value');
  $CI->db->where('name', $name);
  $CI->db->where('user_id', $id);

  $result = $CI->db->get(db_prefix() . 'chatsettings')->row();

  if ( ! $result) {
    return '';
  }

  return $result->value;
}

/**
 * Function that will check check if current message contains image.
 *
 * @param string $string
 *
 * @return string
 */
function prchat_checkMessageIfFileExists($message)
{
  $regexImg = '/^[^?]*\.(unknown|gif|jpg|jpeg|tiff|png|swf|rar|zip|mp3|ogg|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css|PNG|JPG|JPEG)/';
  if (preg_match_all($regexImg, $message)) {
    return true;
  } else {
    return false;
  }
}

/**
 * Check if message has any images or files links containing.
 *
 * @param string $image
 *
 * @return string
 */
function getImageFullName($file)
{
  $url_arr = explode('/', $file);
  $ct = count($url_arr);
  $name = $url_arr[$ct - 1];
  $name_div = explode('.', $name);
  $ct_dot = count($name_div);

  return $name;
}

/**
 * Get staff current role.
 *
 * @param [type] $role_id
 *
 * @return string
 */
function get_staff_userrole($role_id)
{
  $CI = &get_instance();
  $CI->db->select('name');
  $CI->db->where('roleid', $role_id);

  $result = $CI->db->get(db_prefix() . 'roles')->row_array();
  if ($result !== null) {
    return $result['name'];
  }
}

/**
 * Theme options.
 *
 * @return string
 */
function get_chat_theme_option()
{
  get_instance()->db->where('user_id', get_staff_user_id());
  get_instance()->db->where('name', 'current_theme');

  return get_instance()->db->get(db_prefix() . 'chatsettings')->row('value');
}

/**
 * Function that will check chat URL images and will convert to link.
 *
 * @param string $string
 *
 * @return string
 */
function make_url_clickable_cb($matches)
{
  $ret = '';
  $url = $matches[2];
  if (empty($url)) {
    return $matches[0];
  }
  // removed trailing [.,;:] from URL
  if (in_array(substr($url, -1), [
      '.',
      ',',
      ';',
      ':',
    ]) === true) {
    $ret = substr($url, -1);
    $url = substr($url, 0, strlen($url) - 1);
  }

  $hrefDest = str_replace('https://', '//', $url);
  $hrefDest = str_replace('http://', '//', $url);

  return $matches[1] . "<a href=\"$hrefDest\" rel=\"nofollow\" data-lity target=\"_blank\">$url</a>" . $ret;
}

/**
 * Callback for clickable.
 */
function make_web_ftp_clickable_cb($matches)
{
  $ret = '';
  $dest = $matches[2];
  $dest = 'http://' . $dest;
  if (empty($dest)) {
    return $matches[0];
  }
  // removed trailing [,;:] from URL
  if (in_array(substr($dest, -1), [
      '.',
      ',',
      ';',
      ':',
    ]) === true) {
    $ret = substr($dest, -1);
    $dest = substr($dest, 0, strlen($dest) - 1);
  }

  $hrefDest = str_replace('https://', '//', $dest);
  $hrefDest = str_replace('http://', '//', $dest);

  return $matches[1] . "<a href=\"$hrefDest\" rel=\"nofollow\" data-lity target=\"_blank\">$dest</a>" . $ret;
}

/**
 * Callback for clickable.
 */
function make_email_clickable_cb($matches)
{
  $email = $matches[2] . '@' . $matches[3];

  return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

/**
 * Check for links/emails/ftp in string to wrap in href.
 *
 * @param string $ret
 *
 * @return string formatted string with href in any found
 */
function clickable($ret)
{
  $ret = ' ' . $ret;
  // in testing, using arrays here was found to be faster
  $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'make_url_clickable_cb', $ret);
  $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', 'make_web_ftp_clickable_cb', $ret);
  $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'make_email_clickable_cb', $ret);
  // this one is not in an array because we need it to run last, for cleanup of accidental links within links
  $ret = preg_replace('#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', '$1$3</a>', $ret);
  $ret = trim($ret);

  return $ret;
}

function check_for_links_lity($ret)
{
  return clickable($ret);
}

/**
 * Function that handles member data upon member deletion.
 *
 * @param [array] $data
 *
 * @return boolean
 */
function pr_chat_staff_member_data_transfer($data)
{
  $deleted = $data['id'];
  $transfer_data_to = $data['transfer_data_to'];
  $CI = &get_instance();

  $CI->db->trans_start();

  $CI->db->where('member_id', $deleted);
  $CI->db->delete(TABLE_CHATGROUPMEMBERS);

  $CI->db->where('sender_id', $deleted);
  $CI->db->delete(TABLE_CHATGROUPMESSAGES);

  $CI->db->where('sender_id', $deleted);
  $CI->db->or_where('reciever_id', $deleted);
  $CI->db->delete(db_prefix() . 'chatmessages');

  $CI->db->where('user_id', $deleted);
  $CI->db->delete(db_prefix() . 'chatsettings');

  $CI->db->where('created_by_id', $deleted);
  $CI->db->update(TABLE_CHATGROUPS, ['created_by_id' => $transfer_data_to]);

  $group_files = $CI->db->select('file_name')->where('sender_id', $deleted)->get(db_prefix() . 'chatgroupsharedfiles')->result_array();
  $files = $CI->db->select('file_name')->where('sender_id', $deleted)->get(db_prefix() . 'chatsharedfiles')->result_array();

  foreach ($group_files as $group_file) {
    if (is_dir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER)) {
      unlink(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER . '/' . $group_file['file_name']);
    }
  }

  foreach ($files as $file) {
    if (is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
      unlink(PR_CHAT_MODULE_UPLOAD_FOLDER . '/' . $file['file_name']);
    }
  }

  $CI->db->where('sender_id', $deleted);
  $CI->db->delete(TABLE_CHATGROUPSHAREDFILES);

  $CI->db->where('sender_id', $deleted);
  $CI->db->delete(db_prefix() . 'chatsharedfiles');

  if ($CI->db->trans_complete()) {
    return true;
  }

  return false;
}

// Helpers for gradient colors
function validateChatColorBeforeApply($color, $model_check = '')
{
  $validColor = '';
  if (
    colorStartsWith($color, '#') && ! colorEndsWith($color, ';')
    || colorStartsWith($color, 'linear-gradient(') && colorEndsWith($color, ');')
  ) {
    $validColor = $color;
  } else {
    $validColor = '#546bf1';
  }
  if ($model_check == true && $validColor === '#546bf1') {
    return 'unknownColor';
  }

  return $validColor;
}

// Helpers for gradient colors
function colorStartsWith($haystack, $needle)
{
  $length = strlen($needle);

  return substr($haystack, 0, $length) === $needle;
}

function colorEndsWith($haystack, $needle)
{
  $length = strlen($needle);
  if ($length == 0) {
    return true;
  }

  return substr($haystack, -$length) === $needle;
}

/**
 * Returns the customer for a specific (mixed) contact_id.
 *
 * @return row_array
 */
function getOwnClient($id)
{
  $CI = &get_instance();

  $CI->db->select('clients.userid as client_id, ' . db_prefix() . 'contacts.id as contact_id, firstname, lastname, company');
  $CI->db->from(db_prefix() . 'clients as clients');
  $CI->db->where(db_prefix() . 'contacts.id', $id);
  $CI->db->where(db_prefix() . 'contacts.active', 1);
  $CI->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.userid = clients.userid');

  $result = $CI->db->get()->row_array();

  return $result;
}

/**
 * Fetches from database all staff assigned customers
 * If admin fetches all customers.
 *
 * @param int $limit
 * @param int $offset
 *
 * @return json
 */
function get_staff_customers($limit = 50, $offset = 0, $arrayData = false)
{
  $CI = &get_instance();

  $staffCanViewAllClients = staff_can('view', 'customers');

  $CI->db->select('firstname, lastname, ' . db_prefix() . 'contacts.id as contact_id, ' . get_sql_select_client_company());
  $CI->db->where(db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'contacts.active = 1');
  $CI->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid=' . db_prefix() . 'contacts.userid', 'left');
  $CI->db->select(db_prefix() . 'clients.userid as client_id, title');

  /**
   * Remove the comment from this if you want all staff to see all clients also navigate in function get_customer_admins() and follow the
   * instructions for all clients to see all staff
   */
  if ( ! $staffCanViewAllClients) {
    $CI->db->where('(' . db_prefix() . 'clients.userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id="' . get_staff_user_id() . '"))');
  }

  $CI->db->limit($limit, $offset);


  $result = $CI->db->get(db_prefix() . 'contacts')->result_array();


  foreach ($result as $key => $contact) {
    $result[$key]['profile_image_url'] = contact_profile_image_url($contact['contact_id']);
  }

  if ($arrayData) {
    return $result;
  }

  if ($CI->db->affected_rows() !== 0) {
    echo json_encode(['customers' => $result]);
  } else {
    echo json_encode(['customers' => []]);
  }
}

/**
 * Fetches all customer assigned admins.
 *
 * @return json
 */
function get_customer_admins()
{
  $CI = &get_instance();

  $select = 'SELECT firstname, lastname, staffid, email, profile_image, admin, role FROM ' . db_prefix() . 'staff';


  /**
   * Lines no.1 and 2
   */
  $where = 'WHERE (staffid IN (SELECT staff_id from ' . db_prefix() . 'customer_admins WHERE customer_id =' . get_client_user_id() . ')';
  $customer_admins = $CI->db->query('' . $select . ' ' . $where . '  OR admin=1) AND active=1')->result_array();

  /**
   * Line no.3
   * If you want customers to see all staff comment line no.1 and line no.2 above and comment out this line no.3
   * Then you need to navigat to function above get_staff_customers()
   */
  // $customer_admins = $CI->db->query('' . $select . ' WHERE active=1')->result_array();


  foreach ($customer_admins as $key => &$admin) {
    if (get_option('chat_show_only_users_with_chat_permissions') == 1) {
      if ( ! staff_can('view', PR_CHAT_MODULE_NAME, $admin['staffid'])) {
        unset($customer_admins[$key]);
        continue;
      }
    }

    $admin['role'] = get_chat_staff_userrole($admin['role']);

    if ( ! $admin['role']) {
      if ($admin['admin']) {
        $admin['role'] = ' ' . _l('chat_role_administrator');
      } else {
        $admin['role'] = ' ' . _l('chat_role_staff');
      }
    } else if ($admin['role'] && $admin['admin']) {
      $admin['role'] = ' ' . _l('chat_role_administrator') . ' / ' . $admin['role'];
    } else {
      $admin['role'] = " " . $admin['role'];
    }
  }

  echo json_encode($customer_admins);
}

/**
 * Check if staff can delete messages
 *
 * @return bool
 */
function chatStaffCanDelete()
{
  if (is_admin()) {
    return true;
  }
  return get_option('chat_staff_can_delete_messages') == 1;
}


/**
 * Get staff current role.
 *
 * @param [type] $role_id
 *
 * @return string
 */
function get_chat_staff_userrole($role_id)
{
  $CI = &get_instance();
  $CI->db->select('name');
  $CI->db->where('roleid', $role_id);

  $result = $CI->db->get(db_prefix() . 'roles')->row_array();
  if ($result !== null) {
    return $result['name'];
  }
}


/**
 * Get contact user id from contacts table
 * Used for when creating support ticket from messages
 *
 * @return string
 */
function get_contact_customer_user_id($contact_id)
{
  $CI = &get_instance();
  $CI->db->select('userid');
  $CI->db->where('id', $contact_id);
  $result = $CI->db->get(db_prefix() . 'contacts')->row_array();
  if ($result !== null) {
    return $result['userid'];
  }
}


/**
 * Get last ticket_id inserted
 *
 * @return string
 */
function chat_get_tickets_last_inserted_row()
{
  return get_instance()->db->select('ticketid')->order_by('ticketid', "desc")->limit(1)->get(db_prefix() . 'tickets')->row()->ticketid;
}

/**
 * Receives user active chat status
 *
 * @return string
 */
function get_user_chat_status()
{
  $CI = &get_instance();
  $CI->db->where('user_id', get_staff_user_id());
  $CI->db->where('name', 'chat_status');
  $result = $CI->db->get(db_prefix() . 'chatsettings')->row_array();
  if ($result !== null) {
    return $result['value'];
  }
}

/**
 * Check is chat module enabled.
 *
 * @return boolean
 */
function isClientsEnabled()
{
  return get_option('chat_client_enabled');
}

/**
 * Returns groups name with who capitals eg My_Group and underscore.
 *
 * @param $name
 *
 * @return string
 */
function slugifyGroupName($name)
{
  return rtrim(stripslashes(ucwords(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', ucwords($name))))), '-');
}

/**
 * Template components js loader
 *
 * @param       $name
 * @param array $params is user over all components do not remove in any case
 *
 * @return mixed
 */
function loadChatComponent($name, $params = [])
{    // Used in chat_full_view as ['prop' => 'class or something else']
  // Then reuse this in the $name.php $params['prop']
  require('modules/prchat/assets/module_includes/Components/' . $name . '.php');
}


/**
 * After header is rendered add statuses design functionality
 *
 * @return string
 */
if (staff_can('view', PR_CHAT_MODULE_NAME)) {
  function chat_insert_chat_statuses()
  {
    $CI = &get_instance();

    $currentStatus = _l('chat_header_status');
    $onlineStatus = _l('chat_status_online');
    $awayStatus = _l('chat_status_away');
    $busyStatus = _l('chat_status_busy');
    $offlineStatus = _l('chat_status_offline');


    if ($CI->db->table_exists(db_prefix() . 'chatsettings')) {
      $CI->db->where('user_id', get_staff_user_id());
      $CI->db->where('name', 'chat_status');
      $status = $CI->db->get(db_prefix() . 'chatsettings')->row_array();
    }

    $status = $status != '' ? $status['value'] : 'online';

    echo <<<EOD
        <li id='prchat-header-wrapper'>
          <a href='#' class='dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
            <svg class='$status' id='chat_status_top_icon'
                 style='margin:0;height: 25px;width: 25px;'
                 data-toggle='tooltip' title='$currentStatus' data-placement='bottom'
                 viewBox='0 0 24 24'>
              <path
                d='M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M16,14H8V13C8,11.67 10.67,11 12,11C13.33,11 16,11.67 16,13V14M12,6A2,2 0 0,1 14,8A2,2 0 0,1 12,10A2,2 0 0,1 10,8A2,2 0 0,1 12,6Z'/>
            </svg>
          </a>        
          <ul id='top_status-options' class='dropdown-menu animated fadeIn'>        
            <li id='status-online' class='active'><span class='status-circle'></span>
              <a href='#'>$onlineStatus</a>
            </li>
            <li id='status-away'><span class='status-circle'></span>
              <a href='#'>$awayStatus</a>
            </li>
            <li id='status-busy'><span class='status-circle'></span>
              <a href='#'>$busyStatus</a>
            </li>
            <li id='status-offline'><span class='status-circle'></span>
              <a href='#'>$offlineStatus</a>
            </li>
          </ul>
  EOD;
  }
}

?>
