<?php hooks()->do_action('head_element_client'); ?>
<?php if($detailt_product != null) { ?>
	<?php $id = $detailt_product->id;
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

	$tax_value = 0;
	$tax_data = $this->omni_sales_model->get_tax_info_by_product($id);
	if($tax_data){
		$tax_value = $tax_data->taxrate;
		if($tax_value > 0){
			$price = $price * (1 + ($tax_value / 100));
		}
	}

	$user_id = '';
	if(is_client_logged_in()) {
		$user_id = get_client_user_id();
	}

	$date = date('Y-m-d');
	$discount_percent = 0;
	$prices_discount  = 0;

	$discount = $this->omni_sales_model->get_discount_item_portal($id, $user_id, $date);
	if($discount){
		$discount_percent = $discount->discount;
		$prices_discount = $price-(($discount_percent * $price) / 100);
	}


	?>

	<div class="wrapper row">
		<input type="hidden" name="parent_id" value="<?php echo htmlentities($id); ?>">
		<input type="hidden" name="id" value="<?php echo htmlentities($id); ?>">
		<div class="preview col-md-6">						
			<div class="preview-pic tab-content">
				<?php 
				$date = date('Y-m-d');
				$html_listimage = '';
				$active = 'active';
				$list_filename = $this->omni_sales_model->get_all_image_file_name($id);
				foreach ($list_filename as $key => $value) {
					$is_image_exist = false;
					if (file_exists('modules/warehouse/uploads/item_img/' . $id . '/' . $value["file_name"])) {
						$is_image_exist = true;					
					} 
					elseif(file_exists('modules/purchase/uploads/item_img/'. $id . '/' . $value["file_name"])) {
						$is_image_exist = true;					
					}
					elseif(file_exists('modules/manufacturing/uploads/products/'. $id . '/' . $value["file_name"])) {
						$is_image_exist = true;					
					}

					if($is_image_exist == true){

						?>
						<div class="contain_image tab-pane <?php echo html_entity_decode($active); ?>" id="pic-<?php echo html_entity_decode($key); ?>">
							<img src="<?php echo omni_check_image_items($id, $value['file_name']); ?>" />
						</div>
						<?php
						$active = '';
						$html_listimage.='<li class="'.html_entity_decode($active).'"><a data-target="#pic-'.html_entity_decode($key).'" data-toggle="tab"><img src="'.omni_check_image_items($id, $value['file_name']).'" /></a></li>';
					} 
				}

				if($html_listimage == ''){ 
					$active = 'active';
					$file_path  = 'modules/omni_sales/assets/images/no_image.jpg';

					?>
					<div class="contain_image tab-pane <?php echo html_entity_decode($active); ?>" id="pic-<?php echo html_entity_decode(0); ?>">
						<img src="<?php echo site_url($file_path); ?>" />
					</div>
					<?php
					$html_listimage.='<li class="'.html_entity_decode($active).'"><a data-target="#pic-'.html_entity_decode(0).'" data-toggle="tab"><img src="'.site_url($file_path).'" /></a></li>';
				}

				?>		  	  
			</div>
			<ul class="preview-thumbnail nav nav-tabs">
				<?php echo html_entity_decode($html_listimage); ?>
			</ul>		
		</div>

		<div class="details col-md-6">
			<h3 class="product-title"><?php echo html_entity_decode($detailt_product->description); ?></h3>
			<h3 class="product-title sub hide"></h3>
			<span class="product-description"><a href="<?php echo site_url('omni_sales/omni_sales_client/index/1/'.$group_id); ?>"><?php echo _l('group').': '.$group; ?></a></span>

			<p class="product-description"><?php echo html_entity_decode($detailt_product->long_description); ?></p>
			<p class="product-description sub hide"></p>
			<h4 class="price">

				<?php echo _l('price').': '; ?> 
				<?php if($discount_percent > 0){
					?>
					<span class="new-price text-danger"><?php echo app_format_money($prices_discount, $currency_name); ?></span>
					<span class="new-price sub"><?php echo app_format_money($price, $currency_name); ?></span>	   
					<small class="text-danger border rounded border-danger badge float-left bg-white"><?php echo html_entity_decode('-'.$discount_percent.'%') ?></small>
				<?php }else{ ?>
					<span class="new-price text-danger"><?php echo app_format_money($price, $currency_name); ?></span>	  
				<?php } ?>

			</h4>
			<br>
			<div class="col-md-12">
				<input type="hidden" name="quantity_available" value="<?php echo html_entity_decode($amount_in_stock); ?>">
			</div>
			<?php 
			$has_classify = 0;
			$classify_list = json_decode($detailt_product->parent_attributes);
			if(count($classify_list) > 0){
				foreach ($classify_list as $key => $classify) {
					if($has_classify == 0){
						if($classify->name == ""){
							$has_classify = 0;
							break;
						}
						else{
							$has_classify = 1;						
						}
					}
					?>
					<div class="row variation-row row-<?php echo html_entity_decode($key); ?>">
						<div class="col-md-12 variation-items">
							<label><?php echo html_entity_decode($classify->name); ?></label><br>
							<?php 
							foreach ($classify->options as $options) { ?>
								<button class="label label-default product-variation">
									<?php echo html_entity_decode($options); ?>
								</button>
								<?php 
							} ?>
						</div>	
					</div><br>
				<?php  } ?>
				<label class="amount_available hide"><?php echo _l('amount_available'); ?>: <span id="amount_available"></span></label>
			<?php } ?>
			<input type="hidden" name="has_classify" value="<?php echo html_entity_decode($has_classify); ?>">
			<input type="hidden" name="msg_classify" value="<?php echo _l('please_choose'); ?>">
			<br>
			<div class="action row">
				<div class="col-md-12">
					<div class="form-group pull-left">
						<div class="input-group">
							<span class="input-group-addon minus" onclick="change_qty(-1);">
								<i class="fa fa-minus"></i>
							</span>
							<input id="quantity" class="form-control text-center" type="number" value="1" min="1" max="<?php echo html_entity_decode($amount_in_stock); ?>">
							<span class="input-group-addon plus" onclick="change_qty(1);">
								<i class="fa fa-plus"></i>				      
							</span>
						</div>
					</div>

					<?php if($amount_in_stock > 0){ ?>
						<button class="btn btn-success pull-left mleft10 add_to_cart <?php if(in_array($id, $array_list_id)){ echo 'hide'; }else{ echo ''; } ?>" type="button">
							<i class="fa fa-shopping-cart"></i> <?php echo _l('add_to_cart'); ?>
						</button>
						<button class="btn btn-primary pull-left mleft10 added_to_cart <?php if(in_array($id, $array_list_id)){ echo ''; }else{ echo 'hide'; } ?>" type="button">
							<i class="fa fa-check"></i> <?php echo _l('added'); ?>
						</button>	
						<?php 
					}
					elseif($has_classify){ ?>
						<button class="btn btn-success pull-left mleft10 add_to_cart" type="button">
							<i class="fa fa-shopping-cart"></i> <?php echo _l('add_to_cart'); ?>
						</button>
					<?php }
					else{ ?>			
						<button class="btn btn-default pull-left mleft10 input-lg" type="button"><?php echo _l('out_of_stock'); ?></button>	
					<?php } ?>			
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="col-md-12">	
		<div class="wrap_contents long_descriptions" >
			<?php
			echo html_entity_decode($detailt_product->long_descriptions); 
			?>
		</div>
		<div class="wrap_contents long_descriptions sub hide">
		</div>
		<br>
	</div>

	<?php if(count($product) > 0){ ?>
		<div class="right-detail">
			<div class="line">&#9658;<?php echo _l('suggested_products'); ?></div>
			<div id="slidehind">    
				<div class="frame-slide">
					<div class="frame" id="frameslide">
						<?php 
						foreach ($product as $key => $item) { ?>
							<a href="<?php 	echo site_url('omni_sales/omni_sales_client/detailt/'.$item['id']); ?>">
								<?php 
								$file_name = $this->omni_sales_model->get_image_file_name($item['id']);
								?>
								<img src="<?php echo $this->omni_sales_model->get_image_items($item['id']); ?>">
								<div class="name"><?php echo html_entity_decode($item['name']); ?></div>
								<div class="price"><?php echo app_format_money($item['price'],$currency_name); ?></div>
							</a>
						<?php } ?>               
					</div>
				</div>
				<button class="btn btn-primary leftLst" onclick="scroll_slide(-1);"><i class="fa fa-chevron-left"></i></button>
				<button class="btn btn-primary rightLst" onclick="scroll_slide(1);"><i class="fa fa-chevron-right"></i></button>      	
			</div>
		</div>
	<?php } ?>

	<div class="modal fade" id="alert_add" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 alert_content">
							<div class="clearfix"></div>
							<br>
							<br>
							<center class="add_success hide"><h4><?php echo _l('successfully_added'); ?></h4></center>
							<center class="add_error hide"><h4><?php echo _l('sorry_the_number_of_current_products_is_not_enough'); ?></h4></center>
							<br>
							<br>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>              
			</div>
		</div>
	</div>
	<input type="hidden" name="token_name" value="<?php echo html_entity_decode($this->security->get_csrf_token_name()); ?>">
	<input type="hidden" name="token_hash" value="<?php echo html_entity_decode($this->security->get_csrf_hash()); ?>">
<?php }
else{ ?>
	<br>
	<br>
	<br>
	<br>
	<center>
		<h4>
			<?php echo _l('data_does_not_exist'); ?>			
		</h4>
	</center>

	<br>
	<div class="col-md-12 text-center">
		<a href="javascript:history.back()" class="btn btn-primary">
			<i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?php echo _l('return_to_the_previous_page'); ?></a>
		</div>
	</div>
<?php } ?>
<?php hooks()->do_action('client_pt_footer_js'); ?>

