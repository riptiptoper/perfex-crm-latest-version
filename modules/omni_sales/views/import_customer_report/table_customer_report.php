<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [

    'id',
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
    'date_add',
    'version',


    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_customer_report';
$join         = [ ];

$where = [];

$filter_authorized_by   = $this->ci->input->post('filter_authorized_by');
$filter_shift_type      = $this->ci->input->post('filter_shift_type');
$from_date              = $this->ci->input->post('from_date');
$to_date                = $this->ci->input->post('to_date');

if (isset($filter_authorized_by)) {
    $where[] = " AND ".db_prefix()."omni_customer_report.authorized_by  IN ( '" . implode( "', '" , $filter_authorized_by ) . "' ) ";
}

if (isset($filter_shift_type)) {
    $where[] = " AND ".db_prefix()."omni_customer_report.shift_type  IN ( '" . implode( "', '" , $filter_shift_type ) . "' ) ";
}

if (isset($from_date) && $from_date != '') {
    $from_date = to_sql_date($this->ci->input->post('from_date'), true);

    $where[] = ' AND '.db_prefix().'omni_customer_report.date_time_transaction > "' . $from_date . '"';
}

if (isset($to_date) && $to_date != '') {
    $to_date = to_sql_date($this->ci->input->post('to_date'), true);

    $where[] = ' AND '.db_prefix().'omni_customer_report.date_time_transaction <= "' . $to_date . '"';
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
    $row[] =  '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

            $id_value = $aRow['id'];
            if(has_permission('omni_sales', '', 'edit') || is_admin() ){

                $id_value .= ' <a href="#" onclick="edit_customer_reports(this, '.$aRow['id'] .'); return false;"  >' . _l('edit') . '</a>';
            }

    $row[] = $id_value;

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
    $row[] = $aRow['date_add'];
    $row[] = $aRow['version'];

    $output['aaData'][] = $row;

}
