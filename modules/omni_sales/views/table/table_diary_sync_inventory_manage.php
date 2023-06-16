<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('currencies_model');
$currency = $this->ci->currencies_model->get_base_currency();

$aColumns = [ 
    'id',
    'name',
    'regular_price',
    'sale_price',
    'date_on_sale_from',
    'date_on_sale_to',
    'short_description',
    'stock_quantity',
    'stock_quantity_history',
    'chanel',
    'sku',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_log_sync_woo';
$join         = [];
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_sync']);

array_push($where, 'AND type = "inventory"');

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];
    $row[] = $aRow['name'];
    $row[] = app_format_money($aRow['regular_price'], $currency->name);
    $row[] = $aRow['sale_price'];
    $row[] = $aRow['date_on_sale_from'];
    $row[] = $aRow['date_on_sale_to'];
    $row[] = $aRow['short_description'];
    $row[] = $aRow['stock_quantity'];
    $row[] = $aRow['stock_quantity_history'];
    $row[] = $aRow['chanel'];
    $row[] = $aRow['sku'];
    $row[] = _dt($aRow['date_sync']);
    $output['aaData'][] = $row;

}
