<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_113 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		add_option('omni_pos_shipping_fee', 0);
		add_option('omni_portal_shipping_fee', 0);
		add_option('omni_manual_shipping_fee', 0);
		if ($CI->db->table_exists(db_prefix() . 'goods_delivery')) {
		  if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'goods_delivery')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery`
		    ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
		    ');
		  }
		}
		if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'invoices')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
				ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
				');
		}
		add_option('omni_order_statuses_are_allowed_to_sync', '');
	}
}
