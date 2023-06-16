<?php 
$date = date('Y-m-d');
$currency_name = '';
if(isset($base_currency)){
	$currency_name = $base_currency->name;
}
$array_list_id = [];
if(isset($_COOKIE['cart_id_list'])){
	$list_id = $_COOKIE['cart_id_list'];
	if($list_id){
		$array_list_id = explode(',',$list_id);
	}
}

$user_id = '';
if(is_client_logged_in()) {
	$user_id = get_client_user_id();
}

?>

<div class="row">
	<?php foreach ($product as $item) { ?>
		<div class="col-md-3 grid col-sm-6">
			<div class="grid product-cell">

				<?php 
				$tax_value = 0;
				$tax_data = $this->omni_sales_model->get_tax_info_by_product($item['id']);
				if($tax_data){
					$tax_value = $tax_data->taxrate;
					if($tax_value > 0){
						$item['price'] = $item['price'] * (1 + ($tax_value / 100));
					}
				}


				$discount_percent = 0;
				$prices_discount  = 0;

				$discount = $this->omni_sales_model->get_discount_item_portal($item['id'], $user_id, $date);
				if($discount){
					$discount_percent = $discount->discount;
					$prices_discount = $item['price']-(($discount_percent * $item['price']) / 100);
				}
				if($discount_percent > 0){ ?>
					<ul class="tag-item right">
						<li class="list-item">
							<div class="primary">
								<div class="content text-white">
									<span class="fs-13 font-italic"><?php echo '-'.$discount_percent.'%' ?></span>
								</div>
							</div>
						</li>					
					</ul>
				<?php } ?>


				<div class="product-image"> 
					<a href="<?php 	echo site_url('omni_sales/omni_sales_client/detailt/'.$item['id']); ?>"> 
						<img class="pic-1" src="<?php echo $this->omni_sales_model->get_image_items($item['id']); ?>">
					</a>               					                  
				</div>
				<div class="product-content">
					<div class="title"><a href="<?php echo site_url('omni_sales/omni_sales_client/detailt/'.$item['id']); ?>"><?php echo html_entity_decode($item['name']); ?></a></div> 
					<div class="price_w">
						<?php if($discount_percent > 0){
							?>
							<span class="price text-danger">
								<?php echo app_format_money($prices_discount, $currency_name); ?>	
							</span>	
							<span class="price sub text-danger">
								<?php echo app_format_money($item['price'], $currency_name); ?>	
							</span>	
						<?php }else{ ?>
							<span class="price text-danger">
								<?php echo app_format_money($item['price'], $currency_name); ?>	
							</span>	
						<?php } ?>
					</div>
				</div>
				<div class="pb-1 add-cart">
					<input type="hidden" name="has_variation" value="<?php echo html_entity_decode($item['has_variation']); ?>">
					<?php
					if($item['w_quantity'] != 0 || $item['has_variation']){  ?>
						<input type="number" name="qty" class="form-control qty" value="1" min="1" max="<?php echo html_entity_decode($item['w_quantity']); ?>" data-w_quantity="<?php echo html_entity_decode($item['w_quantity']); ?>">
						<button type="button" class="added btn btn-primary <?php if(in_array($item['id'],$array_list_id)){ echo ''; }else{ echo 'hide'; } ?>" data-id="<?php echo html_entity_decode($item['id']); ?>"><i class="fa fa-shopping-cart"></i> <?php echo _l('added'); ?></button>	
						<button type="button" class="add_cart btn btn-success <?php if(in_array($item['id'],$array_list_id)){ echo 'hide'; }else{ echo ''; } ?>" data-id="<?php echo html_entity_decode($item['id']); ?>"><i class="fa fa-shopping-cart"></i> <?php echo _l('add_to_cart'); ?></button>
					<?php }else{ ?>
						<button class="btn btn-default"><?php echo _l('out_of_stock'); ?></button>
					<?php } ?>

				</div>
			</div>
		</div>
	<?php } ?>	             
</div>

