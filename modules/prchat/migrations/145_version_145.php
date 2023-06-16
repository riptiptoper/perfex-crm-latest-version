<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_145 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        /**
         * Alter tables change time_sent to NULL
         */
        $CI->db->query("ALTER TABLE `tblchatmessages` CHANGE `time_sent` `time_sent` DATETIME NULL DEFAULT NULL;");
        $CI->db->query("ALTER TABLE `tblchatclientmessages` CHANGE `time_sent` `time_sent` DATETIME NULL DEFAULT NULL;");
        $CI->db->query("ALTER TABLE `tblchatgroupmessages` CHANGE `time_sent` `time_sent` DATETIME NULL DEFAULT NULL;");

        /**
         * Alter table add viewed_at new column
         */
        $CI->db->query("ALTER TABLE `chatclientmessages` ADD `viewed_at` DATETIME NULL DEFAULT NULL;");
        $CI->db->query("ALTER TABLE `chatmessages` ADD `viewed_at` DATETIME NULL DEFAULT NULL;");

        /**
         * Delete column not needed anymore
         */
        $CI->db->query("ALTER TABLE `" . db_prefix() . "chatmessages` DROP COLUMN `is_deleted`;");
        $CI->db->query("ALTER TABLE `" . db_prefix() . "chatgroupmessages` DROP COLUMN `is_deleted`;");

        /**
         * Delete empty messages because delete functionality is changed
         * Deleted messages will no longer be shown because of unecessary data loading in frontend
         */
        $CI->db->where('message', '');
        $CI->db->delete(db_prefix() . 'chatmessages');

        $CI->db->where('message', '');
        $CI->db->delete(db_prefix() . 'chatgroupmessages');
    }

}
