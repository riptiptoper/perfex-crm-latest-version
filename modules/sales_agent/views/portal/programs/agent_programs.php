<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
  <div class="col-md-12 mtop15">
    <div class="panel_s">
     <div class="panel-body">
      <h4><?php echo html_entity_decode($title); ?></h4>
      <hr>
      <table class="table table-agent-program">
        <thead>
          <th><?php echo _l('name'); ?></th>
          <th><?php echo _l('from_date'); ?></th>
          <th><?php echo _l('to_date'); ?></th>
          <th><?php echo _l('added_by'); ?></th>
          <th><?php echo _l('datecreated'); ?></th>
          <th><?php echo _l('status'); ?></th>
          <th><?php echo _l('options'); ?></th>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/programs/manage_js.php';?>
