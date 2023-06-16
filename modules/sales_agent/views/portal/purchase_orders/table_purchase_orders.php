<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'order_number',
    'order_date',
    'total',
    'datecreated',
    'approve_status', 
    'delivery_date', 
    'delivery_status',
    'order_status',
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_pur_orders';
$join         = [];
$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND agent_id = '.$agent_id);

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND order_date >= "'.to_sql_date($this->ci->input->post('from_date')).'"');
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND order_date <= "'.to_sql_date($this->ci->input->post('to_date')).'"');
}

if ($this->ci->input->post('approve_status') && $this->ci->input->post('approve_status') !=  '') {

    array_push($where, 'AND approve_status = "'.$this->ci->input->post('approve_status').'"');
}

if ($this->ci->input->post('order_status') && $this->ci->input->post('order_status') !=  '') {

    array_push($where, 'AND order_status = "'.$this->ci->input->post('order_status').'"');
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['currency']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];

        $base_currency = get_base_currency();
        if($aRow['currency'] != 0){
            $base_currency = sa_get_currency_by_id($aRow['currency']);
        }
        
        if($aColumns[$i] == 'order_number'){
            $_data = '<a href="'.site_url('sales_agent/portal/order_detail/'. $aRow['id']).'">'.$aRow['order_number'].'</a>';
        }else if($aColumns[$i] == 'order_date'){
            $_data = _d($aRow['order_date']);
        }else if($aColumns[$i] == 'delivery_date'){
            $_data = _d($aRow['delivery_date']);
        }else if($aColumns[$i] == 'approve_status'){
            $_data = get_sa_status_approve($aRow['approve_status']);
        }else if($aColumns[$i] == 'datecreated'){
            $_data = _dt($aRow['datecreated']);
        }else if($aColumns[$i] == 'order_status'){
            $order_status_class = '';
            $order_status_text = '';
            if($aRow['order_status'] == 'new'){
              $order_status_class = 'label-info';
              $order_status_text = _l('new_order');
            }else if($aRow['order_status'] == 'delivered'){
              $order_status_class = 'label-success';
              $order_status_text = _l('delivered');
            }else if($aRow['order_status'] == 'confirmed'){
              $order_status_class = 'label-warning';
              $order_status_text = _l('confirmed');
            }else if($aRow['order_status'] == 'cancelled'){
              $order_status_class = 'label-danger';
              $order_status_text = _l('cancelled');
            }else if($aRow['order_status'] == 'return'){
               $order_status_class = 'label-warning';
               $order_status_text = _l('pur_return');
            }

            $_data = '<span class="label '.$order_status_class.'">'.$order_status_text.'</span>';
        }else if($aColumns[$i] == 'delivery_status'){
            if($aRow['delivery_status'] == 0){
                $delivery_status = '<span class="inline-block label label-danger" id="status_span_'.$aRow['id'].'" task-status-table="undelivered">'._l('undelivered');
            }else if($aRow['delivery_status'] == 1){
                $delivery_status = '<span class="inline-block label label-success" id="status_span_'.$aRow['id'].'" task-status-table="completely_delivered">'._l('completely_delivered');
            }else if($aRow['delivery_status'] == 2){
                $delivery_status = '<span class="inline-block label label-info" id="status_span_'.$aRow['id'].'" task-status-table="pending_delivered">'._l('pending_delivered');
            }else if($aRow['delivery_status'] == 3){
                $delivery_status = '<span class="inline-block label label-warning" id="status_span_'.$aRow['id'].'" task-status-table="partially_delivered">'._l('partially_delivered');
            }

            $delivery_status .= '</span>';
            $_data = $delivery_status;

        }else if($aColumns[$i] == 'id'){
            $_data = '';
            if($aRow['approve_status'] != 2){
                $_data .= '<a href="'.site_url('sales_agent/portal/pur_order/'.$aRow['id']).'" class="btn btn-warning btn-icon"><i class="fa fa-pencil"></i></a>';
            }
            $_data .= '<a href="'.site_url('sales_agent/portal/delete_pur_order/'.$aRow['id']).'" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';
        }else if($aColumns[$i] == 'total'){
            $_data = app_format_money($aRow['total'], $base_currency);
        }   
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
