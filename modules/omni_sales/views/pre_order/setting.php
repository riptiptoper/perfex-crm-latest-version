<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<h4><i class="fa fa-object-group"></i> <?php echo html_entity_decode($title) ?></h4>
				<div class="clearfix"></div>
				<br>
				<div class="clearfix"></div>
				<?php 
				$status_pre_order = $this->omni_sales_model->get_sales_channel_by_channel('pre_order')->status;
				if($status_pre_order == 'active'){
					?>
					<div class="horizontal-scrollable-tabs preview-tabs-top">
						<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
						<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
						<div class="horizontal-tabs">
							<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
								<li role="presentation" class="active">
									<a href="#omni_setting_product" aria-controls="omni_setting_product" role="tab" data-toggle="tab">
										<span class="glyphicon glyphicon-align-justify"></span>&nbsp;<?php echo _l('omni_setting_product'); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="#omni_other_setting" aria-controls="omni_other_setting" role="tab" data-toggle="tab">
										<span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo _l('omni_other_setting'); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="tab-content mtop10">
						<div role="tabpanel" class="tab-pane active" id="omni_setting_product">
							<div class="row">
								<div class="col-md-3"> 
									<a href="#" onclick="add_product(); return false;" class="btn btn-info pull-left">
										<?php echo _l('add'); ?>
									</a>
									<div class="clearfix"></div><br>
								</div>
								<div class="col-md-3">
								</div>
								<div class="col-md-3">
									<?php
									echo render_select('customer_group_filter[]', $omni_customer_group, array('id', 'name'), 'omni_customer_group','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
								</div>	
								<div class="col-md-3">
									<?php echo render_select('customer_filter[]', $omni_customer, array('userid', 'company'), 'omni_customer','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
								</div>							
							</div>	
							<div class="row">
								<div class="col-md-12">
									<a href="#" onclick="staff_bulk_actions(); return false;"  data-table=".table-add_product_management" data-target="#add_product_management" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>                   
									<?php
									$table_data = array(
										'<input type="checkbox" id="mass_select_all" data-to-table="add_product_management">',
										_l('group_product'),
										_l('omni_products'),
										_l('omni_customer'),
										_l('omni_customer_group')
									);
									array_push($table_data, _l('options'));
									render_datatable($table_data,'add_product_management',
										array('customizable-table'),
										array(
											'proposal_sm' => 'proposal_sm',
											'id'=>'table-add_product_management',
											'data-last-order-identifier'=>'add_product_management',
											'data-default-order'=>get_table_last_order('add_product_management'),
										)); ?>	
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane" id="omni_other_setting">
								<?php echo form_open(site_url('omni_sales/pre_order_default_setting'),array('id'=>'save_setting-form','class'=>'save_setting-form')); ?>
								<div class="row">
									<div class="col-md-6">
										<?php
										$omni_default_seller = get_option('omni_default_seller');
										echo render_select('omni_default_seller', $staff, array('staffid' , array('firstname', 'lastname')), 'omni_default_seller', $omni_default_seller);
										?>						
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<hr>
										<br>
										<button class="btn btn-primary pull-right">
											<?php echo _l('omni_submit'); ?>
										</button>
									</div>
								</div>
								<?php echo form_close(); ?>
							</div>
						</div>
					<?php } else { ?>
						<center><span><?php echo _l("omni_this_channel_is_not_activated"); ?></span> </center>
					<?php } ?> 
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="chose_product" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="add-title"><?php echo _l('add_product'); ?></span>
						<span class="update-title hide"><?php echo _l('update_product'); ?></span>
					</h4>
				</div>
				<?php echo form_open(admin_url('omni_sales/add_product_pre_order'),array('id'=>'form_add_product')); ?>	            
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="sales_channel_id" value="<?php echo html_entity_decode($id_channel); ?>">
						<input type="hidden" name="channel" value="<?php echo html_entity_decode($channel); ?>">
						<input type="hidden" name="id" value="">
						<div class="col-md-6">
							<?php
							echo render_select('customer_group[]', $omni_customer_group, array('id', 'name'), 'omni_customer_group','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>	
						<div class="col-md-6">
							<?php echo render_select('customer[]', $omni_customer, array('userid', 'company'), 'omni_customer','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>	
						<div class="col-md-12">
							<?php 
							echo render_select('group_product_id',$group_product,array('id',array('commodity_group_code','name')),'group_product','', array('onchange'=>'get_list_product(this);'));
							?>
						</div>

						<div class="col-md-12">
							<?php echo render_select('product_id[]', $items, array('id', 'label'), 'products','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
				<?php echo form_close(); ?>	                
			</div>
		</div>
	</div>

	<div class="modal add_product_management" id="product-add_product_management" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<?php echo form_open(admin_url('omni_sales/delete_mass_product_pre_order_channel'), array('id' => 'delete_mass_add_product_management' )); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
				</div>
				<div class="modal-body">
					<div class="checkbox checkbox-danger">
						<?php echo form_hidden('check_id'); ?>
						<input type="hidden" name="redirect" value="pre_order">
						<input type="checkbox" name="mass_delete" id="mass_delete">
						<label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
						<input type="hidden" name="channel" value="<?php echo html_entity_decode($channel); ?>">
					</div>
					<input type="hidden" name="check_id">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" class="btn btn-primary"><?php echo _l('confirm'); ?></button>
				</div>

			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
	<!-- End modal -->


	<?php init_tail(); ?>
</body>
</html>
