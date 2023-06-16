<div class="col-md-12 mtop15">
	<div class="panel_s">
		<div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
        </div>
        <?php if(total_rows(db_prefix().'sa_join_program_request', ['program_id' => $program->id, 'agent_id' => get_sale_agent_user_id(), 'status' => 'new']) == 0){ ?>
          <?php if(check_agent_not_in_program($program->id)){ ?>
          <div class="col-md-6">
            <a href="<?php echo site_url('sales_agent/portal/join_program/'.$program->id) ?>" class="btn btn-success pull-right" ><i class="fa fa-sign-in"></i><?php echo ' '._l('joint_the_program'); ?></a>
          </div>
          <?php }else{ ?>

            <span class="label label-success pull-right"><?php echo _l('joined'); ?></span>
          <?php } ?>
        <?php }else{  ?>
          <div class="col-md-6">
              <span class="label label-warning pull-right"><?php echo _l('requested'); ?></span>
          </div>
        <?php } ?>
      </div>
  			<hr />

  			<div class="row">
  				<div class="col-md-6">
  					<p class="bold"><?php echo _l('general_information'); ?></p>
  					<div class="col-md-6 pad_left_0 border-right">
					<p><?php echo _l('from_date').':'; ?><span class="pull-right bold"><?php echo _d($program->from_date); ?></span></p>
 				</div>
 				<div class="col-md-6 pad_right_0">
 					<p><?php echo _l('to_date').':'; ?><span class="pull-right bold">
 					<?php if($program->indefinite == 1){
 						echo _l('indefinite'); 
 					}else{
 						echo _d($program->to_date);
 					}

 					?></span></p>
 				</div>
 				<div class="col-md-12 pad_left_0 pad_right_0">
 					<hr class="mtop5 mbot5">
 				</div>

 				<div class="col-md-6  pad_right_0 border-right">
 					<p><?php echo _l('created_at').':'; ?><span class="pull-right bold"><?php echo _d($program->created_at); ?></span></p>
 				</div>
 				<div class="col-md-6 pad_left_0  ">
 					<p><?php echo _l('created_by').':'; ?><span class="pull-right bold"><?php echo get_staff_full_name(); ?></p>
 					
 				</div>
 				<div class="col-md-12 pad_left_0 pad_right_0">
 					<hr class="mtop5 mbot5">
 				</div>
 				
 				<div class="col-md-12 pad_right_0">
 					<p><span class="bold"><?php echo _l('description').': '; ?></span><span class=""><?php echo html_entity_decode($program->descriptions); ?></span></p>
 				</div>

  				</div>
  				<div class="col-md-6">
  					<p class="bold"><?php echo _l('discount_information'); ?></p>
  					<div class="col-md-12">
  						<div class="table-responsvive">
  							<table class="tbmtop5 table table-striped ">
  								<tr>
  									<td><?php echo _l('from_amount');?></td>
  									<td><?php echo _l('to_amount');?></td>
  									<td><?php 
  										if($program->discount_type == 'percentage'){
  											$type = '%';
  										}else{
  											$type = 'amount';
  										}
  										echo _l('discount').'('.$type.')';
  									?></td>
  								</tr>

  								<?php foreach($program_detail as $detail){  ?>
  									<tr>
    									<td><?php echo html_entity_decode($detail['from_amount']);?></td>
    									<td><?php echo html_entity_decode($detail['to_amount']);?></td>
    									<td><?php echo html_entity_decode($detail['discount']);?></td>
  								</tr>
  								<?php } ?>
  							</table>
  						</div>
  					</div>
  				</div>

  			</div>

        <div class="row">

          <div class="col-md-12">
            <h5 class="bold"><?php echo _l('products_are_applicable'); ?></h5>
              <?php
              $table_data = [];

              $table_data = array_merge($table_data, array( 
                _l('sa_image'),
                _l('invoice_items_list_description'),
                _l('invoice_item_long_description'),
                _l('invoice_items_list_rate'),
                _l('tax_1'),
                _l('tax_2'),
                _l('unit'),
                _l('item_group_name')));


              render_datatable($table_data,'program-items'); 
               ?>
          </div>
        </div>

		</div>
	</div>
</div>

<?php require 'modules/sales_agent/assets/js/portal/programs/program_detail_js.php';?>