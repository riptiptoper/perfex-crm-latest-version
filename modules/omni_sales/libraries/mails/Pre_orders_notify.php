<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pre_orders_notify extends App_mail_template
{

    protected $notification_info;



    public $slug = 'pre-orders-notify';

    public function __construct($notification_info)
    {
        parent::__construct();

        $this->notification_info = $notification_info;
        
        $this->set_merge_fields('pre_orders_notify_merge_fields', $this->notification_info);
    }
    public function build()
    {

        $this->to($this->notification_info->email);
        
    }
}
