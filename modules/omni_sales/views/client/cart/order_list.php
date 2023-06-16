<?php hooks()->do_action('head_element_client'); ?>
<div class="col-md-12">
  <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
         <?php 
         $option = [
          ['id' => -1, 'label' => ''],
          ['id' => 2, 'label' => _l('omni_nornal')],
          ['id' => 6, 'label' => _l('omni_pre_order')]
        ];
        echo render_select('type_filter', $option, array('id' , 'label'), 'omni_type', '', [], [], '', '', false);
        ?>
      </div>
      <div class="col-md-6">
        <br>
        <a href="<?php echo admin_url('omni_sales/omni_sales_client/create_pre_order') ?>" class="btn btn-primary pull-right mtop5">
          <?php echo _l('omni_create_pre_order'); ?>
        </a> 
      </div>
    </div>



    <div class="clearfix"></div>
    <br>
    <div class="clearfix"></div>



    <div class="horizontal-scrollable-tabs mb-5 order-tab">
      <div class="horizontal-tabs mb-4">
        <div class="scroller arrow-left" style="display: block;"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right" style="display: block;"><i class="fa fa-angle-right"></i></div>
        <ul class="nav nav-tabs nav-tabs-horizontal">
          <?php 
          $status_list = omni_status_list();
          $userid = get_client_user_id();
          foreach ($status_list as $key => $value) { ?>
            <li<?php if($tab == $value['id']){echo " class='active'"; } ?>>
            <a href="<?php echo site_url('omni_sales/omni_sales_client/order_list/'.$value['id']); ?>" >
              <?php 
              $count_order = count_portal_order($userid, $value['id'], '', '(channel_id = 2 OR channel_id = 6 OR channel_id = 4)');
              echo html_entity_decode($value['label'].''.($count_order > 0 ? ' <span class="badge badge-portal bg-warning mleft10">'.$count_order.'</span>' : '')); 
              ?>
            </a>
          </li>
        <?php } ?>                              
      </ul>
    </div> 



<input type="hidden" name="status" value="<?php echo html_entity_decode($status); ?>">   
<div class="header-list">
  <ul class="d-flex">
    <li><?php echo _l('order_number'); ?></li>
    <li><?php echo _l('order_date'); ?></li>
    <li><?php echo _l('total_orders'); ?></li>
    <li></li>
  </ul>
</div>  
<div id="list_cart">
  <?php 
  $this->load->view('client/cart/cancelled'); 
  ?>
</div>    
</div>
</div>
</div>
</div>
<input type="hidden" name="token" value="<?php echo html_entity_decode($this->security->get_csrf_hash()); ?>">
<?php hooks()->do_action('client_pt_footer_js'); ?>
