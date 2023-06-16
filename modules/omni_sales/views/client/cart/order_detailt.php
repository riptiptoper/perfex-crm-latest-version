<?php hooks()->do_action('head_element_client'); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">     

         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($client)){ ?>
                     <?php echo form_hidden('isedit'); ?>
                     <?php echo form_hidden('userid', $client->userid); ?>
                     <div class="clearfix"></div>
                  <?php } ?>
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
                <div>
                  <div class="tab-content">
                     <?php
                     $data['status'] = $status;
                     $data['status_key'] = $status_key;
                     $this->load->view('client/cart/order_detailt_partial', $data); ?>
                  </div>
                  <div class="tab-content mtop10">
                     <?php
                     $this->load->view('view_order/shipment_order');                            
                     ?>
                  </div>
                  <div class="row">
                     <div class="col-md-12 mtop15">
                        <a href="<?php echo site_url('omni_sales/omni_sales_client/index/1/0/0'); ?>" class="btn btn-default"><?php echo _l('continue_shopping'); ?></a>
                        <?php  if($status_key == 'draft'){ ?>
                         <button class="btn btn-primary pull-right" onclick="open_modal_chosse();">
                          <?php echo _l('cancel_order'); ?>
                       </button>    
                    <?php } ?>   
                    <?php  if(can_refund_order($order->id)){ ?>
                       <button class="btn btn-primary pull-right" onclick="open_refund_modal();">
                        <?php echo _l('omni_request_a_refund'); ?>
                     </button>   
                  <?php } ?>                  
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
</div>
</div>
<?php hooks()->do_action('client_pt_footer_js'); ?>


