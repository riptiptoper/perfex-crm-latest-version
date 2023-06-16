<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

$aColumns = array_merge($aColumns, [
    'description',
    'long_description',
    db_prefix() . 'items.rate as rate',
    't1.taxrate as taxrate_1',
    't2.taxrate as taxrate_2',
    'unit',
    db_prefix() . 'items_groups.name as group_name',
    ]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'items';
$where        = [];
$join = [
    'LEFT JOIN ' . db_prefix() . 'taxes t1 ON t1.id = ' . db_prefix() . 'items.tax',
    'LEFT JOIN ' . db_prefix() . 'taxes t2 ON t2.id = ' . db_prefix() . 'items.tax2',
    'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id',
    ];
$additionalSelect = [
    db_prefix() . 'items.id',
    't1.name as taxname_1',
    't2.name as taxname_2',
    't1.id as tax_id_1',
    't2.id as tax_id_2',
    'group_id',
    ];

$program_product_group = $this->ci->Sales_agent_model->get_program_product_group($program_id);

$program_product = $this->ci->Sales_agent_model->get_program_product($program_id);

if($program_product != '' || $program_product_group != ''){
    if($program_product!= '' && $program_product_group == ''){
        array_push($where, 'AND '.db_prefix().'items.id IN ('.$program_product.')');
    }else if($program_product == '' && $program_product_group != ''){
        array_push($where, 'AND '.db_prefix().'items.group_id IN ('.$program_product_group.')');
    }else if($program_product != '' && $program_product_group != ''){
        array_push($where, 'AND ('.db_prefix().'items.group_id IN ('.$program_product_group.') OR '.db_prefix().'items.id IN ('.$program_product.'))');
    }
}else{
    array_push($where, 'AND 1=2');
}

array_push($where, 'AND can_be_sold = "can_be_sold"');

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $_data = '';
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

    $row[] = $_data;

    $descriptionOutput = '';
    if(is_staff_logged_in()){
        $descriptionOutput =  '<a href="'.admin_url('sales_agent/item_detail/'.$aRow['id']).'">'.$aRow['description'].'</a>';
    }else if(is_sale_agent_logged_in()){
        $descriptionOutput =  '<a href="'.site_url('sales_agent/portal/item_detail/'.$aRow['id']).'">'.$aRow['description'].'</a>';
    }

    $row[] = $descriptionOutput;

    $row[] = $aRow['long_description'];

    $row[] = app_format_money($aRow['rate'], get_base_currency());

    $aRow['taxrate_1'] = $aRow['taxrate_1'] ?? 0;
    $row[]             = '<span data-toggle="tooltip" title="' . $aRow['taxname_1'] . '" data-taxid="' . $aRow['tax_id_1'] . '">' . app_format_number($aRow['taxrate_1']) . '%' . '</span>';

    $aRow['taxrate_2'] = $aRow['taxrate_2'] ?? 0;
    $row[]             = '<span data-toggle="tooltip" title="' . $aRow['taxname_2'] . '" data-taxid="' . $aRow['tax_id_2'] . '">' . app_format_number($aRow['taxrate_2']) . '%' . '</span>';
    $row[]             = $aRow['unit'];

    $row[] = $aRow['group_name'];


    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
