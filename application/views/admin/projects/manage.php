<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">
                    <div class="row">
                        <div class="col-md-8">
                            <?php if (has_permission('projects', '', 'create')) { ?>
                            <a href="<?php echo admin_url('projects/project'); ?>"
                                class="btn btn-primary pull-left display-block mright5">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_project'); ?>
                            </a>
                            <?php } ?>
                            <?php if (is_admin()) { 
                                ?>

                            <a href="#"
                                class="btn btn-primary pull-left display-block mright5" data-toggle="modal" data-target="#setPercentModal">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('Set Default Percent Value'); ?>
                            </a>
                            <!-- Modal -->
                            <div class="modal fade" id="setPercentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <form id="setPercentForm">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Set Default Percent Values</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="form-group row">
                                                    <label for="staticEmail" class="col-sm-2 col-form-label">Vanzare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="vanzare" class="form-control" id="staticEmail" value="<?php echo $defaultPercentValue->vanzare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="proiectare" class="col-sm-2 col-form-label">Proiectare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="proiectare" class="form-control" id="" value="<?php echo $defaultPercentValue->proiectare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="bc" class="col-sm-2 col-form-label">Bon consum(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="bc" class="form-control" id="bc" value="<?php echo $defaultPercentValue->bc ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="productie" class="col-sm-2 col-form-label">Productie(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="productie" class="form-control" id="productie" value="<?php echo $defaultPercentValue->productie ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="livrare" class="col-sm-2 col-form-label">Livrare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="livrare" class="form-control" id="livrare" value="<?php echo $defaultPercentValue->livrare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="montaj" class="col-sm-2 col-form-label">Montaj(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="montaj" class="form-control" id="montaj" value="<?php echo $defaultPercentValue->montaj ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="profit" class="col-sm-2 col-form-label">Profit(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="profit" class="form-control" id="profit" value="<?php echo $defaultPercentValue->profit ?? '' ?>">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Set</button>
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php } ?>
                            <a href="<?php echo admin_url('projects/gantt'); ?>" data-toggle="tooltip"
                                data-title="<?php echo _l('project_gant'); ?>" class="btn btn-default btn-with-tooltip">
                                <i class="fa fa-align-left" aria-hidden="true"></i>
                            </a>
                            <a href="<?php echo admin_url('projects/switch_kanban/' . $switch_kanban);?>"
                                class="btn btn-default mright5 pull-left hidden-xs" data-toggle="tooltip" data-placement="top"
                                data-title="<?php echo $switch_kanban == 1 ? _l('switch_to_list_view') : _l('leads_switch_to_kanban'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                <i class="fa-solid fa-table-list"></i>
                                <?php } else { ?>
                                <i class="fa-solid fa-grip-vertical"></i>
                                <?php }; ?>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <?php
                        
                            if ($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') { ?>
                                <div data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('search_by_tags'); ?>">
                                    <?php echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'projects_kanban();', 'placeholder' => _l('dt_search')], [], 'no-margin') ?>
                                </div>
                            <?php } else { ?>
                            <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip"
                                data-title="<?php echo _l('filter_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right width300">
                                    <li>
                                        <a href="#" data-cview="all"
                                            onclick="dt_project_custom_view('','.table-projects',''); return false;">
                                            <?php echo _l('expenses_list_all'); ?>
                                        </a>
                                    </li>
                                    <?php
                        // Only show this filter if user has permission for projects view otherwise wont need this becuase by default this filter will be applied
                        if (has_permission('projects', '', 'view')) { ?>
                                    <li>
                                        <a href="#" data-cview="my_projects"
                                            onclick="dt_project_custom_view('my_projects','.table-projects','my_projects'); return false;">
                                            <?php echo _l('home_my_projects'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <li class="divider"></li>
                                    <?php foreach ($statuses as $status) { ?>
                                    <li class="<?php if ($status['filter_default'] == true && !$this->input->get('status') || $this->input->get('status') == $status['id']) {
                            echo 'active';
                        } ?>">
                                        <a href="#" data-cview="<?php echo 'project_status_' . $status['id']; ?>"
                                            onclick="dt_project_custom_view('<?php echo $status['id']; ?>','.table-projects','project_status_<?php echo $status['id']; ?>'); return false;">
                                            <?php echo $status['name']; ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <?php
                    if ($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') { ?>
                    <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                        <div class="row">
                            <div id="kanban-params">
                                <?php echo form_hidden('project_id', $this->input->get('project_id')); ?>
                            </div>
                            <div class="container-fluid">
                                <div id="kan-ban"></div>
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="panel-body">

                        <div class="row mbot15">
                            <div class="col-md-12">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>

                                    <span>
                                        <?php echo _l('projects_summary'); ?>
                                    </span>
                                </h4>

                                <?php
                $_where = '';
                if (!has_permission('projects', '', 'view')) {
                    $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
                }
                ?>
                            </div>
                            <div class="_filters _hidden_inputs">
                                <?php
                echo form_hidden('my_projects');
                foreach ($statuses as $status) {
                    $value = $status['id'];
                    if ($status['filter_default'] == false && !$this->input->get('status')) {
                        $value = '';
                    } elseif ($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['id'] ? $status['id'] : '');
                    }
                    echo form_hidden('project_status_' . $status['id'], $value); ?>
                                <div
                                    class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                    <?php $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = ' . $status['id']; ?>
                                    <a href="#"
                                        class="tw-text-neutral-600 hover:tw-opacity-70 tw-inline-flex tw-items-center"
                                        onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-projects','project_status_<?php echo $status['id']; ?>',true); return false;">
                                        <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                            <?php echo total_rows(db_prefix() . 'projects', $where); ?>
                                        </span>
                                        <span style="color:<?php echo $status['color']; ?>"
                                            project-status-<?php echo $status['id']; ?>">
                                            <?php echo $status['name']; ?>
                                        </span>
                                    </a>
                                </div>
                                <?php
                } ?>
                            </div>
                        </div>
                        <hr class="hr-panel-separator" />
                        <div class="row">
                            <div class="col-md-3" style="margin-left: 30px; margin-right: 15px !important">
                                <div class="row"><?= _l('project_start_date'); ?>: </div>
                                <div class="row">
                                    <?php echo render_date_input('create_from_date','','',array('placeholder' => _l('from_date') )); ?>
                                </div>
                                <div class="row">
                                    <?php echo render_date_input('create_to_date','','',array('placeholder' => _l('to_date') )); ?>
                                </div>
								</div>
                            <div class="col-md-3" style="margin-right: 15px !important">
                                <div class="row"><?= _l('project_deadline'); ?>: </div>
                                <div class="row">
                                    <?php echo render_date_input('from_due_date','','',array('placeholder' => _l('from_date') )); ?>
                                </div>
                                <div class="row">
                                    <?php echo render_date_input('to_due_date','','',array('placeholder' => _l('to_date') )); ?>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-right: 15px !important">
                                <div class="row"><?= _l('date_finished'); ?>: </div>
                                <div class="row">
                                    <?php echo render_date_input('finish_from_date','','',array('placeholder' => _l('from_date') )); ?>
                                </div>
                                <div class="row">
                                    <?php echo render_date_input('finish_to_date','','',array('placeholder' => _l('to_date') )); ?>
                                </div>  
                                <div class="row"><?= _l('Tip comanda'); ?>: </div>
                                <div class="row">
                                    <?php 
                                    $custom_fields = get_custom_fields('projects');
                                    $values = $custom_fields[0]['options'];
                                    $lists = [];
                                    foreach (explode(',' , $values) as $list) {
                                        array_push($lists, ['name' => $list]);
                                    }
                                    ?>
                                    <?php echo render_select('project_type', $lists, ['name', ['name']], '', '', ['multiple' => true,'data-width' => '100%', 'data-none-selected-text' => _l('Tip Comanda')], [], 'no-mbot'); ?>

                                </div>                          
                            </div>
                            <div class="col-md-2" style="margin-right: 15px !important">
                                <div class="row"><?= _l('project_designer'); ?>: </div>
                                <div class="row">
                                    <?php echo render_select('project_designer', $staff, ['staffid', ['firstname', 'lastname']], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('project_designer')], [], 'no-mbot'); ?>
                                </div>
                                <div class="row"><?= _l('project_technolog'); ?>: </div>
                                <div class="row">
                                    <?php echo render_select('project_technologist', $staff, ['staffid', ['firstname', 'lastname']], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('project_technolog')], [], 'no-mbot'); ?>
                                </div>
                                <div class="row"><?= _l('project_montator'); ?>: </div>
                                <div class="row">
                                    <?php echo render_select('project_mounter', $staff, ['staffid', ['firstname', 'lastname']], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('project_montator')], [], 'no-mbot'); ?>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row text-center" style="font-size: 17px;">
                            <div class="col-md-2">
                                <p><?= _l('Proiecte'); ?>:</p> <span id="total_index"></span>
                            </div>
                            <div class="col-md-2">
                                <p><?= _l('total_amount'); ?>:</p> <span id="total_amount"></span>
                            </div>
                            <div class="col-md-2">
                                <p><?= _l('Cheltuieli BC'); ?>:</p> <span id="total_bc"></span>
                            </div>
                            <div class="col-md-2">
                                <p><?= _l('Cheltuieli HR'); ?>:</p> <span id="total_hr"></span>
                            </div>
                            <div class="col-md-2">
                                <p><?= _l('Total Cheltuieli'); ?>:</p> <span id="total_cost"></span>
                            </div>
                            <div class="col-md-2">
                                <p><?= _l('Profit'); ?>:</p> <span id="total_profit"></span>
                            </div>
                        </div>
                        <br>
                        <div class="panel-table-full">
                            <?php echo form_hidden('custom_view'); ?>
                            <?php $this->load->view('admin/projects/table_html'); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
