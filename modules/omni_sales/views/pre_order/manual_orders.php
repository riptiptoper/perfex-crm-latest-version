<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
         <h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
         <hr>
       </div>
     </div>
     <!-- Main content -->
     <div class="row">
      <div class="col-md-12">
        <?php
        echo form_open($this->uri->uri_string(), array('id' => 'order-form', 'class' => '_transaction_form order-form')); ?>
        <div class="panel-body">
          <input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
          <div class="row">
           <div class="col-md-6">
            <div class="row">
              <div class="col-md-12 form-group">
                <label for="customer"><?php echo _l('client'); ?></label>
                <select name="customer" id="customer" class="selectpicker"data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                  <option value=""></option>
                  <?php foreach ($customers as $s) { ?>
                    <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if (isset($order) && $order->userid == $s['userid']) {echo 'selected';}?>><?php echo html_entity_decode($s['company']); ?></option>
                  <?php }?>
                </select>
              </div>

              <div class="col-md-6">
                <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
                <address>
                 <span class="billing_street">
                   <?php
                   $billing_street = (isset($order) ? $order->billing_street : '--');?>
                   <?php $billing_street = ($billing_street == '' ? '--' : $billing_street);?>
                   <?php echo html_entity_decode($billing_street); ?></span><br>
                   <span class="billing_city">
                     <?php $billing_city = (isset($order) ? $order->billing_city : '--');?>
                     <?php $billing_city = ($billing_city == '' ? '--' : $billing_city);?>
                     <?php echo html_entity_decode($billing_city); ?></span>,
                     <span class="billing_state">
                       <?php $billing_state = (isset($order) ? $order->billing_state : '--');?>
                       <?php $billing_state = ($billing_state == '' ? '--' : $billing_state);?>
                       <?php echo html_entity_decode($billing_state); ?></span>
                       <br/>
                       <span class="billing_country">
                         <?php $billing_country = (isset($order) ? get_country_short_name($order->billing_country) : '--');?>
                         <?php $billing_country = ($billing_country == '' ? '--' : $billing_country);?>
                         <?php echo html_entity_decode($billing_country); ?></span>,
                         <span class="billing_zip">
                           <?php $billing_zip = (isset($order) ? $order->billing_zip : '--');?>
                           <?php $billing_zip = ($billing_zip == '' ? '--' : $billing_zip);?>
                           <?php echo html_entity_decode($billing_zip); ?></span>
                         </address>
                       </div>
                       <div class="col-md-6">
                        <p class="bold"><?php echo _l('ship_to'); ?></p>
                        <address>
                         <span class="shipping_street">
                           <?php $shipping_street = (isset($order) ? $order->shipping_street : '--');?>
                           <?php $shipping_street = ($shipping_street == '' ? '--' : $shipping_street);?>
                           <?php echo html_entity_decode($shipping_street); ?></span><br>
                           <span class="shipping_city">
                             <?php $shipping_city = (isset($order) ? $order->shipping_city : '--');?>
                             <?php $shipping_city = ($shipping_city == '' ? '--' : $shipping_city);?>
                             <?php echo html_entity_decode($shipping_city); ?></span>,
                             <span class="shipping_state">
                               <?php $shipping_state = (isset($order) ? $order->shipping_state : '--');?>
                               <?php $shipping_state = ($shipping_state == '' ? '--' : $shipping_state);?>
                               <?php echo html_entity_decode($shipping_state); ?></span>
                               <br/>
                               <span class="shipping_country">
                                 <?php $shipping_country = (isset($order) ? get_country_short_name($order->shipping_country) : '--');?>
                                 <?php $shipping_country = ($shipping_country == '' ? '--' : $shipping_country);?>
                                 <?php echo html_entity_decode($shipping_country); ?></span>,
                                 <span class="shipping_zip">
                                   <?php $shipping_zip = (isset($order) ? $order->shipping_zip : '--');?>
                                   <?php $shipping_zip = ($shipping_zip == '' ? '--' : $shipping_zip);?>
                                   <?php echo html_entity_decode($shipping_zip); ?></span>
                                 </address>
                               </div>
                             </div>
                           </div>
                           <div class="col-md-6">


                            <div class="row">
                              <div class="col-md-6">
                                <?php if(count($payment_modes) > 0){
                                  echo render_select('payment_methods', $payment_modes, array('id', 'name'), 'payment_methods', (isset($order) ? $order->allowed_payment_modes : ''));
                                } ?>  
                              </div>  
                              <div class="col-md-6">
                               <div class="form-group select-placeholder">
                                <label for="discount_type" class="control-label"><?php echo _l('discount_type'); ?></label>
                                <select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                 <option value="" <?php if(!isset($order)){ echo "selected"; } ?> ><?php echo _l('no_discount'); ?></option>
                                 <option value="before_tax" <?php if(isset($order) && $order->discount_type_str == 'before_tax'){ echo "selected"; } ?> ><?php echo _l('discount_type_before_tax'); ?></option>
                                 <option value="after_tax" <?php if(isset($order) && $order->discount_type_str == 'after_tax'){ echo "selected"; } ?>  ><?php echo _l('discount_type_after_tax'); ?></option>
                               </select>
                             </div>
                           </div>
                         </div>


                         <div class="row">
                          <div class="col-md-6">
                           <?php
                           $currency_attr = array('disabled'=>true,'data-show-subtext'=>true);
                           $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');
                           $selected = '';
                           foreach($currencies as $currency){
                             if($currency['isdefault'] == 1){
                               $currency_attr['data-base'] = $currency['id'];
                             }
                             if(isset($order)){
                              if($currency['id'] == $order->currency){
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
                         <?php
                         $i = 0;
                         $selected = '';
                         foreach($staff as $member){
                           if(isset($order)){
                             if($order->seller == $member['staffid']) {
                               $selected = $member['staffid'];
                             }
                           }
                           $i++;
                         }
                         echo render_select('sale_agent',$staff,array('staffid',array('firstname','lastname')),'sale_agent_string',$selected);
                         ?>
                       </div>
                     </div>
                     <div class="row">
                       <div class="col-md-12">
                        <?php $value = (isset($order) ? $order->staff_note : '');?>
                        <?php echo render_textarea('note', 'admin_note', $value); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash(),
              );
              ?>

              <input type="hidden" id="csrf_token_name" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

              <!-- List -->
              <div class="panel-body mtop10">
                <div class="row">
                 <div class="col-md-4 mbot25">
                  <div class="">
                    <div class="items-select-wrapper">
                     <select name="item_select" class="selectpicker no-margin" data-width="100%" id="item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                      <option value=""></option>
                      <?php foreach ($items as $group_id => $_items) {?>
                        <optgroup data-group-id="<?php echo html_entity_decode($group_id); ?>" label="<?php echo html_entity_decode($_items[0]['group_name']); ?>">
                         <?php foreach ($_items as $item) {?>
                           <option value="<?php echo html_entity_decode($item['id']); ?>" data-subtext="<?php echo strip_tags(mb_substr($item['long_description'], 0, 200)) . '...'; ?>">(<?php echo app_format_number($item['rate']); ?>) <?php echo html_entity_decode($item['description']); ?></option>
                         <?php }?>
                       </optgroup>
                     <?php }?>
                   </select>
                 </div>
               </div>
             </div>
             <?php if (!isset($order_from_project) && isset($billable_tasks)) {
              ?>
              <div class="col-md-3">
                <div class="form-group select-placeholder input-group-select form-group-select-task_select popover-250">
                  <div class="input-group input-group-select">
                   <select name="task_select" data-live-search="true" id="task_select" class="selectpicker no-margin _select_input_group" data-width="100%" data-none-selected-text="<?php echo _l('bill_tasks'); ?>">
                    <option value=""></option>
                    <?php foreach ($billable_tasks as $task_billable) {
                      ?>
                      <option value="<?php echo html_entity_decode($task_billable['id']); ?>"<?php if ($task_billable['started_timers'] == true) {?>disabled class="text-danger important" data-subtext="<?php echo _l('invoice_task_billable_timers_found'); ?>" <?php } else {
                        $task_rel_data  = get_relation_data($task_billable['rel_type'], $task_billable['rel_id']);
                        $task_rel_value = get_relation_values($task_rel_data, $task_billable['rel_type']);
                        ?>
                        data-subtext="<?php echo html_entity_decode($task_billable['rel_type']) == 'project' ? '' : html_entity_decode($task_rel_value['name']); ?>" <?php }?>><?php echo html_entity_decode($task_billable['name']); ?></option>
                      <?php }?>
                    </select>
                    <div class="input-group-addon input-group-addon-bill-tasks-help">
                      <?php
                      if (isset($order) && !empty($order->project_id)) {
                        $help_text = _l('showing_billable_tasks_from_project') . ' ' . get_project_name_by_id($order->project_id);
                      } else {
                        $help_text = _l('invoice_task_item_project_tasks_not_included');
                      }
                      echo '<span class="pointer popover-invoker" data-container=".form-group-select-task_select"
                      data-trigger="click" data-placement="top" data-toggle="popover" data-content="' . $help_text . '">
                      <i class="fa fa-question-circle"></i></span>';
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php }?>
            <div class="col-md-<?php if (!isset($order_from_project)) {echo 5;} else {echo 8;}?> text-right show_quantity_as_wrapper">

            </div>
          </div>
          <?php if (isset($order_from_project)) {echo '<hr class="no-mtop" />';}?>
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
           <input type="number" name="rate" class="form-control" placeholder="<?php echo _l('item_rate_placeholder'); ?>">
         </td>
         <td>
          <input type="hidden" name="taxid" value="">
          <input type="hidden" name="taxrate" value="">
          <?php
          echo render_input('tax', '', '', 'text', array('readonly' => 'readonly'));
          ?>
        </td>
        <td>
          <span class="amount"></span>
        </td>
        <td>
         <?php
         $new_item = 'undefined';
         if (isset($order)) {
          $new_item = true;
        }
        ?>
        <button type="button" onclick="add_item_to_table('undefined','undefined',<?php echo html_entity_decode($new_item); ?>); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
      </td>
    </tr>
    <?php if (isset($order) || isset($add_items)) {
      $i               = 1;
      $items_indicator = 'newitems';
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


        $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['prices'] . '" class="form-control"></td>';

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
   <tr id="subtotal">
    <td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>
    </td>
    <td class="subtotal">
    </td>
  </tr>
  <tr class="tax-area" id="tax_area">

  </tr>
  <tr id="discount_area">
    <td>
      <div class="row ">
        <div class="col-md-7 ">
         <span class="bold"><?php echo _l('discount'); ?>:</span>
       </div>
       <div class="col-md-5">
         
        <div class="input-group">
          <input type="number" value="<?php echo (isset($order) ? $order->discount_percent : 0); ?>" class="form-control pull-left input-discount-percent<?php if(isset($order) && !is_sale_discount($order,'percent') && is_sale_discount_applied($order)){echo ' hide';} ?>" min="0" max="100" name="discount_percent">

          <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php echo (isset($order) ? $order->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if(!isset($order) || (isset($order) && !is_sale_discount($order,'fixed'))){echo ' hide';} ?>" min="0" name="discount_total">

          <span class="input-group-addon">
            <div class="dropdown">
              <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="discount-total-type-selected">
                  %
                </span>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" id="discount-total-type-dropdown" aria-labelledby="dropdown_menu_tax_total_type">
                <li>
                  <a href="#" class="discount-total-type discount-type-percent<?php if(!isset($order) || (isset($order) && is_sale_discount($order,'percent')) || (isset($order) && !is_sale_discount_applied($order))){echo ' selected';} ?>">%</a>
                </li>
                <li>
                  <a href="#" class="discount-total-type discount-type-fixed<?php if(isset($order) && is_sale_discount($order,'fixed')){echo ' selected';} ?>">
                    <?php echo _l('discount_fixed_amount'); ?>
                  </a>
                </li>
              </ul>
            </div>
          </span>
        </div>
        

      </div>
    </div>
  </td>
  <td class="discount-total"></td>
</tr>
<tr>
  <td>
   <div class="row">
    <div class="col-md-7">
     <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
   </div>
   <div class="col-md-5">
     <input type="number" data-toggle="tooltip" data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if(isset($order)){echo $order->adjustment; } else { echo 0; } ?>" class="form-control pull-left" name="adjustment">
   </div>
 </div>
</td>
<td class="adjustment"></td>
</tr>
<tr>
  <td><span class="bold"><?php echo _l('invoice_total'); ?> :</span>
  </td>
  <td class="total">
  </td>
</tr>
</tbody>
</table>
</div>
<div id="removed-items"></div>
</div>
<!-- End list -->
<div class="row">
  <div class="col-md-12 mtop10">
   <div class="panel-body bottom-transaction">
    <div class="form-group" app-field-wrapper="clientnote">
      <?php $value = (isset($order) ? $order->notes : '');?>
      <?php echo render_textarea('client_note', 'client_note', $value); ?>
    </div>                        
    <div class="form-group mtop15" app-field-wrapper="terms">
      <?php $value = (isset($order) ? $order->terms : '');?>
      <?php echo render_textarea('terms', 'terms_conditions', $value); ?>
    </div>            
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="text-right">
      <hr>
      <button type="submit" class="btn-tr btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
  </div>
</div>
<?php echo form_close(); ?>
</div>
</div>
<!-- End main content -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
   <?php echo form_open('admin/omni_sales/create_pos_customer',array('id'=>'customers-form','autocomplete'=>'off')); ?>

   <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title"><?php echo _l('new_customer'); ?></h4>
    </div>
    <div class="modal-body">
      <?php $this->load->view('pos/profile'); ?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button class="btn btn-info save-and-add-contact customer-form-submiter">
        <?php echo _l('save'); ?>
      </button>
    </div>
  </div>
  <?php echo form_close(); ?>
</div>
</div>


</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
