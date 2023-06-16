<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open_multipart(site_url('sales_agent/portal/sa_setting'),array('id'=>'pur_order_setting-form')); ?>
<?php $agent_id = get_sale_agent_user_id(); ?>
<div class="col-md-12">
	<p class="text-dark text-uppercase bold"><?php echo _l('sa_purchase');?></p>
    <hr class="mtop15" />  
</div>

<div class="col-md-6">
	<?php echo render_input('pur_order_prefix','pur_order_prefix',get_sa_option('pur_order_prefix', $agent_id)); ?>
</div>

<div class="col-md-6">
	<?php echo render_input('next_po_number','next_po_number',get_sa_option('next_po_number', $agent_id), 'number'); ?>
</div>

<div class="col-md-12">
	<p class="text-dark text-uppercase bold"><?php echo _l('sales');?></p>
    <hr class="mtop15" />  
</div>

<div class="col-md-6">
	<?php echo render_input('sale_invoice_prefix','sale_invoice_prefix',get_sa_option('sale_invoice_prefix', $agent_id)); ?>
</div>

<div class="col-md-6">
	<?php echo render_input('next_sale_invoice_number','next_sale_invoice_number',get_sa_option('next_sale_invoice_number', $agent_id), 'number'); ?>
</div>

<div class="col-md-12">
	<p class="text-dark text-uppercase bold"><?php echo _l('sa_inventory');?></p>
    <hr class="mtop15" />  
</div>

<div class="col-md-6">
	<?php echo render_input('inventory_received_number_prefix','inventory_received_number_prefix',get_sa_option('inventory_received_number_prefix', $agent_id)); ?>
</div>

<div class="col-md-6">
	<?php echo render_input('next_inventory_received_mumber','next_inventory_received_mumber',get_sa_option('next_inventory_received_mumber', $agent_id), 'number'); ?>
</div>

<div class="col-md-6">
	<?php echo render_input('inventory_delivery_number_prefix','inventory_delivery_number_prefix',get_sa_option('inventory_delivery_number_prefix', $agent_id)); ?>
</div>

<div class="col-md-6">
	<?php echo render_input('next_inventory_delivery_mumber','next_inventory_delivery_mumber',get_sa_option('next_inventory_delivery_mumber', $agent_id), 'number'); ?>
</div>

<div class="col-md-12">
  <hr>
</div>
<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>

<?php echo form_close(); ?>