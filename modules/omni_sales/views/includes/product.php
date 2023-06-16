<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
$group_product_id = '';
$product_id = '';
?>

<div class="row dflex">
  <div class="col-sm-4 pright0">
    <a href="#" onclick="add_product(); return false;" class="btn btn-info pull-left mbot10">
      <?php echo _l('add'); ?>
    </a>

  </div>
  <div class="col-sm-8">

    <div class="position-relative pull-right">
      <a href="Javascript:void(0);" id="toggle_popup_woo" class="btn btn-success display-block pull-right mleft15 mbot10"><i class="fa fa-download"></i><?php echo ' '._l('download').' '; ?><i class="fa fa-caret-down"></i>
      </a>
      <ul id="popup_woo" class="dropdown-menu dropdown-menu-right position-absolute">
        <li>
          <a href="#" onclick="sync_store(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" class="btn btn-success pull-right display-block orders-woo" data-toggle="tooltip" data-placeme="top" data-original-title="<?php echo _l("sync_from_the_system_to_the_store") ?>"><i class="fa fa-first-order" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('omni_order'); ?>
        </a>
      </li>
      <li>
        <a href="#" data-id="<?php echo html_entity_decode($id); ?>" class="btn btn-success pull-right display-block sync_products_from_woo" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l("sync_products_from_store") ?>"><i class="fa fa-forward"></i><?php echo ' '._l('product'); ?></a>
      </li>
    </ul>
  </div>



  <div class="position-relative pull-right">
    <a href="Javascript:void(0);" id="toggle_popup_crm" class="btn btn-info display-block pull-right"><i class="fa fa-refresh"></i><?php echo ' '._l('sync_to').' '; ?><i class="fa fa-caret-down"></i>
    </a>
    <ul id="popup_approval" class="dropdown-menu dropdown-menu-right">
      <li>
        <a href="#" onclick="sync_all(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-original-title="<?php echo _l("sync_all") ?>" class="btn btn-info display-block"><i class="fa fa fa-refresh  " data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('sync_all'); ?></a>
      </li>
      <li>
        <a href="#" onclick="sync_decriptions_synchronization(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" class="btn btn-info display-block" data-toggle="tooltip" data-placeme="top" data-original-title="<?php echo _l("sync_decriptions") ?>" ><i class="fa fa-info" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('long_decriptions'); ?>
        </a>
      </li>
      <li>
        <a href="#" onclick="sync_images_synchronization(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l("sync_images") ?>"  class="btn btn-info display-block"><i class="fa fa-picture-o" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('images'); ?></a>
      </li>

      <li>
        <a href="#" onclick="sync_price(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-original-title="<?php echo _l("sync_price") ?>" class="btn btn-info display-block"><i class="fa fa fa-money" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('sync_price'); ?></a>
      </li>

      <li>
        <a href="#" onclick="sync_inventory_synchronization(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-original-title="<?php echo _l("sync_from_store") ?>" class="btn btn-info display-block"><i class="fa fa-snowflake-o" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('inventory_sync'); ?></a>
      </li>

      <li>
        <a href="#" onclick="sync_product_name(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-original-title="<?php echo _l("sync_from_store") ?>" class="btn btn-info display-block"><i class="fa fa-sun-o" data-toggle="dropdown" aria-expanded="false"></i><?php echo ' '._l('product_name'); ?></a>
      </li>

      <li>
        <a href="#" onclick="sync_short_description(this); return false;" data-id="<?php echo html_entity_decode($id); ?>" data-toggle="tooltip" data-original-title="<?php echo _l("sync_from_store") ?>" class="btn btn-info display-block"><i class="fa fa-ticket"></i><?php echo ' '._l('short_description'); ?></a>
      </li>
    </ul>
  </div>
  <a href="#" onclick="price_update(this); return false;" class="btn btn-info pull-right mbot10 mright15 " data-id="<?php echo html_entity_decode($id); ?>"><i class="fa fa-pencil" aria-hidden="true"></i>
      <?php echo ' '._l('update_price'); ?>
  </a>
</div>
</div>

<br>



<div id="popup_confirm"></div>
<div id="box-loadding"></div>


<a href="#" onclick="staff_bulk_actions(); return false;"  data-toggle="modal" data-table=".table-product-woocommerce" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>                   
<?php
$table_data = array(
 '<input type="checkbox" id="mass_select_all" data-to-table="product-woocommerce">',
 _l('product_code'),
 _l('product_name'),
 _l('price'),
 _l('price_on_store'),
 _l('options'),
);

render_datatable($table_data,'product-woocommerce',
  array('customizable-table'),
  array(
    'proposal_sm' => 'proposal_sm',
    'id'=>'table-product-woocommerce',
    'data-last-order-identifier'=>'product-woocommerce',
    'data-default-order'=>get_table_last_order('product-woocommerce'),
  )); ?>

  <div class="row">
    <div class="col-md-12">
      <a href="<?php echo admin_url('omni_sales/omni_sales_channel'); ?>" class="btn btn-danger"><?php echo _l('close'); ?></a>
    </div>
  </div>
  <?php echo form_hidden('check'); ?>
  <?php echo form_hidden('check_product'); ?>
  <div class="modal fade" id="chose_product" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">
            <span class="add-title"><?php echo _l('add_product'); ?></span>
            <span class="update-title hide"><?php echo _l('update_product'); ?></span>
          </h4>
        </div>
        <?php echo form_open(admin_url('omni_sales/add_product_channel_wcm'),array('id'=>'form_add_product')); ?>             
        <div class="modal-body">
         <div class="row">
          <input type="hidden" name="woocommere_store_id" value="<?php echo html_entity_decode($id); ?>">
          <div class="col-md-12">
            <?php 
            echo render_select('group_product_id',$group_product,array('id',array('commodity_group_code','name')),'group_product',$group_product_id,array('onchange'=>'get_list_product(this);'));
            ?>
          </div>

          <div class="col-md-12">
           <div class="form-group" app-field-wrapper="product_id">
            <label for="product_id" class="control-label"><?php echo _l('product'); ?></label>
            <select id="product_id" name="product_id[]" class="selectpicker" multiple  data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" data-actions-box="true" tabindex="-98">
              <option value=""></option>
              <?php foreach ($products as $key => $value){ ?>
                <option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['description']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="col-md-12 pricefr hide">
          <?php 
          $arrAtt = array();
          $arrAtt['data-type']='currency';
          echo render_input('prices','prices','','text',$arrAtt);
          ?>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>                 
  </div>
</div>
</div>


<div class="modal bulk_actions" id="product-woocommerce_bulk_actions" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
   <div class="modal-content">
    <div class="modal-header">
     <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   </div>
   <div class="modal-body">
     <?php if(has_permission('warehouse','','delete') || is_admin()){ ?>
       <div class="checkbox checkbox-danger">
        <input type="checkbox" name="mass_delete" id="mass_delete">
        <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
      </div>

    <?php } ?>
  </div>
  <div class="modal-footer">
   <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

   <?php if(has_permission('warehouse','','delete') || is_admin()){ ?>
     <a href="#" class="btn btn-info" onclick="omi_sales_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
   <?php } ?>
 </div>
</div>

</div>

</div>
<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
