<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
  <div class="col-md-12">
    <div class="panel_s">
     <div class="panel-body">
    <div class="horizontal-scrollable-tabs  mb-5">
      <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
      <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>

      <div class="horizontal-tabs mb-4">
        <ul class="nav nav-tabs nav-tabs-horizontal">
          <?php
          $i = 0;
          foreach($tab as $_group){
            ?>
            <li<?php if($i == 0){echo " class='active'"; } ?>>
            <a href="<?php echo admin_url('omni_sales/manage_customer_report?group='.$_group); ?>" data-group="<?php echo html_entity_decode($_group); ?>">
              <?php 
                echo _l($_group); 
              ?></a>

            </li>
            <?php $i++; } ?>
          </ul>
      </div>

        <?php $this->load->view($tabs['view']); ?>
        
     </div>
  </div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php if($group == 'manage_customer_report' ){
require 'modules/omni_sales/assets/js/customer_report/manage_customer_report_js.php';
}else{ 
require 'modules/omni_sales/assets/js/customer_report/manage_create_customer_report_js.php';
} ?>
</body>
</html>


