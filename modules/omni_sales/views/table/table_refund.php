<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('currencies_model');
$this->ci->load->model('payment_modes_model');

$order_id = $this->ci->input->post('order_id');

$data_base_currency = $this->ci->currencies_model->get_base_currency();
$currency_name = '';
if(isset($data_base_currency)){
$currency_name = $data_base_currency->name;
}
$aColumns = [ 
        'refunded_on',
        'amount',
        'payment_mode',
        'note',
        'id'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_refunds';
$join         = [];
$where = [];
array_push($where, 'AND order_id = '.$order_id);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['refunded_on', 'note']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = _d($aRow['refunded_on']);
    $row[] = app_format_money($aRow['amount'], $currency_name);
    $payment_mode = '';
    $data_payment = $this->ci->payment_modes_model->get($aRow['payment_mode']);
    if($data_payment){
        $payment_mode = $data_payment->name;        
    }
    $row[] = $payment_mode;
    $row[] = $aRow['note'];
    $option = '';
    if(has_permission('omni_order_list', '', 'edit') || is_admin()){
        $option .= '<a href="javascript:void(0)" onclick="edit_refund('.$aRow['id'].')" class="btn btn-default btn-icon">';
        $option .= '<i class="fa fa-edit"></i>';
        $option .= '</a>';
    }

    if(has_permission('omni_order_list', '', 'delete') || is_admin()){
        $option .= '<a href="' . admin_url('omni_sales/delete_refund/'.$aRow['id'] .'/'.$order_id) . '" class="btn btn-danger btn-icon _delete">';
        $option .= '<i class="fa fa-remove"></i>';
        $option .= '</a>';
    }


    $row[] = $option; 
    $output['aaData'][] = $row;
}
