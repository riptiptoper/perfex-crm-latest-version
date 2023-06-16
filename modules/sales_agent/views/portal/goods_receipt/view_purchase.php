<div class="col-md-12 mtop15">
   <div class="panel_s">
      <div class="panel-body">

         <div class="horizontal-scrollable-tabs preview-tabs-top">
            
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_estimate" aria-controls="tab_estimate" role="tab" data-toggle="tab">
                     <?php echo _l('stock_import'); ?>
                     </a>
                  </li>  

 
               </ul>
            </div>
         </div>

         <div class="clearfix"></div>
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_estimate">
                  <div class="row">
                    <div class="col-md-4">

                    </div>
      
                 </div>
                 
               <div id="estimate-preview">

          <div class="col-md-12 panel-padding">
            <table class="table border table-striped table-margintop" >
              <tbody>

              
                  <tr class="project-overview">
                    <td class="bold" width="30%"><?php echo _l('deliver_name'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->deliver_name) ; ?></td>
                 </tr>
        
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('stock_received_docket_code'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->goods_receipt_code) ; ?></td>
                 </tr>
                <tr class="project-overview">
                    <td class="bold"><?php echo _l('note_'); ?></td>
                    <td><?php echo html_entity_decode($goods_receipt->description) ; ?></td>
                 </tr>

                 <?php 
                   
                      if( ($goods_receipt->pr_order_id != '') && ($goods_receipt->pr_order_id != 0) ){ ?>

                        <tr class="project-overview">
                          <td class="bold"><?php echo _l('reference_purchase_order'); ?></td>
                          <td>
                              <a href="<?php echo site_url('sales_agent/portal/order_detail/'.$goods_receipt->pr_order_id) ?>" ><?php echo sa_get_pur_order_name($goods_receipt->pr_order_id) ?></a>

                            </td>
                       </tr>
                    <?php   }
                  ?>
                  </tr>

                
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
                                     <th  colspan="1"><?php echo _l('unit_name') ?></th>
                                     <th  colspan="2" class="text-center"><?php echo _l('quantity') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('unit_price') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('total_money') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('tax_money') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('lot_number') ?></th>
                                     <th align="right" colspan="1"><?php echo _l('expiry_date') ?></th>
                                 </tr>
                              </thead>
                              <tbody class="ui-sortable">
                                
                              <?php 
                              foreach ($goods_receipt_detail as $receipt_key => $receipt_value) {

                                $receipt_key++;
                             $quantities = (isset($receipt_value) ? $receipt_value['quantities'] : '');
                             $unit_price = (isset($receipt_value) ? $receipt_value['unit_price'] : '');
                             $unit_price = (isset($receipt_value) ? $receipt_value['unit_price'] : '');
                             $goods_money = (isset($receipt_value) ? $receipt_value['goods_money'] : '');

                             $commodity_code = sa_get_commodity_name($receipt_value['commodity_code']) != null ? sa_get_commodity_name($receipt_value['commodity_code'])->commodity_code : '';
                             $commodity_name = sa_get_commodity_name($receipt_value['commodity_code']) != null ? sa_get_commodity_name($receipt_value['commodity_code'])->description : '';

                             $unit_name ='';
                             if(is_numeric($receipt_value['unit_id'])){
                               $unit_name = (sa_get_unit_type($receipt_value['unit_id']) != null && isset(sa_get_unit_type($receipt_value['unit_id'])->unit_name)) ? sa_get_unit_type($receipt_value['unit_id'])->unit_name : '';

                             }

                              $warehouse_code = sa_get_warehouse_name($receipt_value['warehouse_id']) != null ? sa_get_warehouse_name($receipt_value['warehouse_id'])->warehouse_name : '';
                              $tax_money =(isset($receipt_value) ? $receipt_value['tax_money'] : '');
                              $expiry_date =(isset($receipt_value) ? $receipt_value['expiry_date'] : '');
                              $lot_number =(isset($receipt_value) ? $receipt_value['lot_number'] : '');
                              $commodity_name = $receipt_value['commodity_name'];
                              if(strlen($commodity_name) == 0){
                                $commodity_name = sa_get_item_variatiom($receipt_value['commodity_code']);
                              }

                              if(strlen($receipt_value['serial_number']) > 0){
                                $name_serial_number_tooltip = _l('wh_serial_number').': '.$receipt_value['serial_number'];
                              }else{
                                $name_serial_number_tooltip = '';
                              }


                            ?>
          
                              <tr data-toggle="tooltip" data-original-title="<?php echo html_entity_decode($name_serial_number_tooltip); ?>">
                              <td ><?php echo html_entity_decode($receipt_key) ?></td>
                                  <td ><?php echo html_entity_decode($commodity_name) ?></td>
                                  <td ><?php echo html_entity_decode($warehouse_code) ?></td>
                                  <td ><?php echo html_entity_decode($unit_name) ?></td>
                                  <td ></td>
                                  <td class="text-right" ><?php echo html_entity_decode($quantities) ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$unit_price,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$goods_money,'') ?></td>
                                  <td class="text-right"><?php echo app_format_money((float)$tax_money,'') ?></td>
                                  <td class="text-right"><?php echo html_entity_decode($lot_number) ?></td>
                                  <td class="text-right"><?php echo _d($expiry_date) ?></td>
                                </tr>
                             <?php  } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <div class="col-md-6 col-md-offset-6">
                      <table class="table text-right table-margintop">
                        <tbody>
                          <tr class="project-overview" id="subtotal">
                            <td class="td_style"><span class="bold"><?php echo _l('total_goods_money'); ?></span>
                            </td>
                            <?php $total_goods_money = (isset($goods_receipt) ? $goods_receipt->total_goods_money : '');?>
                            <td><?php echo app_format_money((float)$total_goods_money, $base_currency); ?></td>
                          </tr>

                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('value_of_inventory'); ?></span>
                            </td>
                            <?php $value_of_inventory = (isset($goods_receipt) ? $goods_receipt->value_of_inventory : '');?>
                            <td><?php echo app_format_money((float)$value_of_inventory, $base_currency); ?></td>
                          </tr>
                          
                          <?php if(isset($goods_receipt) && $tax_data['html_currency'] != ''){
                            echo html_entity_decode($tax_data['html_currency']);
                          } ?>
                          
                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('total_tax_money'); ?></span>
                            </td>
                            <?php $total_tax_money = (isset($goods_receipt) ? $goods_receipt->total_tax_money : '');?>
                            <td><?php echo app_format_money((float)$total_tax_money, $base_currency); ?></td>
                          </tr>

                          <tr class="project-overview">
                            <td class="td_style"><span class="bold"><?php echo _l('total_money'); ?></span>
                            </td>
                            <?php $total_money = (isset($goods_receipt) ? $goods_receipt->total_money : '');?>
                            <td><?php echo app_format_money((float)$total_money, $base_currency); ?></td>

                          </tr>
                        </tbody>
                      </table>
                    </div>

                  </div>
               </div>
            </div>

         </div>

         <div class="modal fade" id="add_action" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         
        <div class="modal-body">
         <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
            <div class="signature-pad--body">
              <canvas id="signature" height="130" width="550"></canvas>
            </div>
            <input type="text" class="sig-input-style" tabindex="-1" name="signature" id="signatureInput">
            <div class="dispay-block">
              <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><?php echo _l('clear'); ?></button>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
           <button onclick="sign_request(<?php echo html_entity_decode($goods_receipt->id); ?>);"  autocomplete="off" class="btn btn-success sign_request_class"><?php echo _l('e_signature_sign'); ?></button>
          </div>


      </div>
   </div>
</div>

      </div>
   </div>
</div>