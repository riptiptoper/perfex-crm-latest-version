<?php if(isset($shipment)){ ?>
	<div class="padding-bottom-3x mb-1">
		<div class="card mb-3">
			<div class="p-4 text-center text-white text-lg bg-dark rounded-top"><span class="text-uppercase"><?php echo _l('wh_shipment_number'); ?> - </span><span class="text-medium"><?php echo html_entity_decode($shipment->shipment_number); ?></span></div>
			<div class="d-flex flex-wrap flex-sm-nowrap justify-content-between py-3 px-2 bg-secondary">
				<div class="w-100 text-center py-1 px-2"><span class="text-medium"></span></div>
				<div class="w-100 text-center py-1 px-2"><span class="text-medium">Status: </span><?php echo _l($shipment->shipment_status); ?></div>
				<div class="w-100 text-center py-1 px-2"><span class="text-medium"></span></div>
			</div>

			<div class="card-body">
				<div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
					<div class="step <?php echo html_entity_decode($confirmed_order); ?>">
						<div class="step-icon-wrap">
							<div class="step-icon"><i class="fa fa-cart-arrow-down mtop-18"></i></div>
						</div>
						<h4 class="step-title"><?php echo _l('confirmed_order'); ?></h4>
					</div>
					<div class="step <?php echo html_entity_decode($processing_order); ?>">
						<div class="step-icon-wrap">
							<div class="step-icon"><i class="fa fa-gear mtop-18"></i></div>
						</div>
						<h4 class="step-title"><?php echo _l('processing_order'); ?></h4>
					</div>
					<div class="step <?php echo html_entity_decode($quality_check); ?>">
						<div class="step-icon-wrap">
							<div class="step-icon"><i class="fa fa-edit mtop-18"></i></div>
						</div>
						<h4 class="step-title"><?php echo _l('quality_check'); ?></h4>
					</div>
					<div class="step <?php echo html_entity_decode($product_dispatched); ?>">
						<div class="step-icon-wrap">
							<div class="step-icon"><i class="fa fa-car mtop-18"></i></div>
						</div>
						<h4 class="step-title"><?php echo _l('product_dispatched'); ?></h4>
					</div>
					<div class="step <?php echo html_entity_decode($product_delivered); ?>">
						<div class="step-icon-wrap">
							<div class="step-icon"><i class="fa fa-home mtop-18"></i></div>
						</div>
						<h4 class="step-title"><?php echo _l('product_delivered'); ?></h4>
					</div>
				</div>
			</div>
		</div>

	</div>

<hr class="no-mtop">
<div class="row">
	<div class="col-md-12">

		<div class="row">
			<div class="col-md-6 col-sm-6">
				<h4 class="bold">
					<span id="invoice-number">
						<?php echo html_entity_decode($shipment->shipment_number); ?>
					</span>
				</h4>
				<address>
					<?php echo format_organization_info(); ?>
				</address>
				<?php if(isset($invoice) && $invoice && $invoice->id){ ?>
					<p class="no-mbot">
						<span class="bold">
							<?php echo _l('invoices'); ?>
							<?php 
							if(!is_staff_logged_in()){ ?>
								<a href="<?php echo site_url('sales_agent/purchase_invoice/index/'.$invoice->id.'/'.$invoice->hash) ?>" ><?php echo format_invoice_number($invoice->id); ?></a> 															
							<?php } else { ?>
								<a href="<?php echo admin_url('invoices#'.$invoice->id) ?>" ><?php echo format_invoice_number($invoice->id); ?></a>
							<?php } ?>
						</span>
						<h5 class="bold">
						</h5>
					</p>
				<?php } ?>
			</div>

			<div class="col-md-6 col-sm-6 text-right">
				<span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
				<address class="invoice-html-customer-shipping-info">
					<b><?php echo get_company_name($cart->agent_id); ?></b>
					<br>
					<?php echo isset($invoice) ? $invoice->billing_street : ''; ?>
					<br><?php echo isset($invoice) ? $invoice->billing_city : ''; ?> <?php echo isset($invoice) ? $invoice->billing_state : ''; ?>
					<br><?php echo isset($invoice) ? get_country_short_name($invoice->billing_country) : ''; ?> <?php echo isset($invoice) ? $invoice->billing_zip : ''; ?>
				</address>
				<span class="bold"><?php echo _l('ship_to'); ?>:</span>
				<address class="invoice-html-customer-shipping-info">
					<?php echo isset($invoice) ? $invoice->shipping_street : ''; ?>
					<br><?php echo isset($invoice) ? $invoice->shipping_city : ''; ?> <?php echo isset($invoice) ? $invoice->shipping_state : ''; ?>
					<br><?php echo isset($invoice) ? get_country_short_name($invoice->shipping_country) : ''; ?> <?php echo isset($invoice) ? $invoice->shipping_zip : ''; ?>
				</address>
				<p class="no-mbot">
					<span class="bold">
						<?php echo _l('order_date'); ?>
					</span>
					<?php echo _dt($shipment->datecreated); ?>
				</p>
			</div>
			<?php if(strlen($cart->vendornote) > 0){ ?>
				<div class="col-md-12">
					<p class="no-mbot">
						<span class="bold">
							<?php echo _l('vendor_note'); ?>
						</span>
						<?php echo html_entity_decode($cart->vendornote); ?>
					</p>
				</div>
			<?php } ?>

			<div class="col-md-12">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs  tabs" role="tablist">
					<?php if(isset($goods_delivery) && count($goods_delivery) > 0){ ?>
						<li class="nav-item active">
							<a class="nav-link" data-toggle="tab" href="#delivery_note" role="tab" aria-selected="false"><strong><?php echo _l('goods_delivery'); ?></strong></a>
						</li>
					<?php } ?>
					<?php if(isset($packing_lists) && count($packing_lists) > 0){ ?>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#packing_list" role="tab" aria-selected="false"><strong><?php echo _l('wh_packing_list'); ?></strong></a>
						</li>
					<?php } ?>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content tabs mt-4">
					<?php if(isset($goods_delivery) && count($goods_delivery) > 0){ ?>
						<div role="tabpanel" class="tab-pane active" id="delivery_note">
							<div class="panel_s no-shadow">

								<div class="row">
									<div class="col-md-12">
										<div class="table-responsive">
											<table class="table items items-preview-delivery-note estimate-items-preview" data-type="estimate">
												<thead>
													<tr>
														<th  colspan="1"><?php echo _l('goods_delivery_code') ?></th>
														<th  colspan="1"><?php echo _l('customer_name') ?></th>
														<th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
														<th align="right" colspan="1"><?php echo _l('total_discount') ?></th>
														<th align="right" colspan="1"><?php echo _l('total_money') ?></th>
														<th align="right" colspan="1"><?php echo _l('day_vouchers') ?></th>
														<th align="right" colspan="1"><?php echo _l('staff_id') ?></th>
														<th align="right" colspan="1"><?php echo _l('status_label') ?></th>
														<th align="right" colspan="1"><?php echo _l('delivery_status') ?></th>
													</tr>
												</thead>
												<tbody class="ui-sortable">
													<?php 
													$subtotal = 0 ;
													foreach ($goods_delivery as $key => $delivery_note) {
														$total_discount = 0 ;
														$total_discount += (float)$delivery_note['total_discount']  + (float)$delivery_note['additional_discount'];
														?>

														<tr>
															<td >
																<?php 
																if($delivery_note['staff_id'] == ''){
																	$delivery_note['staff_id'] = 1;
																}
																$profile_url = '';
																$delivery_url = '';
																if(!is_staff_logged_in()){
																	$profile_url = site_url('omni_sales/omni_sales_client/view_staff_profile/' . urlencode(omni_aes_256_encrypt($delivery_note['staff_id'])));

																	$delivery_url = site_url('omni_sales/omni_sales_client/view_delivery_voucher/' .app_generate_hash().'_'.$delivery_note['id']);

																} else {
																	$profile_url = admin_url('staff/profile/' . $delivery_note['staff_id']);
																	$delivery_url = admin_url('warehouse/manage_delivery/' . $delivery_note['id']);
																}	
																?>
																<a href="<?php echo html_entity_decode($delivery_url); ?>" ><?php echo html_entity_decode($delivery_note['goods_delivery_code']) ?></a>
															</td>
															<td ><?php echo get_company_name($delivery_note['customer_code']) ?></td>
															<td class="text-right"><?php echo app_format_money($delivery_note['sub_total'], '') ?></td>
															<td class="text-right"><?php echo app_format_money($total_discount, '') ?></td>
															<td class="text-right"><?php echo app_format_money($delivery_note['after_discount'], '') ?></td>
															<td class="text-right"><?php echo _d($delivery_note['date_add']) ?></td>
															<td class="text-right">

																<?php echo staff_profile_image($delivery_note['staff_id'], [
																	'staff-profile-image-small',
																	]) ?>
																	<?php echo get_staff_full_name($delivery_note['staff_id']) ?>
																</td>
																<?php 
																$approve_data = '';
																if($delivery_note['approval'] == 1){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
																}elseif($delivery_note['approval'] == 0){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
																}elseif($delivery_note['approval'] == -1){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
																}
																?>
																<td class="text-right"><?php echo html_entity_decode($approve_data); ?></td>
																<td class="text-right"><?php echo render_delivery_status_html($delivery_note['id'], 'delivery', $delivery_note['delivery_status'], false); ?></td>
															</tr>
														<?php  } ?>
													</tbody>
												</table>

											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>

						<?php if(isset($packing_lists) && count($packing_lists) > 0){ ?>
							<div role="tabpanel" class="tab-pane" id="packing_list">
								<div class="panel_s no-shadow">

									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table items items-preview estimate-items-preview" data-type="estimate">
													<thead>
														<tr>
															<th  colspan="1"><?php echo _l('packing_list_number') ?></th>
															<th  colspan="1"><?php echo _l('customer_name') ?></th>
															<th align="right" colspan="1"><?php echo _l('wh_dimension') ?></th>
															<th align="right" colspan="1"><?php echo _l('volume_m3_label') ?></th>
															<th align="right" colspan="1"><?php echo _l('total_amount') ?></th>
															<th align="right" colspan="1"><?php echo _l('discount_total') ?></th>
															<th align="right" colspan="1"><?php echo _l('total_after_discount') ?></th>
															<th align="right" colspan="1"><?php echo _l('datecreated') ?></th>
															<th align="right" colspan="1"><?php echo _l('status_label') ?></th>
															<th align="right" colspan="1"><?php echo _l('delivery_status') ?></th>
														</tr>
													</thead>
													<tbody class="ui-sortable">
														<?php 
														$subtotal = 0 ;
														foreach ($packing_lists as $key => $packing_list) {
															$packing_list_url = '';
															if(!is_staff_logged_in()){

																$packing_list_url = site_url('omni_sales/omni_sales_client/view_packing_list/' . app_generate_hash().'_'.$packing_list['id']);
															} else {
																$packing_list_url = admin_url('warehouse/manage_packing_list/' . $packing_list['id']);
															}
															?>
															<tr>
																<td ><a href="<?php echo html_entity_decode($packing_list_url); ?>" ><?php echo html_entity_decode($packing_list['packing_list_number'] .' - '.$packing_list['packing_list_name']) ?></a></td>
																<td ><?php echo get_company_name($packing_list['clientid']) ?></td>
																<td class="text-right"><?php echo html_entity_decode($packing_list['width'].' x '.$packing_list['height'].' x '.$packing_list['lenght']) ?></td>
																<td class="text-right"><?php echo app_format_money($packing_list['volume'], '') ?></td>
																<td class="text-right"><?php echo app_format_money($packing_list['total_amount'], '') ?></td>
																<td class="text-right"><?php echo app_format_money($packing_list['discount_total']+$packing_list['additional_discount'], '') ?></td>
																<td class="text-right"><?php echo app_format_money($packing_list['total_after_discount'], '') ?></td>
																<td class="text-right"><?php echo _dt($packing_list['datecreated']) ?></td>
																<?php 
																$approve_data = '';
																if($packing_list['approval'] == 1){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
																}elseif($packing_list['approval'] == 0){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
																}elseif($packing_list['approval'] == -1){
																	$approve_data = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
																}
																?>
																<td class="text-right"><?php echo html_entity_decode($approve_data); ?></td>
																<td class="text-right"><?php echo render_delivery_status_html($packing_list['id'], 'packing_list', $packing_list['delivery_status'], false) ?></td>
															</tr>
														<?php  } ?>
													</tbody>
												</table>

											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>

			</div>

		</div>

		<div class="col-md-12">
			<div class="activity-feed pr-0">
				<?php $key = 0; ?>
				<?php foreach ($arr_activity_logs as $date => $activity_log) { ?>
					<div class="feed-item ">
						<div class="date <?php if($key == 0){ echo ' text-info';} ?>">
							<div class="row">
								<div class="col-md-8 col-sm-8">
									<span class="text-has-action <?php if($key == 0){ echo ' text-info';} ?>" ><?php echo _dt($activity_log['date']).' '; ?> </span>
									<span class="text-has-action <?php if($key == 0){ echo ' text-info';} ?>" ><?php echo html_entity_decode($activity_log['description']).' '; ?></span>
								</div>
							</div>
						</div>
					</div>
					<?php $key++; ?>
				<?php } ?>

			</div>
		</div>
	</div>
	<?php } ?>



