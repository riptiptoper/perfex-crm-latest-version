(function($) {
	$('input[name="return_type"]').on('change', function(){
		var val = $(this).val();
		$('.refund_quantity_reason').val('');
		if(val == 'fully'){
			var quantity_obj = $('.refund_quantity_item');
			$.each(quantity_obj, function( key, value ) {
				$(this).attr('readonly', true);
				$(this).val($(this).attr('max'));
			}); 
			$('.select-item-checkbox').prop('checked', true).closest('td,th').addClass('hide');
		}
		else{
			$('.refund_quantity_item').removeAttr('readonly');
			$('.select-item-checkbox').prop('checked', true).closest('td,th').removeClass('hide');
		}
	});

	$('#return_order_send_rq_btn').on('click', function(){
		var reason = $('textarea[name="reason"]').val();
		var select_item_checkbox = $('.select-item-checkbox');
		var check_selected_item = false;
		select_item_checkbox.each(function(i, e){
			if($(this).is(':checked')){
				check_selected_item = true;
			}
		});
		if(!check_selected_item){
			alert_float('danger', $('input[name="please_select_item"]').val());
			return false;
		}
		if(reason == ''){
			alert_float('danger', $('input[name="please_input_return_reason"]').val());
			return false;
		}
		$('#return_order_submit_btn').click();
	});
})(jQuery);

function open_modal_chosse(){
	"use strict";
	$('#chosse').modal();
}
function open_refund_modal(){
	"use strict";
	$('#refund_modal').modal();
}
function select_all_item(el){
	var check = $(el).is(':checked');
	if(check == true){
		$('td .select-item-checkbox').prop('checked', true);
	}
	else{
		$('td .select-item-checkbox').prop('checked', false);
	}
}
function select_return_item(el){
	var check = $(el).is(':checked');
	if(check == false){
		$('th .select-item-checkbox').prop('checked', false);
	}
}