<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_106 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();   
        if (!$CI->db->field_exists('tax' ,db_prefix() . 'cart_detailt')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
              ADD COLUMN `tax` text NULL
              ');
        }

        if (!$CI->db->field_exists('discount_type_str' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `discount_type_str` text null
		      ');
		}
		  
		if (!$CI->db->field_exists('discount_percent' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `discount_percent` DECIMAL(15,2) null
		      ');
		}

		if (!$CI->db->field_exists('adjustment' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `adjustment` DECIMAL(15,2) null
		      ');
		}

		if (!$CI->db->field_exists('currency' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `currency` INT(11) null
		      ');
		}

		if (!$CI->db->field_exists('discount_total' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL;
		      ');
		}

		if (!$CI->db->field_exists('currency' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `currency` INT(11) null
		      ');
		}

		if (!$CI->db->field_exists('terms' ,db_prefix() . 'cart')) {
		    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
		      ADD COLUMN `terms` TEXT null
		      ');
		}
		if (!$CI->db->table_exists(db_prefix() . 'omni_cart_payment')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "omni_cart_payment` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`cart_id` int(11) not null,
				`payment_id` varchar(30) not null,
				`payment_name` varchar(100) null,
				`customer_pay` DECIMAL(15,2) not null default \"0.00\",
				`datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}
    }
}