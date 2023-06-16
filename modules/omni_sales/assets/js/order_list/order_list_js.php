<script>

	var lastAddedItemKey = null;
	(function($) {
		"use strict";  

		<?php if(isset($order) && is_numeric($order->estimate_id) && $order->estimate_id != 0){ ?>
			wh_calculate_total();
		<?php } ?>

	})(jQuery);


	(function($) {
		"use strict";


// Recaulciate total on these changes

$("body").on('change', 'input[name="additional_discount"]', function () {

	// var additional_discount = $('input[name="additional_discount"]').val();
	// var main_additional_discount = $('input[name="main_additional_discount"]').val();
	// if(parseFloat(additional_discount) <= parseFloat(main_additional_discount)){
		wh_calculate_total();
	// }
});



})(jQuery);


function wh_clear_item_preview_values(parent) {
	"use strict";

	var previewArea = $(parent + ' .main');
	previewArea.find('input').val('');
	previewArea.find('textarea').val('');
	previewArea.find('select').val('').selectpicker('refresh');
}

function wh_get_item_row_template(name, commodity_name, quantity, unit_name, unit_price, taxname, commodity_code, unit_id, tax_rate, discount, item_key)  {
	"use strict";

	jQuery.ajaxSetup({
		async: false
	});

	var d = $.post(admin_url + 'warehouse/get_order_return_row_template', {
		name: name,
		commodity_name : commodity_name,
		quantity : quantity,
		unit_name : unit_name,
		unit_price : unit_price,
		taxname : taxname,
		commodity_code : commodity_code,
		unit_id : unit_id,
		tax_rate : tax_rate,
		discount : discount,
		item_key : item_key
	});
	jQuery.ajaxSetup({
		async: true
	});
	return d;
}

