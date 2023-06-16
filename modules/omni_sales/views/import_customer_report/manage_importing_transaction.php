<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-12">
                      <h4 class="no-margin font-bold"><i class="fa fa-file-text-o menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
         </div>
       </div>
     </div>
     

               
     <div class="col-md-12" id="small-table">
            <div class="panel_s">
               <div class="panel-body">


                  <div class="tab-content col-md-12">

                       <!--Import mpesatransc -->            

                      <div class="modal bulk_actions" id="table_manage_import_mpesatransc_bulk_actions" tabindex="-1" role="dialog">
                          <div class="modal-dialog" role="document">
                             <div class="modal-content">
                                <div class="modal-header">
                                   <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                   <?php if(has_permission('warehouse','','delete') || is_admin()){ ?>
                                   <div class="checkbox checkbox-danger">
                                      <input type="checkbox" name="mass_delete1" id="mass_delete1">
                                      <label for="mass_delete1"><?php echo _l('mass_delete'); ?></label>
                                   </div>
                                  
                                   <?php } ?>
                                </div>
                                <div class="modal-footer">
                                   <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>

                                   <?php if(has_permission('warehouse','','delete') || is_admin()){ ?>
                                   <a href="#" class="btn btn-info" onclick="warehouse_delete_bulk_action_mpesatransc(this); return false;"><?php echo _l('confirm'); ?></a>
                                    <?php } ?>
                                </div>
                             </div>
                            
                          </div>
                          
                       </div>

                       <div class="row">

                            <div class=" col-md-2">
                              <div class="form-group">
                                <label><?php echo _l('group_by'); ?></label>
                                <select name="group_by[]" id="group_by" class="selectpicker"  data-live-search="true" multiple="true" data-width="100%" >

                                      <option value="group_by_phone"><?php echo _l('group_by_phone') ; ?></option>
                                      <option value="group_by_date"><?php echo _l('group_by_date') ; ?></option>
                                      <option value="group_by_staff"><?php echo _l('group_by_staff') ; ?></option>

                                  </select>
                              </div>
                            </div>


                            <div class="col-md-2">
                              <?php echo render_input('filter_by_phone','filter_by_phone','','number') ?>
                            </div>

                            <div class="col-md-3">
                              <?php echo render_date_input('from_date', 'from_date_t'); ?>
                            </div>

                            <div class="col-md-3">
                              <?php echo render_date_input('to_date', 'to_date_t'); ?>
                            </div>


                         
                       </div>
                       <a href="#"  onclick="staff_bulk_actions_mpesatransc(); return false;" data-toggle="modal" data-table=".table-table_manage_import_mpesatransc" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>


                        <?php
                          $table_data = array(
                              '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_manage_import_mpesatransc"><label></label></div>',


                              _l('id'),
                              _l('transc_id'),
                              _l('trans_type'),
                              _l('trans_time'),
                              _l('trans_date'),
                              _l('trans_amount'),
                              _l('phone'),
                              _l('first_name'),
                              _l('middle_name'),
                              _l('last_name'),
                              _l('bill_ref_number'),
                              _l('short_code'),
                              _l('trans_id'),
                              _l('sale_id'),
                              _l('pumpId'),
                              _l('employee_name'),
                              _l('mpesaType'),
                              _l('total_transc'),
                              _l('customer_name'),
                              _l('staffname'),


                              );
                          render_datatable($table_data,'table_manage_import_mpesatransc',
                            array('customizable-table'),
                          array(
                            'proposal_sm' => 'proposal_sm',
                             'id'=>'table-table_manage_import_mpesatransc',
                             'data-last-order-identifier'=>'table_manage_import_mpesatransc',
                             'data-default-order'=>get_table_last_order('table_manage_import_mpesatransc'),
                           )); ?>
                  </div>
                    

                
                  
               </div>
            </div>
         </div>

      </div>
   </div>
</div>


