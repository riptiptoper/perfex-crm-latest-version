<div class="modal fade z-index-none" id="warehouse_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo html_entity_decode($title); ?></h4>
			</div>
			<?php echo form_open(admin_url('warehouse/order_return_create_stock_export/'.$id), array('id' => 'select_warehouse_modal', 'autocomplete'=>'off')); ?>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
					<div class="panel-body mtop10 invoice-item">
					<div class="table-responsive s_table ">
						<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
							<thead>
								<tr>
									<th width="50%" align="left" ><?php echo _l('commodity_name'); ?></th>
									<th width="25%" align="left"><?php echo _l('quantity'); ?></th>
									<th width="25%" align="left"><?php echo _l('warehouse_name'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php echo html_entity_decode($html); ?>
							</tbody>
						</table>
					</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<!-- <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button> -->
				<?php if(has_permission('purchase_order_return', '', 'create') || has_permission('purchase_order_return', '', 'edit')){ ?>
					<button  type="submit" class="btn btn-info pull-right mright10"><?php echo _l('submit'); ?></button>
				<?php } ?>
			</div>

		</div>

		<?php echo form_close(); ?>
	</div>
</div>
</div>
<?php require('modules/warehouse/assets/js/order_returns/select_warehouse_modal_js.php'); ?>

