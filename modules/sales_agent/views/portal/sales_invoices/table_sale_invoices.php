<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'inv_number',
    'total',
    'total_tax',
    'date',
    'clientid', 
    'duedate', 
    'status',
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_sale_invoices';
$join         = [];
$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND agent_id = '.$agent_id);

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND date >= "'.to_sql_date($this->ci->input->post('from_date')).'"');
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND date <= "'.to_sql_date($this->ci->input->post('to_date')).'"');
}

if ($this->ci->input->post('status')
    && $this->ci->input->post('status') != '') {

     array_push($where, 'AND status = "'.$this->ci->input->post('status').'"');
}

if ($this->ci->input->post('client')
    && $this->ci->input->post('client') != '') {

     array_push($where, 'AND clientid = "'.$this->ci->input->post('client').'"');
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

        if($aColumns[$i] == 'inv_number'){
            $_data = '<a href="'.site_url('sales_agent/portal/sale_invoice_detail/'.$aRow['id']).'">'.$aRow['inv_number'].'</a>';
        }else if($aColumns[$i] == 'total'){
            $_data = app_format_money($aRow['total'], $base_currency);
        }else if($aColumns[$i] == 'total_tax'){
            $_data = app_format_money($aRow['total_tax'], $base_currency);
        }else if($aColumns[$i] == 'date'){
            $_data = _d($aRow['date']);
        }else if($aColumns[$i] == 'clientid'){
            $_data = get_sa_customer_name_by_id($aRow['clientid']);
        }else if($aColumns[$i] == 'duedate'){
            $_data = _d($aRow['duedate']);
        }else if($aColumns[$i] == 'status'){
            $class = '';
            if($aRow['status'] == 'unpaid'){
                $class = 'danger';
            }elseif($aRow['status'] == 'paid'){
                $class = 'success';
            }elseif ($aRow['status'] == 'partially_paid') {
                $class = 'warning';
            }

            $_data = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['status']).'</span>';
        }else if($aColumns[$i] == 'id'){
            $option = '';

            $option .= '<a href="'.site_url('sales_agent/portal/sale_invoice/'.$aRow['id']).'" class="btn btn-warning btn-icon"><i class="fa fa-pencil"></i></a>';

            $option .= '<a href="'.site_url('sales_agent/portal/delete_sale_invoice/'.$aRow['id']).'" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>';

            $_data = $option;
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
