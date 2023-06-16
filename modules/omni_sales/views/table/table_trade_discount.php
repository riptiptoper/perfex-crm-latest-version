<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('omni_sales_model');
$this->ci->load->model('client_groups_model');
$this->ci->load->model('clients_model');
$this->ci->load->model('currencies_model');
$data_base_currency = $this->ci->currencies_model->get_base_currency();
$currency_name = '';
if(isset($data_base_currency)){
$currency_name = $data_base_currency->name;
}

$aColumns = [ 
    'id',
    'start_time',
    'end_time',
    'formal',
    'discount',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_trade_discount';
$join         = [];
$where = [];

array_push($where, 'AND voucher = ""');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['name_trade_discount']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['name_trade_discount'];
    $row[] = _d($aRow['start_time']);
    $row[] = _d($aRow['end_time']);
    $discount = 0;
    if($aRow['formal'] == 1){
        $row[] = _l('percent_of_product');
        $discount = $aRow['discount'].'%';
    }else{
        $row[] = _l('price');
        $discount = app_format_money($aRow['discount'],'').' '.$currency_name;
    }

    $row[] = $discount;
   
    $option = '';
    $option .= '<a href="' . admin_url('omni_sales/new_trade_discount/' . $aRow['id'].'') . '" class="btn btn-default btn-icon" " >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
  
    $option .= '<a href="' . admin_url('omni_sales/delete_trade_discount/'.$aRow['id'] .'') . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    $row[] = $option; 
    $output['aaData'][] = $row;

}
