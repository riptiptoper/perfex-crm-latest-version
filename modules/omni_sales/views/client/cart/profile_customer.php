 
<?php 
if(isset($client)){ ?>
 <div class="row">
  <div class="col-md-4">
   <input type="hidden" name="userid" value="<?php echo html_entity_decode($client->userid); ?>">
   <h4 class="no-mtop">
     <i class="fa fa-user"></i>
     <?php echo _l('customer_details'); ?>
   </h4>
   <hr />
   <?php  echo ( isset($client) ? $client->company : ''); ?><br>
   <?php  echo ( isset($client) ? $client->phonenumber : ''); ?><br>
   <?php echo ( isset($client) ? $client->address : ''); ?><br>
   <?php echo ( isset($client) ? $client->city : ''); ?> <?php echo ( isset($client) ? $client->state : ''); ?><br>
   <?php echo isset($client) ? get_country_short_name($client->country) : ''; ?> <?php echo ( isset($client) ? $client->zip : ''); ?><br>
 </div>
 <div class="col-md-4">
   <h4 class="no-mtop">
     <i class="fa fa-map"></i>
     <?php echo _l('billing_address'); ?>
   </h4>
   <hr />
   <address class="invoice-html-customer-shipping-info">
    <?php echo isset($client) ? $client->billing_street : ''; ?>
    <br><?php echo isset($client) ? $client->billing_city : ''; ?> <?php echo isset($client) ? $client->billing_state : ''; ?>
    <br><?php echo isset($client) ? get_country_short_name($client->billing_country) : ''; ?> <?php echo isset($client) ? $client->billing_zip : ''; ?>
  </address>
</div>
<div class="col-md-4">
  <h4 class="no-mtop">
    <a href="<?php echo site_url('omni_sales/omni_sales_client/client/'.$client->userid); ?>" class="btn btn-primary pull-right go_to_edit_link"><i class="fa fa-edit"></i></a>
    <i class="fa fa-street-view"></i>
    <?php echo _l('shipping_address'); ?>
  </h4>
  <hr />
  <address class="invoice-html-customer-shipping-info">
    <?php echo isset($client) ? $client->shipping_street : ''; ?>
    <br><?php echo isset($client) ? $client->shipping_city : ''; ?> <?php echo isset($client) ? $client->shipping_state : ''; ?>
    <br><?php echo isset($client) ? get_country_short_name($client->shipping_country) : ''; ?> <?php echo isset($client) ? $client->shipping_zip : ''; ?>
  </address>
</div>
</div>
<div class="row">
 <?php
 $price_tax = 0;
 $currency_name = '';
 if(isset($base_currency)){
  $currency_name = $base_currency->name;
}

$cart_empty = 0;
$list_id = '';
if(isset($_COOKIE['cart_id_list'])){
  $list_id = $_COOKIE['cart_id_list'];
  if(!$list_id){
   $cart_empty = 1;
 }
}
$sub_total = 0;
$date = date('Y-m-d');
$discount_price = 0;
$count_item = 0;
?>
<div class="clearfix"></div>
<br><br>    
<?php hooks()->do_action('omni_sale_discount', isset($client) ? $client : '' ); ?> 
<div class="col-md-4">
</div> 
<div class="col-md-4"></div> 
<div class="col-md-4">
  <div id="discount_area">
    <div width="200px">
      <label for="allowed_payment_modes" class="control-label"><?php echo _l('voucher'); ?></label>
      <br />
      <input type="text" class="form-control" name="voucher">
    </div>
    <?php
    $shipping_fee = get_option('omni_portal_shipping_fee');
    echo form_hidden('discount_type', ''); 
    echo form_hidden('discount', '0'); 
    echo form_hidden('discount_total','0'); 
    echo form_hidden('other_discount','0'); 
    echo form_hidden('shipping',$shipping_fee); 
  ?>

  </div> 
</div>      
</div>      
<br><br>     

<div class="invoice accounting-template fr1 <?php if($cart_empty == 1){ echo 'hide'; } ?>">
  <div class="row">

  </div>


  <div class="fr1">
    <div class="table-responsive s_table">
     <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
      <thead>
       <tr>
        <th width="50%" align="right"><?php echo _l('invoice_table_item_heading'); ?></th>
        <th width="5%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
        <th width="10%" align="right"  valign="center"><?php echo _l('tax').' (%)'; ?></th>
        <th width="15%" align="right"  valign="center"><?php echo _l('price').' ('.$currency_name.')'; ?></th>
        <th width="20%" align="right"><?php echo _l('line_total').' ('.$currency_name.')'; ?></th>
      </tr>
    </thead>
    <tbody>
      <?php 
      if($list_id){
        $array_list_id = explode(',',$list_id);
        $list_qty = $_COOKIE['cart_qty_list'];
        $array_list_qty = explode(',',$list_qty); 
        $tax_total_array = [];
        ?>
        <input type="hidden" name="list_id_product" value="<?php echo html_entity_decode($list_id); ?>">
        <input type="hidden" name="list_qty_product" value="<?php echo html_entity_decode($list_qty); ?>">
        <input type="hidden" name="list_group_product" value="<?php echo html_entity_decode($list_group); ?>">
        <input type="hidden" name="list_prices_product" value="<?php echo html_entity_decode($list_prices); ?>">
        <input type="hidden" name="list_tax_product" value="<?php echo html_entity_decode($list_tax); ?>">


        <?php foreach ($array_list_id as $key => $product_id) { ?>
          <tr class="main">
            <td>
              <a href="#">
                <?php 
                $count_item += 1;
                ?>
                <img class="product pic" src="<?php echo $this->omni_sales_model->get_image_items($product_id); ?>">  
                <strong>
                  <?php
                  $prices = 0;
                  $data_product = $this->omni_sales_model->get_product($product_id);
                  if($data_product){
                    $prices = $data_product->rate;
                    echo html_entity_decode($data_product->description.' - '.$data_product->sku_code);
                  }
                  $prices  = 0;
                  $data_prices = $this->omni_sales_model->get_price_channel($product_id,2);
                  if($data_prices){
                    $prices  = $data_prices->prices;
                  }

                  $discount_percent = 0;
                  $price_after_dc = 0;
                  $discountss = $this->omni_sales_model->check_discount($product_id, $date, 2);
                  $array_product = [];

                  if($discountss){
                    $discount_percent = $discountss->discount;
                    $discount_price += ($discount_percent * $prices) / 100;
                    $price_after_dc = $prices-(($discount_percent * $prices) / 100);
                    array_push($array_product, array('product_id' => $product_id, 'name_discount' => $discountss->name_trade_discount, 'price' => ($discount_percent * $prices) / 100 ));
                    echo form_hidden('discount_price', $discount_price);
                  }else{
                    $price_after_dc = $prices;
                  }
                  echo form_hidden('data_log',$array_product);
                  ?>
                </strong>
              </a>
            </td>
            <td align="right" class="middle">
              <strong><?php echo html_entity_decode($array_list_qty[$key]); ?></strong>
            </td>
            <td align="right" class="middle">
              <strong>
                <?php
                $tax_name = '';
                $data_tax = 0;
                $tax_data = $this->omni_sales_model->get_tax_info_by_product($product_id);
                if($tax_data){
                  $tax_name = $tax_data->name.' ('.$tax_data->taxrate.'%)';
                  echo html_entity_decode($tax_name); 
                  $data_tax = $tax_data->taxrate;

                  $tax_value = (((int)$array_list_qty[$key]*$prices) * $data_tax)/100;

                  $array_tax_index = $data_tax.'_'.$tax_data->id;

                  if(isset($tax_total_array[$array_tax_index])){
                    $old_value_tax = $tax_total_array[$array_tax_index]['value'];
                    $tax_total_array[$array_tax_index] = ['value' => ($old_value_tax + $tax_value), 'name' => $tax_name];
                  }
                  else{
                    $tax_total_array[$array_tax_index] = ['value' => $tax_value, 'name' => $tax_name];
                  }
                }
                ?>                                             
              </strong>
            </td>
            <td align="right" class="middle prices_<?php echo html_entity_decode($product_id); ?>">

              <div class="price_w2 hide"><span class="new_prices"></span></br><span class="old_prices"><?php echo app_format_money($prices,''); ?></span>

              </div>

              <div class="price_w1"><span class="new_prices"><?php echo app_format_money($prices,''); ?></span></div>
            </td>
            <td align="right" class="middle">
             <strong class="line_total_<?php echo html_entity_decode($key); ?>">
               <?php
               $line_total = (int)$array_list_qty[$key]*$prices;
               $sub_total += $line_total;
               echo app_format_money($line_total,'');
               $tax = ($line_total * $data_tax)/100;
               $price_tax += $tax;

               ?>

             </strong>
           </td>

           <?php 
         }
         echo form_hidden('tax',$price_tax);
       }else{ $cart_empty = 1; ?>

        <center><?php echo _l('cart_empty'); ?></center>
      <?php  } ?>
    </tbody>
  </table>
</div>

<div class="row">
  <div class="col-md-8 col-md-offset-4">
   <table class="table text-right">
    <tbody>
     <tr id="subtotal">
      <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
      </td>
      <td class="subtotal">
        <?php echo app_format_money($sub_total,''); ?>
        <?php echo form_hidden('sub_total', $sub_total);?>
        <?php $total = $sub_total; ?>
      </td>
    </tr>
    <tr class="discount_area_discount">
      <td>
       <span class="promotions"><?php echo _l('discount'); ?> :</span>
     </td>
     <td class="promotions_discount_price promotions">
     </td>
   </tr>
   <?php
   foreach ($tax_total_array as $tax_item_row) {
    ?>
    <tr class="discount_area_discount">
      <td>
        <span><?php echo html_entity_decode($tax_item_row['name']); ?> :</span>
      </td>
      <td>
        <?php echo app_format_money($tax_item_row['value'],''); ?>
      </td>
    </tr>
    <?php 
  }
  ?>
  <tr class="discount_area_discount hide">
    <td>
     <span class="promotions"><?php echo _l('tax'); ?> :</span>
   </td>
   <td class="promotions_tax_price promotions"><?php 
   echo app_format_money(round($price_tax),''); 
   ?>
 </td>
</tr>
<?php 
  $show_shipping_fee = 'hide';
  if($shipping_fee > 0){
    $show_shipping_fee = '';
  }
 ?>
<tr class="<?php echo html_entity_decode($show_shipping_fee); ?>">
  <td><?php echo _l('omni_shipping_fee'); ?></td>
  <td class="shipping_fee">
    <?php echo app_format_money(round($shipping_fee),''); ?>
  </td>
</tr>
<tr>
  <td>
    <?php echo _l('total'); ?>
  </td>
  <td class="total">

  </td>
  <?php 
  echo form_hidden('total', $total);?>
</tr>
</tbody>
</table>
</div>
</div>



</div>
<div class="row">
  <div class="col-md-6 mtop15">
    <h4 class="no-mtop">
     <i class="fa fa-edit"></i>
     <?php echo _l('notes'); ?>
   </h4>
   <hr />
   <?php echo render_textarea('notes','','');  ?>                        
   <div class="clearfix"></div>
   <br>
 </div>
 <div class="col-md-6 mtop15">
  <h4 class="no-mtop">
   <i class="fa fa-credit-card"></i>
   <?php echo _l('payment_methods'); ?>
 </h4>
 <hr />
 <?php if(count($payment_modes) > 0){ ?>
  <select class="selectpicker"
  name="payment_methods"
  data-width="100%"
  data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
  <?php foreach($payment_modes as $key => $mode){ ?>
   <option value="<?php echo html_entity_decode($mode['id']); ?>" <?php if($key == 0){ echo 'selected'; } ?>><?php echo html_entity_decode($mode['name']); ?></option>
 <?php } ?>
</select>
<?php } ?>
<div class="clearfix"></div>
<br>
<table class="table text-right">
  <tbody>
   <tr>
    <td><span class="bold"><?php echo _l('total_items'); ?> :</span>
    </td>
    <td class="total_items">
      <?php echo html_entity_decode($count_item); ?>
    </td>
    <td><span class="bold"><?php echo _l('total_payable').' ('.$currency_name.')'; ?> :</span>
    </td>
    <td class="total_payable">
    </td>
  </tr>
</tbody>
</table>
</div> 

</div>
<div class="clearfix"></div>
<div class="modal-footer">
  <button id="sm_btn2" type="submit" class="btn btn-primary"><?php echo _l('omni_order'); ?></button>
</div>                
<div class="content fr2 <?php if($cart_empty != 1){ echo 'hide'; } ?>">
  <div class="panel_s">
   <div class="panel-body">
     <div class="col-md-12 text-center">
      <h4><?php echo _l('cart_empty'); ?></h4>                    
    </div>
    <br>
    <br>
    <br>
    <br>
    <div class="col-md-12 text-center">
      <a href="javascript:history.back()" class="btn btn-primary">
       <i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?php echo _l('return_to_the_previous_page'); ?></a>
     </div>
   </div>
 </div>
</div>
</div>
</div>
<?php  }else{ ?>
  <div class="row">
    <div class="col-md-12">
      <center>
        <a href="<?php echo site_url('omni_sales/omni_sales_client/index/1/0'); ?>" class="btn btn-primary"><?php echo _l('return_to_the_previous_page'); ?></a>
      </center>
    </div>
  </div>
<?php   } ?> 
<?php hooks()->do_action('client_pt_footer_js'); ?>
