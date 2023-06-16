<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop40">
   <div class="col-md-4 col-md-offset-4 text-center">
      <h1 class="text-uppercase mbot20 login-heading">
         <?php
            echo _l(get_option('allow_registration') == 1 ? 'clients_login_heading_register': 'clients_login_heading_no_register');
         ?>
      </h1>
   </div>
   <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
      <?php echo form_open(site_url('sales_agent/authentication_sales_agent/login'),array('class'=>'login-form')); ?>
      <?php hooks()->do_action('clients_login_form_start'); ?>
      <div class="panel_s">
         <div class="panel-body">
            <div class="form-group">
               <label for="email"><?php echo _l('clients_login_email'); ?></label>
               <input type="text" autofocus="true" class="form-control" name="email" id="email">
               <?php echo form_error('email'); ?>
            </div>
            <div class="form-group">
               <label for="password"><?php echo _l('clients_login_password'); ?></label>
               <input type="password" class="form-control" name="password" id="password">
               <?php echo form_error('password'); ?>
            </div>

  

            <div class="form-group">
               <div class="row">
                  
               <div class="col-md-6">
              <a href="<?php echo site_url('sales_agent/authentication_sales_agent/register'); ?>" class="btn btn-success btn-block"><?php echo _l('clients_register_string'); ?></a>
                  
               </div>
               <div class="col-md-6">
               <button type="submit" class="btn btn-info btn-block"><?php echo _l('clients_login_login_string'); ?></button>
               </div>
               </div>
            </div>

            
            <?php hooks()->do_action('clients_login_form_end'); ?>
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>
