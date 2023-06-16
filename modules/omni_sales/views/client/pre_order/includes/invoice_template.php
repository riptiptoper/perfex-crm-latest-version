<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s<?php if(!isset($invoice) || (isset($invoice) && count($invoices_to_merge) == 0 && (isset($invoice) && !isset($invoice_from_project) && count($expenses_to_bill) == 0 || $invoice->status == Invoices_model::STATUS_CANCELLED))){echo ' hide';} ?>" id="invoice_top_info">
 <div class="panel-body">
 </div>
</div>
<div class="panel_s invoice accounting-template">
 <div class="additional"></div>
 <div class="panel-body">
  <div class="row">
   <div class="col-md-6">
     <h4><?php echo (isset($client) ? $client->company : ''); ?></h4>
     <?php
     if(!isset($invoice_from_project)){ ?>
      <div class="form-group select-placeholder projects-wrapper<?php if((!isset($invoice)) || (isset($invoice) && !customer_has_projects($invoice->clientid))){ echo ' hide';} ?>">
       <label for="project_id"><?php echo _l('project'); ?></label>
       <div id="project_ajax_search_wrapper">
         <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
           <?php
           if(isset($invoice) && $invoice->project_id != 0){
            echo '<option value="'.$invoice->project_id.'" selected>'.get_project_name_by_id($invoice->project_id).'</option>';
          }
          ?>
        </select>
      </div>
    </div>
  <?php } ?>
  <div class="row">
   <div class="col-md-12">
     <hr class="hr-10" />
     <a href="<?php echo site_url('/omni_sales/omni_sales_client/client_info_pre_order/'.$userid); ?>" class="edit_shipping_billing_info" ><i class="fa fa-pencil-square-o"></i></a>
   </div>
   <div class="col-md-6">
    <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
    <address>
     <span class="billing_street">
       <?php $billing_street = (isset($client) ? $client->billing_street : '--'); ?>
       <?php $billing_street = ($billing_street == '' ? '--' :$billing_street); ?>
       <?php echo $billing_street; ?></span><br>
       <span class="billing_city">
         <?php $billing_city = (isset($client) ? $client->billing_city : '--'); ?>
         <?php $billing_city = ($billing_city == '' ? '--' :$billing_city); ?>
         <?php echo $billing_city; ?></span>,
         <span class="billing_state">
           <?php $billing_state = (isset($client) ? $client->billing_state : '--'); ?>
           <?php $billing_state = ($billing_state == '' ? '--' :$billing_state); ?>
           <?php echo $billing_state; ?></span>
           <br/>
           <span class="billing_country">
             <?php $billing_country = (isset($client) ? get_country_short_name($client->billing_country) : '--'); ?>
             <?php $billing_country = ($billing_country == '' ? '--' :$billing_country); ?>
             <?php echo $billing_country; ?></span>,
             <span class="billing_zip">
               <?php $billing_zip = (isset($client) ? $client->billing_zip : '--'); ?>
               <?php $billing_zip = ($billing_zip == '' ? '--' :$billing_zip); ?>
               <?php echo $billing_zip; ?></span>
             </address>
           </div>
           <div class="col-md-6">
            <p class="bold"><?php echo _l('ship_to'); ?></p>
            <address>
             <span class="shipping_street">
               <?php $shipping_street = (isset($client) ? $client->shipping_street : '--'); ?>
               <?php $shipping_street = ($shipping_street == '' ? '--' :$shipping_street); ?>
               <?php echo $shipping_street; ?></span><br>
               <span class="shipping_city">
                 <?php $shipping_city = (isset($client) ? $client->shipping_city : '--'); ?>
                 <?php $shipping_city = ($shipping_city == '' ? '--' :$shipping_city); ?>
                 <?php echo $shipping_city; ?></span>,
                 <span class="shipping_state">
                   <?php $shipping_state = (isset($client) ? $client->shipping_state : '--'); ?>
                   <?php $shipping_state = ($shipping_state == '' ? '--' :$shipping_state); ?>
                   <?php echo $shipping_state; ?></span>
                   <br/>
                   <span class="shipping_country">
                     <?php $shipping_country = (isset($client) ? get_country_short_name($client->shipping_country) : '--'); ?>
                     <?php $shipping_country = ($shipping_country == '' ? '--' :$shipping_country); ?>
                     <?php echo $shipping_country; ?></span>,
                     <span class="shipping_zip">
                       <?php $shipping_zip = (isset($client) ? $client->shipping_zip : '--'); ?>
                       <?php $shipping_zip = ($shipping_zip == '' ? '--' :$shipping_zip); ?>
                       <?php echo $shipping_zip; ?></span>
                     </address>
                   </div>
                 </div>
                 <?php
                 $next_invoice_number = get_option('next_invoice_number');
                 $format = get_option('invoice_number_format');

                 if(isset($invoice)){
                  $format = $invoice->number_format;
                }

                $prefix = get_option('invoice_prefix');

                if ($format == 1) {
                 $__number = $next_invoice_number;
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = '<span id="prefix">' . $invoice->prefix . '</span>';
                 }
               } else if($format == 2) {
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' .date('Y',strtotime($invoice->date)).'</span>/';
                 } else {
                  $__number = $next_invoice_number;
                  $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                }
              } else if($format == 3) {
                if(isset($invoice)){
                 $yy = date('y',strtotime($invoice->date));
                 $__number = $invoice->number;
                 $prefix = '<span id="prefix">'. $invoice->prefix . '</span>';
               } else {
                $yy = date('y');
                $__number = $next_invoice_number;
              }
            } else if($format == 4) {
              if(isset($invoice)){
               $yyyy = date('Y',strtotime($invoice->date));
               $mm = date('m',strtotime($invoice->date));
               $__number = $invoice->number;
               $prefix = '<span id="prefix">'. $invoice->prefix . '</span>';
             } else {
              $yyyy = date('Y');
              $mm = date('m');
              $__number = $next_invoice_number;
            }
          }


          ?>
        </div>
        <div class="col-md-6">


         <div class="form-group mbot15 select-placeholder">
          <br>
          <label for="allowed_payment_modes" class="control-label"><?php echo _l('payment_methods'); ?></label>
          <br />
          <?php if(count($payment_modes) > 0){ ?>
            <select class="selectpicker"
            data-toggle="<?php echo $this->input->get('allowed_payment_modes'); ?>"
            name="allowed_payment_modes[]"
            data-actions-box="true"
            multiple="true"
            data-width="100%"
            data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
            <?php foreach($payment_modes as $mode){
             $selected = '';
             if(isset($pre_order)){
               if($pre_order->allowed_payment_modes != ''){
                $inv_modes = explode(',',$pre_order->allowed_payment_modes);
                if(is_array($inv_modes)) {
                 foreach($inv_modes as $_allowed_payment_mode){
                   if($_allowed_payment_mode == $mode['id']){
                     $selected = ' selected';
                   }
                 }
               }
             }
           } else {
             if($mode['selected_by_default'] == 1){
              $selected = ' selected';
            }
          }
          ?>
          <option value="<?php echo $mode['id']; ?>"<?php echo $selected; ?>><?php echo $mode['name']; ?></option>
        <?php } ?>
      </select>
    <?php } else { ?>
      <p><?php echo _l('invoice_add_edit_no_payment_modes_found'); ?></p>
      <a class="btn btn-info" href="<?php echo admin_url('paymentmodes'); ?>">
        <?php echo _l('new_payment_mode'); ?>
      </a>
    <?php } ?>
  </div>

  <div class="row">
    <div class="col-md-6">
     <?php
     $currency_attr = array('disabled'=>true,'data-show-subtext'=>true);
     $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

     foreach($currencies as $currency){
       if($currency['isdefault'] == 1){
         $currency_attr['data-base'] = $currency['id'];
       }
       if(isset($pre_order)){
        if($currency['id'] == $pre_order->currency){
         $selected = $currency['id'];
       }
     } else {
       if($currency['isdefault'] == 1){
         $selected = $currency['id'];
       }
     }
   }
   $currency_attr = hooks()->apply_filters('invoice_currency_attributes',$currency_attr);
   ?>
   <?php echo render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
 </div>
 <div class="col-md-6">
  <?php $duedate = (isset($pre_order)) ? _d($pre_order->duedate) : _d(date('Y-m-d'));
  echo render_date_input('duedate','omni_expiration_date', $duedate); ?>
