<?php 
$id_s = '';
$order_id_s = '';
$staff_id_s  = '';
$refunded_on_s = _d(date('Y-m-d'));
$payment_mode_s = '';
$note_s = '';
$amount_s = '';
if(isset($refund)){
	$id_s = $refund->id;
	$order_id_s = $refund->order_id;
	$staff_id_s  = $refund->staff_id;
	$refunded_on_s = _d($refund->refunded_on);
	$payment_mode_s = $refund->payment_mode;
	$note_s = clear_textarea_breaks($refund->note);
	$amount_s = $refund->amount;
	$max = $max + $refund->amount;
}

?>
<input type="hidden" name="id" value="<?php echo html_entity_decode($id_s); ?>">


<div class="col-md-12">
	<?php
	echo render_input('amount', 'amount', $amount_s, 'number', array('max' => $max, 'min' => 0, 'data-type'=>'currency')); ?>
	<?php echo render_date_input('refunded_on', 'credit_date', $refunded_on_s); ?>
	<div class="form-group">
		<label for="payment_mode" class="control-label"><?php echo _l('payment_mode'); ?></label>
		<select class="selectpicker" name="payment_mode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
			<option value=""></option>
			<?php foreach($payment_modes as $mode){ ?>
				<option value="<?php echo html_entity_decode($mode['id']); ?>"<?php if(isset($refund) && $refund->payment_mode == $mode['id']){echo ' selected'; } ?>><?php echo $mode['name']; ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="col-md-12">
	<div class="form-group">
		<label for="note" class="control-label"><?php echo _l('note'); ?></label>
		<textarea name="note" class="form-control" rows="8" id="note"><?php echo html_entity_decode($note_s); ?></textarea>
	</div>
</div>
