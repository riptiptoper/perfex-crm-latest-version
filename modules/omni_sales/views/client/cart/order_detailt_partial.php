 <?php hooks()->do_action('head_element_client'); ?>


 <div class="row">
  <div class="<?php if($this->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log') && isset($activity_log) && count($activity_log) > 0){ echo "col-md-8"; } else { echo "col-md-12"; } ?>">
   <div class="row">
     <div class="col-md-6">
      <h5><?php echo _l('order_number');  ?>: #<?php  echo ( isset($order) ? $order->order_number : ''); ?></h5>
      <span><?php echo _l('order_date');  ?>: <?php  echo ( isset($order) ? $order->datecreator : ''); ?></span>
    </div>
    <div class="col-md-6 status_order">
      <div class="col-md-8 reasion text-danger">
       <?php 
       if($status_key == 'canceled'){ 
        if($order->admin_action == 0){
          echo _l('was_canceled_by_you_for_a_reason').': '._l($order->reason); 
        }
        else
        {
          echo _l('was_canceled_by_us_for_a_reason').': '._l($order->reason);  
        } 
      } ?> 
    </div>
    <div class="col-md-3">
     <button class="btn pull-right">
       <?php echo _l($status); ?>
     </button>     
   </div>
   <br>
 </div>



 <div class="clearfix"></div>
 <div class="col-md-12">
  <hr>  
</div>
<br>
<br>
<br>
<div class="clearfix"></div>
<div class="col-md-4">
 <input type="hidden" name="userid" value="<?php echo html_entity_decode($order->userid); ?>">
 <h4 class="no-mtop">
   <i class="fa fa-user"></i>
   <?php echo _l('customer_details'); ?>
 </h4>
 <hr />
 <?php  echo ( isset($order) ? $order->company : ''); ?><br>
 <?php  echo ( isset($order) ? $order->phonenumber : ''); ?><br>
 <?php echo ( isset($order) ? $order->address : ''); ?><br>
 <?php echo ( isset($order) ? $order->city : ''); ?> <?php echo ( isset($order) ? $order->state : ''); ?><br>
 <?php echo isset($order) ? get_country_short_name($order->country) : ''; ?> <?php echo ( isset($order) ? $order->zip : ''); ?><br>
</div>
<div class="col-md-4">
 <h4 class="no-mtop">
   <i class="fa fa-map"></i>
   <?php echo _l('billing_address'); ?>
 </h4>
 <hr />
 <address class="invoice-html-customer-shipping-info">
  <?php echo isset($order) ? $order->billing_street : ''; ?>
  <br><?php echo isset($order) ? $order->billing_city : ''; ?> <?php echo isset($order) ? $order->billing_state : ''; ?>
  <br><?php echo isset($order) ? get_country_short_name($order->billing_country) : ''; ?> <?php echo isset($order) ? $order->billing_zip : ''; ?>
</address>
</div>
<div class="col-md-4">
  <h4 class="no-mtop">
   <i class="fa fa-street-view"></i>
   <?php echo _l('shipping_address'); ?>
 </h4>
 <hr />
 <address class="invoice-html-customer-shipping-info">
  <?php echo isset($order) ? $order->shipping_street : ''; ?>
  <br><?php echo isset($order) ? $order->shipping_city : ''; ?> <?php echo isset($order) ? $order->shipping_state : ''; ?>
  <br><?php echo isset($order) ? get_country_short_name($order->shipping_country) : ''; ?> <?php echo isset($order) ? $order->shipping_zip : ''; ?>
