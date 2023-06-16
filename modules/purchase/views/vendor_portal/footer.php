<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $viewuri = $_SERVER['REQUEST_URI']; ?>
<?php if((strpos($viewuri, 'purchase/vendors_portal/add_update_invoice') === false)){ ?>
	<div class="pusher"></div>
	<footer class="navbar-fixed-bottom footer">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<span class="copyright-footer"><?php echo date('Y'); ?> <?php echo _l('clients_copyright', get_option('companyname')); ?></span>
				</div>
			</div>
		</div>
	</footer>
<?php } ?>
