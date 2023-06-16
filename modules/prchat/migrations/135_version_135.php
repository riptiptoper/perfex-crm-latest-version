<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_135 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        add_option('chat_client_enabled', 1);

        $chatClientMessagesTable = db_prefix() . 'chatclientmessages';

        $CI->db->query("CREATE TABLE IF NOT EXISTS `" . $chatClientMessagesTable . "` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `sender_id` VARCHAR(20) NOT NULL,
            `reciever_id` VARCHAR(20) NOT NULL,
            `message` longtext NOT NULL,
            `viewed` TINYINT(11) NOT NULL DEFAULT '0',
            `time_sent` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
    }
}
