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
		          	<a href="<?php echo site_url('sales_agent/portal/sale_invoice'); ?>" class="btn btn-info pull-right"><?php echo _l('new'); ?></a>
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
		        	<?php $statuses = [ 
		        		['id' => 'unpaid', 'label' => _l('unpaid')],
		        		['id' => 'paid', 'label' => _l('paid')],
		        		['id' => 'partially_paid', 'label' => _l('partially_paid')],
		        	];

		        		$select = '';
		        		echo sa_render_select('status', $statuses, array('id', 'label'), 'status', $select); 
		        	 ?>
		        </div>
		        <div class="col-md-3">
		        	<?php 
		        		$select = '';
		        		echo sa_render_select('customer', $clients, array('id', 'name'), 'customer', $select);
		        	 ?>
		        </div>
		    </div>

     		<table class="table table-sales-invoices">
		        <thead>
		          <th><?php echo _l('sa_invoice').' #'; ?></th>
		          <th><?php echo _l('sa_amount'); ?></th>
		          <th><?php echo _l('sa_total_tax'); ?></th>
		          <th><?php echo _l('sa_date'); ?></th>
		          <th><?php echo _l('sa_customer'); ?></th>
		          <th><?php echo _l('sa_due_date'); ?></th>
		          <th><?php echo _l('sa_status'); ?></th>
		          <th><?php echo _l('options'); ?></th>
		        </thead>
		        <tbody>
		        </tbody>
		    </table>
     	</div>
    </div>
</div>

<?php require 'modules/sales_agent/assets/js/portal/sale_invoices/manage_js.php';?>