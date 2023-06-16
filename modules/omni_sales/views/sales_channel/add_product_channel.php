<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
$group_product_id = '';
$product_id = '';
?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<div class="clearfix"></div><br>
				<div class="col-md-12">
					<h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
					<hr>
				</div>
				<div class="row">
					<div class="col-md-3"> 
						<a href="#" onclick="add_product(); return false;" class="btn btn-info pull-left">
							<?php echo _l('add'); ?>
						</a>
						<a href="#" onclick="price_update(); return false;" class="btn btn-info mbot10 mleft10 "><i class="fa fa-pencil" aria-hidden="true"></i>
						      <?php echo ' '._l('update_price'); ?>
						  </a>
						<div class="clearfix"></div><br>
					</div>
					<div class="col-md-3"></div>
					<?php if($channel == 'pos'){ ?>
						<div class="col-md-3">
							<?php
							 echo render_select('product_filter[]', $products, array('id', 'description'), 'products','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>	
						<div class="col-md-3">
							<?php echo render_select('department_filter[]', $departments, array('departmentid', 'name'), 'department','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>							
					<?php }	?>	
				</div>	
				<div class="clearfix"></div>
				<hr class="hr-panel-heading" />
				<div class="clearfix"></div>
				<div id="popup_confirm"></div>
				<div id="box-loadding"></div>						

				<a href="#" onclick="staff_bulk_actions(); return false;"  data-table=".table-add_product_management" data-target="#add_product_management" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>                   
				<?php
				$table_data = array(
					'<input type="checkbox" id="mass_select_all" data-to-table="add_product_management">',
					_l('product_code'),
					_l('product_name'),
					_l('price'),
					_l('price_on_channel')
				);
				if($channel == 'pos'){
					array_push($table_data, _l('department'));
				}
				array_push($table_data, _l('options'));
				render_datatable($table_data,'add_product_management',
					array('customizable-table'),
					array(
						'proposal_sm' => 'proposal_sm',
						'id'=>'table-add_product_management',
						'data-last-order-identifier'=>'add_product_management',
						'data-default-order'=>get_table_last_order('add_product_management'),
					)); ?>

					<div class="col-md-12">
						<a href="<?php echo admin_url('omni_sales/omni_sales_channel'); ?>" class="btn btn-danger"><?php echo _l('close'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
  	<?php echo form_hidden('check'); ?>

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
				<?php echo form_open(admin_url('omni_sales/add_product'),array('id'=>'form_add_product')); ?>	            
				<div class="modal-body">
					<div class="row">
						<input type="hidden" name="sales_channel_id" value="<?php echo html_entity_decode($id_channel); ?>">
						<input type="hidden" name="channel" value="<?php echo html_entity_decode($channel); ?>">
						<input type="hidden" name="id" value="">
						<div class="col-md-12">
							<?php 
							echo render_select('group_product_id',$group_product,array('id',array('commodity_group_code','name')),'group_product',$group_product_id, array('onchange'=>'get_list_product(this);'));
							?>
						</div>

						<div class="col-md-12">
							<?php echo render_select('product_id[]', $products, array('id', 'description'), 'products','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
						</div>
						<?php if($channel == 'pos'){ ?>
							<div class="col-md-12">
								<?php echo render_select('department_id[]', $departments, array('departmentid', 'name'), 'department','', array('multiple' => true, 'data-actions-box' => true), [], '', '', false); ?>
							</div>							
						<?php }	?>						
						<div class="col-md-12 pricefr hide">
							<?php 
							$arrAtt = array();
							$arrAtt['data-type']='currency';
							echo render_input('prices','prices','','text',$arrAtt); ?>

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
			<?php echo form_open(admin_url('omni_sales/delete_mass_add_product_sales_channel'), array('id' => 'delete_mass_add_product_management' )); ?>
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div>
				<div class="modal-body">
					<div class="checkbox checkbox-danger">
						<?php echo form_hidden('check_id'); ?>
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
	<?php init_tail(); ?>
</body>
</html>
