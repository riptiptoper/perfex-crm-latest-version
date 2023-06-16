<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table" >
    <thead>
       <tr>
       	  <th class="hide"></th>
          <th ><?php echo _l('order_number'); ?></th>
          <th ><?php echo _l('order_date'); ?></th>
          <th ><?php echo _l('receiver'); ?></th>
          <th ><?php echo _l('total_orders'); ?></th>
          <th ><?php echo _l('options'); ?></th>
      </tr>
    </thead>
    <tbody>
    	<?php foreach($pre_oreders as $key => $order){ ?>
    		<tr>
    			<td class="hide"><?php echo html_entity_decode($key); ?></td>
    			<td><a href="<?php echo site_url('omni_sales/omni_sales_client/view_order_detail/'.$order['order_number']); ?>" target="_blank"><?php echo html_entity_decode($order['order_number']); ?></a></td>
    			<td><span class="label label-info"><?php echo _dt($order['datecreator']); ?></span></td>
    			<td><?php echo get_company_name($order['userid']); ?></td>
    			<td><?php echo app_format_money($order['total'],''); ?></td>
    			<td>
    				<a href="<?php echo site_url('omni_sales/omni_sales_client/view_order_detail/'.$order['order_number']); ?>" target="_blank" class="btn btn-icon btn-warning"><i class="fa fa-eye"></i></a>
    				<?php if($order['status'] == 0){ ?>
    					<a href="<?php echo site_url('omni_sales/omni_sales_client/create_pre_order/'.$order['id']); ?>" class="btn btn-icon btn-info"><i class="fa fa-pencil"></i></a>
    					<a href="" class="btn btn-icon btn-danger"><i class="fa fa-remove"></i></a>
    				<?php } ?>
    			</td>
    		</tr>
    	<?php } ?>
    </tbody>
</table>