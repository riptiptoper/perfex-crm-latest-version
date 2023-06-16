<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
	$name_channel = '';
	$consumer_key = '';
	$consumer_secret = '';
	$url = '';
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
	     <div class="col-md-3"> 
		    <a href="#" id="add_channel_woocommerce" class="btn btn-info pull-left">
		        <?php echo _l('add'); ?>
		    </a>
		    <div class="clearfix"></div><br>
		 </div>
		 <div id="box-loadding"></div>
		<div class="clearfix"></div>
		<hr class="hr-panel-heading" />
		<div class="clearfix"></div>
		<?php
	        $table_data = array(
	            _l('name_channel'),
	            _l('url'),
	            _l('consumer_key'),
	            _l('consumer_secret'),
	            _l('option'),
	            );
	        render_datatable($table_data,'channel-woocommerce');
      	?>
	</div>
  </div>
 </div>
</div>
<div class="modal fade" id="channel_woocommerce" tabindex="-1" role="dialog">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <span class="add-title"><?php echo _l('add_channel_woocommerce'); ?></span>
                <span class="update-title hide"><?php echo _l('edit_channel_woocommerce'); ?></span>
            </h4>
        </div>
    <?php echo form_open(admin_url('omni_sales/add_channel_woocommerce'),array('id'=>'form_add_channel_woocommerce')); ?>	            
        <div class="modal-body">
        	<?php echo form_hidden('id'); ?>
        	<?php echo render_input('name_channel', 'name_store', $name_channel); ?>
        	<?php echo render_input('url', 'url', $url); ?>
        	<?php echo render_input('consumer_key', 'consumer_key', $consumer_key); ?>
        	<?php echo render_input('consumer_secret', 'consumer_secret', $consumer_secret); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success test_connect hide"><?php echo _l('test_connect'); ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
    <?php echo form_close(); ?>	                
  	</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
