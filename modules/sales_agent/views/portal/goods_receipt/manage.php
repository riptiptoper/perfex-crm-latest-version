<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); 
    ?>
    
<div class="col-md-12 mtop15">
	<div class="panel_s">
	 	<div class="panel-body">

	 		<div class="row">
	            <div class="col-md-12">
		            <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
		            <hr />
	            </div>
            </div>
            <div class="row">    
                <div class="_buttons col-md-3">
                  
                    <a href="<?php echo site_url('sales_agent/portal/goods_receipt'); ?>"class="btn btn-info pull-left mright10 display-block">
                        <?php echo _l('stock_received_docket'); ?>
                    </a>

                </div>
            </div>

            <br/>
            <div class="row">
                <div  class="col-md-3 pull-right">
                    <?php 
                     $input_attr_e = [];
                     $input_attr_e['placeholder'] = _l('day_vouchers');

                 echo sa_render_date_input('date_add','day_vouchers','',$input_attr_e ); ?>
                </div> 

            </div>
            <br/>

            <?php render_datatable(array(
                _l('id'),
                _l('stock_received_docket_code'),
                _l('reference_purchase_order'),
                _l('day_vouchers'),
                _l('total_tax_money'),
                _l('total_goods_money'),
                _l('value_of_inventory'),
                _l('total_money'),
                ),'table_manage_goods_receipt',['purchase_sm' => 'purchase_sm']); ?>

	 	</div>
	 </div>
</div>

<?php require 'modules/sales_agent/assets/js/portal/goods_receipt/manage_js.php';?>