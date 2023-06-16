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
          <a href="<?php echo site_url('sales_agent/portal/pur_order'); ?>" class="btn btn-info pull-right"><?php echo _l('new'); ?></a>
         </div>
      </div>
      <hr>

      <div class="row">
        <div class="col-md-3">
          <?php $order_date = '';
                  echo sa_render_date_input('from_date','from_date',$order_date); ?>
        </div>

        <div class="col-md-3">
          <?php $order_date = '';
                  echo sa_render_date_input('to_date','to_date',$order_date); ?>
        </div>

         <div class="col-md-3">
            <?php 
            $status = [
              ['id' => 1, 'label' => _l('purchase_draft')],
              ['id' => 2, 'label' => _l('purchase_approved')],
              ['id' => 3, 'label' => _l('pur_rejected')],
              ['id' => 4, 'label' => _l('pur_canceled')],
            ];
            $approval_status = '';
               echo sa_render_select('approve_status', $status, array('id', 'label'), 'approval_status', $approval_status); ?>
          </div>

          <div class="col-md-3">
            <?php 
            $order_status = [
              ['id' => 'new', 'label' => _l('new_order')],
              ['id' => 'confirmed', 'label' => _l('sa_confirmed')],
              ['id' => 'delivering', 'label' => _l('sa_delivering')],
              ['id' => 'delivered', 'label' => _l('sa_delivered')],
              ['id' => 'cancelled', 'label' => _l('sa_cancelled')],
            ];
            $od_status = '';
               echo sa_render_select('order_status', $order_status, array('id', 'label'), 'order_status', $od_status); ?>
          </div>
      </div>

      <table class="table table-purchase-order">
        <thead>
          <th><?php echo _l('order_number'); ?></th>
          <th><?php echo _l('order_date'); ?></th>
          <th><?php echo _l('order_value'); ?></th>
          <th><?php echo _l('datecreated'); ?></th>
          <th><?php echo _l('approval_status'); ?></th>
          <th><?php echo _l('delivery_date'); ?></th>
          <th><?php echo _l('delivery_status'); ?></th>
          <th><?php echo _l('order_status'); ?></th>
          <th><?php echo _l('options'); ?></th>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/purchase_orders/manage_js.php';?>