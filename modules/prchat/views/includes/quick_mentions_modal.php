<div class="modal fade" id="quickMentionsModal" tabindex="-1" role="dialog">
    <form id="quickMentionsForm">
        <div class="modal-dialog" role="any">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?= _l('chat_quick_mention_title'); ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <?php
                    $rel_type = '';
                    $rel_id = '';
                    if (isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                        $rel_id = isset($task) ? $task->rel_id : $this->input->get('rel_id');
                        $rel_type = isset($task) ? $task->rel_type : $this->input->get('rel_type');
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="project"> <?php echo _l('project'); ?> </option>
                                    <option value="invoice"> <?php echo _l('invoice'); ?> </option>
                                    <option value="customer"> <?php echo _l('client'); ?> </option>
                                    <option value="estimate"> <?php echo _l('estimate'); ?> </option>
                                    <option value="contract"> <?php echo _l('contract'); ?> </option>
                                    <option value="ticket"> <?php echo _l('ticket'); ?> </option>
                                    <option value="expense"> <?php echo _l('expense'); ?> </option>
                                    <option value="lead"> <?php echo _l('lead'); ?> </option>
                                    <option value="proposal"> <?php echo _l('proposal'); ?> </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group<?php if ($rel_id == '') {
                                                        echo ' hide';
                                                    } ?>" id="rel_id_wrapper">
                                <label for="rel_id" class="control-label"><span class="rel_id_label"></span></label>
                                <div id="rel_id_select">
                                    <select name="rel_id" id="rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <?php if ($rel_id != '' && $rel_type != '') {
                                            $rel_data = get_relation_data($rel_type, $rel_id);
                                            $rel_val = get_relation_values($rel_data, $rel_type);
                                            echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?= _l('chat_mention_text'); ?></button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->


<script>
    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _option_id,
        _related_to,
        _rel_id_wrapper = $('#rel_id_wrapper');

    $(function() {

        $('#quickMentionsForm').on('submit', function(e) {
            e.preventDefault();
            $('#quickMentionsModal').modal('hide');
            $('#quickMentionsModal').selectpicker('refresh');
            return false;
        });

        $("body").off("change", "#rel_id");

        appValidateForm($('#quickMentionsForm'), {
            rel_type: 'required',
            rel_id: 'required'
        }, fncQuickMention);

        function fncQuickMention() {
            let view_id = $('optgroup').find('option:selected').val();
            let title = $('optgroup').find('option:selected').attr('title');
            let relation = _related_to;
            let mention = '';

            if (relation != '') {
                switch (relation) {
                    case 'project':
                        mention = site_url + 'admin/projects/view/' + view_id
                        break;
                    case 'invoice':
                        mention = site_url + 'admin/invoices#' + view_id
                        break;
                    case 'customer':
                        mention = site_url + 'admin/clients/client/' + view_id + '?group=profile'
                        break;
                    case 'estimate':
                        mention = site_url + 'admin/estimates/estimate/' + view_id
                        break;
                    case 'contract':
                        mention = site_url + 'admin/contracts/contract/' + view_id
                        break;
                    case 'expense':
                        mention = site_url + 'admin/expenses#' + view_id
                        break;
                    case 'lead':
                        mention = site_url + 'admin/leads/index/' + view_id
                        break;
                    case 'proposal':
                        mention = site_url + 'admin/proposals#' + view_id
                        break;
                    default:
                }

                var chatBox = $('body').find('.client_chatbox');

                if ($('body').find('ul.chat_nav li.staff').hasClass('active')) {
                    chatBox = $('body').find('.chatbox');
                } else if ($('ul.chat_nav li.groups').hasClass('active')) {
                    chatBox = $('body').find('.group_chatbox');
                }

                $(chatBox).val("<a class='quickMentionLink' href='" + mention + "' target='_blank'>" + title + "</a>");
                $('button.submit').trigger('click');

                setTimeout(() => {
                    $(chatBox).scrollTop($(chatBox)[0].scrollHeight);
                }, 500);
            }
        }

        $('.rel_id_label').html(_rel_type.find('option:selected').text());

        _rel_type.on('change', function() {

            var clonedSelect = _rel_id.html('').clone();
            _rel_id.selectpicker('destroy').remove();
            _rel_id = clonedSelect;
            $('#rel_id_select').append(clonedSelect);
            $('.rel_id_label').html(_rel_type.find('option:selected').text());

            _task_rel_select();
            if ($(this).val() != '') {
                _related_to = _rel_type.find('option:selected').val();
                _rel_id_wrapper.removeClass('hide');
            } else {
                _rel_id_wrapper.addClass('hide');
            }
            _init_project_details(_rel_type.val());
        });

        init_selectpicker();
        _task_rel_select();

        <?php if (!isset($task) && $rel_id != '') { ?>
            _rel_id.change();
        <?php } ?>

    });

    function _init_project_details(type, tasks_visible_to_customer) {
        var wrap = $('.non-project-details');
        var wrap_task_hours = $('.task-hours');
        if (type == 'project') {
            if (wrap_task_hours.hasClass('project-task-hours') == true) {
                wrap_task_hours.removeClass('hide');
            } else {
                wrap_task_hours.addClass('hide');
            }
            wrap.addClass('hide');
            $('.project-details').removeClass('hide');
        } else {
            wrap_task_hours.removeClass('hide');
            wrap.removeClass('hide');
            $('.project-details').addClass('hide');
        }

    }

    function _task_rel_select() {
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        init_ajax_search(_rel_type.val(), _rel_id, serverData);
    }
</script>