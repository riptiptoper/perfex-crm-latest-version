<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('projects', '', 'edit');
$hasPermissionDelete = has_permission('projects', '', 'delete');
$hasPermissionCreate = has_permission('projects', '', 'create');

$aColumns = [
    db_prefix() . 'projects.id as id',
    'name',
    get_sql_select_client_company(),
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
    'start_date',
    'deadline',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id AND user_type=1 ORDER BY staff_id) as mounter_members',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id AND user_type=2 ORDER BY staff_id) as design_members',
    '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id AND user_type=3 ORDER BY staff_id) as tech_members',
    'status',
    'billing_type',
    ];


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects';

$join = [
    'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
];

$where  = [];

if ($this->ci->input->post('create_from_date')
    && $this->ci->input->post('create_from_date') != '') {
    array_push($where, ' AND project_created >= "' . implode('/',array_reverse(explode('/', $this->ci->input->post('create_from_date')))) . '"');
}

if ($this->ci->input->post('create_to_date')
    && $this->ci->input->post('create_to_date') != '') {
    array_push($where, ' AND project_created <= "'.implode('/',array_reverse(explode('/', $this->ci->input->post('create_to_date')))).'"');
}

if ($this->ci->input->post('finish_from_date')
    && $this->ci->input->post('finish_from_date') != '') {
    array_push($where, ' AND date_finished >= "' . implode('/',array_reverse(explode('/', $this->ci->input->post('finish_from_date')))) . '"');
}

if ($this->ci->input->post('finish_to_date')
    && $this->ci->input->post('finish_to_date') != '') {
    array_push($where, ' AND date_finished <= "'.implode('/',array_reverse(explode('/', $this->ci->input->post('finish_to_date')))).'"');
}

if ($this->ci->input->post('from_due_date')
    && $this->ci->input->post('from_due_date') != '') {
    array_push($where, ' AND deadline >= "'. implode('/',array_reverse(explode('/', $this->ci->input->post('from_due_date')))) .'"');
}

if ($this->ci->input->post('to_due_date')
    && $this->ci->input->post('to_due_date') != '') {
    array_push($where, ' AND deadline <= "'. implode('/',array_reverse(explode('/', $this->ci->input->post('to_due_date')))) .'"');
}

$project_ids = [];
$flag = 0;

if ($this->ci->input->post('project_type')
    && $this->ci->input->post('project_type') != '') {
        
    $query = $this->ci->db->select('GROUP_CONCAT(relid SEPARATOR ",") as ids')->where('fieldid', 1)->where('fieldto', 'projects')->where('value', implode(', ',$this->ci->input->post('project_type')))
        ->get(db_prefix() . 'customfieldsvalues')->row();
    $project_ids_by_project_type = $query->ids;
    
    if (!empty($project_ids_by_project_type))
        array_push($where, ' AND ' . db_prefix() . 'projects.id IN (' . $project_ids_by_project_type . ')');
    else
        array_push($where, ' AND ' . db_prefix() . 'projects.id IN (null)');
}

if ($this->ci->input->post('project_mounter')
    && $this->ci->input->post('project_mounter') != '') {
    if ($flag == 0) {
        $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_mounter'))
                        ->where('user_type', 1)->get(db_prefix() . 'project_res_members')->row();
        $project_ids = $query_result->ids;
      
        $flag = 1;
    }
    else
    {   $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_mounter'))
                        ->where('user_type', 1)->where_in('project_id', explode(', ', $project_ids))->get(db_prefix().'project_res_members')->row();
        $project_ids = $query_result->ids;
    }
}

if ($this->ci->input->post('project_technologist')
    && $this->ci->input->post('project_technologist') != '') {
    if ($flag == 0) {
        $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_technologist'))
                        ->where('user_type', 3)->get(db_prefix().'project_res_members')->row();
        $project_ids = $query_result->ids;
        $flag = 1;
    }
    else
    {
        $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_technologist'))
                        ->where('user_type', 3)->where_in('project_id', explode(', ', $project_ids))->get(db_prefix().'project_res_members')->row();
        $project_ids = $query_result->ids;
    }
    
}

if ($this->ci->input->post('project_designer')
    && $this->ci->input->post('project_designer') != '') {
    if ($flag == 0) {
        $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_designer'))
                        ->where('user_type', 2)->get(db_prefix().'project_res_members')->row();
        $project_ids = $query_result->ids;
        $flag = 1;
    }
    else
    {
        $query_result = $this->ci->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->ci->input->post('project_designer'))
                        ->where('user_type', 2)->where_in('project_id', explode(', ', $project_ids))->get(db_prefix().'project_res_members')->row();
        $project_ids = $query_result->ids;
    }
}


if ($flag == 1 && $project_ids != '') {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (' . $project_ids . ')');
}
else if ($flag == 1 && $project_ids == '')
{
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (null)');
}

$filter = [];

