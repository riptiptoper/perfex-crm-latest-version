<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php 
$group_product_id = '';
$product_id = '';
?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
      <div class="clearfix"></div><br>
      <div class="row">
        <div class="col-md-12">
         <h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
         <hr>
       </div>
     </div>
     <?php if(has_permission('omni_order_list', '', 'create') || is_admin()){ ?>
       <div class="row"> 
        <div class="col-md-3"> 
          <a class="btn btn-primary pull-left mright10" href="<?php echo admin_url('omni_sales/order_manual'); ?>">
            <?php echo _l('create_order'); ?>                
          </a>
          <a href="<?php echo admin_url('omni_sales/order_return'); ?>"class="btn btn-info pull-left mright10 ">
            <?php echo _l('omni_create_return_order'); ?>
          </a>
        </div>
      </div>
    <?php } ?>

    <br>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-3">
        <?php echo render_date_input('start_date','start_date',''); ?>
      </div>
      <div class="col-md-3">
        <?php echo render_date_input('end_date','end_date',''); ?>
      </div>

      <?php if(is_admin()){ ?>
        <div class="col-md-3">
          <div class="form-group">
            <label class="control-label" for="seller"><?php echo _l('seller'); ?></label>
            <select class="selectpicker display-block" data-width="100%" name="seller" data-none-selected-text="<?php echo _l('no_seller'); ?>" data-live-search="true">
              <option value=""></option>
              <?php foreach ($staff as $key => $value) { ?>
                <option value="<?php echo html_entity_decode($value['staffid']); ?>"><?php echo html_entity_decode($value['lastname'].' '.$value['firstname']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      <?php } ?>

      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="channel"><?php echo _l('channel'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="channel" data-none-selected-text="<?php echo _l('no_channel'); ?>" data-live-search="true">
            <option value=""></option>
            <option value="1">Pos</option>
            <option value="2">Portal</option>
            <option value="3"><?php echo _l('omni_woocommerce'); ?></option>
            <option value="4"><?php echo _l('omni_manual'); ?></option>
            <option value="6"><?php echo _l('omni_pre_order'); ?></option>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="invoice"><?php echo _l('invoice'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="invoice" data-none-selected-text="<?php echo _l('no_invoice'); ?>" data-live-search="true">
            <option value=""></option>
            <?php foreach ($invoices as $key => $value) { ?>
              <?php  
              $_invoice_number = str_pad($value['number'], get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
              ?>
              <option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($prefix); ?>
              <?php echo html_entity_decode($_invoice_number); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="customer"><?php echo _l('customer'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="customer" data-none-selected-text="<?php echo _l('no_customer'); ?>" data-live-search="true">
            <option value=""></option>
            <?php foreach ($customers as $key => $value) { ?>
              <option value="<?php echo html_entity_decode($value['userid']); ?>"><?php echo html_entity_decode($value['company']); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="status"><?php echo _l('status'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="status" data-none-selected-text="<?php echo _l('no_status'); ?>" data-live-search="true">
            <option value=""></option>
            <?php foreach(omni_status_list() as $item){ ?>
              <option value="<?php echo html_entity_decode($item['id']);?>"><?php echo html_entity_decode($item['label']);?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-md-3">
         <div class="form-group">
          <label class="control-label" for="order_type"><?php echo _l('omni_order_type'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="order_type" data-none-selected-text="<?php echo _l('no_order_type'); ?>" data-live-search="true">
            <option value=""></option>
            <option value="sale_order"><?php echo _l('omni_sale_order');?></option>
            <option value="return_order"><?php echo _l('omni_return_order');?></option>
          </select>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <table class="table table-order_list scroll-responsive">
      <thead>
        <th>ID#</th>
        <th><?php echo _l('order_number'); ?></th>
        <th><?php echo _l('order_date'); ?></th>
        <th><?php echo _l('customer'); ?></th>
        <th><?php echo _l('group_customer'); ?></th>
        <th><?php echo _l('omni_order_type'); ?></th>
        <th><?php echo _l('payment_method'); ?></th>
        <th><?php echo _l('channel'); ?></th>
        <th><?php echo _l('status'); ?></th>
        <th><?php echo _l('invoice'); ?></th>
        <th><?php echo _l('options'); ?></th>
      </thead>
      <tbody></tbody>
      <tfoot>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>      
       <td></td>      
       <td></td>      
     </tfoot>
   </table>

 </div>
</div>
</div>
</div>



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
    <?php echo form_open(admin_url('omni_sales/add_product'),array('id'=>'form_add_product')); ?>	            
    <div class="modal-body">
      <div class="row content">

      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('confirmed'); ?></button>
    </div>
    <?php echo form_close(); ?>	 


  </div>
</div>
</div>


<div class="modal fade" id="view_order" tabindex="-1" role="dialog">
  <div class="modal-dialog">
   <div class="modal-content">


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">
        <span class="add-title"><?php echo _l('add_product'); ?></span>
        <span class="update-title hide"><?php echo _l('update_product'); ?></span>
      </h4>
    </div>
    <?php echo form_open(admin_url('omni_sales/add_product'),array('id'=>'form_add_product')); ?>	            
    <div class="modal-body">
      <div class="row" id="content_order">

      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('confirmed'); ?></button>
    </div>
    <?php echo form_close(); ?>	 


  </div>
</div>
</div>


<?php init_tail(); ?>
</body>
</html>
