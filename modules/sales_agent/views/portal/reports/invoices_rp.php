<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="list_invoices_rp" class="hide">
   <div class="row">
      <div class="col-md-4">
         <div class="form-group">
            
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
<table class="table table-list-inv-report scroll-responsive">
   <thead>
      <tr>
         <th><?php echo _l('invoice_no'); ?></th>
         <th><?php echo _l('customer'); ?></th>
         <th><?php echo _l('invoice_date'); ?></th>
         <th><?php echo _l('due_date'); ?></th>
         <th><?php echo _l('payment_status'); ?></th>
         <th><?php echo _l('invoice_amount'); ?></th>
         <th><?php echo _l('tax_value'); ?></th>
         <th><?php echo _l('total_included_tax'); ?></th>
      </tr>
   </thead>
   <tbody></tbody>
   <tfoot>
      <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td class="total_value"></td>
         <td class="total_tax"></td>
         <td class="total"></td>
      </tr>
   </tfoot>
</table>
</div>
