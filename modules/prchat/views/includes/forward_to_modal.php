<div class="modal fade" data-backdrop="static" id="forwardToModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="any">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"><?= _l('chat_forward_message_btn') ?></h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="_searchUsers"><?= _l('chat_search_users') ?></label>
                        <input type="text" id="_searchUsers" placeholder="<?= _l('kb_search') ?>..." class="form-control">
                    </div>
                    <span><strong><?= _l('staff_members') ?></strong></span>
                    <ul class="staffList">
                        <?php
                        if (is_array($staff) && !empty($staff)) {
                            foreach ($staff as $member) : if (get_staff_user_id() == $member['staffid']) continue;  ?>
                                <a target="_blank" href="<?= admin_url() . 'profile/' . $member['staffid'];  ?>">
                                    <img src="<?= staff_profile_image_url($member['staffid'], 'small'); ?>" data-toggle="tooltip" data-title="<?= $member['firstname'] . ' ' . $member['lastname']; ?>" class="staff-profile-image-small mright5" data-original-title="" title="<?= $member['firstname'] . ' ' . $member['lastname'] ?>">
                                </a>
                                <li class="_user" id="<?= $member['staffid'] ?>"><?= $member['firstname'] . ' ' . $member['lastname']; ?>
                                    <button style="text-transform:capitalize;" class="btn btn-primary" onClick="_forwardTo(<?= $member['staffid'] ?>, this,null)">Send</button>
                                </li>
                        <?php endforeach;
                        } ?>
                    </ul>
                    <span><strong><?= _l('chat_groups_text') ?></strong></span>
                    <ul class="groupsList">
                        <?php
                        if (is_array($groups) && !empty($groups)) {
                            foreach ($groups as $group) : ?>
                                <li id="<?= $group['group_id'] ?>">
                                    <img class="_group_image" src="<?= module_dir_url('prchat', 'assets/chat_implements/icons/groups.png'); ?>" data-toggle="tooltip" data-title="<?= $group['group_name'] ?>">
                                    <span><?= $group['group_name']; ?></span>
                                    <button class="btn btn-primary" onClick="_forwardTo(<?= $group['group_id'] ?>, this,'groups')"><?= _l('send') ?></button>
                                </li>
                        <?php endforeach;
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    $('ul.staffList').on('scroll', function() {
        appendNextStaff($(this));
    })

    $("body").on("keyup", '#_searchUsers', _debounce(function(e) {

        var value = $.trim($(this).val().toLowerCase());
        var searchInDatabase = false;

        if (value == '') {
            $(this).val('');
            $(this).parents('#forwardToModal').find('li').prev().show();
            $(this).parents('#forwardToModal').find('li').show();
            return;
        }

        if (value.length) {
            $("#forwardToModal  ul.staffList li").each(function() {
                if ($(this).text().toLowerCase().indexOf(value) > -1) {
                    $(this).show();
                    $(this).prev().show();
                } else {
                    $(this).hide();
                    $(this).prev().hide();
                }
                if ($(this).parent('ul').find(':visible:last').length === 0) {
                    searchInDatabase = true;
                }
            });


            if (searchInDatabase) {
                var csrf = '';
                if (typeof csrfData != 'undefined') {
                    csrf = csrfData.formatted.csrf_token_name;
                }
                $('.staffList').prepend('<div class="loaderParent"><div class="loading"></div></div>');
                $.getJSON("<?php echo site_url('prchat/Prchat_Controller/searchStaffForForward'); ?>", {
                        search: $("#_searchUsers").val(),
                    })
                    .done(function(staff) {
                        if (staff) {
                            _appendUsers(staff, true);
                        }
                        $('.staffList').find('.loaderParent').remove();
                    });

                return false;
            }
        }
    }, 500));

    var _staffAppendOffset = 11;

    function appendNextStaff(el) {
        var pos = $(el).scrollTop();

        if (el.scrollTop() + el.innerHeight() >= el[0].scrollHeight) {
            _staffAppendOffset += 10;

            if ($('#_searchUsers').val() == '') {
                $('#forwardToModal .modal-footer').append('<div class="loaderParent"><div class="loading"></div></div>');
            }

            $.getJSON("<?php echo site_url('prchat/Prchat_Controller/appendMoreStaff'); ?>", {
                    offset: _staffAppendOffset
                })
                .done(function(staff) {
                    if (staff) {
                        _appendUsers(staff, true);
                    }
                    $('#forwardToModal .modal-footer').find('.loaderParent').remove();
                });
        }
    }

    function _appendUsers(staff, append) {
        var _newStafFsers = [];
        var l_send = "<?= _l('send') ?>";
        $.each(staff, function(i, user) {
            let userIsAdded = $('#forwardToModal').find('ul.staffList li#' + user.staffid + '').length;
            if (!userIsAdded) {
                let staffFullName = user.firstname + ' ' + user.lastname;
                _newStafFsers.push(`
                <a target="_blank" href="${admin_url}/profile/${user.staffid}">
                <img class="_imageAppended staff-profile-image-small mright5" src="${fetchUserAvatar(user.staffid,user.profile_image)}" 
                     data-toggle="tooltip" 
                     data-title="${staffFullName}">
                </a>
                <li class="_appended" id="${user.staffid}">${staffFullName}
                <button class="btn btn-primary btnCapitalize" onClick="_forwardTo(${user.staffid},this,null)">${l_send}</button>
                </li>`);
            }
        });
        (!append) ?
        $('ul.staffList').prepend.apply($('ul.staffList'), _newStafFsers):
            $('ul.staffList').append.apply($('ul.staffList'), _newStafFsers);
    }

    function _forwardTo(receiver_id, el, target) {
        $(el).attr('disabled', true);

        let message = $('#forwardToModal').find('._dataMessage').attr('data-message');
        let messageEscaped = $('#forwardToModal').find('._dataMessage.escaped').attr('data-message-escaped');
        message = message.replace(/"/g, "'").replace('controls="', 'controls ');


        if (message.match('<audio controls')) {
            message = message.replace(/'/g, '"');
            messageEscaped = "<?= _l('chat_new_audio_message_sent'); ?>";
        }

        if (message.match('data-lity=')) {
            message = message.match(/href='([^']*)/)[1];
        }

        if (message.match("class='prchat_convertedImage'")) {
            message = message.match(/href='([^']*)/)[1];
            messageEscaped = "<?= _l('chat_new_file_sent'); ?>";
        }

        if (target === null) {
            // Send to staff
            $.post(prchatSettings.serverPath, {
                from: userSessionId,
                to: receiver_id,
                msg: message,
                typing: false
            });

            $('li.contact#' + receiver_id + ' a .meta p.preview').html("<?= _l('chat_message_you') ?>" + ' ' + messageEscaped);
            $('li.contact#' + receiver_id + ' a .meta .pull-right.time_ago').html(moment().format('hh:mm A'));
        }

        if (target == 'groups') {

            let group_id = $(el).parent('li').attr('id');

            $.post(prchatSettings.groupMessagePath, {
                from: userSessionId,
                group_id: group_id,
                g_message: message,
                typing: false
            });

        }
    }