</div>
</div>
</div>
</div>
</div>
<div class="panel-body mtop10">
  <div class="row">
    <div class="col-md-6">
      <?php $this->load->view('client/pre_order/includes/item_select'); ?>
    </div>
    <div class="col-md-6">
      <?php 
          echo render_input('voucher','voucher'); 
       ?>
       <input type="hidden" name="discount_voucher" value="">
    </div>
  </div>
  <div class="table-responsive s_table">

   <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
    <thead>
     <tr>
      <th></th>
      <th width="20%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
      <th width="25%" align="left"><?php echo _l('invoice_table_item_description'); ?></th>
      <th width="15%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
      <th width="15%" align="right"><?php echo _l('invoice_table_rate_heading'); ?></th>
      <th width="15%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
      <th width="10%" align="right"><?php echo _l('invoice_table_amount_heading'); ?></th>
      <th align="center"><i class="fa fa-cog"></i></th>
    </tr>
  </thead>
  <tbody>
   <tr class="main">
    <td></td>
    <td>
     <input type="hidden" name="product_id">
     <textarea name="description" class="form-control" rows="4" placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
   </td>
   <td>
     <textarea name="long_description" rows="4" class="form-control" placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
   </td>
   <td>
     <div class="form-group">
      <div class="input-group quantity">
        <input type="number" class="form-control" name="quantity" min="0" value="1">
        <span class="input-group-addon unit"><?php echo _l('unit'); ?></span>
      </div>
    </div>
  </td>
  <td>
   <input name="rate" class="form-control" readonly placeholder="<?php echo _l('item_rate_placeholder'); ?>">
 </td>
 <td>
  <input name="tax" class="form-control" readonly placeholder="<?php echo _l('tax'); ?>">
