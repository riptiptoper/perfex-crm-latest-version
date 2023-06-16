<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('currencies_model');
$currency = $this->ci->currencies_model->get_base_currency();

$aColumns = [ 
    'id',
    'name',
    'regular_price',
    'sale_price',
    'chanel',
    'company',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_log_sync_woo';
$join         = [];
$where = [];

array_push($where, 'AND type = "orders"');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_sync']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['name'];
    $row[] = app_format_money($aRow['regular_price'], $currency->name);
    $row[] = $aRow['sale_price'];
    $row[] = $aRow['chanel'];
    $row[] = $aRow['company'];
    $row[] = _dt($aRow['date_sync']);
    
    $output['aaData'][] = $row;

}
