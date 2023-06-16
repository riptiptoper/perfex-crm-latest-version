<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
   <div class="additional"></div>
   <div class="col-md-12">
      <div class="horizontal-scrollable-tabs">
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs profile-tabs row customer-profile-tabs nav-tabs-horizontal" role="tablist">
               <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
                  <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
                     <?php echo _l( 'customer_profile_details'); ?>
                  </a>
               </li>
               <?php
               $customer_custom_fields = false;
               if(total_rows(db_prefix().'customfields',array('fieldto'=>'customers','active'=>1)) > 0 ){
                $customer_custom_fields = true;
                ?>
             <?php } ?>
             <li role="presentation">
               <a href="#billing_and_shipping" aria-controls="billing_and_shipping" role="tab" data-toggle="tab">
                  <?php echo _l( 'billing_shipping'); ?>
               </a>
            </li>
            <?php hooks()->do_action('after_customer_billing_and_shipping_tab', isset($clients) ? $clients : false); ?>
         </ul>
      </div>
   </div>
   <div class="clearfix"></div>
   <br>
   <div class="clearfix"></div>
   <div class="tab-content">
      <?php hooks()->do_action('after_custom_profile_tab_content',isset($clients) ? $clients : false); ?>
      <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
         <div class="row">
            <div class="col-md-12 mtop15 <?php if(isset($clients) && (!is_empty_customer_company($clients->userid) && total_rows(db_prefix().'contacts',array('userid'=>$clients->userid,'is_primary'=>1)) > 0)) { echo ''; } else {echo ' hide';} ?>" id="client-show-primary-contact-wrapper">
               <div class="checkbox checkbox-info mbot20 no-mtop">
                  <input type="checkbox" name="show_primary_contact"<?php if(isset($clients) && $clients->show_primary_contact == 1){echo ' checked';}?> value="1" id="show_primary_contact">
                  <label for="show_primary_contact"><?php echo _l('show_primary_contact',_l('invoices').', '._l('estimates').', '._l('payments').', '._l('credit_notes')); ?></label>
               </div>cu
            </div>
            <div class="col-md-6">
               <?php $value=( isset($clients) ? $clients->company : ''); ?>
               <?php echo render_input( 'company', 'name',$value,'text', array('autofocus'=>true)); ?>
               <div id="company_exists_info" class="hide"></div>
               <?php if(get_option('company_requires_vat_number_field') == 1){
                  $value=( isset($clients) ? $clients->vat : '');
                  echo render_input( 'vat', 'client_vat_number',$value);
               } ?>
               <?php $value=( isset($clients) ? $clients->phonenumber : ''); ?>
               <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
               <?php echo render_input( 'email', 'email',$value,'email',array('onblur'=>'check_exist_email(this)')); ?>
               <center class="alert_email hide"><label class="text-danger"><?php echo _l('email_is_exit'); ?></label></center>
               <?php if((isset($clients) && empty($clients->website)) || !isset($clients)){
                  $value=( isset($clients) ? $clients->website : '');
                  echo render_input( 'website', 'client_website',$value);
               } else { ?>
                  <div class="form-group">
                     <label for="website"><?php echo _l('client_website'); ?></label>
                     <div class="input-group">
                        <input type="text" name="website" id="website" value="<?php echo html_entity_decode($clients->website); ?>" class="form-control">
                        <div class="input-group-addon">
                           <span><a href="<?php echo maybe_add_http($clients->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                        </div>
                     </div>
                  </div>
               <?php } ?>
               <label for="groups" class="control-label"><?php echo _l('groups'); ?></label>
               <select name="groups_in[]" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('none'); ?>" data-live-search="true"> 
                <option></option>
                <?php 
                foreach ($groups as $key => $value) { ?>
                   <option value="<?php echo html_entity_decode($value['id']) ?>"><?php echo html_entity_decode($value['name']) ?></option>                    
                <?php } ?>  
             </select>
             <div class="clearfix"></div>
             <br>
             <div class="clearfix"></div>

             <?php if(!isset($clients)){ ?>
               
             <?php }
             $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
             $selected = '';
             if(isset($clients) && client_have_transactions($clients->userid)){
               $s_attrs['disabled'] = true;
            }
            foreach($currencies as $currency){
               if(isset($clients)){
                if($currency['id'] == $clients->default_currency){
                 $selected = $currency['id'];
              }
           }
        } ?>

        <label for="default_currency" class="control-label"><?php echo _l('invoice_add_edit_currency'); ?></label>
        <select name="default_currency" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-live-search="true"> 
          <option></option>
          <?php 
          foreach ($currencies as $key => $value) { ?>
            <option value="<?php echo html_entity_decode($value['id']) ?>"><?php echo html_entity_decode($value['name']) .' '. html_entity_decode($value['symbol']); ?></option>                    

         <?php } ?>  
      </select>
      <div class="clearfix"></div>
   </div>
   <div class="col-md-6">
      <?php $value=( isset($clients) ? $clients->address : ''); ?>
      <?php echo render_textarea( 'address', 'client_address',$value); ?>
      <?php $value=( isset($clients) ? $clients->city : ''); ?>
      <?php echo render_input( 'city', 'client_city',$value); ?>
      <?php $value=( isset($clients) ? $clients->state : ''); ?>
      <?php echo render_input( 'state', 'client_state',$value); ?>
      <?php $value=( isset($clients) ? $clients->zip : ''); ?>
      <?php echo render_input( 'zip', 'client_postal_code',$value); ?>
      <?php $countries= get_all_countries();
      $customer_default_country = get_option('customer_default_country');
      $selected =( isset($clients) ? $clients->country : $customer_default_country);
      ?>
      <label for="country" class="control-label"><?php echo _l('clients_country'); ?></label>
      <select name="country" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
       <option></option>
       <?php 
       foreach ($countries as $keys => $values) { ?>
         <option value="<?php echo html_entity_decode($values['country_id']) ?>"><?php echo html_entity_decode($values['short_name']); ?></option>                    
         
      <?php } ?>  
   </select> 
   <div class="clearfix"></div>
   <br>  
