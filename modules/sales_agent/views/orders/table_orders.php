<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'order_number',
    'agent_id',
    'order_date',
    'total',
    'datecreated',
    'invoice_id', 
    'delivery_date', 
    'delivery_status',
    'order_status',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_pur_orders';
$join         = [];
$where = [];

array_push($where, 'AND approve_status = 2');

if ($this->ci->input->post('agent') && $this->ci->input->post('agent') != ''){
    array_push($where, 'AND agent_id = '.$this->ci->input->post('agent'));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','currency']);

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
            $_data = '<a href="'.admin_url('sales_agent/order_detail/'. $aRow['id']).'">'.$aRow['order_number'].'</a>';
        }else if($aColumns[$i] == 'order_date'){
            $_data = _d($aRow['order_date']);
        }else if($aColumns[$i] == 'delivery_date'){
            $_data = _d($aRow['delivery_date']);
        }else if($aColumns[$i] == 'invoice_id'){
            if(is_numeric($aRow['invoice_id']) && $aRow['invoice_id'] > 0 ){
                $_data = '<a href="'.admin_url('invoices#'.$aRow['invoice_id']).'">'.format_invoice_number($aRow['invoice_id']).'</a>';
            }else{
                $_data = '<span class="label label-warning">'._l('not_created_yet').'</span>';
            }

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

        }else if($aColumns[$i] == 'agent_id'){
            $_data = '<a href="'.admin_url('sales_agent/sale_agent/'.$aRow['id']).'">'.get_company_name($aRow['agent_id']).'</a>';
        }else if($aColumns[$i] == 'total'){
            $_data = app_format_money($aRow['total'], $base_currency);
        }   
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
