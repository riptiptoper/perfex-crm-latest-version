<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_115 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		
		add_option('omni_display_shopping_cart', 1, 1);

		if (!$CI->db->field_exists('estimate_id' ,db_prefix() . 'cart')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "cart`
				ADD COLUMN `estimate_id` int(11) NULL
				;");
		}

		if (!$CI->db->field_exists('tax_id' ,db_prefix() . 'cart_detailt')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "cart_detailt`
				ADD COLUMN `tax_id` TEXT NULL,
				ADD COLUMN `tax_rate` TEXT NULL,
				ADD COLUMN `tax_name` TEXT NULL
				;");
		}

		if (!$CI->db->field_exists('unit_id' ,db_prefix() . 'cart_detailt')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "cart_detailt`
				ADD COLUMN `unit_id` int(11) NULL,
				ADD COLUMN `unit_name` VARCHAR(255) NULL
				;");
		}

		if (!$CI->db->field_exists('add_discount' ,db_prefix() . 'cart')) { 
			$CI->db->query('ALTER TABLE `' . db_prefix() . "cart`
				ADD COLUMN `add_discount` DECIMAL(15,2) DEFAULT '0.00'
				;");
		}
	}
}
