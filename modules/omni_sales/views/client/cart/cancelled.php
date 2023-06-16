<?php 
$currency_name = '';
if(isset($base_currency)){
    $currency_name = $base_currency->name;
}
if($cart_list && count($cart_list) > 0){
    foreach ($cart_list as $key => $value) {
  ?>
<div class="body-list">
  <ul class="d-flex">
    <li>
        <strong>
            <?php echo html_entity_decode($value['order_number']); ?>            
        </strong>
        <?php if($value['channel_id'] == 6){ ?>
           &nbsp;&nbsp;&nbsp;&nbsp;<span class="badge badge-primary"><?php echo _l('omni_pre_order') ?></span>
        <?php } ?>
    </li>
    <li><?php  echo html_entity_decode($value['datecreator']); ?></li>
    <li><?php echo _l('total_orders').': '.app_format_money($value['total'],'').' '.$currency_name; ?></li>
    <li class="d-grid">
        <div>
          <a class="btn btn-danger pull-right" href="<?php echo site_url('omni_sales/omni_sales_client/view_order_detail/'.$value['order_number']); ?>"><i class="fa fa-eye"></i> <?php echo _l('view_orders'); ?></a>            
        </div>
          <?php 
          if($value['status'] == 8){ 
              if($value['admin_action'] == 0){
                echo '<div class="text-danger mtop10">'._l('was_canceled_by_you_for_a_reason').': '._l($value['reason']).'</div>'; 
            }
            else
            {
                echo '<div class="text-danger mtop10">'._l('was_canceled_by_us_for_a_reason').': '._l($value['reason']).'</div>';  
            } 
        } ?> 
    </li>
  </ul>
</div> 
<?php }} else { ?>
    <div class="row">
        <div class="col-md-12 text-center h4"><?php echo _l('omni_no_orders_yet'); ?></div>        
    </div>
<?php } ?>