<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'email',
    'phonenumber',
    db_prefix().'sa_clients.group',
    'created_at', 
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_clients';
$join         = [];
$where = [];

if ($this->ci->input->post('group') && $this->ci->input->post('group') != ''){
    array_push($where, 'AND '.db_prefix().'sa_clients.group = '.$this->ci->input->post('group') );
}

array_push($where, 'AND agent_id = '.get_sale_agent_user_id());

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        
        if($aColumns[$i] == 'id'){
            $_data = '<a href="'.site_url('sales_agent/portal/client/'. $aRow['id']).'" class="btn btn-icon btn-warning"><i class="fa fa-pencil"></i></a>';
            $_data .= '<a href="'.site_url('sales_agent/portal/delete_client/'. $aRow['id']).'" class="btn btn-icon btn-danger _delete"><i class="fa fa-remove"></i></a>';
        }else if($aColumns[$i] == db_prefix().'sa_clients.group'){
            $_data = get_sa_client_group_name($aRow[db_prefix().'sa_clients.group']);
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
