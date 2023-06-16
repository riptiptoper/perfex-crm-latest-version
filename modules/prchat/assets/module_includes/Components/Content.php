<div class="content">
    <div id="sharedFiles">
        <i class="fa fa-times-circle-o" aria-hidden="true"></i>
        <div class="history_slider">
            <!-- Message and files history -->
        </div>
    </div>
    <div class="chat_group_options">
        <!-- Group options  -->
    </div>
    <div class="contact-profile">
        <a class="actionTask" href="#" data-type="" data-user-id="" data-name="" onclick="chatNewTask(this); return false;">
            <i class="fa fa-check-circle" aria-hidden="true" data-toggle="tooltip" title="<?= _l('chat_associate_task'); ?>"></i></a>
        <svg onclick="chatBackMobile()" data-toggle="tooltip" title="Back" class="chat_back_mobile" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z" />
        </svg>
        <img src="" class="img img-responsive staff-profile-image-small pull-left" alt="" />
        <p></p>
        <i class="fa fa-volume-up user_sound_icon" data-toggle="tooltip" title="<?= _l('chat_sound_notifications'); ?>"></i>
        <div class="social-media mright15">
            <svg data-toggle="tooltip" data-container="body" title="<?php echo _l('chat_shared_files'); ?>" data-placement="left" class="fa fa-share-alt" id="shared_user_files" version="1.0" xmlns="http://www.w3.org/2000/svg" width="492.000000pt" height="390.000000pt" viewBox="0 0 492.000000 390.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,390.000000) scale(0.100000,-0.100000)">
                    <path class="stroke-2" d="M1010 1955 l0 -1735 855 0 855 0 0 95 0 95 -750 0 -750 0 0 1540 0 1540 910 0 910 0 0 -315 0 -315 330 0 330 0 0 -265 0 -265 108 0 107 0 -2 377 -2 378 -318 302 -318 303 -1132 0 -1133 0 0 -1735z m2592 1189 c64 -60 117 -112 117 -116 1 -5 -114 -8 -254 -8 l-255 0 0 246 0 246 138 -130 c75 -71 189 -179 254 -238z" />
                    <path class="stroke-2" d="M1587 2723 c-4 -3 -7 -53 -7 -110 l0 -103 495 0 495 0 0 110 0 110 -488 0 c-269 0 -492 -3 -495 -7z" />
                    <path class="stroke-2" d="M1580 2030 l0 -100 570 0 570 0 0 100 0 100 -570 0 -570 0 0 -100z" />
                    <path class="stroke-2" d="M3473 2065 c-140 -38 -218 -147 -211 -296 l3 -61 -204 -140 -204 -141 -61 22 c-76 26 -140 27 -208 1 -115 -43 -178 -132 -178 -251 0 -121 74 -219 192 -254 69 -20 117 -19 188 5 l60 20 208 -127 209 -128 -1 -80 c-1 -71 2 -86 29 -134 82 -149 276 -189 423 -89 55 38 89 91 104 160 46 218 -188 388 -403 292 -32 -15 -64 -24 -71 -21 -7 3 -98 59 -203 125 -171 108 -190 122 -185 143 6 32 6 171 0 187 -3 9 69 63 200 153 l206 140 58 -27 c160 -73 350 10 395 172 39 140 -40 279 -184 323 -78 24 -91 25 -162 6z" />
                    <path class="stroke-2" d="M1580 1595 c-1 -3 -1 -51 -1 -107 l-1 -103 266 -3 266 -2 0 110 0 110 -265 0 c-146 0 -265 -2 -265 -5z" />
                    <path class="stroke-2" d="M1580 1038 c-1 -2 -2 -51 -3 -111 l-2 -107 255 0 255 0 -2 107 -2 108 -250 3 c-138 1 -251 1 -251 0z" />
                </g>
            </svg>
            <a href="" id="fa-skype" data-toggle="tooltip" data-container="body" class="mright5" title="<?php echo _l('chat_call_on_skype'); ?>"><i class="fa-brands fa-skype" aria-hidden="true"></i></a>
            <a href="" id="fa-facebook" target="_blank" class="mright5">
                <i class="fa-brands fa-facebook" aria-hidden="true"></i>
            </a>
            <a href="" id="fa-linkedin" target="_blank" class="mright5">
                <i class="fa-brands fa-linkedin" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="messages" onscroll="loadMessages(this)">
        <svg class="message_loader" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
        </svg>
        <span class="userIsTyping bounce" id="">
            <img src="<?php echo module_dir_url('prchat', 'assets/chat_implements/userIsTyping.gif'); ?>" />
        </span>
        <ul>
        </ul>
    </div>
    <div class="group_messages" onscroll="loadGroupMessages(this)">
        <svg class="message_group_loader" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
        </svg>
        <div class="chat_group_messages">
            <ul>
            </ul>
        </div>
    </div>
    <?php if (isClientsEnabled()) : ?>
        <div class="client_messages" id="">
            <svg class="message_loader" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
            <span class="userIsTyping bounce" id="">
                <img src="<?php echo module_dir_url('prchat', 'assets/chat_implements/userIsTyping.gif'); ?>" />
            </span>
            <div class="chat_client_messages">
                <!-- Client messages -->
                <ul>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <!-- Staff -->
    <?php loadChatComponent('StaffForm'); ?>
    <!-- Groups -->
    <?php loadChatComponent('GroupsForm'); ?>
    <!-- Clients -->
    <?php loadChatComponent('ClientsForm'); ?>

</div>
