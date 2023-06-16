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
          <div class="col-md-6">
            <a href="<?php echo admin_url('sales_agent/agent_program'); ?>" class="btn btn-info pull-right"><?php echo _l('new'); ?></a>
          </div>
          <div class="col-md-12">
            <hr>
          </div>

          <div class="col-md-3">
            <?php echo render_select('group_filter', $agent_groups, array('id', 'name'), 'agent_groups'); ?>
          </div>

          <div class="col-md-3">
            <?php echo render_select('agent_filter', $agents, array('userid', 'company'), 'agent'); ?>
          </div>

        </div>
        <table class="table table-agent-program">
          <thead>
            <th><?php echo _l('name'); ?></th>
            <th><?php echo _l('from_date'); ?></th>
            <th><?php echo _l('to_date'); ?></th>
            <th><?php echo _l('added_by'); ?></th>
            <th><?php echo _l('agent_groups'); ?></th>
            <th><?php echo _l('agents'); ?></th>
            <th><?php echo _l('options'); ?></th>
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
<?php require 'modules/sales_agent/assets/js/programs/manage_js.php';?>
