<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>
<div class="col-md-12 mtop15">
	<div class="panel_s">
	   	<div id="page-content" class="panel-body">

	   		<div class="row">
             	<div class="col-md-12">
              		<h4 class="no-margin font-bold"><?php echo _l($title); ?> </h4>
 					<br>
             	</div>
            </div>
            <?php 
        		$base_currency = get_base_currency();
        		if($invoice->currency != 0){
        			$base_currency = sa_get_currency_by_id($invoice->currency);
        		}
        	 ?>


        	<div class="horizontal-scrollable-tabs preview-tabs-top">
	   
	            <div class="horizontal-tabs">
	               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
	                  	<li role="presentation" class="active">
		                    <a href="#tab_sale_invoice" aria-controls="tab_sale_invoice" role="tab" data-toggle="tab">
		                     	<?php echo _l('invoice'); ?>
		                    </a>
	                  	</li>
	                  	<li role="presentation">
		                    <a href="#payment_record" aria-controls="payment_record" role="tab" data-toggle="tab">
		                     	<?php echo _l('payment_record'); ?>
		                    </a>
	                  	</li>
	                  	<li role="presentation">
		                    <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
		                     	<?php echo _l('attachment'); ?>
		                    </a>
	                  	</li>  
	               </ul>
	            </div>
	        </div>

	        <div class="row">
         		<div class="col-md-3">
         			<?php $class = '';
         			if($invoice->status == 'unpaid'){
         				$class = 'danger';
         			}elseif($invoice->status == 'paid'){
         				$class = 'success';
         			}elseif($invoice->status == 'partially_paid'){
         				$class = 'warning';
         			} ?>
         			<span class="label label-<?php echo html_entity_decode($class); ?> mtop5 s-status invoice-status-3"><?php echo _l($invoice->status); ?></span>
         		</div>
         		<div class="col-md-9 _buttons">
         			<div class="visible-xs">
	                  <div class="mtop10"></div>
	               	</div>
	               	<div class="pull-right">
	               		<a href="<?php echo site_url('sales_agent/portal/sale_invoice/'.$invoice->id); ?>" data-toggle="tooltip" title="<?php echo _l('edit_invoice'); ?>" class="btn btn-default btn-with-tooltip mright5" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
	               		
	               	   <?php if(saleinvoice_left_to_pay($invoice->id) > 0){ ?>
		               	<a href="#" onclick="add_payment(<?php echo html_entity_decode($invoice->id); ?>); return false;" class="btn btn-success pull-right"><i class="fa fa-plus-square"></i>&nbsp;<?php echo ' '._l('payment'); ?></a>
		               <?php } ?>
	               	</div>
         		</div>
         	</div>

         	<div class="clearfix"></div>
         	<hr class="hr-panel-heading" />

         	<div class="tab-content">
	         	<div role="tabpanel" class="tab-pane ptop10 active" id="tab_sale_invoice">
	         		
	         		<div class="row">
		         		<div class="col-md-6">
	                        <h4 class="bold mbot5">
	                         
	                           <a href="<?php echo admin_url('sales_agent/portal/sale_invoice_detail/'.$invoice->id); ?>">
	                           <span id="estimate-number">
	                           <?php echo html_entity_decode($invoice->inv_number); ?>
	                           </span>
	                           </a>
	                        </h4>
	                        <p><?php echo _l('customer').': '. get_sa_customer_name_by_id($invoice->clientid); ?></p>
	                        <p><?php echo _l('invoice_date').': '. _d($invoice->date); ?></p>
	                        <p><?php echo _l('duedate').': '. _d($invoice->duedate); ?></p>
	                    </div>
	                    <div class="col-md-6">
	                    </div>
	                </div>
	         		
	         		<div class="row">
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
		            </div>

		            <div class="row">
		            	<div class="col-md-12 pad_left_0 pad_right_0">
		         			<div class="table-responsive">
	                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
	                              <thead>
	                                 <tr>
	          
	                                    <th class="description" width="50%" align="left"><?php echo _l('items'); ?></th>
	                                    <th align="right"><?php echo _l('sa_quantity'); ?></th>
	                                    <th align="right"><?php echo _l('sa_unit_price'); ?></th>
	                                    <th align="right"><?php echo _l('into_money'); ?></th>
	                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
	                                    <th align="right"><?php echo _l('tax'); ?></th>
	                                    <?php } ?>
	                                    <th align="right"><?php echo _l('sub_total'); ?></th>
	                                    <th align="right"><?php echo _l('discount(%)'); ?></th>
	                                    <th align="right"><?php echo _l('discount(money)'); ?></th>
	                                    <th align="right"><?php echo _l('total'); ?></th>
	                                 </tr>
	                              </thead>
	                              <tbody class="ui-sortable">

	                                 <?php $item_discount = 0;
	                                 if(count($invoice_details) > 0){
	                                    $count = 1;
	                                    $t_mn = 0;
	                                    
	                                 foreach($invoice_details as $es) { ?>
	                                 <tr nobr="true" class="sortable">

	                                    <td class="description" align="left;"><span><strong><?php 
	                                    $item = sa_get_item_hp($es['item_code']); 
	                                    if(isset($item) && isset($item->commodity_code) && isset($item->description)){
	                                       echo html_entity_decode($item->commodity_code.' - '.$item->description);
	                                    }else{
	                                       echo html_entity_decode($es['item_name']);
	                                    }
	                                    ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo html_entity_decode($es['description']); ?></span><?php } ?></td>
	                                    <td align="right"  width="12%"><?php echo html_entity_decode($es['quantity']); ?></td>
	                                    <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
	                                    <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
	                                    <?php if(get_option('show_purchase_tax_column') == 1){ ?>
	                                    <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency->symbol); ?></td>
	                                    <?php } ?>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
	                                    <td class="amount" width="12%" align="right"><?php echo ($es['discount_percent'].'%'); ?></td>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency->symbol); ?></td>
	                                    <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency->symbol); ?></td>
	                                 </tr>
	                              <?php 
	                              $t_mn += $es['total_money'];
	                              $item_discount += $es['discount_money'];
	                              $count++; } } ?>
	                              </tbody>
	                           </table>
	                        </div>
	                    </div>

	                    <div class="col-md-5 col-md-offset-7 pad_left_0 pad_right_0">
	                        <table class="table text-right">
	                           <tbody>
	                              <tr id="inv_subtotal">
	                                 <td><span class="bold"><?php echo _l('subtotal'); ?></span>
	                                 </td>
	                                 <td class="inv_subtotal">
	                                    <?php echo app_format_money($invoice->subtotal,$base_currency->symbol); ?>
	                                 </td>
	                              </tr>

	                              <?php if($tax_data['preview_html'] != ''){
	                                echo html_entity_decode($tax_data['preview_html']);
	                              } ?>


	                              <?php if(($invoice->discount_total + $item_discount) > 0){ ?>
	                              
	                              <tr id="inv_discount_total">
	                                 <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
	                                 </td>
	                                 <td class="inv_discount_total">
	                                    <?php echo '-'.app_format_money(($invoice->discount_total + $item_discount), $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                              <?php } ?>

	                              <?php if($invoice->shipping_fee  > 0){ ?>
	                              
	                              <tr id="inv_discount_total">
	                                 <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span>
	                                 </td>
	                                 <td class="inv_discount_total">
	                                    <?php echo app_format_money($invoice->shipping_fee, $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                              <?php } ?>


	                              <tr id="inv_total">
	                                 <td><span class="bold"><?php echo _l('total'); ?></span>
	                                 </td>
	                                 <td class="inv_total bold">
	                                    <?php echo app_format_money($invoice->total, $base_currency->symbol); ?>
	                                 </td>
	                              </tr>
	                           </tbody>
	                        </table>
	                    </div>
		            </div>

	         	</div>
	         	<div role="tabpanel" class="tab-pane" id="payment_record">
	                <div class="col-md-6 pad_left_0" >
	               		<h4 class="font-medium mbot15 bold text-success"><?php echo _l('payment_for_invoice').' '.$invoice->inv_number; ?></h4>
	               	</div>
	               
	               	<div class="clearfix"></div>
	               	<table class="table dt-table dt-inline dataTable no-footer">
	                   	<thead>
	                     <th><?php echo _l('payments_table_amount_heading'); ?></th>
	                      <th><?php echo _l('payments_table_mode_heading'); ?></th>
	                      <th><?php echo _l('payment_transaction_id'); ?></th>
	                      <th><?php echo _l('payments_table_date_heading'); ?></th>
	                      <th><?php echo _l('options'); ?></th>
	                   	</thead>
		                <tbody>
		                    <?php foreach($payment as $pay) { ?>
		                        <tr>
		                           <td><?php echo app_format_money($pay['amount'],$base_currency->symbol); ?></td>
		                           <td><?php echo sa_get_payment_mode_by_id($pay['paymentmode']); ?></td>
		                           <td><?php echo html_entity_decode($pay['transactionid']); ?></td>
		                           <td><?php echo _d($pay['date']); ?></td>
		                           <td>
			                           
			                            <a href="<?php echo site_url('sales_agent/portal/delete_payment_sale_invoice/'.$pay['id'].'/'.$invoice->id); ?>" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" data-placement="top" title="<?php echo _l('delete'); ?>" ><i class="fa fa-remove"></i></a>
		                           </td>
		                        </tr>
		                    <?php } ?>
		                </tbody>
	               	</table>
		        </div>

		        <div role="tabpanel" class="tab-pane" id="attachment">
		        	<?php echo form_open_multipart(site_url('sales_agent/portal/sale_invoice_attachment/'.$invoice->id),array('id'=>'partograph-attachments-upload')); ?>
	                <div class="row">
	                  <div class="col-md-12">
	                    <?php echo render_input('file','file','','file'); ?>
	                  </div>
	               </div>
	               <div class="modal-footer bor_top_0" >
	                   <button id="obgy_btn2" type="submit" class="btn btn-info text-white"><?php echo _l('submit'); ?></button>
	               </div>
	                <?php echo form_close(); ?>
	               
	               <div class="col-md-12" id="purorder_pv_file">
                        <?php
                            $file_html = '';
                            if(count($sale_invoice_attachments) > 0){
                                $file_html .= '<hr />';
                                foreach ($sale_invoice_attachments as $f) {
                                    $href_url = site_url(SA_PATH.'sale_invoice/'.$f['rel_id'].'/'.$f['file_name']).'" download';
                                                    if(!empty($f['external'])){
                                                      $href_url = $f['external_link'];
                                                    }
                                   $file_html .= '<div class="mbot15 row inline-block full-width" data-attachment-id="'. $f['id'].'">
                                  <div class="col-md-8">
                                     <a name="preview-purorder-btn" onclick="preview_purorder_btn(this); return false;" rel_id = "'. $f['rel_id']. '" id = "'.$f['id'].'" href="Javascript:void(0);" class="mbot10 mright5 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'. _l('preview_file').'"><i class="fa fa-eye"></i></a>
                                     <div class="pull-left"><i class="'. get_mime_class($f['filetype']).'"></i></div>
                                     <a href=" '. $href_url.'" target="_blank" download>'.$f['file_name'].'</a>
                                     <br />
                                     <small class="text-muted">'.$f['filetype'].'</small>
                                  </div>
                                  <div class="col-md-4 text-right">';
                                    if(is_primary_contact(get_sa_contact_user_id())){
                                    $file_html .= '<a href="#" class="text-danger" onclick="delete_purorder_attachment('. $f['id'].'); return false;"><i class="fa fa-times"></i></a>';
                                    } 
                                   $file_html .= '</div></div>';
                                }
                                echo html_entity_decode($file_html);
                            }
                         ?>
                    </div>

	               <div id="purorder_file_data"></div>
		        </div>

	        </div>
	   	</div>
	</div>
</div>

<div class="modal fade" id="payment_record_pur" tabindex="-1" role="dialog">
	<div class="modal-dialog dialog_30" >
    <?php echo form_open(site_url('sales_agent/portal/add_invoice_payment/'.$invoice->id),array('id'=>'purinvoice-add_payment-form')); ?>
   		<div class="modal-content">
	        <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	            <h4 class="modal-title">
	                <span class="edit-title"><?php echo _l('edit_payment'); ?></span>
	                <span class="add-title"><?php echo _l('new_payment'); ?></span>
	            </h4>
	        </div>
	        <div class="modal-body">
	            <div class="row">
	                <div class="col-md-12">
	                 <div id="additional"></div>
	                 	<?php echo render_input('amount', '<small class="text-danger">* </small>'._l('amount'),saleinvoice_left_to_pay($invoice->id),'number',array('max' => saleinvoice_left_to_pay($invoice->id))); ?>
	                    <?php echo sa_render_date_input('date','payment_edit_date', date('Y-m-d')); ?>
	                    <?php echo sa_render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode'); ?>
	                    
	                    <?php echo render_input('transactionid','payment_transaction_id'); ?>
	                    <?php echo render_textarea('note','note','',array('rows'=>7)); ?>

	                </div>
	            </div>
	        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<?php require 'modules/sales_agent/assets/js/portal/sale_invoices/detail_invoice_js.php';?>