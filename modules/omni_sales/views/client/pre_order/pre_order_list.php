<?php hooks()->do_action('head_element_client'); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">	
				<div class="panel_s invoice accounting-template fr1">
					<div class="panel-body mtop10">
						<div class="row">
							<div class="col-md-12">
								<h4 class="pull-left"><?php echo _l('omni_pre_order'); ?></h4>
								<a href="<?php echo admin_url('omni_sales/omni_sales_client/create_pre_order') ?>" class="btn btn-primary pull-right">
									<?php echo _l('omni_create_pre_order'); ?>
								</a>
							</div>
						</div>
						<div class="clearfix"></div>
						<hr>
						<div class="clearfix"></div>
						<?php 
						if(is_client_logged_in()){ ?>
							<?php $this->load->view('client/pre_order/pre_order_list_item'); ?>
						<?php  }
						else{ ?>
							<center><strong><?php echo _l('omni_please_login_to_show_pre_order_list'); ?></strong></center>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php hooks()->do_action('client_pt_footer_js'); ?>