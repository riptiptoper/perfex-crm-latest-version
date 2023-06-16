

         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-file-text-o menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                  <div class="row">    
                    <div class="_buttons col-md-3">
                    	<?php if (has_permission('omni_sales', '', 'create') || is_admin()) { ?>
                        
                        <div class="btn-group mleft5">
                           <a href="<?php echo admin_url('omni_sales/import_customer_report_csv'); ?>" class="btn btn-success "  aria-haspopup="true" aria-expanded="false"><?php echo _l('importing_customer_report').' '; ?></a>
                       </div> 

                    	<?php } ?>
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

                     <div class="row">

                      <?php echo form_open_multipart(admin_url('omni_sales/create_report_transation_bulk_action'), array('id'=>'create_report_transation_bulk_action')); ?>
                       <div class="col-md-3">
                          <div class="form-group">
                            <label for="version_value" class="control-label"></label>
                             <div class="_buttons ">
                                  <?php if (has_permission('omni_sales', '', 'create') || is_admin()) { ?>
                                  <a href="#" onclick="create_invoice_from_customer_report_bulk_action(this); return false;" class="btn btn-info pull-left mright10 display-block">
                                      <?php echo _l('create_invoice'); ?>
                                  </a>
                                  <a href="#" onclick="create_report_transaction_bulk_action(this); return false;" class="btn btn-info pull-left isplay-block">
                                      <?php echo _l('create_report'); ?>
                                  </a>
                                  <?php } ?>
                            </div>
                            <div class="id_customer_report hide">
                            </div>

                            <div class="id_from_date hide">
                            </div>

                            <div class="id_to_date hide">
                            </div>
                            
                        </div>
                       </div>
                    <?php echo form_close(); ?>



                       <div class="col-md-3">
                         <div class="form-group">
                            <label for="filter_authorized_by"><?php echo _l('authorized_by'); ?></label>
                            <select name="filter_authorized_by[]" id="authorized" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" >
                                <?php foreach($authorized as $a) { ?>
                                <option value="<?php echo html_entity_decode($a['authorized_by']); ?>" ><?php echo html_entity_decode($a['authorized_by']); ?></option>
                                  <?php } ?>
                            </select>
                          </div>
                       </div>

                       <div class="col-md-2">
                         <?php echo render_datetime_input('from_date', 'from_date'); ?>
                       </div>
                       <div class="col-md-2">
                         <?php echo render_datetime_input('to_date', 'to_date'); ?>
                       </div>

                       <div class="col-md-2">
                         <!-- filter by version -->
                         <div class="form-group">
                          <label for="filter_shift_type" class="control-label"><?php echo _l('filter_by_shift_type'); ?></label>
                          <select name="filter_shift_type[]" class="selectpicker" data-width="100%" multiple="true" > 
                              <option value="day_shift"><?php echo _l('day_shift') ?></option>                  
                              <option value="night_shift"><?php echo _l('night_shift') ?></option>                  
                          </select>

                        </div>

                      </div>
                     </div>
                     <a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_manage_import_customer_reports" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

                      <?php
                          $table_data = array(
                              '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_manage_import_customer_reports"><label></label></div>',
                             
                             _l('id'),
                             _l('ser_no'),
                             _l('authorized_by'),
                             _l('date'),
                             _l('time'),
                             _l('transaction_id'),
                             _l('receipt'),
                             _l('pay_mode'),
                             _l('nozzle'),
                             _l('product'),
                             _l('quantity'),
                             _l('total_sale'),
                             _l('ref_slip_no'),
                             _l('date_add'),
                             _l('version'),
                              );

                         render_datatable($table_data,'table_manage_import_customer_reports',
                          array('customizable-table'),
                          array(
                            'proposal_sm' => 'proposal_sm',
                             'id'=>'table-table_manage_import_customer_reports',
                             'data-last-order-identifier'=>'table_manage_import_customer_reports',
                             'data-default-order'=>get_table_last_order('table_manage_import_customer_reports'),
                           )); ?>

                    </div>

                  </div>
                  
               </div>
            </div>
         </div>


<!-- edit transaction -->
<div class="modal" id="edit_customer_reports" tabindex="-1" role="dialog">
        <div class="modal-dialog w-70">
          <?php echo form_open_multipart(admin_url('omni_sales/edit_customer_report'), array('id'=>'edit_customer_report')); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="edit-title"><?php echo _l('edit_customer_report'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="edit_customer_reports_id_t"></div>   
                          <div class="form"> 
                           
                            <div class="col-md-4">
                              <?php echo render_input('ser_no','ser_no','','number',['disabled' => true]) ?>
                            </div>
                            <div class="col-md-4">
                              <?php echo render_input('authorized_by','authorized_by','','text',['disabled' => true]) ?>
                            </div>
                            <div class="col-md-4">
                              <?php echo render_date_input('date', 'date','' ,['disabled' => true]); ?>
                            </div>
                            <div class="col-md-4">
                              <?php echo render_input('time', 'time','','' ,['disabled' => true]); ?>
                            </div>
                            <div class="col-md-4">
                              <?php echo render_input('transaction_id','transaction_id','','text',['disabled' => true]) ?>
                            </div>
                             <div class="col-md-4">
                              <?php echo render_input('receipt','receipt','','text',['disabled' => true]) ?>
                            </div>

                             <div class="col-md-4">
                              <div class="form-group">
                                <label for="pay_mode_id"><?php echo _l('pay_mode'); ?></label>
                                  <select name="pay_mode_id" id="pay_mode_id" class="selectpicker" data-live-search="true" data-width="100%" >
                                      <option value=""></option>
                                      <?php foreach($payment_modes as $pm) { ?>
                                      <option value="<?php echo html_entity_decode($pm['id']); ?>" ><?php echo html_entity_decode($pm['name']); ?></option>
                                        <?php } ?>
                                  </select>
                                </div>

                            </div>

                             <div class="col-md-4">
                              <?php echo render_input('nozzle','nozzle','','number',['disabled' => true]) ?>
                            </div>
                             <div class="col-md-4">
                              <?php echo render_input('product','product','','text',['disabled' => true]) ?>
                            </div>
                            
                             <div class="col-md-4">
                              <?php 
                                $attr = array();
                                $attr = ['data-type' => 'currency'];
                                $attr = ['disabled' => true];
                              echo render_input('quantity','quantity','', 'text', $attr) ?>
                            </div>

                            <div class="col-md-4">
                              <?php 
                                $attr = array();
                                $attr = ['data-type' => 'currency'];
                                $attr = ['disabled' => true];
                                 echo render_input('total_sale', 'total_sale','', 'text', $attr); ?>
                            </div>
                              <div class="col-md-4">
                              <?php echo render_input('ref_slip_no','ref_slip_no','','text') ?>
                            </div>
       
                             <div class="col-md-4">
                              <div class="form-group">
                                <label for="customer_id"><?php echo _l('customer_name'); ?></label>
                                  <select name="customer_id" id="customer_id" class="selectpicker" data-live-search="true" data-width="100%" >
                                      <?php foreach($customer_code as $s) { ?>
                                      <option value="<?php echo html_entity_decode($s['userid']); ?>" ><?php echo html_entity_decode($s['company']); ?></option>
                                        <?php } ?>
                                  </select>
                                </div>

                            </div>

                            <div class="col-md-4">
                              <?php echo render_input('payment_id','payment_id','','text') ?>
                            </div>

                          </div>
                        </div>
                    </div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                        
                         <button type="submit" class="btn btn-info intext-btn"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
    </div> 
