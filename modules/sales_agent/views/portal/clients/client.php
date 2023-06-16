<?php $CI = &get_instance();

    $groupName = $CI->app_scripts->default_theme_group();
    add_bootstrap_select_js_assets($groupName); ?>

<div class="col-md-12 mtop15">
	<?php echo form_open_multipart($this->uri->uri_string(),array('autocomplete'=>'off')); ?>

	<div class="panel_s">
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<h4><?php echo html_entity_decode($title); ?></h4>
					<hr>
				</div>
				
			</div>
			<div class="row">
				<div class="col-md-6">
					<label for="name"><span class="text-danger">* </span><?php echo _l('sa_client_name'); ?></label>
					<?php $value = (isset($_client) ? $_client->name : '');
						echo render_input('name', '', $value, 'text',['required' => true]); 
					?>
				</div>
				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->vat : '');
						echo render_input('vat', 'vat_number', $value); 
					?>
				</div>
				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->email : '');
						echo render_input('email', 'email', $value); 
					?>
				</div>
				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->phonenumber : '');
						echo render_input('phonenumber', 'phonenumber', $value); 
					?>
				</div>
				

				<div class="form-group col-md-6">
                    <label for="lastname"><?php echo _l('clients_country'); ?></label>
                    <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" name="country" class="form-control selectpicker" id="country">
                        <option value=""></option>
                        <?php foreach(get_all_countries() as $country){ ?>
                            <?php
                            $selected = '';
                            if(isset($_client) && $_client->country == $country['country_id']){echo html_entity_decode($selected = true);}
                            ?>
                            <option value="<?php echo html_entity_decode($country['country_id']); ?>" <?php echo set_select('country', $country['country_id'],$selected); ?>><?php echo html_entity_decode($country['short_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->street : '');
						echo render_input('street', 'sa_street', $value); 
					?>
				</div>

                <div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->city : '');
						echo render_input('city', 'city', $value); 
					?>
				</div>

				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->zip : '');
						echo render_input('zip', 'zip', $value); 
					?>
				</div>

				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->state : '');
						echo render_input('state', 'state', $value); 
					?>
				</div>

				<div class="col-md-6">
					<?php $value = (isset($_client) ? $_client->group : ''); 
					 echo sa_render_select('group',$groups,array('id','name'),'group', $value);
					?>
				</div>

				<div class="col-md-12">
					<?php $value = (isset($_client) ? $_client->address : '');
          			echo render_textarea('address','address',$value,array(),array(),'mtop15'); ?>
				</div>

			</div>

			<div class="row">
				<div class="modal-footer">
	                <button type="submit" class="btn btn-info commission-policy-form-submiter"><?php echo _l('submit'); ?></button>
	            </div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>