<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); 
    ?>
    
<div class="col-md-12 mtop15">
	<div class="row">
		<div class="col-md-12" id="small-table">
			<div class="panel_s">
					<?php echo form_open_multipart(site_url('sales_agent/portal/goods_receipt'), array('id'=>'add_goods_receipt')); ?>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>

						<?php 
						$id = '';
						if(isset($goods_receipt)){
							$id = $goods_receipt->id;
							echo form_hidden('isedit');
						}
						?>

						<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
						<input type="hidden" name="save_and_send_request" value="false">

						<!-- start-->
						<div class="row">
							<div class="col-md-6">
								<?php $goods_receipt_code =isset($goods_receipt) ? $goods_receipt->goods_receipt_code : (isset($goods_code) ? $goods_code : '');?>
								<?php echo render_input('goods_receipt_code', 'stock_received_docket_number',$goods_receipt_code,'',array('disabled' => 'true')) ?>
							</div>
							<div class="col-md-3">
								<?php $date_c =  isset($goods_receipt) ? $goods_receipt->date_c : $current_day?>
								<?php echo sa_render_date_input('date_c','accounting_date', _d($date_c)) ?>
							</div>
							<div class="col-md-3">
								<?php $date_add =  isset($goods_receipt) ? $goods_receipt->date_add : $current_day?>
								<?php echo sa_render_date_input('date_add','day_vouchers', _d($date_add)) ?>
							</div>

							<div class="col-md-6 <?php if($pr_orders_status == false){ echo 'hide';} ;?>" >
								<div class="form-group">
									<label for="pr_order_id"><?php echo _l('reference_purchase_order'); ?></label>
									<select name="pr_order_id" id="pr_order_id" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
										<option value=""></option>
										<?php foreach($pr_orders as $pr_order) { ?>
											<option value="<?php echo html_entity_decode($pr_order['id']); ?>" <?php if(isset($goods_receipt) && ($goods_receipt->pr_order_id == $pr_order['id'])){ echo 'selected' ;} ?>><?php echo html_entity_decode($pr_order['order_number'].' - '.$pr_order['order_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class=" col-md-3">
								<?php $deliver_name = (isset($goods_receipt) ? $goods_receipt->deliver_name : '');
								echo render_input('deliver_name','deliver_name',$deliver_name) ?>
							</div>

							<div class="col-md-3 ">
								<?php $warehouse_id_value = (isset($goods_receipt) ? $goods_receipt->warehouse_id : '');?>
								<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle skucode-tooltip"  data-toggle="tooltip" title="<?php echo _l('goods_receipt_warehouse_tooltip'); ?>" data-original-title="<?php echo _l('goods_receipt_warehouse_tooltip'); ?>"></i></a>
								<?php echo sa_render_select('warehouse_id_m',$warehouses,array('warehouse_id','warehouse_name'),'warehouse_name', $warehouse_id_value); ?>
							</div>

							
						
						</div>
					</div>
					<div class="panel-body mtop10 invoice-item">
						<div class="row">
							<div class="col-md-4">
				            	<?php echo sa_render_select('item_select', $items, array('id', array('commodity_code', 'description')), '', '', ['data-none-selected-text' => _l('select_items')] ); ?>
				          	</div>
							<div class="col-md-8 text-right">
								<label class="bold mtop10 text-right" data-toggle="tooltip" title="<?php echo _l('support_barcode_scanner_tooltip'); ?>" data-original-title="<?php echo _l('support_barcode_scanner_tooltip'); ?>"><?php echo _l('support_barcode_scanner'); ?>
								<i class="fa fa-question-circle i_tooltip"></i></label>
							</div>
						</div>

						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th></th>
										<th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="15%" align="left"><?php echo _l('warehouse_name'); ?></th>
										<th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
										<th width="10%" align="right"><?php echo _l('unit_price'); ?></th>
										<th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th width="10%" align="right"><?php echo _l('lot_number'); ?></th>
										<th width="10%" align="right"><?php echo _l('date_manufacture'); ?></th>
										<th width="10%" align="right"><?php echo _l('expiry_date'); ?></th>
										<th width="10%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>

										<th align="center"><i class="fa fa-cog"></i></th>
										<th align="center"></th>
									</tr>
								</thead>
								<tbody>
									<?php echo html_entity_decode($goods_receipt_row_template); ?>
								</tbody>
							</table>
						</div>
						<div class="col-md-8 col-md-offset-4">
							<table class="table text-right">
								<tbody>
									<tr id="subtotal">
										<td><span class="bold"><?php echo _l('total_goods_money'); ?> :</span>
										</td>
										<td class="wh-subtotal">
										</td>
									</tr>
									<tr id="totalmoney">
										<td><span class="bold"><?php echo _l('total_money'); ?> :</span>
										</td>
										<td class="wh-total">
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div id="removed-items"></div>
					</div>

					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body ">
								<?php $description = (isset($goods_receipt) ? $goods_receipt->description : ''); ?>
								<?php echo render_textarea('description','note',$description,array(),array(),'mtop15'); ?>

								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo site_url('sales_agent/portal/receiving_vouchers'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>

									<a href="javascript:void(0)"class="btn btn-info pull-right mright5 add_goods_receipt" ><?php echo _l('submit'); ?></a>
								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>

 			</div>

 			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<div id="modal_wrapper"></div>

<?php require 'modules/sales_agent/assets/js/portal/goods_receipt/purchase_js.php';?>