</td>
<td>
  <span class="amount"></span>
</td>
<td>
 <?php
 $new_item = 'undefined';
 if (isset($pre_order)) {
  $new_item = true;
}
?>
<button type="button" onclick="add_item_to_table(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
</td>
</tr>
<?php if (isset($pre_order) || isset($add_items)) {
  $i               = 1;
  $items_indicator = 'newitems';
  if (isset($pre_order)) {
    $add_items       = $pre_order->items;
    $items_indicator = 'items';
  }
  foreach ($add_items as $item) {

    $manual    = false;
    $table_row = '<tr class="sortable item">';
    $table_row .= '<td class="dragger">';
    if (!is_numeric($item['quantity'])) {
      $item['quantity'] = 1;
    }

    $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
    $amount = $item['prices'] * $item['quantity'];
    $amount = app_format_number($amount);
        // order input
    $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
    $table_row .= '</td>';
    $table_row .= '<td class="bold description"><input type="hidden" name="' . $items_indicator . '[' . $i . '][id]" value="'.$item['id'].'">';
    $table_row .= '<input type="hidden" name="' . $items_indicator . '[' . $i . '][product_id]" value="'.$item['product_id'].'">';
    $table_row .= '<textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['product_name']) . '</textarea></td>';
    $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';


    $product_data = $this->omni_sales_model->get_product($item['product_id']);

    $tax_name = '';
    $taxrate = '';
    $taxid = '';
    $tax_data = $this->omni_sales_model->get_tax($product_data->tax);
    if($tax_data != ''){
      $tax_name = $tax_data->name.' ('.$tax_data->taxrate.'%)';
      $taxrate = $tax_data->taxrate;
      $taxid = $product_data->tax;
    }else{
      $tax = $this->omni_sales_model->get_tax_info_by_product($product_data->tax);
      if($tax){
        $tax_name = $tax->name.' ('.$tax->taxrate.'%)';
        $taxrate = $tax->taxrate;
        $taxid = $tax->taxrate;
      }
    }


    $unit_name = '';
    if($product_data){
      $data_unit = $this->omni_sales_model->get_unit($product_data->unit_id);
      if($data_unit){
        $unit_name = $data_unit->unit_name;
      }          
    }

    $table_row .= '<td><div class="form-group">';
    $table_row .= '<div class="input-group quantity">';
    $table_row .= '<input type="number" class="form-control" data-quantity onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][qty]" min="0" value="' . $item['quantity'] . '">';
    $table_row .= '<span class="input-group-addon unit">' . $unit_name . '</span>';
    $table_row .= '</div>';
    $table_row .= '</div></td>';


    $table_row .= '<td class="rate"><input data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['prices'] . '" class="form-control"></td>';

    $table_row .= '<td><input type="text" class="form-control taxname" readonly name="' . $items_indicator . '[' . $i . '][taxname]" value="' . $tax_name . '" />';
    $table_row .= '<input type="hidden" class="taxid" name="' . $items_indicator . '[' . $i . '][taxid]" value="' . $taxid . '" />';
    $table_row .= '<input type="hidden" class="taxrate" name="' . $items_indicator . '[' . $i . '][taxrate]" value="' . $taxrate . '" /></td>';

    $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
    $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
    if (isset($item['task_id'])) {
      if (!is_array($item['task_id'])) {
        $table_row .= form_hidden('billed_tasks[' . $i . '][]', $item['task_id']);
      } else {
        foreach ($item['task_id'] as $task_id) {
          $table_row .= form_hidden('billed_tasks[' . $i . '][]', $task_id);
        }
      }
    } else if (isset($item['expense_id'])) {
      $table_row .= form_hidden('billed_expenses[' . $i . '][]', $item['expense_id']);
    }
    $table_row .= '</tr>';
    echo html_entity_decode($table_row);
    $i++;
  }
}
?>
</tbody>
</table>


