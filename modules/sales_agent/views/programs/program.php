<?php init_head();?>
<div id="wrapper" class="commission">
  <div class="content">
    	<div class="row">
      		<div class="panel_s">
      			<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'commission-policy-form','autocomplete'=>'off')); ?>
        		<div class="panel-body">
        			<h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          			<hr />
          			<div class="row">
          				<div class="col-md-12">
          					<label for="name"><span class="text-danger">* </span><?php echo _l('name'); ?></label>
          					<?php $value = (isset($agent_program) ? $agent_program->name : ''); ?>
              				<?php echo render_input('name','',$value,'text', ['required' => true]); ?>
          				</div>
          				<div class="col-md-3">
          					<?php $value = (isset($agent_program) ? $agent_program->from_date : ''); ?>
                  			<?php echo render_date_input('from_date','from_date',$value); ?>
          				</div>
          				<div class="col-md-3">
          					<?php $value = (isset($agent_program) ? $agent_program->to_date : ''); ?>
                  			<?php echo render_date_input('to_date','to_date',$value); ?>
          				</div>

          				<div class="col-md-3 mtop20">
          					<div class="form-group ">
				                <div class="checkbox checkbox-primary ">
				                  <input type="checkbox" name="indefinite" id="indefinite" value="enable" <?php if(isset($agent_program) && $agent_program->indefinite == '1'){ echo 'checked';}?>>
				                  <label for="indefinite"><?php echo _l('indefinite'); ?></label>

				                </div>
				            </div>
          				</div>

          				<div class="form-group col-md-3">
			              <?php $selected = (isset($agent_program) ? $agent_program->discount_type : ''); ?>
			              <label for="discount_type"><?php echo _l('discount_type'); ?></label><br />
			              <div class="radio radio-inline radio-primary">
			                <input type="radio" name="discount_type" id="discount_type_percentage" value="percentage" <?php if($selected == 'percentage' || $selected == ''){echo 'checked';} ?>>
			                <label for="discount_type_percentage"><?php echo _l("percentage"); ?></label>
			              </div>
			              <div class="radio radio-inline radio-primary">
			                <input type="radio" name="discount_type" id="discount_type_fixed" value="fixed" <?php if($selected == 'fixed'){echo 'checked';} ?>>
			                <label for="discount_type_fixed"><?php echo _l("fixed"); ?></label>
			              </div>
			            </div>
          				
          				<div class="col-md-12">
          					<?php $value = (isset($agent_program) ? $agent_program->descriptions : '');
          					echo render_textarea('descriptions','description',$value,array(),array(),'mtop15'); ?>
          				</div>

          				<div class="col-md-12">
          					<h5 class="bold"><?php echo _l('agents_are_applicable'); ?></h5>
          					<hr/>
          				</div>

          				<div class="col-md-6">
		                  	<?php
		                      $selected = (isset($agent_program) ? explode(',', $agent_program->agent_group) : ''); 
		                      echo render_select('agent_group[]',$agent_groups,array('id','name'),'agents_groups',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
		                </div>
		                <div class="col-md-6">
		                  	<?php
		                      $selected = (isset($agent_program) ? explode(',',$agent_program->agent) : '');
		                      echo render_select('agent[]',$agents,array('userid','company'),'agents',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
		                </div>

		                <div class="col-md-12">
          					<h5 class="bold"><?php echo _l('agents_can_view_program'); ?></h5>
          					<hr/>
          				</div>

          				<div class="col-md-6">
		                  	<?php
		                      $selected = (isset($agent_program) ? explode(',', $agent_program->agent_group_can_view) : ''); 
		                      echo render_select('agent_group_can_view[]',$agent_groups,array('id','name'),'agents_groups',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
		                </div>
		                <div class="col-md-6">
		                  	<?php
		                      $selected = (isset($agent_program) ? explode(',',$agent_program->agent_can_view) : '');
		                      echo render_select('agent_can_view[]',$agents,array('userid','company'),'agents',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
		                </div>


		                <div class="col-md-12">
          					<h5 class="bold"><?php echo _l('program_detail'); ?></h5>
          					<hr/>
          				</div>

			            <div class="" id = "discount_calculated_as_ladder">
				            <div class="col-md-12">
				              <div class="row discount_list_ladder_setting">
				                <?php if(!isset($agent_program)) { ?>
				                <div id="discount_item_ladder_setting">
				                  <div class="row">
				                    <div class="col-md-11">
				                      <div class="col-md-3">
				                      	<?php
					                      echo render_select('product_group[0][]',$commodity_groups,array('id','name'),'group_item','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>
				                      <div class="col-md-3">
				                      	<?php
					                      echo render_select('product[0][]',$items,array('id','description'),'items','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>	
				                      <div class="col-md-2">
				                        <?php echo render_input('from_amount[0]', '<small class="text-danger">* </small>'._l('from_amount'),'','number', ['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2">
				                        <?php echo render_input('to_amount[0]','<small class="text-danger">* </small>'._l('to_amount'),'','number', ['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2">
				                        <?php echo render_input('discount[0]','discount','','number', array('min' => 0)); ?>
				                      </div>
				                    </div>
				                    <div class="col-md-1 no-padding">
				                    <span class="pull-bot">
				                        <button name="add" class="btn new_discount_item_ladder btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
				                        </span>
				                    </div>
				                  </div>
				                </div>
				                <?php }else{ 
				                  $setting = $program_detail;
				                  if(count($setting) > 0){

				                  ?>
				                  <?php foreach ($setting as $key => $value) { ?>
				                  <div id="discount_item_ladder_setting">
				                    <div class="row">
				                      <div class="col-md-11">

				                      <div class="col-md-3">
				                      	<?php $selected = explode(',', $value['product_group']);
					                      echo render_select('product_group['.$key.'][]',$commodity_groups,array('id','name'),'group_item',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>
				                      <div class="col-md-3">
				                      	<?php $selected = explode(',', $value['product']);
					                      echo render_select('product['.$key.'][]',$items,array('id','description'),'items',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>	

				                       <div class="col-md-2">
				                        <?php echo render_input('from_amount['.$key.']', '<small class="text-danger">* </small>'._l('from_amount'),$value['from_amount'],'number',['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2">
				                        <?php echo render_input('to_amount['.$key.']','<small class="text-danger">* </small>'._l('to_amount'),$value['to_amount'],'number',['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2" id="is_staff_0">
				                        <?php echo render_input('discount['.$key.']','discount',$value['discount'],'number',[]); ?>
				                      </div>
				                      </div>
				                      <div class="col-md-1">
				                      <span class="pull-bot">
				                          <?php if($key != 0){ ?>
				                            <button name="add" class="btn remove_item_ladder btn-danger mtop25" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
				                          <?php }else{ ?>
				                            <button name="add" class="btn new_discount_item_ladder btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
				                          <?php } ?>
				                            </span>
				                      </div>
				                    </div>
				                  </div>
				                  <?php }
				              		}else{ ?>	
				              			<div id="discount_item_ladder_setting">
				                  <div class="row">
				                    <div class="col-md-11">
				                      <div class="col-md-3">
				                      	<?php
					                      echo render_select('product_group[0][]',$commodity_groups,array('id','name'),'group_item','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>
				                      <div class="col-md-3">
				                      	<?php
					                      echo render_select('product[0][]',$items,array('id','description'),'items','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
				                      </div>	
				                      <div class="col-md-2">
				                        <?php echo render_input('from_amount[0]', '<small class="text-danger">* </small>'._l('from_amount'),'','number', ['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2">
				                        <?php echo render_input('to_amount[0]','<small class="text-danger">* </small>'._l('to_amount'),'','number', ['min' => 1, 'step'=> 'any', 'required' => true]); ?>
				                      </div>
				                      <div class="col-md-2">
				                        <?php echo render_input('discount[0]','discount','','number', array('min' => 0)); ?>
				                      </div>
				                    </div>
				                    <div class="col-md-1 no-padding">
				                    <span class="pull-bot">
				                        <button name="add" class="btn new_discount_item_ladder btn-success mtop25" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
				                        </span>
				                    </div>
				                  </div>
				                </div>

				                <?php } } ?>
				              </div>
				            </div>
				        </div>

          			</div>

          			<div class="row">
			           
				            <div class="modal-footer">
				                <button type="submit" class="btn btn-info commission-policy-form-submiter"><?php echo _l('submit'); ?></button>
				            </div>
			            
		          	</div>

        		</div>
        		<?php echo form_close(); ?>

    		</div>
    	</div>
	</div>
</div>

<?php init_tail(); ?>
</body>
</html>

<?php require 'modules/sales_agent/assets/js/programs/program_js.php';?>