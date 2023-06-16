var offsetPush = {};
var endOfScroll = {};

function removeActiveChatWindow(id)
{
    var activeChatWindows = getActiveChatWindowsFromStorage();
    var indexToRemove = null;

    $.each(activeChatWindows, function (index, obj) {
        if (obj.id == id) {
            indexToRemove = index;
        }
    });
    if (indexToRemove !== null) {
        activeChatWindows.splice(indexToRemove, 1);
    }

    localStorage.activeChatWindows = JSON.stringify(activeChatWindows);
}

function addActiveChatWindow(obj)
{

    if (typeof (localStorage.activeChatWindows) == "undefined") {
        localStorage.activeChatWindows = "";
    }

    if (isChatBoxInLocalStorageActiveChats(obj.id)) {
        return false;
    }

    var currentActiveChatWindows = getActiveChatWindowsFromStorage();

    currentActiveChatWindows.push(obj);

    localStorage.activeChatWindows = JSON.stringify(currentActiveChatWindows);
}

function getActiveChatWindowsFromStorage()
{
    if (typeof (localStorage.activeChatWindows) == "undefined") {
        return [];
    }

    var activeChatWindows = localStorage.activeChatWindows;

    if (activeChatWindows == "") {
        return [];
    }

    return JSON.parse(activeChatWindows);
}

function isChatBoxInLocalStorageActiveChats(id)
{

    var retVal = false;
    $.each(getActiveChatWindowsFromStorage(), function (index, obj) {
        if (obj.id == id) {
            retVal = true;
        }
    });

    return retVal;
}

function createTextLinks_(text)
{
    var regex = (/\.(gif|jpg|jpeg|tiff|png|swf)$/i);
    return (text || "").replace(/([^\S]|^)(((https?\:\/\/)|(www\.))(\S+))/gi, function (match, string, url) {
        var hyperlink = url;
        if (!hyperlink.match("^https?:\/\/")) {
            hyperlink = "//" + hyperlink;
        }
        if (hyperlink.match("^http?:\/\/")) {
            hyperlink = hyperlink.replace("http://", "//");
        }
        if (hyperlink.match(regex)) {
            return string + "<a href=\"" + hyperlink + "\" target=\"blank\" data-lity><img style=\"width:100%;height:100%;padding-top:2px;\" rel=\"nofollow\" src=\"" + hyperlink + "\"/></a>";
        } else {
            return string + "<a data-lity target=\"blank\" rel=\"nofollow\" href=\"" + hyperlink + "\">" + url + "</a>";
        }
    });
}

function clearSearchValues()
{
    $(".searchBox").slideUp(200, function () {
        $(".searchBox").addClass("inputHidden");
        $(".searchBox").val("");
        $("#members-list a").filter(function () {
            $(this).css("display", "block");
        });
    });
}

function changeColor(obj)
{
    var url = $(obj).attr("action");
    var color = $(obj).find("input[name=color]").val();
    getCurrentBackgound = color;
    $.post(url, {
        color: color
    }).done(function (r) {
        r = JSON.parse(r);
        if (r.success === "unknownColor") {
            alert_float("warning", prchatSettings.invalidColor);
            return false;
        }

        if (r.success !== false) {
            var newColor = r.success;
            if ((newColor.indexOf("linear-gradient(") > -1) && (newColor.indexOf(");") > -1)) {
                location.reload();
                return false;
            } else {
                color = color;
            }
            $("#pusherChat #membersContent .topInfo").css("background", color);
            $("#pusherChat chatHead").css("background", color);
            $("#pusherChat .chat-footer").css("background", color);
            $("#pusherChat .pusherChatBox .msgTxt p.you").css("background", color);
            changeHoverColor(color);
            return false;
        }

    });
}

function resetChatColors()
{
    if (confirm(prchatSettings.areYouSure)) {
        $.post(prchatSettings.resetChatColors).done(function (r) {
            if (r == "true") {
                location.reload();
            }
        });
    }
}

function changeHoverColor(color)
{
    $("#members-list a, .dropup li a").filter(function () {
        $(this).hover(
            function () {
                $(this).css("background", color);
            },
            function () {
                $(this).css("background", "");
            });
    });
}

/*---------------* function that handles updating unread messages *---------------*/
function updateUnreadMessages(currentElement, pusherChatBox)
{
    if (pusherChatBox) {
        var linkId = pusherChatBox.attr("id").replace("id_", "");
        pusherChatBox.find(".notification-count").text("0");
        pusherChatBox.find(".notification-box").hide();
        $("#membersContent a#" + linkId).find(".unread-notifications").remove();
        $("#membersContent a#" + linkId).removeClass("animated flash");
        updateLatestMessages(linkId);
        return false;
    }
    if (currentElement) {
        var id = $(currentElement).parents(".pusherChatBox").attr("id").replace("id_", "");
        if (id) {
            var notiVal = $(currentElement).parents(".pusherChatBox").find(".notification-count").text();
            if (notiVal > 0) {
                updateLatestMessages(id);
                $("#membersContent a#" + id).removeClass("animated flash");
                $(".pusherChatBox#id_" + id).find(".notification-count").text("0");
            }
        }
    }
}

