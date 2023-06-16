<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
$status_pos = $this->omni_sales_model->get_sales_channel_by_channel('pos')->status;
$status_portal = $this->omni_sales_model->get_sales_channel_by_channel('portal')->status;
$status_woocommerce = $this->omni_sales_model->get_sales_channel_by_channel('woocommerce')->status;
?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-2">
				<ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked">
					<li role="presentation" class="tab_cart <?php if($tab == 'automatic_sync_config'){ echo 'active'; } ?>">
						<a href="<?php echo admin_url('omni_sales/setting?tab=automatic_sync_config'); ?>" aria-controls="tab_config" role="tab" aria-controls="tab_config">
							<?php echo _l('automatic_sync_config'); ?>
						</a>
					</li>
					<li role="presentation" class="tab_cart <?php if($tab == 'notification_recipient'){ echo 'active'; } ?>">
						<a href="<?php echo admin_url('omni_sales/setting?tab=order_notificaiton'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
							<?php echo _l('order_notificaiton'); ?>
						</a>
					</li>
					<li role="presentation" class="tab_cart <?php if($tab == 'notification_recipient'){ echo 'active'; } ?>">
						<a href="<?php echo admin_url('omni_sales/setting?tab=default_setting'); ?>" aria-controls="tab1" role="tab" aria-controls="tab2">
							<?php echo _l('default_setting'); ?>
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">
						<?php $this->load->view('setting/'.$tab); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
