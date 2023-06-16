<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_110 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		
		if (!$CI->db->field_exists('shipping_tax_json' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `shipping_tax_json` varchar(150) NULL
		      ');
		}
		
	}
}
