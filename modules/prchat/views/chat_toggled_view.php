<?php

defined('BASEPATH') or exit('No direct script access allowed');

$color = pr_get_chat_color(get_staff_user_id(), 'chat_color');
$currentChatColor = validateChatColorBeforeApply($color);

?>
<div class="modal_container"></div>
<div id="pusherChat">
  <div id="mainChatId" class="draggable" style="display:none;">
    <div id="membersContent">
      <div class="chatMain">
        <div class="topInfo" onclick="slideChat(this)" style="background:<?php echo $currentChatColor; ?>;">
          <p class="cname">
            <?php echo get_option('companyname'); ?>
          </p>
          <svg class="main_chat" data-toggle="tooltip" data-original-title="<?php echo _l('chat_browser_full_chat') ?>"
               data-placement="left" viewBox="0 0 24 24">
            <path d="M18,6V17H22V6M2,17H6V6H2M7,19H17V4H7V19Z"/>
          </svg>
        </div>
      </div>
      <div class="connection_field">
        <i class="fa fa-wifi blink"></i>
      </div>
      <div class="scroll">
        <div id="members-list"></div>
        <input class="form-control searchBox search_hidden"
               placeholder="<?php echo _l('chat_search_chat_members'); ?>"/>
      </div>
      <div class="chat-footer" style="background:<?php echo $currentChatColor; ?>">
        <div class="online" onclick="slideChat(this)">
          <?php echo _l('chat_online_users'); ?>
          <svg onclick="chatCircleTransform();" fill="#ffffff" data-toggle="tooltip"
               title="<?= _l('chat_toggle_circle_text'); ?>" class="toCircle" viewBox="0 0 24 24">
            <path
              d="M3,20.59L6.59,17H18A2,2 0 0,0 20,15V6A2,2 0 0,0 18,4H5A2,2 0 0,0 3,6V20.59M3,22H2V6A3,3 0 0,1 5,3H18A3,3 0 0,1 21,6V15A3,3 0 0,1 18,18H7L3,22M6,7H17V8H6V7M6,10H17V11H6V10M6,13H14V14H6V13Z"/>
          </svg>

          <span id="count">0</span>
        </div>
        <svg id="disableSound" data-toggle="tooltip" data-placement="left"
             title="<?= _l('chat_sound_notifications'); ?>" viewBox="0 0 24 24">
          <path
            d="M21,12.5C21,16.47 17.91,19.73 14,20V19C17.36,18.73 20,15.92 20,12.5C20,9.08 17.36,6.27 14,6V5C17.91,5.27 21,8.53 21,12.5M18,12.5C18,14.82 16.25,16.72 14,16.97V15.96C15.7,15.72 17,14.26 17,12.5C17,10.74 15.7,9.28 14,9.04V8.03C16.25,8.28 18,10.18 18,12.5M15,12.5C15,13.15 14.58,13.71 14,13.91V11.09C14.58,11.29 15,11.85 15,12.5M2,9H6L10,5H12V20H10L6,16H2V9M3,15H6.41L10.41,19H11V6H10.41L6.41,10H3V15Z"/>
        </svg>
        <svg data-toggle="tooltip" title="<?= _l('chat_search_chat_members'); ?>" id="searchUsers" viewBox="0 0 24 24">
          <path
            d="M9.5,4A6.5,6.5 0 0,1 16,10.5C16,12.12 15.41,13.6 14.43,14.73L20.08,20.38L19.37,21.09L13.72,15.44C12.59,16.41 11.11,17 9.5,17A6.5,6.5 0 0,1 3,10.5A6.5,6.5 0 0,1 9.5,4M9.5,5A5.5,5.5 0 0,0 4,10.5A5.5,5.5 0 0,0 9.5,16A5.5,5.5 0 0,0 15,10.5A5.5,5.5 0 0,0 9.5,5Z"/>
        </svg>
        <div class="dropup">
          <button class="btn btn-primary dropdown-toggle gradientButton" type="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
            <svg id="colorGradientChanger" aria-hidden="true" data-toggle="tooltip"
                 data-original-title="<?= _l('chat_color_settings'); ?>" viewBox="0 0 24 24">
              <path
                d="M19.59,15.5L17.77,14.2C18.07,13.12 18.09,11.95 17.77,10.78L19.59,9.5L18.14,7L16.11,7.92C15.32,7.12 14.32,6.5 13.15,6.21L12.95,4H10.05L9.85,6.21C8.68,6.5 7.68,7.12 6.89,7.92L4.86,7L3.41,9.5L5.23,10.78C4.91,11.95 4.93,13.12 5.23,14.2L3.41,15.5L4.86,18L6.89,17.07C7.68,17.86 8.68,18.46 9.85,18.77L10.05,21H12.95L13.15,18.77C14.32,18.46 15.32,17.86 16.11,17.07L18.14,18L19.59,15.5M13.5,3C13.77,3 14,3.2 14,3.46L14.18,5.5C14.94,5.78 15.62,6.19 16.23,6.68L18.08,5.81C18.31,5.69 18.6,5.77 18.74,6L20.74,9.5C20.88,9.71 20.8,10 20.58,10.15L18.91,11.32C19.04,12.12 19.03,12.91 18.91,13.68L20.58,14.85C20.8,15 20.88,15.29 20.74,15.5L18.74,19C18.6,19.21 18.31,19.29 18.08,19.17L16.23,18.31C15.62,18.8 14.94,19.2 14.18,19.5L14,21.5C14,21.79 13.77,22 13.5,22H9.5A0.5,0.5 0 0,1 9,21.5L8.82,19.5C8.06,19.2 7.38,18.8 6.77,18.31L4.92,19.17C4.69,19.29 4.4,19.21 4.26,19L2.26,15.5C2.12,15.29 2.2,15 2.42,14.85L4.09,13.68C3.97,12.91 3.96,12.12 4.09,11.32L2.42,10.15C2.2,10 2.12,9.71 2.26,9.5L4.26,6C4.4,5.77 4.69,5.69 4.92,5.81L6.77,6.68C7.38,6.19 8.06,5.78 8.82,5.5L9,3.46C9,3.2 9.23,3 9.5,3H13.5M11.5,9A3.5,3.5 0 0,1 15,12.5A3.5,3.5 0 0,1 11.5,16A3.5,3.5 0 0,1 8,12.5A3.5,3.5 0 0,1 11.5,9M11.5,10A2.5,2.5 0 0,0 9,12.5A2.5,2.5 0 0,0 11.5,15A2.5,2.5 0 0,0 14,12.5A2.5,2.5 0 0,0 11.5,10Z"/>
            </svg>
          </button>
          <ul class="dropdown-menu" id="colorChangerMenu">
            <li>
              <a href="#" id="colorChanger"><i class="fa fa-paint-brush"
                                               aria-hidden="true"></i><?= _l('chat_solid_color_text'); ?>
              </a></li>
            <li><a href="#" id="colorGradient"></i>
                <svg viewBox="0 0 24 24">
                  <path
                    d="M19,11.5C19,11.5 17,13.67 17,15A2,2 0 0,0 19,17A2,2 0 0,0 21,15C21,13.67 19,11.5 19,11.5M5.21,10L10,5.21L14.79,10M16.56,8.94L7.62,0L6.21,1.41L8.59,3.79L3.44,8.94C2.85,9.5 2.85,10.47 3.44,11.06L8.94,16.56C9.23,16.85 9.62,17 10,17C10.38,17 10.77,16.85 11.06,16.56L16.56,11.06C17.15,10.47 17.15,9.5 16.56,8.94Z"/>
                </svg><?= _l('chat_gradient_color_text'); ?></a></li>
            <li>
              <a href="#" id="resetColors" onClick="resetChatColors()"><i class="fa fa-refresh"
                                                                          aria-hidden="true"></i><?= _l('chat_reset_color_text'); ?>
              </a></li>
          </ul>
        </div>
        <div class="form-inline colorHolder">
          <form method="POST" name="solidForm" style="display: none"
                action="<?php echo site_url('prchat/Prchat_Controller/colorchange/'); ?>"
                onsubmit="changeColor(this); return false;">
            <input type="text" name="color" class="form-control jscolor float-right chat_color"
                   value="<?php echo $currentChatColor; ?>" required/>
            <button class="btn btn-success btn-sm" id="chColor" type="submit">
              <?php echo _l('chat_change_color'); ?>
            </button>
            <button type="button" class="btn btn-secondary  closeColorButton">Close</button>
          </form>
          <form method="POST" name="gradientForm" style="display: none"
                action="<?php echo site_url('prchat/Prchat_Controller/colorchange/'); ?>"
                onsubmit="changeColor(this); return false;">
            <input type="text" name="color" class="form-control float-right chat_color" value="" required
                   placeholder="<?php echo _l('chat_color_example_type'); ?>"/>
            <button class="btn btn-success btn-sm" id="chGradientColor" type="submit">
              <?php echo _l('chat_change_color'); ?>
            </button>
            <button type="button" class="btn btn-secondary closeColorButton">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    // Positions of chat and main chat append on browser when load
    var positions = JSON.parse(localStorage.positions || "{}");
    $.each(positions, function (id, pos) {
      $("#pusherChat #" + id).css(pos);
    });
    setTimeout(function () {
      $("#mainChatId").css("display", "block");
    }, 200);
  </script>
  <!-- Chat Box Template -->
  <div id="templateChatBox">
    <div class="pusherChatBox">
      <span class="state">
        <span class="userIsTyping"><img
            src="<?php echo module_dir_url('prchat', 'assets/chat_implements/userIsTyping.gif'); ?>"/></span>
        <span class="quote">
          <div class="notification-box">
            <span class="notification-count">0</span>
            <div class="notification-bell">
              <span class="bell-top"></span>
              <span class="bell-middle"></span>
              <span class="bell-bottom"></span>
              <span class="bell-rad"></span>
            </div>
          </div>
        </span>
      </span>
      <span class="user_view_selector">
        <svg class="user_view" data-toggle="tooltip" data-original-title="<?php echo _l('chat_browser_full_chat') ?>"
             data-placement="left" viewBox="0 0 24 24">
          <path d="M18,6V17H22V6M2,17H6V6H2M7,19H17V4H7V19Z"/>
        </svg>
      </span>
      <span class="closeBox">
        <svg data-toggle="tooltip" title="<?= _l('close'); ?>" viewBox="0 0 24 24">
          <path d="M5,13V12H11V6H12V12H18V13H12V19H11V13H5Z"/>
        </svg>
      </span>
      <chatHead class="chat-head" style="background:<?php echo $currentChatColor; ?>" onclick="slideChat(this)">
        <span class="userName"></span>
      </chatHead>

      <div class="slider">
        <div class="logMsg">
          <svg class="message_loader" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
          </svg>
          <div class="msgTxt">
          </div>
        </div>
        <div class="fileUpload" data-toggle="tooltip" title="<?php echo _l('chat_file_upload'); ?>">
          <svg viewBox="0 0 24 24">
            <path
              d="M15,11A3,3 0 0,1 12,8V4H8A2,2 0 0,0 6,6V19A2,2 0 0,0 8,21H17A2,2 0 0,0 19,19V11H15M13,8A2,2 0 0,0 15,10H18.59L13,4.41V8M8,3H13L20,10V19A3,3 0 0,1 17,22H8A3,3 0 0,1 5,19V6A3,3 0 0,1 8,3M8,24A5,5 0 0,1 3,19V7H4V19A4,4 0 0,0 8,23H16V24H8Z"/>
          </svg>
        </div>
        <form hidden enctype="multipart/form-data" name="fileForm" method="post"
              onsubmit="uploadFileForm(this);return false;">
          <input type="file" class="file" name="userfile" required/>
          <input type="submit" name="submit" class="save" value="save"/>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                 value="<?php echo $this->security->get_csrf_hash(); ?>">
        </form>
        <form method="post" enctype="multipart/form-data" name="pusherMessagesForm" onsubmit="return false;">
          <div>
            <div class="enterBtn">
              <svg class="fa-paper-plane" viewBox="0 0 24 24">
                <path
                  d="M8,7.71L18,12L8,16.29V12.95L15.14,12L8,11.05V7.71M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z"/>
              </svg>
            </div>
            <textarea name="msg" class="chatbox" rows="3"
                      placeholder="<?php echo _l('chat_type_a_message'); ?>"></textarea>
            <input type="hidden" name="from" class="from"/>
            <input type="hidden" name="to" class="to"/>
            <input type="hidden" name="typing" class="typing" value="false"/>
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                   value="<?php echo $this->security->get_csrf_hash(); ?>">
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Chat Box Template End -->
<div class="chatBoxWrap">
  <div class="chatBoxslide"></div>
  <span id="slideLeft"><i class="fa fa-angle-double-left" aria-hidden="true"></i></span>
  <span id="slideRight"><i class="fa fa-angle-double-right" aria-hidden="true"></i></span>
