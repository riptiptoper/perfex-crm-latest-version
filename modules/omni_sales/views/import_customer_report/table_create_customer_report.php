<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [

    'id',
    'm_date_report',
    'm_total_amount',
    'm_total_quantity',
    'date_time_transaction',

    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_create_customer_report';
$join         = [ ];

$where = [];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
    $row[] =  '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';

    $id_value = $aRow['id'];
        if(has_permission('omni_sales', '', 'view') || is_admin() ){
            $id_value .= '<div class="row-options">';
            $id_value .= ' <a href="' . admin_url('omni_sales/view_customer_report_detail/' . $aRow['id'] ).'" >' . _l('view_report_detail') . '</a>';
            $id_value .= '</div>';
        }

    $row[] = $id_value;

    $row[] = $aRow['m_date_report'];
    $row[] =  app_format_money((float)$aRow['m_total_quantity'],'');
    $row[] =  app_format_money((float)$aRow['m_total_amount'],'');
    $row[] = $aRow['date_time_transaction'];

    $row[] = ' <a href="' . admin_url('omni_sales/table_create_invoice_from_customer_report/' . $aRow['id'] ).'"  class="btn btn-success btn-xs mleft5   title="'. _l('_create_invoice').'" data-original-title="'. _l('_create_invoice').'"><i class="fa fa-check"></i></a>';

    $output['aaData'][] = $row;

}
