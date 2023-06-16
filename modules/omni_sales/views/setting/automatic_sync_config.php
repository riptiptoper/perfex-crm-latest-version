<a href="#" class="btn btn-info pull-left new_setting_wcm_auto_store_sync">
	<?php echo _l('add'); ?>
</a>
<br>
<br>
<br>
<?php 

	$syn1 = '';	
	$syn2 = '';
	$syn3 = '';
	$syn4 = '';
	$syn5 = '';
	$syn6 = '';
	$syn7 = '';
	$syn8 = '';
?>
<?php
$table_data = array(
    _l('store'),
    _l('sync_omni_sales_products'),
    _l('sync_omni_sales_inventorys'),
    _l('price_crm_woo'),
    _l('sync_omni_sales_description'),
    // _l('sync_omni_sales_images'),
    _l('sync_omni_sales_orders'),
    // _l('product_info_enable_disable'),
    // _l('product_info_image_enable_disable'),
    _l('option'),
    );
render_datatable($table_data,'store_sync_v2');
?>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title add_title"><?php echo _l('new_store_sync_auto'); ?></h4>
        <h4 class="modal-title edit_title hide"><?php echo _l('edit_store_sync_auto'); ?></h4>
      </div>
      <div class="modal-body">
		<?php echo form_open(site_url('omni_sales/sync_auto_store'),array('id'=>'sync-auto-store-form')); ?>
          	<?php echo form_hidden('id'); ?>
          	<?php echo render_select('store',$store,array('id','name_channel'),'store',''); ?>
			<div class="row">
				<div class="col-md-12">
					<h4><?php echo _l('crm_to_woocommerce_store'); ?>&nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('note_setting_1'); ?>"></i></h4>
					<div class="col-md-12 position_partent">
				            <input type="checkbox" name="sync_omni_sales_products" id="sync_omni_sales_products"  <?php echo html_entity_decode($syn1); ?> />
				            <label for="sync_omni_sales_products"><?php echo _l('product_info_enable_disable') ?></label>
				            <input type="number" min="5" name="time1" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_product_info_time1)?>"/>
					</div>
				    <div class="col-md-12 position_partent">
				    	<div class="funkyradio-warning">
				            <input type="checkbox" name="sync_omni_sales_inventorys" id="sync_omni_sales_inventorys" <?php echo html_entity_decode($syn2); ?> />
				            <label for="sync_omni_sales_inventorys"><?php echo _l('inventory_info_enable_disable'); ?></label>
				            <input type="number" min="5" name="time2" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_inventory_info_time2)?>"/>
				        </div>
				    </div>
				    <div class="col-md-12 position_partent">
				    	<div class="funkyradio-warning">
				            <input type="checkbox" name="price_crm_woo" id="price_crm_woo" <?php echo html_entity_decode($syn3); ?> />
				            <label for="price_crm_woo"><?php echo _l('price_enable_disable'); ?></label>
				            <input type="number" min="5" name="time3" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_price_time3)?>"/>
				        </div>
				    </div>
				    <div class="col-md-12 position_partent">
				    	<div class="funkyradio-info">
				            <input type="checkbox" name="sync_omni_sales_description" id="sync_omni_sales_description"  <?php echo html_entity_decode($syn4); ?> />
				            <label for="sync_omni_sales_description"><?php echo _l('descripton_enable_disable'); ?></label>
				            <input type="number" min="5" name="time4" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_decriptions_time4)?>"/>
				        </div>
				    </div>
				    <!-- <div class="col-md-12 position_partent">
				    	 <div class="funkyradio-danger">
				            <input type="checkbox" name="sync_omni_sales_images" id="sync_omni_sales_images"  <?php echo html_entity_decode($syn5); ?> />
				            <label for="sync_omni_sales_images"><?php echo _l('product_image_enable_disable'); ?></label>
				            <input type="number" min="5" name="time5" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_images_time5)?>"/>
				        </div>
				    </div> -->
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
				<h4><?php echo _l('woocommerce_store_to_crm'); ?>&nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('note_setting_2'); ?>"></i></h4>
			 		<div class="col-md-12 position_partent">
					   	<div class="funkyradio">
				            <div class="funkyradio-success">
				                <input type="checkbox" name="sync_omni_sales_orders" id="sync_omni_sales_orders" <?php echo html_entity_decode($syn1); ?> />
				                <label for="sync_omni_sales_orders"><?php echo _l('order_enable_disable'); ?></label>
				                <input type="number" min="5" name="time6" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute); ?>"/>

				            </div>
				        </div>
			 		</div>

			 		<!-- <div class="col-md-12 position_partent">
			 			<div class="funkyradio-success">
				                <input type="checkbox" name="product_info_enable_disable" id="product_info_enable_disable" <?php echo html_entity_decode($syn2); ?> />
				                <label for="product_info_enable_disable"><?php echo _l('product_info_enable_disable'); ?></label>
				                <input type="number" min="5" name="time7" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_product_info_time7); ?>"/>
				            </div>
			 		</div> -->
			 	<!-- 	<div class="col-md-12 position_partent">
				            <div class="funkyradio-success">
				                <input type="checkbox" name="product_info_image_enable_disable" id="product_info_image_enable_disable" <?php echo html_entity_decode($syn3); ?> />
				                <label for="product_info_image_enable_disable"><?php echo _l('product_info_image_enable_disable'); ?></label>
				                <input type="number" min="5" name="time8" class="pull-right cus_input" placeholder="<?php echo _l('num_of_minutes'); ?>" value="<?php echo html_entity_decode($minute_sync_product_info_images_time8); ?>"/>
				            </div>
			 		</div> -->
				</div>
			</div>
      </div>
      <div class="modal-footer">
		<button type="submit" class="btn btn-primary pull-right"><?php echo _l('save'); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close');?></button>
      </div>
	  <?php echo form_close(); ?>
    </div>

  </div>
</div>
  