function wh_delete_item(row, itemid,parent) {
	"use strict";

	$(row).parents('tr').addClass('animated fadeOut', function () {
		setTimeout(function () {
			$(row).parents('tr').remove();
			wh_calculate_total();
		}, 50);
	});
	if (itemid && $('input[name="isedit"]').length > 0) {
		$(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
	}

	$('select[name="return_type"]').val('partially').change();
}

function wh_reorder_items(parent) {
	"use strict";

	var rows = $(parent + ' .table.has-calculations tbody tr.item');
	var i = 1;
	$.each(rows, function () {
		$(this).find('input.order').val(i);
		i++;
	});
}

function wh_calculate_total(){
	"use strict";
	if ($('body').hasClass('no-calculate-total')) {
		return false;
	}

	var calculated_tax,
	taxrate,
	item_taxes,
	row,
	_amount,
	_tax_name,
	taxes = {},
	taxes_rows = [],
	subtotal = 0,
	total = 0,
	total_money = 0,
	total_tax_money = 0,
	quantity = 1,
	total_discount_calculated = 0,
	total_tax_calculated = 0,
	item_discount_percent = 0,
	item_discount = 0,
	item_total_payment,
	rows = $('.table.has-calculations tbody tr.item'),
	subtotal_area = $('#subtotal'),
	discount_area = $('#discount_area'),
	adjustment = $('input[name="adjustment"]').val(),
			// discount_total_type = $('.discount-total-type.selected'),
		discount_type = $('select[name="discount_type"]').val(),
		discount_total_type = $('select[name="add_discount_type"]').val(),
		shipping = $('input[name="shipping"]').val();

		if(discount_total_type == 1){
			// %
			var discount_percent = parseFloat($('input[name="add_discount"]').val());
			var discount_fixed = 0;

		}else if(discount_total_type == 2){
			// fixed
			var discount_percent = 0;
			var discount_fixed = parseFloat($('input[name="add_discount"]').val());
		}
	

		$('.wh-tax-area').remove();

		$.each(rows, function () {

			var item_tax = 0,
			item_amount  = 0;

			quantity = $(this).find('[data-quantity]').val();
			if (quantity === '') {
				quantity = 1;
				$(this).find('[data-quantity]').val(1);
			}
			item_discount_percent = $(this).find('td.discount input').val();

			if (isNaN(item_discount_percent) || item_discount_percent == '') {
				item_discount_percent = 0;
			}

			_amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
			item_amount = _amount;
			_amount = parseFloat(_amount);

			$(this).find('td.amount').html(format_money(_amount));

			subtotal += _amount;

			row = $(this);
			item_taxes = $(this).find('select.taxes').val();

			if (item_taxes) {
				$.each(item_taxes, function (i, taxname) {
					taxrate = row.find('select.taxes [value="' + taxname + '"]').data('taxrate');
					calculated_tax = (_amount / 100 * taxrate);
					item_tax += calculated_tax;
					if (!taxes.hasOwnProperty(taxname)) {
						if (taxrate != 0) {
							_tax_name = taxname.split('|');
							var tax_row = '<tr class="wh-tax-area"><td>' + _tax_name[0] + '(' + taxrate + '%)</td><td id="tax_id_' + slugify(taxname) + '"></td></tr>';
							$(discount_area).after(tax_row);
							taxes[taxname] = calculated_tax;
						}
					} else {
						// Increment total from this tax
						taxes[taxname] = taxes[taxname] += calculated_tax;
					}
				});
			}
			//Discount of item
			item_discount = (parseFloat(item_amount) + parseFloat(item_tax) ) * parseFloat(item_discount_percent) / 100;
			item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);

			// Append value to item
			total_discount_calculated += item_discount;
			$(this).find('td.discount_money input').val(item_discount);
			$(this).find('td.total_after_discount input').val(item_total_payment);

			$(this).find('td.label_discount_money').html(format_money(item_discount));
			$(this).find('td.label_total_after_discount').html(format_money(item_total_payment));

		});

	// Discount by percent
	if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type == 1) {
		total_discount_calculated = (subtotal * discount_percent) / 100;
	} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type == 2) {
		total_discount_calculated = discount_fixed;
	}

	$.each(taxes, function (taxname, total_tax) {
		if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type == 1) {
			total_tax_calculated = (total_tax * discount_percent) / 100;
			total_tax = (total_tax - total_tax_calculated);
		} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type == 2) {
			var t = (discount_fixed / subtotal) * 100;
			total_tax = (total_tax - (total_tax * t) / 100);
		}


		total += total_tax;
		total_tax_money += total_tax;
		total_tax = format_money(total_tax);
		$('#tax_id_' + slugify(taxname)).html(total_tax);
	});


	total = (total + subtotal);

	total_money = total;
	// Discount by percent
	if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type == 1) {
		total_discount_calculated = (total * discount_percent) / 100;
	} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type == 2) {
		total_discount_calculated = discount_fixed;
	}


	total = total - total_discount_calculated;

	adjustment = parseFloat(adjustment);

	// Check if adjustment not empty
	if (!isNaN(adjustment)) {
		total = total + adjustment;
	}

	if (!isNaN(shipping)) {
		total = total + parseFloat(shipping);
	}

	if(!isNaN(parseFloat(total_discount_calculated))){
		var discount_html = '-' + format_money(parseFloat(total_discount_calculated));
	}

	$('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));


	// Append, format to html and display
	$('.discount-total').html(discount_html + hidden_input('discount_total', accounting.toFixed(total_discount_calculated, app.options.decimal_places))  );
	$('.adjustment').html(format_money(adjustment));


	$('.subtotal').html(format_money(subtotal) + hidden_input('subtotal', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('total_amount', accounting.toFixed(total_money, app.options.decimal_places)));

	$('.total').html(format_money(total) + hidden_input('total', accounting.toFixed(total, app.options.decimal_places)));

}


function submit_form(save_and_send_request) {
	"use strict";

	wh_calculate_total();

	var $itemsTable = $('.invoice-items-table');
	var $previewItem = $itemsTable.find('.main');
	var check_warehouse_status = true,
	check_quantity_status = true;

	if ( $itemsTable.length && $itemsTable.find('.item').length === 0) {
		alert_float('warning', '<?php echo _l('wh_enter_at_least_one_product'); ?>', 3000);
		return false;
	}

	$('input[name="save_and_send_request"]').val(save_and_send_request);

	var rows = $('.table.has-calculations tbody tr.item');
	$.each(rows, function () {
		var quantity_value = $(this).find('td.quantities input').val();
		if(parseFloat(quantity_value) == 0){
			check_quantity_status = false;
		}
		
	})

	if( check_quantity_status == true ){
		// Add disabled to submit buttons
		$(this).find('.add_goods_receipt_send').prop('disabled', true);
		$(this).find('.add_goods_receipt').prop('disabled', true);
		$('#add_edit_order_return').submit();
	}else{
		alert_float('warning', '<?php echo _l('please_choose_quantity_export') ?>');

	}

	return true;
}

$("body").on('change', 'select[name="estimate_id"]', function () {

	"use strict";

	var estimate_id = $('select[name="estimate_id"]').val();

	$.post(admin_url + 'omni_sales/order_list_get_estimate_data/'+estimate_id).done(function(response){
		response = JSON.parse(response);

		$('.s_table table.invoice-items-table.items tbody').html('');
		$('.s_table table.invoice-items-table.items tbody').append(response.cart_detail);

		$('select[name="customer"]').val((response.cart.userid)).change();
		$('select[name="sale_agent"]').val((response.cart.seller)).change();
		$('select[name="discount_type"]').val((response.cart.discount_type_str)).change();
		$('select[name="currency"]').val((response.cart.currency)).change();

		$('input[name="subtotal"]').val(response.cart.sub_total);
		$('input[name="add_discount"]').val(response.cart.add_discount);
		$('input[name="adjustment"]').val(response.cart.adjustment);

		$('textarea[name="note"]').val((response.cart.notes));
		$('textarea[name="client_note"]').val((response.cart.staff_note));
		$('textarea[name="terms"]').val((response.cart.terms));

		setTimeout(function () {
			wh_calculate_total();
		}, 15);

		init_selectpicker();
		init_datepicker();
		wh_reorder_items('.s_table');
		// wh_clear_item_preview_values('.s_table');
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');
	});

});


   $("body").on('submit', '._transaction_form_manual_order', function () {

        // On submit re-calculate total and reorder the items for all cases.
        if (!isNaN(estimate_id) && estimate_id != '' ) {

        	wh_calculate_total();
        }else{
        	calculate_total();
        }

        $('body').find('#items-warning').remove();
        var $itemsTable = $(this).find('table.items');
        var $previewItem = $itemsTable.find('.main');

        if ($previewItem.find('[name="description"]').length && $previewItem.find('[name="description"]').val().trim().length > 0 &&
            $previewItem.find('[name="rate"]').val().trim().length > 0) {

            $itemsTable.before('<div class="alert alert-warning mbot20" id="items-warning">' + app.lang.item_forgotten_in_preview + '<i class="fa fa-angle-double-down pointer pull-right fa-2x" style="margin-top:-4px;" onclick="add_item_to_table(\'undefined\',\'undefined\',undefined); return false;"></i></div>');

            $('html,body').animate({
                scrollTop: $("#items-warning").offset().top
            });

            return false;

        } else {
            if ($itemsTable.length && $itemsTable.find('.item').length === 0) {
                $itemsTable.before('<div class="alert alert-warning mbot20" id="items-warning">' + app.lang.no_items_warning + '</div>');
                $('html,body').animate({
                    scrollTop: $("#items-warning").offset().top
                });
                return false;
            }
        }

        reorder_items();

        // Remove the disabled attribute from the disabled fields becuase if they are disabled won't be sent with the request.
        $('select[name="currency"]').prop('disabled', false);
        $('select[name="project_id"]').prop('disabled', false);
        $('input[name="date"]').prop('disabled', false);

        // Add disabled to submit buttons
        $(this).find('.transaction-submit').prop('disabled', true);

        return true;
    });

var total_quantity_default = 0;
var additional_discount_default = 0;

</script>