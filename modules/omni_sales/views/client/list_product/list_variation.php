<?php 
$has_classify = 0;
$classify_list = json_decode($parent_attributes);
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
	<label class="amount_available"><?php echo _l('amount_available'); ?>: <span id="amount_available">0</span></label>
	<?php  } ?>