<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>
  <div class="col-md-12 mtop15">
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <h4><?php echo html_entity_decode($title); ?></h4>
        </div>
         <div class="col-md-6">
          <a href="<?php echo site_url('sales_agent/portal/client'); ?>" class="btn btn-info pull-right"><?php echo _l('new'); ?></a>
         </div>
         <div class="col-md-12">
          <hr>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <?php 
           echo sa_render_select('group',$groups,array('id','name'),'group', '');
          ?>
        </div>
      </div>
     
      <table class="table table-client">
        <thead>
          <th><?php echo _l('name'); ?></th>
          <th><?php echo _l('email'); ?></th>
          <th><?php echo _l('phone'); ?></th>
          <th><?php echo _l('group'); ?></th>
          <th><?php echo _l('datecreated'); ?></th>
          <th><?php echo _l('options'); ?></th>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/clients/manage_js.php';?>