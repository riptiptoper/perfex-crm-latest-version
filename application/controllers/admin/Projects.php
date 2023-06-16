<?php

use app\services\projects\Gantt;
use app\services\projects\AllProjectsGantt;
use app\services\projects\HoursOverviewChart;
use app\services\projects\ProjectsKanban;

defined('BASEPATH') or exit('No direct script access allowed');

class Projects extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('currencies_model');
        $this->load->model('tasks_model');
        $this->load->helper('date');
    }

    public function index()
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['title']    = _l('projects');
        $data['switch_kanban'] = false;
        $setting_values = $this->db->get(db_prefix() . 'settingprojectpercent')->row();
        
        $data['defaultPercentValue'] = $setting_values;
       
        usort($data['statuses'], function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        $data['project_statuses'] = $data['statuses'];
        
        if ($this->session->userdata('projects_kanban_view') == 'true') {
            $data['switch_kanban'] = true;
            $data['bodyclass']     = 'projects-page kan-ban-body';
        }
        $this->load->view('admin/projects/manage', $data);
    }

    public function setDefaultPercentValue()
    {
        $settings = $this->db->get(db_prefix() . 'settingprojectpercent')->result_array();
        if (count($settings) > 0)
            $result = $this->db->where('id', $settings[0]['id'])->update(db_prefix() . 'settingprojectpercent', $this->input->get());
        else
            $result = $this->db->insert(db_prefix() . 'settingprojectpercent', $this->input->get());
        echo $result;        
    }

    public function switch_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }

        $this->session->set_userdata([
            'projects_kanban_view' => $set,
        ]);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function kanban()
    {
        $data['project_statuses'] = hooks()->apply_filters('before_get_project_statuses', [
            [
                'id'             => 1,
                'color'          => '#475569',
                'name'           => _l('project_status_1'),
                'order'          => 1,
                'filter_default' => true,
            ],
            [
                'id'             => 2,
                'color'          => '#8da8db',
                'name'           => _l('project_status_2'),
                'order'          => 4,
                'filter_default' => true,
            ],
            [
                'id'             => 3,
                'color'          => '#fed866',
                'name'           => _l('project_status_3'),
                'order'          => 2,
                'filter_default' => true,
            ],
            [
                'id'             => 4,
                'color'          => '#92cf50',
                'name'           => _l('project_status_4'),
                'order'          => 100,
                'filter_default' => false,
            ],
            [
                'id'             => 5,
                'color'          => '#94a3b8',
                'name'           => _l('project_status_5'),
                'order'          => 9,
                'filter_default' => false,
            ],
            [
                'id'             => 6,
                'color'          => '#f7caad',
                'name'           => _l('project_status_6'),
                'order'          => 3,
                'filter_default' => false,
            ],
            [
                'id'             => 7,
                'color'          => '#c8c911',
                'name'           => _l('project_status_7'),
                'order'          => 7,
                'filter_default' => false,
            ],
            [
                'id'             => 8,
                'color'          => '#fe0000',
                'name'           => _l('project_status_8'),
                'order'          => 8,
                'filter_default' => false,
            ],
        ]);
        usort($data['project_statuses'], function ($a, $b) {
            return $a['order'] - $b['order'];
        });
        echo $this->load->view('admin/projects/kan_ban', $data, true);
    }

    public function projects_kanban_load_more()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $projects = (new ProjectsKanban($status))
        ->search($this->input->get('search'))
        ->sortBy(
            $this->input->get('sort_by'),
            $this->input->get('sort')
        )
        ->page($page)->get();
        
        foreach ($projects as $project) {
            $this->load->view('admin/projects/_kan_ban_card', [
                'project'   => $project,
                'status' => $status,
            ]);
        }
    }

    public function update_order()
    {
        $this->projects_model->update_order($this->input->post());
    }


    public function table($clientid = '')
    {
        $this->app->get_table_data('projects', [
            'clientid' => $clientid,
        ]);
    }

    public function getProjectSummaryData()
    {
        $hasPermissionEdit   = has_permission('projects', '', 'edit');
        $hasPermissionDelete = has_permission('projects', '', 'delete');
        $hasPermissionCreate = has_permission('projects', '', 'create');

        $aColumns = [
             db_prefix() . 'projects.id as id',
            'billing_type',
            ];


        $sIndexColumn = 'id';
        $sTable       = db_prefix() . 'projects';

        $join = [
            'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
        ];

        $where  = [];

        if ($this->input->post('create_from_date')
            && $this->input->post('create_from_date') != '') {
            array_push($where, ' AND project_created >= "' . implode('/',array_reverse(explode('/', $this->input->post('create_from_date')))) . '"');
        }

        if ($this->input->post('create_to_date')
            && $this->input->post('create_to_date') != '') {
            array_push($where, ' AND project_created <= "'.implode('/',array_reverse(explode('/', $this->input->post('create_to_date')))).'"');
        }

        if ($this->input->post('finish_from_date')
            && $this->input->post('finish_from_date') != '') {
            array_push($where, ' AND date_finished >= "' . implode('/',array_reverse(explode('/', $this->input->post('finish_from_date')))) . '"');
        }

        if ($this->input->post('finish_to_date')
            && $this->input->post('finish_to_date') != '') {
            array_push($where, ' AND date_finished <= "'.implode('/',array_reverse(explode('/', $this->input->post('finish_to_date')))).'"');
        }

        if ($this->input->post('from_due_date')
            && $this->input->post('from_due_date') != '') {
            array_push($where, ' AND deadline >= "'. implode('/',array_reverse(explode('/', $this->input->post('from_due_date')))) .'"');
        }

        if ($this->input->post('to_due_date')
            && $this->input->post('to_due_date') != '') {
            array_push($where, ' AND deadline <= "'. implode('/',array_reverse(explode('/', $this->input->post('to_due_date')))) .'"');
        }

        if ($this->input->post('project_type')
            && $this->input->post('project_type') != '') {
                
            $query = $this->db->select('GROUP_CONCAT(relid SEPARATOR ",") as ids')->where('fieldid', 1)->where('fieldto', 'projects')->where('value', implode(', ',$this->input->post('project_type')))
                ->get(db_prefix() . 'customfieldsvalues')->row();
            $project_ids_by_project_type = $query->ids;
            
            if (!empty($project_ids_by_project_type))
                array_push($where, ' AND ' . db_prefix() . 'projects.id IN (' . $project_ids_by_project_type . ')');
            else
                array_push($where, ' AND ' . db_prefix() . 'projects.id IN (null)');
        }

        $project_ids = [];
        $flag = 0;

        if ($this->input->post('project_mounter')
            && $this->input->post('project_mounter') != '') {
            if ($flag == 0) {
                $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_mounter'))
                                ->where('user_type', 1)->get(db_prefix() . 'project_res_members')->row();
                $project_ids = $query_result->ids;
            
                $flag = 1;
            }
            else
            {   $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_mounter'))
                                ->where('user_type', 1)->where_in('project_id', explode(', ', $project_ids))->get(db_prefix().'project_res_members')->row();
                $project_ids = $query_result->ids;
            }
        }

        if ($this->input->post('project_technologist')
            && $this->input->post('project_technologist') != '') {
            if ($flag == 0) {
                $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_technologist'))
                                ->where('user_type', 3)->get(db_prefix().'project_res_members')->row();
                $project_ids = $query_result->ids;
                $flag = 1;
            }
            else
            {
                $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_technologist'))
                                ->where('user_type', 3)->where_in('project_id', explode(', ', $project_ids))->get(db_prefix().'project_res_members')->row();
                $project_ids = $query_result->ids;
            }
            
        }

        if ($this->input->post('project_designer')
            && $this->input->post('project_designer') != '') {
            if ($flag == 0) {
                $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_designer'))
                                ->where('user_type', 2)->get(db_prefix().'project_res_members')->row();
                $project_ids = $query_result->ids;
                $flag = 1;
            }
            else
            {
                $query_result = $this->db->select('GROUP_CONCAT(project_id SEPARATOR ",") as ids')->where('staff_id', $this->input->post('project_designer'))
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

        if (!has_permission('projects', '', 'view') || $this->input->post('my_projects')) {
            array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }

        $statusIds = [];

        foreach ($this->projects_model->get_project_statuses() as $status) {
            if ($this->input->post('project_status_' . $status['id'])) {
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
            @$this->db->query('SET SQL_BIG_SELECTS=1');
        }

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
        $output  = $result['output'];
        $rResult = $result['rResult'];

        $data['total_index'] = $output['iTotalRecords'];
       
        $total_price = $total_cost_bc = $total_cost_hr = $total_cost = 0;
        foreach ($rResult as $aRow)
        {
            
            $this->db->select_sum('total');
            $this->db->where('project_id', $aRow['id']);
            $price = $this->db->get(db_prefix() . 'proposals')->row();
            $total_price += $price->total;
           
            $total_cost_bc += sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $aRow['id']], 'field' => 'amount']);
            $cost_hr = 0;

            if ($aRow['billing_type'] == 3)
            {
                $tasks = $this->db->select(db_prefix() . 'tasks.hourly_rate, (CASE
                WHEN ' . db_prefix() . 'taskstimers.end_time is NULL THEN ' . time() . '-'. db_prefix() . 'taskstimers.start_time
                ELSE ' . db_prefix() . 'taskstimers.end_time-' . db_prefix() . 'taskstimers.start_time
                END) as logged_time')->join(db_prefix() . 'taskstimers', '' . db_prefix() . 'taskstimers.task_id = ' . db_prefix() . 'tasks.id', 'right')->where(db_prefix() . 'tasks.rel_id', $aRow['id'])->where(db_prefix() . 'tasks.rel_type', 'project')
                ->get(db_prefix() . 'tasks')->result_array();
            
                foreach ($tasks as $task)
                {
                    $cost_hr += sec2qty($task['logged_time']) * $task['hourly_rate'];
                }
            }
            $total_cost_hr += $cost_hr;
        }

        $total_cost = $total_cost_bc + $total_cost_hr;
        $total_profit = $total_price - $total_cost;
        
        $data['total_price'] = app_format_money($total_price, get_base_currency());
        $data['total_cost_bc'] = app_format_money($total_cost_bc, get_base_currency());
        $data['total_cost_hr'] = app_format_money($total_cost_hr, get_base_currency());
        $data['total_cost'] = app_format_money($total_cost, get_base_currency());
        $data['total_profit'] = app_format_money($total_profit, get_base_currency());

        echo json_encode($data);
    }

    public function staff_projects()
    {
        $this->app->get_table_data('staff_projects');
    }

    public function expenses($id)
    {
        $this->load->model('expenses_model');
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $this->app->get_table_data('project_expenses', [
            'project_id' => $id,
            'data'       => $data,
        ]);
    }

    public function add_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $id = $this->expenses_model->add($this->input->post());
            if ($id) {
                set_alert('success', _l('added_successfully', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses'),
                    'expenseid' => $id,
                ]);
                die;
            }
            echo json_encode([
                'url' => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses'),
            ]);
            die;
        }
    }

    public function project($id = '')
    {
        if (!staff_can('edit', 'projects') && !staff_can('create', 'projects')) {
            access_denied('Projects');
        }

        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['description'] = html_purify($this->input->post('description', false));
            if ($id == '') {
                if (!staff_can('create', 'projects')) {
                    access_denied('Projects');
                }
                $id = $this->projects_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project')));
                    redirect(admin_url('projects/view/' . $id));
                }
            } else {
                if (!staff_can('edit', 'projects')) {
                    access_denied('Projects');
                }
                $success = $this->projects_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project')));
                }
                redirect(admin_url('projects/view/' . $id));
            }
        }
        if ($id == '') {
            $title                            = _l('add_new', _l('project_lowercase'));
            $data['auto_select_billing_type'] = $this->projects_model->get_most_used_billing_type();

            if ($this->input->get('via_estimate_id')) {
                $this->load->model('estimates_model');
                $data['estimate'] = $this->estimates_model->get($this->input->get('via_estimate_id'));
            }
        } else {
            $data['project']                               = $this->projects_model->get($id);
            $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);
            
            $data['project_members'] = $this->projects_model->get_project_members($id);
            $data['project_mounter_members'] = $this->projects_model->get_project_custom_members($id,1);
            $data['project_design_members'] = $this->projects_model->get_project_custom_members($id,2);
            $data['project_tech_members'] = $this->projects_model->get_project_custom_members($id,3);
            $title                   = _l('edit', _l('project'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['last_project_settings'] = $this->projects_model->get_last_project_settings();

        if (count($data['last_project_settings'])) {
            $key                                          = array_search('available_features', array_column($data['last_project_settings'], 'name'));
            $data['last_project_settings'][$key]['value'] = unserialize($data['last_project_settings'][$key]['value']);
        }

        $data['settings'] = $this->projects_model->get_settings();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['staff']    = $this->staff_model->get('', ['active' => 1]);

        $data['title'] = $title;
        $this->load->view('admin/projects/project', $data);
    }

    public function getTaskStatus()
    {
        $id = $this->input->get('id');
        $status = $this->projects_model->get_task_status($id);
        echo $status;
    }

    public function gantt()
    {
        $data['title'] = _l('project_gant');

        $selected_statuses = [];
        $selectedMember    = null;
        $data['statuses']  = $this->projects_model->get_project_statuses();

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');

        $allStatusesIds = [];
        foreach ($data['statuses'] as $status) {
            if (
                !isset($status['filter_default'])
                || (isset($status['filter_default']) && $status['filter_default'])
                && !$appliedStatuses
            ) {
                $selected_statuses[] = $status['id'];
            } elseif ($appliedStatuses) {
                if (in_array($status['id'], $appliedStatuses)) {
                    $selected_statuses[] = $status['id'];
                }
            } else {
                // All statuses
                $allStatusesIds[] = $status['id'];
            }
        }

        if (count($selected_statuses) == 0) {
            $selected_statuses = $allStatusesIds;
        }


        $data['selected_statuses'] = $selected_statuses;

        if (staff_can('view', 'projects')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            $data['project_members'] = $this->projects_model->get_distinct_projects_members();
        }

        $data['gantt_data'] = (new AllProjectsGantt([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]))->get();

        $this->load->view('admin/projects/gantt', $data);
    }

    public function changeDefaultPercentValue()
    {
        $data = $this->input->get();
        $project = $this->db->where('id', $this->input->get('project_id'));
        unset($data['project_id']);
        echo ($project->update(db_prefix() . 'projects', $data));
    }

    public function view($id)
    {
        if (staff_can('view', 'projects') || $this->projects_model->is_member($id)) {
            close_setup_menu();
            $project = $this->projects_model->get($id);

            if (!$project) {
                blank_page(_l('project_not_found'));
            }

            $project->settings->available_features = unserialize($project->settings->available_features);
            $data['statuses']                      = $this->projects_model->get_project_statuses();

            $group = !$this->input->get('group') ? 'project_overview' : $this->input->get('group');

            // Unable to load the requested file: admin/projects/project_tasks#.php - FIX
            if (strpos($group, '#') !== false) {
                $group = str_replace('#', '', $group);
            }

            $data['tabs'] = get_project_tabs_admin();
            $data['tab']  = $this->app_tabs->filter_tab($data['tabs'], $group);

            if (!$data['tab']) {
                show_404();
            }

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            $data['project']  = $project;
            $data['currency'] = $this->projects_model->get_currency($id);

            $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);

            $data['staff']   = $this->staff_model->get('', ['active' => 1]);
            $percent         = $this->projects_model->calc_progress($id);
            $data['members'] = $this->projects_model->get_project_members($id);
            foreach ($data['members'] as $key => $member) {
                $data['members'][$key]['total_logged_time'] = 0;
                $member_timesheets                          = $this->tasks_model->get_unique_member_logged_task_ids($member['staff_id'], ' AND task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $this->db->escape_str($id) . '")');

                foreach ($member_timesheets as $member_task) {
                    $data['members'][$key]['total_logged_time'] += $this->tasks_model->calc_task_total_time($member_task->task_id, ' AND staff_id=' . $member['staff_id']);
                }
            }
            $data['mounter_members'] = $this->projects_model->get_project_custom_members($id,1);
            $data['design_members'] = $this->projects_model->get_project_custom_members($id,2);
            $data['tech_members'] = $this->projects_model->get_project_custom_members($id,3);

            $data['bodyclass'] = '';

            $this->app_scripts->add(
                'projects-js',
                base_url($this->app_scripts->core_file('assets/js', 'projects.js')) . '?v=' . $this->app_scripts->core_version(),
                'admin',
                ['app-js', 'jquery-comments-js', 'frappe-gantt-js', 'circle-progress-js']
            );

            if ($group == 'project_overview') {
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                        $data['project_time_left_percent'] = round($data['project_time_left_percent'], 2);
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }

                $__total_where_tasks = 'rel_type = "project" AND rel_id=' . $this->db->escape_str($id);
                if (!staff_can('view', 'tasks')) {
                    $__total_where_tasks .= ' AND ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';

                    if (get_option('show_all_tasks_for_project_member') == 1) {
                        $__total_where_tasks .= ' AND (rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . '))';
                    }
                }

                $__total_where_tasks = hooks()->apply_filters('admin_total_project_tasks_where', $__total_where_tasks, $id);

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;

                $data['tasks_not_completed'] = total_rows(db_prefix() . 'tasks', $where);
                $total_tasks                 = total_rows(db_prefix() . 'tasks', $__total_where_tasks);
                $data['total_tasks']         = $total_tasks;

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = ' . Tasks_model::STATUS_COMPLETE . ' AND rel_type="project" AND rel_id="' . $id . '"';

                $data['tasks_completed'] = total_rows(db_prefix() . 'tasks', $where);

                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);
                $data['tasks_not_completed_progress'] = round($data['tasks_not_completed_progress'], 2);

                @$percent_circle        = $percent / 100;
                $data['percent_circle'] = $percent_circle;

                $data['project_overview_chart'] = (new HoursOverviewChart(
                    $id,
                    ($this->input->get('overview_chart') ? $this->input->get('overview_chart') : 'this_week')
                ))->get();
            } elseif ($group == 'project_invoices') {
                $this->load->model('invoices_model');

                $data['invoiceid']   = '';
                $data['status']      = '';
                $data['custom_view'] = '';

                $data['invoices_years']       = $this->invoices_model->get_invoices_years();
                $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
                $data['invoices_statuses']    = $this->invoices_model->get_statuses();
            } elseif ($group == 'project_gantt') {
                $gantt_type         = (!$this->input->get('gantt_type') ? 'milestones' : $this->input->get('gantt_type'));
                $taskStatus         = (!$this->input->get('gantt_task_status') ? null : $this->input->get('gantt_task_status'));
                $data['gantt_data'] = (new Gantt($id, $gantt_type))->forTaskStatus($taskStatus)->get();
            } elseif ($group == 'project_milestones') {
                $data['bodyclass'] .= 'project-milestones ';
                $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed') && $this->input->get('exclude_completed') == 'yes' || !$this->input->get('exclude_completed');

                $data['total_milestones'] = total_rows(db_prefix() . 'milestones', ['project_id' => $id]);
                $data['milestones_found'] = $data['total_milestones'] > 0 || (!$data['total_milestones'] && total_rows(db_prefix() . 'tasks', ['rel_id' => $id, 'rel_type' => 'project', 'milestone' => 0]) > 0);
            } elseif ($group == 'project_files') {
                $data['files'] = $this->projects_model->get_files($id);
            } elseif ($group == 'project_expenses') {
                $this->load->model('taxes_model');
                $this->load->model('expenses_model');
                $data['taxes']              = $this->taxes_model->get();
                $data['expense_categories'] = $this->expenses_model->get_category();
                $data['currencies']         = $this->currencies_model->get();
            } elseif ($group == 'project_activity') {
                $data['activity'] = $this->projects_model->get_activity($id);
            } elseif ($group == 'project_notes') {
                $data['staff_notes'] = $this->projects_model->get_staff_notes($id);
            } elseif ($group == 'project_contracts') {
                $this->load->model('contracts_model');
                $data['contract_types'] = $this->contracts_model->get_contract_types();
                $data['years']          = $this->contracts_model->get_contracts_years();
            } elseif ($group == 'project_estimates') {
                $this->load->model('estimates_model');
                $data['estimates_years']       = $this->estimates_model->get_estimates_years();
                $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();
                $data['estimate_statuses']     = $this->estimates_model->get_statuses();
                $data['estimateid']            = '';
                $data['switch_pipeline']       = '';
            } elseif ($group == 'project_proposals') {
                $this->load->model('proposals_model');
                $data['proposal_statuses']     = $this->proposals_model->get_statuses();
                $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
                $data['years']                 = $this->proposals_model->get_proposals_years();
                $data['proposal_id']           = '';
                $data['switch_pipeline']       = '';
            } elseif ($group == 'project_tickets') {
                $data['chosen_ticket_status'] = '';
                $this->load->model('tickets_model');
                $data['ticket_assignees'] = $this->tickets_model->get_tickets_assignes_disctinct();

                $this->load->model('departments_model');
                $data['staff_deparments_ids']          = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $data['default_tickets_list_statuses'] = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);
            } elseif ($group == 'project_timesheets') {
                // Tasks are used in the timesheet dropdown
                // Completed tasks are excluded from this list because you can't add timesheet on completed task.
                $data['tasks']                = $this->projects_model->get_tasks($id, 'status != ' . Tasks_model::STATUS_COMPLETE . ' AND billed=0');
                $data['timesheets_staff_ids'] = $this->projects_model->get_distinct_tasks_timesheets_staff($id);
            }

            // Discussions
            if ($this->input->get('discussion_id')) {
                $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
                $data['discussion']                        = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                $data['current_user_is_admin']             = is_admin();
            }

            $data['percent'] = $percent;

            $this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

            $other_projects       = [];
            $other_projects_where = 'id != ' . $id;

            $statuses = $this->projects_model->get_project_statuses();

            $other_projects_where .= ' AND (';
            foreach ($statuses as $status) {
                if (isset($status['filter_default']) && $status['filter_default']) {
                    $other_projects_where .= 'status = ' . $status['id'] . ' OR ';
                }
            }

            $other_projects_where = rtrim($other_projects_where, ' OR ');

            $other_projects_where .= ')';

            if (!staff_can('view', 'projects')) {
                $other_projects_where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }

            $data['other_projects'] = $this->projects_model->get('', $other_projects_where);
            $data['title']          = $data['project']->name;
            $data['bodyclass'] .= 'project invoices-total-manual estimates-total-manual';
            $data['project_status'] = get_project_status_by_id($project->status);
            $this->db->select_sum('total');
            $this->db->where('project_id', $project->id);
            $price = $this->db->get(db_prefix() . 'proposals')->row();
            $data['total_price'] = $price->total;

            $_milestones = $this->projects_model->get_milestones($project->id);

            $val = $this->db->where('milestone', 0)->where('rel_id', $project->id)->where('rel_type', 'project')->get(db_prefix() . 'tasks')->result_array();
            
            foreach ($_milestones as $milestone)
            {
                if ($milestone["name"] == "Proiectare" || $milestone["name"] == "PROIECT")
                    $data['proiectare'] = $milestone["total_billable_amount"];
                else if ($milestone["name"] == "Productie ")
                    $data['productie'] = $milestone["total_billable_amount"];
                else if ($milestone["name"] == "Montaj teren")
                    $data['montaj'] = $milestone["total_billable_amount"];
                else if ($milestone["name"] == "Livrare")
                    $data['livrare'] = $milestone["total_billable_amount"];
                else if ($milestone["name"] == "Fara categorie")
                    $data['fara'] = $total_billable_amount;
            }

            $fara_total_amount = 0;
            
            foreach ($val as $item)
            {
                if ($item['rel_type'] == 'project') {
                    $this->db->select('billing_type,project_rate_per_hour,name,project_cost');
                    $this->db->where('id', $item['rel_id']);
                    $project      = $this->db->get(db_prefix() . 'projects')->row();
                    
                    $billing_type = get_project_billing_type($item['rel_id']);

                    if ($project->billing_type == 2) {
                        $item['hourly_rate'] = $project->project_rate_per_hour;
                    }

                    $item['name'] = $project->name . ' - ' . $item['name'];
                }
                
                $total_seconds       = task_timer_round($this->calc_task_total_time($item['id']));
                $item->total_hours   = sec2qty($total_seconds);
                $item->total_seconds = $total_seconds;
               
                if ($project->billing_type == 1)
                {
                    $fara_total_amount += $project->project_cost;
                }
                else
                    $fara_total_amount += $item['total_hours'] * $item['hourly_rate'];
            }
            // var_dump($fara_total_amount);
            // die();
            if ($fara_total_amount > 0)
                $data['fara'] = $fara_total_amount;
            
            $this->load->view('admin/projects/view', $data);
        } else {
            access_denied('Project View');
        }
    }

    public function calc_task_total_time($task_id, $where = '')
    {
        $sql = get_sql_calc_task_logged_time($task_id) . $where;

        $result = $this->db->query($sql)->row();

        if ($result) {
            return $result->total_logged_time;
        }

        return 0;
    }


    public function mark_as()
    {
        $success = false;
        $message = '';
        if ($this->input->is_ajax_request()) {
            if (staff_can('create', 'projects') || staff_can('edit', 'projects')) {
                $status = get_project_status_by_id($this->input->post('status_id'));

                $message = _l('project_marked_as_failed', $status['name']);
                $success = $this->projects_model->mark_as($this->input->post());

                if ($success) {
                    $message = _l('project_marked_as_success', $status['name']);
                }
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function file($id, $project_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();

        $data['file'] = $this->projects_model->get_file($id, $project_id);

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        $this->load->view('admin/projects/_file', $data);
    }

    public function update_file_data()
    {
        if ($this->input->post()) {
            $this->projects_model->update_file_data($this->input->post());
        }
    }

    public function add_external_file()
    {
        if ($this->input->post()) {
            $data                        = [];
            $data['project_id']          = $this->input->post('project_id');
            $data['files']               = $this->input->post('files');
            $data['external']            = $this->input->post('external');
            $data['visible_to_customer'] = ($this->input->post('visible_to_customer') == 'true' ? 1 : 0);
            $data['staffid']             = get_staff_user_id();
            $this->projects_model->add_external_file($data);
        }
    }

    public function download_all_files($id)
    {
        if ($this->projects_model->is_member($id) || staff_can('view', 'projects')) {
            $files = $this->projects_model->get_files($id);
            if (count($files) == 0) {
                set_alert('warning', _l('no_files_found'));
                redirect(admin_url('projects/view/' . $id . '?group=project_files'));
            }
            $path = get_upload_path_by_type('project') . $id;
            $this->load->library('zip');
            foreach ($files as $file) {
                if ($file['original_file_name'] != '') {
                    $this->zip->read_file($path . '/' . $file['file_name'], $file['original_file_name']);
                } else {
                    $this->zip->read_file($path . '/' . $file['file_name']);
                }
            }
            $this->zip->download(slug_it(get_project_name_by_id($id)) . '-files.zip');
            $this->zip->clear_data();
        }
    }

    public function export_project_data($id)
    {
        if (staff_can('create', 'projects')) {
            app_pdf('project-data', LIBSPATH . 'pdf/Project_data_pdf', $id);
        }
    }

    public function update_task_milestone()
    {
        if ($this->input->post()) {
            $this->projects_model->update_task_milestone($this->input->post());
        }
    }

    public function update_milestones_order()
    {
        if ($post_data = $this->input->post()) {
            $this->projects_model->update_milestones_order($post_data);
        }
    }

    public function pin_action($project_id)
    {
        $this->projects_model->pin_action($project_id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function add_edit_members($project_id)
    {
        if (staff_can('edit', 'projects')) {
            $this->projects_model->add_edit_members($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function discussions($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('project_discussions', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function discussion($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_discussion($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->edit_discussion($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
            die;
        }
    }

    public function get_discussion_comments($id, $type)
    {
        echo json_encode($this->projects_model->get_discussion_comments($id, $type));
    }

    public function add_discussion_comment($discussion_id, $type)
    {
        echo json_encode($this->projects_model->add_discussion_comment(
            $this->input->post(null, false),
            $discussion_id,
            $type
        ));
    }

    public function update_discussion_comment()
    {
        echo json_encode($this->projects_model->update_discussion_comment($this->input->post(null, false)));
    }

    public function delete_discussion_comment($id)
    {
        echo json_encode($this->projects_model->delete_discussion_comment($id));
    }

    public function delete_discussion($id)
    {
        $success = false;
        if (staff_can('delete', 'projects')) {
            $success = $this->projects_model->delete_discussion($id);
        }
        $alert_type = 'warning';
        $message    = _l('project_discussion_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('project_discussion_deleted');
        }
        echo json_encode([
            'alert_type' => $alert_type,
            'message'    => $message,
        ]);
    }

    public function change_milestone_color()
    {
        if ($this->input->post()) {
            $this->projects_model->update_milestone_color($this->input->post());
        }
    }

    public function upload_file($project_id)
    {
        handle_project_file_uploads($project_id);
    }

    public function change_file_visibility($id, $visible)
    {
        if ($this->input->is_ajax_request()) {
            $this->projects_model->change_file_visibility($id, $visible);
        }
    }

    public function change_activity_visibility($id, $visible)
    {
        if (staff_can('create', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->projects_model->change_activity_visibility($id, $visible);
            }
        }
    }

    public function remove_file($project_id, $id)
    {
        $this->projects_model->remove_file($id);
        redirect(admin_url('projects/view/' . $project_id . '?group=project_files'));
    }

    public function milestones_kanban()
    {
        $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $data['project_id'] = $this->input->get('project_id');
        $data['milestones'] = [];

        $data['milestones'][] = [
            'name'              => _l('milestones_uncategorized'),
            'id'                => 0,
            'total_logged_time' => $this->projects_model->calc_milestone_logged_time($data['project_id'], 0),
            'color'             => null,
        ];

        $_milestones = $this->projects_model->get_milestones($data['project_id']);
        
        foreach ($_milestones as $m) {
            $data['milestones'][] = $m;
        }
        
        echo $this->load->view('admin/projects/milestones_kan_ban', $data, true);
    }

    public function milestones_kanban_load_more()
    {
        $milestones_exclude_completed_tasks = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $status     = $this->input->get('status');
        $page       = $this->input->get('page');
        $project_id = $this->input->get('project_id');
        $where      = [];
        if ($milestones_exclude_completed_tasks) {
            $where['status !='] = Tasks_model::STATUS_COMPLETE;
        }
        $tasks = $this->projects_model->do_milestones_kanban_query($status, $project_id, $page, $where);
        foreach ($tasks as $task) {
            $this->load->view('admin/projects/_milestone_kanban_card', ['task' => $task, 'milestone' => $status]);
        }
    }

    public function milestones($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('milestones', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function milestone($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                if (!staff_can('create_milestones', 'projects')) {
                    access_denied();
                }

                $id = $this->projects_model->add_milestone($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_milestone')));
                }
            } else {
                if (!staff_can('edit_milestones', 'projects')) {
                    access_denied();
                }

                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->update_milestone($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project_milestone')));
                }
            }
        }

        redirect(admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_milestones'));
    }

    public function delete_milestone($project_id, $id)
    {
        if (staff_can('delete_milestones', 'projects')) {
            if ($this->projects_model->delete_milestone($id)) {
                set_alert('deleted', 'project_milestone');
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=project_milestones'));
    }

    public function bulk_action_files()
    {
        hooks()->do_action('before_do_bulk_action_for_project_files');
        $total_deleted       = 0;
        $hasPermissionDelete = staff_can('delete', 'projects');
        // bulk action for projects currently only have delete button
        if ($this->input->post()) {
            $fVisibility = $this->input->post('visible_to_customer') == 'true' ? 1 : 0;
            $ids         = $this->input->post('ids');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($hasPermissionDelete && $this->input->post('mass_delete') && $this->projects_model->remove_file($id)) {
                        $total_deleted++;
                    } else {
                        $this->projects_model->change_file_visibility($id, $fVisibility);
                    }
                }
            }
        }
        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_files_deleted', $total_deleted));
        }
    }

    public function timesheets($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('timesheets', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function timesheet()
    {
        if ($this->input->post()) {
            if (
                $this->input->post('timer_id') &&
                !(staff_can('edit_timesheet', 'tasks') || (staff_can('edit_own_timesheet', 'tasks') && total_rows(db_prefix() . 'taskstimers', ['staff_id' => get_staff_user_id(), 'id' => $this->input->post('timer_id')]) > 0))
            ) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('access_denied'),
                ]);
                die;
            }
            $message = '';
            $success = false;
            $success = $this->tasks_model->timesheet($this->input->post());
            if ($success === true) {
                $langKey = $this->input->post('timer_id') ? 'updated_successfully' : 'added_successfully';
                $message = _l($langKey, _l('project_timesheet'));
            } elseif (is_array($success) && isset($success['end_time_smaller'])) {
                $message = _l('failed_to_add_project_timesheet_end_time_smaller');
            } else {
                $message = _l('project_timesheet_not_updated');
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die;
        }
    }

    public function timesheet_task_assignees($task_id, $project_id, $staff_id = 'undefined')
    {
        $assignees             = $this->tasks_model->get_task_assignees($task_id);
        $data                  = '';
        $has_permission_edit   = staff_can('edit', 'projects');
        $has_permission_create = staff_can('edit', 'projects');
        // The second condition if staff member edit their own timesheet
        if ($staff_id == 'undefined' || $staff_id != 'undefined' && (!$has_permission_edit || !$has_permission_create)) {
            $staff_id     = get_staff_user_id();
            $current_user = true;
        }
        foreach ($assignees as $staff) {
            $selected = '';
            // maybe is admin and not project member
            if ($staff['assigneeid'] == $staff_id && $this->projects_model->is_member($project_id, $staff_id)) {
                $selected = ' selected';
            }
            if ((!$has_permission_edit || !$has_permission_create) && isset($current_user)) {
                if ($staff['assigneeid'] != $staff_id) {
                    continue;
                }
            }
            $data .= '<option value="' . $staff['assigneeid'] . '"' . $selected . '>' . get_staff_full_name($staff['assigneeid']) . '</option>';
        }
        echo $data;
    }

    public function remove_team_member($project_id, $staff_id)
    {
        if (staff_can('edit', 'projects')) {
            if ($this->projects_model->remove_team_member($project_id, $staff_id)) {
                set_alert('success', _l('project_member_removed'));
            }
        }

        redirect(admin_url('projects/view/' . $project_id));
    }

    public function save_note($project_id)
    {
        if ($this->input->post()) {
            $success = $this->projects_model->save_note($this->input->post(null, false), $project_id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_note')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
        }
    }

    public function delete($project_id)
    {
        if (staff_can('delete', 'projects')) {
            $project = $this->projects_model->get($project_id);
            $success = $this->projects_model->delete($project_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('project')));
                if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    redirect(admin_url('projects'));
                }
            } else {
                set_alert('warning', _l('problem_deleting', _l('project_lowercase')));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function copy($project_id)
    {
        if (staff_can('create', 'projects')) {
            $id = $this->projects_model->copy($project_id, $this->input->post());
            if ($id) {
                set_alert('success', _l('project_copied_successfully'));
                redirect(admin_url('projects/view/' . $id));
            } else {
                set_alert('danger', _l('failed_to_copy_project'));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function mass_stop_timers($project_id, $billable = 'false')
    {
        if (staff_can('create', 'invoices')) {
            $where = [
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ];
            if ($billable == 'true') {
                $where['billable'] = true;
            }
            $tasks                = $this->projects_model->get_tasks($project_id, $where);
            $total_timers_stopped = 0;
            foreach ($tasks as $task) {
                $this->db->where('task_id', $task['id']);
                $this->db->where('end_time IS NULL');
                $this->db->update(db_prefix() . 'taskstimers', [
                    'end_time' => time(),
                ]);
                $total_timers_stopped += $this->db->affected_rows();
            }
            $message = _l('project_tasks_total_timers_stopped', $total_timers_stopped);
            $type    = 'success';
            if ($total_timers_stopped == 0) {
                $type = 'warning';
            }
            echo json_encode([
                'type'    => $type,
                'message' => $message,
            ]);
        }
    }

    public function get_pre_invoice_project_info($project_id)
    {
        if (staff_can('create', 'invoices')) {
            $data['billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'     => 1,
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ]);

            $data['not_billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'    => 1,
                'billed'      => 0,
                'startdate >' => date('Y-m-d'),
            ]);

            $data['project_id']   = $project_id;
            $data['billing_type'] = get_project_billing_type($project_id);

            $this->load->model('expenses_model');
            $this->db->where('invoiceid IS NULL');
            $data['expenses'] = $this->expenses_model->get('', [
                'project_id' => $project_id,
                'billable'   => 1,
            ]);

            $this->load->view('admin/projects/project_pre_invoice_settings', $data);
        }
    }

    public function get_invoice_project_data()
    {
        if (staff_can('create', 'invoices')) {
            $type       = $this->input->post('type');
            $project_id = $this->input->post('project_id');
            // Check for all cases
            if ($type == '') {
                $type == 'single_line';
            }
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [
                'expenses_only !=' => 1,
            ]);
            $this->load->model('taxes_model');
            $data['taxes']         = $this->taxes_model->get();
            $data['currencies']    = $this->currencies_model->get();
            $data['base_currency'] = $this->currencies_model->get_base_currency();
            $this->load->model('invoice_items_model');

            $data['ajaxItems'] = false;
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $data['items'] = $this->invoice_items_model->get_grouped();
            } else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }

            $data['items_groups'] = $this->invoice_items_model->get_groups();
            $data['staff']        = $this->staff_model->get('', ['active' => 1]);
            $project              = $this->projects_model->get($project_id);
            $data['project']      = $project;
            $items                = [];

            $project    = $this->projects_model->get($project_id);
            $item['id'] = 0;

            $default_tax     = unserialize(get_option('default_tax'));
            $item['taxname'] = $default_tax;

            $tasks = $this->input->post('tasks');
            if ($tasks) {
                $item['long_description'] = '';
                $item['qty']              = 0;
                $item['task_id']          = [];
                if ($type == 'single_line') {
                    $item['description'] = $project->name;
                    foreach ($tasks as $task_id) {
                        $task = $this->tasks_model->get($task_id);
                        $sec  = $this->tasks_model->calc_task_total_time($task_id);
                        $item['long_description'] .= $task->name . ' - ' . seconds_to_time_format(task_timer_round($sec)) . ' ' . _l('hours') . "\r\n";
                        $item['task_id'][] = $task_id;
                        if ($project->billing_type == 2) {
                            if ($sec < 60) {
                                $sec = 0;
                            }
                            $item['qty'] += sec2qty(task_timer_round($sec));
                        }
                    }
                    if ($project->billing_type == 1) {
                        $item['qty']  = 1;
                        $item['rate'] = $project->project_cost;
                    } elseif ($project->billing_type == 2) {
                        $item['rate'] = $project->project_rate_per_hour;
                    }
                    $item['unit'] = '';
                    $items[]      = $item;
                } elseif ($type == 'task_per_item') {
                    foreach ($tasks as $task_id) {
                        $task                     = $this->tasks_model->get($task_id);
                        $sec                      = $this->tasks_model->calc_task_total_time($task_id);
                        $item['description']      = $project->name . ' - ' . $task->name;
                        $item['qty']              = floatVal(sec2qty(task_timer_round($sec)));
                        $item['long_description'] = seconds_to_time_format(task_timer_round($sec)) . ' ' . _l('hours');
                        if ($project->billing_type == 2) {
                            $item['rate'] = $project->project_rate_per_hour;
                        } elseif ($project->billing_type == 3) {
                            $item['rate'] = $task->hourly_rate;
                        }
                        $item['task_id'] = $task_id;
                        $item['unit']    = '';
                        $items[]         = $item;
                    }
                } elseif ($type == 'timesheets_individualy') {
                    $timesheets     = $this->projects_model->get_timesheets($project_id, $tasks);
                    $added_task_ids = [];
                    foreach ($timesheets as $timesheet) {
                        if ($timesheet['task_data']->billed == 0 && $timesheet['task_data']->billable == 1) {
                            $item['description'] = $project->name . ' - ' . $timesheet['task_data']->name;
                            if (!in_array($timesheet['task_id'], $added_task_ids)) {
                                $item['task_id'] = $timesheet['task_id'];
                            }

                            array_push($added_task_ids, $timesheet['task_id']);

                            $item['qty']              = floatVal(sec2qty(task_timer_round($timesheet['total_spent'])));
                            $item['long_description'] = _l('project_invoice_timesheet_start_time', _dt($timesheet['start_time'], true)) . "\r\n" . _l('project_invoice_timesheet_end_time', _dt($timesheet['end_time'], true)) . "\r\n" . _l('project_invoice_timesheet_total_logged_time', seconds_to_time_format(task_timer_round($timesheet['total_spent']))) . ' ' . _l('hours');

                            if ($this->input->post('timesheets_include_notes') && $timesheet['note']) {
                                $item['long_description'] .= "\r\n\r\n" . _l('note') . ': ' . $timesheet['note'];
                            }

                            if ($project->billing_type == 2) {
                                $item['rate'] = $project->project_rate_per_hour;
                            } elseif ($project->billing_type == 3) {
                                $item['rate'] = $timesheet['task_data']->hourly_rate;
                            }
                            $item['unit'] = '';
                            $items[]      = $item;
                        }
                    }
                }
            }
            if ($project->billing_type != 1) {
                $data['hours_quantity'] = true;
            }
            if ($this->input->post('expenses')) {
                if (isset($data['hours_quantity'])) {
                    unset($data['hours_quantity']);
                }
                if (count($tasks) > 0) {
                    $data['qty_hrs_quantity'] = true;
                }
                $expenses       = $this->input->post('expenses');
                $addExpenseNote = $this->input->post('expenses_add_note');
                $addExpenseName = $this->input->post('expenses_add_name');

                if (!$addExpenseNote) {
                    $addExpenseNote = [];
                }

                if (!$addExpenseName) {
                    $addExpenseName = [];
                }

                $this->load->model('expenses_model');
                foreach ($expenses as $expense_id) {
                    // reset item array
                    $item                     = [];
                    $item['id']               = 0;
                    $expense                  = $this->expenses_model->get($expense_id);
                    $item['expense_id']       = $expense->expenseid;
                    $item['description']      = _l('item_as_expense') . ' ' . $expense->name;
                    $item['long_description'] = $expense->description;

                    if (in_array($expense_id, $addExpenseNote) && !empty($expense->note)) {
                        $item['long_description'] .= PHP_EOL . $expense->note;
                    }

                    if (in_array($expense_id, $addExpenseName) && !empty($expense->expense_name)) {
                        $item['long_description'] .= PHP_EOL . $expense->expense_name;
                    }

                    $item['qty'] = 1;

                    $item['taxname'] = [];
                    if ($expense->tax != 0) {
                        array_push($item['taxname'], $expense->tax_name . '|' . $expense->taxrate);
                    }
                    if ($expense->tax2 != 0) {
                        array_push($item['taxname'], $expense->tax_name2 . '|' . $expense->taxrate2);
                    }
                    $item['rate']  = $expense->amount;
                    $item['order'] = 1;
                    $item['unit']  = '';
                    $items[]       = $item;
                }
            }
            $data['customer_id']          = $project->clientid;
            $data['invoice_from_project'] = true;
            $data['add_items']            = $items;
            $this->load->view('admin/projects/invoice_project', $data);
        }
    }

    public function get_rel_project_data($id, $task_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $selected_milestone = '';
            $assigned           = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task               = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
                $assigned           = array_map(function ($member) {
                    return $member['assigneeid'];
                }, $this->tasks_model->get_task_assignees($task_id));
            }

            $allow_to_view_tasks = 0;
            $this->db->where('project_id', $id);
            $this->db->where('name', 'view_tasks');
            $project_settings = $this->db->get(db_prefix() . 'project_settings')->row();
            if ($project_settings) {
                $allow_to_view_tasks = $project_settings->value;
            }

            $deadline = get_project_deadline($id);

            echo json_encode([
                'deadline'            => $deadline,
                'deadline_formatted'  => $deadline ? _d($deadline) : null,
                'allow_to_view_tasks' => $allow_to_view_tasks,
                'billing_type'        => get_project_billing_type($id),
                'milestones'          => render_select('milestone', $this->projects_model->get_milestones($id), [
                    'id',
                    'name',
                ], 'task_milestone', $selected_milestone),
                'assignees' => render_select('assignees[]', $this->projects_model->get_project_members($id, true), [
                    'staff_id', ['firstname', 'lastname'],
                ], 'task_single_assignees', $assigned, ['multiple' => true], [], '', '', false),
            ]);
        }
    }

    public function invoice_project($project_id)
    {
        if (staff_can('create', 'invoices')) {
            $this->load->model('invoices_model');
            $data               = $this->input->post();
            $data['project_id'] = $project_id;
            $invoice_id         = $this->invoices_model->add($data);
            if ($invoice_id) {
                $this->projects_model->log_activity($project_id, 'project_activity_invoiced_project', format_invoice_number($invoice_id));
                set_alert('success', _l('project_invoiced_successfully'));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_invoices'));
        }
    }

    public function view_project_as_client($id, $clientid)
    {
        if (is_admin()) {
            login_as_client($clientid);
            redirect(site_url('clients/project/' . $id));
        }
    }

    public function get_staff_names_for_mentions($projectId)
    {
        if ($this->input->is_ajax_request()) {
            $projectId = $this->db->escape_str($projectId);

            $members = $this->projects_model->get_project_members($projectId);
            $members = array_map(function ($member) {
                $staff = $this->staff_model->get($member['staff_id']);

                $_member['id'] = $member['staff_id'];
                $_member['name'] = $staff->firstname . ' ' . $staff->lastname;

                return $_member;
            }, $members);

            echo json_encode($members);
        }
    }
}