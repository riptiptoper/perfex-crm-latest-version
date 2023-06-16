<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 mtop15">
	<?php $base_currency = get_base_currency(); ?>
	<div class="panel_s">
			<div class="panel-body">
				<div class="row">
          <div class="col-md-12">
      		  <p class="text-dark text-uppercase bold"><?php echo _l('sumary'); ?></p>
            <hr class="mtop15">
          </div>

          <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
             <div class="top_stats_wrapper minheight85">
                 <a class="text-info mbot15">
                 <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-product-hunt"></i> <?php echo _l('sa_program_participated'); ?>
                 </p>
                    <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($program_participated); ?></span>
                 </a>
                 <div class="clearfix"></div>
                 <div class="progress no-margin progress-bar-mini">
                  <?php 
                        $percentage = 0;
                        if($program_participated > 0){
                          $percentage = $program_participated/$program_participated*100;
                        }
                        ?>

                    <div class="progress-bar progress-bar-info no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($program_participated); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($program_participated); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="100%">
                    </div>
                 </div>
              </div>
          </div>

          <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
             <div class="top_stats_wrapper minheight85">
                 <a class="text-warning mbot15">
                 <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('total_items'); ?>
                 </p>
                    <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_items); ?></span>
                 </a>
                 <div class="clearfix"></div>
                 <div class="progress no-margin progress-bar-mini">
                  <?php 
                        $percentage = 0;
                        if($total_items > 0){
                          $percentage = $total_items/$total_items*100;
                        }
                        ?>

                    <div class="progress-bar progress-bar-warning no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_items); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_items); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="100%">
                    </div>
                 </div>
              </div>
          </div>

          <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
             <div class="top_stats_wrapper minheight85">
                 <a class="text-danger mbot15">
                 <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-cart-plus"></i> <?php echo _l('total_purchase_orders'); ?>
                 </p>
                    <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_purchase_orders); ?></span>
                 </a>
                 <div class="clearfix"></div>
                 <div class="progress no-margin progress-bar-mini">
                  <?php 
                        $percentage = 0;
                        if($total_purchase_orders > 0){
                          $percentage = $total_purchase_orders/$total_purchase_orders*100;
                        }
                        ?>

                    <div class="progress-bar progress-bar-danger no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_purchase_orders); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_purchase_orders); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="100%">
                    </div>
                 </div>
              </div>
          </div>

          <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
             <div class="top_stats_wrapper minheight85">
                 <a class="text-success mbot15">
                 <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-clipboard"></i> <?php echo _l('total_sale_invoices'); ?>
                 </p>
                    <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_sale_invoices); ?></span>
                 </a>
                 <div class="clearfix"></div>
                 <div class="progress no-margin progress-bar-mini">
                  <?php 
                        $percentage = 0;
                        if($total_sale_invoices > 0){
                          $percentage = $total_sale_invoices/$total_sale_invoices*100;
                        }
                        ?>

                    <div class="progress-bar progress-bar-success no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_sale_invoices); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_sale_invoices); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="100%">
                    </div>
                 </div>
              </div>
          </div>

            <div class="col-md-6">
              <p class="text-dark text-uppercase bold"><?php echo _l('purchase_orders_value'); ?></p>
              <hr class="mtop15">
              <div class="row">

                <div class="col-lg-6 col-xs-12 col-md-12 total-column">        
                  <div class="panel_s">
                    <div class="panel-body">
                      <h3 class="text-muted _total"><?php echo app_format_money($total_po_value, $base_currency); ?></h3>
                      <span class="text-warning"><?php echo _l('total'); ?></span>           
                    </div>        
                  </div>     
                </div>

                <div class="col-lg-6 col-xs-12 col-md-12 total-column">        
                  <div class="panel_s">
                    <div class="panel-body">
                      <h3 class="text-muted _total"><?php echo app_format_money($this_month_po_value, $base_currency); ?></h3>
                      <span class="text-success"><?php echo _l('this_month'); ?></span>           
                    </div>        
                  </div>     
                </div>

              </div>
            </div>


            <div class="col-md-6">
              <p class="text-dark text-uppercase bold"><?php echo _l('sale_invoices_value'); ?></p>
              <hr class="mtop15">

              <div class="row">

                <div class="col-lg-6 col-xs-12 col-md-12 total-column">        
                  <div class="panel_s">
                    <div class="panel-body">
                      <h3 class="text-muted _total"><?php echo app_format_money($total_invoice_value, $base_currency); ?></h3>
                      <span class="text-warning"><?php echo _l('total'); ?></span>           
                    </div>        
                  </div>     
                </div>

                <div class="col-lg-6 col-xs-12 col-md-12 total-column">        
                  <div class="panel_s">
                    <div class="panel-body">
                      <h3 class="text-muted _total"><?php echo app_format_money($this_month_invoice_value, $base_currency); ?></h3>
                      <span class="text-success"><?php echo _l('this_month'); ?></span>           
                    </div>        
                  </div>     
                </div>

              </div>
            </div>

            <div class="col-md-6">
              <div id="po_by_delivery_status" class="minwidth310">

              </div>
              <br>
            </div>

            <div class="col-md-6">
              <div id="invoice_by_status" class="minwidth310">

              </div>
              <br>
            </div>
      
        </div>
	    </div>
  </div>
</div>
<?php require('modules/sales_agent/assets/js/portal/dashboard/dashboard_js.php'); ?>