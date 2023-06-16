<script>

	var lastAddedItemKey = null;
	(function($) {
		"use strict";  
init_return_order_currency();
		appValidateForm($('#add_edit_order_return'), {
			email: 'required',
			phonenumber: 'required',
			order_return_name: 'required',
		});

	 // Maybe items ajax search
	 init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'warehouse/wh_commodity_code_search/rate');


	})(jQuery);


	(function($) {
		"use strict";

// Add item to preview from the dropdown for invoices estimates
$("body").on('change', 'select[name="item_select"]', function () {
	var itemid = $(this).selectpicker('val');
	if (itemid != '') {
		wh_add_item_to_preview(itemid);
	}
});


$("body").on('click', '.add_order_return', function () {
	submit_form(false);
});

$('.add_order_return_send').on('click', function() {
	submit_form(true);
});


})(jQuery);


// Add item to preview
function wh_add_item_to_preview(id) {
	"use strict";

	requestGetJSON('warehouse/get_item_by_id/' + id +'/'+true).done(function (response) {
		clear_item_preview_values();

		$('.main input[name="commodity_code"]').val(response.itemid);
		$('.main textarea[name="commodity_name"]').val(response.code_description);
		$('.main input[name="unit_price"]').val(response.rate);
		$('.main input[name="unit_name"]').val(response.unit_name);
		$('.main input[name="unit_id"]').val(response.unit_id);
		$('.main input[name="quantity"]').val(1);
		$('.selectpicker').selectpicker('refresh');

		var taxSelectedArray = [];
		if (response.taxname && response.taxrate) {
			taxSelectedArray.push(response.taxname + '|' + response.taxrate);
		}
		if (response.taxname_2 && response.taxrate_2) {
			taxSelectedArray.push(response.taxname_2 + '|' + response.taxrate_2);
		}

		$('.main select.taxes').selectpicker('val', taxSelectedArray);
		$('.main input[name="unit"]').val(response.unit_name);

		var $currency = $("body").find('.accounting-template select[name="currency"]');
		var baseCurency = $currency.attr('data-base');
		var selectedCurrency = $currency.find('option:selected').val();
		var $rateInputPreview = $('.main input[name="rate"]');

		if (baseCurency == selectedCurrency) {
			$rateInputPreview.val(response.rate);
		} else {
			var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
			if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
				$rateInputPreview.val(response.rate);
			} else {
				$rateInputPreview.val(itemCurrencyRate);
			}
		}

		$(document).trigger({
			type: "item-added-to-preview",
			item: response,
			item_type: 'item',
		});
	});
}



function wh_get_item_preview_values() {
	"use strict";

	var response = {};
	response.commodity_name = $('.invoice-item .main textarea[name="commodity_name"]').val();
	response.quantity = $('.invoice-item .main input[name="quantity"]').val();
	response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
	response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
	response.taxname = $('.main select.taxes').selectpicker('val');
	response.commodity_code = $('.invoice-item .main input[name="commodity_code"]').val();
	response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
	response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
	response.discount = $('.invoice-item .main input[name="discount"]').val();

	return response;
}

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


function wh_reorder_items(parent) {
	"use strict";

	var rows = $(parent + ' .table.has-calculations tbody tr.item');
	var i = 1;
	$.each(rows, function () {
		$(this).find('input.order').val(i);
		i++;
	});
}



