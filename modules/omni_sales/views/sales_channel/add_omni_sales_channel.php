<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="panel_s">
    <div class="panel-body">
   <div class="clearfix"></div><br>
    <?php echo form_open(admin_url('team_password/category_management'),array('id'=>'form_category_management')); ?>              

      <div class="horizontal-scrollable-tabs  mb-5">
        <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
        <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
        <div class="horizontal-tabs">
         <ul class="nav nav-tabs  nav-tabs-horizontal mb-5" role="tablist">          
            <li role="presentation" class="nav-item active">
               <a href="#general_infor" aria-controls="general_infor"  class="general_infor" role="tab" data-toggle="tab">
               <span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo _l('general_infor'); ?>
               </a>
            </li>
            <li role="presentation" class="nav-item">
               <a href="#product" aria-controls="product" role="tab" data-toggle="tab">
               <i class="fa fa-barcode fsize15"></i>&nbsp;<?php echo _l('product'); ?>
               </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="general_infor">
          <div class="row">

              <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      <input type="hidden" name="id">
                        <?php echo render_input('channel_name','channel_name'); ?>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group" app-field-wrapper="icon">
                      <label class="control-label"><?php echo _l('icon'); ?></label>
                      <div class="input-group">
                       <input type="text" name="icon" class="form-control main-item-icon icon-picker">
                       <span class="input-group-addon">
                         <i id="icon"></i>
                       </span>
                      </div>
                    </div>
                  </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                      <?php  echo render_textarea('description','description','',array(),array(),'','tinymce'); ?>
                    </div>
                  </div>
              </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>

          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="product">
          <div class="row">
            <div class="col-md-12">
              
            </div>
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