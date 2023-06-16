<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>


<?php echo form_hidden('_attachment_sale_id',$estimate->id); ?>
<?php echo form_hidden('_attachment_sale_type','estimate'); ?>
<?php
   $base_currency = get_base_currency();
    if($estimate->currency != ''){
      $base_currency = $estimate->currency;
    }
 ?>
<div id="wrapper">
 <div class="content">
  <div class="row"> 
 <div class="col-md-12 ">
<div class="panel_s">
   <div id="page-content" class="panel-body">
      <div class="card clearfix">
         <?php if($estimate->approve_status == 1){ ?>
           <div class="ribbon info span_style"><span><?php echo _l('purchase_draft'); ?></span></div>
       <?php }elseif($estimate->approve_status == 2){ ?>
         <div class="ribbon success"><span><?php echo _l('purchase_approved'); ?></span></div>
       <?php }elseif($estimate->approve_status == 3){ ?>  
         <div class="ribbon warning"><span><?php echo _l('pur_rejected'); ?></span></div>
       <?php }elseif ($estimate->approve_status == 4) { ?>
         <div class="ribbon danger"><span><?php echo _l('cancelled'); ?></span></div>
      <?php  } ?> 

        <div class="page-title clearfix">
          <h4 class="font-bold"><?php echo html_entity_decode($title); ?></h4>
        </div>

         
        <div class="horizontal-scrollable-tabs preview-tabs-top">

            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('pur_order'); ?>
                     </a>
                  </li>
                  
                  
                  <li role="presentation" class="tab-separator">
                     <a href="#attachment" aria-controls="attachment" role="tab" data-toggle="tab">
                     <?php echo _l('attachment'); ?>
                     </a>
                  </li>  
                  
                  <?php if(isset($shipment)){ ?>
                    <li role="presentation" class="">
                       <a href="#shipment" aria-controls="shipment" role="tab" data-toggle="tab">
                       <?php echo _l('sa_shipment'); ?>
                       </a>
                    </li>  
                  <?php } ?>
                  
               </ul>
            </div>
         </div>
            
         <div class="row mtop10">
            <div class="col-md-4">
             
              <?php 
                $order_status_class = '';
                $order_status_text = '';
                if($estimate->order_status == 'new'){
                  $order_status_class = 'label-info';
                  $order_status_text = _l('new_order');
                }else if($estimate->order_status == 'delivered'){
                  $order_status_class = 'label-success';
                  $order_status_text = _l('delivered');
                }else if($estimate->order_status == 'confirmed'){
                  $order_status_class = 'label-warning';
                  $order_status_text = _l('confirmed');
                }else if($estimate->order_status == 'cancelled'){
                  $order_status_class = 'label-danger';
                  $order_status_text = _l('cancelled');
                }else if($estimate->order_status == 'return'){
                   $order_status_class = 'label-warning';
                   $order_status_text = _l('pur_return');
                }else if($estimate->order_status == 'delivering'){
                   $order_status_class = 'label-info';
                   $order_status_text = _l('pur_delivering');
                }
               ?>

               <?php if($estimate->order_status != null){ ?>
               <p class="bold p_mar"><?php echo _l('order_status').': '; ?><span class="label <?php echo html_entity_decode($order_status_class); ?>"><?php echo html_entity_decode($order_status_text); ?></span></p>
               <?php } ?>

              
            </div>
            <div class="col-md-8">
               <div class="btn-group pull-right">
                  <a href="javascript:void(0)" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                  <ul class="dropdown-menu dropdown-menu-right">
                     <li class="hidden-xs"><a href="<?php echo site_url('sales_agent/portal/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                     <li class="hidden-xs"><a href="<?php echo site_url('sales_agent/portal/purorder_pdf/'.$estimate->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                     <li><a href="<?php echo site_url('sales_agent/portal/purorder_pdf/'.$estimate->id); ?>"><?php echo _l('download'); ?></a></li>
                     <li>
                        <a href="<?php echo site_url('sales_agent/portal/purorder_pdf/'.$estimate->id.'?print=true'); ?>" target="_blank">
                        <?php echo _l('print'); ?>
                        </a>
                     </li>
                  </ul>

               </div>

               <?php if(is_admin()){ ?>
                 <div class="btn-group pull-right mright5">
                     <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php echo _l('more'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">

                        <?php if(is_admin()){ ?>
                        <?php 
                          $statuses = [
                            'new',
                            'delivered',
                            'confirmed',
                            'cancelled',
                            'delivering'
                          ]; 
                        ?>

                        <?php foreach($statuses as $status){ ?>
                          <?php if($status != $estimate->order_status){ ?>
                            <li>
                               <a href="<?php echo admin_url('sales_agent/mark_pur_order_as/'.$status.'/'.$estimate->id); ?>"><?php echo _l('invoice_mark_as',_l($status)); ?></a>
                            </li>
                        <?php } ?>
                      <?php } ?>    
                        <?php } ?>

                     </ul>
                  </div>


                  <?php if($estimate->order_status != 'cancelled' && (!is_numeric($estimate->invoice_id) ||  $estimate->invoice_id == 0)){ ?>
                    <a href="<?php echo admin_url('sales_agent/create_invoice/'.$estimate->id); ?>" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success pull-right mright5">
                     <?php echo _l('create_invoice'); ?>
                     </a>
                  <?php } ?>
                <?php } ?>

                <?php if(sa_get_status_modules('warehouse') == true){ ?>
                 <?php if(is_numeric($estimate->invoice_id) && $estimate->invoice_id > 0 ){ ?>
                  <?php if($estimate->stock_export_id == 0){ ?>
                    <a href="<?php echo admin_url('sales_agent/create_export_stock/'.$estimate->invoice_id.'/'.$estimate->id); ?>" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-warning pull-right mright5" ><?php echo _l('sa_create_export_stock'); ?></a>
                  <?php }else{ ?>
                    <a href="<?php echo admin_url('warehouse/manage_delivery#'.$estimate->stock_export_id); ?>" class="btn btn-success pull-right mright5"><?php echo _l('sa_view_export_stock'); ?></a>
                  <?php } ?>
                 <?php } ?>
               <?php } ?>
            </div>
         </div>

         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">

            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
               <div id="estimate-preview">
                  <div class="row ml5 mr5">

   
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold mbot5">
                         
                           <a href="<?php echo admin_url('sales_agent/order_detail/'.$estimate->id); ?>">
                           <span id="estimate-number">
                           <?php echo html_entity_decode($estimate->order_number.' - '.$estimate->order_name); ?>
                           </span>
                           </a>
                        </h4>

                        <?php if(is_numeric($estimate->invoice_id)){ ?>
                              <p><a href="<?php echo admin_url('invoices#'.$estimate->invoice_id); ?>">
                                <?php echo format_invoice_number($estimate->invoice_id); ?>
                              </a></p>
                        <?php } ?>

                        <p><?php echo _l('order_date').': '. _d($estimate->order_date); ?></p>
                        <p><?php echo _l('delivery_date').': '. _d($estimate->delivery_date); ?></p>
                        <p><?php echo _l('agent').': <a href="'.admin_url('sales_agent/sale_agent/'.$estimate->agent_id).'">'. get_company_name($estimate->agent_id).'</a>'; ?></p>
                        <address class="mbot5">
                           <?php $agent = get_client($estimate->agent_id);
                           echo format_customer_info($agent, 'invoice', 'shipping'); ?>
                        </address>

                     </div>
                     
                     
                  </div>
                  <div class="row ml5 mr5">
                     <div class="col-md-12">

                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th class="description" width="25%" align="left"><?php echo _l('items'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('purchase_quantity'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('purchase_unit_price'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('into_money'); ?></th>
                                
                                    <th align="right" class="text-right"><?php echo _l('tax'); ?></th>
                              
                                    <th align="right" class="text-right"><?php echo _l('sub_total'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('discount(%)'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('discount(money)'); ?></th>
                                    <th align="right" class="text-right"><?php echo _l('total'); ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">

                                 <?php $item_discount = 0;
                                 if(count($estimate_detail) > 0){
                                    $count = 1;
                                    $t_mn = 0;
                                    
                                 foreach($estimate_detail as $es) { ?>
                                 <tr nobr="true" class="sortable">
                                    <td class="dragger item_no ui-sortable-handle" align="center"><?php echo html_entity_decode($count); ?></td>
                                    <td class="description" align="left;"><span><strong><?php 
                                    $item = sa_get_item_hp($es['item_code']); 
                                    if(isset($item) && isset($item->commodity_code) && isset($item->title)){
                                       echo html_entity_decode($item->commodity_code.' - '.$item->title);
                                    }else{
                                       echo html_entity_decode($es['item_name']);
                                    }
                                    ?></strong><?php if($es['description'] != ''){ ?><br><span><?php echo html_entity_decode($es['description']); ?></span><?php } ?></td>
                                    <td align="right"  width="12%"><?php echo html_entity_decode($es['quantity']); ?></td>
                                    <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency); ?></td>
                                    <td align="right"><?php echo app_format_money($es['into_money'],$base_currency); ?></td>
                             
                                    <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency); ?></td>
                              
                                    <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency); ?></td>
                                    <td class="amount" width="12%" align="right"><?php echo ($es['discount_%'].'%'); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency); ?></td>
                                    <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency); ?></td>
                                 </tr>
                              <?php 
                              $t_mn += $es['total_money'];
                              $item_discount += $es['discount_money'];
                              $count++; } } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-7"></div>
                         <div class="col-md-5 col-md-offset-7">
                            <table class="table text-right">
                               <tbody>
                                  <tr id="">
                                     <td><span class="bold"><?php echo _l('subtotal'); ?></span>
                                     </td>
                                     <td class="">
                                        <?php echo app_format_money($estimate->subtotal,$base_currency); ?>
                                     </td>
                                  </tr>

                                  <?php if($tax_data['preview_html'] != ''){
                                    echo html_entity_decode($tax_data['preview_html']);
                                  } ?>


                                  <?php if(($estimate->discount_total + $item_discount) > 0){ ?>
                                  
                                  <tr id="">
                                     <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                                     </td>
                                     <td class="">
                                        <?php echo '-'.app_format_money(($estimate->discount_total + $item_discount), $base_currency); ?>
                                     </td>
                                  </tr>
                                  <?php } ?>

                                  <?php if($estimate->shipping_fee > 0){ ?>
                                    <tr id="">
                                      <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                                      <td class="">
                                        <?php echo app_format_money($estimate->shipping_fee, $base_currency); ?>
                                      </td>
                                    </tr>
                                  <?php } ?>


                                  <tr id="">
                                     <td><span class="bold"><?php echo _l('total'); ?></span>
                                     </td>
                                     <td class=" bold">
                                        <?php echo app_format_money($estimate->total, $base_currency); ?>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                         </div>   
                       </div>
                     <?php if($estimate->vendornote != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
                        <p><?php echo html_entity_decode($estimate->vendornote); ?></p>
                     </div>
                     <?php } ?>
                                                            
                     <?php if($estimate->terms != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                        <p><?php echo html_entity_decode($estimate->terms); ?></p>
                     </div>
                     <?php } ?>
                  </div>

               </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="attachment">
               <?php echo form_open_multipart(admin_url('sales_agent/purchase_order_attachment/'.$estimate->id),array('id'=>'partograph-attachments-upload')); ?>
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
                                        if(count($pur_order_attachments) > 0){
                                            $file_html .= '<hr />';
                                            foreach ($pur_order_attachments as $f) {
                                                $href_url = site_url(SA_PATH.'pur_order/'.$f['rel_id'].'/'.$f['file_name']).'" download';
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
                                                if($f['staffid'] == get_staff_user_id() || is_admin()){
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

            <?php if(isset($shipment)){ ?>
              <div role="tabpanel" class="tab-pane" id="shipment">
                <?php $this->load->view('orders/shipment_order');   ?>
              </div>
            <?php } ?>

         </div>
      </div>
   </div>
</div>
</div></div></div>

    
<div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body border">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="ip_style d-none" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            
            </div>

          </div>
          <div class="modal-footer">
           <button onclick="sign_request(<?php echo html_entity_decode($estimate->id); ?>);" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" class="btn btn-success"><?php echo _l('e_signature_sign'); ?></button>
          </div>

      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<?php require 'modules/sales_agent/assets/js/orders/order_detail_js.php';?>
