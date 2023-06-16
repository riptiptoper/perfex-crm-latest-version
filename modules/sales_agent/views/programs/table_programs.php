<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'from_date',
    'to_date',
    'created_by',
    'agent_group', 
    'agent', 
    'id',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_programs';
$join         = [];
$where = [];

if ($this->ci->input->post('group') && $this->ci->input->post('group') != ''){
    array_push($where, 'AND find_in_set('.$this->ci->input->post('group').', agent_group)');
}

if ($this->ci->input->post('agent') && $this->ci->input->post('agent') != ''){
    array_push($where, 'AND find_in_set('.$this->ci->input->post('agent').', agent)');
}

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
            $_data = '<a href="'.admin_url('sales_agent/program_detail/'.$aRow['id']).'" >'.$aRow['name'].'</a>';
        }else if($aColumns[$i] == 'created_by'){
            $_data = '<a href="'.admin_url('staff/profile/'.$aRow['created_by']).'">'.get_staff_full_name($aRow['created_by']).'</a>';
        }elseif($aColumns[$i] == 'id'){
            $_data = '<a href="'.admin_url('sales_agent/program_detail/'.$aRow['id']).'" class="btn btn-icon btn-success mright5"><i class="fa fa-eye"></i></a>';
            $_data .= '<a href="'.admin_url('sales_agent/agent_program/'.$aRow['id']).'" class="btn btn-icon btn-warning mright5"><i class="fa fa-pencil"></i></a>';
            $_data .= '<a href="'.admin_url('sales_agent/delete_program/'.$aRow['id']).'" class="btn btn-icon btn-danger _delete"><i class="fa fa-remove"></i></a>';
        }elseif($aColumns[$i] == 'agent_group'){

            $groups = explode(',',  $aRow['agent_group']);
            $text = '';
            foreach($groups as $key => $gr){
                if(($key + 1)%2 == 0){
                    $text .= '<span class="label label-tag">'.get_agent_group_name($gr).'</span><br /><br />';
                }else{
                    $text .= '<span class="label label-tag">'.get_agent_group_name($gr).'</span>';
                }
                
            }

            $_data = $text;
        }else if($aColumns[$i] == 'agent'){

            $agents = explode(',',  $aRow['agent']);
            $text = '';
            foreach($agents as $key => $cli){
                if(($key + 1)%2 == 0){
                    $text .= '<a href="'.admin_url('sales_agent/sale_agent/'.$cli).'"><span class="label label-tag">'.get_company_name($cli).'</span></a><br /><br />';
                }else{
                    $text .= '<a href="'.admin_url('sales_agent/sale_agent/'.$cli).'"><span class="label label-tag">'.get_company_name($cli).'</span></a>';
                }
                
            }

            $_data = $text;
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
