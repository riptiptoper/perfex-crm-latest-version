<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
	'warehouse_code',
	'warehouse_name',
	'warehouse_address',
	db_prefix().'sa_warehouse.order as wh_order',
	'display',
	'note',
];
$sIndexColumn = 'warehouse_id';
$sTable = db_prefix() . 'sa_warehouse';

$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND agent_id = '. $agent_id);

$join= [];


$i = 0;


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['warehouse_id','warehouse_code','warehouse_name','warehouse_address','city', 'state', 'zip_code', 'country']);

$output = $result['output'];
$rResult = $result['rResult'];



	foreach ($rResult as $aRow) {
		$row = [];
		for ($i = 0; $i < count($aColumns); $i++) {

			if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
	            $_data = $aRow[strafter($aColumns[$i], 'as ')];
	        } 

			if ($aColumns[$i] == 'warehouse_code') {
				$code = '<a href="' . site_url('sales_agent/portal/view_warehouse_detail/' . $aRow['warehouse_id']) . '">' . $aRow['warehouse_code'] . '</a>';
				$code .= '<div class="row-options">';

				$code .= '<a href="' . site_url('sales_agent/portal/view_warehouse_detail/' . $aRow['warehouse_id']) . '" >' . _l('view') . '</a>';

				$code .= ' | <a href="#" onclick="edit_warehouse_type(this, '.$aRow['warehouse_id'] .'); return false;"  data-commodity_id="' . $aRow['warehouse_id'] . '"  >' . _l('edit') . '</a>';
				
				$code .= ' | <a href="' . site_url('sales_agent/portal/delete_warehouse/' . $aRow['warehouse_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
				
				$code .= '</div>';

				$_data = $code;

			} elseif ($aColumns[$i] == 'warehouse_name') {

				$_data = $aRow['warehouse_name'];

			}elseif($aColumns[$i] == 'warehouse_address'){

				$address='';

				$warehouse_address = [];
				$warehouse_address[0] =  $aRow['warehouse_address'];
				$warehouse_address[1] = $aRow['city'];
				$warehouse_address[2] =  $aRow['state'];
				$warehouse_address[3] =  $aRow['country'];
				$warehouse_address[4] =  $aRow['zip_code'];

				foreach ($warehouse_address as $key => $add_value) {
				    if(isset($add_value) && $add_value != ''){
				    	switch ($key) {
				    		case 0:
				    			$address .= $add_value.'<br>';
				    			break;
				    		case 1:
				    			$address .= $add_value;
				    			break;
				    		case 2:
				    			$address .= ', '.$add_value.'<br>';
				    			break;
				    		case 3:
				    			$address .= get_country_name($add_value);
				    			break;
				    		case 4:
				    			$address .= ', '.$add_value;
				    			break;

				    		default:
				    			# code...
				    			break;
				    	}

				    }
				}

				$_data = $address;

			} elseif ($aColumns[$i] == 'wh_order') {
				$_data = $aRow['wh_order'];

			} elseif ($aColumns[$i] == 'display') {

        		if($aRow['display'] == 0){
        		 $_data =  _l('not_display'); 
        		}else{
        			$_data = _l('display');
        		}

			} elseif ($aColumns[$i] == 'note') {

				$_data = $aRow['note'];
			}

			$row[] = $_data;

		}
		$output['aaData'][] = $row;
	}