<!-- edit mpesatransc -->
<div class="modal" id="edit_mpesatransc" tabindex="-1" role="dialog">
        <div class="modal-dialog w-50">
          <?php echo form_open_multipart(admin_url('omni_sales/edit_mpesatransc'), array('id'=>'edit_mpesatransc')); ?>

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="add-title"><?php echo _l('add_edit_mpesatransc'); ?></span>
                        <span class="edit-title"><?php echo _l('edit_edit_mpesatransc'); ?></span>
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                             <div id="edit_mpesatransc_id_t"></div>   
                          <div class="form">
                            <div class="row">
                            <div class="col-md-6">
                              <?php echo render_input('transc_id', 'transc_id','','', ['disabled' => true]); ?>
                            </div>
                            <div class="col-md-6">
                              <?php echo render_input('trans_type', 'trans_type','','', ['disabled' => true]); ?>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <?php echo render_input('trans_id', 'trans_id','','', ['disabled' => true]); ?>
                            </div>
                            <div class="col-md-6">
                              <?php echo render_input('sale_id', 'sale_id','','', ['disabled' => true]); ?>
                            </div>
                          </div>

                            <div class="row">
                              <div class="col-md-6">
                                 <?php 
                                        $min_p =[];
                                        $min_p['min']='0';
                                        $min_p['disabled']= true;

                                     ?>
                                <?php echo render_input('pumpId','pumpId','','number', $min_p) ?>
                              </div>
                              <div class="col-md-6">
                                <?php echo render_input('employee_name', 'employee_name','','', ['disabled' => true]); ?>
                              </div>
                            </div>

                            <div class="row">
                            <div class="col-md-6">
                              <?php echo render_input('mpesaType', 'mpesaType','','', ['disabled' => true]); ?>
                            </div>
                            <div class="col-md-6">
                              <?php echo render_input('short_code', 'short_code', '', 'number', ['disabled' => true]); ?>
                            </div>
                          </div>
                            
                          <div class="row">
                            <div class="col-md-6">
                              <?php echo render_datetime_input('trans_date', 'trans_date', '',  ['disabled' => true]); ?>
                            </div>
                            <div class="col-md-6">
                              <?php echo render_input('trans_time', 'trans_time','','', ['disabled' => true]) ?>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <?php echo render_input('bill_ref_number', 'bill_ref_number', '','number',  ['disabled' => true]); ?>
                            </div>
                            
                            <div class="col-md-6">
                              <?php echo render_input('first_name', 'first_name','','', ['disabled' => true]); ?>
                            </div>
                          </div>

                          <div class="row">

                             <div class="col-md-6">
                              <?php echo render_input('middle_name', 'middle_name','','', ['disabled' => true]); ?>
                            </div>
                             <div class="col-md-6">
                              <?php echo render_input('last_name', 'last_name','','', ['disabled' => true]); ?>
                            </div>
                          </div>
                          
                          <div class="row">
                            <div class="col-md-6">
                              <?php echo render_input('phone', 'phone','','', ['disabled' => true]); ?>
                            </div>

                            <div class="col-md-6">
                              <?php 
                                $attr = array();
                                $attr = ['data-type' => 'currency'];
                                 echo render_input('trans_amount', 'trans_amount','', 'text', $attr); ?>
                            </div>
                          </div>

                          <div class="row">

                            <div class="col-md-6">
                              <div class="form-group">

                              <label for="staffname"><?php echo _l('staffname'); ?></label>
                              <select name="staffname" id="staffname" class="selectpicker" data-live-search="true" data-width="100%" >
                                <option value=""></option>
                                    <?php foreach($staffs as $s) { ?>
                                    <option value="<?php echo html_entity_decode($s['staffid']); ?>"><?php echo html_entity_decode($s['lastname'] . ' '. $s['firstname']); ?></option>
                                    <?php } ?>
                              </select>
                            </div>

                            </div>
                            
                             <div class="col-md-6">
                              <div class="form-group">
                                <label for="customer_id"><?php echo _l('customer_name'); ?></label>
                                  <select name="customer_id" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" >
                                      <option value=""></option>
                                      <?php foreach($customer_code as $s) { ?>
                                      <option value="<?php echo html_entity_decode($s['userid']); ?>" ><?php echo html_entity_decode($s['company']); ?></option>
                                        <?php } ?>
                                  </select>
                                </div>

                            </div>
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



</div>
<?php init_tail(); ?>
<?php require 'modules/omni_sales/assets/js/customer_report/manage_import_mpesatransc_js.php';?>

</body>
</html>
