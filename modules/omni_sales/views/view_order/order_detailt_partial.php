

<div class="row">
  <div class="<?php if($this->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log') && isset($activity_log) && count($activity_log) > 0){ echo "col-md-8"; } else { echo "col-md-12"; } ?>">
   <div class="row">
     <div class="col-md-6">
      <h5><?php echo _l('order_number');  ?>: #<?php  echo ( isset($order) ? $order->order_number : ''); ?></h5>
      <p><?php echo _l('order_date');  ?>: <?php  echo ( isset($order) ? $order->datecreator : ''); ?></p>
      <?php 
                    if(isset($order)){
                      $payment_method =  $order->payment_method_title;
                      if($payment_method == ''){
                        $data_multi_payment = $this->omni_sales_model->get_order_multi_payment($order->id);
                        if($data_multi_payment){
                          foreach ($data_multi_payment as $key => $mtpayment) {
                            $payment_method .= $mtpayment['payment_name'].', ';
                          }
                          $payment_method = rtrim($payment_method, ', ');
                        }
                        else{
                          $this->load->model('payment_modes_model');  
                          $data_payment = $this->payment_modes_model->get($order->allowed_payment_modes);
                          if($data_payment){
                            $name = isset($data_payment->name) ? $data_payment->name : '';
                            if($name !=''){
                              $payment_method = $name;              
                            }            
                          }
                        }
                      } 
                      if($payment_method != ''){ ?>
                        <span><?php echo _l('payment_method');  ?>: <span class="text-primary"><?php echo html_entity_decode($payment_method); ?></span></span><br>
                      <?php }   
                    }
                    ?>
    </div>
    <div class="col-md-6 row status_order">
      <div class="row">
        <?php 
        $status = '';
        $status_key = '';
        $status_list = omni_status_list();
        foreach ($status_list as $key => $value) {
          if($value['id'] == $order->status){
            $status = $value['label'];
            $status_key = $value['key'];
            break;
          }
        }
        ?>
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
      <div class="col-md-4">
       <button class="btn pull-right">
         <?php echo _l($status); ?>
       </button>     
     </div>
     <br>
   </div>
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
    <div class="table-responsive s_table col-md-12">
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

<?php if(is_numeric($order->estimate_id) && $order->estimate_id != 0){ ?>
  <?php if(isset($order) && $tax_data['html_currency'] != ''){
    echo html_entity_decode($tax_data['html_currency']);
  } ?>
<?php }else{ ?>
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
<?php } ?>

<?php if((int)$order->adjustment != 0){ ?>
  <tr>
    <td>
      <span class="bold"><?php echo _l('invoice_adjustment').' :'; ?></span>
    </td>
    <td class="adjustment_t">
      <?php echo app_format_money($order->adjustment, ''); ?>
    </td>
  </tr>
<?php } ?>

<?php 
if(isset($order->shipping)){
  if($order->shipping != "0.00"){ ?>
    <tr>
      <td><span class="bold"><?php echo _l('shipping'); ?> :</span>
      </td>
      <td>
        <?php echo app_format_money($order->shipping,''); ?>
      </td>
    </tr>
  <?php   }
}
?>

<tr>
  <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
  </td>
  <td class="total">
   <?php echo app_format_money($order->total,''); ?>
 </td>
</tr>
<?php 
  $invoice_id = '';
  if($order->number_invoice != ''){
    $this->load->model('omni_sales/omni_sales_model');
    $invoice_id = $this->omni_sales_model->get_id_invoice($order->number_invoice); 
  }

  if(is_numeric($invoice_id)){
    $this->load->model('invoices_model');
    $invoice = $this->invoices_model->get($invoice_id);
    $total_paid = sum_from_table(db_prefix().'invoicepaymentrecords',array('field'=>'amount','where'=>array('invoiceid'=>$invoice->id)));
  }
?>

<?php if(is_numeric($invoice_id)){ ?>
  <tr>
    <td><span class="bold"><?php echo _l('invoice_total_paid'); ?></span></td>
    <td>
      <?php echo app_format_money($total_paid, $invoice->currency_name); ?>
    </td>
  </tr>

  <tr>
      <td><span class="<?php if($invoice->total_left_to_pay > 0){echo 'text-danger ';} ?>bold"><?php echo _l('invoice_amount_due'); ?></span></td>
      <td>
         <span class="<?php if($invoice->total_left_to_pay > 0){echo 'text-danger';} ?>">
            <?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency_name); ?>
         </span>
      </td>
   </tr>
 <?php } ?>

</tbody>
</table> 

</div>


</div>


</div>
</div>

