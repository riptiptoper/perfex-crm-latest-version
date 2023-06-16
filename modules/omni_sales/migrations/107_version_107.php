<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_107 extends App_module_migration
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

		if (!$CI->db->field_exists('shipping_tax' ,db_prefix() . 'cart')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
              ADD COLUMN `shipping_tax` DECIMAL(15,2) null
              ');
        }
	}
}
