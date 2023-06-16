<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel_s">
					<div class="panel-body">
						<?php echo form_hidden('delivery_id',$delivery_id); ?>
						<div class="row">
							<div class="col-md-11 ">
								<h4 class="no-margin font-bold"><i class="fa fa-reply-all menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
							</div>
							<div class="col-md-1 pull-right">
								<a href="#" class="btn btn-default pull-right btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view_proposal('.order_return_sm','#order_return_sm_view'); return false;" data-toggle="tooltip" title="<?php echo _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
							</div>
						</div>
						<hr />

						<div class="row">    
							<div class="col-md-3">
								
										<a href="<?php echo admin_url('omni_sales/order_return'); ?>"class="btn btn-info pull-left mright10 ">
											<?php echo _l('add'); ?>
										</a>

							</div>
						</div>
						<br/>
						<div class="row">
							<div class="col-md-2">
								<?php echo render_date_input('from_date', 'from_date', $from_date); ?>
							</div>
							<div class="col-md-2">
								<?php echo render_date_input('to_date', 'to_date', $to_date); ?>
							</div>
							<div class="col-md-3">
								<?php echo render_select('staff_id[]', $staffs, array('staffid', array('full_name')), 'als_staff', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
							</div>
							<div class="col-md-3">
								<?php echo render_select('delivery_id[]', $get_goods_delivery, array('id', array('goods_delivery_code')), 'stock_export', '', ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', false); ?>
							</div>
							<?php 
							$order_return_status = [];
							$order_return_status[] = [
								'id' => 'manual',
								'label' => _l('wh_manual'),
							];
							$order_return_status[] = [
								'id' => 'sales_return_order',
								'label' => _l('sales_return_order'),
							];
							$order_return_status[] = [
								'id' => 'purchasing_return_order',
								'label' => _l('purchasing_return_order'),
							];
							
							 ?>
							<div class="col-md-2">
								<?php echo render_select('rel_type_filter[]', $order_return_status, array('id', array('label')), 'status', $rel_type, ['multiple' => true, 'data-width' => '100%', 'class' => 'selectpicker'], array(), '', '', false); ?>
							</div>
							

						</div>

						<br/>
						<?php render_datatable(array(
							_l('id'),
							_l('order_return_number'),
							_l('customer_name'),
							_l('number_of_item_label'),
							_l('order_total_label'),
							_l('total_amount'),
							_l('discount_total'),
							_l('total_after_discount'),
							_l('datecreated'),
							_l('status_label'),
							// _l('option'),
							
							
						),'table_manage_order_return',['order_return_sm' => 'order_return_sm']); ?>
						
					</div>
				</div>
			</div>
			<div class="col-md-7 small-table-right-col">
				<div id="order_return_sm_view" class="hide">
				</div>
			</div>
			<?php $invoice_value = isset($invoice_id) ? $invoice_id: '' ;?>
			<?php echo form_hidden('invoice_id', $invoice_value) ?>

		</div>
	</div>
</div>

<script>var hidden_columns = [3,4,8];</script>
<?php init_tail(); ?>
<?php require 'modules/omni_sales/assets/js/order_returns/manage_order_return_js.php';?>
</body>
</html>
