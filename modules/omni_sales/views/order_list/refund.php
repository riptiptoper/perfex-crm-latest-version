<?php if(has_permission('omni_order_list', '', 'create') || is_admin()){ ?>
<button class="btn btn-primary btn_add_refund">
	<?php echo _l('add'); ?>
</button>
<?php } ?>
<div class="clearfix"></div>
<br>
 <table class="table table-refund_list scroll-responsive">
      <thead>
        <th><?php echo _l('omni_date'); ?></th>
        <th><?php echo _l('omni_refunded_amount'); ?></th>
        <th><?php echo _l('omni_payment_mode'); ?></th>
        <th><?php echo _l('omni_note'); ?></th>
        <th><?php echo _l('omni_options'); ?></th>
      </thead>
      <tbody></tbody>
      <tfoot>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
     </tfoot>
   </table>

<div class="modal fade" id="create_refund_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="add-title"><?php echo _l('omni_create_refund') ?></span>
					<span class="edit-title hide"><?php echo _l('omni_update_refund') ?></span>
				</h4>
			</div>
			<?php echo form_open(admin_url('omni_sales/add_edit_refund'), ['id'=>'omni_sale_refund_form']); ?>
			<input type="hidden" name="order_id" value="<?php echo html_entity_decode($order->id); ?>">
			<div class="modal-body">

			</div>
			<div class="clearfix">               
				<br>
				<br>
				<div class="clearfix">               
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
					<button type="submit" data-status="8" class="btn btn-danger btn_create_refund"><?php echo _l('submit'); ?></button>
				</div>
			</div><!-- /.modal-content -->
			<?php echo form_close(); ?>			
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div><!-- /.modal -->