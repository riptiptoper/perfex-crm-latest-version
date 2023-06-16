<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    'id',
    'commodity_code',
    'description',
    'commodity_barcode',
    'group_id',
    'unit_id',
    'rate',
    'tax',
    'sku_code',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'items';

$where = [];

array_push($where, 'AND can_be_sold = "can_be_sold"');

$agent_id = get_sale_agent_user_id();

$produt_ids = get_product_ids_of_program_by_agent($agent_id);
$group_ids = get_product_group_ids_of_program_by_agent($agent_id);

if(count($produt_ids) > 0 && count($group_ids) == 0){
    array_push($where, 'AND id IN ('.implode(',', $produt_ids).') ');
}else if(count($produt_ids) == 0 && count($group_ids) > 0){
    array_push($where, 'AND group_id IN ('.implode(',', $group_ids).') ');
}else if(count($produt_ids) > 0 && count($group_ids) > 0){
    array_push($where, 'AND (id IN ('.implode(',', $produt_ids).') OR group_id IN ('.implode(',', $group_ids).'))');
}else if(count($produt_ids) == 0 && count($group_ids) == 0){
    array_push($where, 'AND 1=2');
}

if ($this->ci->input->post('group') && $this->ci->input->post('group') != ''){
    array_push($where, 'AND group_id = '.$this->ci->input->post('group') );
}

if ($this->ci->input->post('program') && $this->ci->input->post('program') != ''){
    $program = $this->ci->input->post('program');

    $produt_ids = get_product_ids_of_program($program);
    $group_ids = get_product_group_ids_of_program($program);

    if(count($produt_ids) > 0 && count($group_ids) == 0){
        array_push($where, 'AND id IN ('.implode(',', $produt_ids).') ');
    }else if(count($produt_ids) == 0 && count($group_ids) > 0){
        array_push($where, 'AND group_id IN ('.implode(',', $group_ids).') ');
    }else if(count($produt_ids) > 0 && count($group_ids) > 0){
        array_push($where, 'AND (id IN ('.implode(',', $produt_ids).') OR group_id IN ('.implode(',', $group_ids).'))');
    }else if(count($produt_ids) == 0 && count($group_ids) == 0){
        array_push($where, 'AND 1=2');
    }

}


$arr_inventory_number = $this->ci->Sales_agent_model->arr_inventory_number_by_item($agent_id);

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

$base_currency = get_base_currency();

foreach ($rResult as $aRow) {
    $product_inventory_quantity = 0;
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        /*get commodity file*/
        if ($aColumns[$i] == 'id') {
            $arr_images = $this->ci->Sales_agent_model->get_item_attachments($aRow['id']);
            if(count($arr_images) > 0){
                if(file_exists('modules/purchase/uploads/item_img/' .$arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name'])){
                    $_data = '<img class="images_w_table" src="' . site_url('modules/purchase/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
                }else if(file_exists('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name'])){
                    $_data = '<img class="images_w_table" src="' . site_url('modules/warehouse/uploads/item_img/' . $arr_images[0]['rel_id'] .'/'.$arr_images[0]['file_name']).'" alt="'.$arr_images[0]['file_name'] .'" >';
                }else if(file_exists('modules/manufacturing/uploads/products/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name'])){
                    $_data = '<img class="images_w_table" src="' . site_url('modules/manufacturing/uploads/products/' . $arr_images[0]['rel_id'] . '/' . $arr_images[0]['file_name']) . '" alt="' . $arr_images[0]['file_name'] . '" >';
                }else{
                    $_data = '<img class="images_w_table" src="' . site_url('modules/sales_agent/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
                }

            }else{

                $_data = '<img class="images_w_table" src="' . site_url('modules/sales_agent/uploads/nul_image.jpg' ).'" alt="nul_image.jpg">';
            }
        }


        if($aColumns[$i] == 'commodity_code') {
            $code = '<a href="' . site_url('sales_agent/portal/item_detail/' . $aRow['id'] ).'" >' . $aRow['commodity_code'] . '</a>';
            $_data = $code;

        }elseif ($aColumns[$i] == 'description') {
            
            $_data = $aRow['description'];

        }elseif ($aColumns[$i] == 'unit_id') {
            if($aRow['unit_id'] != null){
                $_data = sa_get_unit_type($aRow['unit_id']) != null ? sa_get_unit_type($aRow['unit_id'])->unit_name : '';
            }else{
                $data = '';
            }
        }elseif ($aColumns[$i] == 'rate') {
            $_data = app_format_money((float)$aRow['rate'],$base_currency->symbol);
        }elseif ($aColumns[$i] == 'tax') {
            $tax ='';
            $tax_rate = sa_get_tax_rate($aRow['tax']);
            $tax_rate2 = sa_get_tax_rate($aRow['tax2']);
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
                $_data = sa_get_group_name_item($aRow['group_id']) != null ? sa_get_group_name_item($aRow['group_id'])->name : '';
            }else{
                $_data = '';
            }
        }else if($aColumns[$i] == 'sku_code'){
            $programs = get_program_of_product($aRow['id'], $aRow['group_id']);

            $text = '';
            foreach($programs as $key => $program){
                if(($key + 1)%2 == 0){
                    $text .= '<a href="'.site_url('sales_agent/portal/program_detail/'.$program).'"><span class="label label-tag">'.get_program_name_by_id($program).'</span></a><br /><br />';
                }else{
                    $text .= '<a href="'.site_url('sales_agent/portal/program_detail/'.$program).'"><span class="label label-tag">'.get_program_name_by_id($program).'</span></a>';
                }
                
            }

            $_data = $text;
        }elseif ($aColumns[$i] == 'commodity_barcode') {
            /*inventory number*/
            $inventory_number = 0;

            if(isset($arr_inventory_number[$aRow['id']])){
                $inventory_number =  $arr_inventory_number[$aRow['id']]['inventory_number'];
            }
            $_data = $inventory_number;

        }
     
     
    $row[] = $_data;
        
    }
    $output['aaData'][] = $row;
}

