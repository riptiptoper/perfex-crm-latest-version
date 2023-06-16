<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_103 extends App_module_migration
{
     public function up()
     {
        $CI = &get_instance();   
        if (!$CI->db->field_exists('shipping' ,db_prefix() . 'cart')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
              ADD COLUMN `shipping` DECIMAL(15,2) not null default "0.00",                
              ADD COLUMN `payment_method_title` varchar(250) null                
          ');
        }
     }
}

