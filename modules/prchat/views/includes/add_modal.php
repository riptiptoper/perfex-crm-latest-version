  <div class="modal fade" data-backdrop="static" id="add_members_modal" tabindex="-1" role="dialog">
    <form method="POST" id="members_form_add">
      <div class="modal-dialog" role="any">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= isset($title) ? $title : ''  ?></h4>
          </div>
          <div class="modal-body">
            <h4 align="center" class="mbot20"><?= _l('chat_select_members_info'); ?></h4>
            <input type="hidden" class="ays-ignore" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label class="mtop20"><?= _l('chat_group_modal_add_title'); ?></label>
              <select data-none-selected-text="<?= _l('chat_non_selected_member_text'); ?>" data-actions-box="true" id="members" name="members[]" multiple class="form-control">
                <?php
                if (is_array($staff) && !empty($staff)) {
                  foreach ($staff as $member) : if (get_staff_user_id() == $member['staffid']) {
                    continue;
                  } ?>
                  <option data-subtext="<?= ($member['admin'] ? 'admin' : ''); ?>" data-icon="fa fa-user-o" value="<?= $member['staffid'] ?>"><?= $member['firstname'] . ' ' . $member['lastname']; ?>
                </option>
              <?php endforeach;
            } ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
        <button type="submit" class="btn btn-info"><?= _l('chat_add_members'); ?></button>
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
      $('#members_form_add').on('submit', function(event) {
        event.preventDefault();
        var group_name = '';
        var group_id = '';

        group_name = $('#frame .chat_groups_list li.group_selector.active').data('channel');
        group_id = $('#frame .chat_groups_list li.group_selector.active').attr('id');

        var select = $('#members_form_add select#members').val();

        if (select.length == 0) {
          alert_float('warning',"<?= _l('chat_group_member_is_required'); ?>");
          return false;
        }

        var formData = $(this).serializeArray();
        formData.push({
          name:'group_name',
          value:group_name
        },
        {
          name:'group_id',
          value:group_id
        });

        // Send data to controller
        $.ajax({
          url: prchatSettings.addChatGroupMembers,
          method: "POST",
          data: formData,
          beforeSend: function() {
            $('.btn-info').attr('disabled','disabled');
          },
          success: function(response) {
            if(response !== ''){
              response = JSON.parse(response);

              if(response.data.result !== 'success'){
                alert_float('warning',"<?= _l('chat_error_float'); ?>");
                return false;
              }

              if (response.data.result == 'success') {
                $('#members option:selected').each(function() {
                  $(this).prop('selected', false);
                });

                $('#members').selectpicker('refresh');

                $('#add_members_modal').modal('hide');
              }
            }
          }
        });
      });
      $('#add_members_modal input').keydown(function(e) {
        if (e.keyCode == 32) {
          return false;
        }
      });
    })(jQuery);
  </script>