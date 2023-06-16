<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ 
    'id',
    'store',
    'sync_omni_sales_products',
    'sync_omni_sales_inventorys',
    'price_crm_woo',
    'sync_omni_sales_description',
    // 'sync_omni_sales_images',
    'sync_omni_sales_orders',
    // 'product_info_image_enable_disable',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'omni_setting_woo_store';
$join         = [];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['time1','time2','time3','time4','time5','time6','time7','time8']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = get_name_store($aRow['store']);
    $row[] = $sync_omni_sales_products = ($aRow['sync_omni_sales_products'] == 1)?'On':'Off';

    $row[] = $sync_omni_sales_inventorys = ($aRow['sync_omni_sales_inventorys'] == 1)?'On':'Off';
    $row[] = $price_crm_woo = ($aRow['price_crm_woo'] == 1)?'On':'Off'; 
    $row[] = $sync_omni_sales_description = ($aRow['sync_omni_sales_description'] == 1)?'On':'Off';
    // $row[] = $sync_omni_sales_images = ($aRow['sync_omni_sales_images'] == 1)?'On':'Off'; 
    $row[] = $sync_omni_sales_orders = ($aRow['sync_omni_sales_orders'] == 1)?'On':'Off';
    // $row[] = $product_info_image_enable_disable = ($aRow['product_info_image_enable_disable'] == 1)?'On':'Off';

    $option = '';
  
    $option .= '<a href="#" class="btn btn-default btn-icon"  onclick="update_setting_woo_store(this);"  data-id="'.$aRow['id'].'" data-time1="'.$aRow['time1'].'" data-time2="'.$aRow['time2'].'" data-time3="'.$aRow['time3'].'" data-time4="'.$aRow['time4'].'" data-time5="'.$aRow['time5'].'" data-time6="'.$aRow['time6'].'" data-time7="'.$aRow['time7'].'" data-time8="'.$aRow['time8'].'"  data-store="'.$aRow['store'].'" data-sync_omni_sales_inventorys="'.$aRow['sync_omni_sales_inventorys'].'" data-price_crm_woo="'.$aRow['price_crm_woo'].'" data-sync_omni_sales_description="'.$aRow['sync_omni_sales_description'].'" data-sync_omni_sales_orders="'.$aRow['sync_omni_sales_orders'].'"  data-sync_omni_sales_products="'.$aRow['sync_omni_sales_products'].'"  class="btn btn-default btn-icon" >';
    $option .= '<i class="fa fa-edit"></i>';
    $option .= '</a>';
    $option .= '<a href="' . admin_url('omni_sales/delete_sync_auto_store/'.$aRow['id']) . '" class="btn btn-danger btn-icon _delete">';
    $option .= '<i class="fa fa-remove"></i>';
    $option .= '</a>';
    $row[] = $option; 

    $output['aaData'][] = $row;

}
