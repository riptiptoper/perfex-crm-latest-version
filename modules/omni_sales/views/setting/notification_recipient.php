<?php
$omni_allow_showing_shipment = get_option('omni_allow_showing_shipment_in_public_link');
echo form_open(site_url('omni_sales/save_setting/notification_recipient'),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form')); ?>
<h4><?php echo _l('diary_sync'); ?></h4>
<hr>
<div class="row">
  <div class="col-md-12">
    <a href="<?php echo admin_url('omni_sales/clear_diary_sync_data'); ?>" class="btn btn-info mbot15 _delete"><?php echo _l('clear_data'); ?></a> <label class="text-danger"><?php echo _l('clear_data_note'); ?></label>
  </div>
</div>
<?php echo render_input('number_of_days_to_save_diary_sync','number_of_days_to_save_diary_sync', $number_of_days_to_save_diary_sync, 'number'); ?>
<div class="form-group">
  <div class="checkbox checkbox-primary">
    <input type="checkbox" id="omni_sales_invoice_setting" name="omni_sales_invoice_setting" 
    <?php if($invoice_sync_configuration == 1){ 
    	echo 'checked="" value="0"';
    }else{
    	echo 'value="1"';
    } ?> >
    <label for="omni_sales_invoice_setting"><?php echo _l('enable_sync_auto') ?>
  </label>
</div>
</div>	
<br>
<h4><?php echo _l('order'); ?></h4>
<hr>
<div class="form-group">
  <div class="checkbox checkbox-primary">
    <input type="checkbox" id="omni_allow_showing_shipment_in_public_link" name="omni_allow_showing_shipment_in_public_link" <?php echo (($omni_allow_showing_shipment && $omni_allow_showing_shipment == 1) ? 'checked' : '') ?> value="1" >
    <label for="omni_allow_showing_shipment_in_public_link"><?php echo _l('omni_allow_showing_shipment_in_public_link') ?>
  </label>
</div>
</div>  
<?php
 echo render_select('staff[]',$staffs ,array('staffid', array('firstname','lastname')),'notification_recipients',$staff, array('multiple' => true, 'data-actions-box' => true),array(),'','',false); 
 ?>
<?php echo render_input('omni_pos_shipping_fee', 'omni_default_shipping_fee_for_pos', get_option('omni_pos_shipping_fee')); ?>
<?php echo render_input('omni_portal_shipping_fee', 'omni_default_shipping_fee_for_client_portal', get_option('omni_portal_shipping_fee')); ?>
<?php echo render_input('omni_manual_shipping_fee', 'omni_default_shipping_fee_for_manual_order', get_option('omni_manual_shipping_fee')); ?>
<?php
$status_allowed_to_sync = [];
$status_allowed_to_sync_data = get_option('omni_order_statuses_are_allowed_to_sync');
if($status_allowed_to_sync_data != ''){
  $status_allowed_to_sync = explode(',',$status_allowed_to_sync_data);      
}
echo render_select('omni_order_statuses_are_allowed_to_sync[]',get_list_woo_status() ,array('id', 'label'),'omni_order_statuses_are_allowed_to_sync', $status_allowed_to_sync, array('multiple' => true, 'data-actions-box' => true),array(),'','',false);?> 
<br>

<h4><?php echo _l('omni_order_returns'); ?></h4>
<hr>
<div class="row">
  <div class="col-md-12">
    <?php echo render_input('omni_return_order_prefix', 'omni_return_order_prefix', get_option('omni_return_order_prefix')); ?>
  </div>
  <div class="col-md-12">
    <div class="form-group" app-field-wrapper="omni_return_request_within_x_day">
      <label class="no-margin font-bold h5-color"><?php echo _l('omni_return_request_must_be_placed_within_X_days_after_the_delivery_date') ?></label>
      <input type="number" min="0" max="100" id="omni_return_request_within_x_day" name="omni_return_request_within_x_day" class="form-control" value="<?php echo get_option('omni_return_request_within_x_day'); ?>">
    </div>
  </div>
  <div class="col-md-12">
   <div class="form-group" app-field-wrapper="omni_fee_for_return_order">
    <label class="no-margin font-bold h5-color"><?php echo _l('omni_fee_for_return_order') ?></label>
    <input type="number" min="0" id="omni_fee_for_return_order" name="omni_fee_for_return_order" class="form-control" value="<?php echo get_option('omni_fee_for_return_order'); ?>">
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input type="checkbox" id="omni_refund_loyaty_point" name="omni_refund_loyaty_point" <?php if(get_option('omni_refund_loyaty_point') == 1 ){ echo 'checked';} ?> value="1">
        <label for="omni_refund_loyaty_point" data-toggle="tooltip" title="" data-original-title="<?php echo _l('omni_refund_loyalty_point_tooltip'); ?>"><?php echo _l('omni_refund_loyalty_point'); ?>
      </label>
    </div>
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-12">
    <?php echo render_textarea('omni_return_policies_information', 'omni_return_policies_information', get_option('omni_return_policies_information'), array(), array(), '', 'tinymce'); ?>
  </div>
</div>

<br>

<h4><?php echo _l('omni_client_portal'); ?></h4>
<hr>
<div class="row">
  <div class="col-md-12">
    <div class="form-group">
      <div class="checkbox checkbox-primary">
        <input type="checkbox" id="omni_display_shopping_cart" name="omni_display_shopping_cart" <?php if(get_option('omni_display_shopping_cart') == 1 ){ echo 'checked';} ?> value="1">
        <label for="omni_display_shopping_cart" ><?php echo _l('omni_display_shopping_cart'); ?>
      </label>
    </div>
  </div>
</div>
</div>


<hr>

<button class="btn btn-primary pull-right"><?php echo _l('save'); ?></button>
<?php echo form_close(); ?>
