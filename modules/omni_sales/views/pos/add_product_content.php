<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<input type="hidden" name="id" value="">
<div class="row">
  <div class="col-md-12">
    <div class="form-group" app-field-wrapper="description">
      <label for="description" class="control-label">
        <small class="req text-danger">* </small>
        <?php echo _l('product_name'); ?>
      </label>
      <input type="text" id="description" name="description" class="form-control" value="">
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="form-group" app-field-wrapper="commodity_code">
    <label for="commodity_code" class="control-label">
      <small class="req text-danger">* </small>
      <?php echo _l('product_code'); ?>
    </label>
    <input type="text" id="commodity_code" name="commodity_code" class="form-control" value="">
  </div>
</div>
<div class="col-md-6">
  <div class="form-group" app-field-wrapper="sku_code">
    <label for="sku_code" class="control-label">
      <small class="req text-danger">* </small>
      SKU
    </label>
    <input type="text" id="sku_code" name="sku_code" class="form-control" value="">
  </div>
</div>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="form-group" app-field-wrapper="quantity">
    <label for="quantity" class="control-label">
      <small class="req text-danger">* </small>
      <?php echo _l('quantity'); ?>
    </label>
    <input type="number" min="1" id="quantity" name="quantity" class="form-control" value="1">
  </div>
</div>
<div class="col-md-6">
  <label for="unit_id" class="control-label">
    <small class="req text-danger">* </small>
    <?php echo _l('unit'); ?>
  </label>
  <select name="unit_id" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
   <option></option>
   <?php 
   foreach ($units as $key => $value) { ?>
    <option value="<?php echo html_entity_decode($value['unit_type_id']) ?>"><?php echo html_entity_decode($value['unit_name']) ?></option>                    
  <?php } ?>  
</select>
</div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group" app-field-wrapper="rate">
      <label for="rate" class="control-label">
        <small class="req text-danger">* </small>
        <?php echo _l('sales_prices'); ?>
      </label>
      <input type="text" id="rate" name="rate" data-type="currency" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" class="form-control" value="">
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group" app-field-wrapper="purchase_price">
      <label for="purchase_price" class="control-label">
        <?php echo _l('purchase_price'); ?>
      </label>
      <input type="text" id="purchase_price" name="purchase_price" data-type="currency" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" class="form-control" value="">
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <label for="tax" class="control-label">
      <?php echo _l('tax'); ?>
    </label>
    <select name="tax" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
     <option></option>
     <?php 
     foreach ($taxes as $key => $value) { ?>
      <option value="<?php echo html_entity_decode($value['id']) ?>"><?php echo html_entity_decode($value['name']); ?></option>                    
    <?php } ?>  
  </select>
</div>
<div class="col-md-6">
 <div class="form-group" app-field-wrapper="commodity_barcode">
  <label for="commodity_barcode" class="control-label">
    <small class="req text-danger">* </small>
    <?php echo _l('commodity_barcode'); ?>
  </label>
  <input type="text" id="commodity_barcode" name="commodity_barcode" class="form-control" value="">
</div>
</div>
</div>
<div class="row">
  <div class="col-md-6">
    <label for="group_id" class="control-label">
      <small class="req text-danger">* </small>
      <?php echo _l('commodity_group'); ?>
    </label>
    <select name="group_id" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
     <option></option>
     <?php 
     foreach ($commodity_groups as $key => $value) { ?>
      <option value="<?php echo html_entity_decode($value['id']) ?>"><?php echo html_entity_decode($value['name']) ?></option>                    
    <?php } ?>  
  </select>
</div>
<div class="col-md-6">
  <label for="warehouse_id" class="control-label">
    <small class="req text-danger">* </small>
    <?php echo _l('warehouses'); ?>
  </label>
  <select name="warehouse_id" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
   <option></option>
   <?php 
   foreach ($warehouse as $key => $value) { ?>
    <option value="<?php echo html_entity_decode($value['warehouse_id']) ?>"><?php echo html_entity_decode($value['warehouse_code'].' - '.$value['warehouse_name']) ?></option>                    
  <?php } ?>  
</select>
<br>
<br>
</div>
</div>
<div class="row">
  <div class="col-md-12">
    <input type="file" id="files" name="file[]" multiple><br/>
    <div id="selectedFiles"></div>
  </div>
</div>

