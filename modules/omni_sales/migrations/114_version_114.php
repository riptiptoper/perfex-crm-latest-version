<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_114 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		
		if (!$CI->db->field_exists('hash' ,db_prefix() . 'cart')) {
		  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		  ADD COLUMN `hash` VARCHAR(32) NULL;
		  ');
		}
	}
}
