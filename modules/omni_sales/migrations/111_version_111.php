<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_111 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		
		if (!$CI->db->field_exists('woo_customer_id' ,db_prefix() . 'clients')) {
		  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients`
		    ADD COLUMN `woo_customer_id` int NULL DEFAULT 0,
		    ADD COLUMN `woo_channel_id` int NULL DEFAULT 0
		    ');
		}
	}
}
