<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">

	<div class="horizontal-scrollable-tabs preview-tabs-top">
      	<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
      	<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
      	<div class="horizontal-tabs">
	      	<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
	      	  <li role="presentation" class="active">
	             <a href="#products" aria-controls="products" role="tab" data-toggle="tab" aria-controls="products">
	             <?php echo _l('products'); ?>
	             </a>
	          </li>
	          <!-- <li role="presentation">
	             <a href="#synchronize_products_from_store_information" aria-controls="synchronize_products_from_store_information" role="tab" data-toggle="tab" aria-controls="synchronize_products_from_store_information">
	             <?php echo _l('products_from_store'); ?>
	             </a>
	          </li> -->
	          <li role="presentation">
	             <a href="#synchronize_products_from_store_information_images" aria-controls="synchronize_products_from_store_information_images" role="tab" data-toggle="tab" aria-controls="synchronize_products_from_store_information_images">
	             <?php echo _l('products_from_store_full'); ?>
	             </a>
	          </li>
	          <li role="presentation">
	             <a href="#synchronize_products_desctiption" aria-controls="synchronize_products_desctiption" role="tab" data-toggle="tab" aria-controls="synchronize_products_desctiption">
	             <?php echo _l('synchronize_products_desctiption'); ?>
	             </a>
	          </li>
	          <li role="presentation">
	             <a href="#price" aria-controls="price" role="tab" data-toggle="tab" aria-controls="price">
	             <?php echo _l('price'); ?>
	             </a>
	          </li>

	          <li role="presentation">
	             <a href="#orders" aria-controls="orders" role="tab" data-toggle="tab" aria-controls="orders">
	             <?php echo _l('orders'); ?>
	             </a>
	          </li>
	          <li role="presentation">
	             <a href="#inventory_manage" aria-controls="inventory_manage" role="tab" data-toggle="tab" aria-controls="inventory_manage">
	             <?php echo _l('inventory_manage'); ?>
	             </a>
	          </li>
	      	</ul>
  		</div>
  	</div> 

	<h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
	<div class="clearfix"></div><br>

  	<div class="tab-content row">
        <div role="tabpanel" class="tab-pane active" id="products">
        	<div class="col-md-12">
        		<p class="text-danger"><?php echo _l('sync_products_data_from_crm_to_store_log'); ?></p>
        	</div>
        	<div class="col-md-12">
        		
				<?php
			        $table_data = array(
			            _l('name'),
			            _l('regular_price'),
			            _l('sale_price'),
			            _l('date_on_sale_from'),
			            _l('date_on_sale_to'),
			            _l('short_description'),
			            _l('store'),
			            _l('sku'),
			            _l('date_sync'),
			            );
			        render_datatable($table_data,'diary-sync-products');
		      	?>
			</div>
        </div>
        <!-- <div role="tabpanel" class="tab-pane" id="synchronize_products_from_store_information">
        	<div class="col-md-12">
        		<p class="text-danger"><?php echo _l('sync_products_data_from_store_to_crm_log'); ?></p>
        	</div>
        	<div class="col-md-12">
				<?php
			        $table_data = array(
			        	_l('name'),
			            _l('regular_price'),
			            _l('short_description'),
			            _l('sku'),
			            _l('chanel'),
			            _l('date_sync'),
			            );
			        render_datatable($table_data,'sync-products-from-the-store-information');
		      	?>
			</div>
        </div> -->
		
		<div role="tabpanel" class="tab-pane" id="synchronize_products_from_store_information_images">
			<div class="col-md-12">
        		<p class="text-danger"><?php echo _l('sync_products_data_from_store_to_crm_log_2'); ?></p>
        	</div>
        	<div class="col-md-12">
				<?php
			        $table_data = array(
			        	_l('name'),
			            _l('regular_price'),
			            _l('short_description'),
			            _l('sku'),
			            _l('chanel'),
			            _l('date_sync'),
			            );
			        render_datatable($table_data,'sync-products-from-the-store-information-images');
		      	?>
			</div>
        </div>
        <div role="tabpanel" class="tab-pane" id="synchronize_products_desctiption">
					<div class="col-md-12">
		        		<p class="text-danger"><?php echo _l('synchronize_products_desctiption'); ?></p>
		        	</div>
		        	<div class="col-md-12">
						<?php
					        $table_data = array(
					        	_l('name'),
					            _l('description'),
					            _l('short_description'),
					            _l('store'),
					            _l('sku'),
					            _l('date_sync'),
					            );
					        render_datatable($table_data,'synchronize-products-desctiption');
				      	?>
					</div>
		        </div>
		<div role="tabpanel" class="tab-pane" id="price">
					<div class="col-md-12">
		        		<p class="text-danger"><?php echo _l('sync_products_data_from_crm_to_store_log_price'); ?></p>
		        	</div>
		        	<div class="col-md-12">
						<?php
					        $table_data = array(
					        	_l('name'),
					            _l('regular_price'),
					            _l('chanel'),
					            _l('date_sync'),
					            );
					        render_datatable($table_data,'sync-price');
				      	?>
					</div>
		        </div>
        <div role="tabpanel" class="tab-pane" id="orders">
        	<div class="col-md-12">
        		<p class="text-danger"><?php echo _l('sync_products_data_from_store_to_crm_log_order'); ?></p>
        	</div>
        	<div class="col-md-12">
				<?php
			        $table_data = array(
			        	_l('name'),
			            _l('regular_price'),
			            _l('sale_price'),
			            _l('channel'),
			            _l('company'),
			            _l('date_sync'),
			            );
			        render_datatable($table_data,'diary-sync-orders');
		      	?>
			</div>
        </div>

        <div role="tabpanel" class="tab-pane" id="inventory_manage">
        	<div class="col-md-12">
        		<p class="text-danger"><?php echo _l('sync_products_data_from_crm_to_store_log_inventory_manage'); ?></p>
        	</div>
        	<div class="col-md-12">
				<?php
			        $table_data = array(
			            _l('name'),
			            _l('regular_price'),
			            _l('sale_price'),
			            _l('date_on_sale_from'),
			            _l('date_on_sale_to'),
			            _l('short_description'),
			            _l('stock_quantity'),
			            _l('stock_quantity_history'),
			            _l('store'),
			            _l('sku'),
			            _l('date_sync'),
			            );
			        render_datatable($table_data,'diary-sync-inventory-manage');
		      	?>
			</div>
        </div>
    </div>    
	

	

	
	
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>