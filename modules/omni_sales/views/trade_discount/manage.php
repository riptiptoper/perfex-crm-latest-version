<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
	
	<div class="col-md-12">
		<h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
		<div class="horizontal-scrollable-tabs preview-tabs-top">
	      	<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
	      	<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
	      	<div class="horizontal-tabs">
		      	<ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
		      	  <li role="presentation" class="active">
		             <a href="#promotions" aria-controls="promotions" role="tab" data-toggle="tab" aria-controls="promotions">
		             <?php echo _l('promotions'); ?>
		             </a>
		          </li>
		          <li role="presentation">
		             <a href="#voucher" aria-controls="voucher" role="tab" data-toggle="tab" aria-controls="voucher">
		             <?php echo _l('voucher'); ?>
		             </a>
		          </li>
		      	</ul>
	  		</div>
	  	</div> 
		
	</div>

	

  	<div class="tab-content w-100">
        <div role="tabpanel" class="tab-pane active" id="promotions">
        	<div class="col-md-12">
        		<div class="col-md-3"> 
					<a href="<?php echo admin_url('omni_sales/new_trade_discount'); ?>" id="new_trade_discount" class="btn btn-info pull-left">
					    <?php echo _l('add'); ?>
					</a>
					<div class="clearfix"></div><br>
				</div>
        		
				<?php
			        $table_data = array(
			            _l('name_trade_discount'),
			            _l('start_time'),
			            _l('end_time'),
			            _l('formal'),
			            _l('discount'),
			            _l('option'),
			            );
			        render_datatable($table_data,'trade-discount');
		      	?>
			</div>
        </div>
        <div role="tabpanel" class="tab-pane" id="voucher">
        	<div class="col-md-12">
        		<div class="col-md-3"> 
					<a href="<?php echo admin_url('omni_sales/new_voucher'); ?>" id="new_voucher" class="btn btn-info pull-left">
					    <?php echo _l('add'); ?>
					</a>
					<div class="clearfix"></div><br>
				</div>
        		
				<?php
			        $table_data = array(
			            _l('name_trade_discount'),
			            _l('start_time'),
			            _l('end_time'),
			            _l('formal'),
			            _l('discount'),
			            _l('voucher'),
			            _l('option'),
			            );
			        render_datatable($table_data,'voucher');
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