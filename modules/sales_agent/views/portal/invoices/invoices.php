<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $groupName = $this->app_scripts->default_theme_group();
    $this->app_scripts->theme('bootstrap-js', 'assets/plugins/bootstrap/js/bootstrap.min.js');
    add_datatables_js_assets($groupName); ?>

<div class="col-md-12 mtop15">
<div class="panel_s">
 <div class="panel-body">
     <?php get_sale_agent_template_part('invoices_stats'); ?>
     <hr />
     <table class="table dt-table dt-inline dataTable no-footer table-invoices" data-order-col="1" data-order-type="desc">
         <thead>
            <tr>
                <th class="th-invoice-number"><?php echo _l('clients_invoice_dt_number'); ?></th>
                <th class="th-invoice-date"><?php echo _l('clients_invoice_dt_date'); ?></th>
                <th class="th-invoice-duedate"><?php echo _l('clients_invoice_dt_duedate'); ?></th>
                <th class="th-invoice-amount"><?php echo _l('clients_invoice_dt_amount'); ?></th>
                <th class="th-invoice-status"><?php echo _l('clients_invoice_dt_status'); ?></th>
                <?php
                $custom_fields = get_custom_fields('invoice',array('show_on_client_portal'=>1));
                foreach($custom_fields as $field){ ?>
                    <th><?php echo html_entity_decode($field['name']); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($invoices as $invoice){ ?>
                <tr>
                    <td data-order="<?php echo html_entity_decode($invoice['number']); ?>"><a href="<?php echo site_url('sales_agent/purchase_invoice/index/' . $invoice['id'] . '/' . $invoice['hash']); ?>" class="invoice-number"><?php echo format_invoice_number($invoice['id']); ?></a></td>
                    <td data-order="<?php echo html_entity_decode($invoice['date']); ?>"><?php echo _d($invoice['date']); ?></td>
                    <td data-order="<?php echo html_entity_decode($invoice['duedate']); ?>"><?php echo _d($invoice['duedate']); ?></td>
                    <td data-order="<?php echo html_entity_decode($invoice['total']); ?>"><?php echo app_format_money($invoice['total'], $invoice['currency_name']); ?></td>
                    <td><?php echo format_invoice_status($invoice['status'], 'inline-block', true); ?></td>
                    <?php foreach($custom_fields as $field){ ?>
                        <td><?php echo get_custom_field_value($invoice['id'],$field['id'],'invoice'); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</div>
</div>