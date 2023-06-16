               
     <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">

                  <div class="tab-content col-md-12">
                    <div role="tabpanel" class="tab-pane active row" id="customer_reports">

                      <!-- Import transactions -->
                      <div class="modal bulk_actions" id="table_manage_create_customer_reports_bulk_actions" tabindex="-1" role="dialog">
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
                                 <a href="#" class="btn btn-info" onclick="create_customer_report_delete_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                  <?php } ?>
                              </div>
                           </div>
                          
                        </div>
                        
                     </div>

                     <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_manage_create_customer_reports" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

                      <?php
                          $table_data = array(
      
                              '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_manage_create_customer_reports"><label></label></div>',
                                _l('id'),
                                _l('m_date_report'),
                                _l('m_total_amount'),
                                _l('m_total_quantity'),
                                _l('date_time_transaction'),
                                _l('_create_invoice'),
                              );

      
                         render_datatable($table_data,'table_manage_create_customer_reports',
                          array('customizable-table'),
                          array(
                            'proposal_sm' => 'proposal_sm',
      
                             'id'=>'table-table_manage_create_customer_reports',
      
                             'data-last-order-identifier'=>'table_manage_create_customer_reports',
      
                             'data-default-order'=>get_table_last_order('table_manage_create_customer_reports'),
                           )); ?>

                    </div>

                  </div>
                  
               </div>
            </div>
         </div>