if ($clientid != '') {
    array_push($where, ' AND clientid=' . $this->ci->db->escape_str($clientid));
}

if (!has_permission('projects', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
}

$statusIds = [];

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR status IN (' . implode(', ', $statusIds) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$custom_fields = get_table_custom_fields('projects');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'projects.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members WHERE project_id=' . db_prefix() . 'projects.id AND user_type=1 ORDER BY staff_id) as mounter_ids',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members WHERE project_id=' . db_prefix() . 'projects.id AND user_type=2 ORDER BY staff_id) as designer_ids',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_res_members WHERE project_id=' . db_prefix() . 'projects.id AND user_type=3 ORDER BY staff_id) as technologist_ids',
]);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    
    $link = admin_url('projects/view/' . $aRow['id']);

    $row[] = '<a href="' . $link . '">' . $aRow['id'] . '</a>';

    $name = '<a href="' . $link . '">' . $aRow['name'] . '</a>';

    $name .= '<div class="row-options">';

    $name .= '<a href="' . $link . '">' . _l('view') . '</a>';

    if ($hasPermissionCreate && !$clientid) {
        $name .= ' | <a href="#" data-name="' . htmlspecialchars($aRow['name'], ENT_QUOTES) . '" onclick="copy_project(' . $aRow['id'] . ', this);return false;">' . _l('copy_project') . '</a>';
    }

    if ($hasPermissionEdit) {
        $name .= ' | <a href="' . admin_url('projects/project/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if ($hasPermissionDelete) {
        $name .= ' | <a href="' . admin_url('projects/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $name .= '</div>';

    $row[] = '<p id="dd">' . $name . '</p>';

    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

    // $row[] = render_tags($aRow['tags']);
    $custom_fields = get_custom_fields('projects');
    $value = get_custom_field_value($aRow['id'], 1, 'projects');
    
    $row[] = $value;

    $row[] = _d($aRow['start_date']);

    $row[] = _d($aRow['deadline']);
    
    $this->ci->db->select_sum('total');
    $this->ci->db->where('project_id', $aRow['id']);
    $data = $this->ci->db->get(db_prefix() . 'proposals')->row();
    
    $baseCurrency = get_base_currency();

    $row[] = app_format_money($data->total, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
    $q = $this->ci->db->query('
            SELECT SUM(CASE
                WHEN end_time is NULL THEN ' . time() . '-start_time
                ELSE end_time-start_time
                END) as total_logged_time
            FROM ' . db_prefix() . 'taskstimers
            WHERE task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id=' . $this->ci->db->escape_str($aRow['id']) . ')')
            ->row();
    
    $row[] = seconds_to_time_format($q->total_logged_time);
    
    $cost_bc = sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $aRow['id']], 'field' => 'amount']);
    $row[] = app_format_money($cost_bc, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
    
    $cost_hr = 0;
    if ($aRow['billing_type'] == 3)
    {
        $tasks = $this->ci->db->select(db_prefix() . 'tasks.hourly_rate, (CASE
        WHEN ' . db_prefix() . 'taskstimers.end_time is NULL THEN ' . time() . '-'. db_prefix() . 'taskstimers.start_time
        ELSE ' . db_prefix() . 'taskstimers.end_time-' . db_prefix() . 'taskstimers.start_time
        END) as logged_time')->join(db_prefix() . 'taskstimers', '' . db_prefix() . 'taskstimers.task_id = ' . db_prefix() . 'tasks.id', 'right')->where(db_prefix() . 'tasks.rel_id', $aRow['id'])->where(db_prefix() . 'tasks.rel_type', 'project')
        ->get(db_prefix() . 'tasks')->result_array();
    
        foreach ($tasks as $task)
        {
            $cost_hr += sec2qty($task['logged_time']) * $task['hourly_rate'];
        }
        $row[] = app_format_money($cost_hr, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
    }
    else
        $row[] = app_format_money($cost_hr, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));

    $row[] = app_format_money(($cost_hr + $cost_bc), ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
    $designMembersOutput = '';
    $members       = explode(',', $aRow['design_members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['designer_ids']);
            $member_id   = $members_ids[$key];
            $designMembersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $designMembersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row[] = $designMembersOutput;
  
    $techMembersOutput = '';

    $members       = explode(',', $aRow['tech_members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['technologist_ids']);
            $member_id   = $members_ids[$key];
            $techMembersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $techMembersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row[] = $techMembersOutput;

    $mounterMembersOutput = '';
    $members       = explode(',', $aRow['mounter_members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['mounter_ids']);
            $member_id   = $members_ids[$key];
            $mounterMembersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }
    $mounterMembersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row[] = $mounterMembersOutput;

    $status = get_project_status_by_id($aRow['status']);
    $row[]  = '<span class="label project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . adjust_hex_brightness($status['color'], 0.4) . ';background: ' . adjust_hex_brightness($status['color'], 0.04) . ';">' . $status['name'] . '</span>';
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}