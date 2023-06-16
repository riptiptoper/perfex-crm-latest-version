<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('omni_sales_model');
$this->ci->load->model('currencies_model');
$currency = $this->ci->currencies_model->get_base_currency();

$aColumns = [ 
    db_prefix().'woocommere_store_detailt.id',
     db_prefix().'woocommere_store_detailt.id',
    'product_id',
    'prices',
    'group_product_id'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'woocommere_store_detailt';
$join         = [' left join '.db_prefix() . 'items on '.db_prefix() . 'items.id = '.db_prefix() . 'woocommere_store_detailt.product_id',];
$where = [];
$id = $this->ci->input->post('id_store');
array_push($where,' AND woocommere_store_id = '.$id );

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['woocommere_store_id',db_prefix() . 'items.commodity_code',db_prefix() . 'items.description',db_prefix() . 'items.rate']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $name =$aRow['description'];
    $code =$aRow['commodity_code'];
    $rate =$aRow['rate'];
    $price_on_store = $aRow['prices'];
    
    $row[] = '<input type="checkbox" class="individual" data-id="'.$aRow['product_id'].'" data-product="'.$aRow['woocommere_store_id'].'" onchange="checked_add(this); return false;"/>';             
    $row[] = $code;             
    $row[] = $name;             
    $row[] = app_format_money($rate, $currency->name);
    $row[] = app_format_money($price_on_store, $currency->name);


   
    $option = '';
    $option .= '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['product_id']) . '" class="btn btn-default btn-icon" data-id="'.$aRow[db_prefix().'woocommere_store_detailt.id'].'" >';
    $option .= '<i class="fa fa-eye"></i>';
    $option .= '</a>';
  
    $option .= '<a href="#" class="btn btn-default btn-icon"  onclick="update_product_woo(this);" data-groupid="'.$aRow['group_product_id'].'"  data-prices="'.$price_on_store.'" data-price_on_store="'.$price_on_store.'" data-productid="'.$aRow['product_id'].'" class="btn btn-default btn-icon" data-id="'.$aRow[db_prefix().'woocommere_store_detailt.id'].'" >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';




    $option .= '<a href="' . admin_url('omni_sales/delete_product_store/'.$aRow['woocommere_store_id'].'/'. $aRow[db_prefix().'woocommere_store_detailt.id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    $row[] = $option; 
    $output['aaData'][] = $row;

}
