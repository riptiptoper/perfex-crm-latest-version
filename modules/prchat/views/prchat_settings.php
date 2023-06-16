<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Show options for chat pusher in Setup->Settings->Chat Settings
 * get_option pusher_chat_enabled default 1
 */

$chat_enabled = get_option('pusher_chat_enabled'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_enable_option'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_pusher_enable_chat" name="settings[pusher_chat_enabled]" value="1" <?= ($chat_enabled == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_pusher_enable_chat"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_pusher_enable_chat" name="settings[pusher_chat_enabled]" value="0" <?= ($chat_enabled == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_pusher_enable_chat">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>

<?php
/**
 * Show only staff members who have chat permissions
 * get_option chat_show_only_users_with_chat_permissions is by default 0
 */
$show_users_with_chat_permissions = get_option('chat_show_only_users_with_chat_permissions'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_options_show_users_label'); ?><br>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_show_only_users_with_chat_permissions" name="settings[chat_show_only_users_with_chat_permissions]" value="1" <?= ($show_users_with_chat_permissions == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_show_only_users_with_chat_permissions"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_show_only_users_with_chat_permissions" name="settings[chat_show_only_users_with_chat_permissions]" value="0" <?= ($show_users_with_chat_permissions == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_show_only_users_with_chat_permissions">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<div>
    <strong>Note: <strong>
            <span class="text-muted"><?= _l('chat_permissions_info_option_select'); ?></span>
</div>
<hr>


<!--  
* Show options for chat pusher in Setup->Settings->Chat Settings
 get_option chat_client_enabled is by default 1
-->
<?php $chat_client_enabled = get_option('chat_client_enabled'); ?>
<div class="form-group">
    <label for="y_opt_1_chat_client_enabled" class="control-label clearfix">
        <?php echo _l('chat_client_module_enabled'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_client_enabled" name="settings[chat_client_enabled]" value="1" <?= ($chat_client_enabled == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_client_enabled"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_client_enabled" name="settings[chat_client_enabled]" value="0" <?= ($chat_client_enabled == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_client_enabled">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>

<!--  
* Show options for chat pusher in Setup->Settings->Chat Settings
* get_option chat_members_can_create_groups is by default 1
-->
<?php $chat_members_can_create_groups = get_option('chat_members_can_create_groups'); ?>
<div class="form-group">
    <label for="y_opt_1_chat_members_can_create_groups" class="control-label clearfix">
        <?php echo _l('chat_staff_can_create_groups'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_members_can_create_groups" name="settings[chat_members_can_create_groups]" value="1" <?= ($chat_members_can_create_groups == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_members_can_create_groups"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_members_can_create_groups" name="settings[chat_members_can_create_groups]" value="0" <?= ($chat_members_can_create_groups == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_members_can_create_groups">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>

<!--  
* Show options for chat pusher in Setup->Settings->Chat Settings
* get_option chat_staff_can_delete_messages is by default 1 
-->
<?php $can_delete = get_option('chat_staff_can_delete_messages'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_allow_delete_messages'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_staff_can_delete_messages" name="settings[chat_staff_can_delete_messages]" value="1" <?= ($can_delete == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_staff_can_delete_messages"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_staff_can_delete_messages" name="settings[chat_staff_can_delete_messages]" value="0" <?= ($can_delete == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_staff_can_delete_messages">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>
<!--  
* Show options for chat pusher in Setup->Settings->Chat Settings
* get_option chat_allow_staff_to_create_tickets is by default 1 
-->
<?php $alllow_to_covert_tickets = get_option('chat_allow_staff_to_create_tickets'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_allow_staff_to_create_tickets'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_allow_staff_to_create_tickets" name="settings[chat_allow_staff_to_create_tickets]" value="1" <?= ($alllow_to_covert_tickets == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_allow_staff_to_create_tickets"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_allow_staff_to_create_tickets" name="settings[chat_allow_staff_to_create_tickets]" value="0" <?= ($alllow_to_covert_tickets == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_allow_staff_to_create_tickets">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>


<!--  
* Show options for chat pusher in Setup->Settings->Chat Settings
* get_option chat_desktop_messages_notifications is by default 1
-->
<?php $notification_option = get_option('chat_desktop_messages_notifications'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('chat_show_desktop_messages_notifications'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_chat_desktop_messages_notifications" name="settings[chat_desktop_messages_notifications]" value="1" <?= ($notification_option == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_chat_desktop_messages_notifications"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_chat_desktop_messages_notifications" name="settings[chat_desktop_messages_notifications]" value="0" <?= ($notification_option == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_chat_desktop_messages_notifications">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>
<div class="form-group">
    <label><strong><?= _l("chat_delete_older_conversation_label"); ?></strong></label><br>
    <span class="text-danger"><?= _l("chat_delete_note"); ?></span><br><br>
    <button class="btn btn-danger" data-toggle="tooltip" title="<?= _l("chat_all_messages_delete_text"); ?>" type="button" onclick="purgeStaffHistory()"><?= _l("chat_purge_staff_label"); ?></button>
    <button class="btn btn-danger" data-toggle="tooltip" type="button" title="<?= _l("chat_all_messages_delete_text"); ?>" onclick="purgeClientsHistory()"><?= _l("chat_purge_clients_label"); ?></button>
    <button class="btn btn-danger" data-toggle="tooltip" type="button" title="<?= _l("chat_all_messages_delete_text"); ?>" onclick="purgeGroupsHistory()"><?= _l("chat_purge_groups_label"); ?></button>
    <hr>
    <label><strong><?= _l("chat_delete_all_data"); ?></strong></label><br>
    <button class="btn btn-danger" data-toggle="tooltip" type="button" title="<?= _l("chat_wipe_confirm"); ?>" onclick="purgeAllHistory()"><?= _l("chat_purge_everything"); ?></button>
</div>
<hr>

<div class="form-group panel-body">
    <span class="text-primary font-medium"><?= _l('chat_permissions_info'); ?></span>
</div>