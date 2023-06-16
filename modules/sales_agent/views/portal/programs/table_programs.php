<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'from_date',
    'to_date',
    'created_by',
    'created_at', 
    'descriptions',
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_programs';
$join         = [];
$where = [];

$agent_id = get_sale_agent_user_id();

$this->ci->load->model('clients_model');
$agent_groups = $this->ci->clients_model->get_customer_groups($agent_id);

$or_where = '';
if(count($agent_groups)){
    foreach($agent_groups as $groups){
        $or_where .= ' OR find_in_set('.$groups['groupid'].', agent_group) OR find_in_set('.$groups['groupid'].', agent_group_can_view)';
    }
}

array_push($where, 'AND (find_in_set('.$agent_id.', agent) OR find_in_set('.$agent_id.', agent_can_view) '.$or_where.')');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['indefinite']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'from_date'){
            $_data = '';
            if($aRow['from_date'] != ''){
                $_data = _d($aRow['from_date']);
            }
        }elseif($aColumns[$i] == 'to_date'){
            $_data = '';
            if($aRow['to_date'] != ''){
                $_data = _d($aRow['to_date']);
            }

            if($aRow['indefinite'] == 1){
                $_data = _l('indefinite');
            }
        }elseif($aColumns[$i] == 'name'){
            $_data = '<a href="'.site_url('sales_agent/portal/program_detail/'.$aRow['id']).'" >'.$aRow['name'].'</a>';
        }else if($aColumns[$i] == 'created_by'){
            $_data = get_staff_full_name($aRow['created_by']);
        }elseif($aColumns[$i] == 'id'){
            $_data = '<a href="'.site_url('sales_agent/portal/program_detail/'.$aRow['id']).'" class="btn btn-icon btn-success mright5"><i class="fa fa-eye"></i></a>';
            if(total_rows(db_prefix().'sa_join_program_request', ['program_id' => $aRow['id'], 'agent_id' => get_sale_agent_user_id(), 'status' => 'new']) == 0){
                if(check_agent_not_in_program($aRow['id'])){
                    $_data .= '<a href="'.site_url('sales_agent/portal/join_program/'.$aRow['id']).'" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="'._l('joint_the_program').'"><i class="fa fa-sign-in"></i></a>';
                }
            }
        }elseif($aColumns[$i] == 'descriptions'){
            if(check_agent_not_in_program($aRow['id'])){ 
                if(total_rows(db_prefix().'sa_join_program_request', ['program_id' => $aRow['id'], 'agent_id' => get_sale_agent_user_id(), 'status' => 'new']) == 0){
                    $_data = '<span class="label label-warning">'._l('not_yet_participate').'</span>';
                }else{
                    $_data = '<span class="label label-warning">'._l('requested').'</span>';
                }
            }else{
                $_data = '<span class="label label-success">'._l('joined').'</span>';
            }
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
