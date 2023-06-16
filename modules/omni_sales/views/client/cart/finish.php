<?php 
$currency_name = '';
if(isset($base_currency)){
    $currency_name = $base_currency->name;
}
foreach ($cart_list as $key => $value) {
	$key = 0;
	$total = 0;
	$item_html = '';
    $data_detailt = $this->omni_sales_model->get_cart_detailt_by_master($value['id']);
    if($data_detailt){
     foreach ($data_detailt as $key => $item) {   
      $total += $item['quantity'] * $item['prices'];
  }
}
?>
<div class="row order">
    <div class="panel_s">
        <div class="panel-body">
         <div class="col-md-12 head-order">
          <div class="col-md-6">
              <h5><?php echo _l('order_number');  ?>: #<?php  echo html_entity_decode($value['order_number']); ?></h5>
              <span><?php echo _l('order_date');  ?>: <?php  echo html_entity_decode($value['datecreator']); ?></span>
          </div>
          <div class="col-md-6">
             <h5><?php echo _l('receiver').': '.$value['company']; ?></h5>
         </div>
     </div>
     <div class="clearfix"></div>  
     <br>
     <br>
     <?php 
     if($data_detailt){
        foreach ($data_detailt as $key => $item) {   
            if($key == 0){
             $key = 1;
             ?>
             <div class="row"> 
              <div class="col-md-12"> 
                 <div class="col-md-8">                      				 
                     <a href="#">
                        <img class="product pic" src="<?php echo $this->omni_sales_model->get_image_items($item['product_id']); ?>">  
                        <strong class="product_name">
                            <?php echo html_entity_decode($item['product_name']); ?>			                                    
                        </strong>
                    </a>
                </div> 
                <div class="col-md-4">
                   <br>
                   <br>
                   <span class="total_order">
                    <?php echo _l('total_orders').': '.app_format_money($total,'').' '.$currency_name; ?>
                </span>
            </div>
        </div>
    </div>
<?php	}
}
}
?>

<div class="col-md-12">
  <a class="btn btn-danger pull-right" href="<?php echo site_url('omni_sales/omni_sales_client/view_order_detail/'.$value['order_number']); ?>"><i class="fa fa-eye"></i> <?php echo _l('view_orders'); ?></a>
</div>                     		
</div>
</div>
</div>
<?php }
?>