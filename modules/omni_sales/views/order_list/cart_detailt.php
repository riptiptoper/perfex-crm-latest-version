<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
$inv = '';
$inv_id = '';
$hash = '';
if(isset($invoice)){
	$inv_id = $invoice->id;
	$hash = $invoice->hash;
} 
$is_return_order = false;
if(is_numeric($order->original_order_id)){
	$is_return_order = true;
}
?>
<input type="hidden" name="order_id" value="<?php echo html_entity_decode($order->id); ?>">
<div id="wrapper">
	<div class="content">

		<div class="panel_s">
			<div class="panel-body">
				<!-- Show Tab if this is return order and approved -->
				<?php if($order->approve_status == 1 && $is_return_order){ ?>
					<div class="horizontal-scrollable-tabs preview-tabs-top">
						<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
						<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
						<div class="horizontal-tabs">
							<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
								<li role="presentation" class="active">
									<a href="#order_detail" aria-controls="order_detail" role="tab" data-toggle="tab">
										<?php echo _l('omni_order_detail'); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="#refund" aria-controls="refund" role="tab" data-toggle="tab">
										<?php echo _l('omni_refund'); ?>
										<?php $count_refund = omni_count_refund($order->id); 
										if($count_refund > 0){ ?>
											<span class="badge badge-portal bg-warning mleft5">
												<?php echo html_entity_decode($count_refund); ?>
											</span>
										<?php }	?>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane ptop10 active" id="order_detail">
						<?php } ?>
						<div class="row">
							<div class="col-md-12">

								<div class="row">
									<div class="col-md-6">
										<h5><?php echo _l('order_number');  ?>: <?php  echo ( isset($order) ? $order->order_number : ''); ?></h5>
										<?php if(isset($order) && $order->seller > 0){ ?>
											<span class="mright15"><?php echo _l('seller');  ?>: <?php echo get_staff_full_name($order->seller); ?></span><br>
										<?php } ?>
										<span><?php echo _l('order_date');  ?>: <?php  echo ( isset($order) ? $order->datecreator : ''); ?></span><br>
										<?php 
										if(!$is_return_order){
											if(isset($invoice)){ ?>
												<span><?php echo _l('invoice');  ?>: <a href="<?php echo admin_url('invoices#'.$invoice->id) ?>"><?php echo html_entity_decode($order->invoice); ?></a></span><br>
											<?php	}
										}else{ ?>
											<span><?php echo _l('from_order');  ?>: <a href="<?php echo admin_url('omni_sales/view_order_detailt/'.$order->original_order_id) ?>"><?php echo get_order_number($order->original_order_id); ?></a></span><br>
										<?php }	?>
										<input type="hidden" name="order_number" value="<?php echo html_entity_decode($order->order_number); ?>">
										<?php 
										if(isset($order)){
											$payment_method =  $order->payment_method_title;
											if($payment_method == ''){
												$data_multi_payment = $this->omni_sales_model->get_order_multi_payment($order->id);
												if($data_multi_payment){
													foreach ($data_multi_payment as $key => $mtpayment) {
														$payment_method .= $mtpayment['payment_name'].', ';
													}
													$payment_method = rtrim($payment_method, ', ');
												}
												else{
													$this->load->model('payment_modes_model');	
													$data_payment = $this->payment_modes_model->get($order->allowed_payment_modes);
													if($data_payment){
														$name = isset($data_payment->name) ? $data_payment->name : '';
														if($name !=''){
															$payment_method = $name;              
														}            
													}
												}
											}	
											if($payment_method != ''){ ?>
												<span><?php echo _l('payment_method');  ?>: <span class="text-primary"><?php echo html_entity_decode($payment_method); ?></span></span><br>
											<?php }		
										}
										?>

										<?php
										if(isset($order) && $order->estimate_id != null &&  is_numeric($order->estimate_id)){ ?>
											<span><?php echo _l('estimates');  ?>: <a href="<?php echo admin_url('estimates#'.$order->estimate_id) ?>"><?php echo format_estimate_number($order->estimate_id); ?></a></span><br>
										<?php	}
										?>

									</div>
									<div class="col-md-6 status_order">
										<div class="row">
											<?php
											$currency_name = '';
											if(isset($base_currency)){
												$currency_name = $base_currency->name;
											}
											$status = get_status_by_index($order->status);    
											?>


											<div class="col-md-12">
												<div class="reasion pull-left">
													<div class="text-danger">
														<?php 
														if(in_array($order->status, [11,6,12,13])){
															$return_order = get_return_order_of_parent($order->id);
															if($return_order){
																echo _l('omni_the_order_was_returned_for_some_reason').': '.$return_order->return_reason.'<br><a target="_blank" href="'.admin_url('omni_sales/view_order_detailt/'.$return_order->id).'">'._l('omni_view_detail_return_order').'</a>';
															}
														}
														?>
														<?php 
														if($order->status == 8){ 
															if($order->admin_action == 0){
																echo _l('was_canceled_by_you_for_a_reason').': '._l($order->reason); 
															}
															else
															{
																echo _l('was_canceled_by_us_for_a_reason').': '._l($order->reason);  
															} 
														} ?> 
													</div>
												</div>
												<!-- add hook display shipment -->
												<?php hooks()->do_action('omni_order_detail_header', $order); ?>

												<div class="btn-group pull-right">
													<button href="#" class="dropdown-toggle btn" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" >
														<?php echo _l($status); ?>  <span class="caret" data-toggle="" data-placement="top" data-original-title="<?php echo _l('change_status'); ?>"></span>
													</button>
													<ul class="dropdown-menu animated fadeIn">
														<li class="customers-nav-item-edit-profile">
															<?php									
															foreach(omni_status_list($is_return_order) as $item){ ?>
																<?php if(has_permission('omni_order_list', '', 'edit') || is_admin()){ ?>
																	<a href="#" class="change_status" data-status="<?php echo html_entity_decode($item['id']);?>">
																		<?php echo html_entity_decode($item['label']);?>
																	</a> 
																<?php }else{ ?>
																	<a href="#" class="" data-status="<?php echo html_entity_decode($item['id']);?>">
																		<?php echo html_entity_decode($item['label']);?>
																	</a> 
																<?php } ?>
															<?php } ?>
														</li> 
													</ul>
												</div>

											</div>
											<br>
										</div>
									</div>
								</div>


								<div class="clearfix"></div>
								<div class="row">
									<div class="col-md-12">
										<hr>  
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="row">
									<div class="col-md-4">
										<input type="hidden" name="userid" value="<?php echo html_entity_decode($order->userid); ?>">
										<h4 class="no-mtop">
											<i class="fa fa-user"></i>
											<?php echo _l('customer_details'); ?>
										</h4>
										<hr />
										<?php echo (isset($order) ? $order->company : ''); ?><br>
										<?php echo (isset($order) ? $order->phonenumber : ''); ?><br>
										<?php echo (isset($order) ? $order->address : ''); ?><br>
										<?php echo (isset($order) ? $order->city : ''); ?> <?php echo ( isset($order) ? $order->state : ''); ?><br>
										<?php echo isset($order) ? get_country_short_name($order->country) : ''; ?> <?php echo ( isset($order) ? $order->zip : ''); ?><br>
									</div>
									<div class="col-md-4">
										<h4 class="no-mtop">
											<i class="fa fa-map"></i>
											<?php echo _l('billing_address'); ?>
										</h4>
										<hr />
										<address class="invoice-html-customer-shipping-info">
											<?php echo isset($order) ? $order->billing_street : ''; ?>
											<br><?php echo isset($order) ? $order->billing_city : ''; ?> <?php echo isset($order) ? $order->billing_state : ''; ?>
											<br><?php echo isset($order) ? get_country_short_name($order->billing_country) : ''; ?> <?php echo isset($order) ? $order->billing_zip : ''; ?>
										</address>
									</div>
									<div class="col-md-4">
										<h4 class="no-mtop">
											<i class="fa fa-street-view"></i>
											<?php echo _l('shipping_address'); ?>
										</h4>
										<hr />
										<address class="invoice-html-customer-shipping-info">
											<?php echo isset($order) ? $order->shipping_street : ''; ?>
											<br><?php echo isset($order) ? $order->shipping_city : ''; ?> <?php echo isset($order) ? $order->shipping_state : ''; ?>
											<br><?php echo isset($order) ? get_country_short_name($order->shipping_country) : ''; ?> <?php echo isset($order) ? $order->shipping_zip : ''; ?>
										</address>
									</div>
								</div>
							</div>

						</div>


						<div class="row">
							<?php
							$tax_total_array = [];
							$sub_total = 0;
							?>
							<div class="clearfix"></div>
							<br>       
							<div class="invoice accounting-template">
								<div class="row">
								</div>
								<div class="fr1">
									<div class="col-md-8">
									</div>
									<div class="col-md-4">
										<span class="pull-right mbot10 italic"><?php echo _l('omni_currency').': '.$currency_name; ?></span>																
									</div>
									<div class="table-responsive s_table">
										<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
											<thead>
												<tr>
													<th width="55%" align="center"><?php echo _l('invoice_table_item_heading'); ?></th>
													<th width="10%" align="center" class="qty"><?php echo _l('quantity'); ?></th>
													<th width="15%" align="center"  valign="center"><?php echo _l('price'); ?></th>
													<th width="15%" align="center"  valign="center"><?php echo _l('tax'); ?></th>
													<th width="15%" align="center"><?php echo _l('line_total'); ?></th>
												</tr>
											</thead>
											<tbody>
												<?php 
												$sub_total = 0; 
												$date = date('Y-m-d');
												?>

												<?php
												$has_item_tax = false;
												foreach ($order_detait as $key => $item_cart) { 
													if($item_cart['tax']){
														$has_item_tax = true;
													}
													?>
													<tr class="main">
														<td>
															<a href="#">
																<?php 
																$discount_price = 0;
																$discountss = $this->omni_sales_model->check_discount($item_cart['product_id'], $date);
																if($discountss){
																	$discount_percent = $discountss->discount;
																	$discount_price += ($discount_percent * $item_cart['prices']) / 100;
																	$price_after_dc = $item_cart['prices']-(($discount_percent * $item_cart['prices']) / 100);
																	echo form_hidden('discount_price', $discount_price);
																}else{
																	$price_after_dc = $item_cart['prices'];
																}

																?>
																<img class="product pic" src="<?php echo $this->omni_sales_model->get_image_items($item_cart['product_id']); ?>">  
																<strong>
																	<?php   
																	echo html_entity_decode($item_cart['product_name']);
																	?>
																</strong>
															</a>
														</td>
														<td align="center" class="middle">
															<?php echo html_entity_decode($item_cart['quantity']); ?>
														</td>
														<td align="center" class="middle">
															<?php if($discountss){ ?>
																<strong><?php 
																echo app_format_money($price_after_dc,'');
															?></strong>
															<p class="price">
																<span class="old-price"><?php echo app_format_money($item_cart['prices'], ''); ?></span>&nbsp;  
															</p>
														<?php }else{ ?>
															<strong><?php 
															echo app_format_money($price_after_dc,'');
														?></strong>
													<?php } ?>
												</td>
												<td align="center" class="middle">
													<?php 
													if($item_cart['tax']){
														$list_tax = json_decode($item_cart['tax']);
														$tax_name = '';
														foreach ($list_tax as $tax_item) {
															$tax_name .= $tax_item->name.' ('.$tax_item->rate.'%)<br>'; 
															$array_tax_index = $tax_item->rate.'_'.$tax_item->id;
															if(isset($tax_total_array[$array_tax_index])){
																$old_value_tax = $tax_total_array[$array_tax_index]['value'];
																$tax_total_array[$array_tax_index] = ['value' => ($old_value_tax + $tax_item->value), 'name' => $tax_item->name.' ('.$tax_item->rate.'%)'];
															}
															else{
																$tax_total_array[$array_tax_index] = ['value' => $tax_item->value, 'name' => $tax_item->name.' ('.$tax_item->rate.'%)'];
															}
														}
														echo html_entity_decode($tax_name);														
													}
													?>
												</td>
												<td align="center" class="middle">
													<strong class="line_total_<?php echo html_entity_decode($key); ?>">
														<?php
														$line_total = (int)$item_cart['quantity']*$item_cart['prices'];
														$sub_total += $line_total;
														echo app_format_money($line_total,''); ?>
													</strong>

												</td>
											</tr>
										<?php     } ?>
									</tbody>
								</table>
							</div>

							<div class="col-md-8 col-md-offset-4">
								<table class="table text-right">
									<tbody>
										<tr id="subtotal">
											<td width="50%"><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
											</td>
											<td class="subtotal_s" width="50%">
												<?php
												$sub_total = 0;
												if($order->sub_total){
													$sub_total = $order->sub_total;
												}
												echo app_format_money($sub_total,''); ?>
											</td>
										</tr>
										<?php if($order->discount){
											if($order->discount>0){ ?>
												<tr>
													<td><span class="bold"><?php echo _l('discount'); ?> :</span>
													</td>
													<td>
														<?php

														$price_discount = $order->sub_total * $order->discount/100;
														echo '-'.app_format_money($order->discount,''); ?>
													</td>
												</tr>
											<?php }	} ?>

											<?php if($order->channel == 'manual'){ ?>
												<?php if(is_sale_discount_applied($order)){ ?>
													<tr>
														<td>
															<span class="bold"><?php echo _l('invoice_discount').' :'; ?>
															<?php if(is_sale_discount($order,'percent')){ ?>
																(<?php echo app_format_number($order->discount_percent,true); ?>%)
																<?php } ?></span>
															</td>
															<td class="discount">
																<?php echo '-' . app_format_money($order->discount_total, ''); ?>
															</td>
														</tr>
													<?php } ?>
												<?php } ?>

												<?php if(is_numeric($order->estimate_id) && $order->estimate_id != 0){ ?>
													<?php if(isset($order) && $tax_data['html_currency'] != ''){
														echo html_entity_decode($tax_data['html_currency']);
													} ?>
												<?php }else{ ?>
													<?php foreach ($tax_total_array as $tax_item_row) {
														?>
														<tr>
															<td><span class="bold"><?php echo html_entity_decode($tax_item_row['name']); ?> :</span>
															</td>
															<td>
																<?php echo app_format_money($tax_item_row['value'],''); ?>
															</td>
														</tr>
														<?php 
													}
													?>
												<?php } ?>

												<?php if((int)$order->adjustment != 0){ ?>
													<tr>
														<td>
															<span class="bold"><?php echo _l('invoice_adjustment').' :'; ?></span>
														</td>
														<td class="adjustment_t">
															<?php echo app_format_money($order->adjustment, ''); ?>
														</td>
													</tr>
												<?php } ?>

												<?php 
												if(isset($order->shipping)){
													if($order->shipping != "0.00"){ ?>
														<tr>
															<td><span class="bold"><?php echo _l('shipping'); ?> :</span>
															</td>
															<td>
																<?php echo app_format_money($order->shipping,''); ?>
															</td>
														</tr>
													<?php 	}
												}
												?>
												<?php 
												if(isset($order->shipping_tax)){
													if($order->shipping != "0.00"){ ?>
														<tr>
															<td><span class="bold"><?php echo _l('shipping_tax'); ?> :</span>
															</td>
															<td>
																<?php echo app_format_money($order->shipping_tax,''); ?>
															</td>
														</tr>
													<?php 	}
												}
												?>
												<?php 
												if(!$has_item_tax){ ?>
													<tr>
														<td><span class="bold"><?php echo _l('tax'); ?> :</span>
														</td>
														<td>
															<?php echo app_format_money($order->tax,''); ?>
														</td>
													</tr>
												<?php } 
												if(is_numeric($order->fee_for_return_order) && $order->fee_for_return_order > 0){ ?>
													<tr id="fee_for_return_order">
														<td><span class="bold"><?php echo _l('omni_fee_for_return_order'); ?> :</span>
														</td>
														<td>
															<?php echo app_format_money($order->fee_for_return_order, ''); ?>
														</td>
													</tr>
												<?php } ?>
												<tr>
													<td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
													</td>
													<td class="total_s">			                              	
														<?php
														$total_s = $order->total;
														echo app_format_money($total_s,''); 
														?>
													</td>
												</tr>
												<?php 
												  $invoice_id = '';
												  if($order->number_invoice != ''){
												  	$this->load->model('omni_sales/omni_sales_model');
												    $invoice_id = $this->omni_sales_model->get_id_invoice($order->number_invoice); 
												  }

												  if(is_numeric($invoice_id)){
												  	$this->load->model('invoices_model');
												    $invoice = $this->invoices_model->get($invoice_id);
												    $total_paid = sum_from_table(db_prefix().'invoicepaymentrecords',array('field'=>'amount','where'=>array('invoiceid'=>$invoice->id)));
												  }
												?>

												<?php if(is_numeric($invoice_id)){ ?>
												  <tr>
												    <td><span class="bold"><?php echo _l('invoice_total_paid'); ?></span></td>
												    <td>
												      <?php echo app_format_money($total_paid, $invoice->currency_name); ?>
												    </td>
												  </tr>

												  <tr>
												      <td><span class="<?php if($invoice->total_left_to_pay > 0){echo 'text-danger ';} ?>bold"><?php echo _l('invoice_amount_due'); ?></span></td>
												      <td>
												         <span class="<?php if($invoice->total_left_to_pay > 0){echo 'text-danger';} ?>">
												            <?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency_name); ?>
												         </span>
												      </td>
												   </tr>
												<?php } ?>

												<?php
												$total_refund = omni_get_total_refund($order->id);
												if($is_return_order){ ?>
													<tr id="total_refund">
														<td><span class="bold"><?php echo _l('omni_total_refund'); ?> :</span>
														</td>
														<td>
															<?php 
															echo app_format_money($total_refund, '');
															?>
														</td>
													</tr>
													<tr id="total_refund">
														<td><span class="bold"><?php echo _l('omni_amount_due'); ?> :</span>
														</td>
														<td>
															<?php 
															$amount_due_s = $total_s - $total_refund;
															if($amount_due_s < 0){
																$amount_due_s = 0;
															}
															echo app_format_money($amount_due_s, '');
															?>
														</td>
													</tr>
												<?php } ?>

												<?php if($order->notes != ''){ ?>
													<tr>
														<td><span class="bold"><?php echo _l('note'); ?> :</span></td>
														<td><?php echo html_entity_decode($order->notes); ?></td>
													</tr>
												<?php } ?>
												<?php if($order->duedate != '' && $order->channel_id == 6){ ?>
													<tr>
														<td><span class="bold"><?php echo _l('omni_expiration_date'); ?> :</span></td>
														<td><?php echo _d($order->duedate); ?></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>		             

								</div>
								<div class="col-md-12">
									<div class="panel-body bottom-transaction">
										<a href="<?php echo admin_url('omni_sales/order_list'); ?>" class="btn btn-default"><?php echo _l('close'); ?></a>
										<?php
										if(!$is_return_order){
											if($order->number_invoice == ""){ ?>
												<?php if(has_permission('omni_order_list', '', 'create') || has_permission('omni_order_list', '', 'edit')){ ?>
													<a href="<?php echo admin_url('omni_sales/create_invoice_detail_order/'.$id); ?>" class="btn btn-primary pull-right">
														<?php echo _l('create_invoice'); ?>
													</a>
												<?php } ?>
											<?php }else{ ?>
												<a href="<?php echo admin_url('invoices#'.$invoice->id); ?>" class="btn pull-right"><?php echo _l('view_invoice'); ?></a>
											<?php }} ?>


											<?php 
											if($order->approve_status == 1 && $is_return_order){ 

												$wh_order_return_data = $this->omni_sales_model->get_order_return_by_rel_id($order->original_order_id);
												$original_cart_data = $this->omni_sales_model->get_cart($order->original_order_id);
												if($wh_order_return_data && $order->process_invoice == 'off' && $original_cart_data->invoice != ''){
													if($wh_order_return_data->return_type == 'partially'){ ?>
														<?php if(has_permission('omni_order_list', '', 'edit')){ ?>
															<a href="<?php echo admin_url('omni_sales/update_invoice/'.$id.'/'.$order->original_order_id); ?>" class="btn btn-warning pull-right mright15">
																<?php echo _l('omni_update_invoice'); ?>
															</a>
														<?php } ?>
													<?php }
													else{ ?>
														<?php if(has_permission('omni_order_list', '', 'edit')){ ?>
															<a href="<?php echo admin_url('omni_sales/cancel_invoice/'.$id.'/'.$order->original_order_id); ?>" class="btn btn-warning pull-right mright15">
																<?php echo _l('omni_cancel_invoice'); ?>
															</a>
														<?php } ?>
													<?php }
												}
												if(omni_get_status_modules('warehouse')){ 
													if($order->stock_import_number == 0){ ?>
														<?php if(has_permission('omni_order_list', '', 'create') || has_permission('omni_order_list', '', 'edit')){ ?>
															<button class="btn btn-success create_import_stock pull-right mright15">
																<?php echo _l('create_import_stock'); ?>													
															</button>
														<?php } ?>
													<?php }else{ ?>
														<a href="<?php echo admin_url('warehouse/manage_purchase#'.$order->stock_import_number); ?>" class="btn pull-right"><?php echo _l('omni_view_import_stock'); ?></a>
													<?php } ?>
												<?php }
											} ?>

											<?php 
											if(!$is_return_order){ 
												if (($order->stock_export_number == '' || !$goods_delivery_exist) && $order->number_invoice != '') { 
													if(omni_get_status_modules('warehouse')){ ?>
														<?php if(has_permission('omni_order_list', '', 'create') || has_permission('omni_order_list', '', 'edit')){ ?>								
															<a href="javascript:void(0)" onclick="create_export_stock(<?php echo html_entity_decode($id); ?>); return false;" class="btn btn-warning pull-right mright15">
																<?php echo _l('create_export_stock'); ?>
															</a>
															<!-- <a href="<?php echo admin_url('omni_sales/create_export_stock/'.$id); ?>" class="btn btn-warning pull-right mright15">
																<?php echo _l('create_export_stock'); ?>
															</a>
															 -->
														<?php } ?>
													<?php } ?>
												<?php }	else if($order->stock_export_number !=''){ ?>
													<a href="<?php echo admin_url('warehouse/manage_delivery#'.$order->stock_export_number); ?>" class="btn pull-right"><?php echo _l('view_export_stock'); ?></a>
												<?php } ?>	
											<?php } ?>	




											<?php if($order->channel_id == 6 && !$is_return_order){ 
												if(omni_get_status_modules('purchase') == true){ 
													if(omni_get_status_modules('warehouse') == true){ 
														?>
														<button class="btn btn-danger pull-right mright15 inventory_check" onclick="inventory_check('<?php echo html_entity_decode($order->order_number) ?>')">
															<?php 	echo _l('omni_inventory_check'); ?>
														</button>
														<?php 
													}
												}
												if($order->status == 0){ ?>
													<div class="pull-right">
														<?php echo form_open(admin_url('omni_sales/pre_order_hand_over'),array('id'=>'form_pre_order_hand_over')); ?>	            
														<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
														<button class="btn btn-success pull-right mright15">
															<?php echo _l('omni_hand_over'); ?>
														</button>
														<div class="pull-right mright15">
															<div class="form-group hanover_option no-mbot">
																<select class="selectpicker display-block" required data-width="100%" name="seller" data-none-selected-text="<?php echo _l('staff'); ?>" data-live-search="true">
																	<option value=""></option>
																	<?php foreach ($staffs as $key => $value) { ?>
																		<option value="<?php echo html_entity_decode($value['staffid']); ?>"><?php echo html_entity_decode($value['firstname'].' '.$value['lastname']); ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<?php echo form_close(); ?>	 
													</div>
												<?php } } ?>

											</div>
										</div>

										<?php if($order->approve_status == 0 && $is_return_order){ ?>
											<div class="col-md-6"></div>
											<div class="col-md-6">
												<div class="reasion mtop25">
													<div class="text-info">
														<?php 
														if($is_return_order && $order->return_reason != ''){ 
															echo _l('omni_the_order_was_returned_for_some_reason').': '.$order->return_reason;
														} ?> 
													</div>
													<div class="text-danger mtop25">
														<div class="pull-right row">
															<?php if(has_permission('omni_order_list', '', 'edit') || is_admin()){ ?>
																<div class="col-md-6">
																	<button class="btn btn-success" onclick="approve_return_order(1)"><?php echo _l('omni_accept'); ?></button>
																</div>
																<div class="col-md-6">
																	<button class="btn btn-warning" onclick="approve_return_order(-1)"><?php echo _l('omni_reject'); ?></button>
																</div>
															<?php } ?>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>

									</div>
								</div>  



								<?php if($order->approve_status == 1 && $is_return_order){ ?>
								</div>
								<div role="tabpanel" class="tab-pane ptop10" id="refund">
									<?php $this->load->view('order_list/refund'); ?>
								</div>
							</div>
						<?php } ?>             
					</div>
				</div>

				<div class="modal fade" id="chosse" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">
									<span class="add-title"><?php echo _l('please_let_us_know_the_reason_for_canceling_the_order') ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<div class="col-md-12">
									<?php echo render_textarea('cancel_reason','cancel_reason',''); ?>
								</div>
							</div>
							<div class="clearfix">               
								<br>
								<br>
								<div class="clearfix">               
								</div>
								<div class="modal-footer">
									<button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
									<button type="button" data-status="8" class="btn btn-danger cancell_order"><?php echo _l('cancell'); ?></button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</div><!-- /.modal -->

				<div class="modal fade" id="inventory_check" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">
									<span class="add-title"><?php echo _l('omni_inventory_check') ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<table class="table inventory_check_table">
									<thead>
										<tr>
											<th scope="col"></th>
											<th scope="col"><?php echo _l('omni_item'); ?></th>
											<th scope="col"><?php echo _l('omni_quantity'); ?></th>
											<th scope="col"><?php echo _l('omni_quantity_in_stock'); ?></th>
											<th scope="col"><?php echo _l('omni_difference'); ?></th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<div class="clearfix">               
								<br>
								<br>
								<div class="clearfix">               
								</div>
								<div class="modal-footer">
									<?php echo form_open(admin_url('omni_sales/create_purchase_request'),array('id'=>'form_create_purchase_request')); ?>	            
									<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
									<button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
									<button type="submit" class="btn btn-danger" ><?php echo _l('omni_create_purchase_request'); ?></button>
									<?php echo form_close(); ?>	 
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</div><!-- /.modal -->

				<div class="modal fade" id="reject_reason" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">
									<span class="add-title"><?php echo _l('omni_please_enter_the_reason_for_the_refusal') ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<div class="col-md-12">
									<?php echo render_textarea('return_reason','',''); ?>
								</div>
							</div>
							<div class="clearfix">               
								<br>
								<br>
								<div class="clearfix">               
								</div>
								<div class="modal-footer">
									<button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
									<button type="button" data-status="8" class="btn btn-danger reject_order"><?php echo _l('omni_reject'); ?></button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</div><!-- /.modal -->



				<div class="modal fade" id="create_import_stock_modal" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">
									<span class="add-title"><?php echo _l('omni_please_select_a_warehouse') ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<div class="col-md-12">
									<?php echo render_select('warehouse_id', $warehouses, array('warehouse_id', array('warehouse_code', 'warehouse_name'))); ?>
								</div>
							</div>
							<div class="clearfix">               
								<br>
								<br>
								<div class="clearfix">               
								</div>
								<div class="modal-footer">
									<button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
									<?php if(has_permission('omni_order_list', '', 'create') || has_permission('omni_order_list', '', 'edit') || is_admin()){ ?>
										<button type="button" data-status="8" class="btn btn-danger create_import_stock_btn"><?php echo _l('create_import_stock'); ?></button>
									<?php } ?>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</div><!-- /.modal -->

				<input type="hidden" name="goods_delivery_id" value="<?php echo html_entity_decode($order->stock_export_number) ?>">
				<input type="hidden" name="are_you_sure_you_want_to_accept_returns" value="<?php echo _l('omni_are_you_sure_you_want_to_accept_returns'); ?>">
				<input type="hidden" name="please_select_a_warehouse" value="<?php echo _l('omni_please_select_a_warehouse'); ?>">


				<?php init_tail(); ?>
				<?php require 'modules/omni_sales/assets/js/order/view_order_detail_js.php';?>

			</body>
			</html>