</address>
</div>
</div>
</div>
<?php if($this->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log') &&  isset($activity_log) && count($activity_log) > 0){ ?>
  <div class="col-md-4">
    <div class="no-shadow no-margin activity_log_client custom-bar">
      <div class="activity-feed">
        <?php foreach($activity_log as $log){ ?>
          <div class="feed-item">
            <div class="date">
              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                <?php echo time_ago($log['date']); ?>
              </span>
            </div>
            <div class="text">
              <?php if($log['staffid'] != 0){ ?>
                <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                  <?php echo staff_profile_image($log['staffid'],array('staff-profile-xs-image pull-left mright5'));
                  ?>
                </a>
                <?php
              }
              $additional_data = '';
              if(!empty($log['additional_data'])){
                $additional_data = unserialize($log['additional_data']);
                echo ($log['staffid'] == 0) ? _l($log['description'],$additional_data) : $log['full_name'] .' - '._l($log['description'],$additional_data);
              } else {
                echo $log['full_name'] . ' - ';
                echo _l($log['description']);
              }
              ?>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>


</div>




<div class="row">
 <?php
 $currency_name = '';
 if(isset($base_currency)){
  $currency_name = $base_currency->name;
}
$tax_total_array = [];
$sub_total = 0;
?>





<div class="clearfix"></div>
<br>       
<div class="invoice accounting-template">
  <div class="row">

  </div>


  <div class="fr1">
    <div class="col-md-12">
      <small class="pull-right mbot10 italic"><?php echo _l('omni_currency').': '.$currency_name; ?></small>
    </div>
    <div class="clearfix"></div>
    <div class="table-responsive s_table">
     <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
      <thead>
       <tr>
        <th width="50%" align="center"><?php echo _l('invoice_table_item_heading'); ?></th>
        <th width="10%" align="center" class="qty"><?php echo _l('quantity'); ?></th>
        <th width="20%" align="center"  valign="center"><?php echo _l('price'); ?></th>
        <th width="15%" align="center"  valign="center"><?php echo _l('tax'); ?></th>
        <th width="20%" align="center"><?php echo _l('line_total'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $sub_total = 0; 
      ?>

      <?php foreach ($order_detait as $key => $item_cart) { ?>
        <tr class="main">
          <td>
            <a href="#">
              <img class="product pic" src="<?php echo $this->omni_sales_model->get_image_items($item_cart['product_id']); ?>">  
              <strong>
                <?php   
                echo html_entity_decode($item_cart['product_name']);
                ?>
              </strong>
            </a>
          </td>
          <td align="center" class="middle">
            <?php echo html_entity_decode($item_cart['quantity']); ?>
          </td>
          <td align="center" class="middle">
           <strong><?php 
           echo app_format_money($item_cart['prices'],'');
         ?></strong>
       </td>
       <td align="center" class="middle">
        <?php 
        if($item_cart['tax']){
          $list_tax = json_decode($item_cart['tax']);
          $tax_name = '';
          foreach ($list_tax as $tax_item) {
            $tax_name .= $tax_item->name.' ('.$tax_item->rate.'%)<br>'; 
            $array_tax_index = $tax_item->rate.'_'.$tax_item->id;
            if(isset($tax_total_array[$array_tax_index])){
              $old_value_tax = $tax_total_array[$array_tax_index]['value'];
              $tax_total_array[$array_tax_index] = ['value' => ($old_value_tax + $tax_item->value), 'name' => $tax_item->name.' ('.$tax_item->rate.'%)'];
            }
            else{
              $tax_total_array[$array_tax_index] = ['value' => $tax_item->value, 'name' => $tax_item->name.' ('.$tax_item->rate.'%)'];
            }
          }
          echo html_entity_decode($tax_name);                           
        }
        ?>
      </td>
      <td align="center" class="middle">
       <strong class="line_total_<?php echo html_entity_decode($key); ?>">
         <?php
         $line_total = (int)$item_cart['quantity']*$item_cart['prices'];
         $sub_total += $line_total;
         echo app_format_money($line_total,''); ?>
       </strong>
     </td>
   </tr>
 <?php     } ?>
</tbody>
</table>
</div>

<div class="col-md-8 col-md-offset-4">
 <table class="table text-right">
  <tbody>
   <tr id="subtotal">
    <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
    </td>
    <td class="subtotal">
      <?php echo app_format_money($order->sub_total,''); ?>
    </td>
  </tr>
  <?php
  if($order->discount){
    if($order->discount>0){
      if($order->discount_type == 1){
        $voucher = '';
        if($order->voucher){
          if($order->voucher!=''){
            $voucher = '<span class="text-danger">'.$order->voucher.'</span>';
          }
        }
        ?>
        <tr>
          <td><span class="bold"><?php echo _l('discount').' ('.$voucher.' -'.$order->discount.'%)'; ?> :</span>
          </td>
          <td>
            <?php

            $price_discount = $order->sub_total * $order->discount/100;
            echo '-'.app_format_money($price_discount,''); ?>
          </td>
        </tr>
      <?php  }if($order->discount_type == 2){ 
       ?>
       <tr>
        <td><span class="bold"><?php echo _l('discount'); ?> :</span>
        </td>
        <td>
          <?php
          echo '-'.app_format_money($order->discount,''); ?>
        </td>
      </tr>
      <?php 
    }
  }
} ?>
<?php foreach ($tax_total_array as $tax_item_row) {
  ?>
  <tr>
    <td><span class="bold"><?php echo html_entity_decode($tax_item_row['name']); ?> :</span>
    </td>
    <td>
      <?php echo app_format_money($tax_item_row['value'],''); ?>
    </td>
  </tr>
  <?php 
}
?>
<?php 
if($order->shipping > 0){ ?>
  <tr>
    <td><span class="bold"><?php echo _l('omni_shipping_fee'); ?> :</span>
    </td>
    <td>
     <?php echo app_format_money($order->shipping,''); ?>
   </td>
 </tr>
<?php } ?>
<tr>
  <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
  </td>
  <td class="total">
   <?php echo app_format_money($order->total,''); ?>
 </td>
</tr>
</tbody>
</table> 

</div>


</div>


</div>
</div>

<div class="modal fade" id="chosse" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="add-title"><?php echo _l('please_let_us_know_the_reason_for_canceling_the_order') ?></span>
        </h4>
      </div>
      <?php echo form_open(site_url('omni_sales/omni_sales_client/change_status_order/'.$order->order_number),array('id'=>'form_medicine_category')); ?>         
      <input type="hidden" name="status" value="8">
      <div class="modal-body">
        <div class="col-md-12">
          <div class="input-field">
           <div class="radio-button">
            <input name="cancelReason" checked id="cancel_reason_1" type="radio" value="change_line_items">
            <label for="cancel_reason_1"><?php echo _l('change_line_items'); ?></label>
          </div>
          <div class="radio-button">
            <input name="cancelReason" id="cancel_reason_2" type="radio" value="change_delivery_address">
            <label for="cancel_reason_2"><?php echo _l('change_delivery_address'); ?></label>
          </div><div class="radio-button">
            <input name="cancelReason" id="cancel_reason_3" type="radio" value="high_shipping_cost">
            <label for="cancel_reason_3"><?php echo _l('high_shipping_cost'); ?></label>
          </div>
          <div class="radio-button">
            <input name="cancelReason" id="cancel_reason_4" type="radio" value="delivery_time_is_too_long">
            <label for="cancel_reason_4"><?php echo _l('delivery_time_is_too_long'); ?></label>
          </div>
          <div class="radio-button">
            <input name="cancelReason" id="cancel_reason_5" type="radio" value="misplaced_identical_product">
            <label for="cancel_reason_5"><?php echo _l('misplaced_identical_product'); ?></label>
          </div>
          <div class="radio-button">
            <input name="cancelReason" id="cancel_reason_6" type="radio" value="do_not_want_to_buy_anymore">
            <label for="cancel_reason_6"><?php echo _l('do_not_want_to_buy_anymore'); ?></label>
          </div><div class="radio-button">
            <input name="cancelReason" id="cancel_reason_7" type="radio" value="change_payment_method">
            <label for="cancel_reason_7"><?php echo _l('change_payment_method'); ?></label>
          </div>
          <div class="radio-button">
            <input name="cancelReason" id="cancel_reason_8" type="radio" value="forgot_to_use_discount_code_refund_codes">
            <label for="cancel_reason_8"><?php echo _l('forgot_to_use_discount_code_refund_codes'); ?></label>
          </div>
          <br>
        </div>

      </div>
    </div>
    <div class="clearfix">               
      <br>
      <br>
      <div class="clearfix">               
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-danger" type="submit"><?php echo _l('cancel_order'); ?></button>
      </div>
      <?php echo form_close(); ?>                 
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</div><!-- /.modal -->

<div class="modal fade" id="refund_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="add-title"><?php echo _l('omni_create_return_request') ?></span>
        </h4>
      </div>
      <?php echo form_open(site_url('omni_sales/omni_sales_client/create_return_request/'.$order->order_number),array('id'=>'form_create_return_request')); ?>         
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <h5 class="title-wide"><?php echo _l('omni_return_type'); ?></h5>
            <div class="custom-radio d-flex">
              <label>
                <input type="radio" name="return_type" value="partially" checked>
                <span class="value"><?php echo _l('omni_partially'); ?></span>
              </label>
              <label>
                <input type="radio" name="return_type" value="fully">
                <span class="value"><?php echo _l('omni_fully'); ?></span>
              </label>
            </div>
          </div>
          <div class="col-md-12">
            <h5 class="title-wide"><?php echo _l('omni_return_item'); ?></h5>
            <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
              <thead>
                <tr>
                  <th width="5%" align="center" class="custom-checkbox pbot-4px">
                    <input type="checkbox" onchange="select_all_item(this); return false" class="select-item-checkbox" id="all" value="all">
                    <label for="all"><span></span></label>
                  </th>
                  <th width="50%" align="center"><?php echo _l('invoice_table_item_heading'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($order_detait as $key => $item_cart) { ?>
                  <tr class="main">
                    <td>
                      <p class="custom-checkbox mtop-40px">
                        <input type="checkbox" onchange="select_return_item(this); return false" class="select-item-checkbox" name="item[<?php echo html_entity_decode($key); ?>][select]" id="<?php echo html_entity_decode($item_cart['id']); ?>" value="<?php echo html_entity_decode($item_cart['id']); ?>">
                        <label for="<?php echo html_entity_decode($item_cart['id']); ?>"><span></span></label>
                      </p>
                    </td>
                    <td>
                      <div class="d-flex">
                        <div>
                          <img class="product pic" src="<?php echo $this->omni_sales_model->get_image_items($item_cart['product_id']); ?>">                        
                        </div>
                        <div class="d-grid flex2 mleft10">
                          <strong>
                            <?php   
                            echo html_entity_decode($item_cart['product_name']);
                            ?>
                          </strong>
                          <div class="p-relative">
                            <?php 
                            echo render_input('item['.$key.'][quantity]', 'quantity', $item_cart['quantity'], 'number', ['max' => $item_cart['quantity']], [], '', 'refund_quantity_item p-absolute');
                            ?>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php     } ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <h5 class="title-wide"><?php echo _l('omni_return_reason'); ?></h5>
            <?php echo render_textarea('reason', ''); ?>
          </div>
          <div class="col-md-12">
            <?php 
            $return_policy = get_option('omni_return_policies_information');
            if($return_policy != ''){ ?>
              <label><strong><?php echo _l('omni_return_policies_information'); ?></strong></label>
              <div class="return-policy-fr"><?php echo html_entity_decode($return_policy) ; ?></div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="clearfix">               
        <br>
        <br>
        <div class="clearfix">               
        </div>
        <div class="modal-footer">
          <input type="submit" class="hide" id="return_order_submit_btn">
          <button class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
          <button type="button" class="btn btn-danger" id="return_order_send_rq_btn"><?php echo _l('omni_send_request'); ?></button>
        </div>
        <?php echo form_close(); ?>                 
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div><!-- /.modal -->
<input type="hidden" name="please_select_item" value="<?php echo _l('omni_please_select_items'); ?>">
<input type="hidden" name="please_input_return_reason" value="<?php echo _l('omni_please_input_return_reason'); ?>">