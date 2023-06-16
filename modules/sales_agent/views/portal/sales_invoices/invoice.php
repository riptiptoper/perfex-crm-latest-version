<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>
<div class="col-md-12 mtop15">
    <div class="panel_s accounting-template">
    	<?php
	    	$agent_id = get_sale_agent_user_id();
		    echo form_open($this->uri->uri_string(),array('id'=>'sale_invoice0-form','class'=>'_transaction_form'));
		    if(isset($invoice)){
		        echo form_hidden('isedit');
		    }
		?>

    	<div class="panel-body">
    		<div class="row">
	    		<div class="col-md-6">
	    			<div class="row">
	    				<?php $additional_discount = 0; ?>
                  		<input type="hidden" name="additional_discount" value="<?php echo html_entity_decode($additional_discount); ?>">
		    			<div class="col-md-12">
		    				<?php 
				        		$select = isset($invoice) ? $invoice->clientid : '';
				        		echo sa_render_select('clientid', $clients, array('id', 'name'), '<span class="text-danger">* </span>'._l('customer'), $select, ['required' => true]);
				        	 ?>
		    			</div>
		    			<div class="col-md-12">
			                <hr class="hr-10" />
			                  <a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa fa-pencil-square-o"></i></a>
			               

			                  <?php require 'modules/sales_agent/views/portal/sales_invoices/billing_and_shipping_template.php';?>
			            </div>
		               <div class="col-md-6">
		                  <p class="bold"><?php echo _l('invoice_bill_to'); ?></p>
		                  <address>
		                     <span class="billing_street">
		                     <?php $billing_street = (isset($invoice) ? $invoice->billing_street : '--'); ?>
		                     <?php $billing_street = ($billing_street == '' ? '--' :$billing_street); ?>
		                     <?php echo html_entity_decode($billing_street); ?></span><br>
		                     <span class="billing_city">
		                     <?php $billing_city = (isset($invoice) ? $invoice->billing_city : '--'); ?>
		                     <?php $billing_city = ($billing_city == '' ? '--' :$billing_city); ?>
		                     <?php echo html_entity_decode($billing_city); ?></span>,
		                     <span class="billing_state">
		                     <?php $billing_state = (isset($invoice) ? $invoice->billing_state : '--'); ?>
		                     <?php $billing_state = ($billing_state == '' ? '--' :$billing_state); ?>
		                     <?php echo html_entity_decode($billing_state); ?></span>
		                     <br/>
		                     <span class="billing_country">
		                     <?php $billing_country = (isset($invoice) ? get_country_short_name($invoice->billing_country) : '--'); ?>
		                     <?php $billing_country = ($billing_country == '' ? '--' :$billing_country); ?>
		                     <?php echo html_entity_decode($billing_country); ?></span>,
		                     <span class="billing_zip">
		                     <?php $billing_zip = (isset($invoice) ? $invoice->billing_zip : '--'); ?>
		                     <?php $billing_zip = ($billing_zip == '' ? '--' :$billing_zip); ?>
		                     <?php echo html_entity_decode($billing_zip); ?></span>
		                  </address>
		               </div>
		               <div class="col-md-6">
		                  <p class="bold"><?php echo _l('ship_to'); ?></p>
		                  <address>
		                     <span class="shipping_street">
		                     <?php $shipping_street = (isset($invoice) ? $invoice->shipping_street : '--'); ?>
		                     <?php $shipping_street = ($shipping_street == '' ? '--' :$shipping_street); ?>
		                     <?php echo html_entity_decode($shipping_street); ?></span><br>
		                     <span class="shipping_city">
		                     <?php $shipping_city = (isset($invoice) ? $invoice->shipping_city : '--'); ?>
		                     <?php $shipping_city = ($shipping_city == '' ? '--' :$shipping_city); ?>
		                     <?php echo html_entity_decode($shipping_city); ?></span>,
		                     <span class="shipping_state">
		                     <?php $shipping_state = (isset($invoice) ? $invoice->shipping_state : '--'); ?>
		                     <?php $shipping_state = ($shipping_state == '' ? '--' :$shipping_state); ?>
		                     <?php echo html_entity_decode($shipping_state); ?></span>
		                     <br/>
		                     <span class="shipping_country">
		                     <?php $shipping_country = (isset($invoice) ? get_country_short_name($invoice->shipping_country) : '--'); ?>
		                     <?php $shipping_country = ($shipping_country == '' ? '--' :$shipping_country); ?>
		                     <?php echo html_entity_decode($shipping_country); ?></span>,
		                     <span class="shipping_zip">
		                     <?php $shipping_zip = (isset($invoice) ? $invoice->shipping_zip : '--'); ?>
		                     <?php $shipping_zip = ($shipping_zip == '' ? '--' :$shipping_zip); ?>
		                     <?php echo html_entity_decode($shipping_zip); ?></span>
		                  </address>
		               </div>

		               <div class="col-md-12 form-group">
		               		<?php $prefix = get_sa_option('sale_invoice_prefix', $agent_id);
			                        $next_number = get_sa_option('next_sale_invoice_number', $agent_id);

			                  $inv_number = (isset($invoice) ? $invoice->inv_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y'));      
			                  if(get_option('po_only_prefix_and_number') == 1){
			                     $inv_number = (isset($invoice) ? $invoice->inv_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT));
			                  }      
			                  
			                  $number = (isset($invoice) ? $invoice->number : $next_number);
			                  echo form_hidden('number',$number); ?> 
			                  
			                    <label for="inv_number"><span class="text-danger">* </span><?php echo _l('inv_number'); ?></label>
			                    <input type="text" readonly required class="form-control" name="inv_number" value="<?php echo html_entity_decode($inv_number); ?>"> 

			                    <?php
			                    	if($prefix == '' || $next_number == ''){
				                   		echo '<p class="mbot5"><span class="text-danger">'._l('you_should_update').'</span><a href="'.site_url('sales_agent/portal/settings').'">'._l('general_settings').'</a><span class="text-danger">&nbsp;'._l('before_create_this_transaction').'</span></p>';
				                    }
			                    ?>
		               </div>

		                <div class="col-md-6">
			                <?php $date = (isset($invoice) ? _d($invoice->date) : date('Y-m-d'));
			                echo sa_render_date_input('date','invoice_date',$date); ?>
			            </div>

			            <div class="col-md-6">
			                <?php $duedate = (isset($invoice) ? _d($invoice->duedate) : date('Y-m-d'));
		                     echo sa_render_date_input('duedate','duedate',$duedate); ?>
			            </div>
		            </div>
	    		</div>
	    		<div class="col-md-6">
	    			<div class="row">
	    				<div class="col-md-12">
	    					<div class="form-group mbot15 ">
				                <label for="allowed_payment_modes" class="control-label"><?php echo _l('invoice_add_edit_allowed_payment_modes'); ?></label>
				                  <br />
				                  <?php if(count($payment_modes) > 0){ ?>
				                  <select class="form-control selectpicker"
				                  data-toggle="<?php echo html_entity_decode($this->input->get('allowed_payment_modes')); ?>"
				                  name="allowed_payment_modes[]"
				                  data-actions-box="true"
				                  multiple="true"
				                  data-width="100%"
				                  data-title="<?php echo _l('dropdown_non_selected_tex'); ?>">
				                  <?php foreach($payment_modes as $mode){
				                     $selected = '';
				                     if(isset($invoice)){
				                       if($invoice->allowed_payment_modes){
				                        $inv_modes = unserialize($invoice->allowed_payment_modes);
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
				                     <option value="<?php echo html_entity_decode($mode['id']); ?>"<?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($mode['name']); ?></option>
				                  <?php } ?>
				                  </select>
				                  <?php } else { ?>
				                  <p><?php echo _l('invoice_add_edit_no_payment_modes_found'); ?></p>
				                  <a class="btn btn-info" href="<?php echo admin_url('paymentmodes'); ?>">
				                  <?php echo _l('new_payment_mode'); ?>
				                  </a>
				                  <?php } ?>
				            </div>
	    				</div>
	    				<div class="col-md-6">
	    					<?php
		                        $currency_attr = array('data-show-subtext'=>true, 'disabled' => true);
		                        $currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

		                        foreach($currencies as $currency){
		                         if($currency['isdefault'] == 1){
		                           $currency_attr['data-base'] = $currency['id'];
		                         }
		                         if(isset($invoice)){
		                          if($currency['id'] == $invoice->currency){
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
		                     <?php echo sa_render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>
		                     <?php echo form_hidden('currency', $selected); ?>
	    				</div>
	    				<div class="col-md-6">
		                    <?php
		                        $i = 0;
		                        $selected = '';
		                        foreach($staff as $member){
		                         if(isset($invoice)){
		                           if($invoice->seller == $member['id']) {
		                             $selected = $member['id'];
		                           }
		                         }else{
		                         	$selected = get_sa_contact_user_id();
		                         }
		                         $i++;
		                        }
		                        echo sa_render_select('seller',$staff,array('id',array('firstname','lastname')),'sale_agent_string',$selected);
		                     ?>
		                </div>
		                <div class="col-md-12">
		                	<?php $value = (isset($invoice) ? $invoice->adminnote : ''); ?>
               				<?php echo render_textarea('adminnote','sa_note',$value, ['rows' => 11]); ?>
		                </div>

	    			</div>
	    		</div>
	    	</div>
    	</div>

    	<div class="panel-body mtop10 invoice-item">
    		<div class="row">
	          	<div class="col-md-4">
	            	<?php echo sa_render_select('item_select', $items, array('id', array('commodity_code', 'description')), '', '', ['data-none-selected-text' => _l('select_items')] ); ?>
	          	</div>
	          	    <?php
				        $base_currency = get_base_currency();

		                $po_currency = $base_currency;
		                if(isset($invoice) && $invoice->currency != 0){
		                  $po_currency = sa_get_currency_by_id($invoice->currency);
		                } 

		                $from_currency = (isset($invoice) && $invoice->from_currency != null) ? $invoice->from_currency : $base_currency->id;
		                echo form_hidden('from_currency', $from_currency);

		              ?>
		        <div class="col-md-8 <?php if($po_currency->id == $base_currency->id){ echo 'hide'; } ?>" id="currency_rate_div">
		            <div class="col-md-10 text-right">
		              
		              <p class="mtop10"><?php echo _l('currency_rate'); ?><span id="convert_str"><?php echo ' ('.$base_currency->name.' => '.$po_currency->name.'): ';  ?></span></p>
		            </div>
		            <div class="col-md-2 pull-right">
		              <?php $currency_rate = 1;
		                if(isset($invoice) && $invoice->currency != 0){
		                  $currency_rate = sa_get_currency_rate($base_currency->name, $po_currency->name);
		                }
		              echo render_input('currency_rate', '', $currency_rate, 'number', [], [], '', 'text-right'); 
		              ?>
		            </div>
		        </div>
	        </div>
	       	<div class="row">
	          <div class="col-md-12">
	            <div class="table-responsive ">
	              <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
	                <thead>
	                  <tr>
	                    <th></th>
	                    <th width="12%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> <?php echo _l('invoice_table_item_heading'); ?></th>
	                    <th width="15%" align="left"><?php echo _l('item_description'); ?></th>
	                    <th width="10%" align="right"><?php echo _l('unit_price'); ?><span class="th_currency"></span></th>
	                    <th width="10%" align="right" class="qty"><?php echo _l('quantity'); ?></th>
	                    <th width="12%" align="right"><?php echo _l('invoice_table_tax_heading'); ?></th>
	                    <th width="10%" align="right"><?php echo _l('tax_value'); ?><span class="th_currency"></span></th>
	                    <th width="10%" align="right"><?php echo _l('pur_subtotal_after_tax'); ?><span class="th_currency"></span></th>
	                    <th width="7%" align="right"><?php echo _l('discount').'(%)'; ?></th>
	                    <th width="10%" align="right"><?php echo _l('discount'); ?><span class="th_currency"></span></th>
	                    <th width="10%" align="right"><?php echo _l('total'); ?><span class="th_currency"></span></th>
	                    <th align="center"><i class="fa fa-cog"></i></th>
	                  </tr>
	                </thead>
	                <tbody>
	                  <?php echo html_entity_decode($invoice_row_template); ?>
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
			                      <?php $discount_total = isset($invoice) ? $invoice->discount_total : '';
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
			                  <input type="number" onchange="pur_calculate_total()" data-toggle="tooltip" value="<?php if(isset($invoice)){ echo html_entity_decode($invoice->shipping_fee); }else{ echo '0';} ?>" class="form-control pull-left text-right" name="shipping_fee">
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
        	 <?php $value = (isset($invoice) ? $invoice->clientnote : ''); ?>
            <?php echo render_textarea('clientnote','estimate_add_edit_client_note',$value,array(),array(),'mtop15'); ?>
            <?php $value = (isset($invoice) ? $invoice->terms :  ''); ?>
            <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
            
            <div class="row">
				<div class="modal-footer">
	                <button type="submit" class="btn btn-info commission-policy-form-submiter po-submit"><?php echo _l('submit'); ?></button>
	            </div>
			</div>

    	</div>

    	<?php echo form_close(); ?>
    </div>
</div>

<?php require 'modules/sales_agent/assets/js/portal/sale_invoices/invoice_js.php';?>