<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_101 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();   
        if (!$CI->db->table_exists(db_prefix() . 'omni_setting_woo_store')) {
          $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_setting_woo_store` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `store` int(11) NOT NULL,
            `sync_omni_sales_products` int(11) NOT NULL default 0,
            `time1` int(11) NOT NULL default 50,
            `sync_omni_sales_inventorys` int(11) NOT NULL default 0,
            `time2` int(11) NOT NULL default 50,
            `price_crm_woo` int(11) NOT NULL default 0,
            `time3` int(11) NOT NULL default 50,
            `sync_omni_sales_description` int(11) NOT NULL default 0,
            `time4` int(11) NOT NULL default 50,
            `sync_omni_sales_images` int(11) NOT NULL default 0,
            `time5` int(11) NOT NULL default 50,
            `sync_omni_sales_orders` int(11) NOT NULL default 0,
            `time6` int(11) NOT NULL default 50,
            `product_info_enable_disable` int(11) NOT NULL default 0,
            `time7` int(11) NOT NULL default 50,
            `product_info_image_enable_disable` int(11) NOT NULL default 0,
            `time8` int(11) NOT NULL default 50,
            `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
        }
        if (!$CI->db->field_exists('staff_note' ,db_prefix() . 'cart')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
              ADD COLUMN `staff_note` text null,                  
              ADD COLUMN `payment_note` text null                  
          ');
        }
        if (!$CI->db->field_exists('allowed_payment_modes' ,db_prefix() . 'cart')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
              ADD COLUMN `allowed_payment_modes` varchar(200) null                 
          ');
        }

        create_email_template('Purchase receipt', 'Hi {staff_name}! <br /><br />Thank you for shopping in our store.<br />
          We send a receipt of your purchase below.<br />{<span 12pt="">notification_content</span>}. <br /><br />Kind Regards.<br/>Very pleased to serve you!', 'purchase_receipt', 'Purchase receipt (Sent to customer)', 'purchase-receipt');

        if ($CI->db->field_exists('quantity' ,db_prefix() . 'cart_detailt')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
              MODIFY `quantity` float not null                 
          ');
        }
        if (!$CI->db->field_exists('warehouse_id' ,db_prefix() . 'cart')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
              ADD COLUMN `warehouse_id` INT null                 
          ');
        }

     }

}

