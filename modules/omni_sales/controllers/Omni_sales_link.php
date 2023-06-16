<?php
class Omni_sales_link extends ClientsController
{   
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('omni_sales_model');
    }
  
}