<!DOCTYPE html>
<html>
<head>
	<title></title>
	<?php hooks()->do_action('head_element_client'); ?>
</head>
<body class="bodyfixed not-select">
    <?php 
          $currency_name = '';
          if(isset($base_currency)){
            $currency_name = $base_currency->name;
          }
     ?>
<div class="row header_pos d-flex">

  
	<div class="left_header flex2">
    <div class="row">
      <div class="col-sm-6 pleft10 pright5">
            <input type="text" class="form-control input_groups" onkeyup="change_result(this);" name="keyword" placeholder="Search for products here ..." aria-describedby="basic-addon1">
            <span class="search_btn bbrr3 btrr3 append_right w40px" onclick="search(this)"><i class="fa fa-search"></i></span>
      </div>
      <div class="col-sm-6 pleft5 pright5">
               <div class="customerfr">
                  <select name="client_id" class="selectpicker input_groups" onchange="get_trade_discount(this);" data-width="100%" data-none-selected-text="<?php echo _l('customer'); ?>" data-live-search="true"> 
                     <option></option>
                      <?php 
                            foreach ($client as $key => $value) { ?>
                              <option value="<?php echo html_entity_decode($value['userid']) ?>"><?php echo html_entity_decode($value['company']) ?></option>                    
                      <?php } ?>  
                  </select>
                  <a href="#" class="append_right bbrr3 btrr3 registration-client w40px">
                    <i class="fa fa-plus"></i>
                  </a>
                </div>
      </div>
    </div>
	</div>
	<div class="right_header flex2"> 
        <div class="row">
          <div class="col-sm-6 pleft5 pright5">
                <div class="customerfr">
                  <select name="seller" class="selectpicker input_groups" data-width="100%" data-none-selected-text="<?php echo _l('salesman'); ?>" data-live-search="true"> 
                     <option></option>
                      <?php 
                            foreach ($staff as $key => $value) { ?>
                              <option value="<?php echo html_entity_decode($value['staffid']) ?>" <?php if(get_staff_user_id() == $value['staffid']){ echo 'selected'; } ?>><?php echo html_entity_decode($value['lastname'].' '.$value['firstname']) ?></option>                    
                      <?php } ?>  
                  </select>
                  <a href="../staff" target="blank" class="append_right bbrr3 btrr3 w40px">
                    <i class="fa fa-plus"></i>
                  </a>
                </div>
          </div>
          <div class="col-sm-6 pleft5 pright5">
            <div class="d-flex">
                <div class="customerfr flex1 pright5">
                  <select name="warehouse_id" class="selectpicker" onchange="get_list_product_ware_house();" data-width="100%" data-none-selected-text="<?php echo _l('warehouse'); ?>" data-live-search="true"> 
                     <option></option>
                      <?php 
                            foreach ($warehouse as $key => $value) { ?>
                              <option value="<?php echo html_entity_decode($value['warehouse_id']) ?>"><?php echo html_entity_decode($value['warehouse_name']); ?></option>                    
                      <?php } ?>  
                  </select>
                </div>
                <div class="menu_fr pright5">
                  <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-th-large"></i>
                    <span class="caret"></span></button>
                    <div class="dropdown-menu drop-menu-panel" x-placement="bottom-left">
                      <div class="menu-panel">
                        <table class="table-menu">
                          <tr>
                            <td>
                              <a href="#" data-id="setting">
                                <div class="cell-menu">
                                  <i class="fa fa-cogs"></i>
                                  <label><?php echo _l('setting'); ?></label>
                                </div>
                              </a>
                            </td>
                            <td>
                              <a href="#" data-id="customer">
                                <div class="cell-menu">
                                  <i class="fa fa-users"></i>
                                  <label><?php echo _l('customer'); ?></label>
                                </div>
                              </a>
                            </td>
                          </tr>
                          <tr>
                            <td>
                             <a href="../staff">
                              <div class="cell-menu">
                                <i class="fa fa-users"></i>
                                <label><?php echo _l('staff'); ?></label>
                              </div>
                            </a>
                            </td>
                            <td>
                              <a href="#" data-id="calculator">
                                <div class="cell-menu">
                                  <i class="fa fa-calculator"></i>
                                  <label><?php echo _l('calculator'); ?></label>
                                </div>
                              </a>
                            </td>
                          </tr>
                        </table>
                      <div class="clearfix"></div>
                    </div>
                  </div>              
                </div>            
            </div>
          </div>
        </div>
  </div>
</div>

