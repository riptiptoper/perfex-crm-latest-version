<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="panel_s">
       <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <h4><?php echo html_entity_decode($title); ?></h4>
          </div>

          <div class="col-md-12">
            <hr>
          </div>

          <div class="col-md-3">
            <?php echo render_select('agent_filter', $agents, array('userid', 'company'), 'agent'); ?>
          </div>

        </div>
        <table class="table table-orders">
          <thead>
            <th><?php echo _l('order_number'); ?></th>
            <th><?php echo _l('agent'); ?></th>
            <th><?php echo _l('order_date'); ?></th>
            <th><?php echo _l('order_value'); ?></th>
            <th><?php echo _l('datecreated'); ?></th>
            <th><?php echo _l('invoice'); ?></th>
            <th><?php echo _l('delivery_date'); ?></th>
            <th><?php echo _l('delivery_status'); ?></th>
            <th><?php echo _l('order_status'); ?></th>

          </thead>
          <tbody>
            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/sales_agent/assets/js/orders/manage_js.php';?>
