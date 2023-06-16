<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>
  <div class="col-md-12 mtop15">
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <h4><?php echo html_entity_decode($title); ?></h4>
        </div>

        <div class="col-md-12">
          <hr>
        </div>
      </div>
      <div class="row mbot15">
        <div class="col-md-3">
          <label for="group_item"><?php echo _l('group_item'); ?></label>
          <select name="product_group" id="group_item" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" >
            <option value=""></option>
              <?php foreach($commodity_groups as $s) { ?>
              <option value="<?php echo html_entity_decode($s['id']); ?>" ><?php echo html_entity_decode($s['name']); ?></option>
                <?php } ?>
          </select>  
        </div>

         <div class="col-md-3">
          <label for="group_item"><?php echo _l('sa_programs'); ?></label>
            <select name="program" id="group_item" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" >
            <option value=""></option>
              <?php foreach($programs as $program) { ?>
              <option value="<?php echo html_entity_decode($program['id']); ?>" ><?php echo html_entity_decode($program['name']); ?></option>
                <?php } ?>
          </select>  
         </div>
      </div>

      
      <table class="table table-products">
        <thead>
          <th><?php echo _l('sa_image'); ?></th>
          <th><?php echo _l('sa_commodity_code'); ?></th>
          <th><?php echo _l('sa_commodity_name'); ?></th>
          <th><?php echo _l('sa_inventory'); ?></th>
          <th><?php echo _l('sa_group'); ?></th>
          <th><?php echo _l('sa_unit_name'); ?></th>
          <th><?php echo _l('sa_rate'); ?></th>
          <th><?php echo _l('tax'); ?></th>
          <th><?php echo _l('discount_program'); ?></th>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require 'modules/sales_agent/assets/js/portal/products/manage_js.php';?>