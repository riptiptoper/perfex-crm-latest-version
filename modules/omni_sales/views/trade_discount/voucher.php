<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php echo form_open_multipart(admin_url('omni_sales/add_discount_form'),array('id'=>'new_voucher')); ?>
<?php 
	$name_trade_discount = '';
	$start_time = '';
	$end_time = '';
	$discounts = '';
	$formal = '';
	$clientss = [];
	$group_clientss = [];
	$group_itemss = [];
	$itemss = [];
	$check1 = '';
	$check2 = '';
	$hide1 = 'hide';
	$hide2 = 'hide';
	$hide3 = 'hide';
	$col1 = 'col-md-11';
	$col2 = 'col-md-11';
	$voucher = '';
	$minimum_order_value = '';
	$channel = '';
	if(isset($id)){ 
		echo form_hidden('id', $id); 
		$name_trade_discount = $discount->name_trade_discount;
		$start_time = _d($discount->start_time);
		$end_time = _d($discount->end_time);
		$formal = $discount->formal;
		$discounts = $discount->discount;
		$voucher = $discount->voucher;
		$clientss = explode(',', $discount->clients);
		$group_clientss =  explode(',',$discount->group_clients);
		$group_itemss =  explode(',', $discount->group_items);
		$itemss =  explode(',',$discount->items);
		$minimum_order_value = app_format_money($discount->minimum_order_value,'');
		$channel = $discount->channel;
		if(isset($clientss[0])){
			if($clientss[0]!=''){
				if(count($clientss) > 0){
					$check1 = 'checked';
					$hide1 = '';
					$col1 = 'col-md-6';
				}	
			}
		}
		if($itemss != ''){
			$check2 = 'checked';
			$hide2 = '';
			$col2 = 'col-md-6';
		}
		if($formal== 1){
			$hide3 = '';
		}
	}else{
		echo form_hidden('id'); 
	}  
		$currency_name = '';
    if(isset($base_currency)){
        $currency_name = $base_currency->name;
    }
?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
<div class="clearfix"></div><br>
	<div class="col-md-12">
		<h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php
		 echo html_entity_decode($title); ?></h4>
		<hr>
	</div>
	<div class="col-md-12">
		<?php echo render_input('name_trade_discount', 'name', $name_trade_discount); ?>
	</div>
	<div class="voucher_code col-md-12">
		<?php echo render_input('voucher', 'code', $voucher); ?>
	</div>
	<div class="col-md-6">
		<?php echo render_date_input('start_time', 'start_time', $start_time); ?>
	</div>
	<div class="col-md-6">
		<?php echo render_date_input('end_time', 'end_time', $end_time); ?>
	</div>
	<div class="gr-clients <?php echo html_entity_decode($col1); ?>">
		<?php echo render_select('group_clients[]',$group_clients,array('id', 'name'),'group_clients', $group_clientss,array('multiple'  => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="client col-md-5 <?php echo html_entity_decode($hide1); ?>"> 
		<?php echo render_select('clients[]', $clients, array('userid', 'company'), 'clients', $clientss, array('multiple'  => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="col-md-1 checkbox-option-client" >
		<input type="checkbox" id="select-option-client" name="select-option-client" value="1" <?php echo html_entity_decode($check1); ?>>
		<label for="select-option-client"><?php echo _l('select_clients')?></label>
	</div>
	<div class="col-md-6">
		<div class="form-group">
            <label class="control-label" for="type"><?php echo _l('type'); ?></label>
            <select class="selectpicker display-block" data-width="100%" name="formal" data-none-selected-text="<?php echo _l('no_formal'); ?>">
                <option value="1" <?php  if($formal == 1){ echo 'selected'; } ?> ><?php echo _l('coupon').' ('._l('reduced_by_%').')'; ?></option>
                <option value="2" <?php  if($formal == 2){ echo 'selected'; } ?> ><?php echo _l('voucher').' ('._l('reduced_by_amount').')'; ?></option>
            </select>
        </div>
	</div>
	<div class="col-md-6">
		<?php echo render_input('discount', 'discount', $discounts); ?>
	</div>
	<div class="col-md-6">
		<div class="form-group">
            <label class="control-label" for="channel"><?php echo _l('channel'); ?></label>
            <select class="selectpicker display-block" data-width="100%" name="channel" data-none-selected-text="<?php echo _l('no_channel'); ?>">
                <option value="1" <?php if($channel == 1 ){ echo 'selected';}?> >Pos</option>
                <option value="2" <?php if($channel == 2 ){ echo 'selected';}?> >Portal</option>
                <option value="3" <?php if($channel == 3 ){ echo 'selected';}?> >WooCommerce</option>
                <option value="6" <?php if($channel == 6 ){ echo 'selected';}?> >Pre-order</option>
            </select>
        </div>
	</div>
	<div class="col-md-6">
		<div class="form-group" app-field-wrapper="minimum_order_value">
			<label for="minimum_order_value" class="control-label"><?php echo _l('minimum_order_value').' ('.$currency_name.')'; ?></label>
			<input type="text" id="minimum_order_value" name="minimum_order_value" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" class="form-control" value="<?php echo html_entity_decode($minimum_order_value); ?>" aria-invalid="false">
		</div>
	</div>

	<div class="clearfix"></div>
	<div class="modal-footer" >
  		<button id="sm_btn2" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
	</div>
<?php echo form_close(); ?>

</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
