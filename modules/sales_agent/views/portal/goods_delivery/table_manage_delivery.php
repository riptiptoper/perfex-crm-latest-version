<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'goods_delivery_code',
    'customer_code',
    'date_add',
    'invoice_id',
    'to_', 
    'address',
    'staff_id',
    'delivery_status',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_goods_delivery';
$join         = [ ];

$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND agent_id = '.$agent_id);

if($this->ci->input->post('day_vouchers')){
    $day_vouchers = to_sql_date($this->ci->input->post('day_vouchers'));
}

if (isset($day_vouchers)) {

    $where[] = 'AND '.db_prefix().'sa_goods_delivery.date_add <= "' . $day_vouchers . '"';
    
}




$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id','date_add','date_c','goods_delivery_code','total_money', 'type_of_delivery']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {
    $CI           = & get_instance();

        $_data = $aRow[$aColumns[$i]];

        if($aColumns[$i] == 'customer_code'){
            $_data = '';
            if($aRow['customer_code']){
                $CI->db->where(db_prefix() . 'sa_clients.id', $aRow['customer_code']);
                $client = $CI->db->get(db_prefix() . 'sa_clients')->row();
                if($client){
                    $_data = $client->name;
                }

            }


        }elseif($aColumns[$i] == 'invoice_id'){
            $_data = '';
            if($aRow['invoice_id']){

                $type_of_delivery='';
                if($aRow['type_of_delivery'] == 'partial'){
                    $type_of_delivery .= '( <span class="text-danger">'._l($aRow['type_of_delivery']).'</span> )';
                }elseif($aRow['type_of_delivery'] == 'total'){
                    $type_of_delivery .= '( <span class="text-success">'._l($aRow['type_of_delivery']).'</span> )';
                }

               $_data = get_sa_invoice_number($aRow['invoice_id']).' '.sa_get_invoice_company($aRow['invoice_id']).' '.$type_of_delivery;

            }


        }elseif($aColumns[$i] == 'date_add'){

            $_data = _d($aRow['date_add']);

        }elseif($aColumns[$i] == 'staff_id'){

            $_data =  get_contact_full_name($aRow['staff_id']);
        }elseif($aColumns[$i] == 'goods_delivery_code'){
            $name = '<a href="' . site_url('sales_agent/portal/view_delivery/' . $aRow['id'] ).'" >' . $aRow['goods_delivery_code'] . '</a>';

            $name .= '<div class="row-options">';

            $name .= '<a href="' . site_url('sales_agent/portal/view_delivery/' . $aRow['id'] ).'" >' . _l('view') . '</a>';
            
            $name .= ' | <a href="' . site_url('sales_agent/portal/goods_delivery/' . $aRow['id'] ).'/true" >' . _l('edit') . '</a>';
            
            $name .= ' | <a href="' . site_url('sales_agent/portal/delete_goods_delivery/' . $aRow['id'] ).'" class="text-danger _delete" >' . _l('delete') . '</a>';
            
            $name .= '</div>';

            $_data = $name;
        }elseif ($aColumns[$i] == 'custumer_name') {
            $_data =$aRow['custumer_name'];
        }elseif ($aColumns[$i] == 'to_') {
            $_data =    $aRow['to_'];
        }elseif($aColumns[$i] == 'address') {
            $_data = $aRow['address'];
        }elseif($aColumns[$i] == 'delivery_status'){
            $_data = sa_render_delivery_status_html($aRow['id'], 'delivery', $aRow['delivery_status']);
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
