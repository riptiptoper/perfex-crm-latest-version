<div class="col-md-12 head_title pt-2">
		<div><?php echo html_entity_decode($title_group); ?></div>
	</div>
<div class="product_list">	
	<?php $this->load->view('pos/list_product_partial');  ?>
</div> 	  
<br>	
<br>	
<div class="clearfix"></div>
<div class="text-right page-list page">
<?php
 for ($i=1; $i <= $total_page; $i++) {
 	$active = '';
 	if($page == $i){
 		$active = 'active';
 	}
   ?> 
 		<button onclick="change_page(this);" class="btn btn_page <?php echo html_entity_decode($active); ?>" data-page="<?php echo html_entity_decode($i); ?>"><?php echo html_entity_decode($i); ?></button>
<?php } ?>	
</div>
<input type="hidden" name="group_id" value="<?php echo html_entity_decode($group_id); ?>">
