<?php echo form_open(site_url('omni_sales/save_setting/sync_orders'),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form')); ?>
<div class="tab-content cart-tab w-100">
			   <div role="tab1" class="tab-pane item-tab active" id="tab1">
			      <div class="panel-body">
			    	<div class="row contents">
			 		<div class="col-md-12">
			 				<?php 	
			 					echo render_input('minute','minute',$minute,'number');
			 				 ?>
			 		</div>
				</div>
			</div>
	</div>
	<div class="row">
		<hr>
		<button class="btn btn-primary pull-right"><?php echo _l('save'); ?></button>
	</div>
</div>
<?php echo form_close(); ?>
