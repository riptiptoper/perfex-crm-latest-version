<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
               <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-file-text-o menu-icon" aria-hidden="true"></i> <?php echo _l('customer_report_detail'); ?></h4>
                        <?php if(isset($customer_report)){ ?>
                      <h5>
                        <?php echo _l('customer_report_id').': '. $customer_report->id ?>
                      </h5>
                      <h5>
                        <?php echo _l('date_report').': '. $customer_report->m_date_report ?>
                      </h5>
                      <h5>
                        <?php echo _l('total_quatity').': '. app_format_money((float)$customer_report->m_total_quantity,'') ?>
                      </h5>
                      <h5>
                        <?php echo _l('total_amount').': '. app_format_money((float)$customer_report->m_total_amount,'') ?>
                      </h5>
                      
                        <?}
                         ?>
                      <hr />
                    </div>
                  </div>
                  <div class="row">    
                    <div class="_buttons col-md-3">
                        <div class="btn-group mleft5">
                           <a href="<?php echo admin_url('omni_sales/manage_customer_report?group=manage_create_customer_report'); ?>" class="btn btn-success mright-5"  aria-haspopup="true" aria-expanded="false"><?php echo _l('go_to_customer_report_manage').' '; ?></a>
                       </div> 
                    </div> 

                     <div class="_buttons col-md-3">
                        <div class="btn-group mleft5">
                           <a href="<?php echo admin_url('omni_sales/table_create_invoice_from_customer_report_temp/'.$customer_report->id); ?>" class="btn btn-primary mright-5"  aria-haspopup="true" aria-expanded="false"><?php echo _l('_create_invoice').' '; ?></a>
                           
                       </div> 
                    </div> 
                          
                  </div>
 
         </div>
       </div>
     </div>

			<div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">

                  <div class="tab-content col-md-12">
                    <div role="tabpanel" class="tab-pane active row" id="customer_reports">

                      <!-- Import transactions -->
                      <div class="modal bulk_actions" id="table_manage_import_customer_reports_bulk_actions" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                              <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                              </div>
                              <div class="modal-body">
                                 <?php if(has_permission('omni_sales','','delete') || is_admin()){ ?>
                                 <div class="checkbox checkbox-danger">
                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                 </div>
                                
                                 <?php } ?>
                              </div>
                              <div class="modal-footer">
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                                 <?php if(has_permission('omni_sales','','delete') || is_admin()){ ?>
                                 <a href="#" class="btn btn-info" onclick="customer_report_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                  <?php } ?>
                              </div>
                           </div>
                          
                        </div>
                        
                     </div>

                           <div class="clearfix"></div>
                              <table id="dtBasicExample"  class="table dt-table border table-striped">
                               <thead>
                                  <th><?php echo _l('#'); ?></th>
                                  <th><?php echo _l('ser_no'); ?></th>
                                  <th><?php echo _l('authorized_by'); ?></th>
                                  <th><?php echo _l('date'); ?></th>
                                  <th><?php echo _l('transaction_id'); ?></th>
                                  <th><?php echo _l('receipt'); ?></th>
                                  <th><?php echo _l('pay_mode'); ?></th>
                                  <th><?php echo _l('nozzle'); ?></th>
                                  <th><?php echo _l('product'); ?></th>
                                  <th><?php echo _l('quantity'); ?></th>
                                  <th><?php echo _l('total_sale'); ?></th>
                                  <th><?php echo _l('ref_slip_no'); ?></th>
                                  <th><?php echo _l('shift_type'); ?></th>
                                  <th><?php echo _l('customer_id'); ?></th>
                                  <th><?php echo _l('payment_id'); ?></th>

                               </thead>
                                <tbody>

                                  <?php 
                                      $data_insert_customer_report_detail=[];

                                      $data_temp_detail                   =[];
                                      $data_temp_detail['total_by_cash']  =0;
                                      $data_temp_detail['total_by_mpesa'] =0;
                                      $data_temp_detail['total_by_card']  =0;
                                      $data_temp_detail['total_by_invoice']   =0;
                                      $data_temp_detail['total_diesel']       =0;
                                      $data_temp_detail['total_pertrol']      =0;
                                      $data_temp_detail['total_other_product']=0;
                                      $data_temp_detail['total_sale']=0;
                                      $check_authorized_shift_type=[];

                                   ?>

                                  <?php foreach($arr_customer_report as $report_data_key => $customer_report_value){ ?>

                                    <?php 
                                      $data_temp_detail['total_sale'] += (float)$customer_report_value['total_sale'];

                                        switch ($customer_report_value['pay_mode']) {
                                          case 'Cash':
                                            $data_temp_detail['total_by_cash'] += (float)$customer_report_value['total_sale'];
                                            break;

                                          case 'Mobile':
                                            $data_temp_detail['total_by_mpesa'] += (float)$customer_report_value['total_sale'];
                                            break;

                                          case 'Card':
                                            $data_temp_detail['total_by_card'] += (float)$customer_report_value['total_sale'];
                                            break;

                                          case 'Invoice ':
                                            $data_temp_detail['total_by_invoice'] += (float)$customer_report_value['total_sale'];
                                            break;
                                          
                                          default:
                                            # code...
                                            break;
                                        }

                                        switch ($customer_report_value['product']) {
                                          case 'DX':
                                            $data_temp_detail['total_diesel'] += (float)$customer_report_value['total_sale'];
                                            break;
                                          
                                          case 'ULX':
                                            $data_temp_detail['total_pertrol'] += (float)$customer_report_value['total_sale'];
                                            break;
                                          
                                          default:
                                            $data_temp_detail['total_other_product'] += (float)$customer_report_value['total_sale'];
                                            break;
                                        }


                                        //check create
                                        if(count($check_authorized_shift_type) == 0){
                                          //first value
                                          $check_authorized_shift_type['authorized_by']=$customer_report_value['authorized_by'];
                                          $check_authorized_shift_type['shift_type']=$customer_report_value['shift_type'];

                                        }
                                     ?>

                                  <tr>

                                      <td></td>
                                      <td><?php echo html_entity_decode($customer_report_value['ser_no']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['authorized_by']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['date']. ' '. $customer_report_value['time']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['transaction_id']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['receipt']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['pay_mode']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['nozzle']); ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['product']); ?></td>

                                      <td><?php echo app_format_money((float)$customer_report_value['quantity'],'') ?></td>
                                      <td><?php echo app_format_money((float)$customer_report_value['total_sale'],'') ?></td>
                                      <td><?php echo html_entity_decode($customer_report_value['ref_slip_no']); ?></td>
                                      <td><?php echo _l($customer_report_value['shift_type']) ?></td>
                                      <td><?php echo omni_sales_get_customer_name($customer_report_value['customer_id'], $customer_report_value['authorized_by']) ?></td>
                                      <td><?php echo $customer_report_value['payment_id'] ?></td>

                                  </tr>

                                  <?php 
                                      if(count($arr_customer_report) != $report_data_key+1){
                                          if( ($check_authorized_shift_type['authorized_by'] != $arr_customer_report[$report_data_key+1]['authorized_by']) || ($check_authorized_shift_type['shift_type'] != $arr_customer_report[$report_data_key+1]['shift_type'])){ ?>

                                        <!-- total by author -->
                                        <tr class="bold text-danger">
                                            <td></td>
                                            <td ><?php echo _l('authorized_by').': '.  $check_authorized_shift_type['authorized_by'] ; ?></td>
                                            <td><?php echo  _l($check_authorized_shift_type['shift_type']) ?></td>
                                            <td></td>
                                            <td colspan="3">
                                              <?php echo _l('diesel').': '.app_format_money((float) $data_temp_detail['total_diesel'],'') ?><br>
                                              <?php echo _l('pertrol').': '.app_format_money((float) $data_temp_detail['total_pertrol'],'') ?><br>
                                              <?php echo _l('other').': '.app_format_money((float) $data_temp_detail['total_other_product'],'') ?>
                                            </td>
                                            <td class="style-display"></td>
                                            <td class="style-display"></td>
                                            <td></td>
                                            <td colspan="2">
                                              <?php echo _l('cash').': '.app_format_money((float) $data_temp_detail['total_by_cash'],'') ?><br>
                                              <?php echo _l('mpesa').': '.app_format_money((float) $data_temp_detail['total_by_mpesa'],'') ?><br>
                                              <?php echo _l('card').': '.app_format_money((float) $data_temp_detail['total_by_card'],'') ?><br>
                                              <?php echo _l('invoice').': '.app_format_money((float) $data_temp_detail['total_by_invoice'],'') ?><br>
                                            </td class="style-display">
                                            <td class="style-display"></td>

                                            <td>
                                              <?php echo _l('total_sale').': '.app_format_money((float) $data_temp_detail['total_sale'],'') ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                      <?    //reset 
                                            $data_temp_detail                   =[];
                                            $data_temp_detail['total_by_cash']  =0;
                                            $data_temp_detail['total_by_mpesa'] =0;
                                            $data_temp_detail['total_by_card']  =0;
                                            $data_temp_detail['total_by_invoice']   =0;
                                            $data_temp_detail['total_diesel']       =0;
                                            $data_temp_detail['total_pertrol']      =0;
                                            $data_temp_detail['total_other_product']=0;
                                            $data_temp_detail['total_sale']=0;

                                            $check_authorized_shift_type=[];

                                          }
                                        }else{ ?>

                                         <!-- total by author -->
                                        <tr class="bold text-danger">
                                            <td></td>
                                            <td ><?php echo _l('authorized_by').': '.  $check_authorized_shift_type['authorized_by'] ; ?></td>
                                            <td><?php echo  _l($check_authorized_shift_type['shift_type']) ?></td>
                                            <td></td>
                                            <td colspan="3">
                                              <?php echo _l('diesel').': '.app_format_money((float) $data_temp_detail['total_diesel'],'') ?><br>
                                              <?php echo _l('pertrol').': '.app_format_money((float) $data_temp_detail['total_pertrol'],'') ?><br>
                                              <?php echo _l('other').': '.app_format_money((float) $data_temp_detail['total_other_product'],'') ?>
                                            </td>
                                            <td class="style-display"></td>
                                            <td class="style-display"></td>
                                            <td></td>
                                            <td colspan="2">
                                              <?php echo _l('cash').': '.app_format_money((float) $data_temp_detail['total_by_cash'],'') ?><br>
                                              <?php echo _l('mpesa').': '.app_format_money((float) $data_temp_detail['total_by_mpesa'],'') ?><br>
                                              <?php echo _l('card').': '.app_format_money((float) $data_temp_detail['total_by_card'],'') ?><br>
                                              <?php echo _l('invoice').': '.app_format_money((float) $data_temp_detail['total_by_invoice'],'') ?><br>
                                            </td class="style-display">
                                            <td class="style-display"></td>

                                            <td>
                                              <?php echo _l('total_sale').': '.app_format_money((float) $data_temp_detail['total_sale'],'') ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                   <?     }
                                   ?>
                                  <?php } ?>
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


<?php init_tail(); ?>
</body>
</html>
