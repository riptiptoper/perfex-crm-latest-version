<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <?php echo form_hidden('project_id', $project->id) ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons">
                    <div class="row">
                        <div class="col-md-7 project-heading">
                            <div class="tw-flex tw-flex-wrap tw-items-center">
                                <h3 class="hide project-name"><?php echo $project->name; ?></h3>
                                <div id="project_view_name" class="tw-max-w-sm tw-mr-3">
                                    <div class="tw-w-full">
                                        <select class="selectpicker" id="project_top" data-width="100%"
                                            <?php if (count($other_projects) > 6) { ?> data-live-search="true"
                                            <?php } ?>>
                                            <option value="<?php echo $project->id; ?>" selected
                                                data-content="<?php echo $project->name; ?> - <small><?php echo $project->client_data->company; ?></small>">
                                                <?php echo $project->client_data->company; ?>
                                                <?php echo $project->name; ?>
                                            </option>
                                            <?php foreach ($other_projects as $op) { ?>
                                            <option value="<?php echo $op['id']; ?>"
                                                data-subtext="<?php echo $op['company']; ?>">#<?php echo $op['id']; ?> -
                                                <?php echo $op['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="visible-xs">
                                    <div class="clearfix"></div>
                                </div>

                                <div class="tw-items-center ltr:tw-space-x-2 tw-inline-flex">
                                    <div class="tw-flex -tw-space-x-1">
                                        <?php foreach ($members as $member) { ?>
                                        <span class="tw-group tw-relative"
                                            data-title="<?php echo get_staff_full_name($member['staff_id']) . (has_permission('projects', '', 'create') || $member['staff_id'] == get_staff_user_id() ? ' - ' . _l('total_logged_hours_by_staff') . ': ' . seconds_to_time_format($member['total_logged_time']) : ''); ?>"
                                            data-toggle="tooltip">
                                            <?php if (has_permission('projects', '', 'edit')) { ?>
                                            <a href="<?php echo admin_url('projects/remove_team_member/' . $project->id . '/' . $member['staff_id']); ?>"
                                                class="_delete group-hover:tw-inline-flex tw-hidden tw-rounded-full tw-absolute tw-items-center tw-justify-center tw-bg-neutral-300/50 tw-h-7 tw-w-7 tw-cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="tw-w-4 tw-h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </a>
                                            <?php } ?>
                                            <?php echo staff_profile_image($member['staff_id'], ['tw-inline-block tw-h-7 tw-w-7 tw-rounded-full tw-ring-2 tw-ring-white', '']); ?>
                                        </span>
                                        <?php } ?>
                                    </div>
                                    <a href="#" data-target="#add-edit-members" data-toggle="modal"
                                        class="tw-mt-1.5 rtl:tw-mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="tw-w-5 tw-h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </div>
                                <?php
                                echo '<span class="tw-ml-1 project_status tw-inline-block label project-status-' . $project->status . '" style="color:' . $project_status['color'] . ';border:1px solid ' . adjust_hex_brightness($project_status['color'], 0.4) . ';background: ' . adjust_hex_brightness($project_status['color'], 0.04) . ';">' . $project_status['name'] . '</span>';
                            ?>
                            </div>
                        </div>
                        <div class="col-md-5 text-right">
                            <?php if (is_admin()) { 
                                    ?>
                            <a href="#"
                                class="btn btn-primary" data-toggle="modal" data-target="#setPercentModal">
                                <?php echo _l('Change Percent Value'); ?>
                            </a>
                            <!-- Modal -->
                            <div class="modal fade" id="setPercentModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
                                <form id="setPercentForm">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" style="float: left" id="exampleModalLongTitle">Set Percent Values</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="form-group row">
                                                    <label for="staticEmail" class="col-sm-2 col-form-label">Vanzare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="vanzare" class="form-control" id="staticEmail" value="<?php echo $project->vanzare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <input hidden name="project_id" value="<?php echo $project->id; ?>">
                                                <div class="form-group row">
                                                    <label for="proiectare" class="col-sm-2 col-form-label">Proiectare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="proiectare" class="form-control" id="" value="<?php echo $project->proiectare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="bc" class="col-sm-2 col-form-label">Bon consum(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="bc" class="form-control" id="bc" value="<?php echo $project->bc ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="productie" class="col-sm-2 col-form-label">Productie(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="productie" class="form-control" id="productie" value="<?php echo $project->productie ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="livrare" class="col-sm-2 col-form-label">Livrare(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="livrare" class="form-control" id="livrare" value="<?php echo $project->livrare ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="montaj" class="col-sm-2 col-form-label">Montaj(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="montaj" class="form-control" id="montaj" value="<?php echo $project->montaj ?? '' ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="profit" class="col-sm-2 col-form-label">Profit(%):</label>
                                                    <div class="col-sm-10">
                                                    <input type="number" name="profit" class="form-control" id="profit" value="<?php echo $project->profit ?? '' ?>">
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
                            <?php if (has_permission('tasks', '', 'create')) { ?>
                            <a href="#"
                                onclick="new_task_from_relation(undefined,'project',<?php echo $project->id; ?>); return false;"
                                class="btn btn-primary">
                                <i class="fa-regular fa-plus tw-mr-1"></i>
                                <?php echo _l('new_task'); ?>
                            </a>
                            <?php } ?>
                            <?php
                           $invoice_func = 'pre_invoice_project';
                           ?>
                            <?php if (has_permission('invoices', '', 'create')) { ?>
                            <a href="#"
                                onclick="<?php echo $invoice_func; ?>(<?php echo $project->id; ?>); return false;"
                                class="invoice-project btn btn-primary<?php if ($project->client_data->active == 0) {
                               echo ' disabled';
                           } ?>">
                                <i class="fa-solid fa-file-invoice tw-mr-1"></i>
                                <?php echo _l('invoice_project'); ?>
                            </a>
                            <?php } ?>
                            <?php
                           $project_pin_tooltip = _l('pin_project');
                           if (total_rows(db_prefix() . 'pinned_projects', ['staff_id' => get_staff_user_id(), 'project_id' => $project->id]) > 0) {
                               $project_pin_tooltip = _l('unpin_project');
                           }
                           ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <?php echo _l('more'); ?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                                    <li>
                                        <a href="<?php echo admin_url('projects/pin_action/' . $project->id); ?>">
                                            <?php echo $project_pin_tooltip; ?>
                                        </a>
                                    </li>
                                    <?php if (has_permission('projects', '', 'edit')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/project/' . $project->id); ?>">
                                            <?php echo _l('edit_project'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'create')) { ?>
                                    <li>
                                        <a href="#" onclick="copy_project(<?php echo $project->id;?>); return false;">
                                            <?php echo _l('copy_project'); ?>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) { ?>
                                    <li class="divider"></li>
                                    <?php foreach ($statuses as $status) {
                               if ($status['id'] == $project->status) {
                                   continue;
                               } ?>
                                    <li>
                                        <a href="#" data-name="<?php echo _l('project_status_' . $status['id']); ?>"
                                            onclick="project_mark_as_modal(<?php echo $status['id']; ?>,<?php echo $project->id; ?>, this); return false;"><?php echo _l('project_mark_as', $status['name']); ?></a>
                                    </li>
                                    <?php
                           } ?>
                                    <?php } ?>
                                    <li class="divider"></li>
                                    <?php if (has_permission('projects', '', 'create')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/export_project_data/' . $project->id); ?>"
                                            target="_blank"><?php echo _l('export_project_data'); ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if (is_admin()) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/view_project_as_client/' . $project->id . '/' . $project->clientid); ?>"
                                            target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if (has_permission('projects', '', 'delete')) { ?>
                                    <li>
                                        <a href="<?php echo admin_url('projects/delete/' . $project->id); ?>"
                                            class="_delete">
                                            <span class="text-danger"><?php echo _l('delete_project'); ?></span>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="project-menu-panel tw-my-5">
                    <?php hooks()->do_action('before_render_project_view', $project->id); ?>
                    <?php $this->load->view('admin/projects/project_tabs'); ?>
                </div>
                <?php
               if ((has_permission('projects', '', 'create') || has_permission('projects', '', 'edit'))
                 && $project->status == 1
                 && $this->projects_model->timers_started_for_project($project->id)
                 && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning project-no-started-timers-found mbot15">
                    <?php echo _l('project_not_started_status_tasks_timers_found'); ?>
                </div>
                <?php
               } ?>
                <?php
               if ($project->deadline && date('Y-m-d') > $project->deadline
                && $project->status == 2
                && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning bold project-due-notice mbot15">
                    <?php echo _l('project_due_notice', floor((abs(time() - strtotime($project->deadline))) / (60 * 60 * 24))); ?>
                </div>
                <?php
               } ?>
                <?php
               if (!has_contact_permission('projects', get_primary_contact_user_id($project->clientid))
                 && total_rows(db_prefix() . 'contacts', ['userid' => $project->clientid]) > 0
                 && $tab['slug'] != 'project_milestones') {
                   ?>
                <div class="alert alert-warning project-permissions-warning mbot15">
                    <?php echo _l('project_customer_permission_warning'); ?>
                </div>
                <?php
               } ?>

                <?php $this->load->view(($tab ? $tab['view'] : 'admin/projects/project_overview')); ?>

            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/add_edit_members/' . $project->id)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
            </div>
            <div class="modal-body">
                <?php
            $selected = [];
            foreach ($members as $member) {
                array_push($selected, $member['staff_id']);
            }
           echo render_select('project_members[]', $staff, ['staffid', ['firstname', 'lastname']], 'project_members', $selected, ['multiple' => true, 'data-actions-box' => true], [], '', '', false);
           ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary" autocomplete="off"
                    data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php if (isset($discussion)) {
               echo form_hidden('discussion_id', $discussion->id);
               echo form_hidden('discussion_user_profile_image_url', $discussion_user_profile_image_url);
               echo form_hidden('current_user_is_admin', $current_user_is_admin);
           }
   echo form_hidden('project_percent', $percent);
   ?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php $this->load->view('admin/projects/milestone'); ?>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php $this->load->view('admin/projects/_mark_tasks_finished'); ?>
<?php init_tail(); ?>
<!-- For invoices table -->
<script>
taskid = '<?php echo $this->input->get('taskid'); ?>';
</script>
<script>
$('#setPercentForm').on('submit', function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: admin_url + 'projects/changeDefaultPercentValue',
        method: "GET",
        data: formData,
        success: function(response) {
            if (response == 1)
            {
                $('#setPercentModal').modal('hide');
                window.location.reload();
            }
        },
    });
});
</script>
<script>
var gantt_data = {};
<?php if (isset($gantt_data)) { ?>
gantt_data = <?php echo json_encode($gantt_data); ?>;
<?php } ?>
var discussion_id = $('input[name="discussion_id"]').val();
var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
var project_id = $('input[name="project_id"]').val();
if (typeof(discussion_id) != 'undefined') {
    discussion_comments('#discussion-comments', discussion_id, 'regular');
}
$(function() {
    var project_progress_color =
        '<?php echo hooks()->apply_filters('admin_project_progress_color', '#84c529'); ?>';
    var circle = $('.project-progress').circleProgress({
        fill: {
            gradient: [project_progress_color, project_progress_color]
        }
    }).on('circle-animation-progress', function(event, progress, stepValue) {
        $(this).find('strong.project-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
    });
});

function discussion_comments(selector, discussion_id, discussion_type) {
    var defaults = _get_jquery_comments_default_config(
        <?php echo json_encode(get_project_discussions_language_array()); ?>);
    var options = {
        // https://github.com/Viima/jquery-comments/pull/169
        wysiwyg_editor: {
            opts: {
                enable: true,
                is_html: true,
                container_id: 'editor-container',
                comment_index: 0,
            },
            init: function(textarea, content) {
                var comment_index = textarea.data('comment_index');
                var editorConfig = _simple_editor_config();
                editorConfig.setup = function(ed) {
                    textarea.data('wysiwyg_editor', ed);

                    ed.on('change', function() {
                        var value = ed.getContent();
                        if (value !== ed._lastChange) {
                            ed._lastChange = value;
                            textarea.trigger('change');
                        }
                    });

                    ed.on('keyup', function() {
                        var value = ed.getContent();
                        if (value !== ed._lastChange) {
                            ed._lastChange = value;
                            textarea.trigger('change');
                        }
                    });

                    ed.on('Focus', function(e) {
                        setTimeout(function() {
                            textarea.trigger('click');
                        }, 500)
                    });

                    ed.on('init', function() {
                        if (content) ed.setContent(content);

                        if ($('#mention-autocomplete-css').length === 0) {
                            $('<link>').appendTo('head').attr({
                                id: 'mention-autocomplete-css',
                                type: 'text/css',
                                rel: 'stylesheet',
                                href: site_url +
                                    'assets/plugins/tinymce/plugins/mention/autocomplete.css'
                            });
                        }

                        if ($('#mention-css').length === 0) {
                            $('<link>').appendTo('head').attr({
                                type: 'text/css',
                                id: 'mention-css',
                                rel: 'stylesheet',
                                href: site_url +
                                    'assets/plugins/tinymce/plugins/mention/rte-content.css'
                            });
                        }
                    })
                }

                editorConfig.toolbar = editorConfig.toolbar.replace('alignright', 'alignright strikethrough')
                editorConfig.plugins[0] += ' mention';
                editorConfig.content_style = 'span.mention {\
                     background-color: #eeeeee;\
                     padding: 3px;\
                  }';
                var projectUserMentions = [];
                editorConfig.mentions = {
                    source: function(query, process, delimiter) {
                        if (projectUserMentions.length < 1) {
                            $.getJSON(admin_url + 'projects/get_staff_names_for_mentions/' + project_id,
                                function(data) {
                                    projectUserMentions = data;
                                    process(data)
                                });
                        } else {
                            process(projectUserMentions)
                        }
                    },
                    insert: function(item) {
                        return '<span class="mention" contenteditable="false" data-mention-id="' + item
                            .id + '">@' +
                            item.name + '</span>&nbsp;';
                    }
                };

                var containerId = this.get_container_id(comment_index);
                tinyMCE.remove('#' + containerId);

                setTimeout(function() {
                    init_editor('#' + containerId, editorConfig)
                }, 100)
            },
            get_container: function(textarea) {
                if (!textarea.data('comment_index')) {
                    textarea.data('comment_index', ++this.opts.comment_index);
                }

                return $('<div/>', {
                    'id': this.get_container_id(this.opts.comment_index)
                });
            },
            get_contents: function(editor) {
                return editor.getContent();
            },
            on_post_comment: function(editor, evt) {
                editor.setContent('');
            },
            get_container_id: function(comment_index) {
                var container_id = this.opts.container_id;
                if (comment_index) container_id = container_id + "-" + comment_index;
                return container_id;
            }
        },
        currentUserIsAdmin: current_user_is_admin,
        getComments: function(success, error) {
            $.get(admin_url + 'projects/get_discussion_comments/' + discussion_id + '/' + discussion_type,
                function(response) {
                    success(response);
                }, 'json');
        },
        postComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/add_discussion_comment/' + discussion_id + '/' +
                    discussion_type,
                data: commentJSON,
                success: function(comment) {
                    comment = JSON.parse(comment);
                    success(comment)
                },
                error: error
            });
        },
        putComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/update_discussion_comment',
                data: commentJSON,
                success: function(comment) {
                    comment = JSON.parse(comment);
                    success(comment)
                },
                error: error
            });
        },
        deleteComment: function(commentJSON, success, error) {
            $.ajax({
                type: 'post',
                url: admin_url + 'projects/delete_discussion_comment/' + commentJSON.id,
                success: success,
                error: error
            });
        },
        uploadAttachments: function(commentArray, success, error) {
            var responses = 0;
            var successfulUploads = [];
            var serverResponded = function() {
                responses++;
                // Check if all requests have finished
                if (responses == commentArray.length) {
                    // Case: all failed
                    if (successfulUploads.length == 0) {
                        error();
                        // Case: some succeeded
                    } else {
                        successfulUploads = JSON.parse(successfulUploads);
                        success(successfulUploads)
                    }
                }
            }
            $(commentArray).each(function(index, commentJSON) {
                // Create form data
                var formData = new FormData();
                if (commentJSON.file.size && commentJSON.file.size > app
                    .max_php_ini_upload_size_bytes) {
                    alert_float('danger', "<?php echo _l('file_exceeds_max_filesize'); ?>");
                    serverResponded();
                } else {
                    $(Object.keys(commentJSON)).each(function(index, key) {
                        var value = commentJSON[key];
                        if (value) formData.append(key, value);
                    });

                    if (typeof(csrfData) !== 'undefined') {
                        formData.append(csrfData['token_name'], csrfData['hash']);
                    }
                    $.ajax({
                        url: admin_url + 'projects/add_discussion_comment/' + discussion_id +
                            '/' + discussion_type,
                        type: 'POST',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(commentJSON) {
                            successfulUploads.push(commentJSON);
                            serverResponded();
                        },
                        error: function(data) {
                            var error = JSON.parse(data.responseText);
                            alert_float('danger', error.message);
                            serverResponded();
                        },
                    });
                }
            });
        }
    }
    var settings = $.extend({}, defaults, options);
    $(selector).comments(settings);
}
</script>
</body>

</html>