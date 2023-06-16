<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
    <div class="row">
  
    
    <div class="horizontal-scrollable-tabs  col-md-3">
      
           <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
            <?php
            $i = 0;
            foreach($tab as $groups){
              ?>
              <li <?php if($i == 0){echo " class='active'"; } ?>>
              <a href="<?php echo admin_url('sales_agent/settings?group='.$groups); ?>" data-group="<?php echo html_entity_decode($groups); ?>">
               <?php echo _l($groups); ?></a>
              </li>
              <?php $i++; } ?>
            </ul>
       
      </div>

 
  <div class="col-md-9">
    <div class="panel_s">
     <div class="panel-body">

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
</body>
</html>
<?php require 'modules/sales_agent/assets/js/settings/currency_rate_js.php';?>