/*---------------*  Function removeChatMember and addChatMember must remain untouched and not moved to another place ! *---------------*/
var pendingRemoves = [];

function addChatMember(members)
{
    var pendingRemoveTimeout = pendingRemoves[members.id];
    $("a#" + members.id).addClass("on").removeClass("off");
    $(".pusherChatBox#id_" + members.id).addClass("on").removeClass("off");

    if (!$("a#" + members.id).hasClass(members.info.status)) {
        $("a#" + members.id).addClass(members.info.status);
    }
    if (!$(".pusherChatBox#id_" + members.id).hasClass(members.info.status)) {
        $(".pusherChatBox#id_" + members.id).addClass(members.info.status);
    }
    if (pendingRemoveTimeout) {
        clearTimeout(pendingRemoveTimeout);
    }
}

function removeChatMember(members)
{
    pendingRemoves[members.id] = setTimeout(function () {
        if (presenceChannel.members.count >= 1) {
            $("#count").html(presenceChannel.members.count - 1);
        }
        if ($(".liveUsers").length) {
            $(".liveUsers").html(presenceChannel.members.count - 1);
        } else {
            $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (presenceChannel.members.count - 1) + "</span>");
        }

        $("a#" + members.id).removeClass("on " + members.info.status).addClass("off");
        $(".pusherChatBox#id_" + members.id).addClass("off").removeClass("on stillActive " + members.info.status);
        chatMemberUpdate();
    }, 5000);
}


/*-----------------------------* reorganize the chat box position on adding or removing users * -----------------------------*/
function updateBoxPosition()
{
    var right = 0;
    var slideLeft = false;
    $(".chatBoxslide .pusherChatBox:visible").each(function () {
        $(this).css({
            "right": right,
        });
        right += $(this).width() + 20;
        $(".chatBoxslide").css({
            "width": right
        });
        if ($(this).offset().left - 20 < 0) {
            $(this).addClass("overFlow");
            slideLeft = true;
        } else {
            $(this).removeClass("overFlow");
        }
    });
    if (slideLeft) {
        $("#slideLeft").show();
    } else {
        $("#slideLeft").hide();
    }
    if ($(".overFlowHide").html()) {
        $("#slideRight").show();
    } else {
        $("#slideRight").hide();
    }
}

function activateLoader(id, prepending)
{
    var initLoader = $(".pusherChatBox .logMsg#" + id);
    initLoader.find(".message_loader").show(function () {
        initLoader.find(".message_loader").hide();
        if (prepending == true) {
            initiatePrepending();
        }
    });
}


