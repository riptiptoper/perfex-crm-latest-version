<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php echo form_open_multipart(admin_url('omni_sales/add_discount_form'),array('id'=>'new_trade_discount_form')); ?>
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
	$channel = '';
	$store = '';
	$voucher = '';
	$minimum_order_value = '';
	if(isset($id)){ 
		echo form_hidden('id', $id); 
		$name_trade_discount = $discount->name_trade_discount;
		$start_time = _d($discount->start_time);
		$end_time = _d($discount->end_time);
		$formal = $discount->formal;
		$discounts = $discount->discount;
		$voucher = $discount->voucher;
		$channel = $discount->channel;
		$store = $discount->store;
		$clientss = explode(',', $discount->clients);
		$group_clientss =  explode(',',$discount->group_clients);
		$group_itemss =  explode(',', $discount->group_items);
		$itemss =  explode(',',$discount->items);
		$minimum_order_value = app_format_money($discount->minimum_order_value,'');

		if(isset($clientss[0])){
			if($clientss[0]!=''){
				if(count($clientss) > 0){
					$check1 = 'checked';
					$hide1 = '';
					$col1 = 'col-md-6';
				}	
			}
		}

		if(isset($itemss[0])){
			if($itemss[0]!=''){
				if(count($itemss) > 0){
					$check2 = 'checked';
					$hide2 = '';
					$col2 = 'col-md-6';
				}	
			}
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
		<h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
		<hr>
	</div>
	<div class="col-md-12">
		<?php echo render_input('name_trade_discount', 'name_trade_discount', $name_trade_discount); ?>
	</div>
	<div class="col-md-6">
		<?php echo render_date_input('start_time', 'start_time', $start_time,array('onblur'=>'check_start_date(this);')); ?>
	</div>
	<div class="col-md-6">
		<?php echo render_date_input('end_time', 'end_time', $end_time); ?>
	</div>
	<div class="gr-clients <?php echo html_entity_decode($col1); ?>">
		<?php echo render_select('group_clients[]',$group_clients,array('id', 'name'),'group_clients', $group_clientss,array('multiple' => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="client col-md-5 <?php echo html_entity_decode($hide1); ?>"> 
		<?php echo render_select('clients[]', $clients, array('userid', 'company'), 'clients', $clientss, array('multiple'  => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="col-md-1 checkbox-option-client" >
		<input type="checkbox" id="select-option-client" name="select-option-client" value="1" <?php echo html_entity_decode($check1); ?>>
		<label for="select-option-client"><?php echo _l('select_clients')?></label>
	</div>

	<div class="gr-items <?php echo html_entity_decode($col2); ?>">
		<?php echo render_select('group_items[]',$group_items,array('id', array('commodity_group_code','name')),'group_items',$group_itemss,array('multiple' => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="item col-md-5 <?php echo html_entity_decode($hide2); ?>"> 
		<?php echo render_select('items[]', $items, array('id', 'label'), 'items', $itemss, array('multiple' => true, 'data-actions-box' => true),array(),'','',false); ?>
	</div>
	<div class="col-md-1 checkbox-option-items" >
		<input type="checkbox" id="select-option-items" name="select-option-items" value="1" <?php echo html_entity_decode($check2); ?>>
		<label for="select-option-items"><?php echo _l('select_items')?></label>
	</div>
	
	<div class="col-md-6">
		<div class="form-group">
            <label class="control-label" for="formal"><?php echo _l('formal'); ?></label>
            <select class="selectpicker display-block" data-width="100%" name="formal" data-none-selected-text="<?php echo _l('no_formal'); ?>">
                <option value="1" <?php  if($formal == 1){ echo 'selected'; } ?> ><?php echo _l('percent_of_product'); ?></option>
                <option value="2" <?php  if($formal == 2){ echo 'selected'; } ?> ><?php echo _l('price'); ?></option>
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
                <option value="4" <?php if($channel == 4 ){ echo 'selected';}?> >Manual</option>
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
	<?php if($store != ''){ ?>
		<div class="store_woo col-md-6">
			<div class="form-group">
	            <label class="control-label" for="store"><?php echo _l('store'); ?></label>
	            <select class="selectpicker display-block" data-width="100%" name="store" data-none-selected-text="<?php echo _l('no_store'); ?>">
	                <?php foreach ($stores as $key => $value) { ?>
	               		<option value="<?php echo html_entity_decode($value['id']); ?>" <?php if($store == $value['id']){ echo 'selected'; } ?> ><?php echo html_entity_decode($value['name_channel']); ?></option>

	                <?php }?>
	            </select>
	        </div>
		</div>
	<?php }else{ ?>
		<div class="store_woo col-md-6 hide">
			<div class="form-group">
	            <label class="control-label" for="store"><?php echo _l('store'); ?></label>
	            <select class="selectpicker display-block" data-width="100%" name="store" data-none-selected-text="<?php echo _l('no_store'); ?>">
	                <option value=""></option>
	            </select>
	        </div>
		</div>
	<?php } ?>	

	<div class="voucher_code col-md-12 hide">
		<?php echo render_input('voucher', 'voucher'); ?>
	</div>

	<div class="clearfix"></div>
	<div class="modal-footer">
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
