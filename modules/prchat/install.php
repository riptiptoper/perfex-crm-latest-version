<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * The file is responsible for handing the chat installation
 */
$CI = &get_instance();

if (!is_dir(PR_CHAT_MODULE_UPLOAD_FOLDER)) {
  mkdir(PR_CHAT_MODULE_UPLOAD_FOLDER, 0755);
  $fp = fopen(PR_CHAT_MODULE_UPLOAD_FOLDER . '/index.html', 'w');
  fclose($fp);
}

if (!is_dir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER)) {
  mkdir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER, 0755);
  $fp = fopen(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER . '/index.html', 'w');
  fclose($fp);
}

if (!is_dir(PR_CHAT_MODULE_AUDIO_UPLOAD_FOLDER)) {
  mkdir(PR_CHAT_MODULE_AUDIO_UPLOAD_FOLDER, 0755);
  $fp = fopen(PR_CHAT_MODULE_AUDIO_UPLOAD_FOLDER . '/index.html', 'w');
  fclose($fp);
}

add_option('pusher_chat_enabled', 1);
add_option('chat_staff_can_delete_messages', 1);
add_option('chat_desktop_messages_notifications', 1);
add_option('chat_members_can_create_groups', 1);
add_option('chat_client_enabled', 1);
add_option('chat_allow_staff_to_create_tickets', 1);
add_option('chat_show_only_users_with_chat_permissions', 0);

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATMESSAGES . "` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` INT(11) NOT NULL,
  `reciever_id` INT(11) NOT NULL,
  `message` longtext NOT NULL,
  `viewed` INT(11) DEFAULT '0',
  `time_sent` DATETIME NULL DEFAULT NULL,
  `viewed_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATSETTINGS . "` (
  id INT NOT NULL AUTO_INCREMENT, 
  user_id INT(11) NOT NULL,
  name VARCHAR(255) NOT NULL,
  value VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)  
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATSHAREDFILES . "` (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id INT(11),
  reciever_id INT(11),
  file_name VARCHAR(255)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATGROUPS . "` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `created_by_id` INT(11) NOT NULL,
  `group_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATGROUPMEMBERS . "` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) NOT NULL,
  `member_id` INT(11) NOT NULL,
  `group_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`group_id`) REFERENCES `" . TABLE_CHATGROUPS . "` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATGROUPMESSAGES . "` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) NOT NULL,
  `message` longtext NOT NULL,
  `sender_id` INT(11) NOT NULL,
  `time_sent` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATGROUPSHAREDFILES . "` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `group_id` INT(11) NOT NULL,
  `sender_id` INT(11) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

$CI->db->query("CREATE TABLE IF NOT EXISTS `" . TABLE_CHATCLIENTMESSAGES . "` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` VARCHAR(20) NOT NULL,
  `reciever_id` VARCHAR(20) NOT NULL,
  `message` longtext NOT NULL,
  `viewed` TINYINT(11) NOT NULL DEFAULT '0',
  `time_sent` DATETIME NULL DEFAULT NULL,
  `viewed_at` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
