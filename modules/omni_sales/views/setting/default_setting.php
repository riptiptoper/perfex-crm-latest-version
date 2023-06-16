<?php $omni_show_products_by_department = get_option('omni_show_products_by_department'); ?>
<?php echo form_open(site_url('omni_sales/save_setting/default_setting'),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form')); ?>
<?php 
$bill_header_pos = '';
$data_header_option = get_option('bill_header_pos');
if($data_header_option){
	$bill_header_pos = $data_header_option;      
}
$bill_footer_pos = '';
$data_footer_option = get_option('bill_footer_pos');
if($data_footer_option){
	$bill_footer_pos = $data_footer_option;      
}
?>
<hr>
<fieldset>
	<legend>POS</legend>
	<div class="form-group">
		<div class="checkbox checkbox-primary">
			<input type="checkbox" id="omni_show_products_by_department" name="omni_show_products_by_department" 
			<?php echo (($omni_show_products_by_department == 1) ? 'checked' : '') ?> value="1" >
			<label for="omni_show_products_by_department"><?php echo _l('omni_show_products_by_department') ?></label>
		</div>
	</div>	
	<div class="row">
		<div class="col-md-6">
			<?php echo render_textarea('bill_header_pos','bill_header_pos',$bill_header_pos,array('row'=>3),array(),'','tinymce'); ?>
		</div>
		<div class="col-md-6">
			<?php echo render_textarea('bill_footer_pos','bill_footer_pos',$bill_footer_pos,array('row'=>3),array(),'','tinymce'); ?>
		</div>
	</div>
</fieldset>	 				
<hr>

<button class="btn btn-primary pull-right"><?php echo _l('save'); ?></button>
<?php echo form_close(); ?>
