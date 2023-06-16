<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
  (function () {

    "use strict";

    /*---------------* Pusher Trigger accessing channel *---------------*/
    var clientsChannel = pusher.subscribe("presence-clients");
    var customersUlFirst = "";
    var hiddenUnreadMessages = $(".invisibleUnread");
    var own_image_url = $(".icon.header-user-profile a img").prop("src");
    var clientOffsetPush = 0;
    var clientEndOfScroll = false;
    var lang_customer = "<?= _l('chat_lang_customer'); ?>";
    var lang_contact = "<?= _l('chat_lang_contact'); ?>";
    var checkForNewClientUnreadMessages = prchatSettings.getClientUnreadMessages;
    var myCustomers = "";

    var f = {
      fetchClients: function () {
        var customers = JSON.stringify(<?php get_staff_customers(); ?>);
        myCustomers = JSON.parse(customers);
        return myCustomers;
      },
      chatAppendCustomers: function (customers) {
        var dfd = new $.Deferred();
        var promise = dfd.promise();

        customers = chatGroupBy(customers, "client_id");

        if (Object.keys(customers).length === 0) {
          $("form[name=clientMessagesForm] .message-input").hide();
          $(".chat_clients_list").append("<li class='text-center m-t-5 cp-5 bg-chat-primary'><h4><?= _l('chat_assigned_contacts'); ?></h4></li>");
          return false;
        }

        initCustomersAppending(customers);
        dfd.resolve(customers);

        promise.then(function () {
          customersUlFirst = $(".chat_clients_list ul:first");
          f.initAfterAppendClients();
        });
      },
      initAfterAppendClients: function () {
        // Append client contacts unread messages
        if (customersUlFirst !== "") {
          customersUlFirst.find("p.customers_toggler").click();
        }
        // Show button if there are more than 16 clients available in database
        if ($(".chat_clients_list").children().length > 16) {
          $(".chat_clients_list").after('<button type="button" onClick="fetchMoreClients(this)" class="btn btn-primary btn-block chat_load_more_clients"><?= _l('chat_load_more_clients'); ?></button');
        }

        f.checkForUnreadMessages();
      },
      updateUnreadMessages: function (id) {
        $.post(prchatSettings.updateClientUnread, {
          id: id
        });
      },
      checkForUnreadMessages: function () {
        $.post(checkForNewClientUnreadMessages).done(function (r) {
          var contacts = "";
          r = JSON.parse(r);
          if (r.result !== false) {
            $.each(r, function (i, sender) {
              var sender_id = sender.sender_id.replace("client_", "");
              var contactName = $("body").find(".contact_name#" + sender_id);

              if (contactName.length === 0 && sender.client_data !== null) {
                var contact_fullname = sender.client_data.firstname + " " + sender.client_data.lastname;

                $("<ul class=\"list-group company_selector active\" id=\"client_" + sender.client_data.client_id + "\"><p class=\"customers_toggler chevron-default\">" + sender.client_data.company + "</p><li class=\"list-group-item contact_name\" style=\"display:list-item;\" id=\"" + sender.client_data.contact_id + "\"><span class=\"client-unread-notifications\" data-badge=\"" + sender.count_messages + "\"></span>" + contact_fullname + "</li></ul>").prependTo("#crm_clients .chat_clients_list");

                clientsChannel.bind("pusher:subscription_succeeded", function (members) {
                  if (members.get(sender.sender_id)) {
                    $("li.contact_name#" + sender.client_data.contact_id).addClass("contactActive");
                  }
                });
              }

              contactName.append("<span class=\"client-unread-notifications\" data-badge=\"" + sender.count_messages + "\"></span>");
              contactName.css("display", "block");
              contactName.parent().addClass("active");
              contactName.parent().children("p.customers_toggler").chevronRemoveRightAddDefault();
              contactName.parent().prependTo(".chat_clients_list");
            });
            $(".crm_clients a").addClass("flashit");
          }
        });
      }
    };


    // Get clients and contacts list
    var customerData = f.fetchClients();
    if (customerData.customers !== "none") f.chatAppendCustomers(customerData.customers);


    // DOM Functions
    // Div clients_container functionality for order when clicking
    $("body").on("click", "p.customers_toggler", function (e) {
      e.preventDefault();
      var $this = $(this);
      var parentUl = $this.parent();
      var hasClassActive = parentUl.hasClass("active");

      if (hasClassActive) {
        parentUl.find("li").animate({
          opacity: "toggle"
        }, {
          duration: 100,
          queue: false
        });
        parentUl.removeClass("active");
        $this.removeClass("chevron-default").addClass("chevron-right");
      } else {
        parentUl.find("li").animate({
          opacity: "toggle"
        }, {
          duration: 200,
          queue: false
        });
        parentUl.addClass("active");
        $this.chevronRemoveRightAddDefault();
      }
    });

    /*---------------* Handle Clients DOM *---------------*/
    $("#frame li.crm_clients a").on("click", function () {
      $("#mainFilterDiv").hide();
      $(".actionTask").addClass("clients").show();
      var hasNotifications = $(".contact_name .client-unread-notifications");

      if (!window.matchMedia("only screen and (max-width: 735px)").matches) {
        if (customersUlFirst !== "" && customersUlFirst.find("li:first").length > 0) {
          customersUlFirst.find("li:first").click();
        }
      }

      if (customersUlFirst.length > 0 && !customersUlFirst.hasClass("active")) {
        customersUlFirst.find(".customers_toggler").click();
        hasNotifications.parent().show();
        hasNotifications.parents("ul").addClass("active");
        hasNotifications.parents("ul").children("p.chevron-right").removeClass("chevron-right").addClass("chevron-down");

        var id = customersUlFirst.children("li:first").attr("id");
        f.updateUnreadMessages(id);
      }

      $("#frame form[name=groupMessagesForm],#frame form[name=pusherMessagesForm],#frame .content .group_messages, #frame .groupOptions").hide();
      $("#frame .chat_group_options.active").hide().removeClass("active").css({
        "right": "-360px"
      });

      $("#search #search_field").attr("id", "search_clients_field");
      $("#search #search_clients_field").attr("placeholder", '<?= _l('chat_search_customers'); ?>');

      chat_contact_profile_img.hide();
      chat_contact_profile_img.next().hide();
      chat_contact_profile_img.next().next().hide();
      chat_social_media.hide();
      chat_content_messages.hide();
      chat_client_messages.show();

      $(".group_members_inline").remove();

      // Hide staff chatbox form
      $("#frame #pusherMessagesForm, #sharedFiles").hide().css({
        "right": "-360px"
      });
      $(this).removeClass("flashit");

      $("#frame .content .client_messages, #frame form[name=clientMessagesForm]").show();

    });

    /*---------------* Search customers *---------------*/
    var crmClientsParent = $("#crm_clients ul:not(.dropdown-menu)");
    var crmClientsChildren = $("#crm_clients ul:not(.dropdown-menu) li");

    $("body").on("focusout", "#search_clients_field", function () {
      $(this).val("");
      var id = "";
      setTimeout(function () {
        id = $(".client_data a").attr("id");
        crmClientsParent.show();
        var selector = $("ul.company_selector#client_" + id).addClass("active");
        selector.prependTo(".chat_clients_list");
      }, 800);
    });

    $("body").on("keyup", "#search_clients_field", _debounce(function (e) {

      var value = $.trim($(this).val().toLowerCase());
      var searchInDatabase = false;

      if (value === "") {
        $("#frame #crm_clients ul:not(.dropdown-menu)").show();
        return;
      }

      var key = e.keyCode || e.which;

      if (key === 8 || key === 46)
        crmClientsChildren.hide() &&
        crmClientsParent.show();

      if (e.keyCode === 27 || e.which == 27) {
        $("#search_clients_field").val("");
        crmClientsParent.show();
        return false;
      }

      if (value.length >= 3) {
        $("#frame  #crm_clients ul.company_selector li").each(function () {

          if ($(this).text().toLowerCase().indexOf(value) > -1) {
            $(this).show();
            $(this).children().show();
          } else {

            if ($(this).children("ul").is(":visible")) {
              $(this).show().focus();
            } else {
              $(this).hide();
            }
          }
          if (crmClientsParent.find("ul:visible:last").length === 0
            || crmClientsParent.find("ul:visible:first").length === 0) searchInDatabase = true;
        });


        if (searchInDatabase) {

          var data = {"search": $("#search_clients_field").val(),};

          $.ajax({
            type: "POST",
            url: "<?php echo site_url('prchat/Prchat_ClientsController/searchClients'); ?>",
            cache: false,
            data: data,
            success: function (response) {

              var obj = JSON.parse(response);

              if (obj.length > 0) {

                try {
                  var items = [];
                  $.each(obj, function (i, val) {

                    var isOnline = "";
                    var span_class = "contact_name_placeholder_notitle";
                    var span_title = "";

                    var client_id = val.client_id,
                      contact_id = val.contact_id,
                      company = val.company,
                      contact_fullname = val.firstname + " " + val.lastname;

                    if (val.title != "") {
                      span_class = "contact_name_placeholder";
                      span_title = "<br><span class=\"pull-left\">" + val.title + "</span>";
                    }

                    if ($("ul#client_" + client_id + "").length === 0) {
                      if (clientsChannel.members.get("client_" + contact_id)) {
                        isOnline = "contactActive";
                      }
                      items.push("<ul class=\"list-group company_selector active\" id=\"client_" + client_id + "\"><p class=\"customers_toggler chevron-down\">" + company + "</p><li class=\"list-group-item contact_name " + isOnline + "\" id=\"" + contact_id + "\" style=\"display: list-item;\"><img class=\"chatContactImage\" src=\"" + val.profile_image_url + "\"/><span class=\"" + span_class + "\">" + contact_fullname + "" + span_title + "</span></li></ul>");
                    }
                  });

                  $(".chat_clients_list").prepend.apply($(".chat_clients_list"), items);

                } catch (e) {
                  alert_float("warning", e);
                }
              }
            },
            error: function (err) {
              alert_float("danger", err);
            }
          });
          return false;
        }
      }
    }, 500));


    /*--------------------  * send message & typing event to server  * ------------------- */
    $("#frame").on("keypress", "textarea.client_chatbox", function (e) {
      var form = $("form[name=clientMessagesForm]");
      var imgPath = $("#sidepanel #profile .wrap img").prop("currentSrc");
      var sent_at = "<?= _l('chat_sent_at'); ?>";

      if (e.which == 13 && !e.shiftKey) {
        e.preventDefault();
        var message = $.trim($(this).val());

        if (message == "" || internetConnectionCheck() === false) {
          return false;
        }

        message = createTextLinks_(emojify.replace(message));

        var optionsMore = deleteOrForward();

        $(".client_messages .chat_client_messages ul").append("<li class=\"sent\" id=\"" + userSessionId + '"><i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="top" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i><img data-toggle="tooltip" title="<?= _l('chat_sent_at') . " " . _dt(date("Y-m-d H:i:s")); ?>" class="myProfilePic" src="' + imgPath + "\"/><p class=\"you\" id=\"" + userSessionId + "\">" + message + "</p>" + optionsMore + "</li>");

        $(this).next().next().next().val("false");
        message = escapeHtml(message);
        // send event
        var formData = form.serializeArray();

        $.post(prchatSettings.clientsMessagesPath, formData);
        $(this).val("").focus();
        scroll_event();
      } else if (!$(this).val() || ($(this).next().next().next().val() == "null" && $(this).val())) {
        // typing event
        $(this).next().next().next().val("true");
        $.post(prchatSettings.clientsMessagesPath, form.serialize());
      }
    });

    /*---------------* Check for unread frontend *---------------*/
    $("#frame").on("focus", ".client_chatbox", function () {
      var id = $(".client_messages").attr("id");
      if (id !== "") {
        id.replace("client_", "");
        if (hiddenUnreadMessages.val() > 0) {
          f.updateUnreadMessages(id);
          hiddenUnreadMessages.val("0");
        }
      }
    });

    // Init Crm clients messages view
    $(function () {
      $("body").on("click", ".company_selector > li", function () {
        $("body").find(".company_selector > li.selected").removeClass("selected");
        if (!$(this).parent().hasClass("active")) {
          $(this).parent().addClass("active");
        }
        $(this).addClass("selected");
      });
    });


    var createChatBoxClientRequest = null;

    function getClientMessages(staff_id, client_id, contact) {
      var not_seen_icon = "";
      var notificationDiv = contact.children(".client-unread-notifications");
      var notificationLength = contact.children(".client-unread-notifications");

      if (notificationLength.length > 0) {
        notificationDiv.remove();
      }

      $("#no_messages, .group_members_inline").remove();

      var deferred = $.Deferred();
      var promise = deferred.promise();

      if (createChatBoxClientRequest) {
        createChatBoxClientRequest.abort();
        deferred.resolve([]);
      } else {
        createChatBoxClientRequest = $.get(prchatSettings.getMutualMessages, {
          reciever_id: "staff_" + staff_id,
          sender_id: "client_" + client_id,
          offset: 0,
          limit: 20
        })
          .done(function (messages) {
            clientOffsetPush = 10;
            clientOffsetPush += 10;
            deferred.resolve(messages);

          }).always(function () {
            if ($("#no_messages").length > 0) {
              $("#no_messages").remove();
            }
            createChatBoxClientRequest = null;
          });
      }

      /*---------------* After users are fetched from database -> continue with loading *---------------*/
      promise.then(function (messages) {
        var clientUl = $(".client_messages ul");

        $(messages).each(function (i, value) {
          var previous_time = moment(messages[i].time_sent).format("YYYY-MM-DD HH");
          if (messages[i + 1]) {
            var current_time = moment(messages[i + 1].time_sent).format("YYYY-MM-DD HH");
          }

          /**
           * Check if it is and emoji
           */
          value.message = emojify.replace(value.message);

          /**
           * Check if it is audio message and decode html
           */
          value.message = isAudioMessage(value.message);

          var isViewed = value.viewed == 1;

          if (!isViewed) {
            not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
          }

          var optionsMore = deleteOrForward(userSessionId, "ticket");

          if (value.sender_id == "staff_" + staff_id) {
            clientUl.prepend("<li class=\"sent\" id=\"" + value.id + "\">" + not_seen_icon + '<img data-toggle="tooltip" data-container="body" data-placement="top" title="<?= _l('chat_sent_at') ?>' + value.time_sent_formatted + "\" class=\"myProfilePic\" src=\"" + own_image_url + "\"/><p class=\"you\" id=\"msg_" + value.id + "\" data-toggle=\"tooltip\" title=\"" + (!isViewed ? '<?= _l('chat_not_seen'); ?>' : '<?= _l('chat_msg_seen'); ?> ' + moment(value.viewed_at).format("h:mm:ss A, DD MMM YYYY")) + "\">" + value.message + "</p>" + optionsMore + "</li>");

            if (previous_time !== current_time) {
              $("<span class=\"middleDateTime\">" + moment(value.time_sent).format("llll") + "</span>").prependTo($(".client_messages ul li#" + value.id).parents("ul"));
            }

          } else {
            clientUl.prepend("<li class=\"replies\" data-sqldate=\"" + value.time_sent + "\"><img class=\"clientProfilePic\" data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"right\" data-original-title=\"" + value.time_sent_formatted + "\" src=\"" + value.client_image_path + "\"/><p  class=\"client\">" + value.message + "</p>" + optionsMore + "</li>");
          }
        });
        $(".group_members_inline").remove();
      });

      activateLoader(promise, true);

      $.when(promise)
        .then(function () {
          ($(".client_messages").hasScrollBar());
          scroll_event();
          if ($(window).width() > 760) {
            $(".client_chatbox").focus();
          }
        });
      return false;
    }


    /*---------------* Check for messages history and append to main chat window *---------------*/
    $(".client_messages").on("scroll", function () {

      var pos = $(this).scrollTop();
      var client_id = $(this).attr("id");
      var to = $("#clients_container li.contact_name.selected").attr("id");
      var not_seen_icon = "";

      var defScroll = $.Deferred();
      var promise = defScroll.promise();

      if (pos == 0 && clientOffsetPush >= 10) {

        $.get(prchatSettings.getMutualMessages, {
          reciever_id: client_id,
          sender_id: "staff_" + userSessionId,
          offset: clientOffsetPush,
        })
          .done(function (messages) {
            if (Array.isArray(messages) === false) {
              clientEndOfScroll = true;
              $("#frame .client_messages").find(".message_loader").hide();
              if ($(".client_messages").hasScrollBar() && clientEndOfScroll == true) {
                prchat_setNoMoreMessages();
              }
            } else {
              clientOffsetPush += 10;
              defScroll.resolve(messages);
            }
            $(messages).each(function (i, value) {

              var previous_time = moment(messages[i].time_sent).format("YYYY-MM-DD HH");
              if (messages[i + 1] !== undefined) {
                var current_time = moment(messages[i + 1].time_sent).format("YYYY-MM-DD HH");
              }

              var clientUl = $(".client_messages ul");
              var optionsMore = deleteOrForward(userSessionId);
              value.message = emojify.replace(value.message);
              value.message = isAudioMessage(value.message);

              var isViewed = value.viewed == 1;

              if (!isViewed) {
                not_seen_icon = '<i class="fa-solid fa-circle-check circle-unseen" data-toggle="tooltip" data-container="body" data-placement="left" title="<?= _l('chat_msg_delivered'); ?>" aria-hidden="true"></i>';
              }

              if (value.sender_id == "staff_" + userSessionId) {
                clientUl.prepend("<li class=\"sent\" id=\"" + value.id + "\">" + not_seen_icon + "<img data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"left\" data-original-title=\"" + value.time_sent_formatted + "\" class=\"myProfilePic\" src=\"" + own_image_url + "\"/><p class=\"you\" id=\"msg_" + value.id + "\" data-toggle=\"tooltip\" title=\"" + (!isViewed ? '<?= _l('chat_not_seen'); ?>' : '<?= _l('chat_msg_seen'); ?> ' + moment(value.viewed_at).format("h:mm:ss A, DD MMM YYYY")) + "\">" + value.message + "</p>" + optionsMore + "</li>");

                if (previous_time !== current_time) {
                  $("<span class=\"middleDateTime\">" + moment(value.time_sent).format("llll") + "</span>").prependTo($(".client_messages ul li#" + value.id).parents("ul"));
                }
              } else {
                clientUl.prepend("<li class=\"replies\" data-sqldate=\"" + value.time_sent + "\"><img class=\"clientProfilePic\" data-toggle=\"tooltip\" data-container=\"body\" data-placement=\"right\" data-original-title=\"" + value.time_sent_formatted + "\" src=\"" + value.client_image_path + "\"/><p  class=\"client\">" + value.message + "</p>" + optionsMore + "</li>");
              }

            });
            if (clientEndOfScroll === false) {
              $(".client_messages").scrollTop(200);
            }
          });
        if (!clientEndOfScroll === true) {
          activateLoader(promise, true);
        }
      }
    });

    // File upload
    $("#frame").on("click", ".clientFileUpload", function () {
      $("#frame").find("form[name=\"clientFileForm\"] input:first").click();
    });

    // Show All Contacts
    $("#frame").on("click", "#clients_show", function () {
      // Used and duplicated due to evenent delegation
      var $selector = $("body").find(".chat_clients_list ul.company_selector").children("p");
      if ($selector.hasClass("chevron-right")) {
        $selector.addClass("chevron-default").removeClass("chevron-right");
        $(this).children("i").removeClass("fa-align-center").addClass("fa-align-justify");
      } else {
        $selector.removeClass("chevron-default").addClass("chevron-right");
        $(this).children("i").removeClass("fa-align-justify").addClass("fa-align-center");
      }
      $("body").find(".chat_clients_list ul.company_selector li").toggle("fast");
    });

    /*---------------* Event that handles when clicked on a specific contact in sidebar *---------------*/
    $("#clients_container").on("click", ".contact_name", function (e) {
      e.preventDefault();
      animateContent();
      if ($(this).children(".client-unread-notifications").length > 0)
        f.updateUnreadMessages($(this).attr("id"));

      // Clear messages
      chat_client_messages.html("");
      chat_client_messages.append("<ul></ul>");

      // Reset offset and end of scroll
      clientOffsetPush = 0;
      clientEndOfScroll = false;


      var contactId = $(this).attr("id"),
        clientName = $(this).find("span.contact_name_placeholder").html() || $(this).text(),
        customerName = $(this).parent().find(".customers_toggler").text(),
        contactProfile = $("#frame .contact-profile"),
        clientId = $(this).parent().attr("id").replace("client_", ""),
        clientData = "";

      $(".client_data").remove();
      $("body").find(".client_messages").attr("id", "client_" + $(this).attr("id"));
      $("body").find(".client_messages").addClass("isFocused");

      $("body").find("form#clientMessagesForm .to").val("client_" + $(this).attr("id"));
      $("body").find("form#clientMessagesForm .from").val("staff_" + userSessionId);

      getClientMessages(userSessionId, contactId, $(this));

      var extraClass = $(this).find("span.contact_name_placeholder").length ? "" : "notitle";

      var text_class = ($("body").hasClass("chat_light")) ? "dark-text" : "white-text";

      clientData += "<div class=\"client_data\">";
      clientData += "<span class=\"contact_lang " + extraClass + "\"> " + lang_contact + " <a href=\"" + site_url + "admin/clients/client/" + clientId + "?group=contacts&contactid=" + contactId + "\" target=\"_blank\"\"><strong>" + clientName + " </strong></a></span>";
      clientData += "<p> <strong class=\"" + text_class + "\">" + lang_customer + "</strong> <a id=\"" + clientId + "\" href=\"" + site_url + "admin/clients/client/" + clientId + "\" target=\"_blank\"><strong>" + customerName + " </strong></a></p>";
      clientData += "</div>";
      contactProfile.prepend(clientData);
    });

    /*---------------* Member array for online / offline activity *---------------*/
    var pendingRemoves = [];

    /*---------------* Pusher Trigger user subscribed successfully *---------------*/
    clientsChannel.bind("pusher:subscription_succeeded", function (member) {
      checkIfContactIsActive(member);
      $("#main_loader_init").fadeOut(500);
      ifContactOnlineAddToClientsList(member);
      appendClientIfNotInOnlineClients();
    });

    function appendClientIfNotInOnlineClients() {
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

    /*---------------* Pusher Trigger user logout *---------------*/
    clientsChannel.bind("pusher:member_removed", function (member) {
      removeClientMember(member);
    });


    /*---------------* Pusher Trigger user connected *---------------*/
    clientsChannel.bind("pusher:member_added", function (member) {
      /**
       * Check if clients is in my own list clients
       */
      var ownClient = myCustomers.customers.find(function (client) {
        if (client.client_id !== undefined) {
          return client.client_id == member.info.client_id;
        }
      });

      if (ownClient == undefined) {
        return false;
      }

      var contact = member.info;

      var contactIsLoggedIn = $("ul.company_selector#client_" + contact.client_id);

      if (member.info.justLoggedIn == true) {
        appendClientIfNotInOnlineClients();
      }

      if (typeof contact.company !== "undefined" && contact.contact_id !== "undefined") {
        if (contactIsLoggedIn.length === 0) {
          $("<ul class=\"list-group company_selector active\" id=\"client_" + contact.client_id + "\"><p class=\"customers_toggler chevron-down\">" + contact.company.name + "</p><li class=\"list-group-item contact_name contactActive\" id=\"" + contact.contact_id + "\" style=\"display: list-item;\">" + contact.name + "</li></ul>").prependTo(".chat_clients_list");
        }
        addClientMember(member);
      }
    });

    /*---------------* New staff member activity online / offline  *---------------*/
    function ifContactOnlineAddToClientsList() {
      var c = f.fetchClients();
      for (var i = 0; i < c.customers.length; i++) {
        var contact = clientsChannel.members.get("client_" + c.customers[i].contact_id);
        var cid = c.customers[i].client_id;
        if (contact !== null) {
          var bodyCid = $("body").find(".company_selector#client_" + cid);
          if (!$(bodyCid).hasClass("active")) {
            bodyCid.addClass("active");
            bodyCid.find("p").chevronRemoveRightAddDefault();
            bodyCid.children("li").css("display", "list-item");
          }
          $("body").find(".company_selector#client_" + cid + " .contact_name#" + c.customers[i].contact_id).addClass("contactActive");
        }
      }
      ;
    }

    /*---------------* New chat members tracking / removing *---------------*/
    function addClientMember(member) {
      var member = member.id.replace("client_", "");
      var pendingRemoveTimeout = pendingRemoves[member.id];

      var activeContact = $("body").find(".company_selector .contact_name#" + member).addClass("contactActive");

      activeContact.parent().addClass("active");
      activeContact.parent().children("p").chevronRemoveRightAddDefault();
      activeContact.parent().children("li").css("display", "list-item");

      var c = f.fetchClients();
      for (var i = 0; i < c.customers.length; i++) {
        var contact = clientsChannel.members.get("client_" + c.customers[i].contact_id);
        if (contact !== null) {
          if (contact.info.justLoggedIn && "client_" + member == contact.id) {
            pushDesktopOnlineNotification(contact);
          }
        }
      }
      ;
      if (pendingRemoveTimeout) {
        clearTimeout(pendingRemoveTimeout);
      }
    }

    /*---------------* New chat members tracking / removing from channel and view *---------------*/
    function removeClientMember(member) {
      var member = member.id.replace("client_", "");

      pendingRemoves[member.id] = setTimeout(function () {
        var rMemberCount = 0;
        $(".liveClients").remove();
        for (var key in clientsChannel.members.members) {
          if (!key.startsWith("client")) continue;
          rMemberCount = rMemberCount + 1;
        }
        $("#menu .menu-item-prchat span.menu-text").append('<span class="liveClients badge menu-badge bg-info" data-toggle="tooltip" title="<?= _l('chat_online_clients_label'); ?>">' + rMemberCount + "</span>");
        if ($("body").find(".company_selector .contact_name#" + member).length) {
          $("body").find(".company_selector .contact_name#" + member).removeClass("contactActive");
        }
      }, 5000);
    }

    /*---------------* Bind the 'send-event' & update the chat box message log *---------------*/
    var invisibleCounter = 1;

    clientsChannel.bind("send-event", function (data) {
      $("#frame .client_messages").find("span.userIsTyping").fadeOut(500);
      var clientMessages = $(".chat_client_messages");
      var selectedContact = $(".chat_clients_list ul.active li.selected");

      selectedContact = "client_" + selectedContact.attr("id");

      var optionsMore = deleteOrForward(userSessionId, "ticket");

      if (data.last_insert_id) {
        clientMessages.find("li.sent .you#" + userSessionId).attr("id", "msg_" + data.last_insert_id);
        clientMessages.find("li.sent#" + userSessionId).find(".optionsMore").attr("data-id", data.last_insert_id);
        clientMessages.find("li.sent#" + userSessionId).attr("id", data.last_insert_id);
      }

      if (selectedContact == data.from && data.to == "staff_" + userSessionId) {

        data.message = createTextLinks_(emojify.replace(data.message));

        $(".client_messages#" + data.from + " .chat_client_messages ul")
          .append("<li class=\"replies\" data-sqldate=\"" + moment().format("YYYY-MM-DD HH:mm:ss") + "\"><img class=\"clientProfilePic\" data-toggle=\"tooltip\" data-original-title=\"" + moment().format("hh:mm A") + "\" src=\"" + data.client_image_path + "\"/><p class=\"client\">" + data.message + "</p>" + optionsMore + "</li>");

        parseInt(hiddenUnreadMessages.val(invisibleCounter++));
        scroll_event();

      } else if (data.to == "staff_" + userSessionId) {
        data.from = data.from.replace("client_", "");
        var contactNotification = $("body").find(".contact_name#" + data.from);
        var contactNotificationParent = $("body").find(".contact_name#" + data.from).parent();
        var contactNotificationClass = ".client-unread-notifications";

        $(".crm_clients:not(.active) a").addClass("flashit");

        var topLi = $(".chat_clients_list ul li").first();

        var isClientLoaded = $(".chat_clients_list ul#client_" + data.client_id);

        if (isClientLoaded.length === 0) {
          $("<ul class=\"list-group company_selector active\" id=\"client_" + data.client_id + "\"><p class=\"customers_toggler chevron-default\">" + data.company + "</p><li class=\"list-group-item contact_name contactActive\" style=\"display:list-item;\" id=\"" + data.from + "\"><span class=\"client-unread-notifications\" data-badge=\"1\"></span>" + data.contact_full_name + "</li></ul>").prependTo("#crm_clients .chat_clients_list");
        }

        if (topLi.attr("id") !== data.from) {
          contactNotificationParent.prependTo(".chat_clients_list");
        }

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

            $.notify("", {
              "title": l_Contact + " " + data.contact_full_name,
              "body": unescapeHtml(data.message),
              "requireInteraction": true,
              "tag": "contact-message-client_" + data.from,
              "icon": data.client_image_path,
              "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : null
            }).show(function (e) {
              window.focus();
              setTimeout(function () {
                e.target.close();
              }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
            });
          }
        }

        if (user_chat_status != "busy" && user_chat_status != "offline") {
          initClientSound(data);
        }

        if (!contactNotification.find(contactNotificationClass).length) {
          contactNotification.append("<span class=\"client-unread-notifications\" data-badge=\"1\"></span>");
          contactNotification.parent("ul").addClass("active");
          contactNotification.show();

        } else {
          var currentNotifications = parseInt(contactNotification.find(contactNotificationClass).attr("data-badge"));
          contactNotification.find(contactNotificationClass).attr("data-badge", currentNotifications + 1);
        }
      }
    });
    /*---------------* Detect when a user is typing a message *---------------*/
    clientsChannel.bind("typing-event", function (data) {
      var clearTypingInterval = 2500; // 2.2 seconds
      var clearTypingTimerId;
      var clientMessages = $("#frame .client_messages");
      var selectedContact = $(".chat_clients_list ul.active li.selected");
      selectedContact = "client_" + selectedContact.attr("id");

      if (clientMessages.hasClass("isFocused") &&
        data.from === selectedContact &&
        data.to == "staff_" + userSessionId &&
        data.message == "true") {
        clientMessages.find(".userIsTyping").fadeIn(500);
        clearTimeout(clearTypingTimerId);
        clearTypingTimerId = setTimeout(function () {
          clientMessages.find(".userIsTyping").fadeOut(500);
        }, clearTypingInterval);
        clientMessages.find("span.userIsTyping").fadeIn(500);
      } else if (
        clientMessages.hasClass("isFocused") &&
        data.from === selectedContact &&
        data.to == "staff_" + userSessionId &&
        data.message == "true") {
        clientMessages.find("span.userIsTyping").fadeOut(500);
      }
    });

    $("body").on("click", ".prchat_convertedImage", function () {
      if ($("body").find(".lity-opened")) {
        $("body").find(".lity-opened").remove();
      }
    });

    function checkIfContactIsActive(member) {
      member.each(function (m) {

        if (m.id.startsWith("client")) {
          return false;
        }
        var contact = m.info;
        var contactIsLoggedIn = $("body").find("ul.company_selector#client_" + contact.client_id);
        // If contact has been deleted also !== null
        // Thanks to ndkdesign bug reported and fixed
        if (typeof contact.company !== "undefined" && contact.company != null) {
          if (contactIsLoggedIn.length === 0) {
            $("<ul class=\"list-group company_selector active\" id=\"client_" + contact.client_id + "\"><p class=\"customers_toggler chevron-down\">" + contact.company.name + "</p><li class=\"list-group-item contact_name contactActive\" id=\"" + contact.contact_id + "\" style=\"display: list-item;\">" + contact.name + "</li></ul>").prependTo(".chat_clients_list");
          }
        }
      });
    }

    function pushDesktopOnlineNotification(contact) {
      return $.notify("", {
        "title": app.lang.new_notification,
        "body": lang_contact + " " + contact.info.name + " " + prchatSettings.hasComeOnlineText,
        "requireInteraction": true,
        "tag": "contact-join-" + contact.id,
        "closeTime": app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : null
      }).show(function (e) {
        window.focus();
        setTimeout(function () {
          e.target.close();
        }, app.options.dismiss_desktop_not_after != "0" ? app.options.dismiss_desktop_not_after * 1000 : 3000);
      });
    }

    var onlineToggled = false;
    var mainClientsContainer = $(".chat_clients_list");

    $("body").on("click touchstart", "#resetContacts", function (e) {
      e.stopPropagation();
      $.each(mainClientsContainer.children("ul"), function (i, el) {
        el = $(el);
        if (el.is(":hidden")) {
          el.show();
        }
      });
      if (onlineToggled === false) {
        alert_float("warning", "You are already seeing all clients");
      }
      onlineToggled = false;

    });
    $("body").on("click touchstart", "#showOnlineContacts", function (e) {
      e.stopPropagation();
      if (!onlineToggled) {
        onlineToggled = true;
        $.each(mainClientsContainer.children("ul"), function (i, el) {
          el = $(el);
          if (!el.children("li").hasClass("contactActive")) {
            el.hide();
            if (el.length) {
              el.appendTo(".chat_clients_list");
            }
          } else {
            el.attr("data-toggle", "tooltip");
            el.attr("title", "<?= _l('chat_clients_click_to_see_info'); ?>");
            el.children("li:not(.contactActive)").hide();
          }
        });

        clientsChannel.members.each(function (m) {
          var contact = m.info;
          var contactIsLoggedIn = $("body").find("ul.company_selector#client_" + contact.client_id);

          if (typeof contact.company !== "undefined") {
            if (contactIsLoggedIn.length === 0) {
              $("<ul class=\"list-group company_selector active\" id=\"client_" + contact.client_id + "\"><p class=\"customers_toggler chevron-down\">" + contact.company.name + "</p><li class=\"list-group-item contact_name contactActive\" id=\"" + contact.contact_id + "\" style=\"display: list-item;\">" + contact.name + "</li></ul>").prependTo(".chat_clients_list");
            }
          }
        });
      }
    });
  }());

  function initCustomersAppending(customers) {
    for (var index in customers) {

      var client_id = client_id = customers[index][0].client_id,
        company = customers[index][0].company,
        companyExists = $("ul#client_" + client_id);

      if (!companyExists.length)
        $("#crm_clients .chat_clients_list")
          .append("<ul class=\"list-group company_selector\" id=\"client_" + client_id + "\"><p class=\"customers_toggler chevron-right\">" + company + "</p></ul>");

      customers[index].forEach(function (contact) {
        var firstname = contact.firstname,
          lastname = contact.lastname,
          contact_id = contact.contact_id,
          image_path = contact.profile_image_url;

        if (contact.title !== "") {
          $("#crm_clients .chat_clients_list ul#client_" + client_id)
            .append("<li class=\"list-group-item contact_name\" data-name=\"" + firstname + " " + lastname + "\" id=\"" + contact_id + "\"><img class=\"chatContactImage\" src=\"" + image_path + "\"/><span class=\"contact_name_placeholder\">" + firstname + " " + lastname + '<br><span><span class="pull-left"><?= ucfirst(_l('title')); ?>: ' + contact.title + "</span></li>");
        } else {
          $("#crm_clients .chat_clients_list ul#client_" + client_id)
            .append("<li class=\"list-group-item contact_name\" data-name=\"" + firstname + " " + lastname + "\" id=\"" + contact_id + "\"><img class=\"chatContactImage\" src=\"" + image_path + "\"/><span class=\"contact_name_placeholder_notitle\">" + firstname + " " + lastname + "</span></li>");
        }
      });
    }
  }

  // Handles client file form upload
  function uploadClientFileForm(form) {
    var formData = new FormData();
    var fileForm = $(form).children("input[type=file]")[0].files[0];
    var sentTo = $(".company_selector.active").attr("id");
    var token_name = $(form).children("input[name=csrf_token_name]").val();
    var formId = $(form).attr("id");

    formData.append("userfile", fileForm);
    formData.append("send_to", sentTo);
    formData.append("send_from", "staff_" + userSessionId);
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
            alert_float("warning", '<?php echo _l('chat_permitted_files') ?>');
            $(".content .chat-module-loader").remove();
            return false;
          }
        } else {
          $(".content .chat-module-loader").remove();
          return false;
        }
      },
      success: function (r) {
        if (!r.error) {
          var uploadSend = $.Event("keypress", {
            which: 13
          });
          var basePath = "<?php echo base_url('modules/prchat/uploads/'); ?>";
          $("#frame textarea.client_chatbox").val(basePath + r.upload_data.file_name);
          setTimeout(function () {
            if ($("#frame textarea.client_chatbox ").trigger(uploadSend)) {
              alert_float("info", "File " + r.upload_data.file_name + " sent.");
              $(".content .chat-module-loader").fadeOut();
            }
          }, 100);
        } else {
          $(".content .chat-module-loader").fadeOut();
          alert_float("danger", r.error);
        }
      }
    });
    $("form#" + formId).trigger("reset");
  }

  /*!
   * Group items from an array together by some criteria or value.
   * @param  {Array}           arr      The array to group items from
   * @param  {String|Function} criteria The criteria to group by
   * @return {Object}                   The grouped object
   */

  function chatGroupBy(arr, criteria) {
    return arr.reduce(function (obj, item) {
      // Check if the criteria is a function to run on the item or a property of it
      var key = typeof criteria === "function" ? criteria(item) : item[criteria];

      // If the key doesn't exist yet, create it
      if (!obj.hasOwnProperty(key)) {
        obj[key] = [];
      }

      // Push the value to the object
      obj[key].push(item);

      // Return the object to the next item in the loop
      return obj;

    }, {});
  };

  var clientsOffset = 0;

  function fetchMoreClients(el) {
    clientsOffset += 50;
    $(el).html('<div class="fa-1x chat_loading_more_clients"><?= _l('chat_load_more_clients'); ?><i class="fa fa-spinner fa-pulse"></i><i class="fa fa-stroopwafel fa-spin"></i></div>');
    $.getJSON("<?php echo site_url('prchat/Prchat_ClientsController/loadMoreClients'); ?>", {
      offset: clientsOffset
    })
      .done(function (clients /** json format **/) {
        customers = chatGroupBy(clients.customers, "client_id");

        if (Object.keys(customers).length === 0) {
          var content = "<li class='text-center m-t-5 cp-5 no_more_clients bg-chat-primary'><h4><?= _l('chat_no_more_contacts_found'); ?></h4></li>";
          $(el).html("<?= _l('chat_load_more_clients'); ?>");
          if ($(".chat_clients_list li.no_more_clients").length) {
            $(".chat_clients_list li.no_more_clients").html(content);
            if ($("body").hasClass("chat_dark")) {
              $(".chat_load_more_clients").hide();
            }
          } else {
            $(".chat_clients_list").append(content);
            $("#clients_container").scrollTop($("#clients_container")[0].scrollHeight);
          }
          return false;
        }

        initCustomersAppending(customers);

        $(el).html("<?= _l('chat_load_more_clients'); ?>");
      });
  }

  $("body").on("click", ".chat_client_messages .btn_convert_conversation_to_ticket", function (e) {
    var msgTxt = $(this).parents("li").find("p").text();

    var msgId, user_id;
    var id = $(".tab-pane.fade.active.in .company_selector.active .contact_name.selected").attr("id");
    var current_message = $(this).prev().text();

    $(".modal_container").load(prchatSettings.convertToTicket, {
        id: "client_" + id,
      },
      function (res) {

        var poppedUp = $("#convert_to_ticket_modal").modal({
          show: true
        });

        if (poppedUp.length) {
          setTimeout(function () {
            $("#convert_to_ticket_modal label").filter(function () {
              if ($(this).text() === msgTxt) {
                msgId = $(this).prev().attr("id").replace("message_", "");
                ;
                return $(this).prev().prop("checked", true);
              }
            }).focus();

            user_id = "client_" + id;

            var client_id = $(".client_messages").attr("id");
            var ticket_message = msgTxt;
            var user_name = $("#converToTicketModal .mbot20").children("strong").text();

            filteredTicketMessages.push({
              "user_id": user_id,
              "message_id": msgId,
              "message": ticket_message,
              "client_id": client_id,
              "user_name": user_name
            });

          }, 500);
        }
      });
  });


  jQuery.fn.extend({
    chevronRemoveRightAddDefault: function () {
      return $(this).removeClass("chevron-right").addClass("chevron-default");
    }
  });

  // Create ticket modal
  <?php if (get_option('chat_allow_staff_to_create_tickets') == 1 || is_admin() && is_staff_member()) :  ?>

  $("body").on("mouseover", ".chat_client_messages .replies", function () {
    var message_date = $(this).data("sqldate");

    var newFormat = "YYYY-MM-DD HH:mm:ss";
    var today = moment().format(newFormat);

    var hours = moment(today, newFormat).diff(moment(message_date, newFormat), "hours");

    // Only allow to convert messages send in last 48 hours
    if (hours >= 48) {
      return;
    }

    $(this).find(".btn_convert_conversation_to_ticket").show();
  }).mouseout(function () {
    $(this).find(".btn_convert_conversation_to_ticket").hide();
  });
  <?php endif; ?>

  /**
   * Remove message click event
   */
  $("body").on("click", ".chat_client_messages ._removeMessage", function () {
    $(this).attr("disabled", true);
    delete_chat_client_message($(this).parents("li.sent").attr("id"));
  });

  /*---------------* Delete clients messages *---------------*/
  function delete_chat_client_message(msg_id) {
    var selector = $(".chat_client_messages").find("li.sent#" + msg_id);

    $.post(prchatSettings.deleteClientMessage, {
      message_id: msg_id
    }).done(function (response) {
      if (response) {
        selector.remove();

        let lastChildren = $("body").find(".chat_client_messages ul").children().last();

        if (lastChildren.hasClass("middleDateTime")) {
          lastChildren.remove();
        }

        getSharedFiles(userSessionId, msg_id);
      } else {
        selector.remove();
        alert_float("danger", "<?php echo _l('chat_error_float'); ?>");
      }
    });
  }
</script>

<?php
if (get_option('chat_allow_staff_to_create_tickets') == 1 || is_admin() && is_staff_member()) {
  // Include conver to ticket modal javascript code
  require('modules/prchat/assets/module_includes/modal_additional_files_includes/convert_to_ticket_inc.php');
} ?>
