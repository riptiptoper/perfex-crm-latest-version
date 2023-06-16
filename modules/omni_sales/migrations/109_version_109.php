<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_109 extends App_module_migration
{
	public function up()
	{
		$CI = &get_instance();   
		
		add_option('number_of_days_to_save_diary_sync', 30);
		
	}
}