/*---------------* chatMemberUpdate() place & update users on user page, unred messages notifications *---------------*/
function chatMemberUpdate(subscribed_event)
{
    var insertId = "";
    var notification = "";

    $.get(prchatSettings.usersList, function (data) {
        var offlineUser = "";
        var onlineUser = "";
        data = JSON.parse(data);
        $.each(data, function (user_id, value) {
            if (value.staffid != presenceChannel.members.me.id) {
                user = presenceChannel.members.get(value.staffid);

                if (value.status != undefined && value.status.length != undefined && value.status == "online") {
                    value.status = "";
                }
                var user_status = ("" == value.status) ? "online" : value.status;
                var translated_status = "";
                for (var status in chat_user_statuses) {
                    if (status == user_status) {
                        translated_status = chat_user_statuses[status];
                    }
                }
                if (user != null) {
                    onlineUser += "<a data-status=\"" + value.status + "\" data-toggle=\"tooltip\" title=\"" + translated_status + "\" href=\"#" + value.staffid + "\" id=\"" + value.staffid + "\" class=\"on " + value.status + "\"><span class=\"user-name onlineUsername\">" + strCapitalize(value.firstname + " " + value.lastname) + "</span><img src=\"" + fetchUserAvatar(value.staffid, value.profile_image) + "\" class=\"imgFriend\" /></a>";
                    if (presenceChannel.members.count > 0) {
                        $("#count").html(presenceChannel.members.count - 1);
                        $(".liveUsers").remove();
                        $("#menu .menu-item-prchat span.menu-text").append("<span class=\"liveUsers badge menu-badge bg-info\" data-toggle=\"tooltip\" title=\"" + prchatSettings.onlineUsersMenu + "\">" + (" " + presenceChannel.members.count - 1) + "</span>");
                    }
                } else {
                    offlineUser += "<a href=\"#" + value.staffid + "\" id=\"" + value.staffid + "\" class=\"off\"";
                    var lastLoginText = "";
                    if (value.last_login) {
                        lastLoginText = moment(value.last_login, "YYYYMMDD h:mm:ss").fromNow();
                    } else {
                        lastLoginText = "Never";
                    }
                    offlineUser += " data-toggle=\"tooltip\" title=\"" + prchatSettings.chatLastSeenText + ": " + lastLoginText + "\">";
                    offlineUser += "<span class=\"user-name\">" + strCapitalize(value.firstname + " " + value.lastname) + "</span><img src=\"" + fetchUserAvatar(value.staffid, value.profile_image) + "\" class=\"imgOther\" /></a>";
                }
            }
        });
        $("#pusherChat #members-list").html("");
        $("#pusherChat #members-list").prepend(onlineUser + offlineUser);


        if (subscribed_event === true) {
            if (prchatSettings.getUnread != null) {
                var parsedUnreadMessages = JSON.parse(prchatSettings.getUnread);
                $.each(parsedUnreadMessages, function (i, sender) {
                    insertId = $("#pusherChat #members-list a#" + sender.sender_id);
                    if (sender.sender_id === $(insertId).attr("id")) {
                        notification = "<span class=\"unread-notifications\" data-badge=\"" + sender.count_messages + "\"></span>";
                        $(insertId).addClass("animated flash");
                        $(notification).insertBefore("#pusherChat #members-list a#" + sender.sender_id + " span");
                    }
                });
            }

            $.each(getActiveChatWindowsFromStorage(), function (index, obj) {
                var $userList = $("body").find("#members-list a[href=\"#" + obj.id + "\"]");
                $userList.addClass("active-windows-click");
                $userList.click();
            });
        }
    });
}

/*---------------* Capitalize first string of letter *---------------*/
function strCapitalize(string)
{
    if (string != undefined) {
        //  All unicode languages support
        var firstCP = string.codePointAt(0);
        var index = firstCP > 0xFFFF ? 2 : 1;

        return String.fromCodePoint(firstCP).toUpperCase() + string.slice(index);
    }
}

function updateLatestMessages(id)
{
    $.post(prchatSettings.updateUnread, {
        id: id
    }).done(function (r) {
        if (r != "true") {
            return false;
        }
    });
}

function fetchUserAvatar(id, image_name)
{
    var type = "thumb";
    var url = site_url + "/assets/images/user-placeholder.jpg";
    if (image_name == false) {
        return url;
    }
    if (image_name != null) {
        url = site_url + "/uploads/staff_profile_images/" + id + "/" + type + "_" + image_name;
    } else {
        url = site_url + "/assets/images/user-placeholder.jpg";
    }
    return url;
}

function prchat_setNoMoreMessages(to)
{
    if ($("#no_messages_" + to).length == 0) {
        $(".logMsg#id_" + to).prepend("<div class=\"text-center\" style=\"margin-top:5px;\" id=\"no_messages_" + to + "\">" + prchatSettings.noMoreMessagesText + "</div>");
    }
}

/*---------------* Sound functions *---------------*/
var getSound = new Audio(site_url + "modules/prchat/assets/chat_implements/sounds/push.mp3");
var getSeenSound = new Audio(site_url + "modules/prchat/assets/chat_implements/sounds/chat_seen.mp3");

var isSoundMuted = "";

function playChatSound()
{
    return $(
        "<audio class=\"sound-player\" autoplay=\"autoplay\" " + isSoundMuted + " style=\"display:none;\">" + "<source src=\"" + arguments[0] + "\" />" + "<embed src=\"" + arguments[0] + "\" hidden=\"true\" autostart=\"true\" loop=\"false\"/>" + "</audio>").appendTo("body");
}

function appendUserSound()
{
    return $(
        "<audio class=\"sound-player\" autoplay=\"autoplay\" style=\"display:none;\">" + "<source src=\"" + arguments[0] + "\" />" + "<embed src=\"" + arguments[0] + "\" hidden=\"true\" autostart=\"true\" loop=\"false\"/>" + "</audio>").appendTo("body");
}

function userSeenNotify()
{
    appendUserSound(getSeenSound.src) && stopSound();
}


function stopSound()
{
    setTimeout(function () {
        $(".sound-player").remove();
    }, 1000);
}

function playPushSound()
{
    playChatSound(getSound.src);
    stopSound();
}

var positions = JSON.parse(localStorage.positions || "{}");
var availableWidth = document.body.clientWidth - 305;
var availableHeight = document.body.clientHeight - 250;

