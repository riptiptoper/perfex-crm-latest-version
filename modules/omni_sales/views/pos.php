<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php hooks()->do_action('head_element_client'); ?>
</head>
<body class="bodyfixed not-select">
  <div class="wrap-content">
    <?php 
    $currency_name = '';
    if(isset($base_currency)){
      $currency_name = $base_currency->name;
    }
    ?>
    <div class="row header_pos d-flex">
      <div class="left_header flex2">
        <div class="row d-flex">

          <div class="pleft10 pright5 flex2 relative">
           <input type="text" class="form-control input_groups" onkeyup="change_result(this);"  data-check="false" name="keyword" placeholder="Search by name, prices, SKU, barcode" aria-describedby="basic-addon1"
           autocomplete="off"
           autocorrect="off"
           autocapitalize="none"
           spellcheck="false">
           <span class="search_btn append_right w40px" onclick="search(this)"><i class="fa fa-search"></i></span>
         </div>
         <?php 
         if(omni_get_status_modules('warehouse')){ ?>
           <div class="pleft5 pright0 flex2 warehouse">
            <div class="customerfr flex1 pright5 warehouse_filter">
              <select name="warehouse_id" class="selectpicker" onchange="get_list_product_ware_house();" data-width="100%" data-none-selected-text="<?php echo _l('warehouses'); ?>" data-live-search="true"> 
               <option></option>
               <?php 
               foreach ($warehouse as $key => $value) { ?>
                <option value="<?php echo html_entity_decode($value['warehouse_id']) ?>"><?php echo html_entity_decode($value['warehouse_name']); ?></option>                    
              <?php } ?>  
            </select>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>
  <div class="right_header"> 

    <div class="row">
      <div class="col-sm-12 pleft0 pright5">
        <table class="table-menu">
          <tr>
            <td class="current-info">
              <span class="info">
                &nbsp;
                <?php $staff_id = get_staff_user_id(); ?>
                <input type="hidden" name="seller" value="<?php echo html_entity_decode($staff_id); ?>">
                <span id="staff_login" onclick="staff_profile(this)"><?php echo _l('staff').': '.get_staff_full_name($staff_id); ?></span>
                &nbsp;
                &nbsp;
                <span id="current">
                  <?php echo _d(date('Y-m-d')); ?> <span id="current_time"> 00:00:00</span>&nbsp;
                </span>
              </span>
            </td>

            <td class="menu-item">
              <object type="image/svg+xml" class="bg-style6" data="<?php echo site_url('modules/omni_sales/assets/images/transaction_history.svg'); ?>" >
              </object>
              <a href="#" onclick="menu(this); return false;" data-id="transaction_history" data-toggle="tooltip" data-placement="top" title="<?php echo _l('shift'); ?>">
              </a>
            </td>
            <?php 
            if(omni_get_status_modules('warehouse')){
            ?>
            <td class="menu-item">
              <object type="image/svg+xml" class="bg-style4" data="<?php echo site_url('modules/omni_sales/assets/images/020.svg'); ?>" >
              </object>
              <a href="#" onclick="menu(this); return false;" data-id="webcame" data-toggle="tooltip" data-placement="top" title="<?php echo _l('webcame'); ?>">
              </a>
            </td>
            <td class="menu-item">
              <object type="image/svg+xml" class="bg-style2" data="<?php echo site_url('modules/omni_sales/assets/images/010.svg'); ?>" >
              </object>
              <a href="#" onclick="menu(this); return false;" data-id="barcode" data-toggle="tooltip" data-placement="top" title="<?php echo _l('barcode'); ?>">
              </a>
            </td>
          <?php } ?>
            <td class="menu-item">
              <object type="image/svg+xml" class="bg-style3" data="<?php echo site_url('modules/omni_sales/assets/images/030.svg'); ?>" >
              </object>
              <a href="#" onclick="menu(this); return false;" data-id="customer" data-toggle="tooltip" data-placement="top" title="<?php echo _l('customer'); ?>">
              </a>
            </td>
            <td class="menu-item">
              <object type="image/svg+xml" class="bg-style1" data="<?php echo site_url('modules/omni_sales/assets/images/040.svg'); ?>" >
              </object>
              <a href="../staff" data-toggle="tooltip" data-placement="top" title="<?php echo _l('staff'); ?>">
              </a>
            </td>
            <td class="menu-item">
             <object type="image/svg+xml" class="bg-style8" data="<?php echo site_url('modules/omni_sales/assets/images/080.svg'); ?>" >
             </object>
             <a href="#" onclick="menu(this); return false;" data-id="calculator" data-toggle="tooltip" data-placement="top" title="<?php echo _l('calculator'); ?>">
             </a>
           </td>
           <?php 
            if(omni_get_status_modules('warehouse')){
            ?>
           <td class="menu-item">
            <object type="image/svg+xml" class="bg-style7" data="<?php echo site_url('modules/omni_sales/assets/images/plus.svg'); ?>" >
            </object>
            <a href="#" onclick="menu(this); return false;" data-id="add_product" data-toggle="tooltip" data-placement="top" title="<?php echo _l('calculator'); ?>">
            </a>
          </td>
        <?php } ?>
          <td class="menu-item">
            <object type="image/svg+xml" class="bg-style5" data="<?php echo site_url('modules/omni_sales/assets/images/settings.svg'); ?>" >
            </object>
            <a href="#" onclick="menu(this); return false;" data-id="settintg_cart" data-toggle="tooltip" data-placement="top" title="<?php echo _l('setting'); ?>">
            </a>
          </td>


        </tr>
      </table>
    </div>
  </div>

</div>
</div>

<div class="frame_1">

	<div class="product_list_fr">
   <div class="horizontal-scrollable-tabs preview-tabs-top" >
    <div class="arrow_left" onclick="scroll_list(-1);">
      <i>&#9664;</i>
    </div>
    <div class="horizontal-tabs header-tab-group">
     <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
       <li role="presentation" id="all_product"  data-group="0" class="active item-group">
         <a href="#tab_0" aria-controls="tab_invoice" role="tab" data-toggle="tab" aria-expanded="true">
           <?php echo _l('all_products'); ?></a>
         </li>  
         <?php foreach ($list_group as $key => $value) { 
          if($this->omni_sales_model->has_product_cat(1,$value['id'])){

            ?>
            <li role="presentation" class="item-group" data-group="<?php echo html_entity_decode($value['id']); ?>">
             <a href="#tab_<?php echo html_entity_decode($key+1); ?>" aria-controls="tab_invoice" data-id="<?php echo html_entity_decode($value['id']); ?>" role="tab" data-toggle="tab" aria-expanded="true">
               <?php echo html_entity_decode($value['name']); ?></a>
             </li>  
             <?php 
           }
         }

         ?>                      
         <li role="presentation"  class="item-groups">

         </li> 
       </ul>
     </div>
     <div class="arrow_right" onclick="scroll_list(1);">
       <i>&#9658;</i>
     </div>
   </div>

   <div class="tab-content">
     <div id="tab_list">
      <div class="panel panel-info" >
        <div class="panel-body frame_content">
         <div class="pad_left0 pad_right0 content_list">
         </div>
       </div>
     </div>
   </div>
 </div>  
</div>

<div class="right_pos">
  <div class="cart_pos">
   <div class="preview-tabs-top">
    <div class="horizontal-tabs tabs-gen_cart">      
      <div class="arrow_left arrow" onclick="scroll_tab_list(-1, '.gen_cart');">
        <i>&#9664;</i>
      </div>
      <ul class="nav nav-tabs mbot15 gen_cart" role="tablist">
       <li role="presentation" onclick="open_tab(this);" class="tab_cart wtab_1 active">
         <a href="#tab1" class="exits_show" aria-controls="tab1" role="tab" data-toggle="tab" >
           1
         </a>
       </li>
       <li onclick="general_tab(this);" class="tab" id="general_tab">
         <a href="#tab2" aria-controls="tab2" role="tab">
          <i class="fa fa-plus"></i>
        </a>
      </li>
    </ul>
    <div class="arrow_right arrow" onclick="scroll_tab_list(1, '.gen_cart');">
      <i>&#9658;</i>
    </div>
  </div>
</div> 
<div class="tab-content cart-tab w-100">
  <div role="tab1" class="tab-pane item-tab client_tab_content client_tab_content_1  active" id="tab1">
  </div>
</div>
</div>
</div>
</div>
<div class="hide" id="tab_content_template">
 <div class="panel panel-info" >
  <div class="panel-body">

    <div class="title_pn">
      <div class="row">
        <div class="col-md-12">
          <h4><?php echo _l('shopping_cart') ?></h4>
          <button class="btn exit_tab" onclick="remove_tab(this);">&#10006;</button>
        </div>      
      </div>
      <div class="row fvoucher">
        <div class="col-md-12">
          <input type="text" class="form-control" onchange="get_voucher(this);" data-check="false" name="voucher" placeholder="<?php echo _l('vouchers'); ?>">            
        </div>
      </div>
      <div class="clearfix line"></div>
      <div class="row fclientid">
        <div class="col-md-12">
          <div class="customerfr">
            <select name="client_id" class="selectpicker input_groups" onchange="get_trade_discount(this);" data-width="100%" data-none-selected-text="<?php echo _l('customer'); ?>" data-live-search="true"> 
             <option></option>
             <?php 
             $has_public = false;
             foreach ($client as $key => $value) { 
              $is_public = '';
              if($has_public == false){
                $custom_fields_items = get_custom_field_value($value['userid'], 'customers_is_public', 'customers');
                if($custom_fields_items != '' && $custom_fields_items == 'public'){
                  $has_public = true;
                  $is_public = 'selected';
                }
              } ?>
              <option value="<?php echo html_entity_decode($value['userid']) ?>" <?php echo html_entity_decode($is_public); ?>><?php echo html_entity_decode($value['company']) ?></option>                    
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

  </div>
  <div class="row contents">
    <input type="hidden" name="list_id_product" class="list_id_product" value="">
    <input type="hidden" name="list_qty_product" class="list_qty_product" value="">
    <input type="hidden" name="list_price_product" class="list_price_product" value="">                     
    <input type="hidden" name="list_price_discount_product" class="list_price_discount_product" value="">                     
    <input type="hidden" name="list_percent_discount_product" class="list_percent_discount_product" value="">                     
    <input type="hidden" name="list_price_tax" class="list_price_tax" value="">   
    <input type="hidden" name="discount_total" class="discount_total" value="">
    <input type="hidden" name="discount_voucher" class="discount_voucher" value="0">
    <input type="hidden" name="discount_voucher_value" class="discount_voucher_value" value="0">
    <input type="hidden" name="discount_auto" class="discount_auto" value="0">
    <input type="hidden" name="discount_type" class="discount_type" value="">
    

    <input type="hidden" name="discount_auto_event" class="discount_auto_event" value="">
    <input type="hidden" name="discount_voucher_event" class="discount_voucher_event" value="">
    <?php echo form_hidden('other_discount','0'); ?>

    <div class="content_cart">
     <div class="list_item"></div>
     <?php hooks()->do_action('omni_sale_pos_redeem'); ?>
     <table class="table text-right">
      <tbody>
       <tr id="subtotal">
        <td><span class="bold"><?php echo _l('invoice_subtotal').' ('.$currency_name.')'; ?> :</span>
        </td>
        <input type="hidden" name="sub_total_cart" value="">                   
        <td class="subtotal">
          <?php echo app_format_money(0,$currency_name); ?>
        </td>
      </tr>
      <tr id="discount_area">
        <td>
         <?php echo _l('discount').':'; ?>
       </td>
       <td class="discount-total">
       </td>
     </tr>
     <tr>
      <tr class="discount_area_discount">
        <td>
         <span class="promotions bold"><?php echo _l('tax'); ?> :</span>
       </td>
       <td class="promotions_tax_price promotions">+ <?php echo app_format_money(0,''); ?>
     </td>
     <input type="hidden" name="tax" value="">                   
   </tr>
 </tr>
 <tr>
   <td>
     <span class="bold"><?php echo _l('omni_shipping_fee'); ?> :</span>
   </td>
   <td>
     <input type="text" id="shipping" name="shipping" onchange="total_cart()" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" class="form-control text-right" value="<?php echo app_format_money(get_option('omni_pos_shipping_fee'),''); ?>">
   </td>
 </tr>
 <tr>
  <td><span class="bold"><?php echo _l('invoice_total').' ('.$currency_name.')'; ?> :</span>
  </td>
  <input type="hidden" name="total_cart" value="0">             
  <td class="total">  
    <?php echo app_format_money(0, $currency_name); ?>   
  </td>
</tr>                                               
</tbody>
</table>
</div> 
<div class="bottom-btn">  
  <div class="payment_s">
    <button class="btn btn-primary w100 payment-btn" onclick="checkout_cart()"><?php echo _l('payment'); ?></button>       
  </div>
  <div class="bottom-payment d-flex">
    <button class="btn btn-primary btn_create_invoice flex2 create_invoice_after hide" data-insert_id="">
      <?php echo _l('create_invoice'); ?>
    </button>
  </div>
  <div class="success_s hide d-flex">
    <button class="btn btn-default print-bill hide flex3" onclick="print_bill();" ><?php echo _l('print') ?></button>    
    <a href="#" class="btn btn-primary view-invoice hide flex3" target="_blank"><?php echo _l('view_invoice'); ?></a>
    <?php 
        if(omni_get_status_modules('warehouse')){
     ?>
    <a href="#" class="btn btn-info view-export-stock hide flex3" target="_blank"><?php echo _l('view_export_stock'); ?></a>     
    <?php } ?>           
  </div>
</div>
</div>
</div>
</div>
</div>
<input type="hidden" name="id_group" value="0">
<input type="hidden" name="index_page" value="1">
<div class="modal fade" id="alert" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">   
      <div class="modal-body">
        <center class="alert_content"></center>
      </div>       
    </div>
  </div>
</div>
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
<div class="patent_fam">
  <div class="relative">
    <video autoplay></video>
    <div id="inner"></div>
    <div id="redline">
    </div>
    <ul id="decoded">
    </ul>
    <canvas class="displaynone"></canvas>
    <div class="clearfix"></div>
    <div class="btn btn-default exit_video_frame">&times;</div>
  </div>
</div>
<div id="payments" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _l('Choose a payment method'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="row checkout_frmd">
          <div class="left pad_right0">
            <div class="row">
              <div class="col-md-6">
                <?php echo render_textarea('note','sales_note'); ?>
              </div>
              <div class="col-md-6">
                <?php echo render_textarea('staff_note','staff_note'); ?>
              </div>
            </div> 
            <div class="row panel_payment">
              <div class="col-md-12">

                <div class="row payment_row">
                  <div class="col-md-6">
                    <label class="control-label"><?php echo _l('payment_methods'); ?></label>
                    <br />
                    <select class="selectpicker form-control" 
                    name="payment_methods[]"
                    data-title="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                    onchange="change_payment_ui(this)">
                    <?php 
                    foreach ($list_payment as $key => $payment) {  ?>
                      <option value="<?php echo html_entity_decode($payment['id']); ?>" <?php echo (($key == 0) ? 'selected' : '') ?> ><?php echo html_entity_decode($payment['name']); ?></option>
                    <?php }  ?>
                  </select>
                  <center class="payment_methods_alert hide"><label class="text-danger"><?php echo _l('please_select_a_payment_method'); ?></label></center>
                  <div class="clearfix"></div>
                  <br>
                </div>
                <div class="col-md-5 form-group required">                                
                 <label class="control-label"><?php echo  _l('customers_pay').' ('.$currency_name.')'; ?></label>
                 <br>
                 <input class="form-control" placeholder="..." data-type="currency" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" onchange="cal_price(this);" onclick="get_obj(this)" name="customers_pay[]" aria-describedby="basic-addon2" value="0">
               </div>
               <div class="col-md-1">
                 <br>
                 <button class="btn btn-default mtop7 add-payment-btn add_new_payment">&#43;</button>
               </div>
             </div>

             <div class="row">
              <div class="col-md-12">
                <?php echo render_textarea('payment_note','payment_note'); ?>
              </div>
              <div class="col-md-12">
                <label class="container_checkbox">
                  <?php echo _l('create_invoice'); ?>
                  <input type="checkbox" onchange="ui_check();" name="create_invoice" checked value="on">
                  <span class="checkmark"></span>
                </label>
                <?php if(omni_get_status_modules('warehouse')){ ?>
                <label class="container_checkbox stock_export">
                  <?php echo _l('stock_export'); ?>
                  <input type="checkbox" name="stock_export" checked value="on">
                  <span class="checkmark"></span>
                </label>
              <?php } ?>
              </div>
              <div class="col-md-12">
                <label class="container_checkbox">
                  <?php echo _l('debit_order'); ?>
                  <input type="checkbox" onchange="ui_check_debit_order();" name="debit_order" value="on">
                  <span class="checkmark"></span>
                </label>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
               <table class="table text-right">
                <tbody>
                 <tr>
                  <td><span class="bold"><?php echo _l('total_items'); ?> :</span>
                  </td>
                  <td class="total_items">
                  </td>
                  <td><span class="bold"><?php echo _l('total_payable').' ('.$currency_name.')'; ?> :</span>
                  </td>
                  <td class="total_payable">
                  </td>
                </tr>
                <tr>
                  <td><span class="bold"><?php echo _l('total_paying').' ('.$currency_name.')'; ?> :</span>
                  </td>
                  <td class="total_paying_s">
                  </td>
                  <td><span class="bold"><?php echo _l('balance').' ('.$currency_name.')'; ?> :</span>
                  </td>
                  <td class="balance_s">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="right pad_left0">
    <ul class="quick_cash">
      <li class="items title"><label><?php echo _l('quick_cash'); ?></label></li>
      <li class="items item-cash" data-value="2700"><span>2700</span></li>
      <li class="items item-cash" data-value="10"><span>10</span></li>
      <li class="items item-cash" data-value="20"><span>20</span></li>
      <li class="items item-cash" data-value="50"><span>50</span></li>
      <li class="items item-cash" data-value="100"><span>100</span></li>
      <li class="items item-cash" data-value="500"><span>500</span></li>
      <li class="items item-cash" data-value="1000"><span>1000</span></li>
      <li class="items item-cash" data-value="5000"><span>5000</span></li>
      <li class="items item-cash bottom" data-value="0"><span><?php echo _l('clear'); ?></span></li>
    </ul>
  </div>
</div>

</div>
<div class="modal-footer">
  <button class="btn btn-primary w100 btn-order" onclick="create_invoice(this);">
    <?php echo _l('omni_order'); ?>
  </button>
</div>
</div>
</div>
</div>

<div id="modal_iframe" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="check_out_success">
          <iframe id="content_print" class="w100" name="content_print"></iframe>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default print_bill">Print bill</button>                 
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>

<div id="modal_staff" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _l('are_you_sure_you_want_to_log_out'); ?></h4>
      </div>
      <div class="modal-body">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button onclick="logout(); return false;" class="btn btn-info">
          <?php echo _l('agree'); ?>
        </button>
      </div>
    </div>
  </div>
</div>
<div id="modal_setting" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _l('are_you_sure_you_want_to_log_out'); ?></h4>
      </div>
      <div class="modal-body">

        <?php 
        $type = '1';
        if(isset($_COOKIE['type_input_qty'])){
          $type = $_COOKIE['type_input_qty'];
        }
        ?>

        <fieldset>
          <legend><?php echo _l('quantity_format'); ?></legend>
          <div class="col-md-6">
            <div class="checkbox">
              <input type="radio" name="type_input_qty" id="type_integer" value="1" <?php if($type == '1'){ echo 'checked'; } ?> >
              <label for="type_integer"><?php echo _l('integer'); ?></label>
            </div> 
          </div>
          <div class="col-md-6">
            <div class="checkbox">
              <input type="radio" name="type_input_qty" id="type_decimal" value="0.1" <?php if($type == '0.1'){ echo 'checked'; } ?> >
              <label for="type_decimal"><?php echo _l('decimal'); ?></label>
            </div> 
          </div>
        </fieldset>
        <br>
        <?php 
        $enable_keyboard = '0';
        if(isset($_COOKIE['enable_keyboard'])){
          $enable_keyboard = $_COOKIE['enable_keyboard'];
        }
        ?>
        <fieldset>
          <legend><?php echo _l('enable_keyboard'); ?></legend>
          <div class="col-md-12 custom_setting_style">
            <div class="checkbox">
              <input type="checkbox" name="enable_keyboard" id="enable_keyboard" value="1" <?php if($enable_keyboard == '1'){ echo 'checked'; } ?>>
              <label for="enable_keyboard"><?php echo _l('enable'); ?></label>
            </div> 
          </div>
        </fieldset>
        <br>
        <?php 
        $auto_open_new_tab = '0';
        if(isset($_COOKIE['auto_open_new_tab'])){
          $auto_open_new_tab = $_COOKIE['auto_open_new_tab'];
        }
        ?>
        <fieldset>
          <legend><?php echo _l('auto_open_new_tab'); ?></legend>
          <div class="col-md-12 custom_setting_style">
            <div class="checkbox">
              <input type="checkbox" name="auto_open_new_tab" id="auto_open_new_tab" value="1" <?php if($auto_open_new_tab == '1'){ echo 'checked'; } ?>>
              <label for="auto_open_new_tab"><?php echo _l('enable'); ?></label>
            </div> 
          </div>
        </fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button class="btn btn-info btn_save_setting" id="type_input_qty" onclick="save_setting(this); return false;">
          <?php echo _l('save'); ?>
        </button>
      </div>
    </div>
  </div>
</div>

<!--typing area-->
<div id="keyboard" class="hide">
  <!--lowercase keyboard-->
  <div id="lowercase">
    <div class="row">
      <div class="key white">
        <span>q</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>w</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>e</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>r</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>t</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>y</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>u</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>i</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>o</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>p</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray delete_orange">
        <span>&#9003;</span>
      </div>
    </div>
    <div class="row">
      <div class="key white">
        <span>a</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>s</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>d</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>f</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>g</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>h</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>j</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>k</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>l</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray-enter delete_orange">
        <span>Enter</span>
      </div>

    </div>
    <div class="row">
      <div class="key gray gray-cus">
        <span>&#8679;</span>
      </div>
      <div class="key white">
        <span>z</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>x</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>c</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>v</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>b</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>n</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>m</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray gray-cus">
        <span>&#8679;</span>
      </div>
    </div>
    <div class="row">
      <div class="key gray">
        <span>123</span>
      </div>
      <div class="key white">
        <span>space</span>
      </div>
      <div class="key gray">
        <span>return</span>
      </div>
      <div class="key gray gray-cancel">
        <span>cancel</span>
      </div>
    </div>
  </div>
  <!--uppercase keyboard-->
  <div id="uppercase">
    <div class="row">
      <div class="key white">
        <span>Q</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>W</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>E</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>R</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>T</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>Y</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>U</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>I</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>O</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>P</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray delete_orange">
        <span>&#9003;</span>
      </div>
    </div>
    <div class="row">
      <div class="key white">
        <span>A</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>S</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>D</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>F</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>G</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>H</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>J</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>K</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>L</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray-enter delete_orange">
        <span>Enter</span>
      </div>
    </div>
    <div class="row">
      <div class="key gray gray-cus background-green">
        <span>&#11014;</span>
      </div>
      <div class="key white">
        <span>Z</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>X</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>C</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>V</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>B</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>N</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>M</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray gray-cus background-green">
        <span>&#11014;</span>
      </div>

    </div>
    <div class="row">
      <div class="key gray">
        <span>123</span>
      </div>
      <div class="key white">
        <span>space</span>
      </div>
      <div class="key gray">
        <span>return</span>
      </div>
      <div class="key gray gray-cancel">
        <span>cancel</span>
      </div>
    </div>
  </div>
  <!--numbers and symbols keyboard-->
  <div id="numbers">
    <div class="row">
      <div class="key white">
        <span>1</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>2</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>3</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>4</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>5</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>6</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>7</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>8</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>9</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>0</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="key white">
        <span>-</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>/</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>:</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>(</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>)</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>$</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&amp;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>@</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>"</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="key gray gray__w symbol_success">
        <span>#+=</span>
      </div>
      <div class="key white">
        <span>.</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>,</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>?</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>!</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>'</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray gray__w delete_orange">
        <span>&#9003;</span>
      </div>
    </div>
    <div class="row">
      <div class="key gray">
        <span>ABC</span>
      </div>
      <div class="key white">
        <span>space</span>
      </div>
      <div class="key gray">
        <span>return</span>
      </div>
      <div class="key gray gray-cancel">
        <span>cancel</span>
      </div>
    </div>
  </div>
  <!--additional symbols keyboard-->
  <div id="symbols">
    <div class="row">
      <div class="key white">
        <span>[</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>]</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>{</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>}</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>#</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>%</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>^</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>*</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>+</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>=</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="key white">
        <span>_</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>\</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>|</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>~</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&lt;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&gt;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&euro;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&pound;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&yen;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>&bull;</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="key gray gray-sym">
        <span>123</span>
      </div>
      <div class="key white">
        <span>.</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>,</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>?</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>!</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key white">
        <span>'</span>
        <div class="popout">
          <div class="head"></div>
          <div class="neck"></div>
        </div>
      </div>
      <div class="key gray gray-sym delete_orange">
        <span>&#9003;</span>
      </div>
    </div>
    <div class="row">
      <div class="key gray">
        <span>ABC</span>
      </div>
      <div class="key white">
        <span>space</span>
      </div>
      <div class="key gray">
        <span>Return</span>
      </div>
      <div class="key gray gray-cancel">
        <span>cancel</span>
      </div>
    </div>
  </div>
</div>

<form id="calculator" class="calculator" name="calc">
  <div class="header"></div>
  <button type="button" class="btn exit_calculator">&#10006;</button>
  <input class="value" type="text" name="txt" readonly="">
  <span class="num clear" onclick="document.calc.txt.value =''">C</span>
  <span class="num" onclick="document.calc.txt.value += '/'">/</span>
  <span class="num" onclick="document.calc.txt.value += '*'">*</span>
  <span class="num" onclick="document.calc.txt.value += '7'">7</span>
  <span class="num" onclick="document.calc.txt.value += '8'">8</span>
  <span class="num" onclick="document.calc.txt.value += '9'">9</span>
  <span class="num" onclick="document.calc.txt.value += '-'">-</span>
  <span class="num" onclick="document.calc.txt.value += '4'">4</span>
  <span class="num" onclick="document.calc.txt.value += '5'">5</span>
  <span class="num" onclick="document.calc.txt.value += '6'">6</span>
  <span class="num plus" onclick="document.calc.txt.value += '+'">+</span>
  <span class="num" onclick="document.calc.txt.value += '3'">3</span>
  <span class="num" onclick="document.calc.txt.value += '2'">2</span>
  <span class="num" onclick="document.calc.txt.value += '1'">1</span>
  <span class="num" onclick="document.calc.txt.value += '0'">0</span>
  <span class="num" onclick="document.calc.txt.value += '00'">00</span>
  <span class="num" onclick="document.calc.txt.value += '.'">.</span>
  <span class="num equal" onclick="document.calc.txt.value = eval(calc.txt.value)">=</span>
</form>
</div>
</div>

<div class="modal fade" id="transaction_history_modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title"><?php echo _l('shift'); ?></h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12 title-transaction">
          <h4><?php echo _l('transaction_history').' '.date('Y-m-d'); ?></h4>          
        </div>
        <div class="content">
        </div>
        <div class="col-md-12">
          <div class="alert alert-success clearfix">
            <div class="col-md-3 card-total">
              <strong><?php echo _l('granted_amount'); ?></strong>
              <br>
              <span class="ml-10" id="granted_amount_n"></span>
            </div>
            <div class="col-md-3 card-total">
              <strong><?php echo _l('incurred_amount'); ?></strong>
              <br>
              <span class="ml-10" id="incurred_amount_n"></span>
            </div>
            <div class="col-md-3 card-total">
              <strong><?php echo _l('closing_amount'); ?></strong>
              <br>
              <span class="ml-10" id="closing_amount_n"></span>
            </div>
            <div class="col-md-3 card-total">
              <strong><?php echo _l('revenue'); ?></strong>
              <br>
              <span class="ml-10" id="revenue_n"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button class="btn btn-primary submit" onclick="close_shift()"><?php echo _l('close_shift'); ?></button>                 
    </div>
  </div>
</div>
</div>



<div class="modal fade" id="add_product_modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-lg">
  <?php echo form_open_multipart(admin_url('omni_sales/add_product_pos'),array('class'=>'add_product_pos', 'id'=>'add_product_pos-form', 'autocomplete'=>'off')); ?>
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">
        <?php echo _l('add_product'); ?>
      </h4>
    </div>
    <div class="modal-body">
      <?php $this->load->view('pos/add_product_content'); ?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button class="btn btn-primary submit"><?php echo _l('save'); ?></button>                 
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
</div>
<div class="alert alert_float alert-success right-alert hide">
  <div class="content"></div>
  <div class="exit">
    <span>&#10006;</span>
  </div>
</div>
<input type="hidden" name="shift" value="<?php echo html_entity_decode($shift); ?>">
<?php hooks()->do_action('client_pt_footer_js'); ?>
<?php require 'modules/omni_sales/assets/js/pos/pos_js.php';?>
</body>
</html>



