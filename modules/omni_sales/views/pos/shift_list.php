<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
      <div class="clearfix"></div><br>
      <div class="row">
        <div class="col-md-12">
         <h4><i class="fa fa-list-ul">&nbsp;&nbsp;</i><?php echo html_entity_decode($title); ?></h4>
         <hr>
       </div>
     </div>
     <div class="row"> 
      <div class="col-md-3"> 
        <button class="btn btn-primary" onclick="create_shift()">
          <?php echo _l('create_shift'); ?>                
        </button>
      </div>
    </div>
    <br>
    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-3">
        <?php echo render_date_input('start_date','start_date',''); ?>
      </div>
      <div class="col-md-3">
        <?php echo render_date_input('end_date','end_date',''); ?>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="seller"><?php echo _l('seller'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="seller[]" data-none-selected-text="<?php echo _l('no_seller'); ?>" multiple data-live-search="true">
            <?php foreach ($staff as $key => $value) { ?>
              <option value="<?php echo html_entity_decode($value['staffid']); ?>"><?php echo html_entity_decode($value['lastname'].' '.$value['firstname']); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <label class="control-label" for="status"><?php echo _l('status'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="status" data-none-selected-text="<?php echo _l('no_status'); ?>" data-live-search="true">
            <option value=""></option>
            <option value="1"><?php echo _l('open');?></option>
            <option value="2"><?php echo _l('closed');?></option>
          </select>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <table class="table table-shift_list scroll-responsive">
      <thead>
        <th>ID#</th>
        <th><?php echo _l('staff'); ?></th>
        <th><?php echo _l('date'); ?></th>
        <th><?php echo _l('granted_amount'); ?></th>
        <th><?php echo _l('incurred_amount'); ?></th>
        <th><?php echo _l('closing_amount'); ?></th>
        <th><?php echo _l('order_value'); ?></th>
        <th><?php echo _l('status'); ?></th>
        <th><?php echo _l('options'); ?></th>
      </thead>
      <tbody></tbody>
      <tfoot>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>      
       <td></td>      
     </tfoot>
   </table>

 </div>
</div>
</div>
</div>



<div class="modal fade" id="add_edit_shift" tabindex="-1" role="dialog">
  <div class="modal-dialog">
   <div class="modal-content">


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">
        <span class="add-title"><?php echo _l('new_shift'); ?></span>
        <span class="update-title hide"><?php echo _l('edit_shift'); ?></span>
      </h4>
    </div>
    <?php echo form_open(admin_url('omni_sales/add_shift'),array('id'=>'form_add_shift')); ?>	            
    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="staff_id" value="<?php echo get_staff_user_id(); ?>">
        <input type="hidden" name="shift_code" value="<?php echo date('Y-m-d H:i:s'); ?>">
        <div class="col-md-12">


          <div class="form-group">
            <label for="gst"><?php echo _l('granted_amount'); ?></label>            
            <div class="input-group">
              <input type="text" class="form-control" data-type="currency" name="granted_amount" value="">
              <span class="input-group-addon"><?php echo html_entity_decode($currency_name); ?></span>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>	 


  </div>
</div>
</div>


<?php init_tail(); ?>
</body>
</html>
