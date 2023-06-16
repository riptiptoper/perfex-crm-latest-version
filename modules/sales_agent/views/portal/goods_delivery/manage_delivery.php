<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 mtop15">
	<div class="panel_s">
		<div class="panel-body">
	
			<div class="row">
             <div class="col-md-12 ">
              <h4 class="no-margin font-bold"><i class="fa fa-shopping-basket" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
              <hr />
             </div>
          	</div>
          	<div class="row">    
                <div class="_buttons col-md-3">
                    <a href="<?php echo site_url('sales_agent/portal/goods_delivery'); ?>"class="btn btn-info pull-left mright10 display-block">
                        <?php echo _l('export_ouput_splip'); ?>
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
            _l('goods_delivery_code'),
            _l('customer_name'),
            _l('day_vouchers'),
            _l('invoices'),
            _l('to'),
            _l('address'),
            _l('staff'),
            _l('delivery_status'),
            ),'table_manage_delivery',['delivery_sm' => 'delivery_sm']); ?>
			
		</div>
	</div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/goods_delivery/manage_delivery_js.php';?>