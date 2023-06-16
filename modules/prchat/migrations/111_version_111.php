<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_111 extends App_module_migration
{
    /**
     * { chatsettings table changed to global table and can be used for multiple items }
     * @param  Array $tables
 	 * @return null
     */
    public function up()
    {

    	$CI =& get_instance();
    	$table_name = db_prefix().'chatsettings';

    	$get_table = $CI->db->get($table_name);

    	if ($get_table) {
    		$results = $get_table->result_array();

    			if ($results) {
    				$del_old_table = $CI->db->query("DROP TABLE {$table_name}");

    				if ($del_old_table) {
    					$newtable = "CREATE TABLE IF NOT EXISTS {$table_name} (
    					id INT(11) NOT NULL AUTO_INCREMENT, 
    					user_id INT(11) NOT NULL,
    					name VARCHAR(255) NOT NULL,
    					value VARCHAR(255) NOT NULL,
    					PRIMARY KEY (id)  
    				);";

    				$query = $CI->db->query($newtable);
    				if ($query) {
    					foreach ($results as $user) {
    						$CI->db->query("INSERT INTO {$table_name} (id, user_id, name, value) VALUES ('{$user['id']}','{$user['user_id']}','chat_color','{$user['chat_color']}')");
    					}
    				}
    			}
    		}
    	}
    }
}

