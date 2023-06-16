<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="panel_s">
          	<div class="panel-body">
          		<div class="row">
          			<div class="col-md-12">
          				<p class="text-dark text-uppercase bold"><?php echo _l('sumary');?></p>
          				<hr class="mtop15" />  
          			</div>
          			<?php 
          			$where_summary = '';
               
                    $where_agent = ' AND '.db_prefix().'clients.client_type = "agent"'; ?>


                    <?php $total_agent =  total_rows(db_prefix().'clients',($where_summary != '' ? substr($where_summary . $where_agent,5) : substr($where_agent,5) )); ?>
          			<div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-default mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-user-o"></i> <?php echo _l('agent_summary_total'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_agent); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">
			                  <div class="progress-bar progress-bar-default no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_agent); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_agent); ?>" data-percent="100%">
			                  </div>
			               </div>
			            </div>
			        </div>

			        <?php $active_agent = total_rows(db_prefix().'clients','active=1'.$where_summary. $where_agent);  ?>
			        <div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-success mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-user-o"></i> <?php echo _l('active_agent'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($active_agent); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">

			               	<?php 
			                  $percentage = 0;
			                  if($total_agent > 0){
			                    $percentage = $active_agent/$total_agent*100;
			                  }
			                  ?>

			                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($active_agent); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_agent); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="<?php echo html_entity_decode($percentage); ?>%">
			                  </div>
			               </div>
			            </div>
			        </div>

			        <?php $inactive_active_agent = total_rows(db_prefix().'clients','active=0'.$where_summary. $where_agent);  ?>
			        <div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-danger mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-user-o"></i> <?php echo _l('inactive_active_agent'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($inactive_active_agent); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">

			               	<?php 
			                  $percentage = 0;
			                  if($total_agent > 0){
			                    $percentage = $inactive_active_agent/$total_agent*100;
			                  }
			                  ?>

			                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($inactive_active_agent); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_agent); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="<?php echo html_entity_decode($percentage); ?>%">
			                  </div>
			               </div>
			            </div>
			        </div>


			        <?php $total_program =  total_rows(db_prefix().'sa_programs', '1=1'); ?>
          			<div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-infor mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-product-hunt"></i> <?php echo _l('total_programs'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_program); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">
			                  <div class="progress-bar progress-bar-infor no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_program); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_program); ?>" data-percent="100%">
			                  </div>
			               </div>
			            </div>
			        </div>

			        <?php $total_order =  total_rows(db_prefix().'sa_pur_orders', 'approve_status = 2'); ?>
          			<div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-warning mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo _l('total_orders'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($total_order); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">
			                  <div class="progress-bar progress-bar-warning no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($total_order); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_order); ?>" data-percent="100%">
			                  </div>
			               </div>
			            </div>
			        </div>

			        <?php $delivered_order =  total_rows(db_prefix().'sa_pur_orders', 'approve_status = 2 AND delivery_status = 1'); ?>
          			<div class="quick-stats-invoices col-xs-12 col-md-2 col-sm-6">
			           <div class="top_stats_wrapper minheight85">
			               <a class="text-success mbot15">
			               <p class="text-uppercase mtop5 minheight35"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo _l('delivered_orders'); ?>
			               </p>
			                  <span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($delivered_order); ?></span>
			               </a>
			               <div class="clearfix"></div>
			               <div class="progress no-margin progress-bar-mini">

			               		<?php 
			                  $percentage = 0;
			                  if($total_order > 0){
			                    $percentage = $delivered_order/$total_order*100;
			                  }
			                  ?>

			                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($delivered_order); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($total_order); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent="<?php echo html_entity_decode($percentage); ?>%">
			                  </div>
			               </div>
			            </div>
			        </div>


			        <div class="col-md-12">
			        	<p class="text-dark text-uppercase bold"><?php echo _l('order_value_by_agent');?></p>
          				<hr class="mtop15" />  

          				<table class="table table-orders-value">
				          <thead>
				            <th><?php echo _l('clients_list_company'); ?></th>
				            <th><?php echo _l('contact_primary'); ?></th>
				            <th><?php echo _l('email'); ?></th>
				            <th><?php echo _l('phone'); ?></th>
				            <th><?php echo _l('total_orders'); ?></th>
				            <th><?php echo _l('total_value'); ?></th>
				            <th><?php echo _l('paid_amount'); ?></th>
				          </thead>
				          <tbody>
				            
				          </tbody>
				        </table>
			        </div>



          		</div>
          	</div>
        </div>
	</div>
</div>

<?php init_tail(); ?>

<?php require('modules/sales_agent/assets/js/dashboard/dashboard_js.php'); ?>