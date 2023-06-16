<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_130 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();

		add_option('chat_desktop_messages_notifications', 0);

		if (!is_dir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER)) {
			mkdir(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER, 0755);
			$fp = fopen(PR_CHAT_MODULE_GROUPS_UPLOAD_FOLDER . '/index.html', 'w');
			fclose($fp);
		}

		$chatgroups = db_prefix() . 'chatgroups';
		$chatgroupmembers = db_prefix() . 'chatgroupmembers';
		$chatgroupmessages = db_prefix() . 'chatgroupmessages';
		$chatgroupsharedfiles = db_prefix() . 'chatgroupsharedfiles';

		$CI->db->query("CREATE TABLE IF NOT EXISTS `" . $chatgroups . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`created_by_id` int(11) NOT NULL,
			`group_name` varchar(255) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


		$CI->db->query("CREATE TABLE IF NOT EXISTS `" . $chatgroupmembers . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`group_id` int(11) NOT NULL,
			`member_id` int(11) NOT NULL,
			`group_name` varchar(255) NOT NULL,
			PRIMARY KEY (`id`),
			FOREIGN KEY (`group_id`) REFERENCES `" . $chatgroups . "` (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


		$CI->db->query("CREATE TABLE IF NOT EXISTS `" . $chatgroupmessages . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`group_id` int(11) NOT NULL,
			`message` longtext NOT NULL,
			`sender_id` int(11) NOT NULL,
			`time_sent` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");


		$CI->db->query("CREATE TABLE IF NOT EXISTS `" . $chatgroupsharedfiles . "` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`group_id` int(11) NOT NULL,
			`sender_id` int(11) NOT NULL,
			`file_name` varchar(255) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
	}
}
