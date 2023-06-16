<?php $instance = &get_instance(); ?>
<form hidden enctype="multipart/form-data" name="clientFileForm" id="clientFileForm" method="post" onsubmit="uploadClientFileForm(this);return false;">
     <input type="file" class="file" name="userfile" required />
     <input type="submit" name="submit" class="save" value="save" />
     <input type="hidden" name="<?php echo $instance->security->get_csrf_token_name(); ?>" value="<?php echo $instance->security->get_csrf_hash(); ?>">
</form>
<form hidden method="post" enctype="multipart/form-data" name="clientMessagesForm" id="clientMessagesForm" onsubmit="return false;">
     <div class="message-input client_msg_input">
          <div class="wrap">
               <textarea type="text" name="client_message" class="client_chatbox ays-ignore" placeholder="<?= _l('chat_type_a_message'); ?>"></textarea>
               <input type="hidden" class="ays-ignore from" name="from" value="staff_" />
               <input type="hidden" class="ays-ignore to" name="to" value="client_" />
               <input type="hidden" class="ays-ignore typing" name="typing" value="false" />
               <input type="hidden" class="ays-ignore" name="<?php echo $instance->security->get_csrf_token_name(); ?>" value="<?php echo $instance->security->get_csrf_hash(); ?>">
               <i class="fa-regular fa-file-image attachment clientFileUpload" data-container="body" data-toggle="tooltip" title="<?php echo _l('chat_file_upload'); ?>" aria-hidden="true"></i>
               <?php loadChatComponent('MicrophoneIcon'); ?>
               <?php loadChatComponent('SearchMessages', ['props' => 'search_client_messages']); ?>
               <input type="hidden" class="ays-ignore invisibleUnread" value="" />
               <button class="submit enterClientBtn" name="enterClientBtn"><svg class="fa-paper-plane" fill="#ffffff" viewBox="0 0 24 24">
                         <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M8,7.71V11.05L15.14,12L8,12.95V16.29L18,12L8,7.71Z" />
                    </svg></button>
          </div>
     </div>
</form>
