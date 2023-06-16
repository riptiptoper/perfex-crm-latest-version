<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<li data-project-id="<?php echo $project['id']; ?>" class="project">
        <div class="panel-body lead-body">
        <div class="row">
            <div class="col-md-12 lead-name">
                <a href="<?php echo admin_url('projects/view/' . $project['id']); ?>" class="pull-left">
                    <span
                        class="inline-block mtop10 mbot10">#<?php echo $project['id'] . ' - ' . $project['name']; ?></span>
                </a>
            </div>
            <div class="col-md-12">
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-text-sm tw-mb-0">
                            <?php echo _l('project_customer'); ?>: <?php echo $project['client_company']; ?>
                        </p>
                    </div>
                </div>
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-text-sm tw-mb-0">
                            <?php echo _l('project_description'); ?>: <?php echo $project['description']; ?>
                        </p>
                    </div>
                </div>
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-text-sm tw-mb-0">
                            <?php echo _l('created_at'); ?>: <?php echo $project['project_created']; ?>
                        </p>
                    </div>
                </div>
                <?php if ($status == "4") { ?>
                <div class="tw-flex">
                    <div class="tw-grow">
                        <p class="tw-text-sm tw-mb-0">
                            <?php echo _l('date_finished'); ?>: <?php echo $project['date_finished']; ?>
                        </p>
                    </div>
                </div>
                <?php } ?>
            </div>

            <a href="#" class="pull-right text-muted kan-ban-expand-top"
                onclick="slideToggle('#kan-ban-expand-<?php echo $project['id']; ?>'); return false;">
                <i class="fa fa-expand" aria-hidden="true"></i>
            </a>
            <div class="clearfix no-margin"></div>
            <div id="kan-ban-expand-<?php echo $project['id']; ?>" class="padding-10" style="display:none;">
                <div class="clearfix"></div>
                <hr class="hr-10" />
                <p class="text-muted lead-field-heading"><?php echo _l('Tip comanda'); ?></p>
                <p class="bold tw-text-sm">
                    <?php $custom_fields = get_custom_fields('projects');
                        $value = get_custom_field_value($project['id'], 1, 'projects'); ?>
                    <?php echo($value != '' ? $value : '-') ?>
                </p>
                <p class="text-muted lead-field-heading"><?php echo _l('project_start_date'); ?></p>
                <p class="bold tw-text-sm">
                    <?php echo($project['start_date'] != '' ? $project['start_date'] : '-') ?>
                </p>
                <p class="text-muted lead-field-heading"><?php echo _l('project_deadline'); ?></p>
                <p class="bold tw-text-sm">
                    <?php echo($project['deadline'] != '' ? $project['deadline'] : '-') ?>
                </p>
                <?php
                    $this->db->select_sum('total');
                    $this->db->where('project_id', $project['id']);
                    $data = $this->db->get(db_prefix() . 'proposals')->row();
                    
                    $baseCurrency = get_base_currency();
                
                    $total_amount = app_format_money($data->total, $baseCurrency);
                ?>
                <p class="text-muted lead-field-heading"><?php echo _l('total_amount'); ?></p>
                <p class="bold tw-text-sm">
                    <?php echo($total_amount != '' ? $total_amount : '-') ?>
                </p>
                <?php
                    $q = $this->db->query('
                    SELECT SUM(CASE
                        WHEN end_time is NULL THEN ' . time() . '-start_time
                        ELSE end_time-start_time
                        END) as total_logged_time
                    FROM ' . db_prefix() . 'taskstimers
                    WHERE task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id=' . $this->db->escape_str($project['id']) . ')')
                    ->row();
        
                    $total_logged_time = seconds_to_time_format($q->total_logged_time);
                    
                    $cost_bc = sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project['id']], 'field' => 'amount']);
                    $cost_cur_bc = app_format_money($cost_bc, ($project['currency'] != 0 ? get_currency($project['currency']) : $baseCurrency));
                    
                    $cost_hr = 0;
                    if ($project['billing_type'] == 3)
                    {
                        $tasks = $this->db->select(db_prefix() . 'tasks.hourly_rate, (CASE
                        WHEN ' . db_prefix() . 'taskstimers.end_time is NULL THEN ' . time() . '-'. db_prefix() . 'taskstimers.start_time
                        ELSE ' . db_prefix() . 'taskstimers.end_time-' . db_prefix() . 'taskstimers.start_time
                        END) as logged_time')->join(db_prefix() . 'taskstimers', '' . db_prefix() . 'taskstimers.task_id = ' . db_prefix() . 'tasks.id', 'right')->where(db_prefix() . 'tasks.rel_id', $project['id'])->where(db_prefix() . 'tasks.rel_type', 'project')
                        ->get(db_prefix() . 'tasks')->result_array();
                    
                        foreach ($tasks as $task)
                        {
                            $cost_hr += sec2qty($task['logged_time']) * $task['hourly_rate'];
                        }
                        $cost_cur_hr = app_format_money($cost_hr, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
                    }
                    else
                        $cost_cur_hr = app_format_money($cost_hr, ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
                
                    $total = app_format_money(($cost_hr + $cost_bc), ($aRow['currency'] != 0 ? get_currency($aRow['currency']) : $baseCurrency));
                ?>
                <p class="text-muted lead-field-heading"><?php echo _l('Cheltuieli - BC'); ?></p>
                <p class="bold tw-text-sm"><?php echo($cost_cur_bc) ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('Cheltuieli - HR'); ?></p>
                <p class="bold tw-text-sm"><?php echo($cost_cur_hr) ?></p>
                <p class="text-muted lead-field-heading"><?php echo _l('Cheltuieli totale'); ?></p>
                <p class="bold tw-text-sm"><?php echo($total) ?></p>
                <?php
                    $designers = $this->db->select(db_prefix() . 'project_res_members.staff_id, CONCAT('. db_prefix() . 'staff.firstname, \' \', '. db_prefix() . 'staff.lastname) as full_name')
                            ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id')
                            ->where('project_id', $project['id'])
                            ->where('user_type', 2)
                            ->get(db_prefix() . 'project_res_members')->result_array();
                ?>
                <p class="text-muted lead-field-heading"><?php echo _l('project_designer'); ?></p>
                <p class="bold tw-text-sm">
                    <?php 
                    if(count($designers) > 0)
                        foreach ($designers as $designer)
                            echo '<a href="' . admin_url('profile/' . $designer['staff_id']) . '">' .
                                staff_profile_image($designer['staff_id'], [
                                    'staff-profile-image-small mright5',
                                    ], 'small', [
                                    'data-toggle' => 'tooltip',
                                    'data-title'  => $designer['full_name'],
                                    ]) . '</a>';
                    else
                        echo '-';
                    ?>
                </p>
                <?php
                    $techs = $this->db->select(db_prefix() . 'project_res_members.staff_id, CONCAT('. db_prefix() . 'staff.firstname, \' \', '. db_prefix() . 'staff.lastname) as full_name')
                            ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id')
                            ->where('project_id', $project['id'])
                            ->where('user_type', 2)
                            ->get(db_prefix() . 'project_res_members')->result_array();
                ?>
                <p class="text-muted lead-field-heading"><?php echo _l('project_technolog'); ?></p>
                <p class="bold tw-text-sm">
                    <?php 
                    if(count($techs) > 0)
                    foreach ($techs as $tech)
                        echo '<a href="' . admin_url('profile/' . $tech['staff_id']) . '">' .
                            staff_profile_image($tech['staff_id'], [
                                'staff-profile-image-small mright5',
                                ], 'small', [
                                'data-toggle' => 'tooltip',
                                'data-title'  => $tech['full_name'],
                                ]) . '</a>';
                    else
                        echo '-';
                    ?>
                </p>
                <?php
                    $mounters = $this->db->select(db_prefix() . 'project_res_members.staff_id, CONCAT('. db_prefix() . 'staff.firstname, \' \', '. db_prefix() . 'staff.lastname) as full_name')
                            ->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_res_members.staff_id')
                            ->where('project_id', $project['id'])
                            ->where('user_type', 1)
                            ->get(db_prefix() . 'project_res_members')->result_array();
                ?>
                <p class="text-muted lead-field-heading"><?php echo _l('project_montator'); ?></p>
                <p class="bold tw-text-sm">
                    <?php 
                    if(count($techs) > 0)
                        foreach ($mounters as $mounter)
                            echo '<a href="' . admin_url('profile/' . $mounter['staff_id']) . '">' .
                                staff_profile_image($mounter['staff_id'], [
                                    'staff-profile-image-small mright5',
                                    ], 'small', [
                                    'data-toggle' => 'tooltip',
                                    'data-title'  => $mounter['full_name'],
                                    ]) . '</a>';
                    else
                        echo '-';
                    ?>
                </p>
            </div>
        </div>
    </div>
</li>