
<div class="col-md-12 no-padding">
	<div class="panel_s">
		<div class="panel-body">
			<div class="clearfix"></div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">

					<div id="estimate-preview">
						<div class="row">
							<div class="col-md-6">
								<h4 class="bold">
									<span id="invoice-number">
										<?php echo html_entity_decode($packing_list->packing_list_number .' - '.$packing_list->packing_list_name); ?>
									</span>
								</h4>
								<address>
									<?php echo format_organization_info(); ?>
								</address>
								<p class="no-mbot">
									<span class="bold">
										<?php echo _l('stock_export'); 
										$delivery_url = '';
										if(!is_staff_logged_in()){
											$delivery_url = site_url('omni_sales/omni_sales_client/view_delivery_voucher/' . app_generate_hash().'_'.$packing_list->delivery_note_id);
										} else {
											$delivery_url = admin_url('warehouse/manage_delivery/' . $packing_list->delivery_note_id);
										}	
										?>
										<a href="<?php echo html_entity_decode($delivery_url); ?>" ><?php echo wh_get_delivery_code($packing_list->delivery_note_id); ?></a>
									</span>
									<h5 class="bold">
									</h5>
								</p>
							</div>
							<div class="col-sm-6 text-right">
								<span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
								<address>
									<?php echo public_format_customer_info($packing_list, 'invoice', 'billing', true); ?>
								</address>
								<span class="bold"><?php echo _l('ship_to'); ?>:</span>
								<address>
									<?php echo public_format_customer_info($packing_list, 'invoice', 'shipping'); ?>
								</address>
								<p class="no-mbot">
									<span class="bold">
										<?php echo _l('packing_date'); ?>
									</span>
									<?php echo _d($packing_list->datecreated); ?>
								</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table items items-preview estimate-items-preview" data-type="estimate">
										<thead>
											<tr>
												<th align="center">#</th>
												<th  colspan="1"><?php echo _l('commodity_code') ?></th>
												<th align="right" colspan="1"><?php echo _l('quantity') ?></th>
												<th align="right" colspan="1"><?php echo _l('rate') ?></th>
												<th align="right" colspan="1"><?php echo _l('invoice_table_tax_heading') ?></th>
												<th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
												<th align="right" colspan="1"><?php echo _l('discount').'(%)' ?></th>
												<th align="right" colspan="1"><?php echo _l('discount(money)') ?></th>
												<th align="right" colspan="1"><?php echo _l('total_money') ?></th>
											</tr>
										</thead>
										<tbody class="ui-sortable">
											<?php 
											$subtotal = 0 ;
											foreach ($packing_list_detail as $delivery => $packing_list_value) {
												$delivery++;
												$discount = (isset($packing_list_value) ? $packing_list_value['discount'] : '');
												$discount_money = (isset($packing_list_value) ? $packing_list_value['discount_total'] : '');

												$quantity = (isset($packing_list_value) ? $packing_list_value['quantity'] : '');
												$unit_price = (isset($packing_list_value) ? $packing_list_value['unit_price'] : '');
												$total_after_discount = (isset($packing_list_value) ? $packing_list_value['total_after_discount'] : '');

												$commodity_code = get_commodity_name($packing_list_value['commodity_code']) != null ? get_commodity_name($packing_list_value['commodity_code'])->commodity_code : '';
												$commodity_name = get_commodity_name($packing_list_value['commodity_code']) != null ? get_commodity_name($packing_list_value['commodity_code'])->description : '';

												$unit_name = '';
												if(is_numeric($packing_list_value['unit_id'])){
													$unit_name = get_unit_type($packing_list_value['unit_id']) != null ? ' '.get_unit_type($packing_list_value['unit_id'])->unit_name : '';
												}

												$commodity_name = $packing_list_value['commodity_name'];
												if(strlen($commodity_name) == 0){
													$commodity_name = wh_get_item_variatiom($packing_list_value['commodity_code']);
												}

												?>

												<tr>
													<td ><?php echo html_entity_decode($delivery) ?></td>
													<td ><?php echo html_entity_decode($commodity_name) ?></td>
													<td class="text-right"><?php echo html_entity_decode($quantity).$unit_name ?></td>
													<td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>

													<?php echo  wh_render_taxes_html(wh_convert_item_taxes($packing_list_value['tax_id'], $packing_list_value['tax_rate'], $packing_list_value['tax_name']), 15); ?>
													<td class="text-right"><?php echo app_format_money((float)$packing_list_value['sub_total'],'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount_money,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$total_after_discount,'') ?></td>
												</tr>
											<?php  } ?>
										</tbody>
									</table>

									<div class="col-md-8 col-md-offset-4">
										<table class="table text-right">
											<tbody>
												<tr id="subtotal">
													<td class="bold"><?php echo _l('subtotal'); ?></td>
													<td><?php echo app_format_money((float)$packing_list->subtotal, $base_currency); ?></td>
												</tr>
												<?php if(isset($packing_list) && $tax_data['html_currency'] != ''){
													echo html_entity_decode($tax_data['html_currency']);
												} ?>
												<tr id="total_discount">
													<?php
													$discount_total = 0 ;
													if(isset($packing_list)){
														$discount_total += (float)$packing_list->discount_total  + (float)$packing_list->additional_discount;
													}
													?>
													<td class="bold"><?php echo _l('total_discount'); ?></td>
													<td><?php echo app_format_money((float)$discount_total, $base_currency); ?></td>
												</tr>
												<tr id="totalmoney">
													<?php
													$total_after_discount = isset($packing_list) ?  $packing_list->total_after_discount : 0 ;
													?>
													<td class="bold"><?php echo _l('total_money'); ?></td>
													<td><?php echo app_format_money((float)$total_after_discount, $base_currency); ?></td>
												</tr>
											</tbody>
										</table>
									</div>

								</div>
							</div>

							<div class="col-md-12">
								<div class="project-overview-right">
									<?php if(count($list_approve_status) > 0){ ?>

										<div class="row">
											<div class="col-md-12 project-overview-expenses-finance">
												<div class="col-md-4 text-center">
												</div>
												<?php 
												$this->load->model('staff_model');
												$enter_charge_code = 0;
												foreach ($list_approve_status as $value) {
													$value['staffid'] = explode(', ',$value['staffid']);
													if($value['action'] == 'sign'){
														?>
														<div class="col-md-3 text-center">
															<p class="text-uppercase text-muted no-mtop bold">
																<?php
																$staff_name = '';
																$st = _l('status_0');
																$color = 'warning';
																foreach ($value['staffid'] as $key => $val) {
																	if($staff_name != '')
																	{
																		$staff_name .= ' or ';
																	}
																	$staff_name .= $this->staff_model->get($val)->firstname;
																}
																echo html_entity_decode($staff_name); 
															?></p>
															<?php if($value['approve'] == 1){ 
																?>

																<?php if (file_exists(WAREHOUSE_STOCK_EXPORT_MODULE_UPLOAD_FOLDER . $packing_list->id . '/signature_'.$value['id'].'.png') ){ ?>

																	<img src="<?php echo site_url('modules/warehouse/uploads/stock_export/'.$packing_list->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">

																<?php }else{ ?>
																	<img src="<?php echo site_url('modules/warehouse/uploads/image_not_available.jpg'); ?>" class="img-width-height">
																<?php } ?>


															<?php }
															?> 
															<p class="text-muted no-mtop bold">  
																<?php echo html_entity_decode($value['note']) ?>
															</p>   
														</div>
													<?php }else{ ?>
														<div class="col-md-3 text-center">
															<p class="text-uppercase text-muted no-mtop bold">
																<?php
																$staff_name = '';
																foreach ($value['staffid'] as $key => $val) {
																	if($staff_name != '')
																	{
																		$staff_name .= ' or ';
																	}
																	$staff_name .= $this->staff_model->get($val)->firstname;
																}
																echo html_entity_decode($staff_name); 
															?></p>
															<?php if($value['approve'] == 1){ 
																?>
																<img src="<?php echo site_url('modules/warehouse/uploads/approval/approved.png') ; ?>" class="img-width-height">
															<?php }elseif($value['approve'] == -1){ ?>
																<img src="<?php echo site_url('modules/warehouse/uploads/approval/rejected.png') ; ?>" class="img-width-height">
															<?php }
															?>  

															<p class="text-muted no-mtop bold">  
																<?php echo html_entity_decode($value['note']) ?>
															</p>
														</div>
													<?php }
												} ?>
											</div>
										</div>

									<?php } ?>
								</div>


									</div>                                          

								</div>

								<hr />
								<?php if($packing_list->client_note != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('client_note'); ?></p>
										<p><?php echo html_entity_decode($packing_list->client_note); ?></p>
									</div>
								<?php } ?>
								<?php if($packing_list->admin_note != ''){ ?>
									<div class="col-md-12 row mtop15">
										<p class="bold text-muted"><?php echo _l('admin_note'); ?></p>
										<p><?php echo html_entity_decode($packing_list->admin_note); ?></p>
									</div>
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

