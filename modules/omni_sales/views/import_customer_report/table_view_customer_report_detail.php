<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [

    'ser_no',
    'authorized_by',
    'date',
    'time',
    'transaction_id',
    'receipt',
    'pay_mode',
    'nozzle',
    'product',
    'quantity',
    'total_sale',
    'ref_slip_no',
    'shift_type',


    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_customer_report';
$join         = [ ];

$where = [];


$id                = $this->ci->input->post('id');

if (isset($id)) {
    $list_customer_report_id = $this->ci->omni_sales_model->get_create_customer_report($id);

    if($list_customer_report_id){
        $where[] = " AND ".db_prefix()."omni_customer_report.id  IN ( " . $list_customer_report_id->list_customer_report_id. " ) ";

    }

}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'version']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    

    $row[] = $aRow['ser_no'];
    $row[] = $aRow['authorized_by'];
    $row[] = $aRow['date'];
    $row[] = $aRow['time'];
    $row[] = $aRow['transaction_id'];
    $row[] = $aRow['receipt'];
    $row[] = $aRow['pay_mode'];
    $row[] = $aRow['nozzle'];
    $row[] = $aRow['product'];
    $row[] =  app_format_money((float)$aRow['quantity'],'');
    $row[] =  app_format_money((float)$aRow['total_sale'],'');
    $row[] = $aRow['ref_slip_no'];
    $row[] = _l($aRow['shift_type']);

    $output['aaData'][] = $row;

}


//
//
$aColumns_1 = [

    'date_add',
    'attendant_name',
    'shift_type',
    'date_report',
    'total_diesel',
    'total_pertrol',
    'total_other_product',
    'total_by_cash',
    'total_by_mpesa',
    'total_by_card',
    'total_by_invoice',
    'total_sales',


    ];
$sIndexColumn_1 = 'id';
$sTable_1       = db_prefix().'omni_create_customer_report_detail';
$join_1         = [ ];

$where_1 = [];


$id                = $this->ci->input->post('id');

if (isset($id)) {

    $where_1[] = " AND ".db_prefix()."omni_create_customer_report_detail.create_customer_report_id  = '" . $list_customer_report_id->list_customer_report_id. "'";

}

$result_1 = data_tables_init($aColumns_1, $sIndexColumn_1, $sTable_1, $join_1, $where_1, ['id']);

$output_1  = $result_1['output'];
$rResult_1 = $result_1['rResult'];

foreach ($rResult_1 as $aRow) {
    $row = [];
    
$row[] = $aRow['date_add'];
$row[] = $aRow['attendant_name'];
$row[] = $aRow['shift_type'];
$row[] = $aRow['date_report'];
$row[] = $aRow['total_diesel'];
$row[] = $aRow['total_pertrol'];
$row[] = $aRow['total_other_product'];
$row[] = $aRow['total_by_cash'];
$row[] = $aRow['total_by_mpesa'];
$row[] = $aRow['total_by_card'];
$row[] = $aRow['total_by_invoice'];
$row[] = app_format_money((float)$aRow['total_sales'],''); 

    $output['aaData'][] = $row;

}
