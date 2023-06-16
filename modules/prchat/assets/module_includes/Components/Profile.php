<div id="profile">
     <div class="btn-group pull-right" id="mainFilterDiv" data-toggle="tooltip" data-title="<?= _l('filter_by'); ?>">
          <button type="button" class="btn btn-default dropdown-toggle btn_filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fa fa-filter filterAttrs" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu">
               <li>
                    <a href="#" data-filter="all" onclick="_chatFilter(this); return false;">
                         <?= _l('all'); ?> </a>
                    <a href="#" data-filter="online" onclick="_chatFilter(this); return false;">
                         <?= _l('chat_online_filter'); ?> </a>
                    <a href="#" data-filter="offline" onclick="_chatFilter(this); return false;">
                         <?= _l('chat_offline_filter'); ?> </a>
                    <a href="#" data-filter="unread" onclick="_chatUnreadFilter(); return false;">
                         <?= _l('chat_unread_filter'); ?> </a>
               </li>
          </ul>
     </div>
     <div class="wrap">
          <?php echo staff_profile_image($params['props']->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left'), 'small', ['id' => 'profile-img']); ?>
          <p>
               <?php echo get_staff_full_name(); ?>
          <div id="status-options" class="">
               <ul>
                    <li id="status-online" class="active"><span class="status-circle"></span>
                         <p><?= _l('chat_status_online'); ?></p>
                    </li>
                    <li id="status-away"><span class="status-circle"></span>
                         <p><?= _l('chat_status_away'); ?></p>
                    </li>
                    <li id="status-busy"><span class="status-circle"></span>
                         <p><?= _l('chat_status_busy'); ?></p>
                    </li>
                    <li id="status-offline"><span class="status-circle"></span>
                         <p><?= _l('chat_status_offline'); ?></p>
                    </li>
               </ul>
          </div>
          </p>
     </div>
</div>
<script>
     function _chatUnreadFilter() {
          $(".chat_contacts_list li a").filter(function(i, el) {
               const user = $(el);
               if (user.find('span.unread-notifications').is(":hidden")) {
                    user.toggle();
               }
          });
     }

     function _chatFilter(el) {

          const filterName = $(el).data('filter');
          const liOff = $(".chat_contacts_list li a.off");
          const liOn = $(".chat_contacts_list li a.on");
          const status = $(".chat_contacts_list li .wrap span");

          if (filterName == 'all') {
               $(".chat_contacts_list li, .chat_contacts_list li a").show();
               return;
          }

          if ($(".chat_contacts_list").children('li').children('a').attr('style') == "display: none;") {
               $(".chat_contacts_list li a").toggle();
               return;
          }

          status.filter(function() {
               if (filterName == 'online') {
                    liOff.parent().hide();
                    (liOn.parent().is(":hidden")) && liOn.parent().show();
               }
               if (filterName == 'offline') {
                    liOn.parent().hide();
                    (liOff.parent().is(":hidden")) && liOff.parent().show();
               }
          });
     }
</script>