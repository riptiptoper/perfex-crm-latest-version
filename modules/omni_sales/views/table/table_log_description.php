<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('currencies_model');
$currency = $this->ci->currencies_model->get_base_currency();

$aColumns = [ 
    'id',
    'name',
    'description',
    'short_description',
    'chanel',
    'sku',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_log_sync_woo';
$join         = [];
$where = [];
array_push($where, 'AND type = "description"');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_sync']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = [];

    $row[] = $aRow['name'];
    $row[] = $aRow['description'];
    $row[] = $aRow['short_description'];
    $row[] = $aRow['chanel'];
    $row[] = $aRow['sku'];
    $row[] = _dt($aRow['date_sync']);

    $output['aaData'][] = $row;

}
