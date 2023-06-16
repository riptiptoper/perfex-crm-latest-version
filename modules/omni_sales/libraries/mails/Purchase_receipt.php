<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_receipt extends App_mail_template
{

    protected $notification_info;



    public $slug = 'purchase-receipt';

    public function __construct($notification_info)
    {
        parent::__construct();

        $this->notification_info = $notification_info;
        
        $this->set_merge_fields('purchase_receipt_merge_fields', $this->notification_info);
    }
    public function build()
    {

        $this->to($this->notification_info->email);
        
    }
}
