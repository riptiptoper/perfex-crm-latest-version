<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_112 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		add_option('omni_3des_key', '3des1213141516ahiocrth');	
		add_option('omni_allow_showing_shipment_in_public_link', 1);
		if (!$CI->db->field_exists('discount_voucher' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `discount_voucher` varchar(150) NULL
				');
		}
		add_option('omi_return_order_prefix', 'RE');
		add_option('omni_return_request_within_x_day', 30);
		add_option('omni_fee_for_return_order', 0);
		add_option('omni_refund_loyaty_point', 1);
		add_option('omni_return_policies_information', '');
		if (!$CI->db->field_exists('original_order_id' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `original_order_id` int(11) NULL
				');
		}
		if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `return_reason` longtext NULL
				');
		}

		if (!$CI->db->field_exists('approve_status' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `approve_status` int(11) NOT NULL DEFAULT 0;
				');
		}
		if (!$CI->db->field_exists('process_invoice' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `process_invoice` varchar(5) NOT NULL DEFAULT "off";
				');
		}
		if (!$CI->db->field_exists('stock_import_number' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `stock_import_number` int(11) NOT NULL DEFAULT 0
				');
		}
		if (!$CI->db->field_exists('fee_for_return_order' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `fee_for_return_order` DECIMAL(15,2) NULL;
				');
		}
		if (!$CI->db->table_exists(db_prefix() . 'omni_refunds')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "omni_refunds` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`order_id` INT(11) NULL,
				`staff_id` INT(11) NULL,
				`refunded_on` date NULL,
				`payment_mode` varchar(40) NULL,
				`note` text NULL,
				`amount` decimal(15,2) NULL,
				`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`rel_id` INT(11) NULL,
				`rel_type` VARCHAR(50) NOT NULL COMMENT'manual, sales_return_order, purchasing_return_order',
				`return_type` VARCHAR(50) NULL COMMENT'manual, partially, fully',
				`company_id` INT(11) NULL,
				`company_name` VARCHAR(500) NULL,
				`email` VARCHAR(100) NULL,
				`phonenumber` VARCHAR(20) NULL,
				`order_number` VARCHAR(500) NULL,
				`order_date` DATETIME NULL,
				`number_of_item` DECIMAL(15,2) NULL DEFAULT '0.00',
				`order_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				`order_return_number` VARCHAR(200) NULL,
				`order_return_name` VARCHAR(500) NULL,
				`fee_return_order` DECIMAL(15,2) NULL DEFAULT '0.00',
				`refund_loyaty_point` INT(11) NULL DEFAULT '0',
				`subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
				`additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
				`return_policies_information` TEXT NULL,
				`admin_note` TEXT NULL,
				`approval` INT(11) NULL DEFAULT 0,
				`datecreated` DATETIME NULL,
				`staff_id` INT(11) NULL,
				`receipt_delivery_id` INT(1) NULL DEFAULT 0,
				`return_reason` longtext NULL,
				`receipt_delivery_type` VARCHAR(100) NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->table_exists(db_prefix() . 'wh_order_return_details')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_return_details` (

			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`order_return_id` INT(11) NOT NULL,
			`rel_type_detail_id` INT(11) NULL,
			`commodity_code` INT(11) NULL,
			`commodity_name` TEXT NULL,
			`quantity` DECIMAL(15,2) NULL DEFAULT '0.00',
			`unit_id` INT(11) NULL,
			`unit_price` DECIMAL(15,2) NULL DEFAULT '0.00',
			`sub_total` DECIMAL(15,2) NULL DEFAULT '0.00',
			`tax_id`  TEXT NULL,
			`tax_rate`  TEXT NULL,
			`tax_name`  TEXT NULL,
			`total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
			`discount` DECIMAL(15,2) NULL DEFAULT '0.00',
			`discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
			`total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
			`reason_return` VARCHAR(200) NULL,

			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'wh_order_returns')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'wh_order_returns`
				ADD COLUMN `return_reason` longtext NULL
				');
		}
	}
}
