  <div class="modal fade" data-backdrop="static" id="chat_groups_custom_modal" tabindex="-1" role="dialog">
    <form method="POST" id="members_form">
      <div class="modal-dialog" role="any">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= isset($title) ? $title : ''  ?></h4>
          </div>
          <div class="modal-body">
            <h4 align="center" class="mbot20"><?= _l('chat_message_groups'); ?></h4>
            <input type="hidden" class="ays-ignore" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label for="group_name" class="mtop20"><?= _l('chat_groups_channel_text'); ?></label>
              <input type="text" name="group_name" class="form-control group_name" placeholder="<?= _l('chat_groups_channel_text'); ?>">
            </div>
            <div class="form-group">
              <label class="mtop20"><?= _l('chat_select_members_info'); ?></label>
              <select data-none-selected-text="<?= _l('chat_non_selected_member_text'); ?>" data-actions-box="true" id="members" name="members[]" multiple class="form-control">
                <?php
                if (is_array($staff) && !empty($staff)) {
                  foreach ($staff as $member) : if (get_staff_user_id() == $member['staffid']) continue;  ?>
                    <option data-subtext="<?= ($member['admin'] ? 'admin' : ''); ?>" data-icon="fa fa-user-o" value="<?= $member['staffid'] ?>"><?= $member['firstname'] . ' ' . $member['lastname']; ?>
                    </option>
                <?php endforeach;
                } ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?= _l('chat_message_groups_text'); ?></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </form>
  </div><!-- /.modal -->


  <script>
    (function($) {
      "use strict";
      // Select picker settings
      $('#members').selectpicker({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '400px'
      });

      // On submit event
      $('#members_form').on('submit', function(e) {
        e.preventDefault();
        // Additional security checks
        if (staffCanCreateGroups === '0' && !isAdmin) {
          alert_float('warning', "<?php echo _l('access_denied'); ?>");
          setTimeout(function() {
            window.location = site_url + 'admin/prchat/Prchat_Controller/chat_full_view';
          }, 1600);
          return false;
        }

        appValidateForm($("#members_form"), {
          group_name: "required",
          'members[]': {
            required: true,
            minlength: 1
          }
        });

        var formData = $(this).serializeArray();
        var group_name = $.trim($('#members_form input[name=group_name]').val());
        var select = $('#members_form select#members').val();

        if (group_name == '') {
          alert_float('warning', "<?php echo _l('chat_group_name_required'); ?>");
          return false;
        }
        if (select.length == 0) {
          alert_float('warning', "<?php echo _l('chat_group_member_is_required'); ?>");
          return false;
        }

        // Send data to controller
        $.ajax({
          url: prchatSettings.addChatGroup,
          method: "POST",
          data: formData,
          beforeSend: function() {
            $('.btn-info').attr('disabled', 'disabled');
          },
          success: function(r) {
            if (r !== '') {
              r = JSON.parse(r)
              if (r.message) {
                alert_float('warning', r.message);
                return false;
              }
              if (r.data.result === 'success') {
                $('#members option:selected').each(function() {
                  $(this).prop('selected', false);
                });

                $('#members').selectpicker('refresh');

                $('#chat_groups_custom_modal').modal('hide');
                $('#chat_groups_custom_modal').on('hidden.bs.modal', function() {
                  alert_float('warning', r.data.message);
                  $('#frame #sidepanel .groups a').click();
                  $('.message-input.group_msg_input').show();
                });
                appendChatGroup(r.data);
              }
            }
          }
        });
      });

      $('#chat_groups_custom_modal input').keydown(function(e) {
          // if (e.keyCode == 32) {
          //   return false;
          // }
      });
    })(jQuery);
  </script>