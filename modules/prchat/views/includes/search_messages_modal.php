<div class="modal right fade" id="search_messages_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="any">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= _l('chat_messages_history_title'); ?></h4>
            </div>
            <div class="modal-body">
                <?php ?>
                <h4 class="mbot20  text-center"><?= _l('chat_search_mutual_messages'); ?> <strong><?= $user_full_name; ?></strong></h4>
                <input type="text" placeholder="<?= _l('chat_messages_search_here'); ?>" id="search_mutual_messages" class="form-control">
                <div class="chat_message_history_view" style="padding-top:10px;">
                </div>
                <?php if ($messages !== 'null' && is_admin()) : ?>
                    <button class="btn btn-danger" id="deleteConversation" onClick="deleteConversation(<?php echo $id; ?>)">
                        <?= _l("chat_delete_conversation"); ?>
                    </button>
                    <a class="btn btn-primary" id="exportConversation" href="<?= admin_url('prchat/Prchat_Controller/exportCSV?user=' . $id); ?>"><?= _l("chat_export_messages_label"); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";

        var messages_history = JSON.parse('<?= $messages; ?>');
        var messageHistoryParent = $('.chat_message_history_view');

        if (!$.isEmptyObject(messages_history)) {

            messageHistoryParent.html(showMessagesRecords());

            $('body').on('keydown', '#search_mutual_messages', _debounce(function() {

                var input = $.trim($(this).val());
                var render = [];

                if (input.length) {
                    for (var i = 0; i < messages_history.length; i++) {
                        for (var key in messages_history[i]) {
                            if (messages_history[i].message.toLowerCase().matchAll(input.toLowerCase()) &&
                                !render.includes(messages_history[i])) {
                                render.push(messages_history[i]);
                            }
                        }
                    }
                    appendResultsToModal(render);
                } else {
                    input = '';
                    messageHistoryParent.html('');
                }
            }, 500));
        } else {
            messageHistoryParent.html('<h3 class="text-center">' + "<?= _l('chat_sorry_no_data'); ?>" + '</h3>');
            $('#deleteConversation, #exportConversation').remove();
        }

        function appendResultsToModal(data) {
            renderMessagesToView(data);
        };

        function renderMessagesToView(messages) {
            var html = '';
            $.each(messages, function(i, msg) {
                var sender_fullname = msg.sender_fullname ? msg.sender_fullname : msg.contact_fullname;

                if (msg.sender_id === userSessionId || msg.sender_id == 'staff_' + userSessionId) {
                    html += '<div class="panel panel-primary chat_message_history_own" data-toggle="tooltip" title="' + msg.time_sent + '">';
                    html += '<div class="panel-heading"><small>' + moment(msg.time_sent, "YYYYMMDD h:mm:ss").fromNow() + '</small> <h3 class="panel-title">' + sender_fullname + '</h3></div>';
                    html += '<div style="border: 1px solid #ababab;padding: 10px;border-radius: 6px;"><span class="message_content">' + decodeChatMessageHtml(msg.message) + '<span></div> </div>';
                } else {
                    html += '<div class="panel panel-primary chat_message_history_other" data-toggle="tooltip" title="' + msg.time_sent + '">';
                    html += '<div class="panel-heading"><small>' + moment(msg.time_sent, "YYYYMMDD h:mm:ss").fromNow() + '</small> <h3 class="panel-title">' + sender_fullname + '</h3></div>';
                    html += '<div style="border: 1px solid #ababab;padding: 10px;border-radius: 6px;"><span class="message_content">' + decodeChatMessageHtml(msg.message) + '</span></div> </div>';
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
            recordsFound += '<?= _l('chat_total_mutual_messages_label'); ?><strong>' + <?= $this->session->userdata('chat_messages_count'); ?> + '</strong></div>';

            return recordsFound;
        }

        $('#search_messages_modal').on('show.bs.modal', function(e) {
            if ($('.modal-backdrop.fade').hasClass('in')) {
                $('.modal-backdrop.fade').remove();
            }
            setTimeout(function() {
                $(".modal_container input").focus();
            }, 750);
        });
    })(jQuery);



    function deleteConversation(id) {
        var table = '';

        if (typeof(id) == 'number') {
            table = 'chatmessages';
        }

        if (typeof(id) == 'object') {
            table = 'chatclientmessages';
            id = $(id).children('li.selected').attr('id') || $(id).attr('id');
            if (id.startsWith('client_')) {
                id = id.replace('client_', '');
            }
        }

        var lang_confirm_deletion = confirm("<?= _l('chat_are_you_sure_delete_conversation'); ?>");

        var hreff = $('#exportConversation').attr('href');

        if (lang_confirm_deletion) {
            if (confirm("<?= _l('chat_do_you_want_to_export'); ?>")) {
                $.ajax({
                    url: hreff,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        let currDate = new Date().toDateString().replace(/\s/g, '');
                        let a = document.createElement('a');
                        let url = window.URL.createObjectURL(data);
                        a.href = url;
                        a.download = 'messsages_' + currDate + '.csv';
                        document.body.append(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);
                        _deleteUserMessages(id, table);
                    }
                });
            } else {
                _deleteUserMessages(id, table);
            }
        }

        function _deleteUserMessages(id, table) {
            $.post('deleteChatConversation', {
                id: id,
                table: table,
                beforeSend: function() {
                    $('#deleteConversation').attr('disabled', true);
                    $('#deleteConversation').html("<?= _l('chat_deleting_message') ?><i class='fa fa-refresh fa-spin fa-fw'></i>");
                },
            }).done(function(r) {
                if (r.success == true) {
                    setTimeout(function() {
                        alert_float('success', "<?php echo _l('chat_conversation_deleted'); ?>");
                        location.reload();
                    }, 2000);
                }
            });
        }
    }
</script>
