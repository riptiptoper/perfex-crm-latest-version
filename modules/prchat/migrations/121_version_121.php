<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_121 extends App_module_migration
{
	public function up()
	{
		$chatsharedfiles = db_prefix().'chatsharedfiles';
		$chatmessages = db_prefix().'chatmessages';

		$CI =& get_instance();

		 $allFiles = "unknown|rar|zip|mp3|mp4|mov|flv|wmv|avi|doc|docx|pdf|xls|xlsx|zip|rar|txt|php|html|css|jpeg|jpg|png|swf|PNG|JPG|JPEG";

		if (!$CI->db->table_exists($chatsharedfiles)) {

			$sql = "CREATE TABLE ".$chatsharedfiles." (id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, sender_id int(11),reciever_id int(11), file_name VARCHAR(255))";

			if ($CI->db->query($sql)) {

				$data  = $CI->db->query("SELECT message, sender_id, reciever_id FROM ".$chatmessages." WHERE message REGEXP '^.*\.(".$allFiles.")$'");

				if ($data->num_rows() > 0 || $data !== 0) {

					$data = $data->result_array();

					foreach ($data as $file_name) {

						$file_name['message'] = pathinfo($file_name['message']);
						
						$CI->db->query("INSERT INTO ".$chatsharedfiles." (sender_id, reciever_id, file_name) VALUES ('".$file_name['sender_id']."', '".$file_name['reciever_id']."', '".$file_name['message']['basename']."')");

					}
				}
			}
		}
	}
}

