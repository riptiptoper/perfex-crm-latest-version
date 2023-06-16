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

?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<div class="col-md-6">
					<h5><?php echo _l('order_number');  ?>: #<?php  echo ( isset($order) ? $order->order_number : ''); ?></h5>
					<span><?php echo _l('order_date');  ?>: <?php  echo ( isset($order) ? $order->datecreator : ''); ?></span>
					<?php if(isset($invoice)){ ?>
						<h4><?php echo _l('invoice');  ?>: <a href="<?php echo admin_url('invoices#'.$invoice->id) ?>"><?php echo html_entity_decode($order->invoice); ?></a></h4>
						
					<?php	} ?>
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
							<h5><?php echo _l('payment_method');  ?>: <span class="text-primary"><?php echo html_entity_decode($payment_method); ?></span></h5>
						<?php }		
					}
					?>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-12">
					<hr>  
				</div>
				<br>
				<br>
				<br>
				<div class="clearfix"></div>
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
				<div class="row">
					<?php
					$currency_name = '';
					if(isset($base_currency)){
						$currency_name = $base_currency->name;
					}
					$tax_total_array = [];
					$sub_total = 0;
					?>
					<div class="clearfix"></div>
					<br><br>        
					<div class="invoice accounting-template">
						<div class="row">
						</div>
						<div class="fr1">
							<div class="table-responsive s_table">
								<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
									<thead>
										<tr>
											<th width="55%" align="center"><?php echo _l('invoice_table_item_heading'); ?></th>
											<th width="10%" align="center" class="qty"><?php echo _l('quantity'); ?></th>
											<th width="15%" align="center"  valign="center"><?php echo _l('price').' ('.$currency_name.')'; ?></th>
											<th width="15%" align="center"  valign="center"><?php echo _l('tax'); ?></th>
											<th width="15%" align="center"><?php echo _l('line_total').' ('.$currency_name.')'; ?></th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$sub_total = 0; 
										$date = date('Y-m-d');
										?>
										
										<?php foreach ($order_detait as $key => $item_cart) { ?>
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
															$tax_name .= '<br>'.$tax_item->name.' ('.$tax_item->rate.'%)'; 
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
											<td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
											</td>
											<td class="subtotal_s">
												<?php
												$sub_total = 0;
												if($order->sub_total){
													$sub_total = $order->sub_total;
												}
												echo app_format_money($sub_total,'').' '.$currency_name; ?>
											</td>
										</tr>
										<?php if($order->discount){
											if($order->discount>0){
												if($order->discount_type == 1){
													$voucher = '';
													if($order->voucher){
														if($order->voucher!=''){
															$voucher = '<span class="text-danger">'.$order->voucher.'</span>';
														}
													}
													?>
													<tr>
														<td><span class="bold"><?php echo _l('discount').' ('.$voucher.' -'.$order->discount.'%)'; ?> :</span>
														</td>
														<td>
															<?php

															$price_discount = $order->sub_total * $order->discount/100;
															echo '-'.app_format_money($price_discount,''); ?>
														</td>
													</tr>


												<?php  }if($order->discount_type == 2){  ?>
													<tr>
														<td><span class="bold"><?php echo _l('discount'); ?> :</span>
														</td>
														<td>
															<?php
															$price_discount = $order->sub_total - $order->discount;
															echo '-'.app_format_money($order->discount,'').' '.$currency_name; ?>
														</td>
													</tr>
													<?php 
												}
											}
										} ?>

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
															<?php echo '-' . app_format_money($order->discount_total, '').' '.$currency_name; ?>
														</td>
													</tr>
												<?php } ?>
											<?php } ?>

											<?php foreach ($tax_total_array as $tax_item_row) {
												?>
												<tr>
													<td><span class="bold"><?php echo html_entity_decode($tax_item_row['name']); ?> :</span>
													</td>
													<td>
														<?php echo app_format_money($tax_item_row['value'],'').' '.$currency_name; ?>
													</td>
												</tr>
												<?php 
											}
											?>

											<?php if((int)$order->adjustment != 0){ ?>
												<tr>
													<td>
														<span class="bold"><?php echo _l('invoice_adjustment').' :'; ?></span>
													</td>
													<td class="adjustment_t">
														<?php echo app_format_money($order->adjustment, '').' '.$currency_name; ?>
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
															<?php echo app_format_money($order->shipping,'').' '.$currency_name; ?>
														</td>
													</tr>
												<?php 	}
											}
											?>
											<?php 
											if(!$item_cart['tax']){ ?>
												<tr>
													<td><span class="bold"><?php echo _l('tax'); ?> :</span>
													</td>
													<td>
														<?php echo app_format_money($order->tax,'').' '.$currency_name; ?>
													</td>
												</tr>
											<?php } ?>
											<tr>
												<td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
												</td>
												<td class="total_s">			                              	
													<?php echo app_format_money($order->total,'').' '.$currency_name; ?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>		             

							</div>
							<div class="col-md-12 mtop15">
								<div class="row">
									<div class="bottom-transaction">

										<?php echo form_open(admin_url('omni_sales/pre_order_hand_over'),array('id'=>'form_pre_order_hand_over')); ?>	            
										<div class="col-md-6">
											<a href="<?php echo admin_url('omni_sales/pre_order_list'); ?>" class="btn btn-default"><?php echo _l('close'); ?></a>											
										</div>
										<div class="col-md-6">
											<div class="row">
												<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
												<div class="col-md-9">
													<div class="form-group">
														<select class="selectpicker display-block" required data-width="100%" name="seller" data-none-selected-text="<?php echo _l('staff'); ?>" data-live-search="true">
															<option value=""></option>
															<?php foreach ($staffs as $key => $value) { ?>
																<option value="<?php echo html_entity_decode($value['staffid']); ?>"><?php echo html_entity_decode($value['lastname'].' '.$value['firstname']); ?></option>
															<?php } ?>
														</select>
													</div>
												</div>
												<div class="col-md-3">
													<button class="btn btn-primary pull-right">
														<?php echo _l('omni_hand_over'); ?>
													</button>
												</div>
											</div>
										</div>
										<?php echo form_close(); ?>	 


									</div>
									<div class="btn-bottom-pusher"></div>
								</div>     
							</div>
						</div>
					</div>               
				</div>
			</div>


			<?php init_tail(); ?>
		</body>
		</html>