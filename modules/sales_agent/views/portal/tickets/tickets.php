<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12 mtop15">
  <div class="panel_s section-heading section-tickets">
    <div class="panel-body">
      <h4 class="no-margin section-text"><?php echo _l('clients_tickets_heading'); ?></h4>
    </div>
  </div>
  <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
          <h3 class="text-success pull-left no-mtop tickets-summary-heading"><?php echo _l('tickets_summary'); ?></h3>
          <a href="<?php echo site_url('sales_agent/portal/open_ticket'); ?>" class="btn btn-info new-ticket pull-right">
            <?php echo _l('clients_ticket_open_subject'); ?>
          </a>
          <div class="clearfix"></div>
          <hr />
        </div>
        <?php foreach(sa_get_agent_area_tickets_summary($ticket_statuses) as $status){ ?>
          <div class="col-md-2 list-status ticket-status">
           <a href="<?php echo html_entity_decode($status['url']); ?>" class="<?php if(in_array($status['ticketstatusid'], $list_statuses)){echo 'active';} ?>">
              <h3 class="bold ticket-status-heading">
                <?php echo html_entity_decode($status['total_tickets']); ?>
              </h3>
              <span style="color:<?php echo html_entity_decode($status['statuscolor']); ?>">
                <?php echo html_entity_decode($status['translated_name']); ?>
              </span>
          </a>
        </div>
      <?php } ?>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div class="clearfix"></div>

  <table class="table dt-table dt-inline dataTable no-footer table-tickets" data-order-col="<?php echo (get_option('services') == 1 ? 7 : 6); ?>" data-order-type="desc">
    <thead>
      <th width="10%" class="th-ticket-number"><?php echo _l('clients_tickets_dt_number'); ?></th>
      <th class="th-ticket-subject"><?php echo _l('clients_tickets_dt_subject'); ?></th>
      <?php if($show_submitter_on_table) { ?>
        <th class="th-ticket-submitter"><?php echo _l('ticket_dt_submitter'); ?></th>
      <?php } ?>
      <th class="th-ticket-department"><?php echo _l('clients_tickets_dt_department'); ?></th>
 
      <?php if(get_option('services') == 1){ ?>
        <th class="th-ticket-service"><?php echo _l('clients_tickets_dt_service'); ?></th>
      <?php } ?>
      <th class="th-ticket-priority"><?php echo _l('priority'); ?></th>
      <th class="th-ticket-status"><?php echo _l('clients_tickets_dt_status'); ?></th>
      <th class="th-ticket-last-reply"><?php echo _l('clients_tickets_dt_last_reply'); ?></th>
      <?php
      $custom_fields = get_custom_fields('tickets',array('show_on_client_portal'=>1));
      foreach($custom_fields as $field){ ?>
        <th><?php echo html_entity_decode($field['name']); ?></th>
      <?php } ?>
    </thead>
    <tbody>
      <?php foreach($tickets as $ticket){ ?>
        <tr class="<?php if($ticket['clientread'] == 0){echo 'text-danger';} ?>">
          <td data-order="<?php echo html_entity_decode($ticket['ticketid']); ?>">
            <a href="<?php echo site_url('sales_agent/portal/ticket/'.$ticket['ticketid']); ?>">
              #<?php echo html_entity_decode($ticket['ticketid']); ?>
            </a>
          </td>
          <td>
            <a href="<?php echo site_url('sales_agent/portal/ticket/'.$ticket['ticketid']); ?>">
              <?php echo html_entity_decode($ticket['subject']); ?>
            </a>
          </td>
          <?php if($show_submitter_on_table) { ?>
            <td>
              <?php echo html_entity_decode($ticket['user_firstname'] . ' ' . $ticket['user_lastname']);  ?>
            </td>
          <?php } ?>
          <td>
            <?php echo html_entity_decode($ticket['department_name']); ?>
          </td>

          <?php if(get_option('services') == 1){ ?>
            <td>
              <?php echo html_entity_decode($ticket['service_name']); ?>
            </td>
          <?php } ?>
          <td>
            <?php
            echo ticket_priority_translate($ticket['priority']);
            ?>
          </td>
          <td>
            <span class="label inline-block" style="background:<?php echo html_entity_decode($ticket['statuscolor']); ?>">
              <?php echo ticket_status_translate($ticket['ticketstatusid']); ?></span>
            </td>
            <td data-order="<?php echo html_entity_decode($ticket['lastreply']); ?>">
              <?php
              if ($ticket['lastreply'] == NULL) {
               echo _l('client_no_reply');
             } else {
               echo _dt($ticket['lastreply']);
             }
             ?>
           </td>
           <?php foreach($custom_fields as $field){ ?>
            <td>
              <?php echo get_custom_field_value($ticket['ticketid'],$field['id'],'tickets'); ?>
            </td>
          <?php } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>


  </div>
  </div>
  </div>
</div>