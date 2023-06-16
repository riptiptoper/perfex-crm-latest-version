<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>

  <div class="col-md-12 mtop15">
    <div class="panel_s">
    	<?php
	    	  $agent_id = get_sale_agent_user_id();
		      echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
		      if(isset($pur_order)){
		        echo form_hidden('isedit');
		      }
		      ?>
	    <div class="panel-body">
	    	<div class="row">
	    		<div class="col-md-12">
	    			<h4><?php echo html_entity_decode($title); ?></h4>
	    		</div>
	    		<div class="col-md-12"><hr class="hr-panel-heading"></div>
	    	</div>
	    	
	    	<div class="row">
	    		<?php $additional_discount = 0; ?>
                  <input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">

	    		<div class="col-md-3">
	    			<label for="order_name"><span class="text-danger">* </span><?php echo _l('order_name'); ?></label>
                  <?php $pur_order_name = (isset($pur_order) ? $pur_order->order_name : '');
                  echo render_input('order_name','',$pur_order_name); ?>
                </div>
	    		<div class="col-md-3 form-group">
                    <?php $prefix = get_sa_option('pur_order_prefix', $agent_id);
                        $next_number = get_sa_option('next_po_number', $agent_id);

                  $pur_order_number = (isset($pur_order) ? $pur_order->order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y'));      
                  if(get_option('po_only_prefix_and_number') == 1){
                     $pur_order_number = (isset($pur_order) ? $pur_order->order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT));
                  }      
                  
                  $number = (isset($pur_order) ? $pur_order->number : $next_number);
                  echo form_hidden('number',$number); ?> 
                  
                    <label for="order_number"><span class="text-danger">* </span><?php echo _l('order_number'); ?></label>
                    <input type="text" readonly required class="form-control" name="order_number" value="<?php echo html_entity_decode($pur_order_number); ?>"> 

                    <?php
                    	if($prefix == '' || $next_number == ''){
	                   		echo '<p class="mbot5"><span class="text-danger">'._l('you_should_update').'</span><a href="'.site_url('sales_agent/portal/settings').'">'._l('general_settings').'</a><span class="text-danger">&nbsp;'._l('before_create_this_transaction').'</span></p>';
	                    }
                    ?>
                </div>

                <div class="col-md-3">
	                <?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : date('Y-m-d'));
	                echo sa_render_date_input('order_date','order_date',$order_date); ?>
	            </div>

	            <div class="col-md-3">
	                <?php $delivery_date = (isset($pur_order) ? _d($pur_order->delivery_date) : date('Y-m-d'));
                     echo sa_render_date_input('delivery_date','delivery_date',$delivery_date, ['data-date-format' => "DD MMMM YYYY"]); ?>
	            </div>

	            <div class="col-md-3">
	                <?php 
	                $types = [
	                	['id' => 'capex', 'label' => _l('capex')],
	                	['id' => 'opex', 'label' => _l('opex')],
	                ];

	                $type = (isset($pur_order) ? _d($pur_order->type) : '');
                     echo sa_render_select('type', $types, array('id', 'label'), 'type', $type); ?>
              	</div>

	            <div class="col-md-3">
	                 <?php
	                    $currency_attr = array('data-show-subtext'=>true, 'disabled' => true);

	                    $selected = '';
	                    foreach($currencies as $currency){
	                      if(isset($pur_order) && $pur_order->currency != 0){
	                        if($currency['id'] == $pur_order->currency){
	                          $selected = $currency['id'];
	                        }
	                      }else{
	                       if($currency['isdefault'] == 1){
	                         $selected = $currency['id'];
	                       }
	                      }
	                    }
	   
	                    ?>
	                 <?php echo sa_render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
                   <?php echo form_hidden('currency', $selected); ?>
              	</div>

	            <div class="col-md-6">
	                <?php 
	                $buyer = (isset($pur_order) ? $pur_order->buyer : get_sa_contact_user_id());
                     echo sa_render_select('buyer', $staffs, array('id', 'full_name'), 'person_in_charge', $buyer); ?>
              	</div>


	    	</div>

	    	
	    </div>


	    <div class="panel-body mtop10 invoice-item">

        <div class="row">

           <div class="col-md-2">
           	<?php echo sa_render_select('disount_program', $disount_programs, array('id', 'name'), '', '', ['data-none-selected-text' => _l('select_program')]); ?>
           </div>
          <div class="col-md-4">
            <?php $this->load->view('sales_agent/item_include/main_item_select'); ?>
          </div>
                <?php
                $po_currency = $base_currency;
                if(isset($pur_order) && $pur_order->currency != 0){
                  $po_currency = sa_get_currency_by_id($pur_order->currency);
                } 

                $from_currency = (isset($pur_order) && $pur_order->from_currency != null) ? $pur_order->from_currency : $base_currency->id;
                echo form_hidden('from_currency', $from_currency);

              ?>
          <div class="col-md-6 <?php if($po_currency->id == $base_currency->id){ echo 'hide'; } ?>" id="currency_rate_div">
            <div class="col-md-10 text-right">
              
              <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency->name.' => '.$po_currency->name.'): ';  ?></span></p>
            </div>
            <div class="col-md-2 pull-right">
              <?php $currency_rate = 1;
                if(isset($pur_order) && $pur_order->currency != 0){
                  $currency_rate = sa_get_currency_rate($base_currency->name, $po_currency->name);
                }
              echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
              ?>
            </div>
          </div>
        </div> 
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive  ">
              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                  <tr>
                    <th></th>
                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
                    <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
                    <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
                    <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
                    <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
                    <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
                    <th width="10%" align="right"><?php echo _l('discount'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
                    <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"><?php echo '('.$po_currency->name.')'; ?></span></th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                  </tr>
                </thead>
                <tbody>
                  <?php echo html_entity_decode($pur_order_row_template); ?>
                </tbody>
              </table>
            </div>
          </div>
         <div class="col-md-8 col-md-offset-4">
          <table class="table text-right">
            <tbody>
              <tr id="subtotal">
                <td><span class="bold"><?php echo _l('subtotal'); ?> :</span>
                  <?php echo form_hidden('total_mn', ''); ?>
                </td>
                <td class="wh-subtotal">
                </td>
              </tr>
              
              <tr id="order_discount_percent">
                <td>
                  <div class="row">
                    <div class="col-md-7">
                      <span class="bold"><?php echo _l('pur_discount'); ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo _l('discount_percent_note'); ?>" ></i></span>
                    </div>
                    <div class="col-md-3">
                      <?php $discount_total = isset($pur_order) ? $pur_order->discount_total : '';
                      echo render_input('order_discount', '', $discount_total, 'number', ['onchange' => 'pur_calculate_total()', 'onblur' => 'pur_calculate_total()']); ?>
                    </div>
                     <div class="col-md-2">
                        <select name="add_discount_type" id="add_discount_type" class="selectpicker" onchange="pur_calculate_total(); return false;" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                            <option value="percent">%</option>
                            <option value="amount" selected><?php echo _l('amount'); ?></option>
                        </select>
                     </div>
                  </div>
                </td>
                <td class="order_discount_value">

                </td>
              </tr>

              <tr id="total_discount">
                <td><span class="bold"><?php echo _l('total_discount'); ?> :</span>
                  <?php echo form_hidden('dc_total', ''); ?>
                </td>
                <td class="wh-total_discount">
                </td>
              </tr>

              <tr>
                <td>
                 <div class="row">
                  <div class="col-md-9">
                   <span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
                 </div>
                 <div class="col-md-3">
                  <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($pur_order)){ echo html_entity_decode($pur_order->shipping_fee); }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
                </div>
              </div>
              </td>
              <td class="shiping_fee">
              </td>
              </tr>
              
              <tr id="totalmoney">
                <td><span class="bold"><?php echo _l('grand_total'); ?> :</span>
                  <?php echo form_hidden('grand_total', ''); ?>
                </td>
                <td class="wh-total">
                </td>
              </tr>

            </tbody>
          </table>
        </div>
        <div id="removed-items"></div> 
        </div>
        <div class="col-md-12"><hr class="hr-panel-heading"></div>
        	 <?php $value = (isset($pur_order) ? $pur_order->vendornote : ''); ?>
            <?php echo render_textarea('vendornote','estimate_add_edit_vendor_note',$value,array(),array(),'mtop15'); ?>
            <?php $value = (isset($pur_order) ? $pur_order->terms :  ''); ?>
            <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
            
            <div class="row">
				<div class="modal-footer">
	                <button type="button" class="btn btn-info commission-policy-form-submiter po-submit"><?php echo _l('submit'); ?></button>
	            </div>
			</div>
        </div>
       
        <?php echo form_close(); ?>

 	</div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/purchase_orders/pur_order_js.php';?>