</div>
<div class="col-md-6">
   <?php if(get_option('disable_language') == 0){ ?>
      <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
   </label>
   <select name="default_language" id="default_language" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-live-search="true"> 
    <option></option>
    <?php foreach($this->app->get_available_languages() as $availableLanguage){
      $selected = '';
      if(isset($clients)){
         if($clients->default_language == $availableLanguage){
            $selected = 'selected';
         }
      }
      ?>
      <option value="<?php echo html_entity_decode($availableLanguage); ?>" <?php echo html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
   <?php } ?> 
</select>
<?php } ?>
</div>
</div>
</div>
<?php if(isset($clients)){ ?>
   <div role="tabpanel" class="tab-pane" id="customer_admins">
      <?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
         <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
      <?php } ?>
      <table class="table dt-table">
         <thead>
            <tr>
               <th><?php echo _l('staff_member'); ?></th>
               <th><?php echo _l('customer_admin_date_assigned'); ?></th>
               <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                  <th><?php echo _l('options'); ?></th>
               <?php } ?>
            </tr>
         </thead>
         <tbody>
            <?php foreach($customer_admins as $c_admin){ ?>
               <tr>
                  <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                     <?php echo staff_profile_image($c_admin['staff_id'], array(
                        'staff-profile-image-small',
                        'mright5'
                     ));
                     echo get_staff_full_name($c_admin['staff_id']); ?></a>
                  </td>
                  <td data-order="<?php echo html_entity_decode($c_admin['date_assigned']); ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                  <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('clients/delete_customer_admin/'.$clients->userid.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                  <?php } ?>
               </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
<?php } ?>
<div role="tabpanel" class="tab-pane" id="billing_and_shipping">
   <div class="row">
      <div class="col-md-12">
         <div class="row">
            <div class="col-md-6">
               <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
               <hr />
               <?php $value=( isset($clients) ? $clients->billing_street : ''); ?>
               <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
               <?php $value=( isset($clients) ? $clients->billing_city : ''); ?>
               <?php echo render_input( 'billing_city', 'billing_city',$value); ?>
               <?php $value=( isset($clients) ? $clients->billing_state : ''); ?>
               <?php echo render_input( 'billing_state', 'billing_state',$value); ?>
               <?php $value=( isset($clients) ? $clients->billing_zip : ''); ?>
               <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
               <?php $selected=( isset($clients) ? $clients->billing_country : '' ); ?>
               <label for="billing_country" class="control-label"><?php echo _l('billing_country'); ?></label>
               <select name="billing_country" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
                <option></option>
                <?php 
                foreach ($countries as $key => $value) { ?>
                   <option value="<?php echo html_entity_decode($value['country_id']) ?>"><?php echo html_entity_decode($value['short_name']) ?></option>                    
                <?php } ?>  
             </select>
          </div>
          <div class="col-md-6">
            <h4 class="no-mtop">
               <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
               <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
            </h4>
            <hr />
            <?php $value=( isset($clients) ? $clients->shipping_street : ''); ?>
            <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
            <?php $value=( isset($clients) ? $clients->shipping_city : ''); ?>
            <?php echo render_input( 'shipping_city', 'shipping_city',$value); ?>
            <?php $value=( isset($clients) ? $clients->shipping_state : ''); ?>
            <?php echo render_input( 'shipping_state', 'shipping_state',$value); ?>
            <?php $value=( isset($clients) ? $clients->shipping_zip : ''); ?>
            <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
            <?php $selected=( isset($clients) ? $clients->shipping_country : '' ); ?>
            <label for="shipping_country" class="control-label"><?php echo _l('shipping_country'); ?></label>
            <select name="shipping_country" class="selectpicker" data-width="100%" data-actions-box="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
             <option></option>
             <?php 
             foreach ($countries as $key => $value) { ?>
                <option value="<?php echo html_entity_decode($value['country_id']) ?>"><?php echo html_entity_decode($value['short_name']) ?></option>                    
             <?php } ?>  
          </select>
          
       </div>
       
    </div>
 </div>
</div>
</div>
</div>
</div>
<?php echo form_close(); ?>
</div>

