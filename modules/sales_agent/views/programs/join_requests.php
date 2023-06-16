<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
          <h4><?php echo _l('join_request_of').' ' ?><a href="<?php echo admin_url('sales_agent/program_detail/'.$program_id); ?>"><?php echo get_program_name_by_id($program_id); ?></a></h4>
          <hr>
        </div>
      </div>
      <table class="table dt-table">
        <thead>

          <th><?php echo _l('agent'); ?></th>
          <th><?php echo _l('email'); ?></th>
          <th><?php echo _l('phone'); ?></th>
          <th><?php echo _l('status'); ?></th>
          <th><?php echo _l('options'); ?></th>
        </thead>
        <tbody>
          <?php foreach($requests as $request){ ?>
            <?php $primary_contact = sa_get_primary_contact_user_id($request['agent_id']); ?>
            <tr>
              <td>
                <a href="<?php echo admin_url('sales_agent/sale_agent/'.$request['agent_id']); ?>"><?php echo get_company_name($request['agent_id']); ?></a>
              </td>
              <td>
                <?php if($primary_contact){ 
                    echo html_entity_decode($primary_contact->email);
               } ?>
              </td>
              <td>
                <?php if($primary_contact){ 
                    echo html_entity_decode($primary_contact->phonenumber);
               } ?>
              </td>
              <td>
                <?php if($request['status'] == 'new'){ ?>
                  <span class="label label-info"><?php echo _l('new'); ?> </span>
                <?php }else{ ?>
                  <span class="label label-success"><?php echo _l('approved'); ?> </span>
                <?php } ?>
              </td>

              <td>
                <?php if($request['status'] == 'new'){ ?>
                  <a href="<?php echo admin_url('sales_agent/approve_join_program_request/'.$program_id.'/'.$request['id'].'/approved'); ?>" class="btn btn-success btn-icon _delete"><i class="fa fa-check"></i></a>
                  <a href="<?php echo admin_url('sales_agent/approve_join_program_request/'.$program_id.'/'.$request['id'].'/reject'); ?>" class="btn btn-warning btn-icon _delete"><i class="fa fa-ban"></i></a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</div>
<?php init_tail(); ?>