</div>
<div class="col-md-8 col-md-offset-4">
 <table class="table text-right">
  <tbody>
    <tr>
      <td colspan="2">
      </td>
    </tr>
    <tr>
      <td><span class="bold"><?php echo _l('sub_total'); ?> :</span>
      </td>
      <td id="sub_total">
      </td>
    </tr>
    <tr id="discount_area">
       <td><span class="bold"><?php echo _l('discount'); ?> :</span>
         <input type="hidden" name="discount">
      </td>
      <td id="discount">
      </td>
    </tr>
    <?php 
    $hide_shipping = '';
    $shipping_fee = get_option('omni_portal_shipping_fee');
    if($shipping_fee == 0){
      $hide_shipping = 'hide';
    }
    ?>
    <tr class="<?php echo html_entity_decode($hide_shipping) ?>">
      <td><span class="bold"><?php echo _l('omni_shipping_fee'); ?> :</span>
         <input type="hidden" name="shipping" value="<?php echo html_entity_decode($shipping_fee); ?>">
      </td>
      <td id="shipping_fee">
      </td>
    </tr>
    <tr>
      <td><span class="bold"><?php echo _l('total'); ?> :</span>
      </td>
      <td id="total">
      </td>
    </tr>
  </tbody>
</table>
</div>
</div>
<div class="row">
  <div class="col-md-12 mtop15">
   <div class="panel-body bottom-transaction">
    <?php $value = (isset($invoice) ? $invoice->clientnote : get_option('predefined_clientnote_invoice')); ?>
    <?php echo render_textarea('clientnote','invoice_add_edit_client_note',$value,array(),array(),'mtop15'); ?>
    <div class="btn-bottom-toolbar text-right">
    <hr>
      <button class="btn-tr btn btn-primary mleft10 create_pre_order_btn">
        <?php echo _l('omni_create_pre_order'); ?>
      </button>
    </div>

  </div>
  <div class="btn-bottom-pusher"></div>
</div>
</div>
</div>
<input type="hidden" name="unit_text" value="<?php echo _l('unit'); ?>">
