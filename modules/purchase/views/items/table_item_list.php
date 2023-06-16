<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    '1',
    'id',
    'commodity_code',
    'description',
    'group_id',
    'unit_id',
    'rate',
    'purchase_price',
    'tax',
    'from_vendor_item',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'items';



$where = [];

if ($group_filter)
{
    array_push($where, $group_filter);
}

if ($sub_group_filter)
{
    array_push($where, $sub_group_filter);
}

$join =[];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'commodity_barcode', 
    'group_id' ,
    'long_description' ,  
    'sku_code',  
    'sku_name',
    'tax2'  
    ]);


$output  = $result['output'];
$rResult = $result['rResult'];

$base_currency = get_base_currency_pur();

foreach ($rResult as $aRow) {
     $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        /*get commodity file*/
        if ($aColumns[$i] == 'id') {
            $arr_images = $this->ci->purchase_model->get_item_attachments($aRow['id']);
            if(count($arr_images) > 0){
                if(file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER .$arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name'])){
                    $_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
                }else if(file_exists('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name'])){
                    $_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
                }else {
                    $_data = '<img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/products/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name']) . '" alt="' . $arr_images[0]['file_name'] . '" >';
                }

            }else{

                $_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
            }
        }


         if($aColumns[$i] == 'commodity_code') {
             $code = '<a href="' . admin_url('purchase/commodity_detail/' . $aRow['id'] ).'" onclick="init_commodity_detail('.$aRow['id'].'); return false;">' . $aRow['commodity_code'] . '</a>';
              $code .= '<div class="row-options">';

            $code .= '<a href="' . admin_url('purchase/commodity_detail/' . $aRow['id'] ).'" onclick="init_commodity_detail('.$aRow['id'].'); return false;">' . _l('view') . '</a>';
            if (has_permission('purchase_items', '', 'edit') || is_admin()) {
                $code .= ' | <a href="#" onclick="edit_commodity_item(this); return false;"  data-commodity_id="'.$aRow['id'].'" data-description="'.$aRow['description'].'" data-unit_id="'.$aRow['unit_id'].'" data-commodity_code="'.$aRow['commodity_code'].'" data-commodity_barcode="'.$aRow['commodity_barcode'].'" data-long_description="'.$aRow['long_description'].'" data-rate="'.$aRow['rate'].'" data-group_id="'.$aRow['group_id'].'" data-tax="'.$aRow['tax'].'" data-tax2="'.$aRow['tax2'].'"  data-sku_code="'.$aRow['sku_code'].'" data-sku_name="'.$aRow['sku_name'].'" data-purchase_price="'.$aRow['purchase_price'].'" >' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_items', '', 'delete') || is_admin()) {
                $code .= ' | <a href="' . admin_url('purchase/delete_commodity/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }

            $code .= '</div>';


            $_data = $code;

        }elseif ($aColumns[$i] == 'description') {
            
            $_data = $aRow['description'];

        }elseif ($aColumns[$i] == 'unit_id') {
            if($aRow['unit_id'] != null){
                $_data = get_unit_type_item($aRow['unit_id']) != null ? get_unit_type_item($aRow['unit_id'])->unit_name : '';
            }else{
                $data = '';
            }
        }elseif ($aColumns[$i] == 'rate') {
            $_data = app_format_money((float)$aRow['rate'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'purchase_price'){
            $price = app_format_money((float)$aRow['purchase_price'],$base_currency->symbol);

            $vendor_item = $this->ci->purchase_model->get_item_of_vendor($aRow['from_vendor_item']);
            if(isset($vendor_item->vendor_id)){ 
                $vendor_currency_id = get_vendor_currency($vendor_item->vendor_id);

                $vendor_currency = $base_currency;
                if($vendor_currency_id != 0){
                    $vendor_currency = pur_get_currency_by_id($vendor_currency_id);
                }

                if($vendor_currency->name != $base_currency->name){
                    $price .= '<br>'._l('original_price').': '.app_format_money($vendor_item->rate, $vendor_currency->name);
                }
            }

            $_data = $price;

        }elseif ($aColumns[$i] == 'tax') {
            $tax ='';
            $tax_rate = get_tax_rate_item($aRow['tax']);
            $tax_rate2 = get_tax_rate_item($aRow['tax2']);
            if($aRow['tax']){
                if($tax_rate && $tax_rate != null && $tax_rate != 'null'){
                    $tax .= _l('tax_1').': '.$tax_rate->name;
                }
            }

            if($aRow['tax2']){
                if($tax_rate2 && $tax_rate2 != null && $tax_rate2 != 'null'){
                    $tax .= '<br>'._l('tax_2').': '.$tax_rate2->name;
                }
            }

            $_data = $tax;

        }elseif ($aColumns[$i] == 'group_id') {
            if($aRow['group_id'] != null){
                $_data = get_group_name_item($aRow['group_id']) != null ? get_group_name_item($aRow['group_id'])->name : '';
            }else{
                $_data = '';
            }
        }elseif($aColumns[$i] == '1'){
                $_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        }elseif($aColumns[$i] == 'from_vendor_item'){
            $vendor_item = $this->ci->purchase_model->get_item_of_vendor($aRow['from_vendor_item']);
            if(isset($vendor_item->vendor_id)){
                $_data = '<a href="'.admin_url('purchase/vendor/'. $vendor_item->vendor_id).'">'.get_vendor_company_name($vendor_item->vendor_id).'</a>';
            }else{
                $_data ='';
            }

        }
     
     
    $row[] = $_data;
        
    }
    $output['aaData'][] = $row;
}
