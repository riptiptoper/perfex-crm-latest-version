
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="table_history hide">
	<div class="col-md-12">
		<?php
		$table_data = array(
		  _l('name_discount'),
		  _l('client'),
		  _l('order_number'),
	      _l('voucher').', '._l('coupon'),
	      _l('total_order'),      
		  _l('discount'),     
		  _l('tax'),     
		  _l('after_discount'),     
	      _l('date_apply'),
		    );
		render_datatable($table_data,'log_discount');
		?>
	</div>	
</div>

