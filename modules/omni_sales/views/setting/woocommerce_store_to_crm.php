<?php echo form_open(site_url('omni_sales/save_setting/woo_to_crm'),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form')); ?>
<?php 

	$syn1 = '';	
	$syn2 = '';
	$syn3 = '';
	
	if($sync_omni_sales_orders  == "1"){
		$syn1 = "checked";
	}
	if($product_info_enable_disable  == "1"){
		$syn2 = "checked";
	}
	if($product_info_image_enable_disable  == "1"){
		$syn3 = "checked";
	}
	
?>
<div class="tab-content cart-tab w-100">
			   <div role="tab1" class="tab-pane item-tab active" id="tab1">
			      <div class="panel-body">
			    	<div class="row contents">
			 		<div class="col-md-7 position_partent">
					   	<div class="funkyradio">
				            <div class="funkyradio-success">
				                <input type="checkbox" name="sync_omni_sales_orders" id="sync_omni_sales_orders" <?php echo html_entity_decode($syn1); ?> />
				                <label for="sync_omni_sales_orders"><?php echo _l('order_enable_disable'); ?></label>
				                <input type="number" name="time6" class="pull-right cus_input" value="<?php echo html_entity_decode($minute); ?>"/>
				            </div>
				        </div>
			 		</div>

			 		<div class="col-md-7 position_partent">
			 			<div class="funkyradio-success">
				                <input type="checkbox" name="product_info_enable_disable" id="product_info_enable_disable" <?php echo html_entity_decode($syn2); ?> />
				                <label for="product_info_enable_disable"><?php echo _l('product_info_enable_disable'); ?></label>
				                <input type="number" name="time7" class="pull-right cus_input" value="<?php echo html_entity_decode($minute_sync_product_info_time7); ?>"/>
				            </div>
			 		</div>
			 		<div class="col-md-7 position_partent">
				            <div class="funkyradio-success">
				                <input type="checkbox" name="product_info_image_enable_disable" id="product_info_image_enable_disable" <?php echo html_entity_decode($syn3); ?> />
				                <label for="product_info_image_enable_disable"><?php echo _l('product_info_image_enable_disable'); ?></label>
				                <input type="number" name="time8" class="pull-right cus_input" value="<?php echo html_entity_decode($minute_sync_product_info_images_time8); ?>"/>
				            </div>
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
