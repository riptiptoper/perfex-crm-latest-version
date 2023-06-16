<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">

                    <div class="panel-body">
                      <div class="horizontal-scrollable-tabs mb-5">
                        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                        <div class="horizontal-tabs mb-4">
                          <ul class="nav nav-tabs nav-tabs-horizontal">
                            <?php
                              $i = 0;
                              foreach($tab as $group){
                                ?>
                                <li<?php if($i == 0){echo " class='active'"; } ?>>
                                <a href="<?php echo admin_url('omni_sales/detail_channel_wcm/'.$id.'?group='.$group); ?>" data-group="<?php echo html_entity_decode($group); ?>">
                                  <?php 
                                  if($group == 'product'){
                                    echo _l('os_product'); 
                                  }else{
                                    echo _l($group); 
                                  }
                                  ?>
                                </a>
                                </li>
                            <?php $i++; } ?>
                          </ul>
                        </div>
                          <?php if($status == 0 ){ ?>
                            <span class="label label-primary pull-right status-sync"><?php echo _l('normal_state'); ?></span>
                          <?php }elseif($status == 1) { ?>  
                            <span class="label label-danger pull-right status-sync"><?php echo _l('wait_for_sync'); ?></span>
                          <?php }elseif($status == 2) { ?>
                            <span class="label label-success pull-right status-sync"><?php echo _l('sync_success'); ?></span>
                          <?php } ?>
                          <h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
                          <br>

                        <?php $this->load->view($tabs['view']); ?>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>

