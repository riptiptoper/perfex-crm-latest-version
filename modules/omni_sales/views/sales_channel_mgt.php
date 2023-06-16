<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
$status_pos = $this->omni_sales_model->get_sales_channel_by_channel('pos')->status;
$status_portal = $this->omni_sales_model->get_sales_channel_by_channel('portal')->status;
$status_woocommerce = $this->omni_sales_model->get_sales_channel_by_channel('woocommerce')->status;
$status_pre_order = $this->omni_sales_model->get_sales_channel_by_channel('pre_order')->status;
?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
			<div class="panel-body">
				<h4><i class="fa fa-object-group"></i> <?php echo html_entity_decode($title) ?></h4>
				<hr>
				<div class="clearfix"></div>


				<div class="row mbot15">
					<div class="col-md-4">
						<div class="col-md-12 channel pos shadow <?php if($status_pos == 'active'){ echo 'active'; } ?> red">
							<a class="wrap-link not-select" href="<?php echo admin_url('omni_sales/add_product_channel/pos'); ?>">			 		
								<span>POS</span>					
							</a>

							<div class="switch">
								<div class="onoffswitch" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('active'); ?>">
									<input type="checkbox" name="onoff" onchange="change_active_ch(this);" class="onoffswitch-checkbox" data-channel="pos" id="pos" <?php if($status_pos == 'active'){ echo 'checked'; } ?>>
									<label class="onoffswitch-label" for="pos">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>	
							</div>
							<div class="bottoms">
								<a class="link not-select" href="<?php echo admin_url('omni_sales/pos'); ?>"><?php echo _l('go_to_page'); ?></a> | 
								<a class="link not-select" href="<?php echo admin_url('omni_sales/add_product_channel/pos'); ?>"><?php echo _l('setting_product'); ?></a>	      

							</div>
						</div>	        
					</div>
						<div class="col-md-4">
							<div class="col-md-12 channel portal shadow <?php if($status_portal == 'active'){ echo 'active'; } ?> blue">
								<a class="wrap-link not-select" href="<?php echo admin_url('omni_sales/add_product_channel/portal'); ?>">			 		
									<span>Portal</span>					
								</a>

								<div class="switch">
									<div class="onoffswitch" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('active'); ?>">
										<input type="checkbox" name="onoff" onchange="change_active_ch(this);" class="onoffswitch-checkbox" data-channel="portal" id="portal" <?php if($status_portal == 'active'){ echo 'checked'; } ?>>
										<label class="onoffswitch-label" for="portal">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>		        
								</div>	
								<div class="bottoms">
									<a class="link not-select" href="<?php echo site_url('omni_sales/omni_sales_client/index/1/0/0'); ?>"><?php echo _l('go_to_page'); ?></a> | 
									<a class="link not-select" href="<?php echo admin_url('omni_sales/add_product_channel/portal'); ?>"><?php echo _l('setting_product'); ?></a>	      
								</div>	        
							</div>		        
						</div>
						<div class="col-md-4">
							<div class="col-md-12 channel woocommerce shadow <?php if($status_woocommerce == 'active'){ echo 'active'; } ?> pink">
								<a class="wrap-link not-select" href="<?php echo admin_url('omni_sales/add_woocommerce_store'); ?>">
									<span>Woo Commerce</span>					
								</a>
								<div class="switch">				
									<div class="onoffswitch" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('active'); ?>">
										<input type="checkbox" name="onoff" onchange="change_active_ch(this);" class="onoffswitch-checkbox" data-channel="woocommerce" id="woocomere" <?php if($status_woocommerce == 'active'){ echo 'checked'; } ?>>
										<label class="onoffswitch-label" for="woocomere">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>		        
								</div>	
								<div class="bottoms">
									<a class="link not-select" href="<?php echo admin_url('omni_sales/add_woocommerce_store'); ?>"><?php echo _l('setting_channel'); ?></a>	      
								</div>	        
							</div>		        
						</div>
				</div>
				<div class="row">
						<div class="col-md-4 mtop15">
							<div class="col-md-12 channel pre-order shadow <?php if($status_pre_order == 'active'){ echo 'active'; } ?> blue">
								<a class="wrap-link not-select" href="<?php echo admin_url('omni_sales/pre_order_list'); ?>">
									<span>Pre-Order</span>					
								</a>
								<div class="switch">				
									<div class="onoffswitch" data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('active'); ?>">
										<input type="checkbox" name="onoff" onchange="change_active_ch(this);" class="onoffswitch-checkbox" data-channel="pre_order" id="pre_order" <?php if($status_pre_order == 'active'){ echo 'checked'; } ?>>
										<label class="onoffswitch-label" for="pre_order">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>		        
								</div>	
								<div class="bottoms">
									<a class="link not-select" href="<?php echo admin_url('omni_sales/pre_order'); ?>"><?php echo _l('setting_channel'); ?></a>	      
								</div>	        
							</div>		        
						</div>
				</div>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
			</div>
		</div>
	</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
