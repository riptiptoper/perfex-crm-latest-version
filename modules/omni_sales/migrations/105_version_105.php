<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_105 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();   
        add_option('omni_show_products_by_department', 0);
        if (!$CI->db->field_exists('department' ,db_prefix() . 'sales_channel_detailt')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
                ADD COLUMN `department` text
                ');
        }
        add_option('bill_header_pos', '<div class="cls_003" style="text-align: center;"><span class="cls_003"><strong>PURCHASE RECEIPT</strong></span></div>');
        add_option('bill_footer_pos', '<div class="cls_004"><span class="cls_004">Thank you for shopping with us. Please come again</span></div>');

    }
}