var ProjectsServerParams = {};
$(function() {
    reload();
    projects_kanban();

});

$('#create_from_date').blur(function() {
    reload();
});
$('#create_to_date').blur(function() {
    reload();
});
$('#finish_from_date').blur(function() {
    reload();
});
$('#finish_to_date').blur(function() {
    reload();
});
$('#from_due_date').blur(function() {
    reload();
});
$('#to_due_date').blur(function() {
    reload();
});
$('#project_mounter').change(function() {
    reload();
});
$('#project_technologist').change(function() {
    reload();
});
$('#project_designer').change(function() {
    reload();
});
$('#project_type').change(function() {
    reload();
});

data = {};
function reload()
{
    $.each($('._hidden_inputs._filters input'), function() {
        ProjectsServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        data[$(this).attr('name')] = $('input[name="' + $(this).attr('name') + '"]').val();
    });

    data[$('#create_from_date').attr('name')] = $('input[name="' + $('#create_from_date').attr('name') + '"]').val();
    data[$('#create_to_date').attr('name')] = $('input[name="' + $('#create_to_date').attr('name') + '"]').val();
    data[$('#finish_from_date').attr('name')] = $('input[name="' + $('#finish_from_date').attr('name') + '"]').val();
    data[$('#finish_to_date').attr('name')] = $('input[name="' + $('#finish_to_date').attr('name') + '"]').val();
    data[$('#from_due_date').attr('name')] = $('input[name="' + $('#from_due_date').attr('name') + '"]').val();
    data[$('#to_due_date').attr('name')] = $('input[name="' + $('#to_due_date').attr('name') + '"]').val();
    data[$('#project_mounter').attr('name')] = $('select[name="' + $('#project_mounter').attr('name') + '"]').val();
    data[$('#project_technologist').attr('name')] = $('select[name="' + $('#project_technologist').attr('name') + '"]').val();
    data[$('#project_designer').attr('name')] = $('select[name="' + $('#project_designer').attr('name') + '"]').val();
    data[$('#project_type').attr('name')] = $('select[name="' + $('#project_type').attr('name') + '"]').val();

    ProjectsServerParams[$('#create_from_date').attr('name')] = '[name="' + $('#create_from_date').attr('name') + '"]';
    ProjectsServerParams[$('#create_to_date').attr('name')] = '[name="' + $('#create_to_date').attr('name') + '"]';
    ProjectsServerParams[$('#finish_from_date').attr('name')] = '[name="' + $('#finish_from_date').attr('name') + '"]';
    ProjectsServerParams[$('#finish_to_date').attr('name')] = '[name="' + $('#finish_to_date').attr('name') + '"]';
    ProjectsServerParams[$('#from_due_date').attr('name')] = '[name="' + $('#from_due_date').attr('name') + '"]';
    ProjectsServerParams[$('#to_due_date').attr('name')] = '[name="' + $('#to_due_date').attr('name') + '"]';
    ProjectsServerParams[$('#project_mounter').attr('name')] = '[name="' + $('#project_mounter').attr('name') + '"]';
    ProjectsServerParams[$('#project_technologist').attr('name')] = '[name="' + $('#project_technologist').attr('name') + '"]';
    ProjectsServerParams[$('#project_type').attr('name')] = '[name="' + $('#project_type').attr('name') + '"]';

    $.post(admin_url + 'projects/getProjectSummaryData', data).done(function(response) {
        var result = JSON.parse(response);
        $('#total_index').text(result['total_index']);
        $('#total_amount').text(result['total_price']);
        $('#total_bc').text(result['total_cost_bc']);
        $('#total_hr').text(result['total_cost_hr']);
        $('#total_cost').text(result['total_cost']);
        $('#total_profit').text(result['total_profit']);
    });
    $('.table-projects').DataTable().destroy();
    initDataTable('.table-projects', admin_url + 'projects/table', undefined, undefined, ProjectsServerParams,
        <?php hooks()->apply_filters('projects_table_default_order', json_encode([5, 'asc'])); ?>);
    
    init_ajax_search('customer', '#clientid_copy_project.ajax-search');
}

function dt_project_custom_view(value, table, custom_input_name, clear_other_filters) {
  var name =
    typeof custom_input_name == "undefined" ? "custom_view" : custom_input_name;
  if (typeof clear_other_filters != "undefined") {
    var filters = $("._filter_data li.active").not(".clear-all-prevent");
    filters.removeClass("active");
    $.each(filters, function () {
      var input_name = $(this).find("a").attr("data-cview");
      $('._filters input[name="' + input_name + '"]').val("");
    });
  }
  var _cinput = do_filter_active(name);
  if (_cinput != name) {
    value = "";
  }
  $('input[name="' + name + '"]').val(value);
  reload();
}

$('#setPercentForm').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: admin_url + 'projects/setDefaultPercentValue',
        method: "GET",
        data: formData,
        success: function(response) {
            if (response == 1)
                $('#setPercentModal').modal('hide');
        },
    });

});
</script>
</body>

</html>