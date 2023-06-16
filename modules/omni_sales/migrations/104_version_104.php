<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_Version_104 extends App_module_migration
{
     public function up()
     {
        update_option('records_time1', date("H:i:s"));
        update_option('records_time2', date("H:i:s"));
        update_option('records_time3', date("H:i:s"));
        update_option('records_time4', date("H:i:s")); 
        update_option('records_time5', date("H:i:s"));
        update_option('records_time6', date("H:i:s"));
        update_option('records_time7', date("H:i:s"));
        update_option('records_time8', date("H:i:s"));
     }
}

