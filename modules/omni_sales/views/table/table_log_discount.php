<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('omni_sales_model');
$this->ci->load->model('client_groups_model');
$this->ci->load->model('clients_model');
$this->ci->load->model('warehouse/warehouse_model');

$aColumns = [ 
    'name_discount',
    'client',
    'order_number',
    'voucher_coupon',
    'total_order',
    'discount',
    'tax',
    'total_after',
    'date_apply',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_log_discount';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_apply']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['name_discount'];
    $row[] = get_company_name($aRow['client']);
    $row[] = '#'.$aRow['order_number'];
    $row[] = $aRow['voucher_coupon'];
    $row[] = $aRow['total_order'];
    $row[] = $aRow['discount'];
    $row[] = $aRow['tax'];
    $row[] = $aRow['total_after'];
    $row[] = _d($aRow['date_apply']);
    $output['aaData'][] = $row;

}
