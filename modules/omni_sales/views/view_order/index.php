<?php hooks()->do_action('head_element_client'); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">     
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div>
                     <div class="tab-content">
                        <?php
                        $this->load->view('view_order/order_detailt_partial'); 
                        ?>
                     </div>
                     <div class="tab-content">
                        <?php
                        $show_shipment = get_option('omni_allow_showing_shipment_in_public_link');
                        if($show_shipment && $show_shipment == 1){
                           $this->load->view('view_order/shipment_order');                            
                        } 
                        ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php hooks()->do_action('client_pt_footer_js'); ?>


