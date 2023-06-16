<?php
$CI = &get_instance();
$model = $CI->load->model('departments_model');
$departments = $model->departments_model->get();
$own_departments = $model->departments_model->get_staff_departments();
$staff_departments = $departments;
$assigned = 0;

if (count($staff_departments) == 0) {
     echo '<script>$(function(){
            alert_float("warning","' . _l('chat_no_departments_found') . '");
        });</script>';
     die;
}

?>
<div class="modal right fade" data-backdrop="static" data-keyboard="false" id="convert_to_ticket_modal" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-lg" role="any">
          <div class="modal-content">
               <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= _l('chat_convert_messages_to_ticket'); ?></h4>
               </div>
               <div class="modal-body" id="converToTicketModal">
                    <?php ?>
                    <h4 class="mbot20  text-center"><?= _l('chat_your_todays_ticket_messages'); ?><strong><?= $user_full_name; ?></strong></h4>
                    <div class="mutual_messages_select_view" style="padding-top:10px;">
                    </div>
                    <?php if (
                         $messages !== 'null'
                         && get_option('chat_allow_staff_to_create_tickets', PR_CHAT_MODULE_NAME) == 1
                         || is_admin()
                         && is_staff_member()
                    ) : ?>

                         <?php
                         if (!is_admin()) {
                              if ((sizeof($own_departments) > 0)) {
                                   $staff_departments = $own_departments;
                                   $assigned = 1;
                              }
                         }

                         echo render_select(
                              'department',
                              $staff_departments,
                              array('departmentid', array('name')),
                              _l('department'),
                              '',
                              [],
                              [],
                              '',
                              '',
                              false
                         );
                         ?>
                         <button class="btn btn-info btn-xs" id="convertToTicket"><?= _l('chat_convert_to_ticket_label'); ?></button>
                         <button type="button" class="btn btn-warning pull-right" data-dismiss="modal"><?= _l('dismiss'); ?></button>
                    <?php endif; ?>
               </div>
          </div>
     </div>
</div>
<?php if (get_option('chat_allow_staff_to_create_tickets') == 1 || is_admin() && is_staff_member()) :  ?>
     <script>
          var chat_ticket_assigned = "<?= $assigned; ?>";

          (function($) {

               "use strict";
               init_selectpicker();

               var messages_history = JSON.parse(ifNewLinesReplaceWithBreak('<?= json_encode($messages); ?>'));
               var messageHistoryParent = $('.mutual_messages_select_view');
               var messagesCountSelector = $('#convert_to_ticket_modal .modal-body');

               if (!$.isEmptyObject(messages_history)) {

                    renderMessagesToView(messages_history);
                    messagesCountSelector.prepend(showMessagesRecords());

               }

               /** Replaces \n with break because JSON.parse blows and scripts crashes JSON cant use multiple new and be rendered instantly lines */
               function ifNewLinesReplaceWithBreak(str) {
                    return str.replace(/\n/g, "<br>").replace(/\r/g, "\\\\r").replace(/\t/g, "\\\\t");
               }


               function renderMessagesToView(messages) {
                    var html = '';
                    $.each(messages, function(i, msg) {

                         // We dont want to show notify links(messages) for already created support tickets
                         if (msg.message.includes('quot;chat_ticket_link&amp;quot') &&
                              msg.message.endsWith('&amp;lt;/a&amp;gt;')) {
                              // If condition is true skip this message
                              return;
                         }

                         var sender_fullname = msg.sender_fullname ? msg.sender_fullname : msg.contact_fullname;

                         if (msg.sender_id === userSessionId || msg.sender_id == 'staff_' + userSessionId) {

                              html += '<div data-user="' + userSessionId + '" class="checkbox chat_ticket_messages mleft10"><span class="pull-left"><strong>' + sender_fullname + '</strong><small class="pull-right">' + msg.time_sent + '</small></span><br><input type="checkbox" name="ticket_messages" class="no-padding no-margin" id="message_' + msg.id + '" ><label for="message_' + msg.id + '">' + decodeChatMessageHtml(msg.message) + '</label></div>';
                         } else {
                              html += '<div data-user="' + msg.sender_id + '" class="checkbox chat_ticket_messages mleft10"><span class="pull-left text-info"><strong>' + sender_fullname + '</strong><small class="pull-right text-dark">' + msg.time_sent + '</small></span><br><input type="checkbox" name="ticket_messages" class="no-padding no-margin" id="message_' + msg.id + '" ><label for="message_' + msg.id + '">' + decodeChatMessageHtml(msg.message) + '</label></div>';
                         }
                    });

                    if (html === '') {
                         messageHistoryParent.html('<h3 class="text-center">' + "<?= _l('chat_sorry_no_data'); ?>" + '</h3>');
                         return;
                    }

                    messageHistoryParent.html($.parseHTML(html));
               }

               function showMessagesRecords() {
                    var recordsFound = '';

                    recordsFound += '<div class="alert alert-info text-center" role="alert">';
                    recordsFound += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                    recordsFound += '<span aria-hidden="true">&times;</span></button>';
                    recordsFound += '<?= _l('chat_total_ticket_messages'); ?>' + '<strong>' + <?= $this->session->userdata('chat_support_ticket_messages_count'); ?> + '</strong>';

                    return recordsFound;
               }
          })(jQuery);
     </script>
<?php endif; ?>