<div class="frame_1">

	<div class="product_list_fr">
		     <div class="horizontal-scrollable-tabs preview-tabs-top" >
            <div class="arrow_left" onclick="scroll_list(-1);">
              <i class="fa fa-chevron-left"></i>
            </div>
            <div class="horizontal-tabs header-tab-group">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                 <li role="presentation" id="all_product"  data-group="0" class="active item-group">
                     <a href="#tab_0" aria-controls="tab_invoice" role="tab" data-toggle="tab" aria-expanded="true">
                     <?php echo _l('all_products'); ?></a>
                 </li>  
               	<?php foreach ($list_group as $key => $value) { ?>
               	 <li role="presentation" class="item-group" data-group="<?php echo html_entity_decode($value['id']); ?>">
                     <a href="#tab_<?php echo html_entity_decode($key+1); ?>" aria-controls="tab_invoice" data-id="<?php echo html_entity_decode($value['id']); ?>" role="tab" data-toggle="tab" aria-expanded="true">
                     <?php echo html_entity_decode($value['name']); ?></a>
                 </li>  
               	<?php } ?>                      
                 <li role="presentation"  class="item-groups">
                    
                 </li> 
               </ul>
            </div>
            <div class="arrow_right" onclick="scroll_list(1);">
              <i class="fa fa-chevron-right"></i>
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
  		  <div class="horizontal-tabs">
  		  	<ul class="nav nav-tabs mbot15 gen_cart" role="tablist">
  		  	  <li role="presentation" onclick="open_tab(this);" onmouseleave="active_customer();" class="tab_cart wtab_1 active">
  		         <a href="#tab1" class="exits_show" aria-controls="tab1" role="tab" data-toggle="tab" >
  		         1
  		         </a>
               <span class="exit_tab" onclick="remove_tab(this);">&#10006;</span>
  		      </li>

  		      <li onclick="general_tab(this);" class="tab">
  		         <a href="#tab2" aria-controls="tab2" role="tab">
  		          <i class="fa fa-plus"></i>
  		         </a>
  		      </li>

  		  	</ul>
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
        <div class="col-md-6">
            <h4><?php echo _l('shopping_cart') ?></h4>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" onchange="get_voucher(this);" name="voucher" placeholder="<?php echo _l('vouchers'); ?>">
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
              <input type="hidden" name="discount_auto" class="discount_auto" value="0">
              <input type="hidden" name="discount_type" class="discount_type" value="">

              <input type="hidden" name="discount_auto_event" class="discount_auto_event" value="">
              <input type="hidden" name="discount_voucher_event" class="discount_voucher_event" value="">

              <input type="hidden" name="customer_id" class="customer_id" value="">
              <?php echo form_hidden('other_discount','0'); ?>

              <div class="content_cart">
                 <div class="list_item"></div>
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
                            <td><span class="bold"><?php echo _l('invoice_total').' ('.$currency_name.')'; ?> :</span>
                            </td>
                              <input type="hidden" name="total_cart" value="0">             
                            <td class="total">  
                              <?php echo app_format_money(0, $currency_name); ?>   
                            </td>
                         </tr>                                               
                         <tr class="border0">
                            <td colspan="2" class="view_invoice border0"></td>
                         </tr> 
                         <tr class="border0">
                            <td colspan="2" class="print_invoice border0"></td>
                         </tr>  
                         <tr class="border0">
                            <td colspan="2" class="view_stock_export border0"></td>
                         </tr> 
                         <tr>
                           <td colspan="2">
                            <button class="btn btn-primary w100 payment-btn" onclick="checkout_cart()"><?php echo _l('payment'); ?></button>                            
                           </td>
                         </tr>             
                      </tbody>
                   </table>
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
                <div class="row">
                  <div class="col-md-6">
                    <label class="control-label"><?php echo _l('payment_methods'); ?></label>
                    <br />
                    <?php if(count($payment_modes) > 0){ ?>
                      <select class="selectpicker form-control"
                      name="payment_methods[]"
                      multiple="true"
                      data-width="100%"
                      data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
                      <?php foreach($payment_modes as $mode){ ?>
                         <option value="<?php echo html_entity_decode($mode['id']); ?>"><?php echo html_entity_decode($mode['name']); ?></option>
                      <?php } ?>
                      </select>
                      <?php } ?>
                      <center class="payment_methods_alert hide"><label class="text-danger"><?php echo _l('please_select_a_payment_method'); ?></label></center>
                      <div class="clearfix"></div>

                      <br>
                  </div>
                  <div class="col-md-6">                                
                     <label class="control-label"><?php echo  _l('customers_pay').' ('.$currency_name.')'; ?></label>
                     <br>
                     <input class="form-control" placeholder="..." data-type="currency" onkeyup="formatCurrency($(this));" onblur="formatCurrency($(this), 'blur');" onchange="cal_price(this);" name="customers_pay" aria-describedby="basic-addon2">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 ">
                    <?php echo render_textarea('payment_note','payment_note'); ?>
                  </div>
                  <div class="col-md-12">
                    <div class="checkbox create_invoice_fr pull-left">                          
                      <input type="checkbox" class="capability" name="create_invoice" checked value="on">
                      <label><?php echo _l('create_invoice'); ?></label>
                    </div>
                  </div>
                   <div class="col-md-12">
                     <div class="checkbox stock_export_fr pull-left">                          
                      <input type="checkbox" class="capability" name="stock_export" checked value="on">
                      <label><?php echo _l('stock_export'); ?></label>
                    </div>
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
        <button class="btn btn-primary w100 btn-order" onclick="create_invoice(this);" disabled>
          <?php echo _l('order'); ?>
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
          <div class="content_print">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>

<?php hooks()->do_action('client_pt_footer_js'); ?>
<?php require 'modules/omni_sales/assets/js/pos/pos_js.php';?>
</body>
</html>



