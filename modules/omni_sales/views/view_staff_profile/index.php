      <div class="panel_s">

        <div class="panel-body">
        <h4 class="no-margin">
          <?php echo _l('staff_profile_string'); ?>
        </h4>
       <hr class="hr-panel-heading" />
          <?php if($staff_p->active == 0){ ?>
          <div class="alert alert-danger text-center"><?php echo _l('staff_profile_inactive_account'); ?></div>
          <hr />
          <?php } ?>
          <div class="button-group mtop10 pull-right">
           <?php if(!empty($staff_p->facebook)){ ?>
            <a href="<?php echo html_escape($staff_p->facebook); ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa fa-facebook"></i></a>
            <?php } ?>
            <?php if(!empty($staff_p->linkedin)){ ?>
            <a href="<?php echo html_escape($staff_p->linkedin); ?>" class="btn btn-default btn-icon"><i class="fa fa-linkedin"></i></a>
            <?php } ?>
            <?php if(!empty($staff_p->skype)){ ?>
            <a href="skype:<?php echo html_escape($staff_p->skype); ?>" data-toggle="tooltip" title="<?php echo html_escape($staff_p->skype); ?>" target="_blank" class="btn btn-default btn-icon"><i class="fa fa-skype"></i></a>
            <?php } ?>
          </div>
          <div class="clearfix"></div>

          <?php echo staff_profile_image($staff_p->staffid,array('staff-profile-image-thumb'),'thumb'); ?>
          <div class="profile mtop20 display-inline-block">
            <h4>
            <?php echo $staff_p->firstname . ' ' . $staff_p->lastname; ?>
              <?php if($staff_p->last_activity && $staff_p->staffid != get_staff_user_id()){ ?>
              <small> - <?php echo _l('last_active'); ?>:
                <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($staff_p->last_activity); ?>">
                  <?php echo time_ago($staff_p->last_activity); ?>
                </span>
              </small>
            <?php } ?>
            </h4>
            <p class="display-block"><i class="fa fa-envelope"></i> <a href="mailto:<?php echo $staff_p->email; ?>"><?php echo $staff_p->email; ?></a></p>
            <?php if($staff_p->phonenumber != ''){ ?>
            <p><i class="fa fa-phone-square"></i> <?php echo $staff_p->phonenumber; ?></p>
            <?php } ?>
            <?php if(count($staff_departments) > 0) { ?>
            <div class="form-group mtop10">
              <label for="departments" class="control-label"><?php echo _l('staff_profile_departments'); ?></label>
              <div class="clearfix"></div>
              <?php
              foreach($departments as $department){ ?>
              <?php
              foreach ($staff_departments as $staff_department) {
               if($staff_department['departmentid'] == $department['departmentid']){ ?>
               <div class="chip-circle"><?php echo $staff_department['name']; ?></div>
               <?php }
             }
             ?>
             <?php } ?>
           </div>
           <?php } ?>
         </div>
       </div>
     </div>