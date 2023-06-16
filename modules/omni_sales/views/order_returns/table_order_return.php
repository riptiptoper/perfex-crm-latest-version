<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'id',
	'order_return_name',
	'company_id',
	'number_of_item',
	'order_total',
	'total_amount',
	'discount_total',
	'total_after_discount',
	'datecreated',
	'approval',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'wh_order_returns';
$join         = [ ];

$where = [];

if ($this->ci->input->post('from_date')) {
	array_push($where, "AND date_format(datecreated, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('from_date')))) . "'");
}
if ($this->ci->input->post('to_date')) {
	array_push($where, "AND date_format(datecreated, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date($this->ci->input->post('to_date')))) . "'");
}
if ($this->ci->input->post('staff_id') && $this->ci->input->post('staff_id') != '') {
	array_push($where, 'AND staff_id IN (' . implode(', ', $this->ci->input->post('staff_id')) . ')');
}

if ($this->ci->input->post('rel_type_filter') && $this->ci->input->post('rel_type_filter') != '') {
	// $status_arr = $this->ci->input->post('rel_type_filter');

	// array_push($where, 'AND rel_type IN (' . implode('","', $status_arr) . ')');
}


if ($this->ci->input->post('delivery_id') && $this->ci->input->post('delivery_id') != '') {
	array_push($where, 'AND delivery_note_id IN (' . implode(', ', $this->ci->input->post('delivery_id')) . ')');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'order_return_name', 'additional_discount', 'approval', 'return_type', 'rel_id', 'rel_type', 'order_return_number', 'receipt_delivery_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = $aRow['id'];

		$name = '<a href="' . admin_url('warehouse/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . $aRow['order_return_number'] .' - '.$aRow['order_return_name']. '</a>';

		$name .= '<div class="row-options">';
		$name .= '<a href="' . admin_url('warehouse/view_order_return/' . $aRow['id'] ).'" onclick="init_order_return('.$aRow['id'].'); return false;">' . _l('view') . '</a>';

		if((has_permission('warehouse', '', 'edit') || is_admin()) && ($aRow['approval'] == 0)){
			$name .= ' | <a href="' . admin_url('warehouse/order_return/'.$aRow['rel_type']. '/' . $aRow['id'] ).'" >' . _l('edit') . '</a>';
		}

		if ((has_permission('warehouse', '', 'delete') || is_admin()) && ($aRow['approval'] == 0)) {
			$name .= ' | <a href="' . admin_url('warehouse/delete_order_return/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
		}			

		$name .= '</div>';

	$row[] = $name;
	
	if($aRow['rel_type'] == 'sales_return_order' || $aRow['rel_type'] == 'manual'){
		$row[] = get_company_name($aRow['company_id']);
	}else{
		$row[] = omni_get_vendor_company_name($aRow['company_id']);
	}

	$row[] = $aRow['number_of_item'];
	$row[] = app_format_money($aRow['order_total'], '');
	$row[] = app_format_money($aRow['total_amount'], '');
	$row[] = app_format_money($aRow['discount_total'], '');
	$row[] = app_format_money($aRow['total_after_discount'], '');
	$row[] = _dt($aRow['datecreated']);

	$approve_data = '';
	if($aRow['approval'] == 1){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
	}elseif($aRow['approval'] == 0){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
	}elseif($aRow['approval'] == -1){
		$approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
	}

	$row[] = $approve_data;

	$option = '';

	if(omni_get_status_modules('warehouse')){
		if($aRow['approval'] == 1 && $aRow['receipt_delivery_id'] == 0){
			$option .= icon_btn('warehouse/order_return_create_stock_import_export/' . $aRow['id'], 'share', 'btn-success _delete', ['data-original-title' => _l('wh_create_inventory_receiving_vocucher'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
		}else{
			if($aRow['receipt_delivery_id'] != 0){
				if($aRow['rel_type'] == 'manual' || $aRow['rel_type'] = 'sales_return_order'){
					$option .= icon_btn('warehouse/manage_purchase#' . $aRow['receipt_delivery_id'], 'eye', 'btn-primary', ['data-original-title' => _l('goods_receipt'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);

				}elseif($aRow['rel_type'] = 'purchasing_return_order'){
					$option .= icon_btn('warehouse/manage_delivery#' . $aRow['receipt_delivery_id'], 'eye', 'btn-primary', ['data-original-title' => _l('stock_export'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']);
				}
			}
		}
	}

	// $row[] = $option;

	
	$output['aaData'][] = $row;

}
