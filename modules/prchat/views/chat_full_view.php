<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

    <div id="wrapper" class="desktop_chat">

<?php

$isHttps = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? $isHttps = true : false);

if ($isHttps) loadChatComponent('AudioComponent');

?>

    <div id="frame">

        <?php if (isset($current_user)) {
            loadChatComponent('Sidepanel', ['props' => $current_user]);
        } ?>

        <?php loadChatComponent('Content'); ?>

    </div>
<?php loadChatComponent('ChooseAnnouncement'); ?>
    <div class="modal_container"></div>

<?php init_tail(); ?>

    <!-- Include chat settings file -->
<?php require('modules/prchat/assets/module_includes/chat_settings.php'); ?>

    <!-- Include chat settings file -->
<?php require('modules/prchat/assets/module_includes/chat_statuses.php'); ?>

    <!-- Include various mutual functions file -->
<?php require('modules/prchat/assets/module_includes/mutual_and_helper_functions.php'); ?>
<?php
if (!chatStaffCanDelete()) { ?>
    <style>
        ._removeMessage {
            display: none;
        }
    </style>
<?php } ?>
    <script>
        "use strict";

        if (localStorage.chat_theme_name) {
            $("body").addClass("chat_" + localStorage.chat_theme_name);
        }
        var isContentActive = false;
        window.addEventListener("online", handleConnectionChange);
        window.addEventListener("offline", handleConnectionChange);

        monitorWindowActivity();

        /*---------------* Main first thing get users/staff from database *---------------*/
        var users = $.get(prchatSettings.usersList);

        var offsetPush = 0;
        var groupOffsetPush = 0;
        var endOfScroll = false;
        var groupEndOfScroll = false;
        var friendUsername = "";
        var unreadMessages = "";
        var pusherKey = "<?= get_option('pusher_app_key') ?>";
        var appCluster = "<?= get_option('pusher_cluster') ?>";
        var staffFullName = "<?= get_staff_full_name(); ?>";
        var userSessionId = "<?= get_staff_user_id(); ?>";
        var isAdmin = app.user_is_admin;
        var staffCanCreateGroups = "<?= get_option('chat_members_can_create_groups'); ?>";
        var checkForNewMessages = prchatSettings.getUnread;
        var chat_desktop_notifications_enabled = "<?php echo get_option('chat_desktop_messages_notifications') ?>";
        chat_desktop_notifications_enabled = (chat_desktop_notifications_enabled == "0") ? false : true;
        var user_chat_status = "<?= get_user_chat_status(); ?>";

        if (staffCanCreateGroups === "0" && !isAdmin) {
            $("#add_group_btn").remove();
        }

        /*---------------* Handles input form sending *---------------*/
        $("#frame").on("click", ".fileUpload", function () {
            $("#frame").find("form[name=\"fileForm\"] input:first").click();
        });

        $("#frame").on("click", ".groupFileUpload", function () {
            $("#frame").find("form[name=\"groupFileForm\"] input:first").click();
        });

        $("#frame").on("change", "input[type=file]", function () {
            $(this).parent("form").submit();
        });

        // Handles file form upload  for staff to staff
        function uploadFileForm(form)
        {
            var formData = new FormData();
            var fileForm = $(form).children("input[type=file]")[0].files[0];
            var sentTo = $("li.contact.active").attr("id");
            var token_name = $(form).children("input[name=csrf_token_name]").val();
            var formId = $(form).attr("id");

            formData.append("userfile", fileForm);
            formData.append("send_to", sentTo);
            formData.append("send_from", userSessionId);
            formData.append("csrf_token_name", token_name);

            $.ajax({
                type: "POST",
                url: prchatSettings.uploadMethod,
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function () {
                    if (fileForm != undefined) {
                        if ($(".chat-module-loader").length == 0) {
                            $(".content").prepend("<div class=\"chat-module-loader\"><div></div><div></div><div></div></div>");
                        } else {
                            $(".content .chat-module-loader").fadeIn();
                        }
                        var Regex = new RegExp("\[~%:\()@]");
                        if (Regex.test(fileForm.name)) {
                            alert_float("warning", "<?php echo _l('chat_permitted_files') ?>");
                            $(".content .chat-module-loader").remove();
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
                        $("#frame textarea.chatbox").val(basePath + r.upload_data.file_name);
                        setTimeout(function () {
                            if ($("#frame textarea.chatbox").trigger(uploadSend)) {
                                alert_float("info", "File " + r.upload_data.file_name + " sent.");
                                $(".content .chat-module-loader").fadeOut();
                            }
                        }, 100);
                        getSharedFiles(userSessionId, sentTo);
                    } else {
                        $(".content .chat-module-loader").fadeOut();
                        alert_float("danger", r.error);
                    }
                }
            });
            $("form#" + formId).trigger("reset");
        }


        /*---------------* Check for messages history and append to main chat window *---------------*/
        function loadMessages(el)
        {
            var pos = $(el).scrollTop();
            var id = $(el).attr("id");
            var to = $("#contacts ul li.contact").children("a.active_chat").attr("id");
            var from = userSessionId;
            var not_seen_icon = "";

            if (pos == 0 && offsetPush >= 10) {

                $("#frame .messages").find(".message_loader").show();
                $.get(prchatSettings.getMessages, {
                    from: from,
                    to: to,
                    offset: offsetPush,
                })
                    .done(function (message) {
                        message = JSON.parse(message);
                        if (Array.isArray(message) == false) {
                            endOfScroll = true;
                            $("#frame .messages").find(".message_loader").hide();
                            if ($(el).hasScrollBar() && endOfScroll == true) {
                                prchat_setNoMoreMessages();
                            }
                        } else {
                            offsetPush += 10;
                        }
                        $(message).each(function (i, value) {

                            if (message[i].time_sent !== undefined && message[i].time_sent !== "undefined") {
                                var previous_time = moment(message[i].time_sent).format("YYYY-MM-DD HH");

                                if (message[i + 1] !== undefined) {
                                    var current_time = moment(message[i + 1].time_sent).format("YYYY-MM-DD HH");
                                }
                            }

                            value.message = emojify.replace(value.message);
                            var element = $(".messages#id_" + to).find("ul");

                            if (value.message.includes("class='quickMentionLink'")) {
                                value.message = unescapeHtml(value.message);
                            }

                            /**
                             * Check if it is audio message and decode html
                             */
                            value.message = isAudioMessage(value.message);

                            var optionsMore = deleteOrForward(value.id);
                            var isViewed = value.viewed == 1;

                            if (!isViewed) {
                                not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
                            }

                            if (value.reciever_id == from) {
                                element.prepend("<li class=\"replies\" id=\"" + value.id + "\"><img class=\"friendProfilePic\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\" data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"right\" title=\"" + value.time_sent_formatted + "\"/><p class=\"friend\">" + value.message + "</p>" + optionsMore + "</li>");
                            } else {
                                element.prepend("<li class=\"sent\" id=\"" + value.id + "\">" + not_seen_icon + "<img class=\"myProfilePic\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\" data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"left\" title=\"" + value.time_sent_formatted + "\"/><p class=\"you\" id=\"msg_" + value.id + "\" data-toggle=\"tooltip\" data-title=\"" + (!isViewed ? '<?= _l('chat_not_seen'); ?>' : '<?= _l('chat_msg_seen'); ?> ' + moment(value.viewed_at).format("h:mm:ss A, DD MMM YYYY")) + "\">" + value.message + "</p>" + optionsMore + "</li>");
                            }

                            if (message[i + 1] !== undefined) {
                                if (previous_time !== current_time) {
                                    $("<span class=\"middleDateTime\">" + moment(value.time_sent).format("llll") + "</span>").prependTo($(".messages ul li#" + value.id).parents("ul"));
                                }
                            }
                        });
                        if (endOfScroll == false) {
                            $(el).scrollTop(200);
                            $("#frame .messages").find(".message_loader").hide();
                        }
                    });
                activateLoader();
            }
        }

        /*---------------* Put prchatSettings.debug for debug mode for Pusher *---------------*/
        if (prchatSettings.debug) {
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
            authEndpoint: prchatSettings.pusherAuthentication,
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
        var presenceChannel = pusher.subscribe("presence-mychanel");
        var chat_status = pusher.subscribe("user_changed_chat_status");
        var user_messages_events = pusher.subscribe("user_messages");
        var groupChannels = pusher.subscribe("group-chat");

        pusher.config.unavailable_timeout = 5000;
        pusher.connection.bind("state_change", function (states) {
            var prevState = states.previous;
            var currState = states.current;
            var conn_tracker = $(".connection_field");
            if (currState == "unavailable") {
                conn_tracker.fadeIn();
                conn_tracker.children("i.fa-wifi").fadeIn();
                conn_tracker.css("background", "#f03d25");
            } else if (currState == "connected") {
                if (conn_tracker.is(":visible")) {
                    conn_tracker.children("i.fa-wifi").removeClass("blink");
                    conn_tracker.css("background", "#04cc04", function () {
                        conn_tracker.fadeOut(2000);
                    });
                }
            }
        });

        /*---------------* Pusher Trigger subscription succeeded *---------------*/
        presenceChannel.bind("pusher:subscription_succeeded", function (members) {
            chatMemberUpdate();
            var redirect_staff_id = localStorage.staff_to_redirect;
            users.then(function () {
                if (localStorage.touchClientsTab) {
                    $("li.crm_clients a").click();
                    localStorage.touchClientsTab = "";
                }
                if (redirect_staff_id != "") {
                    $(".chat_nav li.staff a").trigger("click");
                    $("#contacts a#" + redirect_staff_id).trigger("click");
                    localStorage.staff_to_redirect = "";
                } else {
                    setTimeout(function () {
                        if (!window.matchMedia("only screen and (max-width: 735px)").matches) {
                            $("#frame #sidepanel ul.nav.nav-tabs li.staff.active a").click();
                        }
                    }, 600);
                }
            });
        });

        /*---------------* Pusher Trigger user connected *---------------*/
        presenceChannel.bind("pusher:member_added", function (member) {
            addChatMember(member);
            if (member.info.status == "") {
                member.info.status = "online";
            }
            if (member.info.status != "") {
                var userPlaceholder = $("body").find(".chat_contacts_list li a#" + member.id + " .wrap img");
                userPlaceholder.attr("title", strCapitalize(member.info.status)).attr("data-original-title", strCapitalize(member.info.status));
                userPlaceholder.removeClass();
                userPlaceholder.addClass("imgFriend " + member.info.status + "");
                $("body").find(".chat_contacts_list li a#" + member.id + " .wrap span").removeClass().addClass(member.info.status);
            }

            if (member.info.justLoggedIn) {
                var message_selector = $("#contacts .contact a#" + member.id).find(".wrap .meta .preview");
                var old_message_content = message_selector.html();
                message_selector.html("<strong class=\"contact_role\">" + member.info.name + "<?php echo _l('chat_user_is_online'); ?>" + "</strong>");
                setTimeout(function () {
                    message_selector.html(old_message_content);
                }, 7000);
                $.notify("", {
                    "title": app.lang.new_notification,
                    "body": member.info.name + " " + prchatSettings.hasComeOnlineText,
                    "requireInteraction": true,
                    "icon": $("#header").find("img").attr("src"),
                    "tag": "user-join-" + member.id,
                    "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : null
                }).show(function (e) {
                    window.focus();
                    setTimeout(function () {
                        e.target.close();
                    }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 2000);
                });
            }
        });

        /*---------------* Pusher Trigger user logout *---------------*/
        presenceChannel.bind("pusher:member_removed", function (members) {
            removeChatMember(members);
        });

        var pendingRemoves = [];

        /*---------------* New chat members tracking / removing *---------------*/
        function addChatMember(member)
        {
            var pendingRemoveTimeout = pendingRemoves[member.id];
            $("a#" + member.id + " .wrap span").addClass("online").removeClass("offline");
            if (presenceChannel.members.count > 0) {
                if (!$(".liveUsers").length) {
                    $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (" " + presenceChannel.members.count - 1) + "</span>");
                } else {
                    $(".liveUsers").html(presenceChannel.members.count - 1);
                }
            }
            if (member.info.justLoggedIn == true) {
                appendMemberToTop(member.id);
            } else {
                if ($("#contacts li.contact#" + member.id).find(".unread-notifications").attr("data-badge") != 0) {
                    appendMemberToTop(member.id);
                }
            }
            if (pendingRemoveTimeout) {
                clearTimeout(pendingRemoveTimeout);
            }
        }

        /*---------------* New chat members tracking / removing *---------------*/
        function removeChatMember(members)
        {
            pendingRemoves[members.id] = setTimeout(function () {
                if (presenceChannel.members.count > 0) {
                    $(".liveUsers").remove();
                    $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (presenceChannel.members.count - 1) + "</span>");
                }
                $("a#" + members.id + " .wrap span").addClass("online").removeClass("offline");
                chatMemberUpdate();
            }, 5000);
        }

        /*---------------* Append member to top of sidebar after logged in *---------------*/
        function appendMemberToTop(member)
        {
            var contactMember = $("#contacts li.contact#" + member);
            var $cloned = contactMember.clone();
            contactMember.remove();
            $cloned.prependTo("#contacts ul");
        }

        /*---------------* Bind the 'send-event' & update the chat box message log *---------------*/
        presenceChannel.bind("send-event", function (data) {
            /**
             * Check if it is audio message and decode html
             */
            data.message = isAudioMessage(data.message);

            if (data.global) {
                data.message = "<?= '<strong>' . _l('chat_message_announce') . '</strong>'; ?>" + data.message;
            }

            $("#frame .messages").find("span.userIsTyping").fadeOut(500);

            if (data.last_insert_id) {
                $(".messages").find("li.sent .you#" + userSessionId).attr("id", "msg_" + data.last_insert_id);
                $(".messages").find("li.sent#" + userSessionId).find(".optionsMore").attr("data-id", data.last_insert_id);
                $(".messages").find("li.sent#" + userSessionId).attr("id", data.last_insert_id);
            }

            if (presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id) {
                $(".has_newmessages").val("true").attr("id", data.from);
                data.message = createTextLinks_(emojify.replace(data.message));
                var optionsMore = deleteOrForward(userSessionId);

                $(".messages#id_" + data.from + " ul").append("<li class=\"replies\"><img class=\"friendProfilePic\" src=\"" + fetchUserAvatar(data.from, data.sender_image) + "\"/><p class=\"friend\">" + data.message + "</p>" + optionsMore + "</li>");

                if (data.message.includes("class='quickMentionLink'")) {
                    data.message = unescapeHtml(data.message);
                    data.message = data.message.replace(/<[^>]*>?/gm, "");
                }

                $("#contacts .contact a#" + data.from).find(".wrap .meta .preview").html(data.message);
                $("#contacts .contact a#" + data.from).find(".wrap .meta .pull-right.time_ago").html(moment().format("hh:mm A"));

                if (user_chat_status != "busy" && user_chat_status != "offline") {
                    initUserSound(data);
                }

                if ($(".messages").hasScrollBar()) {
                    scroll_event();
                }
            }

            if (presenceChannel.members.me.id == data.to) {
                var old_data = emojify.replace(data.message);
                data.message = escapeHtml(data.message);

                var firstname = presenceChannel.members.members[data.from].name.replace(/ .*/, "");

                if (data.message.includes("class=\"prchat_convertedImage\"")) {
                    data.message = "<p class=\"tb\">" + firstname + " " + '<?php echo _l('chat_new_file_uploaded'); ?></p>';
                }

                if (data.message.includes("data-lity target=\"blank\" href")) {
                    data.message = "<p class=\"tb\">" + firstname + " " + '<?php echo _l('chat_new_link_shared'); ?></p>';
                }

                if (data.message.match("audio/ogg")) {
                    data.message = "<p class=\"tb\">" + firstname + " " + '<?php echo _l('chat_new_audio_message'); ?></p>';
                }

                var truncated_message = "";

                if (old_data.includes("emoji") && !old_data.includes("href")) {
                    $("#contacts .contact a#" + data.from).find(".wrap .meta .preview").html(old_data);
                    if ($(".messages").hasScrollBar()) {
                        scroll_event();
                    }
                    return false;
                }

                if (!data.message.includes("class=\"tb\"")) {
                    truncated_message = data.message.trunc(36);
                } else {
                    truncated_message = data.message.trunc(46);
                }

                if ($(window).width() > 733) {
                    $("#contacts .contact a#" + data.from).find(".unread-notifications").hide();
                }
                $("#contacts .contact a#" + data.from).find(".wrap .meta .preview").html(truncated_message);
            }

        });

        /*---------------* Detect when a user is typing a message *---------------*/
        presenceChannel.bind("typing-event", function (data) {
            if (
                presenceChannel.members.me.id == data.to &&
                data.from != presenceChannel.members.me.id &&
                data.message == "true"
            ) {
                $("#frame .messages")
                    .find("span.userIsTyping#" + data.from)
                    .fadeIn(500);
            } else if (
                presenceChannel.members.me.id == data.to &&
                data.from != presenceChannel.members.me.id &&
                data.message == "null"
            ) {
                $("#frame .messages")
                    .find("span.userIsTyping#" + data.from)
                    .fadeOut(500);
            }
        });

        /*---------------* Trigger notification popup increment and live notification *---------------*/
        presenceChannel.bind("notify-event", function (data) {
            if (chat_desktop_notifications_enabled) {
                if (data.from !== userSessionId && data.to == userSessionId) {
                    if (user_chat_status != "busy" && user_chat_status != "offline") {
                        /**
                         * Check if message is audio or user uploaded new file
                         */
                        if (data.message.match("type=\"audio/ogg\"&gt;&lt;/audio&gt")) {
                            data.message = "<?= _l('chat_i_sent_new_message'); ?>";
                        }
                        if (data.message.includes("class=\"prchat_convertedImage\"")) {
                            data.message = "<?= _l("chat_new_file_sent") ?>";
                        }

                        setTimeout(() => {
                            $.notify("", {
                                "title": data.from_name,
                                "body": data.message,
                                "requireInteraction": true,
                                "icon": fetchUserAvatar(data.from, data.sender_image),
                                "tag": "user-message-" + data.from,
                                "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : null
                            }).show(function (e) {
                                window.focus();
                                setTimeout(function () {
                                    e.target.close();
                                }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
                            });
                        }, 100);
                    }
                }
            }

            if ($(window).width() < 733) {
                if (presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id) {
                    var notiBox = $("body").find("li.contact#" + data.from + " a#" + data.from);
                    if (!$(notiBox).hasClass("active_chat")) {
                        $(notiBox).find("img").addClass("shaking");
                        var notification = parseInt($(notiBox).find(".unread-notifications#" + data.from).attr("data-badge"));
                        var badge = $(notiBox).find(".unread-notifications#" + data.from);
                        badge.attr("data-badge", notification + 1);
                        $(notiBox).find(".unread-notifications#" + data.from).show();
                    }
                    delay(function () {
                        $(notiBox).find("img").removeClass("shaking");
                    }, 600);
                }
            }
        });

        /*---------------* On click send message button trigger post message *---------------*/
        $("body").on("click", ".enterBtn, .enterGroupBtn, .enterClientBtn, .fa-paper-plane", function (e) {
            var eventEnter = $.Event("keypress", {
                which: 13
            });

            var targetName = "";
            if (e.currentTarget.getAttribute("name") !== null) {
                targetName = e.currentTarget.getAttribute("name");
            }
            // Groups
            if (targetName == "") {
                targetName = e.currentTarget.parentNode.getAttribute("name");
            }
            if (targetName == "enterBtn") {
                $("#frame").find(".chatbox").trigger(eventEnter);
                $(".chatbox").focus();
            } else if (targetName == "enterGroupBtn") {
                $("#frame").find(".group_chatbox").trigger(eventEnter);
                $(".group_chatbox").focus();
            } else if (targetName == "enterClientBtn") {
                $("#frame").find(".client_chatbox").trigger(eventEnter);
                $(".client_chatbox").focus();
            }
        });

        /*---------------* chatMemberUpdate() place & update users on user page, unread messages notifications *---------------*/
        function chatMemberUpdate()
        {
            users.then(function (data) {
                var offlineUser = "";
                var onlineUser = "";
                data = JSON.parse(data);
                if (Object.keys(data).length > 1) {
                    $("#frame .nav.nav-tabs li.groups, #frame .nav.nav-tabs li.groups a, #frame .nav.nav-tabs li.crm_clients a").removeClass("events_disabled");
                } else {
                    $(".message-input, .contact-profile").remove();

                    $(".content .messages").html("<h3 class=\"text-center alert alert-info pt5\">No staff users with valid permissions found, try adding <strong>new staff users</strong> or (Grant Chat Access) to existing staff users.<br><br> To manage staff user permissions for chat navigate in sidemenu <strong>Setup->Staff</strong> select specific staff member and click Permissions and scroll down to Chat Module to assign chat access permissions, or disable In chat view show only users with chat permissions (also applies on client side) option in <strong>Setup->Settings->Chat Settings</strong><br><br> Administrators will not need any permissions.<br><br></h3><div class=\"text-center\"><strong class=\"text-center alert alert-warning\">You have to create at least one staff member including you !</strong></div>");
                    return false;
                }
                $(".chatbox").prop("disabled", "");
                $.each(data, function (user_id, value) {
                    if (value.role == null) {
                        value.role = '<?= _l("als_staff"); ?>';
                    }
                    if (value.staffid != presenceChannel.members.me.id) {
                        var user = presenceChannel.members.get(value.staffid);

                        if (value.message == undefined) value.message = prchatSettings.sayHiText + " " + strCapitalize(value.firstname + " " + value.lastname);

                        if (value.time_sent_formatted == undefined) value.time_sent_formatted = "";

                        var user_status = (value.status != undefined || value.status == "") ? value.status : "online";

                        var translated_status = "";
                        for (var status in chat_user_statuses) {
                            if (status == user_status) {
                                translated_status = chat_user_statuses[status];
                            }
                        }

                        if (value.message.includes("class='quickMentionLink'")) {
                            value.message = unescapeHtml(value.message);
                            value.message = value.message.replace(/<[^>]*>?/gm, "");
                        }

                        if (user != null) {
                            onlineUser += "<li class=\"contact\" id=\"" + value.staffid + '" data-toggle="tooltip" data-container="body" title="<?php echo _l('chat_user_active_now'); ?>">';
                            onlineUser += "<a href=\"#" + value.staffid + "\" id=\"" + value.staffid + "\" class=\"on\"><div class=\"wrap\"><span class=\"online " + user_status + "\"></span>";
                            onlineUser += "<img data-toggle=\"tooltip\" title=\"" + translated_status + "\" src=\"" + fetchUserAvatar(value.staffid, value.profile_image) + "\" class=\"imgFriend " + user_status + "\" />";
                            onlineUser += "<div class=\"meta\"><p role=\"" + value.role + "\" class=\"name\">" + strCapitalize(value.firstname + " " + value.lastname) + "</p>";
                            onlineUser += "<social_info skype=\"" + value.skype + "\" facebook=\"" + value.facebook + "\" linkedin=\"" + value.linkedin + "\"></social_info>";
                            onlineUser += "<p class=\"preview\">" + value.message + "</p><p class=\"pull-right time_ago\">" + value.time_sent_formatted + "</p></div></div>";
                            onlineUser += "<span class=\"unread-notifications\" id=\"" + value.staffid + "\" data-badge=\"0\"></span></a></li>";

                            if (presenceChannel.members.count > 0) {
                                $(".liveUsers").remove();
                                $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (" " + presenceChannel.members.count - 1) + "</span>");
                            }
                        } else {

                            offlineUser += "<li class=\"contact\" id=\"" + value.staffid + "\"";
                            var lastLoginText = "Never";

                            if (value.last_activity) {
                                lastLoginText = moment(value.last_activity, "YYYYMMDD h:mm:ss").fromNow();
                            }

                            offlineUser += ' data-toggle="tooltip" data-container="body" title="<?php echo _l('chat_last_seen'); ?>: ' + lastLoginText + "\">";
                            offlineUser += "<a href=\"#" + value.staffid + "\" id=\"" + value.staffid + "\" class=\"off\"><div class=\"wrap\"><span class=\"offline\"></span>";
                            offlineUser += "<img data-toggle=\"tooltip\" title=\"" + strCapitalize("offline") + "\" src=\"" + fetchUserAvatar(value.staffid, value.profile_image) + "\" class=\"imgFriend\" /><div class=\"meta\"><p role=\"" + value.role + "\" class=\"name\">" + strCapitalize(value.firstname + " " + value.lastname) + "</p>";
                            offlineUser += "<p class=\"preview\">" + value.message + "</p><p class=\"pull-right time_ago\">" + value.time_sent_formatted + "</p><social_info skype=\"" + value.skype + "\" facebook=\"" + value.facebook + "\" linkedin=\"" + value.linkedin + "\"></social_info>";
                            offlineUser += "</div></div><span class=\"unread-notifications\" id=\"" + value.staffid + "\" data-badge=\"0\"></span></a></li>";
                        }
                    }
                });
                $("#frame #contacts ul").html("");
                $("#frame #contacts ul").prepend(onlineUser + offlineUser);

                var newUnreadMessages = JSON.parse(checkForNewMessages);

                if (!checkForNewMessages.includes("false")) {
                    var notifications;
                    $.each(newUnreadMessages, function (i, sender) {
                        notifications = $("#contacts li a#" + sender.sender_id).find(".unread-notifications#" + sender.sender_id);
                        if (notifications.length) {
                            notifications.attr("data-badge", sender.count_messages);
                            notifications.show();
                        }
                    });
                }
                return false;
            });
        }

        /*---------------* Trigger click on user & create chat box and check for messages *---------------*/
        $("#frame #contacts .chat_contacts_list").on("click", "li.contact a", function (event) {
            animateContent();
            var obj = $(this);
            var id = obj.attr("id").replace("id_", "");
            var contact_selector = $("#contacts a#" + id);

            // Handle unread messages
            if ($(".has_newmessages").attr("id") == id && $(".has_newmessages").val() == "true" ||
                $(this).find(".unread-notifications#" + id).attr("data-badge") > 0) {
                updateLatestMessages(id);
            }


            $(".has_newmessages").val("false").attr("id", "");

            $(".group_members_inline").remove();

            var currentSoundMembers = JSON.parse(localStorage.getItem("soundDisabledMembers"));
            (currentSoundMembers.includes(id)) ?
                $(".user_sound_icon").removeClass("fa-volume-up").addClass("fa-volume-off") : $(".user_sound_icon").removeClass("fa-volume-off").addClass("fa-volume-up");


            var contact_image = $("#frame .contact-profile img.staff-profile-image-small");
            if (contact_image.is(":hidden")) {
                contact_image.show();
            }
            endOfScroll = false;
            offsetPush = 0;
            $("#frame .chatbox").val("");

            $("#contacts li a").removeClass("active_chat");

            $("#contacts .contact").removeClass("active");

            contact_selector.parent(".contact").addClass("active");

            $(this).addClass("active_chat");

            if (contact_selector.parent(".contact").find(".tb")) {
                contact_selector.parent(".contact").find(".tb").css({
                    "font-weight": "normal",
                    "color": "rgba(153, 153, 153, 1)"
                });
            }

            createChatBox(obj);

            if ($("#search_field").val() !== "") {
                clearSearchValues();
            }

            if ($(this).find(".unread-notifications#" + id).attr("data-badge") > 0) {
                updateUnreadMessages($(this));
                setTimeout(function () {
                    obj.find(".unread-notifications#" + id).attr("data-badge", "0").hide();
                }, 1000);
            }
        });

        /*---------------* Creating chat box from the html template to the DOM *---------------*/
        var createChatBoxRequest = null;

        function createChatBox(obj)
        {
            $(".messages ul").html("");
            var id = obj.attr("href");
            var fullName = obj.find(".meta").children("p:first-child").text();
            var contactRole = obj.find(".meta").children("p:first-child").attr("role");
            var optionsMore = "";
            var not_seen_icon = "";

            var contact_id = id.replace("#", "");
            id = id.replace("#", "id_");
            $(".messages").find("span.userIsTyping").attr("id", contact_id);

            $("#frame .content .contact-profile p").html(fullName + "<br><a target=\"_blank\" href=\"" + site_url + "admin/profile/" + contact_id + "\"><small class=\"contact_role\"></small></a>");
            var taskName, userId;
            userId = $("li.contact.active").attr("id");
            taskName = $("li.contact.active a").find("p.name").text();

            $("#frame .content .contact-profile .actionTask").attr("data-name", taskName);
            $("#frame .content .contact-profile .actionTask").attr("data-user-id", userId);
            $("#frame .content .contact-profile .actionTask").attr("data-type", "staff");

            $("#frame .content .contact-profile .user_sound_icon").attr("data-sound_user_id", contact_id);
            $(".group_members_inline").remove();

            checkContactRole(contactRole);

            var currentActiveChatWindow = obj.hasClass("active");

            var dfd = $.Deferred();
            var promise = dfd.promise();

            if (!currentActiveChatWindow) {
                if (createChatBoxRequest) {
                    createChatBoxRequest.abort();
                }

                createChatBoxRequest = $.get(prchatSettings.getMessages, {
                    from: userSessionId,
                    to: contact_id,
                    offset: 0,
                    limit: 20
                })
                    .done(function (r) {
                        offsetPush = 10;

                        r = JSON.parse(r);

                        var message = r;

                        offsetPush += 10;
                        dfd.resolve(message);

                    }).always(function () {
                        if ($("#no_messages").length) {
                            $("#no_messages").remove();
                        }
                        createChatBoxRequest = null;
                    });
            } else {
                dfd.resolve([]);
            }

            /*---------------* After users are fetched from database -> continue with loading *---------------*/
            promise.then(function (message) {
                var sliderimg = obj.find("img").prop("currentSrc");
                $("#frame .content .contact-profile img").prop("src", sliderimg);

                $("#pusherMessagesForm").attr("id", id);
                $(".messages#" + id).parent(".content").find(".to:hidden").val(id.replace("id_", ""));
                $(".messages#" + id).parent(".content").find(".from:hidden").val(userSessionId);

                $(message).each(function (i, value) {

                    var previous_time = moment(message[i].time_sent).format("YYYY-MM-DD HH");
                    if (message[i + 1] !== undefined) {
                        var current_time = moment(message[i + 1].time_sent).format("YYYY-MM-DD HH");
                    }

                    if (value.message.startsWith("<?= _l('chat_message_announce'); ?>")) {
                        value.message = "<strong class=\"italic\">" + value.message + "</strong>";
                    }

                    if (value.message.includes("class='quickMentionLink'")) {
                        value.message = unescapeHtml(value.message);
                    }

                    value.message = emojify.replace(value.message);

                    /**
                     * Check if it is audio message and decode html
                     */
                    value.message = isAudioMessage(value.message);
                    var isViewed = value.viewed == 1;

                    if (value.sender_id == userSessionId) {

                        if (!isViewed) {
                            not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
                        }

                        optionsMore = deleteOrForward(value.id);

                        $(".messages ul").prepend("<li class=\"sent\" id=\"" + value.id + "\">" + not_seen_icon + '<img data-toggle="tooltip" data-container="body" data-placement="top" title=" <?= _l('chat_sent_at') ?>' + value.time_sent_formatted + "\" class=\"myProfilePic\" src=\"" + fetchUserAvatar(userSessionId, value.user_image) + "\"/><p class=\"you\" id=\"msg_" + value.id + "\" data-toggle=\"tooltip\" data-title=\"" + (!isViewed ? '<?= _l('chat_not_seen'); ?>' : '<?= _l('chat_msg_seen'); ?> ' + moment(value.viewed_at).format("h:mm:ss A, DD MMM YYYY")) + "\">" + value.message + "</p>" + optionsMore + "</li>");

                        if (previous_time !== current_time) {
                            $("<span class=\"middleDateTime\">" + moment(value.time_sent).format("llll") + "</span>").prependTo($(".messages ul li#" + value.id).parents("ul"));
                        }

                    } else {
                        if (!isViewed) {
                            $(".has_newmessages").val("true").attr("id", value.sender_id);
                        }
                        var optionsMore = deleteOrForward(userSessionId);
                        $(".messages ul").prepend("<li class=\"replies\"><img data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"right\" title=\"" + value.time_sent_formatted + "\" class=\"friendProfilePic\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/><p  class=\"friend\">" + value.message + "</p>" + optionsMore + "</li>");
                    }
                });
                $(".group_members_inline").remove();
            });
            fillSocialIconsData(obj);
            $(".messages").attr("id", id);

            activateLoader(promise);

            $.when(promise.then())
                .then(function () {
                    if ($(".messages").hasScrollBar() && $(window).width() > 733) {
                        scroll_event();
                        $(".message-input textarea.chatbox").focus();
                    } else if ($(window).width() < 733) {
                        // Due to mobile devices bug and loading time
                        scroll_event();
                        scroll_event();
                    } else {
                        // One last check for mobile devices
                        scroll_event();
                    }
                });

            $(".contact #" + id + " .from").val(presenceChannel.members.me.id);

            $(".contact #" + id + " .to").val(obj.attr("href"));

            getSharedFiles(userSessionId, contact_id);

            $("#frame .nav.nav-tabs li.groups, #frame .nav.nav-tabs li.groups a, #frame .nav.nav-tabs li.crm_clients a").removeClass("events_disabled");
            $(".user_sound_icon").show();
            return false;
        }

        /*--------------------  * send message & typing event to server  * ------------------- */
        $("#frame").on("keypress", "textarea.chatbox", function (e) {
            var form = $(this).parents("form");
            var imgPath = $("#sidepanel #profile .wrap img").prop("currentSrc");

            var input_from = $(this).next().next().next();

            if (e.which == 13 && !e.shiftKey) {

                e.preventDefault();

                var message = $.trim($(this).val());

                if (message == "" || internetConnectionCheck() === false) {
                    return false;
                }

                message = createTextLinks_(emojify.replace(message));

                var langMessageSent = "<?= _l('chat_new_audio_message_sent'); ?>";

                var audioOrMessage = message.match("<audio controls src=\"") ? langMessageSent : message;

                if (audioOrMessage.includes("class='quickMentionLink'")) {
                    audioOrMessage = audioOrMessage.replace(/<[^>]*>?/gm, "");
                }

                var optionsMore = deleteOrForward(userSessionId);
                $("#contacts .contact.active").find(".wrap .meta .preview").html('<?php echo _l('chat_message_you'); ?>' + " " + audioOrMessage);

                $(".messages ul").append("<li class=\"sent\" id=\"" + userSessionId + '"><i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i><img class="myProfilePic" data-toggle="tooltip" data-container="body" data-placement="top" title="<?= _l('chat_sent_at') . " " . _dt(date("Y-m-d H:i:s")); ?>" src="' + imgPath + "\"/><p class=\"you\" id=\"" + userSessionId + "\">" + message + "</p>" + optionsMore + "</li>");

                input_from.val("false");

                // send event
                var formData = form.serializeArray();

                $.post(prchatSettings.serverPath, formData);
                $(this).val("");
                $(this).focus();
                scroll_event();

            } else if (!$(this).val() || (input_from.val() == "null" && $(this).val())) {
                // typing event
                input_from.val("true");
                $.post(prchatSettings.serverPath, form.serialize());
            }
        });

        /*---------------* Update user lastes message into dabatase *---------------*/
        function updateLatestMessages(id)
        {
            $.post(prchatSettings.updateUnread, {
                id: id
            }).done(function (r) {
                if (r != "true") {
                    return false;
                }
                return true;
            });
        }

        /*---------------* Updating unread messages trigger and notification trigger *---------------*/
        function updateUnreadMessages(member_id)
        {
            var timeOut = 2000;
            member_id = $(member_id).attr("id");
            setTimeout(function () {
                if (member_id) {
                    updateLatestMessages(member_id);
                    $(".unread-notifications#" + member_id).hide();
                    return true;
                }
            }, timeOut);
        }

        /*---------------* Additional checks for chatbox and unread message update control *---------------*/
        $("#frame").on("click", "textarea.chatbox, div.messages", function () {
            updateMessageToSeen();
        });

        /*---------------* prevent showing dots if user is not typing *---------------*/
        $("#frame").on("focus", ".chatbox, .messages", function (e) {

            if (e.currentTarget.tagName == "TEXTAREA") {
                $(".messages").scrollTop($(".messages")[0].scrollHeight);
            }

            $(".messages").find("span.userIsTyping").fadeOut(500);
            updateMessageToSeen();
            if ($(".tb")) {
                $(".tb").css({
                    "font-weight": "normal",
                    "color": "rgba(153, 153, 153, 1)"
                });
            }
        });

        function updateMessageToSeen()
        {
            var member_id = $("#sidepanel li.active a.active_chat").attr("id");

            if ($(".has_newmessages").attr("id") == member_id && $(".has_newmessages").val() == "true") {
                updateLatestMessages(member_id);
                $(".has_newmessages").val("false");
            }
        }

        /*---------------* Switch user chat theme *---------------*/
        $("body").on("click", "#_light", function () {
            chatSwitchTheme("light");
        });
        $("body").on("click", "#_dark", function () {
            chatSwitchTheme("dark");
        });

        function chatSwitchTheme(theme_name)
        {
            $.post(prchatSettings.switchTheme, {
                theme_name: theme_name
            }).done(function (r) {
                if (r.success !== false) {
                    localStorage.chat_theme_name = theme_name;
                    location.reload();
                }
            });
        }

        /*---------------* Search members *---------------*/
        $("#frame #search #search_field").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#frame #contacts ul li").filter(function () {
                $(this).toggle($(this).children("a").find("p.name").text().toLowerCase().indexOf(value) > -1);
            });
        });

        /*---------------* On focus out clear out input field and show all members if not found in searchbox *---------------*/
        function clearSearchValues()
        {
            $("#frame #search_field").val("");
            $("#contacts ul li").filter(function () {
                $(this).css("display", "block");
                $("#profile").click();
            });
        }

        $("#frame").keyup("#search_field", function (e) {
            if (e.keyCode === 27) {
                clearSearchValues();
            }
        });
        (jQuery);

        /*---------------* Fill Social Iconds with data *---------------*/
        function fillSocialIconsData(obj)
        {
            var social_info_attributes = $(obj).find("social_info");

            var socialMedia = [{
                type: "skype",
                value: social_info_attributes[0].attributes.skype.value,
                link: "skype:" + social_info_attributes[0].attributes.skype.value + "?call"
            },
                {
                    type: "facebook",
                    value: social_info_attributes[0].attributes.facebook.value,
                    link: "http://www.facebook.com/" + social_info_attributes[0].attributes.facebook.value
                },
                {
                    type: "linkedin",
                    value: social_info_attributes[0].attributes.linkedin.value,
                    link: "http://www.linkedin.com/in/" + social_info_attributes[0].attributes.linkedin.value
                },
            ];

            for (var i in socialMedia) {
                var element = $("#frame").find(".contact-profile #fa-" + socialMedia[i].type);
                socialMedia[i].value == "" ?
                    element.hide() :
                    element.attr("href", socialMedia[i].link).show();
            }
        }

        /*---------------* Delete own messages function *---------------*/
        function delete_chat_message(msg_id)
        {
            var contact_id = $("#contacts ul li").children("a.active_chat").attr("id");
            var selector = $("body").find(".messages li#" + msg_id);

            $.post(prchatSettings.deleteMessage, {
                id: msg_id,
                contact_id: contact_id
            }).done(function (response) {
                if (response) {
                    selector.remove();
                    let lastChildren = $("body").find(".messages ul").children().last();
                    if (lastChildren.hasClass("middleDateTime")) {
                        lastChildren.remove();
                    }

                    getSharedFiles(userSessionId, contact_id);
                } else {
                    selector.remove();
                    alert_float("danger", "<?php echo _l('chat_error_float'); ?>");
                }
            });
        }

        /*---------------* Check contact/staff role and append *---------------*/
        function checkContactRole(contactRole)
        {
            if (contactRole == "0") {
                $("#frame .content .contact-profile p small").html("<?= _l('chat_role_administrator'); ?>");
            } else {
                $("#frame .content .contact-profile p small").html(contactRole);
            }
        }

        /*---------------* Init current chat loader synchronized with messages append *---------------*/
        function activateLoader(promise = null, client = false)
        {
            if (promise !== null) {
                var initLoader = (client) ? $("#frame .client_messages") : $("#frame .messages");
                if (initLoader.is(":visible")) {
                    if (initLoader.find(".message_loader").show()) {
                        promise.then(function () {
                            initLoader.find(".message_loader").hide();
                        });
                    }
                    ;
                }
            }
        }

        /*---------------* Get current chat shared files *---------------*/
        function getSharedFiles(own_id, contact_id)
        {
            $.post(prchatSettings.getSharedFiles, {
                own_id: own_id,
                contact_id: contact_id
            }).done(function (data) {
                $(".history_slider").html("");
                $(".history_slider").html(JSON.parse(data));
            });
        }


        /*---------------* Truncate text message to user view left sidebar *---------------*/
        String.prototype.trunc = String.prototype.trunc ||
            function (n) {
                return (this.length > n) ? this.substr(0, n - 1) + "&hellip;" : this;
            };

        /*---------------* Scroll bottom *---------------*/
        function scroll_event()
        {
            var m = $(".messages"),
                gm = $(".group_messages"),
                cm = $(".client_messages");

            if (m.is(":visible") && m.hasScrollBar()) m.scrollTop(m[0].scrollHeight);
            if (gm.is(":visible") && gm.hasScrollBar()) gm.scrollTop(gm[0].scrollHeight);
            if (cm.is(":visible") && cm.hasScrollBar()) cm.scrollTop(cm[0].scrollHeight);

        }

        /*---------------* For mobile devices vh ports adjust for better UX *---------------*/
        var vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty("--vh", vh + "px");

        window.addEventListener("resize", function () {
            var vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty("--vh", vh + "px");
        });

        /*---------------* Theme events  *---------------*/
        $("#bottom-bar").on("click", "#switchTheme", function () {
            $(".dropdown-content").toggle(10);
        });

        /*---------------* Check if staff has permissions for settings  *---------------*/
        $("#settings").on("click", function () {
            <?php if (staff_can('view', 'settings')) : ?>
            window.location = "<?php echo admin_url('settings?group=prchat-settings'); ?>";
            <?php else : ?>
            alert_float("warning", "<?php echo _l('chat_settings_permission'); ?>");
            <?php endif; ?>
        });

        var isUserMobile = window.matchMedia("only screen and (max-width: 735px)").matches;
        /*---------------* Shared files on lick icon hide div with shared items  *---------------*/
        $("#sharedFiles").on("click", "i.fa-times-circle-o", function () {
            $("#sharedFiles").css({
                "right": "-360px",
                "width": "360px"
            }, 10, "linear").hide(1000);
        });

        /*---------------* On click event for shared files  *---------------*/
        $("#shared_user_files").on("click", function () {
            ($("#sharedFiles").css("right") == "-360px") ?
                $("#sharedFiles").show().animate({
                    "right": "0",
                    "width": (isUserMobile) ? "100%" : "360px"
                }, 10, "linear") : $("#sharedFiles").css({
                    "right": "-360px",
                    "width": (isUserMobile) ? "-360px" : "0px"
                }, 10, "linear").hide(500);
        });
        /*---------------* Event click tracker for shared files   *---------------*/
        $(".messages, .group_messages, .chat_client_messages, #contacts, textarea, #header, #menu").on("click", function () {
            ($("#sharedFiles").is(":visible"));
            $("#sharedFiles").css({
                "right": "-360px",
                "width": "360px"
            }, 10, "linear").hide(500);

            $(".chat_group_options, #status-options").removeClass("active");
            $(".chat_group_options").css({
                "right": "-360px",
                "width": "360px"
            }, 10, "linear").hide(500);
        });

        /*---------------* Modal create announcement handler  *---------------*/
        $(function () {
            $("#frame .dropdown .i_announcement").on("click touchstart", function () {
                if ($(".modal-backdrop.fade").hasClass("in")) {
                    $(".modal-backdrop.fade").remove();
                }
                $("#chooseAnnouncement").modal({
                    show: true
                });
            });

            $("body #staffAnnouncementModal").on("click touchstart", function () {
                $(".modal_container").load(prchatSettings.chatAnnouncement, function (res) {
                    if ($(".modal-backdrop.fade").hasClass("in")) {
                        $(".modal-backdrop.fade").remove();
                    }
                    $("#_staffAnnouncementModal").modal({
                        show: true
                    });
                    $("#chooseAnnouncement").modal("hide");
                });
            });

            $("body #clientsAnnouncementModal").on("click touchstart", function () {
                $(".modal_container").load(prchatSettings.sendClientsAnnouncement, function (res) {
                    if ($(".modal-backdrop.fade").hasClass("in")) {
                        $(".modal-backdrop.fade").remove();
                    }
                    $("#_clientsAnnouncementModal").modal({
                        show: true
                    });
                    $("#chooseAnnouncement").modal("hide");
                });
            });

            $("#frame .dropdown .quickMentions").on("click touchstart", function () {
                $(".modal_container").load(prchatSettings.quickMentions, function (res) {
                    if ($(".modal-backdrop.fade").hasClass("in")) {
                        $(".modal-backdrop.fade").remove();
                    }
                    $("#quickMentionsModal").modal({
                        show: true
                    });
                });
            });
        });
        /*---------------* Modal create group handler  *---------------*/
        $("#frame .dropdown .i_groups, #frame #sidepanel #add_group_btn").on("click touchstart", function () {
            $(".modal_container").load(prchatSettings.chatGroups, function (res) {
                if ($(".modal-backdrop.fade").hasClass("in")) {
                    $(".modal-backdrop.fade").remove();
                }
                if ($("#chat_groups_custom_modal").is(":hidden")) {
                    $("#chat_groups_custom_modal").modal({
                        show: true
                    });
                }
            });
        });


        /*---------------* Some cached variables for group chat  *---------------*/
        var chat_group_messages = $("#frame .content .group_messages .chat_group_messages");
        var chat_client_messages = $("#frame .content .client_messages .chat_client_messages");
        var chat_contact_profile_img = $("#frame .content .contact-profile img");
        var chat_social_media = $("#frame .content .social-media");
        var chat_content_messages = $("#frame .content .messages");
        var chat_content_client_messages = $("#frame .content .client_messages");

        var changeSearchField = function () {
            $("#search #search_clients_field").attr("id", "search_field");
            $("#search #search_field").attr("placeholder", "<?= _l('chat_search_chat_members'); ?>");
            $(".chat_contacts_list li").show();
        };

        /*---------------* Click event for staff users sidebar  *---------------*/
        $("#frame #sidepanel .staff a").click(function () {
            $("#mainFilterDiv").show();
            $(".actionTask").removeClass("clients").show();
            $(".chat_contacts_list li a").show();
            // hide groups form
            $("#frame form[name=groupMessagesForm],#frame form[name=clientMessagesForm], #frame .groupOptions").hide();

            $("#frame .chat_group_options.active").hide().removeClass("active").css({
                "right": "-360px",
                "width": "360px"
            });
            $("#frame form[name=pusherMessagesForm], #frame .startMic").show();
            $(".client_data").remove();

            if ($(".group_members_inline").remove()) {

                chat_contact_profile_img.show();
                chat_contact_profile_img.next().show();
                chat_contact_profile_img.next().next().show();
                chat_social_media.show();
                chat_content_messages.show();

            }
            if (!optionsSelector.hasClass("active")) {
                optionsSelector.css("display", "");
            }

            clientsListCheck();

            changeSearchField();

            $(".group_members_inline").remove();
            if (!window.matchMedia("only screen and (max-width: 735px)").matches) {
                $("#frame #contacts ul li").first().children("a").first().click();
            }
        });
        /*---------------* Click event for groups sidebar  *---------------*/
        $("#frame #sidepanel .groups a").click(function () {
            $("#mainFilterDiv").hide();
            $(".actionTask").removeClass("clients").hide();
            // Hide staff chatbox form
            $("#frame form[name=pusherMessagesForm], #frame form[name=clientMessagesForm]").hide();

            $("#sharedFiles").hide().css({
                "right": "-360px",
                "width": "360px"
            });
            $(this).parents("li").removeClass("flashit");
            $(".client_data").remove();

            chat_group_messages.html("");
            chat_group_messages.append("<ul></ul>");

            // show groups form
            $("#frame .content .group_messages, #frame form[name=groupMessagesForm], #frame .startMic").show();

            chat_content_messages.hide();
            chat_content_client_messages.hide();
            chat_contact_profile_img.hide();
            chat_contact_profile_img.next().hide();
            chat_contact_profile_img.next().next().hide();

            if (!window.matchMedia("only screen and (max-width: 735px)").matches) {
                $("#frame ul.chat_groups_list li.active a").click();
            }

            var group_id = $("#frame ul.chat_groups_list li.active").attr("id");

            getGroupMessages(group_id);

            appendOptionsBar();

            clientsListCheck();

            changeSearchField();
            if ($(".chat_groups_list li").length == 0) {
                $(".contact-profile .groupOptions, .contact-profile .social-media").hide();
            }
            if (group_id == undefined) {
                $(".message_group_loader").hide();
            }
        });

        /**
         * Mobile device back button pressed on phone
         */
        window.addEventListener("hashchange", function (event) {
            if (window.matchMedia("only screen and (max-width: 735px)").matches && isContentActive) {
                $("body").find("#frame #sidepanel").fadeIn(50);
                $("body").find("#frame .content").hide(1);
                isContentActive = false;
            }
        });


        // Fix for height on the wrapper
        function chatPinnedProjectFix()
        {
            var pinned = $("li.pinned_project");
            if (pinned.length > 3 && !is_mobile()) {
                // Get and set current height
                var topHeaderHeight = 63;
                var navigationH = side_bar.height();
                var bodyContentHeight = $("#wrapper").find(".content").height();
                var content_wrapper = $("#sidepanel, #frame .content, body .main_loader_init, body #audio-wrapper");
                content_wrapper.css("min-height", $(document).outerHeight(true) - topHeaderHeight + "px");
                // Set new height when content height is less then navigation
                if (bodyContentHeight < navigationH) {
                    content_wrapper.css("min-height", navigationH + "px");
                }

                // Set new height when content height is less then navigation and navigation is less then window
                if (bodyContentHeight < navigationH && navigationH < $(window).height()) {
                    content_wrapper.css("min-height", $(window).height() - topHeaderHeight + "px");
                }
                // Set new height when content is higher then navigation but less then window
                if (bodyContentHeight > navigationH && bodyContentHeight < $(window).height()) {
                    content_wrapper.css("min-height", $(window).height() - topHeaderHeight + "px");
                }
            }
        }

        chatPinnedProjectFix();

        var isHostHttps = '<?= $isHttps; ?>';

        if (!isHostHttps) {
            $(".startMic").remove();
        } else {
            $(".startMic").css("display", "block");
        }

        $("#frame .message-input .wrap .search_messages").on("click", function () {
            var table = "chatmessages";
            var id = $(".tab-pane.fade.active.in .contact.active a").attr("id");
            $(".modal_container").load(prchatSettings.searchMessagesView, {
                    id: id,
                    table: table
                },
                function (res) {
                    $("#search_messages_modal").modal({
                        show: true
                    });
                });
        });

        $("body").on("hidden.bs.modal", "#search_messages_modal .modal", function () {
            $(this).removeData();
        });

        $("#frame .message-input .wrap .search_client_messages").on("click", function () {
            var table = "chatclientmessages";
            var id = $(".tab-pane.fade.active.in .company_selector.active .contact_name.selected").attr("id");
            $(".modal_container").load(prchatSettings.searchMessagesView, {
                    id: "client_" + id,
                    table: table
                },
                function (res) {
                    $("#search_messages_modal").modal({
                        show: true
                    });
                });
        });

        /**
         * Mark messages as seen pusher event
         */
        user_messages_events.bind("message_seen", function (messages) {

            var recieverId, senderId;
            if (Array.isArray(messages)) {
                for (var i = 0; i < messages.length; i++) {

                    let _msg = $(".client_messages#" + messages[i].reciever_id);

                    recieverId = messages[i].reciever_id;
                    senderId = messages[i].sender_id;

                    /** Clients */
                    if (messages[i].sender_id.startsWith("staff_")) {
                        _msg.find("i.circle-unseen").remove();
                        userSeenNotify(userSessionId);
                        return;
                    }

                    /** Staff */
                    if (messages[i].reciever_id == $("li.contact a.active_chat").attr("id") && messages[i].sender_id == userSessionId) {
                        $(".messages#id_" + messages[i].reciever_id).find("i.circle-unseen").remove();
                    }
                }
                ;

                if (senderId.startsWith("staff")) {
                    if (senderId.replace("staff", "") == userSessionId) {
                        userSeenNotify(userSessionId);
                    }
                } else {
                    if (senderId == userSessionId) {
                        userSeenNotify(userSessionId);
                    }
                }
            }
        });

        // Create new task from chat related to staff
        function chatNewTask(userInfo)
        {
            var user_name = $("body").find("a.active_chat p.name").text();
            var rel_id = $("body").find("a.active_chat").attr("id");
            var rel_type = "staff";

            if ($("body").find(".crm_clients").hasClass("active")) {
                user_name = $("body").find(".contact_name.selected").attr("data-name");
                rel_id = $("body").find(".company_selector.active").children("li.contact_name.selected").attr("id");
                rel_type = "customer";
            }

            var url = admin_url + "tasks/task?rel_id=" + rel_id + "&rel_type=" + rel_type;

            new_task(url);

            var infoMessage = `<div class="alert alert-info" id="_infoMessage"><span><?= _l("chat_task_info_message") ?></span></div>`;

            $("#_task").on("show.bs.modal", function (e) {
                $("body").find("#_infoMessage").remove();

                if (rel_type == "customer") {
                    $("body").find("#_task div[app-field-wrapper=\"name\"]").prepend(infoMessage);
                }

                $("body").find("#_task #rel_id_wrapper .rel_id_label").text("checked");
                $("body").find("#_task #name").val("<?= _l("chat_task_assign"); ?>" + "" + user_name + "");
                $("body").find("#_task #task_is_billable").attr("checked", false);
            });
        }
    </script>

    <!-- Include chat groups file -->
<?php require('modules/prchat/assets/module_includes/groups.php'); ?>

<?php if (isClientsEnabled()) require('modules/prchat/assets/module_includes/crm_clients.php'); ?>

    <!-- Include chat sound settings file -->
<?php require('modules/prchat/assets/module_includes/chat_sound_settings.php'); ?>