</script>

<style>
    #forwardToModal .loaderParent {
        display: flex;
        justify-content: center;
    }

    #forwardToModal .loading {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: forward_spin 2s linear infinite;
    }

    #forwardToModal .modal-dialog {
        max-width: 500px;
    }

    #forwardToModal .modal-title {
        margin-left: 22px;
    }

    #forwardToModal .modal-body {
        padding: 0px;
    }

    #forwardToModal .modal-body .col-md-12 {
        margin-top: 13px;
    }

    #forwardToModal ul.staffList {
        margin-top: 15px;
        overflow-x: auto;
        max-height: 400px;
    }

    #forwardToModal ul.staffList img.staff-profile-image-small {
        float: left;
        clear: both;
        margin-top: 4px;
        margin-right: 10px;
    }

    #forwardToModal ul.staffList li._user {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 3px 0px 3px 0px;
        margin-right: 5px;
    }

    #forwardToModal ul.staffList img._imageAppended {
        float: left;
        clear: both;
        margin-top: 4px;
        margin-right: 10px;
    }

    #forwardToModal ul.staffList li._appended {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 3px 0px 3px 0px;
        margin-right: 5px;
    }

    #forwardToModal ul.staffList li button {
        text-transform: capitalize;
    }

    #forwardToModal.modal {
        z-index: 99999999999999999999999;
    }

    #forwardToModal ul.groupsList {
        margin-top: 15px;
        overflow-x: auto;
        max-height: 400px;
    }

    #forwardToModal ul.groupsList li {
        clear: both;
        margin-right: 5px;
    }

    #forwardToModal ul.groupsList li img._group_image {
        float: left;
        padding-bottom: 5px;
        width: 35px;
        border-radius: 0px;
    }

    #forwardToModal ul.groupsList li span {
        position: absolute;
        margin-top: 8px;
        margin-left: 8px;
    }

    #forwardToModal ul.groupsList li button {
        text-transform: capitalize;
        float: right;
    }

    @keyframes forward_spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>