</div>
</div>
<!-- Chat Template End -->

<!-- Include chat settings file -->
<?php require('modules/prchat/assets/module_includes/chat_settings.php'); ?>
<!-- Include chat statuses file -->
<?php require('modules/prchat/assets/module_includes/chat_statuses.php'); ?>

<script>
  $(function () {
    "use strict";

    if (localStorage.isToggled == "true") {
      $("#membersContent").hide(function () {
        setTimeout(function () {
          $("#membersContent").show();
        }, 2000);
      });
    }
  });

  window.addEventListener("online", handleConnectionChange);
  window.addEventListener("offline", handleConnectionChange);
  // Parse emojies in chat area do not touch
  emojify.setConfig({
    emojify_tag_type: "div",
    "img_dir": site_url + "/modules/prchat/assets/chat_implements/emojis"
  });
  emojify.run();

  var getCurrentBackgound = "";
  var prevBackground = "<?php echo $currentChatColor; ?>";
  var pageTitle = $("title").html();
  var pusherKey = "<?php echo get_option('pusher_app_key') ?>";
  var appCluster = "<?php echo get_option('pusher_cluster') ?>";
  var staffFullName = "<?php echo get_staff_full_name(); ?>";
  var userSessionId = "<?php echo get_staff_user_id(); ?>";
  var chat_desktop_notifications_enabled = "<?php echo get_option('chat_desktop_messages_notifications') ?>";
  chat_desktop_notifications_enabled = (chat_desktop_notifications_enabled == "0") ? false : true;
  var user_chat_status = "<?= get_user_chat_status(); ?>";

  $("#pusherChat").on("click", ".fileUpload", function () {
    $(this).parents(".pusherChatBox").find("form input:first").trigger("click");
  });

  $("#pusherChat").on("change", "input[type=file]", function () {
    var id = $(this).attr("name");
    $("form#" + id).submit();
  });

  function uploadFileForm(file) {
    var formData = new FormData();
    var fileForm = $(file).children("input[type=file]")[0].files[0];
    var sentTo = $(file).attr("id");
    var token_name = $(file).children("input:nth-child(3)").val();
    var formId = $(file).attr("id");

    formData.append("userfile", fileForm);
    formData.append("send_to", sentTo);
    formData.append("send_from", userSessionId);
    formData.append("csrf_token_name", token_name);

    $.ajax({
      type: "POST",
      url: '<?php echo site_url('prchat/Prchat_Controller/uploadMethod'); ?>',
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      beforeSend: function () {
        $(".pusherChatBox#" + sentTo).prepend("<div class=\"chat-module-loader\"><div></div><div></div><div></div></div>");

        var Regex = new RegExp("\[~%:\()@]");
        if (Regex.test(fileForm.name)) {
          alert_float("warning", '<?php echo _l('chat_permitted_files') ?>');
          $(".pusherChatBox#" + sentTo + " .chat-module-loader").fadeOut(function () {
            $(this).remove();
          });
          return false;
        }
      },
      success: function (r) {

        if (r.error) {
          alert_float("danger", r.error);
          $(".pusherChatBox#" + sentTo + " .chat-module-loader").fadeOut(function () {
            $(this).remove();
          });
          return;
        }

        const uploadSend = $.Event("keypress", {
          which: 13
        });
        var basePath = "<?php echo module_dir_url('prchat', 'uploads/'); ?>";

        $("#pusherChat .pusherChatBox#" + formId + " textarea").val(basePath + r.upload_data.file_name);
        setTimeout(function () {
          if ($("#pusherChat .pusherChatBox#" + formId + " textarea").trigger(uploadSend)) {
            alert_float("info", "File " + r.upload_data.file_name + " sent.");
            $(".pusherChatBox#" + sentTo + " .chat-module-loader").fadeOut(function () {
              $(this).remove();
            });
          }
        }, 100);

        var messagesContainer = $("#pusherChat .pusherChatBox#" + formId + " .logMsg");
        messagesContainer.animate({
          scrollTop: messagesContainer.prop("scrollHeight")
        }, 1000);

      }
    });
    $("form#" + formId).trigger("reset");
  }

  function loadMessages(el) {
    var pos = $(el).scrollTop();
    var id = $(el).attr("id");
    var to = $(el).parents().find(".pusherChatBox#" + id).attr("id").replace("id_", "");
    var from = userSessionId;
    var optionsMore = "";

    if (endOfScroll[to] == true) {
      prchat_setNoMoreMessages(to);
      return false;
    }

    if (pos == 0) {

      activateLoader(id, true);
      initiatePrepending = function () {
        $.get(prchatSettings.getMessages, {
          from: from,
          to: to,
          offset: offsetPush[to]
        }).done(function (message) {

          message = JSON.parse(message);

          if (Array.isArray(message) == false) {
            endOfScroll[to] = true;
            prchat_setNoMoreMessages(to);
          } else {
            offsetPush[to] += 10;
          }

          $(message).each(function (key, value) {

            value.message = emojify.replace(value.message);

            if (value.message.includes("class='quickMentionLink'")) {
              value.message = unescapeHtml(value.message);
            }

            value.message = ifAudioRender(value.message);

            var element = $(".pusherChatBox#id_" + to + " .logMsg .msgTxt");

            optionsMore = deleteOrForward(value.id);

            if (value.reciever_id == from) {
              element.prepend("<div class=\"conversation_from\"><img class=\"friendProfilePic\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/></br><div class=\"message_container\">\"<p data-toggle=\"tooltip\" title=\"" + value.time_sent_formatted + "\" class=\"friend\">" + value.message + "</p>" + optionsMore + "</div></div>");
            } else {
              element.prepend("<div class=\"conversation_me\"><img class=\"myProfilePic\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/></br><div class=\"message_container\"><p data-toggle=\"tooltip\" title=\"" + value.time_sent_formatted + "\" class=\"you\" id=\"" + value.id + "\" style=\"background:" + prchatSettings.getChatColor + "\">" + value.message + "</p>" + optionsMore + "</div></div>");
            }
          });
          if (endOfScroll[to] == false) {
            $(el).scrollTop(200);
          }
        });
      };
    }
  }

  $("#pusherChat").on("click", "#disableSound", function () {
    if (isSoundMuted == "") {
      isSoundMuted = "muted";
      $(this).find("path").attr("d", "M2.79,4.46L3.5,3.75L20.25,20.5L19.54,21.21L17.3,18.97C16.32,19.54 15.2,19.9 14,20V19C14.92,18.91 15.79,18.65 16.57,18.23L15.06,16.72C14.72,16.85 14.37,16.93 14,16.97V15.96C14.09,15.95 14.17,15.94 14.25,15.92L12,13.66V20H10L6,16H2V9H6L6.67,8.33L2.79,4.46M21,12.5C21,14.53 20.19,16.37 18.88,17.72L18.18,17C19.31,15.84 20,14.25 20,12.5C20,9.08 17.36,6.27 14,6V5C17.91,5.27 21,8.53 21,12.5M18,12.5C18,13.7 17.53,14.79 16.76,15.6L16.06,14.89C16.64,14.27 17,13.42 17,12.5C17,10.74 15.7,9.28 14,9.04V8.03C16.25,8.28 18,10.18 18,12.5M15,12.5C15,12.87 14.86,13.21 14.64,13.5L14,12.84V11.09C14.58,11.29 15,11.85 15,12.5M6.41,10H3V15H6.41L10.41,19H11V12.66L7.38,9.04L6.41,10M10,5H12V10.84L11,9.84V6H10.41L8.79,7.63L8.08,6.92L10,5Z");
    } else if (isSoundMuted == "muted") {
      $(this).find("path").attr("d", "M21,12.5C21,16.47 17.91,19.73 14,20V19C17.36,18.73 20,15.92 20,12.5C20,9.08 17.36,6.27 14,6V5C17.91,5.27 21,8.53 21,12.5M18,12.5C18,14.82 16.25,16.72 14,16.97V15.96C15.7,15.72 17,14.26 17,12.5C17,10.74 15.7,9.28 14,9.04V8.03C16.25,8.28 18,10.18 18,12.5M15,12.5C15,13.15 14.58,13.71 14,13.91V11.09C14.58,11.29 15,11.85 15,12.5M2,9H6L10,5H12V20H10L6,16H2V9M3,15H6.41L10.41,19H11V6H10.41L6.41,10H3V15Z");
      isSoundMuted = "";
    }
  });

  $("#pusherChat").on("click", ".enterBtn", function () {
    const eventEnter = $.Event("keypress", {
      which: 13
    });
    $(this).parents(".pusherChatBox").find("textarea").trigger(eventEnter);
  });

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

  var pusher = new Pusher(pusherKey, {
    authEndpoint: "<?php echo site_url('prchat/Prchat_Controller/pusher_auth'); ?>",
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
  var user_messages_events_small = pusher.subscribe("user_messages");

  /*---------------* Pusher Trigger subscription succeeded *---------------*/
  presenceChannel.bind("pusher:subscription_succeeded", function (members) {
    chatMemberUpdate(true);
    if (!$(".liveUsers").length) {
      $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (presenceChannel.members.count - 1) + "</span>");
    }
  });

  /*---------------* Pusher Trigger user connected *---------------*/
  presenceChannel.bind("pusher:member_added", function (member) {
    chatMemberUpdate();
    addChatMember(member);
    if (member.info.justLoggedIn == true) {
      $(".liveUsers").remove();
      $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (presenceChannel.member.count - 1) + "</span>");

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
        }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
      });
    }
  });

  /*---------------* Pusher Trigger user logout *---------------*/
  presenceChannel.bind("pusher:member_removed", function (members) {
    removeChatMember(members);
  });

  /*---------------* Bind the 'send-event' & update the chat box message log *---------------*/
  presenceChannel.bind("send-event", function (data) {
    if (data.global) {
      data.message = "<?= '<strong>' . _l('chat_message_announce') . '</strong>'; ?>" + data.message;
    }
    var current_time = new Date().toLocaleTimeString();
    var obj = $("a[href=\\#" + data.from + "]");

    var optionsMore = deleteOrForward(userSessionId);

    if (presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id) {
      if (app.options.desktop_notifications && chat_desktop_notifications_enabled) {
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
              "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000
            }).show(function (e) {
              window.focus();
              setTimeout(function () {
                e.target.close();
              }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
            });
          }, 150);
        }
      }
      if (user_chat_status != "busy" && user_chat_status != "offline") {
        playPushSound();
      }
      if ($(".pusherChatBox.on#id_" + data.from).hasClass("stillActive")) {
        $(".pusherChatBox#id_" + data.from).css("display", "block");
        updateBoxPosition(obj);
      }
      data.message = createTextLinks_(emojify.replace(data.message));

      var pusherFrom = $("#pusherChat .pusherChatBox#id_" + data.from);
      var pusherDataLogMsg = $("#pusherChat .pusherChatBox#id_" + data.from + " .logMsg");
      var name = $(".pusherChatBox#id_" + data.from).find(".userName").html();

      $("#pusherChat .pusherChatBox#id_" + data.from + " .state").show();
      pusherFrom.addClass("stillActive");
      pusherFrom.addClass("receiveMsg").removeClass("writing");
      pusherDataLogMsg.find(".msgTxt").show();
      pusherFrom.find(".userName").addClass("newMessage");

      data.message = ifAudioRender(data.message);

      $("#pusherChat .pusherChatBox#id_" + data.from + " .msgTxt").append("<div class=\"conversation_from\"><img class=\"friendProfilePic\" data-toggle=\"tooltip\" title=\"" + current_time + "\" src=\"" + fetchUserAvatar(data.from, data.sender_image) + "\"/></br><div class=\"message_container\"><p class=\"friend\">" + data.message + "</p>" + optionsMore + "<div class=\"fixinline\"></div></div></div>");
      $("title").html("");
      if ($("title").text().search('<?php echo _l('chat_sent_you_a_message'); ?>') == -1) {
        if (name !== undefined) {
          $("title").text(name + ' <?php echo _l('chat_sent_you_a_message'); ?>');
        } else {
          $("title").text('<?php echo _l('chat_you_have_a_new_message'); ?>');
        }
        if ($(".pusherChatBox#id_" + data.from).is(":hidden") &&
          user_chat_status != "busy" ||
          $(".pusherChatBox#id_" + data.from).is(":hidden") &&
          user_chat_status != "offline") {
          playPushSound();
        }
      }
      createChatBox(obj);
      $("#pusherChat .pusherChatBox#id_" + data.from + " .logMsg").scrollTop($("#pusherChat .pusherChatBox#id_" + data.from + " .logMsg")[0].scrollHeight);
    }
    if (presenceChannel.members.me.id == data.from) {

      data.message = createTextLinks_(emojify.replace(data.message));

      $("#pusherChat .pusherChatBox#id_" + data.to + " .msgTxt").append("<div class=\"conversation_me\"><img class=\"myProfilePic\" data-toggle=\"tooltip\" title=\"" + current_time + "\" src=\"" + fetchUserAvatar(userSessionId, data.sender_image) + "\"/></br><div class=\"message_container\"><p class=\"you\" style=\"background:" + getCurrentBackgound + "\" id=\"" + data.last_insert_id + "\">" + data.message + "</p>" + optionsMore + '</div><i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i></div><div class="fixinline"></div>');
      var pusherDatalogMsgTo = $("#pusherChat .pusherChatBox#id_" + data.to + " .logMsg");
      if (pusherDatalogMsgTo != "undefined") {
        pusherDatalogMsgTo.scrollTop(pusherDatalogMsgTo[0].scrollHeight);
      }
    }
  });


  /*---------------* Detect when a user is typing a message *---------------*/
  presenceChannel.bind("typing-event", function (data) {
    if (presenceChannel.members.me.id == data.to && data.from !== presenceChannel.members.me.id && data.message == "true") {
      $("#id_" + data.from).find("span.userIsTyping img").show();
      $("#id_" + data.from).addClass("writing");
    } else if (presenceChannel.members.me.id == data.to && data.from != presenceChannel.members.me.id && data.message == "null") {
      $("#id_" + data.to).find("span.userIsTyping img").fadeOut();
      $("#id_" + data.to).removeClass("writing");
    }
  });

  /*---------------* Trigger notification popup increment*---------------*/
  presenceChannel.bind("notify-event", function (data) {
    var chatBox = $(".pusherChatBox.on#id_" + data.from).find(".chatbox");
    var notiBox = $(".pusherChatBox.on#id_" + data.from).find(".notification-box");
    var notiCount = $(".pusherChatBox.on#id_" + data.from).find(".notification-count");
    if (!chatBox.is(":focus")) {
      var notiValue = parseInt(notiCount.html());
      if (notiBox.is(":hidden")) {
        $(notiBox.show());
      }
      $(notiCount.html(notiValue = notiValue + 1));
    } else {
      $(notiBox).hide();
    }
  });

  /*---------------* Trigger when user stop typing *---------------*/
  $("#pusherChat").on("focusout", ".chatbox", function () {
    var from = $(this).parents("form");
    if ($(this).next().next().next().val() == "true") {
      $.post(prchatSettings.serverPath, from.serialize());
      $(this).next().next().next().val("null");
    }
  });

  /*---------------* Slide up & down users list & chat boxes, update messages *---------------*/
  $("#pusherChat").on("click", ".pusherChatBox chathead", function (event) {
    $("title").html("");

    var obj = $(this);
    var id = obj.parent().attr("id");
    var slider = $("#pusherChat .pusherChatBox#" + id + " .slider");
    var uName = $("#pusherChat .pusherChatBox#" + id + " .userName");
    var isSliderActive = $.Deferred();
    isSliderActive.promise(slider);
    // Resolve the deferred
    isSliderActive.resolve(slider);
    $("#pusherChat .pusherChatBox#" + id + " .logMsg").scrollTop($("#pusherChat .pusherChatBox#" + id + " .logMsg")[0].scrollHeight);
    isSliderActive.done(function (select) {
      setTimeout(function () {
        if (!select.is(":visible")) {
          $("#pusherChat .pusherChatBox#" + id).addClass("hanging");
        }
      }, 500);
    });
    uName.removeClass("newMessage");
  });

  /*---------------* Close chatbox, update messages *---------------*/
  $("#pusherChat").on("click", ".closeBox", function (event) {
    $("title").html("");
    soundFinished = false;
    var id = $(this).parents(".pusherChatBox").attr("id");
    var updateId = $(this).parents(".pusherChatBox").attr("id").replace("id_", "");
    removeActiveChatWindow(updateId);
    var chatBox = $(this).parents(".pusherChatBox#" + id);
    var selector = $("#pusherChat .pusherChatBox#" + id + " .slider");
    $(selector).find(".fileUpload, .enterBtn").css("display", "block");
    $(this).parents(".pusherChatBox#" + id).hide();
    $(this).parents(".pusherChatBox.on#" + id).addClass("stillActive");
    $(this).parents(".pusherChatBox#" + id).removeClass("hanging");
    $(this).parents(".pusherChatBox#" + id + " .userName").removeClass("newMessage");
    $(chatBox).find(".slider").addClass("animated fadeIn").show();
    $(chatBox).find(".notification-count").text("0");
    updateBoxPosition();
    return false;
  });

  /*---------------* Trigger click on user & create chat box and check for messages *---------------*/
  $("#pusherChat #members-list").on("click", "a", function (event) {

    $("#pusherChat .scroll").animate({
      scrollTop: 0
    });

    var obj = $(this);
    var id = obj.attr("id");

    addActiveChatWindow({
      id: id,
      fullName: obj.find(".user-name").text().trim()
    });

    var hasActiveWindowClickClass = $(this).hasClass("active-windows-click");
    createChatBox(obj);

    var chatBox = obj.parents("#pusherChat").find(".pusherChatBox#id_" + id);
    var notiBox = $(this).children(".unread-notifications").data("badge");

    if (!hasActiveWindowClickClass && notiBox > 0) {
      updateUnreadMessages(this, chatBox);
    }

    if ($(chatBox).is(":visible") && !$(chatBox).hasClass("manually-added")) {
      $(chatBox).find(".slider").addClass("animated fadeIn").show();
    }

    $(chatBox).removeClass("manually-added");

    if ($(chatBox).hasClass("on")) {
      $("#pusherChat .pusherChatBox#id_" + id + " .logMsg").scrollTop($("#pusherChat .pusherChatBox#id_" + id + " .logMsg")[0].scrollHeight);
    }
  });


  $("#slideLeft").on("click", function () {
    $(".chatBoxslide .pusherChatBox:visible:first").addClass("overFlowHide");
    $(".chatBoxslide .pusherChatBox.overFlow").removeClass("overFlow");
    updateBoxPosition();
  });

  $("#slideRight").on("click", function () {
    $(".chatBoxslide .pusherChatBox.overFlowHide:last").removeClass("overFlowHide");
    updateBoxPosition();
  });

  /*--------------------  * send message & typing event to server  * ------------------- */
  $("#pusherChat").on("keypress", ".pusherChatBox textarea", function (e) {
    var form = $(this).parents("form");
    var chatId = $(form).parents().parent(".pusherChatBox").attr("id");

    if (e.which == 13 && !e.shiftKey) {
      var message = $(this).val();
      if (message.trim() == "" || internetConnectionCheck() === false) {
        return false;
      }

      var msgTxt = $(".logMsg").find(".msgTxt");
      if (!$(msgTxt).is(":visible")) {
        $(".logMsg").find(".msgTxt").show();
      }

      $("#pusherChat #" + chatId + " .logMsg").scrollTop($("#pusherChat #" + chatId + " .logMsg")[0].scrollHeight); // just in case
      $(this).next().next().next().val("false");
      // Send event
      $.post(prchatSettings.serverPath, form.serialize());
      e.preventDefault();
      $(this).val("");
      $(this).focus();
    } else if (!$(this).val() || ($(this).next().next().next().val() == "null" && $(this).val())) {
      // Typing event
      $(this).next().next().next().val("true");
      $.post(prchatSettings.serverPath, form.serialize());
    }
  });

  /*-----------------------    * additional dynamic styling  *-----------------------*/
  $("#pusherChat .chatBoxWrap").css({
    "width": $(window).width() - $("#membersContent").width() - 30
  });

  $(window).resize(function () {
    $("#pusherChat .chatBoxWrap").css({
      "width": $(window).width() - $("#membersContent").width() - 30
    });
    updateBoxPosition();
  });

  /*---------------* Additional checks for chatbox and unread message update control *---------------*/
  $("#pusherChat").on("click", ".logMsg, .chatbox", function (e) {
    var member_id = $(this).prev("div.enterBtn").attr("id") || $(this).attr("id");
    var pusherChatBox = $(this).parents(".pusherChatBox#" + member_id);
    pusherChatBox.find(".userName").removeClass("newMessage");
    var toUpdate = pusherChatBox.children(".state").children(".quote").find(".notification-box .notification-count").text();
    $("title").html("");
    if (toUpdate > 0) {
      updateUnreadMessages(this, pusherChatBox);
    }
  });

  /*---------------* prevent showing dots if user is not typing *---------------*/
  $("#pusherChat").on("focus", ".chatbox", function () {
    $(".pusherChatBox.on.writing").find("span.userIsTyping img").fadeOut().removeClass("writing receiveMsg");
  });

  /*---------------* Search users *---------------*/
  $(".searchBox").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    $("#members-list a").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  /*---------------* On click show input search field and focus *---------------*/
  $("#searchUsers").click(function () {
    if ($(".searchBox").hasClass("search_hidden")) {
      if ($(".scroll").is(":hidden")) {
        $(".scroll").show();
      }
      localStorage.chat_head_position = "block";
      $(".searchBox").css("display", "block");
      $(".searchBox").focus();
    }
  });

  /*---------------* On focus out clear out input field and show all users if not found in searchbox *---------------*/
  $("#membersContent").keyup(".searchBox", function (e) {
    if (e.keyCode === 27) {
      clearSearchValues();
    }
  });
  $("#membersContent").focusout(".searchBox", function () {
    clearSearchValues();
  });

  /*---------------* Change Boxes, Chat color update in database and dynamically set color *---------------*/
  var solidColorForm = $("#membersContent").find("form[name=solidForm]");
  var gradientColorForm = $("#membersContent").find("form[name=gradientForm]");

  $(document).on("click", "#colorChanger", function () {
    gradientColorForm.hide();
    solidColorForm.show();
    return false;
  });

  $("body").on("click", ".closeColorButton", function () {
    $(this).parent("form").toggle();
  });

  $(document).on("click", "#colorGradient", function () {
    gradientColorForm.show();
    solidColorForm.hide();
    return false;
  });

  /*---------------* Responsible for loading message history and UI experience *---------------*/
  function slideChat(chatHead) {
    if ($(chatHead).hasClass("topInfo") || $(chatHead).hasClass("online")) {

      if (!$("#mainChatId").hasClass("main-chat-dragging")) {
        $(chatHead).parents("#membersContent").find(".scroll").slideToggle("fast");

        var scroll = $("#membersContent .scroll");
        if (localStorage.chat_head_position == "none") {
          localStorage.chat_head_position = "block";
        } else {
          localStorage.chat_head_position = "none";
        }
      } else {
        $("#mainChatId").removeClass("main-chat-dragging");
      }

    } else {
      if (prevBackground != getCurrentBackgound) {
        $(chatHead).parents(".pusherChatBox").find("p.you").attr("style", "background: " + getCurrentBackgound + " !important");
      }
      $(chatHead).next().slideToggle("fast");
      var box = $(chatHead).parents(".pusherChatBox");
      if (box.hasClass("hanging")) {
        var id = box.attr("id").replace("id_", "");
        $("#members-list").find("a#" + id).click();
      }
    }
  }

  /*---------------* Creating chat box from the html template to the DOM *---------------*/
  function createChatBox(obj) {
    var id = obj.attr("href");
    var message = "";
    var fullName = obj.children("span").text();
    var getMsgId = id.replace("#", "");
    id = id.replace("#", "id_");

    var off = "on";
    var optionsMore = "";
    var not_seen_icon = "";

    if (obj.hasClass("off")) {
      off = "off";
    }

    var fromActiveChatWindowsClick = obj.hasClass("active-windows-click");
    var onlyLoadMessages = $(".pusherChatBox#" + id).hasClass("hanging");

    var dfd = $.Deferred();
    var promise = dfd.promise();

    if (!$(".pusherChatBox#" + id).html() || onlyLoadMessages) {
      // Get class and append to pusherbox
      $(".pusherChatBox#" + id).removeClass("hanging");

      if (!onlyLoadMessages) {
        $.get(prchatSettings.getMessages, {
          from: userSessionId,
          to: getMsgId,
          offset: 0
        })
          .done(function (r) {
            r = JSON.parse(r);
            message = r;

            if (typeof (offsetPush[getMsgId]) == "undefined") {
              offsetPush[getMsgId] = 0;
            }

            if (typeof (endOfScroll[getMsgId]) == "undefined") {
              endOfScroll[getMsgId] = 0;
            }

            offsetPush[getMsgId] += 10;
            dfd.resolve(message);
          });
      } else {
        dfd.resolve([]);
      }

      $("#templateChatBox .pusherChatBox chatHead .userName").html(fullName);

      promise.then(function (message) {
        $(".pusherChatBox#" + id + " .logMsg .msgTxt").css("display", "block");
        if (!$(".pusherChatBox#" + id + " form:hidden").attr("id")) {
          $(".pusherChatBox#" + id + " form:hidden").attr("id", id);
          $(".pusherChatBox#" + id + " form:hidden input:first").attr("name", id);
          $(".pusherChatBox#" + id + " .enterBtn").attr("id", id);
        }
        if (obj.hasClass("on")) {
          $(message).each(function (key, value) {
            if (value.message.startsWith("<?= _l('chat_message_announce'); ?>")) {
              value.message = "<strong class=\"italic_small\">" + value.message + "</strong>";
            }

            if (value.message.includes("class='quickMentionLink'")) {
              value.message = unescapeHtml(value.message);
            }
            value.message = emojify.replace(value.message);
            value.message = ifAudioRender(value.message);

            optionsMore = deleteOrForward(value.id);

            var isViewed = value.viewed == 1;

            if (value.reciever_id == userSessionId) {
              $(".pusherChatBox#" + id + " .logMsg .msgTxt").prepend("<div class=\"conversation_from\"><img class=\"friendProfilePic\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" + value.time_sent_formatted + "\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/></br><div class=\"message_container\"><p class=\"friend\">" + value.message + "</p>" + optionsMore + "<div class=\"fixinline\"></div></div></div>");
            } else {

              if (!isViewed) {
                not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
              }

              $(".pusherChatBox#" + id + " .logMsg .msgTxt").prepend('<div class="conversation_me"><img class="myProfilePic" data-toggle="tooltip" data-placement="top" title=" <?= _l('chat_sent_at') ?>' + value.time_sent_formatted + "\" src=\"" + fetchUserAvatar(userSessionId, value.user_image) + "\"/></br><div class=\"message_container\"><p data-toggle=\"tooltip\"  data-placement=\"top\" id=\"" + value.id + "\" data-title=\"" + (!isViewed ? '<?= _l('chat_not_seen'); ?>' : '<?= _l('chat_msg_seen'); ?> ' + moment(value.viewed_at).format("h:mm:ss A, DD MMM YYYY")) + "\" class=\"you\">" + value.message + "</p>" + optionsMore + "</div>" + not_seen_icon + "<div class=\"fixinline\"></div></div>");
            }
          });
          // $("#pusherChat #" + id + " .logMsg").scrollTop($("#pusherChat #" + id + " .logMsg")[0].scrollHeight);
          // $("#pusherChat #" + id + " .userName").addClass("newMessage");
        } else if (obj.hasClass("off")) {
          $(message).each(function (key, value) {

            value.message = emojify.replace(value.message);

            if (value.message.includes("class='quickMentionLink'")) {
              value.message = unescapeHtml(value.message);
            }

            optionsMore = deleteOrForward(value.id);

            var isViewed = value.viewed == 1;

            if (!isViewed) {
              not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
            }

            value.message = ifAudioRender(value.message);
            if (value.reciever_id == userSessionId) {
              $(".pusherChatBox#" + id + " .logMsg .msgTxt").prepend("<div class=\"conversation_from\"><img class=\"friendProfilePic\" data-toggle=\"tooltip\" title=\"" + value.time_sent_formatted + "\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/></br><div class=\"message_container\"><p class=\"friend\">" + value.message + "</p>" + optionsMore + "</div></div>");
            } else {
              $(".pusherChatBox#" + id + " .logMsg .msgTxt").prepend('<div class="conversation_me"><img class="myProfilePic" data-toggle="tooltip" data-placement="top" title=" <?= _l('chat_sent_at') ?>' + value.time_sent_formatted + "\" src=\"" + fetchUserAvatar(value.sender_id, value.user_image) + "\"/></br><div class=\"message_container\"><p id=\"" + value.id + "\" class=\"you\">" + value.message + "</p>" + optionsMore + "</div>" + not_seen_icon + "</div><div class=\"fixinline\"></div>");
            }
          });
        }
        $("#pusherChat #" + id + " .logMsg").scrollTop($("#pusherChat #" + id + " .logMsg")[0].scrollHeight);
        $(".pusherChatBox#" + id + " p").has("audio").css("max-width", "235px");
      });

      if (!onlyLoadMessages) {
        var $cloned = $("#templateChatBox .pusherChatBox").clone().attr("id", id);
        if (fromActiveChatWindowsClick) {
          $cloned.find(".slider").css("display", "none");
          $cloned.addClass("manually-added");
          $cloned.addClass("hanging");
          obj.removeClass("active-windows-click");
        }
        $(".chatBoxslide").prepend($cloned);
      }

      $(".pusherChatBox#" + id + " .logMsg").attr("id", id);
      $(".pusherChatBox#" + id + " .logMsg").attr("onscroll", "loadMessages(this)");

      setTimeout(function () {
        // UI Experience
        activateLoader(id, false);
      }, 800);

      setTimeout(function () {
        $("[data-toggle=\"tooltip\"]").tooltip();
        if (prevBackground != getCurrentBackgound) {
          $(".pusherChatBox#" + id + " .msgTxt p.you").filter(function () {
            $(this).css("background", "" + getCurrentBackgound + "");
          });
        }
        // $("#pusherChat #" + id + " .logMsg").scrollTop($("#pusherChat #" + id + " .logMsg")[0].scrollHeight);
        // $(".pusherChatBox#" + id + " textarea").focus();
      }, 300);
    } else if (!$(".pusherChatBox#" + id).is(":visible")) {
      setTimeout(function () {

        // $(".pusherChatBox#" + id + " textarea").focus();
        // $(".pusherChatBox#" + id + " .logMsg").scrollTop($(".pusherChatBox#" + id + " .logMsg")[0].scrollHeight);
      }, 300);
      clone = $(".pusherChatBox#" + id).clone();
      $(".pusherChatBox#" + id).remove();

      if (!$(".chatBoxslide .pusherChatBox:visible:first").html()) {
        $(".chatBoxslide").prepend(clone.show());
      } else {
        $(clone.show()).insertBefore(".chatBoxslide .pusherChatBox:visible:first");
      }
    }
    var user_status_label = $("body").find("a#" + id.replace("id_", "")).attr("data-status");

    // $(".pusherChatBox#" + id + " textarea").focus();
    $(".pusherChatBox#" + id + " .from").val(presenceChannel.members.me.id);
    $(".pusherChatBox#" + id + " .to").val(obj.attr("href"));
    $(".pusherChatBox#" + id).addClass(off);
    $(".pusherChatBox#" + id).addClass("stillActive " + (user_status_label != "undefined") ? user_status_label : obj.data("status"));

    updateBoxPosition();

    return false;

  }

  $(document).keyup(function (e) {
    if (e.keyCode == 27) {
      var $prChatChatboxes = $("body").find(".closeBox");
      $.each($prChatChatboxes, function () {
        if ($(this).parents(".pusherChatBox").find(".chatbox").is(":focus")) {
          $(this).trigger("click");
        }
      });
    }
  });

  /*---------------* Delete own messages function *---------------*/
  function delete_chat_message(msg_id) {
    var selector = $("p#" + msg_id).parents(".conversation_me");
    $.post(prchatSettings.deleteMessage, {
      id: msg_id
    }).done(function (response) {
      response = JSON.parse(response)
      if (response === true) {
        selector.remove();
      }
    });
  }

  /*---------------* Open browser full view chat *---------------*/
  $("#pusherChat svg.main_chat").on("click", function () {
    var redirect_url = $(".menu-item-prchat a").attr("href");
    window.location.href = redirect_url;
    return false;
  });

  /*---------------* Redirect directly to contact from toggled chat view *---------------*/
  $("#pusherChat").on("click", "svg.user_view", function () {
    var parent_id = $(this).parents(".pusherChatBox").attr("id").replace("id_", "");
    localStorage.staff_to_redirect = parent_id;
    var redirect_url = $(".menu-item-prchat a").attr("href");
    window.location.href = redirect_url;
  });

  /*---------------* Redirect to contacts for new messages *---------------*/
  $("body").on("click", ".client_message_link", function () {
    localStorage.touchClientsTab = true;
    window.location.href = admin_url + "prchat/Prchat_Controller/chat_full_view";
  });
  /*---------------* Track window resize activity, hides chat when in mobile version *---------------*/
  $(window).resize(function () {
    if ($(window).width() < 733) {
      $("#pusherChat").hide();
    } else {
      $("#pusherChat").show();
    }
  });
  if ($(window).width() < 733) {
    $("#pusherChat").hide();
  } else {
    $("#pusherChat").show();
  }

  chat_status.bind("status-changed-event", function (user) {
    if (user.user_id !== userSessionId) {
      if (user.status == "online") {
        user.status = "";
      }
      var userPlaceholder = $("body").find("#members-list a#" + user.user_id);
      var memberHref = $("#members-list #" + user.user_id);
      userPlaceholder.removeClass().addClass("on " + user.status);

      $(".pusherChatBox#id_" + user.user_id).removeClass("away busy offline online").addClass("on " + user.status);

      if ("" == user.status) {
        user.status = "online";
      }
      var translated_status = "";
      for (var status in chat_user_statuses) {
        if (status == user.status) {
          translated_status = chat_user_statuses[status];
        }
      }
      memberHref.attr("data-status", user.status);
      memberHref.attr("data-original-title", translated_status);
      memberHref.attr("title", translated_status);
    }
  });

  /*---------------* Internet connection navigator tracker *---------------*/
  function internetConnectionCheck() {
    return navigator.onLine;
  }

  /*---------------* Live internet connection tracking *---------------*/
  function handleConnectionChange(event) {
    var conn_tracker = $(".connection_field");
    if (event.type == "offline") {
      conn_tracker.fadeIn();
      conn_tracker.children("i").addClass("blink");
      conn_tracker.css("background", "#f03d25");
      conn_tracker.children("i").fadeIn();
    }
    if (event.type == "online") {
      conn_tracker.css("background", "#04cc04");
      conn_tracker.children("i").fadeIn();
      conn_tracker.children("i").removeClass("blink");
      conn_tracker.delay(4000).fadeOut(0, function () {
        conn_tracker.children("i").fadeOut();
      });
    }
  }

  $("body").on("click", ".prchat_convertedImage", function () {
    if ($("body").find(".lity-opened")) {
      $("body").find(".lity-opened").remove();
    }
  });

  <?php if (isClientsEnabled()) : ?>
  var clientsChannel = pusher.subscribe("presence-clients");
  var clientPendingRemoves = [];

  clientsChannel.bind("send-event", function (data) {

    if (data.to == "staff_" + userSessionId) {

      let l_newChatFile = "<?= _l('chat_new_file_uploaded'); ?>";
      let l_newChatLink = "<?= _l('chat_new_link_shared'); ?>";
      let l_newAudioMsg = "<?= _l('chat_new_audio_message'); ?>";
      let l_Contact = "<?= _l('chat_lang_contact'); ?>";

      if (app.options.desktop_notifications && chat_desktop_notifications_enabled) {
        if (user_chat_status != "busy" && user_chat_status != "offline") {

          if (data.message.includes("class=\"prchat_convertedImage\"")) {
            data.message = data.contact_full_name + " " + l_newChatFile;
          }

          if (data.message.includes("audio/ogg")) {
            data.message = data.contact_full_name + " " + l_newAudioMsg;
          }

          setTimeout(() => {
            $.notify("", {
              "title": l_Contact + " " + data.contact_full_name,
              "body": unescapeHtml(data.message),
              "requireInteraction": true,
              "icon": data.client_image_path,
              "tag": "contact-message-" + data.from,
              "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : null
            }).show(function (e) {
              window.focus();
              setTimeout(function () {
                e.target.close();
              }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
            });
          }, 70);
        }
      }

      if (user_chat_status != "busy" && user_chat_status != "offline") {
        playPushSound();
      }

      if ($(".chatNewMessages").length > 0) {
        var currentCount = parseInt($(".chatNewMessages").text());
        $(".chatNewMessages").text(currentCount += 1);
      } else {
        $("#menu .menu-item-prchat").append("<a href=\"#\" class=\"client_message_link\" data-toggle=\"tooltip\" title=\"" + prchatSettings.newMessages + "\" ><i class=\"fa fa-envelope icon_new_messages\"><span class=\"chatNewMessages\">1</span></i></a>");
      }
    }
  });

  clientsChannel.bind("pusher:member_added", function (member) {
    if (member.info.justLoggedIn == true) {
      appendClientIfOnline();
    }
  });

  clientsChannel.bind("pusher:subscription_succeeded", function (member) {
    appendClientIfOnline();
  });

  function appendClientIfOnline() {
    var addMemberCount = 0;
    $(".liveClients").remove();
    for (var key in clientsChannel.members.members) {
      var isContactIdValid = clientsChannel.members.members[key].contact_id;
      if (isContactIdValid) {
        if (!key.startsWith("client")) continue;
        addMemberCount = addMemberCount + 1;
      }
    }
    $("#menu .menu-item-prchat span.menu-text").append('<span class="liveClients badge menu-badge bg-info" data-toggle="tooltip" title="<?= _l('chat_online_clients_label'); ?>">' + addMemberCount + "</span>");
  }

  clientsChannel.bind("pusher:member_removed", function (member) {
    removeClientMember(member);
  });

  function removeClientMember(member) {
    var member = member.id.replace("client_", "");
    clientPendingRemoves[member.id] = setTimeout(function () {
      var rMemberCount = 0;
      $(".liveClients").remove();
      for (var key in clientsChannel.members.members) {
        if (!key.startsWith("client")) continue;
        rMemberCount = rMemberCount + 1;
      }
      $("#menu .menu-item-prchat span.menu-text").append('<span class="liveClients badge menu-badge bg-info" data-toggle="tooltip" title="<?= _l('chat_online_clients_label'); ?>">' + rMemberCount + "</span>");
    }, 5000);
  }
  <?php endif; ?>

  user_messages_events_small.bind("message_seen", function (messages) {
    var l_seen = "<?= _l('chat_msg_seen'); ?>";
    var recieverId, senderId;
    if (Array.isArray(messages)) {
      for (var i = 0; i < messages.length; i++) {

        var seen_at = l_seen + moment("<?= date('Y-m-d H:i:s'); ?>").format("h:mm:ss A, DD MMM YYYY");

        /** Staff */
        if (messages[i].reciever_id != userSessionId) {
          recieverId = messages[i].reciever_id;
          senderId = messages[i].sender_id;

          $(".pusherChatBox#id_" + recieverId).find("i.circle-unseen").remove();
        }
      }
      ;

      if (senderId == userSessionId) {
        userSeenNotify();
      }
    }
  });

  /** Delete and Forward mesage functionality events */

  /**
   * OptionsMore hover evenets show and hide options three dots
   */
  $("body").on("mouseenter touchstart", "div.conversation_from, div.conversation_me", function () {
    $(this).find(".chooseOption").show();
  }).on("mouseleave touchend", "div.conversation_from, div.conversation_me", function () {
    $(this).find(".chooseOption, .optionsMore").hide();
  });

  /**
   * Show options for Remove or Forward
   */
  $("body").on("click", ".msgTxt .chooseOption", function () {
    $(this).next().toggle();
  });

  /**
   * Remove message click event
   */
  $("body").on("click", ".msgTxt ._removeMessage", function () {
    let parent = $(this).parents(".conversation_me");
    if (parent.length) {
      delete_chat_message(parent.find("p.you").attr("id"));
    }
  });

  /**
   * Forward message click event
   */
  $("body").on("click", ".msgTxt ._forwardMessage", function () {
    var parent = $(this).parents(".message_container");

    var messageEscaped = parent.children("p").text();
    var message = parent.children("p").html();

    $(".modal_container").load(prchatSettings.showForwardModal, function () {
      if ($(".modal-backdrop.fade").hasClass("in")) {
        $(".modal-backdrop.fade").remove();
      }
      $("#forwardToModal").modal({
        show: true
      });
      $("body").find("#forwardToModal").append(`
            <input class="_dataMessage" hidden data-message='${message}'/>
            <input class="_dataMessage escaped" hidden data-message-escaped='${messageEscaped}'/>
            `);
    });
  });

  /**
   * Delete or forward html for message
   */
  function deleteOrForward(messageId) {
    return `
            <div class="messageOptionsDiv">
            <svg height="22px" class="chooseOption" width="22px" viewBox="0 0 22 22"><circle fill="#777777" cx="11" cy="6" r="2" stroke-width="1px"></circle><circle fill="#777777" cx="11" cy="11" r="2" stroke-width="1px"></circle><circle fill="#777777" cx="11" cy="16" r="2" stroke-width="1px"></circle></svg>
            <div class="optionsMore" data-id="${messageId}">
            <span class="pointer optionBtn _removeMessage">Remove</span>
            <span class="pointer optionBtn _forwardMessage">Forward</span>
            </div>
            </div>
            `;
  }
</script>
