<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_108 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		if (!omni_channel_exists('pre_order')) {
			$data['channel'] = 'pre_order';
			$data['status'] = 'deactive';
			$CI->db->insert(db_prefix().'sales_channel' , $data);
		}
		if (!$CI->db->field_exists('enable' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `enable` int not null default 1
				');
		}
		add_option('omni_default_seller', '');
		if (!$CI->db->field_exists('duedate' ,db_prefix() . 'cart')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
				ADD COLUMN `duedate` date NULL
				');
		}

		if (!$CI->db->table_exists(db_prefix() . 'omni_pre_order_product_setting')) {
			$CI->db->query('CREATE TABLE `' . db_prefix() . "omni_pre_order_product_setting` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`channel_id` int(11) not null,
				`customer_group` text null,
				`customer` text null,
				`group_product_id` int(11) NULL,
				`datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
		}

		if (!$CI->db->field_exists('pre_order_product_st_id' ,db_prefix() . 'sales_channel_detailt')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
				ADD COLUMN `pre_order_product_st_id` int(11) null
				');
		}
		
		if (!$CI->db->field_exists('customer_group' ,db_prefix() . 'sales_channel_detailt')) {
			$CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
				ADD COLUMN `customer_group` text null,
				ADD COLUMN `customer` text null
				');
		}
		if ($CI->db->field_exists('type' ,db_prefix() . 'emailtemplates')) {
			$CI->db->where('type', "purchase_receipt");
			$CI->db->update( db_prefix() . 'emailtemplates', ['type' => "omni_sales"]);
		}
		
		create_email_template('Pre-orders notify', 'Hi {seller_name}! <br /><br />You have a new order from {buyer_name}, the order is created at {create_at}. View order details: {link}.<br />', 'omni_sales', 'Pre-orders notify (Sent to seller)', 'pre-orders-notify');
		create_email_template('Pre-orders handover', 'Hi {to_name}! <br /><br />{from_name} has handed over an order to you. View order details: {link}.<br />', 'omni_sales', 'Pre-orders handover', 'pre-orders-handover');
	}
}