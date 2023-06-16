<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_receipt_code',
    'pr_order_id',
    'date_add',
    'total_tax_money', 
    'total_goods_money',
    'value_of_inventory',
    'total_money',

];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_goods_receipt';
$join         = [ ];
$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND agent_id = '.$agent_id);

if($this->ci->input->post('day_vouchers')){
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if (isset($day_vouchers)) {

    $where[] = 'AND '.db_prefix().'sa_goods_receipt.date_add <= "' . $day_vouchers . '"';
    
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','date_add','date_c','goods_receipt_code', 'supplier_code']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'date_add'){
            $_data = _d($aRow['date_add']);
        }elseif ($aColumns[$i] == 'total_tax_money') {
            $_data = app_format_money((float)$aRow['total_tax_money'],'');
        }elseif($aColumns[$i] == 'goods_receipt_code'){
            $name = '<a href="' . site_url('sales_agent/portal/view_purchase/' . $aRow['id'] ).'" >' . $aRow['goods_receipt_code'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . site_url('sales_agent/portal/view_purchase/' . $aRow['id'] ).'" >' . _l('view') . '</a>';         
            
            $name .= ' | <a href="' . site_url('sales_agent/portal/delete_goods_receipt/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
            
            $name .= '</div>';

            $_data = $name;
        }elseif ($aColumns[$i] == 'total_goods_money') {
            $_data = app_format_money((float)$aRow['total_goods_money'],'');
        }elseif ($aColumns[$i] == 'total_money') {
            $_data = app_format_money((float)$aRow['total_money'],'');
        }elseif($aColumns[$i] == 'value_of_inventory') {
            $_data = app_format_money((float)$aRow['value_of_inventory'],'');
        }elseif($aColumns[$i] == 'pr_order_id'){
            $get_pur_order_name ='';
            
            if( ($aRow['pr_order_id'] != '') && ($aRow['pr_order_id'] != 0) ){
                $get_pur_order_name .='<a href="'. site_url('sales_agent/portal/order_detail/'.$aRow['pr_order_id']) .'" >'. sa_get_pur_order_name($aRow['pr_order_id']) .'</a>';
            }

            $_data = $get_pur_order_name;

        }
    


    $row[] = $_data;
}
$output['aaData'][] = $row;

}
