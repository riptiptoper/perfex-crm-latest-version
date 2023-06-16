<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$isHttps = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? $isHttps = true : false);

if ($isHttps) {
    loadChatComponent('AudioComponent');
}
?>
    <div class="ch_pointer">
        <div id="ch_pointer-main" class="ch_pointer-main">
            <div class="chatNewNotification"></div>
            <span class="ch_pointer-main-first">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <g>
                    <path d="M294.1,365.5c-2.6-1.8-7.2-4.5-17.5-4.5H160.5c-34.7,0-64.5-26.1-64.5-59.2V201h-1.8C67.9,201,48,221.5,48,246.5v128.9
                        c0,25,21.4,40.6,47.7,40.6H112v48l53.1-45c1.9-1.4,5.3-3,13.2-3h89.8c23,0,47.4-11.4,51.9-32L294.1,365.5z">
                    </path>
                    <path d="M401,48H183.7C149,48,128,74.8,128,107.8v69.7V276c0,33.1,28,60,62.7,60h101.1c10.4,0,15,2.3,17.5,4.2L384,400v-64h17
                        c34.8,0,63-26.9,63-59.9V107.8C464,74.8,435.8,48,401,48z">
                    </path>
                </g>
            </svg>
        </span>

            <span class="ch_pointer-main-under">
            <span class="ch_pointer-main-prefix">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="512px" height="512px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                    <path d="M437.5,386.6L306.9,256l130.6-130.6c14.1-14.1,14.1-36.8,0-50.9c-14.1-14.1-36.8-14.1-50.9,0L256,205.1L125.4,74.5
						c-14.1-14.1-36.8-14.1-50.9,0c-14.1,14.1-14.1,36.8,0,50.9L205.1,256L74.5,386.6c-14.1,14.1-14.1,36.8,0,50.9
                        c14.1,14.1,36.8,14.1,50.9,0L256,306.9l130.6,130.6c14.1,14.1,36.8,14.1,50.9,0C451.5,423.4,451.5,400.6,437.5,386.6z">
                    </path>
                </svg>
            </span>
        </span>
        </div>
    </div>
    <div id="clientChat">
        <div class="firstDiv">
            <div class="company_top_info">

                <div class="top_close_icon">
                    <svg fill="#FFFFFF" height="15" viewBox="0 0 15 15" width="15" xmlns="http://www.w3.org/2000/svg" class="closeIcon">
                        <line x1="1" y1="15" x2="15" y2="1" stroke="white" stroke-width="1"></line>
                        <line x1="1" y1="1" x2="15" y2="15" stroke="white" stroke-width="1"></line>
                    </svg>
                </div>

                <div>
                    <div class="company_top_info_parent">
                        <div class="company_top_info_first_child">
                            <div class="company_holder">
                                <div class="company_logo_placeholder">
                                    <?= get_company_logo(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="customer_admins_wrapper">
                <div class="customer_first_co_wrapper">
                    <div>
                        <div>
                            <div class="customer_main_placeholder_top">
                                <h2 class="staff_online_text"><?= _l('chat_clients_assigned_admins'); ?></h2>
                                <div class="staff_info_wrapper_div">
                                    <div class="staff_muted_text_info"><?= _l('chat_clients_choose_and_start'); ?></div>
                                </div>
                                <div class="staff_image_parent">
                                    <div class="staff_image_wrapper">
                                        <!-- Handled on the fly -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clientwrapper">
                <div class="m-area" data-staffid="" onscroll="loadMessages(this)" id="">
                    <svg class="message_client_loader" viewBox="0 0 50 50">
                        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                    </svg>
                    <ol class="chat">
                        <!-- Handled on the fly -->
                    </ol>
                </div>
                <span class="userIsTyping" id="">
                <img src="<?php echo module_dir_url('prchat', 'assets/chat_implements/userIsTyping.gif'); ?>" />
            </span>
                <div class="placeholder-messages">
                    <form hidden enctype="multipart/form-data" name="staffMessagesFileForm" id="staffMessagesFileForm" method="post" onsubmit="uploadClientFileForm(this);return false;">
                        <input type="file" class="file" name="userfile" required />
                        <input type="submit" name="submit" class="save" value="save" />
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    </form>
                    <form method="post" enctype="multipart/form-data" id="staffMessagesForm" onsubmit="return false;">
                        <i class="fa fa-paper-plane send_client_message" aria-hidden="true"></i>
                        <textarea class="clients_textarea ays-ignore" placeholder="<?= _l('chat_type_a_message'); ?>" autocomplete="off"></textarea>
                        <input type="hidden" class="ays-ignore from" name="from" value="" />
                        <input type="hidden" class="ays-ignore to" name="to" value="" />
                        <input type="hidden" class="ays-ignore typing" name="typing" value="false" />
                        <input type="hidden" class="ays-ignore" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <?php loadChatComponent('MicrophoneIcon'); ?>
                        <i class="fa-regular fa-file-image attachment fileUpload" data-container="body" data-toggle="tooltip" title="<?php echo _l('chat_file_upload'); ?>" aria-hidden="true"></i>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require 'modules/prchat/assets/module_includes/mutual_and_helper_functions.php'; ?>
    <script>
        var clientsArea = $(".clients_textarea");
        var contact_id = "<?= get_contact_user_id(); ?>";
        var client_id = "<?= get_client_user_id(); ?>";
        var contact_company_name = document.getElementsByTagName("title")[0].innerHTML;
        var pusherKey = "<?= get_option('pusher_app_key') ?>";
        var appCluster = "<?= get_option('pusher_cluster') ?>";
        var customerAdmins = JSON.parse('<?php get_customer_admins(); ?>');
        var contact_name_id = "client_" + contact_id;
        var contact_full_name = $(".customers-nav-item-profile a img").data("title");
        var checkForStaffUnreadMessages = $.getJSON(customerSettings.getStaffUnreadMessages);
        var offsetPush = 0;
        var groupOffsetPush = 0;
        var endOfScroll = false;
        var currentStaff;


        /*---------------* Put customerSettings.debug for debug mode for Pusher *---------------*/
        if (customerSettings.debug) {
            try {
                Pusher.log = function (message) {
                    if (window.console && window.console.log) {
                        window.console.log(message);
                    }
                };
            } catch (e) {
                if (e instanceof ReferenceError) {
                    alert_float("danger", e);
                }
            }
        }
        /*---------------* Init pusher library, and register *---------------*/
        var pusher = new Pusher(pusherKey, {
            authEndpoint: customerSettings.clientPusherAuth,
            authTransport: "jsonp",
            "cluster": appCluster,
            disableStats: true,
            auth: {
                headers: {
                    "X-CSRF-Token": (typeof csrfData == "undefined") ? "" : csrfData.formatted.csrf_token_name, // CSRF token
                }
            }
        });

        /*---------------* Pusher Trigger accessing channel *---------------*/
        var clientsChannel = pusher.subscribe("presence-clients");

        /*---------------* Pusher Trigger Message Seen / Unseen *---------------*/
        var user_messages_events = pusher.subscribe("user_messages");

        /*---------------* Member array for online / offline activity *---------------*/
        var pendingRemoves = [];

        clientsChannel.bind("pusher:subscription_succeeded", function (member) {
            pushNewMember();
        });

        /*---------------* Pusher Trigger user logout *---------------*/
        clientsChannel.bind("pusher:member_removed", function (members) {
            removeChatMember(members);
        });

        /*---------------* Pusher Trigger user connected *---------------*/
        clientsChannel.bind("pusher:member_added", function (member) {
            addChatMember(member);
        });
        document.addEventListener("DOMContentLoaded", function () {

        });

        /*---------------* New staff member activity online / offline  *---------------*/
        function pushNewMember()
        {
            $.each(customerAdmins, function (i, admin) {
                var user = clientsChannel.members.get(admin.staffid);
                if (user !== null) {
                    $(".staff_container#staff_" + user.id).find(".staff_children_parent_child_div").addClass("onlineStaff");
                }
            });
        }

        /*---------------* New chat members tracking / removing *---------------*/
        function addChatMember(member)
        {
            var pendingRemoveTimeout = pendingRemoves[member.id];
            $("div#staff_" + member.id).find(".staff_children_parent_child_div").addClass("onlineStaff");

            if (pendingRemoveTimeout) {
                clearTimeout(pendingRemoveTimeout);
            }
        }

        /*---------------* New chat members tracking / removing from channel and UX*---------------*/
        function removeChatMember(member)
        {
            pendingRemoves[member.id] = setTimeout(function () {
                $("div#staff_" + member.id).find(".staff_children_parent_child_div").removeClass("onlineStaff");
            }, 5000);
        }


        /*---------------* Addend customer admins in view *---------------*/
        function appendCustomerAdmins()
        {
            var dfd = jQuery.Deferred();
            var promises = [];
            var counter = 1;

            if (Object.keys(customerAdmins).length === 0) {
                console.log("No admins or customer admins found");
                return false;
            }

            $.each(customerAdmins, function (i, admin) {
                var fullname = admin.firstname + " " + admin.lastname;
                var adminImage = "";

                adminImage += "<div class =\"staff_container\" id=\"staff_" + admin.staffid + "\">";
                adminImage += "<div class =\"staff_image_second_children_div\">";
                adminImage += "<div class =\"staff_notification\" data-notification=\"0\"></div>";
                adminImage += "<div class = \"staff_children_parent_child_div\" ><img src=\"" + fetchUserAvatar(admin.staffid, admin.profile_image) + "\" data-toggle=\"tooltip\" data-placement=\"left\" data-html=\"true\" data-original-title=\"" + fullname + "  <br>" + admin.role + "\"/></div>";
                adminImage += "</div></div>";

                $(".staff_image_wrapper").append(adminImage);

                counter++;
                promises.push(counter);
            });

            var topStaff = $(".staff_image_wrapper .staff_container:first").attr("id");
            $(".m-area").attr("data-staffid", topStaff.replace("staff_", ""));
            currentStaff = $(".m-area").data("staffid");

            if (counter === counter) {
                dfd.resolve(counter);
            }
        }

        /*---------------* Promise after admins are appended to view *---------------*/
        $.when(appendCustomerAdmins()).then(
            function () {
                $(".staff_container").first().trigger("click");
                checkForStaffUnreadMessages.done(function (r) {
                    if (!r.null) {
                        $.each(r, function (i, sender) {
                            $("body").find("div#" + sender.sender_id)
                                .find(".staff_notification")
                                .attr("data-notification", sender.count_messages)
                                .show();
                        });
                    }
                });
            }
        );

        /*---------------* Function that handles updating unread messages  *---------------*/
        function updateUnreadNotifications(id)
        {
            $.post(customerSettings.updateStaffUnread, {
                id: id,
                client: "client"
            });
            clearNotifications();
        }

        /*---------------* Paperplane button send event click  *---------------*/
        $(".send_client_message").on("click", function () {
            clientsArea.trigger($.Event("keypress", {
                which: 13
            }));
        });

        /*---------------* Event click  and function handlers for textarea to check for unread messages *---------------*/
        $("body").on("click", ".clients_textarea, .m-area", function (e) {
            checkForUnreadMessages();
        });

        function checkForUnreadMessages()
        {
            var staff_id = $(".m-area").attr("data-staffid");
            var lastMessage = $(".m-area li:last");
            var notification = $(".active_staff").prev().data("notification");
            var activeStaffId = $(".active_staff").parents(".staff_container").attr("id");

            if (lastMessage.hasClass("customer_admin") && notification > 0 || staff_id == activeStaffId) {
                updateUnreadNotifications(staff_id);
            }
        }

        /*---------------* Ul li customers click event *---------------*/

        $(".ch_pointer-main-first").click(function () {
            scrollBottom();
        });

        $(".ch_pointer-main, .top_close_icon").on("click", function () {
            if ($(".chatNewNotification").length > 0) {
                resetChatNotifications();
            }
            $(".firstDiv").toggle(400);
            scrollBottom();
            $(".ch_pointer .ch_pointer-main-first").toggle(350);
            $(".ch_pointer .ch_pointer-main-under").toggle(550);
        });

        /*---------------* Function that handles message sending send and typing events *---------------*/
        clientsArea.on("keypress", function (e) {
            var form = $(this).parents("form#staffMessagesForm");

        if (e.which == 13 && !e.shiftKey) {
                e.preventDefault();

                var message = $.trim($(this).val());

                if ($.trim(message) == "" || internetConnectionCheck() === false) {
                    return false;
                }

                messageToAppend = "";
                messageToAppend += "<li class=\"client isUnseenPadding\">";
                messageToAppend += "<div class=\"msg\"><span class=\"client_name\">" + contact_full_name + "</span>";
                messageToAppend += "<p>" + createTextLinks_(emojify.replace(message)) + "</p>";
                messageToAppend += '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
                messageToAppend += "</div>";
                messageToAppend += "</li>";

                $(".m-area .chat").append(messageToAppend);

                $(this).next().next().next().val("false");
                message = escapeHtml(message);

                var formData = form.serializeArray();

                formData.push({
                    name: "client_message",
                    value: message
                }, {
                    name: "client_id",
                    value: client_id
                }, {
                    name: "contact_full_name",
                    value: contact_full_name
                }, {
                    name: "company",
                    value: contact_company_name
                });

                // send event
                $.post(customerSettings.clientsMessagesPath, formData);
                $(this).val("").focus();
                scrollBottom();
                $(".clients_textarea").val("");

                // scroll_event();
            } else if ($(this).val() == "" || ($(this).next().next().next().val() == "null" && $(this).val() == "")) {

                $(this).next().next().next().val("true");
                $.post(customerSettings.clientsMessagesPath, form.serializeArray());
            }
        });

        /*---------------* Event that is binded to typing event with pusher webockets *---------------*/
        clientsChannel.bind("typing-event", function (data) {
            var clearTypingInterval = 2500; // 2.2 seconds
            var clearTypingTimerId;
            var clientWrapper = $(".clientwrapper");
            if (data.from == currentStaff && data.to == contact_name_id && data.message == "true") {
                clientWrapper.find(".userIsTyping").fadeIn(500);
                clearTimeout(clearTypingTimerId);
                clearTypingTimerId = setTimeout(function () {
                    clientWrapper.find(".userIsTyping").fadeOut(500);
                }, clearTypingInterval);
            } else if (data.from == currentStaff && data.to == contact_name_id && data.message == "null") {
                clientWrapper.find(".userIsTyping").fadeOut(500);
            }
        });

        /*---------------* Event that is binded to send event with pusher webockets *---------------*/
        clientsChannel.bind("send-event", function (data) {
            $(".clientwrapper").find(".userIsTyping").fadeOut(500);
            var isChatMinimized = $("body").find(".firstDiv").is(":hidden");

            if (data.from == currentStaff && data.to == contact_name_id) {

                if (isChatMinimized) {
                    showChatNotification();
                    initClientSound(data);
                }

                data.message = createTextLinks_(emojify.replace(data.message));
                data.message = isAudioMessage(data.message);

                var msgHtml = "<li class=\"customer_admin\"><div class=\"msg\"><span class=\"admin_name\">";
                msgHtml += data.from_name + "</span><p class=\"staff_message \">" + data.message + "</p></li></div>";

                $(".m-area ol.chat").append(msgHtml);
                scrollBottom();
            }

            if (data.from !== currentStaff) {
                var notifyStaff = $("#" + data.from + " .staff_notification");
                var notification = parseInt(notifyStaff.attr("data-notification"));
                notifyStaff.attr("data-notification", notification + 1).show();
            }

        });

        /*---------------* Init current chat loader synchronized with client messages append *---------------*/
        function activateClientsLoader(promise = null)
        {
            if (promise !== null) {
                var initLoader = $(".m-area");
                if (initLoader.find(".message_client_loader").show()) {
                    promise.then(function () {
                        initLoader.find(".message_client_loader").hide();
                    });
                }
                ;
            }
        }

        /*---------------* Functions that handles staff information and messages in view *---------------*/
        $("body").on("click", ".staff_container", function () {
            endOfScroll = false;
            offsetPush = 0;
            var staff_id = $(this).attr("id");
            var clName = "active_staff";
            var staff_chpd = ".staff_children_parent_child_div";
            currentStaff = staff_id;

            $(".m-area").attr("data-staffid", staff_id);
            $(".m-area").attr("id", staff_id);

            $(".staff_container " + staff_chpd).removeClass("active_staff");

            $(this).find(staff_chpd).addClass("active_staff");

            $("#staffMessagesForm .to").val(staff_id);

            $("#staffMessagesForm .from").val("client_" + contact_id);

            checkForUnreadMessages();
            appendMutualMessages(staff_id, contact_id);
        });

        function fetchStaffMessages(staffid, cid)
        {
            $.get(customerSettings.getMutualMessages, {
                reciever_id: staffid,
                sender_id: "client_" + cid
            })
                .done(function (data) {
                    appendMutualMessages(data);
                });
        }

        /**
         * Function that handles customer admins and contacts conversation messages
         */
        function prependContactMessages(value)
        {

            var element = $(".m-area ol.chat");
            var closeDivLi = "</div></li>";
            var msgDiv = "<div class=\"msg\" data-toggle=\"tooltip\" title=\"" + value.time_sent_formatted + "\">";
            var theMessage = "<p>" + createTextLinks_(emojify.replace(value.message)) + "</p>";
            var messageContainer = "";
            var isViewed = value.viewed == 1;
            var isRead = "";
            var classUnseen = "";

            if (!isViewed) {
                isRead = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
                classUnseen = "isUnseenPadding";
            }

            theMessage = "<p>" + isAudioMessage(value.message) + "</p>";

            if (value.reciever_id == contact_name_id) {
                messageContainer += "<li class=\"customer_admin\">";
                messageContainer += msgDiv;
                messageContainer += "<span class=\"admin_name\">" + value.sender_fullname + "</span>";
                messageContainer += theMessage;
                messageContainer += closeDivLi;
            } else {
                messageContainer += "<li class=\"client " + classUnseen + "\" id=\"" + value.id + "\">";
                messageContainer += msgDiv;
                messageContainer += "<span class=\"client_name\">" + contact_full_name + "</span>";
                messageContainer += theMessage;
                messageContainer += isRead;
                messageContainer += closeDivLi;
            }
            element.prepend(messageContainer);
        }


        /*---------------* Check for messages history and append to main chat window *---------------*/
        function loadMessages(el)
        {
            var pos = $(el).scrollTop();
            var staff_id = $(el).attr("data-staffid");

            $(".m-area").find(".message_loader").show();

            if (pos == 0 && offsetPush >= 10) {

                var mutualMessagesPromisse = $.get(customerSettings.getMutualMessages, {
                    reciever_id: staff_id,
                    sender_id: contact_name_id,
                    offset: offsetPush,
                })
                    .done(function (messages) {

                        var isHostHttps = '<?= $isHttps; ?>';

                        if (!isHostHttps) {
                            $("body").find(".startMic").remove();
                        }

                        if (Array.isArray(messages) == false) {
                            endOfScroll = true;
                            $(".m-area .message_client_loader").hide();
                            if ($(el).hasScrollBar() && endOfScroll == true) {
                                prchat_setNoMoreStaffMessages();
                            }
                        } else {
                            offsetPush += 10;
                        }

                        $(messages).each(function (key, value) {
                            prependContactMessages(value);
                        });

                        if (endOfScroll == false) {
                            $(el).scrollTop(200);
                        }
                    });
                activateClientsLoader(mutualMessagesPromisse);
            }
        }

        // Handles client file form upload
        function uploadClientFileForm(form)
        {
            var formData = new FormData();
            var fileForm = $(form).children("input[type=file]")[0].files[0];
            var sentTo = $(".m-area").attr("data-staffid");
            var token_name = $(form).children("input[name=csrf_token_name]").val();

            formData.append("userfile", fileForm);
            formData.append("send_to", sentTo);
            formData.append("send_from", contact_name_id);
            formData.append("csrf_token_name", token_name);

            $.ajax({
                type: "POST",
                url: customerSettings.uploadMethod,
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function () {
                    if (fileForm != undefined) {
                        if ($(".chat-module-loader").length == 0) {
                            $(".m-area").prepend("<div class=\"chat-module-loader\"><div></div><div></div><div></div></div>");
                        } else {
                            $(".m-area .chat-module-loader").fadeIn();
                        }
                        var Regex = new RegExp("\[~%:\()@]");
                        if (Regex.test(fileForm.name)) {
                            alert_float("warning", '<?php echo _l('chat_permitted_files') ?>');
                            $(".m-area .chat-module-loader").remove();
                            return false;
                        }
                    } else {
                        $(".m-area .chat-module-loader").remove();
                        return false;
                    }
                },
                success: function (r) {
                    if (!r.error) {
                        var uploadSend = $.Event("keypress", {
                            which: 13
                        });
                        var basePath = "<?php echo base_url('modules/prchat/uploads/'); ?>";
                        var clientTextArea = $("#clientChat textarea.clients_textarea");
                        clientTextArea.val(basePath + r.upload_data.file_name);
                        setTimeout(function () {
                            if (clientTextArea.trigger(uploadSend)) {
                                alert_float("info", "File " + r.upload_data.file_name + " sent.");
                                $(".m-area .chat-module-loader").fadeOut();
                            }
                        }, 100);
                    } else {
                        $(".m-area .chat-module-loader").fadeOut();
                        alert_float("danger", r.error);
                    }
                }
            });
            $("form#staffMessagesFileForm").trigger("reset");
        }

        clientsChannel.bind("chat-ticket-event", function (event) {
            if (event.client_id == contact_id) {
                alert_float("success", "<?= _l('chat_client_new_ticket_created'); ?>");
            }
        });

        var createMessagesRequest = null;

        function appendMutualMessages(staff_id, contact_id)
        {
            $(".m-area ol").html("");

            var deferred = $.Deferred();
            var promise = deferred.promise();

            var clientCurrentWindow = !$("body").find(".staff_children_parent_child_div").hasClass("active_staff");

            if (!clientCurrentWindow) {
                if (createMessagesRequest) {
                    createMessagesRequest.abort();
                }

                createMessagesRequest = $.get(customerSettings.getMutualMessages, {
                    reciever_id: staff_id,
                    sender_id: "client_" + contact_id,
                    offset: 0,
                    limit: 20
                })
                    .done(function (messages) {
                        offsetPush = 10;

                        offsetPush += 10;
                        deferred.resolve(messages);

                    })
                    .always(function () {
                        if ($("#no_messages").length) {
                            $("#no_messages").remove();
                        }
                        createMessagesRequest = null;
                    });
            } else {
                deferred.resolve([]);
            }

            /*---------------* After users are fetched from database -> continue with loading *---------------*/
            promise.then(function (messages) {

                $(messages).each(function (key, value) {
                    prependContactMessages(value);
                });

                checkForUnreadMessages();
                scrollBottom();
            });

            // Loader is dependant of the promise... after messages are loaded loader dissapears
            activateClientsLoader(promise);
        }

        // File upload
        $("#clientChat").on("click", ".fileUpload", function () {
            $("#clientChat").find("form[name=staffMessagesFileForm] input:first").click();
        });

        $("#clientChat").on("change", "input[type=file]", function () {
            $(this).parent("form").submit();
        });

        /*---------------* Helper function scroll bottom to messages div *---------------*/
        function scrollBottom()
        {
            $(".m-area").scrollTop($(".m-area")[0].scrollHeight);
        }

        /*---------------* Helper functions that clears and shows all notifications *---------------*/
        function clearNotifications()
        {
            $("body").find(".active_staff").prev().attr("data-notification", "0").hide();
        }

        var ntf = document.querySelector(".chatNewNotification");

        function resetChatNotifications()
        {
            ntf.setAttribute("data-count", 0);
            ntf.style.display = "none";
        }

        function showChatNotification()
        {
            var count = Number(ntf.getAttribute("data-count")) || 0;
            ntf.style.display = "block";
            ntf.setAttribute("data-count", count + 1);
            ntf.classList.remove("notify");
            ntf.offsetWidth = ntf.offsetWidth;
            ntf.classList.add("notify");
            if (count === 0) {
                ntf.classList.add("show-count");
            }
        }

        /**
         * Mark messages as seen pusher event
         */
        user_messages_events.bind("message_seen", function (messages) {
            var recieverId, senderId;

            var l_seen = "<?= _l('chat_msg_seen'); ?>";
            if (Array.isArray(messages)) {
                var staff_id = $(".m-area").attr("data-staffid");

                for (var i = 0; i < messages.length; i++) {
                    recieverId = messages[i].reciever_id;
                    senderId = messages[i].sender_id;
                    var seen_at = l_seen + ": " + moment("<?= date('Y-m-d H:i:s'); ?>").format("h:mm:ss A, DD MMM YYYY");

                    $(".m-area#" + messages[i].reciever_id).find("i.circle-unseen").remove();
                    $(".m-area#" + messages[i].reciever_id).find("li.client").removeClass("isUnseenPadding");
                }
                ;
                $(".m-area .chat li").css("padding-right", "0.5em");

                if (senderId.startsWith("client") && senderId == contact_name_id) {
                    clientSeenNotify();
                }
            }
        });
    </script>
<?php require 'modules/prchat/assets/module_includes/chat_sound_settings.php'; ?>