$("#pusherChat .draggable").draggable({
    axis: "x,y",
    scroll: false,
    handle: "#membersContent .topInfo, #membersContent .chat-footer",
    start: function (event, ui) {
        $("#mainChatId").addClass("main-chat-dragging isToggled");
    },
    drag: function (event, ui) {
        if (ui.position.left > 0) {
            ui.position.left = 0;
            positions[this.id] = ui.position;
        }
        if (ui.position.left < -availableWidth) {
            ui.position.left = -availableWidth;
            positions[this.id] = -availableWidth;
        }
        if (ui.position.top > 0) {
            ui.position.top = 0;
            positions[this.id] = ui.position;
        }
        if (ui.position.top < -availableHeight) {
            ui.position.top = -availableHeight;
            positions[this.id] = -availableHeight;
        }
        positions[this.id] = ui.position;
        localStorage.positions = JSON.stringify(positions);
    }
});

var scrollPosition = $("#pusherChat .scroll");
window.onload = function () {
    var localStoragePos = localStorage.chat_head_position;
    var localStorageisToggled = localStorage.isToggled;
    if (localStorage.isToggled == "true") {
        chatCircleTransform();
    }
    if (typeof (localStoragePos) != "undefined") {
        if (localStorageisToggled == "true") {
            localStorage.chat_head_position = "block";
            scrollPosition.css("display", "block");
            return;
        }
        scrollPosition.css("display", localStoragePos);
    } else {
        localStorage.chat_head_position = "block";
        scrollPosition.css("display", localStoragePos);
    }
};


function _debounce(func, wait, immediate)
{
    var timeout;
    return function () {
        var context = this,
            args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};


function chatCircleTransform()
{
    var inputColor = $(".colorHolder");
    var inputcolorGradient = $("#colorGradientChanger");

    if (!$("#mainChatId").hasClass("main-chat-dragging")) {
        $(".scroll, .chat-footer, .fa.fa-eercast, .chat-footer .online, .topInfo, #searchUsers, #disableSound, #colorChanger, #membersContent").toggleClass("isToggled");
        (inputColor.is(":visible")) ? inputColor.hide() : inputColor.show();
    }
    $(".toCircle").css({"width": "30px", "height": "30px", "top": "11px", "right": "9px"});
    if ($(".scroll").hasClass("isToggled")) {
        inputcolorGradient.hide();
        localStorage.isToggled = "true";
        localStorage.chat_head_position = "none";
        scrollPosition.css("display", "none");
    } else {
        $(".toCircle").css({"width": "23px", "height": "23px", "top": "unset", "right": "36px"});
        inputcolorGradient.show();
        scrollPosition.css("display", "none");
        localStorage.chat_head_position = "none";
        localStorage.isToggled = "false";
    }
}


function unescapeHtml(unsafe)
{
    return unsafe
        .replace(/&amp;/g, "&")
        .replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">")
        .replace(/&quot;/g, "\"")
        .replace(/&#039;/g, "'");
}

/**
 * Check for audio message then convert to html readable element
 */
function ifAudioRender(message)
{
    /**
     * Check if it is audio message and decode html
     */
    if (message.match("type=\"audio/ogg\"&gt;&lt;/audio&gt")) {
        message = renderHtmlForAudio(message);
    }
    return message;
}


// Delete conversations
const deleteUrl = admin_url + "prchat/Prchat_Controller/purgeConversations";

function purgeStaffHistory()
{
    if (askConfirmation() && promptAccepted()) {
        deleteChatConversation("staff");
    }
}

function purgeClientsHistory()
{
    if (askConfirmation() && promptAccepted()) {
        deleteChatConversation("clients");
    }
}

function purgeGroupsHistory()
{
    if (askConfirmation() && promptAccepted()) {
        deleteChatConversation("groups");
    }
}

function purgeAllHistory()
{
    if (askConfirmation() && promptAccepted()) {
        deleteChatConversation("all");
    }
}

function promptAccepted()
{
    let confirmation = prompt(prchatSettings.typeInToConfirm, "");

    confirmation = confirmation === "DELETE";

    if (!confirmation) alert(prchatSettings.actionCancelled);

    return confirmation;
}

function askConfirmation()
{
    return confirm(prchatSettings.areYouSure);
}

function deleteChatConversation(type)
{
    $.post(deleteUrl, {type: type}).done(function (r) {

        if (true === r.success) alert_float("success", prchatSettings.conversationDeleted);
        if (false === r.success) alert_float("info", prchatSettings.conversationAlreadyDeleted);
        if (r.error) alert_float("info", r.error);

        setTimeout(() => location.reload(), 2000);
    });
}
