<div class="col-md-12 mtop15">
   <div class="panel_s">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-12">
            <h4><?php echo html_entity_decode($goods_delivery->goods_delivery_code) ?></h4>
          </div>
        </div>
        
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('export_output_slip'); ?>
                     </a>
                  </li>  
               </ul>
            </div>
         </div>

         <div class="clearfix"></div>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                

               <div id="estimate-preview">

          <div class="col-md-12 row-margin">
            <table class="table border table-striped table-margintop ">
              <tbody>
                <?php 
                $customer_name='';
                if($goods_delivery){
                  
                    if(is_numeric($goods_delivery->customer_code)){
                      
                        $customer_name .= get_sa_client_name($goods_delivery->customer_code);
                  }

                }
                 ?>

                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('customer_name'); ?></td>
                    <td><?php echo html_entity_decode($customer_name) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('to'); ?></td>
                    <td><?php echo html_entity_decode($goods_delivery->to_) ; ?></td>
                 </tr>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('address'); ?></td>
                    <td><?php echo html_entity_decode($goods_delivery->address) ; ?></td>
                 </tr>
                 <tr class="project-overview">
                    <td class="bold"><?php echo _l('note_'); ?></td>
                    <td><?php echo html_entity_decode($goods_delivery->description) ; ?></td>
                 </tr>

                 <?php 
                      if( ($goods_delivery->invoice_id != '') && ($goods_delivery->invoice_id != 0) ){ ?>

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('invoices'); ?></td>
                          <td>
                              <a href="<?php echo site_url('sales_agent/portal/sale_invoice_detail'.$goods_delivery->invoice_id) ?>" ><?php echo get_sa_invoice_number($goods_delivery->invoice_id).' '.sa_get_invoice_company($goods_delivery->invoice_id) ?></a>

                            </td>
                       </tr>

                    <?php   }
                  ?>

                  </tbody>
          </table>
        </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="table-responsive">
                           <table class="table items items-preview estimate-items-preview" data-type="estimate">
                              <thead>
                                 <tr>
                                    <th align="center">#</th>
                                    <th  colspan="1"><?php echo _l('commodity_code') ?></th>
                                     <th colspan="1"><?php echo _l('warehouse_name') ?></th>
                                     <th colspan="1"><?php echo _l('available_quantity') ?></th>
                                     <th  colspan="1"><?php echo _l('unit_name') ?></th>
                                     <th  colspan="1" class="text-center"><?php echo _l('quantity') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('rate') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('subtotal') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('subtotal_after_tax') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('discount').'(%)' ?></th>
                                     <th align="right" colspan="1"><?php echo _l('discount(money)') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('lot_number').'/'._l('quantity') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('total_money') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('guarantee_period') ?></th>
            
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                              <?php 
                              $subtotal = 0 ;
                              foreach ($goods_delivery_detail as $delivery => $delivery_value) {
                             $delivery++;
                             $available_quantity = (isset($delivery_value) ? $delivery_value['available_quantity'] : '');
                             $total_money = (isset($delivery_value) ? $delivery_value['total_money'] : '');
                             $discount = (isset($delivery_value) ? $delivery_value['discount'] : '');
                             $discount_money = (isset($delivery_value) ? $delivery_value['discount_money'] : '');
                             $guarantee_period = (isset($delivery_value) ? _d($delivery_value['guarantee_period']) : '');

                             $quantities = (isset($delivery_value) ? $delivery_value['quantities'] : '');
                             $unit_price = (isset($delivery_value) ? $delivery_value['unit_price'] : '');
                             $total_after_discount = (isset($delivery_value) ? $delivery_value['total_after_discount'] : '');

                             $commodity_code = sa_get_commodity_name($delivery_value['commodity_code']) != null ? sa_get_commodity_name($delivery_value['commodity_code'])->commodity_code : '';
                             $commodity_name = sa_get_commodity_name($delivery_value['commodity_code']) != null ? sa_get_commodity_name($delivery_value['commodity_code'])->description : '';
                             $subtotal += (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
                             $item_subtotal = (float)$delivery_value['quantities'] * (float)$delivery_value['unit_price'];
                             


                             $warehouse_name ='';

                            if(isset($delivery_value['warehouse_id']) && ($delivery_value['warehouse_id'] !='')){
                              $arr_warehouse = explode(',', $delivery_value['warehouse_id']);

                              $str = '';
                              if(count($arr_warehouse) > 0){

                                foreach ($arr_warehouse as $wh_key => $warehouseid) {
                                  $str = '';
                                  if ($warehouseid != '' && $warehouseid != '0') {

                                    $team = sa_get_warehouse_name($warehouseid);
                                    if($team){
                                      $value = $team != null ? get_object_vars($team)['warehouse_name'] : '';

                                      $str .= '<span class="label label-tag tag-id-1"><span class="tag">' . $value . '</span><span class="hide">, </span></span>&nbsp';

                                      $warehouse_name .= $str;
                                      if($wh_key%3 ==0){
                                        $warehouse_name .='<br/>';
                                      }
                                    }

                                  }
                                }

                              } else {
                                $warehouse_name = '';
                              }
                            }



                             $unit_name = '';
                             if(is_numeric($delivery_value['unit_id'])){
                                $unit_name = sa_get_unit_type($delivery_value['unit_id']) != null ? sa_get_unit_type($delivery_value['unit_id'])->unit_name : '';
                              }

                             $lot_number ='';
                             if(($delivery_value['lot_number'] != null) && ( $delivery_value['lot_number'] != '') ){
                                $array_lot_number = explode(',', $delivery_value['lot_number']);
                                foreach ($array_lot_number as $key => $lot_value) {
                                    
                                    if($key%2 ==0){
                                      $lot_number .= $lot_value;
                                    }else{
                                      $lot_number .= ' : '.$lot_value.' ';
                                    }

                                }
                             }

                             $commodity_name = $delivery_value['commodity_name'];
                             if(strlen($commodity_name) == 0){
                              $commodity_name = sa_get_item_variatiom($delivery_value['commodity_code']);
                            }

                            ?>
          
                              <tr>
                              <td ><?php echo html_entity_decode($delivery) ?></td>
                                  <td ><?php echo html_entity_decode($commodity_name) ?></td>
                                  <td ><?php echo html_entity_decode($warehouse_name) ?></td>
                                  <td ><?php echo html_entity_decode($available_quantity) ?></td>
                                  <td ><?php echo html_entity_decode($unit_name) ?></td>
                                  <td class="text-right"><?php echo html_entity_decode($quantities) ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$item_subtotal,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$total_money,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$discount,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$discount_money,'') ?></td>
                                  <td class="text-right"><?php echo html_entity_decode($lot_number) ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$total_after_discount,'') ?></td>
                                  <td class="text-right"><?php echo html_entity_decode($guarantee_period) ?></td>
                                </tr>
                             <?php  } ?>
                              </tbody>
                           </table>

                           <div class="col-md-8 col-md-offset-4">
                             <table class="table text-right">
                              <tbody>
                                <tr id="subtotal">
                                  <td class="bold"><?php echo _l('subtotal'); ?></td>
                                  <td><?php echo app_format_money((float)$subtotal, $base_currency); ?></td>
                                </tr>
                                <?php if(isset($goods_delivery) && $tax_data['html_currency'] != ''){
                                  echo html_entity_decode($tax_data['html_currency']);
                                } ?>
                                <tr id="total_discount">
                                  <?php
                                  $total_discount = 0 ;
                                  if(isset($goods_delivery)){
                                    $total_discount += (float)$goods_delivery->total_discount  + (float)$goods_delivery->additional_discount;
                                  }
                                  ?>
                                  <td class="bold"><?php echo _l('total_discount'); ?></td>
                                  <td><?php echo app_format_money((float)$total_discount, $base_currency); ?></td>
                                </tr>
                                <tr id="shipping_fee">
                                  <?php
                                  $shipping_fee = 0 ;
                                  if(isset($goods_delivery)){
                                    $shipping_fee = (float)$goods_delivery->shipping_fee;
                                  }
                                  ?>
                                  <td class="bold"><?php echo _l('wh_shipping_fee'); ?></td>
                                  <td><?php echo app_format_money((float)$shipping_fee, $base_currency); ?></td>
                                </tr>
                                <tr id="totalmoney">
                                  <?php
                                  $after_discount = isset($goods_delivery) ?  $goods_delivery->after_discount : 0 ;
                                  if($goods_delivery->after_discount == null){
                                    $after_discount = $goods_delivery->total_money;
                                  }
                                  ?>
                                 <td class="bold"><?php echo _l('total_money'); ?></td>
                                  <td><?php echo app_format_money((float)$after_discount, $base_currency); ?></td>
                                </tr>
                              </tbody>
                             </table>
                           </div>

                        </div>
                     </div>                                     
                    
                  </div>
               </div>
            </div>

         </div>

      </div>
   </div>
</div>