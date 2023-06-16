<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_133 extends App_module_migration
{
    public function up()
    {
        add_option('chat_members_can_create_groups', 1);
    }
}
