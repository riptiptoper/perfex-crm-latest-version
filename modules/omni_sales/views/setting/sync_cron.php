<?php echo form_open(site_url('omni_sales/save_setting/sync_cron'),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form')); ?>
<?php 

	$syn1 = '';	
	$syn2 = '';
	$syn3 = '';
	$syn4 = '';
	$syn5 = '';

	if($sync_omni_sales_products  == "1"){
		$syn1 = "checked";
	}
	if($sync_omni_sales_orders  == "1"){
		$syn2 = "checked";
	}
	if($sync_omni_sales_inventorys  == "1"){
		$syn3 = "checked";
	}
	if($sync_omni_sales_description  == "1"){
		$syn4 = "checked";
	}
	if($sync_omni_sales_images  == "1"){
		$syn5 = "checked";
	}
?>
<div class="tab-content cart-tab w-100">
			   <div role="tab1" class="tab-pane item-tab active" id="tab1">
			      <div class="panel-body">
			    	<div class="row contents">
			 		<div class="col-md-12">
					   	<div class="funkyradio">
				            <div class="funkyradio-primary">
				                <input type="checkbox" name="sync_omni_sales_products" id="sync_omni_sales_products"  <?php echo html_entity_decode($syn1); ?> />
				                <label for="sync_omni_sales_products"><?php echo _l('sync_omni_sales_products') ?></label>
				            </div>
				            <div class="funkyradio-success">
				                <input type="checkbox" name="sync_omni_sales_orders" id="sync_omni_sales_orders" <?php echo html_entity_decode($syn2); ?> />
				                <label for="sync_omni_sales_orders"><?php echo _l('sync_omni_sales_orders'); ?></label>
				            </div>
				            <div class="funkyradio-warning">
				                <input type="checkbox" name="sync_omni_sales_inventorys" id="sync_omni_sales_inventorys" <?php echo html_entity_decode($syn3); ?> />
				                <label for="sync_omni_sales_inventorys"><?php echo _l('sync_omni_sales_inventorys'); ?></label>
				            </div>
				            <div class="funkyradio-info">
				                <input type="checkbox" name="sync_omni_sales_description" id="sync_omni_sales_description"  <?php echo html_entity_decode($syn4); ?> />
				                <label for="sync_omni_sales_description"><?php echo _l('sync_omni_sales_description'); ?></label>
				            </div>
				            <div class="funkyradio-danger">
				                <input type="checkbox" name="sync_omni_sales_images" id="sync_omni_sales_images"  <?php echo html_entity_decode($syn5); ?> />
				                <label for="sync_omni_sales_images"><?php echo _l('sync_omni_sales_images'); ?></label>
				            </div>
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
