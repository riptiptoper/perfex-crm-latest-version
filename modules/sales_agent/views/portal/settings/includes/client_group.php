<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); 
    add_datatables_js_assets($groupName);
    ?>

<div class="_buttons">
	<a href="#" class="btn btn-info pull-left" onclick="new_client_group(); return false;"><?php echo _l('sa_new_client_group'); ?></a>
</div>
<div class="clearfix"></div>
<hr class="hr-panel-heading" />
<div class="clearfix"></div>
<table class="table dt-table dt-inline dataTable no-footer">
	<thead>
		<th><?php echo _l('id'); ?></th>
		<th><?php echo _l('name'); ?></th>
		<th><?php echo _l('description'); ?></th>
		<th><?php echo _l('options'); ?></th>
	</thead>
	<tbody>
	<?php foreach($client_groups as $value){ ?>
		<tr>
		   <td><?php echo html_entity_decode($value['id']); ?></td>
		   <td><?php echo html_entity_decode($value['name']); ?></td>
		   <td><?php echo html_entity_decode($value['description']); ?></td>
		   <td>
		     <a href="#" onclick="edit_client_group(this,<?php echo html_entity_decode($value['id']); ?>); return false" data-name="<?php echo html_entity_decode($value['name']); ?>" data-description="<?php echo html_entity_decode($value['description']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
		      <a href="<?php echo site_url('sales_agent/portal/delete_client_group/'.$value['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
		   </td>
		</tr>
	<?php } ?>
	</tbody>
</table>


<div class="modal fade" id="client_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog withd_1k" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title"><?php echo _l('edit_client_group'); ?></span>
					<span class="add-title"><?php echo _l('new_client_group'); ?></span>
				</h4>
			</div>

			<?php echo form_open('sales_agent/portal/client_group_form',array('id'=>'client-group-form')); ?>
			<?php echo form_hidden('client_group_id'); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php echo render_input('name','group_name','','text'); ?>
						
						<?php echo render_textarea('description', 'description', ''); ?>

						<div class="list_approve">
							
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

