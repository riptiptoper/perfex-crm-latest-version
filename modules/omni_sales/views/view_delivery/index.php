
<div class="col-md-12 no-padding">
	<div class="panel_s">
		<div class="panel-body">
			<div class="clearfix"></div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
					<div id="estimate-preview">
						<div class="col-md-12">
							<h5>
								<?php echo html_entity_decode($goods_delivery->goods_delivery_code) ?>								
							</h5>
							<hr>
						</div>
						<div class="col-md-12 row-margin">
							<table class="table border table-striped table-margintop ">
								<tbody>
									<?php 
									$customer_name='';
									if($goods_delivery){
										if(is_numeric($goods_delivery->customer_code)){
											$customer_value = $this->clients_model->get($goods_delivery->customer_code);
											if($customer_value){
												$customer_name .= $customer_value->company;
											}
										}
									}
									?>
									<tr class="project-overview">
										<td class="bold" width="30%"><?php echo _l('customer_name'); ?></td>
										<td><?php echo html_entity_decode($customer_name) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('to'); ?></td>
										<td><?php echo html_entity_decode($goods_delivery->to_) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('address'); ?></td>
										<td><?php echo html_entity_decode($goods_delivery->address) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo _l('note_'); ?></td>
										<td><?php echo html_entity_decode($goods_delivery->description) ; ?></td>
									</tr>
									<?php 
									if(($goods_delivery->invoice_id != '') && ($goods_delivery->invoice_id != 0) ){


									 ?>
										<tr class="project-overview">
											<td class="bold"><?php echo _l('invoices'); ?></td>
											<td>
												<?php  if(!is_staff_logged_in()){ ?>
													<a href="<?php echo site_url('invoice/'.$goods_delivery->invoice_id.'/'.$goods_delivery->hash) ?>" ><?php echo format_invoice_number($goods_delivery->invoice_id).get_invoice_company_projecy($goods_delivery->invoice_id) ?></a> 															
												<?php } else { ?>
													<a href="<?php echo admin_url('invoices#'.$goods_delivery->invoice_id) ?>" ><?php echo format_invoice_number($goods_delivery->invoice_id).get_invoice_company_projecy($goods_delivery->invoice_id) ?></a>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table items items-preview estimate-items-preview" data-type="estimate">
										<thead>
											<tr>
												<th align="center">#</th>
												<th  colspan="1"><?php echo _l('commodity_code') ?></th>
												<th colspan="1"><?php echo _l('warehouse_name') ?></th>
												<!-- <th colspan="1"><?php echo _l('available_quantity') ?></th> -->
												<th  colspan="1"><?php echo _l('unit_name') ?></th>
												<th  colspan="1" class="text-center"><?php echo _l('quantity') ?></th>
												<th align="right" colspan="1"><?php echo _l('rate') ?></th>
												<th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
												<th align="right" colspan="1"><?php echo _l('subtotal_after_tax') ?></th>
												<th align="right" colspan="1"><?php echo _l('discount').'(%)' ?></th>
												<th align="right" colspan="1"><?php echo _l('discount(money)') ?></th>
												<th align="right" colspan="1"><?php echo _l('lot_number').'/'._l('quantity') ?></th>
												<th align="right" colspan="1"><?php echo _l('total_money') ?></th>
												<th align="right" colspan="1"><?php echo _l('guarantee_period') ?></th>

											</tr>
										</thead>
										<tbody class="ui-sortable">
											<?php 
											$subtotal = 0 ;
											foreach ($goods_delivery_detail as $delivery => $delivery_value) {
												$delivery++;
												$available_quantity = (isset($delivery_value) ? $delivery_value['available_quantity'] : '');
												$total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
												$discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
												$discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');
												$guarantee_period = (isset($delivery_value) ? _d($delivery_value['guarantee_period']) : '');

												$quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
												$unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');
												$total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');

												$commodity_code = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';
												$commodity_name = get_commodity_name($delivery_value['commodity_code']) != null ? get_commodity_name($delivery_value['commodity_code'])->description : '';
												$subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
												$item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];



												$warehouse_name ='';

												if(isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] !='')){
													$arr_warehouse = explode(',', $delivery_value['warehouse_id']);

													$str = '';
													if(count($arr_warehouse) > 0){

														foreach ($arr_warehouse as $wh_key => $warehouseid) {
															$str = '';
															if ($warehouseid != '' && $warehouseid != '0') {

																$team = get_warehouse_name($warehouseid);
																if($team){
																	$value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

																	$str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';

																	$warehouse_name .= $str;
																	if($wh_key%3 ==0){
																		$warehouse_name .='<br/>';
																	}
																}

															}
														}

													} else {
														$warehouse_name = '';
													}
												}



												$unit_name = '';
												if(is_numeric($delivery_value['unit_id'])){
													$unit_name = get_unit_type($delivery_value['unit_id']) != null ? get_unit_type($delivery_value['unit_id'])->unit_name : '';
												}

												$lot_number ='';
												if(($delivery_value['lot_number'] != null) && ( $delivery_value['lot_number'] != '') ){
													$array_lot_number = explode(',', $delivery_value['lot_number']);
													foreach ($array_lot_number as $key => $lot_value) {

														if($key%2 ==0){
															$lot_number .= $lot_value;
														}else{
															$lot_number .= ' : '.$lot_value.' ';
														}

													}
												}

												$commodity_name = $delivery_value['commodity_name'];
												if(strlen($commodity_name) == 0){
													$commodity_name = wh_get_item_variatiom($delivery_value['commodity_code']);
												}

												?>

												<tr>
													<td ><?php echo html_entity_decode($delivery) ?></td>
													<td ><?php echo html_entity_decode($commodity_name) ?></td>
													<td ><?php echo html_entity_decode($warehouse_name) ?></td>
													<!-- <td ><?php echo html_entity_decode($available_quantity) ?></td> -->
													<td ><?php echo html_entity_decode($unit_name) ?></td>
													<td class="text-right"><?php echo html_entity_decode($quantities) ?></td>
													<td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$item_subtotal,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$total_money,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount,'') ?></td>
													<td class="text-right"><?php echo app_format_money((float)$discount_money,'') ?></td>
													<td class="text-right"><?php echo html_entity_decode($lot_number) ?></td>
													<td class="text-right"><?php echo app_format_money((float)$total_after_discount,'') ?></td>
													<td class="text-right"><?php echo html_entity_decode($guarantee_period) ?></td>
												</tr>
											<?php  } ?>
										</tbody>
									</table>

									<div class="col-md-8 col-md-offset-4">
										<table class="table text-right">
											<tbody>
												<tr id="subtotal">
													<td class="bold"><?php echo _l('subtotal'); ?></td>
													<td><?php echo app_format_money((float)$subtotal, $base_currency); ?></td>
												</tr>
												<?php if(isset($goods_delivery) && $tax_data['html_currency'] != ''){
													echo html_entity_decode($tax_data['html_currency']);
												} ?>
												<tr id="total_discount">
													<?php
													$total_discount = 0 ;
													if(isset($goods_delivery)){
														$total_discount += (float)$goods_delivery->total_discount  + (float)$goods_delivery->additional_discount;
													}
													?>
													<td class="bold"><?php echo _l('total_discount'); ?></td>
													<td><?php echo app_format_money((float)$total_discount, $base_currency); ?></td>
												</tr>
												<tr id="totalmoney">
													<?php
													$after_discount = isset($goods_delivery) ?  $goods_delivery->after_discount : 0 ;
													if($goods_delivery->after_discount == null){
														$after_discount = $goods_delivery->total_money;
													}
													?>
													<td class="bold"><?php echo _l('total_money'); ?></td>
													<td><?php echo app_format_money((float)$after_discount, $base_currency); ?></td>
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

																<?php if (file_exists(WAREHOUSE_STOCK_EXPORT_MODULE_UPLOAD_FOLDER . $goods_delivery->id . '/signature_'.$value['id'].'.png') ){ ?>

																	<img src="<?php echo site_url('modules/warehouse/uploads/stock_export/'.$goods_delivery->id.'/signature_'.$value['id'].'.png'); ?>" class="img-width-height">

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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
