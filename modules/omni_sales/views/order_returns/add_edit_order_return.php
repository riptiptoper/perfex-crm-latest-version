<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12" id="small-table">
				<div class="panel">
					<?php echo form_open_multipart(admin_url('omni_sales/order_return'), array('id'=>'add_edit_order_return')); ?>
					<div class="panel-body">
						<input type="hidden" name="currency" value="<?php echo html_entity_decode($currency->id); ?>">

						<div class="row">
							<div class="col-md-12">
								<h4 class="no-margin font-bold "><i class="fa fa-inbox" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
								<hr>
							</div>
						</div>

						<?php 
						$id = '';
						$rel_type = $order_return_type;
						$additional_discount = 0;
						if(isset($order_return)){
							$id = $order_return->id;
							echo form_hidden('isedit');
							$additional_discount = $order_return->additional_discount;
							$rel_type = $order_return->rel_type;
						}
						?>
						<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
						<input type="hidden" name="save_and_send_request" value="false">
						<input type="hidden" name="rel_type" value="<?php echo html_entity_decode($rel_type); ?>">
						<input type="hidden" name="main_additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">
						<input type="hidden" name="fee_for_return_order" value="<?php echo get_option('omni_fee_for_return_order'); ?>">
						<?php 
						$input_number_attr = ['min' => '0.00', 'step' => 'any'];
						$volume_attr = ['min' => '0.00', 'step' => 'any', 'readonly' => true];
						$order_return_code = isset($order_return)? $order_return->order_return_number : (isset($goods_code) ? $goods_code : '');
						$company_id = isset($order_return)? $order_return->company_id : '';
						$rel_id = isset($order_return)? $order_return->rel_id : '';
						$admin_note = isset($order_return)? $order_return->admin_note : '';
						$wh_return_policies_information = isset($order_return)? $order_return->return_policies_information : '';
						$email = isset($order_return)? $order_return->email : '';
						$phonenumber = isset($order_return)? $order_return->phonenumber : '';
						$order_number = isset($order_return)? $order_return->order_number : '';
						$order_date = isset($order_return)? _dt($order_return->order_date) : _dt(date("Y-m-d H:i:s"));
						$number_of_item = isset($order_return)? $order_return->number_of_item : 0;
						$order_total = isset($order_return)? $order_return->order_total : 0;
						$datecreated = isset($order_return)? _dt($order_return->datecreated) : _dt(date("Y-m-d H:i:s"));
						$return_type = isset($order_return)? $order_return->return_type : 'partially';

						$rel_id_lable = _l('omni_sales_order');
						$rel_id_data = $this->omni_sales_model->get_omni_sale_order_list();
						$company_id_lable = _l('omni_customer');
						$company_id_data = $this->clients_model->get();
						$rate_label = _l('rate');
						$main_item_select_hide = 'hide';
						$return_reason = (isset($order_return) ? $order_return->return_reason : '');
						?>

						<!-- start -->
						<div class="row" >
							<div class="col-md-6">

								<div class="row">
									<div class="col-md-6">
										<label for="number">
											<?php echo _l('omni_order_return_number'); ?>
										</label>
										<input type="text" readonly name="order_return_name" class="form-control" value="<?php echo get_option('omni_return_order_prefix').$this->omni_sales_model->incrementalHash(); ?>" >
									</div>
									<div class="col-md-6">
										<?php echo render_select('rel_id', $rel_id_data, array('id', array('goods_delivery_code')), $rel_id_lable, $rel_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
									</div>
								</div>



								<div class="row">
									<div class="col-md-6">
										<?php echo render_select('company_id', $company_id_data, array('userid', array('company')), $company_id_lable, $company_id, ['data-width' => '100%', 'class' => 'selectpicker', 'data-live-search' => "true"], array(), '', '', true); ?>
									</div>
									<div class="col-md-6">
										<?php echo render_input('email','email',$email, 'text') ?>
									</div>
								</div>							

							</div>

							<div class="col-md-6">
								<div class="row">
									<div class="col-md-6">
										<?php echo render_input('phonenumber','phonenumber',$phonenumber, 'text') ?>
									</div>
									<div class="col-md-6">
										<?php echo render_datetime_input('order_date','order_date',$order_date) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<?php echo render_datetime_input('datecreated','date_created',$datecreated) ?>
									</div>
									<div class="col-md-6">
										<?php 
										$return_type_data = [];
										$return_type_data[] = [
											'id' => 'partially',
											'label' => _l('omni_partially'),
										];
										$return_type_data[] = [
											'id' => 'fully',
											'label' => _l('omni_fully'),
										];
										?>
										<?php echo render_select('return_type',$return_type_data,array('id', 'label'), 'omni_return_type', $return_type, [], [], '' , '', false) ?>
									</div>
									
								</div>
								
							</div>

						</div>

					</div>

					<div class="panel-body mtop10 invoice-item">
						<div class="table-responsive s_table ">
							<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
								<thead>
									<tr>
										<th width="3%"></th>
										<th align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
										<th width="7%" align="right" class="qty"><?php echo _l('omni_quantity'); ?></th>
										<th width="10%" align="right"><?php echo html_entity_decode($rate_label); ?></th>
										<th width="10%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
										<th width="10%" align="right"><?php echo _l('subtotal'); ?></th>
										<th width="10%" class="hide" align="right"><?php echo _l('discount'); ?></th>
										<th width="10%" align="right"><?php echo _l('discount(money)'); ?></th>
										<th width="10%" align="right"><?php echo _l('omni_total_money'); ?></th>
										<th width="3%" align="center" class="remove-control"><i class="fa fa-cog"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php echo html_entity_decode($order_return_row_template); ?>
								</tbody>
							</table>
						</div>
						<input type="hidden" name="totaltax" value="">
						<div class="col-md-8 col-md-offset-4">
							<table class="table text-right">
								<tbody>
									<tr id="subtotal">
										<td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
										</td>
										<td class="wh-subtotal">
										</td>
									</tr>
									<tr id="total_discount">
										<td><span class="bold"><?php echo _l('omni_total_discount'); ?> :</span>
										</td>
										<td class="wh-total_discount">
										</td>
									</tr>
									<?php 
									if(get_option('omni_fee_for_return_order') > 0){ ?>
										<tr id="fee_for_return_order">
											<td><span class="bold"><?php echo _l('omni_fee_for_return_order'); ?> :</span>
											</td>
											<td class="wh-fee_for_return_order">
											</td>
										</tr>
									<?php } ?>

									<tr id="totalmoney">
										<td><span class="bold"><?php echo _l('omni_total_money'); ?> :</span>
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
							<div class="panel-body bottom-transaction">

								<?php echo render_textarea('return_reason','omni_return_reason',$return_reason,array(),array(),'mtop15'); ?>
								<?php echo render_textarea('admin_note','admin_note',$admin_note,array(),array(),'mtop15'); ?>

								<div class=" row ">
									<div class="col-md-12">
										<?php 
										$return_policy = get_option('omni_return_policies_information');
										if($return_policy != ''){ ?>
											<label><strong><?php echo _l('omni_return_policies_information'); ?></strong></label>
											<h5><?php echo html_entity_decode($return_policy) ; ?></h5>
										<?php } ?>
									</div>
								</div>

								<div class="btn-bottom-toolbar text-right">
									<a href="<?php echo admin_url('warehouse/manage_order_return'); ?>"class="btn btn-default text-right mright5"><?php echo _l('close'); ?></a>
									<?php if (has_permission('omni_order_list', '', 'create') || has_permission('omni_order_list', '', 'edit') || is_admin()) { ?>
										<a href="javascript:void(0)"class="btn btn-info pull-right add_order_return" ><?php echo _l('save'); ?></a>
									<?php } ?>

								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/omni_sales/assets/js/order_returns/add_edit_order_return_js.php';?>
</body>
</html>



