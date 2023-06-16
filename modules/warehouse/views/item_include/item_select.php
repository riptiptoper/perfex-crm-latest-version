<?php if(isset($label_name)){ ?>
	<label><?php echo _l($label_name); ?>  </label>
<?php } ?>

<?php 
$data_action = '';
if(isset($multiple) && $multiple){ 
	$data_action = ' data-actions-box="true"';
}
?>

<div class="form-group  select-placeholder">
	<select name="<?php echo html_entity_decode($select_name) ?>" class="selectpicker <?php if($ajaxItems == true){echo ' ajax-search';} ?>" data-width="100%" id="<?php echo html_entity_decode($id_name) ?>" <?php if(isset($multiple)){ ?> multiple="<?php echo html_entity_decode($multiple) ?>" <?php } ?> data-none-selected-text="<?php if(isset($data_none_selected_text)){echo _l($data_none_selected_text);} ?>" data-live-search="true" <?php echo $data_action; ?>>
		<option value=""></option>
		<?php foreach($items as $group_id=>$_items){ ?>
			<optgroup data-group-id="<?php echo html_entity_decode($group_id); ?>" label="<?php echo html_entity_decode($_items[0]['group_name']); ?>">
				<?php foreach($_items as $item){ ?>
					<?php 
						$selected = '';
						if(isset($item_id) && $item['id'] == $item_id){
							$selected = ' selected';
						}
					 ?>
					<option value="<?php echo html_entity_decode($item['id']); ?>" <?php echo html_entity_decode($selected); ?> data-subtext="<?php echo strip_tags(mb_substr($item['long_description'],0,200)).'...'; ?>">(<?php echo app_format_number($item['rate']); ; ?>) <?php echo html_entity_decode($item['description']); ?></option>
				<?php } ?>
			</optgroup>
		<?php } ?>
	</select>
</div>
