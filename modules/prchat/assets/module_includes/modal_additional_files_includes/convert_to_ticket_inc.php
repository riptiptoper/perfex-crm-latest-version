<script>
    $("#convert_to_ticket_modal").on("show.bs.modal", function (e) {
        const hasClass = $(".modal-backdrop.fade").hasClass;
        if (hasClass("in")) {
            hasClass.remove();
        }
    });

    let _body = $("body");

    var filteredTicketMessages = [];

    _body.on("change", "#convert_to_ticket_modal input[type=\"checkbox\"]", function () {

        let message_id = $(this).parents(".chat_ticket_messages").children("input").attr("id").replace("message_", "");
        let user_id = $(this).parents(".chat_ticket_messages").data("user");
        let client_id = $(".client_messages").attr("id");
        let ticket_message = $(this).parents(".chat_ticket_messages").children("label").text();
        let user_name = $(this).parents(".chat_ticket_messages").children("span").children("strong").text();

        let message_content = {
            "user_id": user_id,
            "message_id": message_id,
            "message": ticket_message,
            "client_id": client_id,
            "user_name": user_name
        };

        if ($(this).prop("checked")) {
            // Add the new ticket message if checked:
            if (filteredTicketMessages.indexOf(message_content.message_id) < 0) {
                filteredTicketMessages.push(message_content);
            }
        } else {
            // Remove from array if ticket message is unchecked
            filteredTicketMessages = filteredTicketMessages.filter(function (item, index) {
                return item.message_id !== message_content.message_id;
            });
        }
    });

    _body.on("click", "#convertToTicket", function () {
        const csrf = (typeof csrfData == "undefined") ? "" : csrfData.formatted.csrf_token_name;
        let department = $("select[name=\"department\"] option:selected").val();

        if (filteredTicketMessages.length === 0) {
            alert_float("warning", '<?= _l('chat_at_least_one_message_required'); ?>');
            return;
        }

        var subject = prompt('<?= _l('chat_type_in_ticket_subject'); ?>');

        if (subject == null) {
            return;
        } else if ($.trim(subject) === "") {
            alert_float("warning", '<?= _l('chat_ticket_subject_empty'); ?>');
            return;
        }

        subject = escapeHtml(subject);

        if (subject !== "") {
            $.post("createNewSupportTicket", {
                content: filteredTicketMessages,
                department: department,
                subject: subject,
                csrf: csrf,
                assigned: chat_ticket_assigned,
                beforeSend: function () {
                    _body.prepend("<div class=\"ticket_form_loading_spinner\"></div>");
                    $("#convertToTicket").html('<?= _l('chat_please_wait_creating_ticket'); ?> <i class="fa fa-refresh fa-spin fa-fw"></i>');
                }
            }).done(function (r) {
                r = JSON.parse(r);

                if (r.message === "no_message") {
                    alert_float("warning", "<?= _l('chat_at_least_one_message_required'); ?>");
                    return;
                }

                if (r.message === "success") {
                    $(".ticket_form_loading_spinner").remove();
                    $("#convert_to_ticket_modal").modal("hide");

                    alert_float("success", "<?= _l('chat_new_ticket_created'); ?>" + subject);

                    setTimeout(function () {
                        let _clientChatbox = $(".client_chatbox");
                        _clientChatbox.val('<?= _l('chat_client_created_new_support_ticket'); ?><a class="chat_ticket_link" href="' + location.origin + "/clients/ticket/" + r.ticket_id + '" target="_blank"><?= _l('support_ticket'); ?></a>');

                        _clientChatbox.trigger($.Event("keypress", {
                            which: 13
                        }));
                    }, 1000);
                }
                $.post("<?= site_url('prchat/Prchat_ClientsController/trigger_ticket_event'); ?>", {
                    client_id: r.client_id,
                    ticket_id: r.ticket_id
                }).done(function (r) {
                    filteredTicketMessages = [];
                });
            }).fail(function (error) {
                // Ticket is created but due to email templates sending emails adds and error sometimes
                alert_float("success", '<?= _l('chat_new_ticket_created'); ?>' + subject);
                $("#convert_to_ticket_modal").modal("hide");
                $(".ticket_form_loading_spinner").remove();
                filteredTicketMessages = [];
            });
        }
    });
</script>