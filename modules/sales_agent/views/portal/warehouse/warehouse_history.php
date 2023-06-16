<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); 
    ?>

<div class="col-md-12 mtop15" id="small-table">
    <div class="panel_s">
       <div class="panel-body">
          <div class="row">
             <div class="col-md-12">
              <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
              <hr>
              <br>

            </div>
          </div>
          <div class="row">
             
            <div class=" col-md-4">
                <div class="form-group">
                  <select name="warehouse_filter[]" id="warehouse_filter" class="selectpicker" multiple="true" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('filters_by_warehouse'); ?>">

                      <?php foreach($warehouse_filter as $warehouse) { ?>
                        <option value="<?php echo html_entity_decode($warehouse['warehouse_id']); ?>"><?php echo html_entity_decode($warehouse['warehouse_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class=" col-md-4">
            <?php echo sa_render_select('commodity_filter[]', $items, array('id', array('commodity_code', 'description')), '', '', ['multiple' => true, 'data-actions-box' => true,'data-none-selected-text' => _l('filters_by_commodity')], [], '', '', false ); ?>
        </div>
        
        <div class=" col-md-4">
            <div class="form-group">
              <select name="status[]" id="status" class="selectpicker" data-live-search="true" multiple="true" data-width="100%" data-none-selected-text="<?php echo _l('filters_by_status'); ?>">

                <option value="1"><?php echo _l('stock_import'); ?></option>
                <option value="2"><?php echo _l('stock_export'); ?></option>
            </select>
        </div>
    </div>
    <div  class="col-md-2 leads-filter-column">
   


        <?php 
                     $input_attr_e = [];
                     $input_attr_e['placeholder'] = _l('from_date');

                 echo sa_render_date_input('validity_start_date','from_date','',$input_attr_e ); ?>
    </div>
    <div  class="col-md-2 leads-filter-column">
       

        <?php 
                     $input_attr_e = [];
                     $input_attr_e['placeholder'] = _l('to_date');

                 echo sa_render_date_input('validity_end_date','to_date','',$input_attr_e ); ?>
    </div> 


    
</div>
            <br><br>

              <?php render_datatable(array(
                _l('id'),
                _l('form_code'),
                _l('commodity_code'),
                _l('warehouse_code'),
                _l('warehouse_name'),
                _l('day_vouchers'),
                _l('opening_stock'),
                _l('closing_stock'),
                _l('lot_number').'/'._l('quantity_sold'),
                _l('expiry_date'),
                _l('wh_serial_number'),
                _l('note'),
                _l('status_label'),
                ),'table_warehouse_history'); ?>
       </div>
    </div>
 </div>

 <?php require 'modules/sales_agent/assets/js/portal/warehouse/warehouse_history_js.php';?>