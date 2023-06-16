
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                  <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('sales_report_heading'); ?></h4>
                      <hr />  
                      <a href="#" class="font-medium" onclick="init_report(this,'trade_discount_application_history'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('trade_discount_application_history'); ?></a>
                      </p>
                      <hr class="hr-10" />                  
                  </div>
                  <div class="col-md-4 border-right">
                    <h4 class="no-margin font-medium"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('charts_based_report'); ?></h4>
                    <hr />
                          <a href="#" class="font-medium" onclick="init_report(this,'sales_statistics_by_week'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_statistics_by_week'); ?></a>
                      </p>
                      <hr class="hr-10" />
                          <a href="#" class="font-medium" onclick="init_report(this,'sales_statistics_by_month'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_statistics_by_month'); ?></a>
                      </p>
                      <hr class="hr-10" />
                          <a href="#" class="font-medium" onclick="init_report(this,'sales_statistics_by_year'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_statistics_by_year'); ?></a>
                      </p>
                      <hr class="hr-10" />
                          <a href="#" class="font-medium" onclick="init_report(this,'sales_statistics_by_stage'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('sales_statistics_by_stage'); ?></a>
                      </p>
                      <hr class="hr-10" />

                 </div>
                 <div class="col-md-4">
                      <div class="bg-light-gray border-radius-4">
                        <div class="p8">
                             <?php if(isset($currencies)){ ?>
                  <div id="currency" class="form-group hide">
                     <label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('report_sales_base_currency_select_explanation'); ?>"></i> <?php echo _l('currency'); ?></label><br />
                     <select class="selectpicker" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
                           $selected = '';
                           if($currency['isdefault'] == 1){
                              $selected = 'selected';
                           }
                           ?>
                           <option value="<?php echo html_entity_decode($currency['id']); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($currency['name']); ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <?php } ?>
                     <div id="income-years" class="hide mbot15">
                        <label for="payments_years"><?php echo _l('year'); ?></label><br />
                        <select class="selectpicker" name="payments_years" data-width="100%" onchange="total_income_bar_report();" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php foreach($payments_years as $year) { ?>
                           <option value="<?php echo html_entity_decode($year['year']); ?>"<?php if($year['year'] == date('Y')){echo 'selected';} ?>>
                              <?php echo html_entity_decode($year['year']); ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
                     <div class="form-group hide" id="report-time">
                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value="this_month"><?php echo _l('this_month'); ?></option>
                           <option value="1"><?php echo _l('last_month'); ?></option>
                           <option value="this_year"><?php echo _l('this_year'); ?></option>
                           <option value="last_year"><?php echo _l('last_year'); ?></option>
                           <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                           <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                           <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                           <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                        </select>                       
                     </div>


                        <div class="by_month time_filter hide">
                          <label for="year-report"><?php echo _l('year'); ?></label><br />
                          <select class="selectpicker" name="by_month" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php $year = date('Y'); ?>
                            <?php for($i = $year-3; $i <= $year; $i++){ ?>
                             <option value="<?php echo html_entity_decode($i); ?>" <?php if($i == $year){ echo 'selected';} ?>><?php echo html_entity_decode($i); ?></option>
                            <?php } ?>
                          </select>
                        </div>


                         <div class="by_year time_filter hide">
                          <label for="year-report"><?php echo _l('year'); ?></label><br />
                          <select class="selectpicker" name="by_year" multiple data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php $year = date('Y'); ?>
                            <?php for($i = $year-3; $i <= $year; $i++){ 
                              $selected = '';
                              ?>

                             <option value="<?php echo html_entity_decode($i); ?>" <?php if($i == $year){ echo 'selected';} ?> >
                              <?php echo html_entity_decode($i); ?></option>
                            <?php } ?>
                          </select>
                        </div>

                        <div class="by_stage time_filter hide">
                           <div class="row">
                           <div class="col-md-6">
                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                        </div>

                     <div id="date-range" class="hide mbot15">
                        <div class="row">
                           <div class="col-md-6">
                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>






                        </div>
                      </div>
                  </div>
               </div>
               <div id="report">


                <div class="row filter_2 hide">
                   <div class="col-md-4">
                          <label for="channel"><?php echo _l('channel'); ?></label><br />
                          <select class="selectpicker" name="channel[]" data-width="100%" multiple data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <option value="1">POS</option>
                              <option value="2">Portal</option>
                              <option value="3">WooCommerce</option>
                          </select>

                   </div>
                   <div class="col-md-4">
                          <label for="group"><?php echo _l('group'); ?></label><br />
                          <select class="selectpicker" name="group[]" data-width="100%" multiple data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php 
                              foreach ($list_group as $key => $value) { ?>
                                <option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['name']); ?></option>
                             <?php }
                             ?>
                          </select>

                   </div>
                   <div class="col-md-4">
                          <label for="by_product"><?php echo _l('products'); ?></label><br />
                          <select class="selectpicker" name="product[]" data-width="100%" multiple data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                             <?php 
                              foreach ($product as $key => $value) { ?>
                                <option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['description']); ?></option>
                             <?php }
                             ?>
                          </select>
                   </div>
                 </div>

               

                 <hr class="hr-panel-heading" />                 
                    <?php $this->load->view('report/include/total_amount_sales'); ?>              
                    <?php $this->load->view('includes/results_trade_discount'); ?>              
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/omni_sales/assets/js/report/sales_js.php';?>
</body>
</html>