function submit_form(save_and_send_request) {
	"use strict";

	var $itemsTable = $('.invoice-items-table');
	var $previewItem = $itemsTable.find('.main');
	var check_warehouse_status = true,
	check_quantity_status = true;

	var sales_order = $('select[name="rel_id"]').val();
	if (sales_order == '') {
		alert_float('warning', '<?php echo _l('omni_please_select_a_sale_order'); ?>', 3000);
		return false;
	}
	if ( $itemsTable.length && $itemsTable.find('.item').length === 0) {
		alert_float('warning', '<?php echo _l('omni_this_order_has_no_products'); ?>', 3000);
		return false;
	}
	var reason = $('textarea[name="return_reason"]').val();
	if(reason == ''){
		alert_float('warning', '<?php echo _l('omni_please_input_return_reason'); ?>', 3000);
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


$("body").on('change', '#company_id', function () {
	var rel_type = $('input[name="rel_type"]').val();
	var company_id = $('select[name="company_id"]').val();
	
	if(company_id.length > 0){
		requestGetJSON('warehouse/wh_client_data/' + company_id +'/'+ rel_type).done(function (response) {
			$('input[name="email"]').val(response.email);
			$('input[name="phonenumber"]').val(response.phonenumber);
		});
	}else{
		$('input[name="email"]').val('');
		$('input[name="phonenumber"]').val('');
	}
});

$('select[name="return_type"]').on('change', function(){
	var val = $(this).val();
	if(val == 'fully'){
		// Refresh to default
		if(response_data){
			$('select[name="company_id"]').val(response_data.company_id).change();
			$('input[name="email"]').val(response_data.email);
			$('input[name="phonenumber"]').val(response_data.phonenumber);
			$('input[name="order_number"]').val(response_data.order_number);
			$('input[name="order_date"]').val(response_data.order_date);
			$('input[name="number_of_item"]').val(response_data.number_of_item);
			$('input[name="order_total"]').val(response_data.order_total);
			$('input[name="datecreated"]').val(response_data.datecreated);

			$('input[name="main_additional_discount"]').val(response_data.additional_discount);
			$('input[name="additional_discount"]').val(response_data.additional_discount);
			$('.invoice-item table.invoice-items-table.items tbody').html('');
			$('.invoice-item table.invoice-items-table.items tbody').append(response_data.result);
			total_quantity_default = response_data.total_item_qty;
			additional_discount_default = response_data.additional_discount;


			setTimeout(function () {
				wh_sale_order_calculate_total();
			}, 15);
			init_selectpicker();
			init_datepicker();
			wh_reorder_items('.invoice-item');
			wh_clear_item_preview_values('.invoice-item');
			$('body').find('#items-warning').remove();
			$("body").find('.dt-loader').remove();
			$('#item_select').selectpicker('val', '');
		}
		$('.input-control').attr('readonly', true);
		$('.remove-control').addClass('hide');
	}
	else{
		$('.input-control').removeAttr('readonly');
		$('.remove-control').removeClass('hide');
	}
});

var response_data;
$('select[name="rel_id"]').on('change', function() {
	"use strict";
	var rel_id = $('select[name="rel_id"]').val();
	var rel_type = $('input[name="rel_type"]').val();
	$.post(admin_url + 'omni_sales/order_return_get_item_data/'+rel_id+'/'+rel_type).done(function(response){
		response = JSON.parse(response);
		response_data = response;
		$('select[name="company_id"]').val(response.company_id).change();
		$('input[name="email"]').val(response.email);
		$('input[name="phonenumber"]').val(response.phonenumber);
		$('input[name="order_number"]').val(response.order_number);
		$('input[name="order_date"]').val(response.order_date);
		$('input[name="number_of_item"]').val(response.number_of_item);
		$('input[name="order_total"]').val(response.order_total);
		$('input[name="datecreated"]').val(response.datecreated);
		$('input[name="currency"]').val(response.currency);

		$('input[name="main_additional_discount"]').val(response.additional_discount);
		$('input[name="additional_discount"]').val(response.additional_discount);
		$('.invoice-item table.invoice-items-table.items tbody').html('');
		$('.invoice-item table.invoice-items-table.items tbody').append(response.result);
		total_quantity_default = response.total_item_qty;
		additional_discount_default = response.additional_discount;
		init_return_order_currency();
		setTimeout(function () {
			wh_sale_order_calculate_total();
		}, 15);
		init_selectpicker();
		init_datepicker();
		wh_reorder_items('.invoice-item');
		wh_clear_item_preview_values('.invoice-item');
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');
		$('#return_type').val('partially').change();
	});
});

var total_quantity_default = 0;
var additional_discount_default = 0;

function wh_sale_order_calculate_total(){
	"use strict";
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
	item_discount_percent = 0,
	item_discount = 0,
	total_qty = 0,
	item_total_payment,
	fee_for_return_order = $('input[name="fee_for_return_order"]').val(),
	rows = $('.table.has-calculations tbody tr.item'),
	subtotal_area = $('#subtotal'),
	discount_area = $('#discount_area'),
	adjustment = $('input[name="adjustment"]').val(),
	discount_percent = 'before_tax',
	discount_fixed = $('input[name="discount_total"]').val(),
	discount_total_type = $('.discount-total-type.selected'),
	discount_type = $('select[name="discount_type"]').val(),
	additional_discount = $('input[name="additional_discount"]').val(),
	main_additional_discount = $('input[name="main_additional_discount"]').val();

	$('.tax-area').remove();

	$.each(rows, function () {

		var item_tax = 0,
		item_amount  = 0;

		quantity = $(this).find('[data-quantity]').val();
		if (quantity === '') {
			quantity = 1;
			$(this).find('[data-quantity]').val(1);
		}
		quantity = parseFloat(quantity);
		total_qty += quantity;
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

		var name = $(this).find('td .taxname').val();
		var tax_id = $(this).find('td .taxid').val();
		var tax_rate = $(this).find('td .taxrate').val();
		var calculated_tax = (tax_rate * _amount / 100);
		var obj_key = tax_rate.replace(/\./g, '_');
		if (!taxes.hasOwnProperty(obj_key)) {
			if (tax_rate != 0) {
				var tax_row = '<tr class="tax-area"><td>'+name+'</td><td id="tax_id_' + obj_key + '">'+format_money(calculated_tax)+'</td></tr>';
				$(subtotal_area).after(tax_row);
				taxes[obj_key] = calculated_tax;
			}
		} else {
			var new_val = taxes[obj_key] + calculated_tax;
			taxes[obj_key] = new_val;
			$('td#tax_id_'+obj_key+'').text(format_money(new_val));
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
	if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
		total_discount_calculated = (subtotal * discount_percent) / 100;
	} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
		total_discount_calculated = discount_fixed;
	}

	$.each(taxes, function (taxname, total_tax) {
		if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-percent')) {
			total_tax_calculated = (total_tax * discount_percent) / 100;
			total_tax = (total_tax - total_tax_calculated);
		} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'before_tax' && discount_total_type.hasClass('discount-type-fixed')) {
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
	if ((discount_percent !== '' && discount_percent != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-percent')) {
		total_discount_calculated = (total * discount_percent) / 100;
	} else if ((discount_fixed !== '' && discount_fixed != 0) && discount_type == 'after_tax' && discount_total_type.hasClass('discount-type-fixed')) {
		total_discount_calculated = discount_fixed;
	}

	additional_discount = accounting.toFixed(calculate_additional_discount(total_qty, total_quantity_default, additional_discount_default), app.options.decimal_places);

	if (!isNaN(parseFloat(additional_discount))) {
		total = total - total_discount_calculated - parseFloat(additional_discount);
	} else {
		total = total - total_discount_calculated;
	}

	adjustment = parseFloat(adjustment);

	// Check if adjustment not empty
	if (!isNaN(adjustment)) {
		total = total + adjustment;
	}

	var total_discount = 0;
	var discount_html = 0;
	if (!isNaN(parseFloat(total_discount_calculated)) && !isNaN(parseFloat(additional_discount))) {
		total_discount = parseFloat(total_discount_calculated) + parseFloat(additional_discount);
		discount_html = '-' + format_money(total_discount);
	}else if(!isNaN(parseFloat(total_discount_calculated))){
		total_discount = parseFloat(total_discount_calculated);
		discount_html = '-' + format_money(total_discount);
	}else if(!isNaN(parseFloat(additional_discount))){
		total_discount = parseFloat(additional_discount);
		discount_html = '-' + format_money(total_discount);
	}
	$('input[name="totaltax"]').val(total_tax_money);
	// Append, format to html and display
	$('.wh-total_discount').html(discount_html + hidden_input('discount_total', accounting.toFixed(total_discount, app.options.decimal_places)));
	$('.adjustment').html(format_money(adjustment));

	$('.wh-additional_discount').html('<input type="number" name="additional_discount" min="0.0" step="any" max="'+main_additional_discount+'" value="' + additional_discount + '">');

	$('.wh-subtotal').html(format_money(subtotal) + hidden_input('subtotal', accounting.toFixed(subtotal, app.options.decimal_places)) + hidden_input('total_amount', accounting.toFixed(total_money, app.options.decimal_places)));
	$('.wh-fee_for_return_order').html(format_money(fee_for_return_order));
	total = total + parseFloat(fee_for_return_order);		
	$('.wh-total').html(format_money(total) + hidden_input('total_after_discount', accounting.toFixed(total, app.options.decimal_places)));
	$('.wh-total_refund').html('-'+format_money(total));



	$(document).trigger('wh-packing-list-total-calculated');
}

function wh_sales_order_delete_item(row, itemid,parent) {
	"use strict";
	$(row).parents('tr').addClass('animated fadeOut', function () {
		setTimeout(function () {
			$(row).parents('tr').remove();
			wh_sale_order_calculate_total();
		}, 50);
	});
	if (itemid && $('input[name="isedit"]').length > 0) {
		$(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
	}
	if($('.sortable.item').length == 0){
		$('select[name="company_id"]').val('').change();
		$('input[name="email"]').val('');
		$('input[name="phonenumber"]').val('');
		$('input[name="order_number"]').val('');
		$('input[name="number_of_item"]').val('');
		$('input[name="order_total"]').val('');
		$('input[name="main_additional_discount"]').val(0);
		$('body').find('#items-warning').remove();
		$("body").find('.dt-loader').remove();
		$('#item_select').selectpicker('val', '');
	}
}


function calculate_additional_discount(current_quantity, total_quantity_default, additional_discount_default){
	"use strict";
	return current_quantity * additional_discount_default / total_quantity_default;
}

// Set the currency for accounting
function init_return_order_currency(id, callback) {
    var $accountingTemplate = $("body");

    if ($accountingTemplate.length || id) {
        var selectedCurrencyId = !id ? $accountingTemplate.find('input[name="currency"]').val() : id;
        requestGetJSON(admin_url+'misc/get_currency/' + selectedCurrencyId)
            .done(function (currency) {
                // Used for formatting money
                accounting.settings.currency.decimal = currency.decimal_separator;
                accounting.settings.currency.thousand = currency.thousand_separator;
                accounting.settings.currency.symbol = currency.symbol;
                accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';

                calculate_total();

                if(callback) {
                    callback();
                }
            });
    }
}
</script>