var billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];
var estimate_id = $('select[name="estimate_id"]').val();
console.log('manual order estimate_id', estimate_id);

(function($) {
    "use strict";
    init_order_currency();


    appValidateForm($('#order-form'), {
     customer: 'required',
     payment_methods: 'required'
 });
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    calculate_total();
    if (typeof(jQuery) != 'undefined') {
        init_item_js();
    } else {
        window.addEventListener('load', function() {
            var initItemsJsInterval = setInterval(function() {
                if (typeof(jQuery) != 'undefined') {
                    init_item_js();
                    clearInterval(initItemsJsInterval);
                }
            }, 1000);
        });
    }
    // Show quantity as change we need to change on the table QTY heading for better user experience
    $("body").on('change', 'input[name="show_quantity_as"]', function() {
        $("body").find('th.qty').html($(this).data('text'));
    });
    // Recaulciate total on these changes
    $("body").on('change', 'input[name="adjustment"],select.tax', function() {
        if (!isNaN(estimate_id)) {
            wh_calculate_total();
        }else{

            calculate_total();
        }
    });


    $('body').on('change', 'select[name="add_discount_type"], .discount-item, input[name="add_discount"], select[name="discount_type"]', function(e) {
        if (!isNaN(estimate_id)) {
            wh_calculate_total();
        }else{
            calculate_total();
        }
    });

    $('body').on('click', '.discount-total-type', function(e) {
        e.preventDefault();
        $('#discount-total-type-dropdown').find('.discount-total-type').removeClass('selected');
        $(this).addClass('selected');
        $('.discount-total-type-selected').html($(this).text());
        if ($(this).hasClass('discount-type-percent')) {
            $('.input-discount-fixed').addClass('hide').val(0);
            $('.input-discount-percent').removeClass('hide');
        } else {
            $('.input-discount-fixed').removeClass('hide');
            $('.input-discount-percent').addClass('hide').val(0);
            $('#discount_percent-error').remove();
        }
        if (!isNaN(estimate_id)) {
            wh_calculate_total();
        }else{
            calculate_total();
        }
    });
    // In case user enter discount percent but there is no discount type set
    $("body").on('change', 'input[name="discount_percent"],input[name="discount_total"]', function() {
        if ($('select[name="discount_type"]').val() === '' && $(this).val() != 0) {
            alert('You need to select discount type');
            $('html,body').animate({
                scrollTop: 0
            }, 'slow');
            $('#wrapper').highlight($('label[for="discount_type"]').text());
            setTimeout(function() {
                $('#wrapper').unhighlight();
            }, 3000);
            return false;
        }
        if ($.isNumeric($(this).val())) {
            if (!isNaN(estimate_id)) {
                wh_calculate_total();
            }else{
                calculate_total();
            }
        }
    });
    
    $("body").on('change', 'select[name="customer"]', function() {
        var val = $(this).val();
        $('address .billing_street').text('--');
        $('address .billing_city').text('--');
        $('address .billing_state').text('--');
        $('address .billing_country').text('--');
        $('address .billing_zip').text('--');

        $('address .shipping_street').text('--');
        $('address .shipping_city').text('--');
        $('address .shipping_state').text('--');
        $('address .shipping_country').text('--');
        $('address .shipping_zip').text('--');
        requestGetJSON('omni_sales/client_change_data/' + val).done(function(response) {
            if(response.status == true || response.status == 'true'){

                if(response['billing_shipping'][0]['billing_street'] != ''){
                    if(response['billing_shipping'][0]['billing_city'] != ''){
                        if(response['billing_shipping'][0]['billing_state'] != ''){
                            $('address .billing_street').text(response['billing_shipping'][0]['billing_street']);
                            $('address .billing_city').text(response['billing_shipping'][0]['billing_city']);
                            $('address .billing_state').text(response['billing_shipping'][0]['billing_state']);
                            $('address .billing_country').text(response['billing_shipping'][0]['billing_country']);
                            $('address .billing_zip').text(response['billing_shipping'][0]['billing_zip']);
                        }
                    }
                }
                if(response['billing_shipping'][0]['shipping_street'] != ''){
                    if(response['billing_shipping'][0]['shipping_city'] != ''){
                        if(response['billing_shipping'][0]['shipping_state'] != ''){
                            $('address .shipping_street').text(response['billing_shipping'][0]['shipping_street']);
                            $('address .shipping_city').text(response['billing_shipping'][0]['shipping_city']);
                            $('address .shipping_state').text(response['billing_shipping'][0]['shipping_state']);
                            $('address .shipping_country').text(response['billing_shipping'][0]['shipping_country']);
                            $('address .shipping_zip').text(response['billing_shipping'][0]['shipping_zip']);
                        }
                    }
                }
            }
            $('select[name="currency"]').val(response.currency_id).change();

        });
        $('.selectpicker').selectpicker('refresh');
    });


    $("body").on('change', 'select[name="currency"]', function () {
      init_order_currency();
  });
})(jQuery);
// Items add/edit
function manage_invoice_items(form) {
    "use strict";
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($("body").find('.accounting-template').length > 0) {
                if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
                        var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {
                    item_select.contents().filter(function() {
                        return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();
                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, site_url + 'affiliate/usercontrol/search_item');
                }
                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function(data) {
        alert_float('danger', data.responseText);
    });
    return false;
}

function init_item_js() {
    "use strict";
    // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function() {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            add_item_to_preview(itemid);
        }
    });
    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function(event) {
        $('.affect-warning').addClass('hide');
        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');
        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof(id) !== 'undefined') {
            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);
            requestGetJSON('affiliate/usercontrol/get_item_by_id/' + id).done(function(response) {
                $itemModal.find('input[name="description"]').val(response.description);
                $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                $('select[name="tax"]').selectpicker('val', response.taxid).change();
                $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $.each(response, function(column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });
                $('#custom_fields_items').html(response.custom_fields_html);
                init_selectpicker();
                init_color_pickers();
                init_datepicker();
                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });
        }
    });
    $("body").on("hidden.bs.modal", '#sales_item_modal', function(event) {
        $('#item_select').selectpicker('val', '');
    });
    validate_item_form();
}

function validate_item_form() {
    "use strict";
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
        description: 'required',
        rate: {
            required: true,
        }
    }, manage_invoice_items);
}

function ReplaceNumberWithCommas(yourNumber) {
    //Seperates the components of the number
    var n= yourNumber.toString().split(".");
    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}

// Add item to preview
function add_item_to_preview(id) {
    "use strict";
    requestGetJSON('omni_sales/get_item_by_id/' + id).done(function(response) {
        clear_item_preview_values();
        $('.main input[name="product_id"]').val(response.id);
        $('.main input[name="group_id"]').val(response.group_id);
        $('.main textarea[name="description"]').val(response.description);
        if(response.long_description != null){
            $('.main textarea[name="long_description"]').val(response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
        }
        else{
            $('.main textarea[name="long_description"]').val('');
        }
        $('.main input[name="quantity"]').val(1);
        $('.main input[name="tax"]').val(response.taxname);
        $('.main input[name="taxid"]').val(response.tax);
        $('.main input[name="taxrate"]').val(response.taxrate);
        $('.main input[name="rate"]').val(response.rate);
        $('.main .quantity .unit').text(response.unitname);
    });
}
// General helper function for $.get ajax requests
function requestGet(uri, params) {
    "use strict";
    params = typeof(params) == 'undefined' ? {} : params;
    var options = {
        type: 'GET',
        url: uri.indexOf(site_url) > -1 ? uri : site_url + uri
    };
    return $.ajax($.extend({}, options, params));
}
// General helper function for $.get ajax requests with dataType JSON
function requestGetJSON(uri, params) {
    "use strict";
    params = typeof(params) == 'undefined' ? {} : params;
    params.dataType = 'json';
    return requestGet(uri, params);
}
// Clear the items added to preview
function clear_item_preview_values(default_taxes) {
    "use strict";
    // Get the last taxes applied to be available for the next item
    var last_taxes_applied = $('table.items tbody').find('tr:last-child').find('select').selectpicker('val');
    var previewArea = $('.main');
    previewArea.find('textarea').val(''); // includes cf
    previewArea.find('td.custom_field input[type="checkbox"]').prop('checked', false); // cf
    previewArea.find('td.custom_field input:not(:checkbox):not(:hidden)').val(''); // cf // not hidden for chkbox hidden helpers
    previewArea.find('td.custom_field select').selectpicker('val', ''); // cf
    previewArea.find('input[name="quantity"]').val(1);
    previewArea.find('select.tax').selectpicker('val', last_taxes_applied);
    previewArea.find('input[name="rate"]').val('');
    previewArea.find('input[name="unit"]').val('');
    $('input[name="task_id"]').val('');
    $('input[name="expense_id"]').val('');
}

function _set_item_preview_custom_fields_array(custom_fields) {
    "use strict";
    var cf_act_as_inputs = ['input', 'number', 'date_picker', 'date_picker_time', 'colorpicker'];
    for (var i = 0; i < custom_fields.length; i++) {
        var cf = custom_fields[i];
        if ($.inArray(cf.type, cf_act_as_inputs) > -1) {
            var f = $('tr.main td[data-id="' + cf.id + '"] input');
            // trigger change eq. for colorpicker
            f.val(cf.value).trigger('change');
        } else if (cf.type == 'textarea') {
            $('tr.main td[data-id="' + cf.id + '"] textarea').val(cf.value);
        } else if (cf.type == 'select' || cf.type == 'multiselect') {
            if (!empty(cf.value)) {
                var selected = cf.value.split(',');
                selected = selected.map(function(e) {
                    return e.trim();
                });
                $('tr.main td[data-id="' + cf.id + '"] select').selectpicker('val', selected);
            }
        } else if (cf.type == 'checkbox') {
            if (!empty(cf.value)) {
                var selected = cf.value.split(',');
                selected = selected.map(function(e) {
                    return e.trim();
                });
                $.each(selected, function(i, e) {
                    $('tr.main td[data-id="' + cf.id + '"] input[type="checkbox"][value="' + e + '"]').prop('checked', true);
                });
            }
        }
    }
}
// Append the added items to the preview to the table as items
function add_item_to_table(data, itemid, merge_invoice, bill_expense) {
    "use strict";
    var description = $('.main textarea[name="description"]').val();
    var long_description = $('.main textarea[name="long_description"]').val();
    if(description.trim()){
    // If not custom data passed get from the preview
    data = typeof(data) == 'undefined' || data == 'undefined' ? get_item_preview_values() : data;
    if (data.description === "" && data.long_description === "" && data.rate === "") {
        return;
    }
    var table_row = '';
    var item_key = $("body").find('tbody .item').length + 1;
    table_row += '<tr class="sortable item" data-merge-invoice="' + merge_invoice + '" data-bill-expense="' + bill_expense + '">';
    table_row += '<td class="dragger">';
    // Check if quantity is number
    if (isNaN(data.qty)) {
        data.qty = 1;
    }
    // Check if rate is number
    if (data.rate === '' || isNaN(data.rate)) {
        data.rate = 0;
    }
    var amount = data.rate * data.qty;
    var tax_name = 'newitems[' + item_key + '][taxname][]';
    $("body").append('<div class="dt-loader"></div>');
    var regex = /<br[^>]*>/gi;

        // order input
        table_row += '<input type="hidden" class="order" name="newitems[' + item_key + '][order]">';
        table_row += '</td>';

        table_row += '<td class="bold description"><input type="hidden" name="newitems[' + item_key + '][id]" value="">';
        table_row += '<input type="hidden" name="newitems[' + item_key + '][product_id]" value="' + data.itemid + '">';
        table_row += '<textarea name="newitems[' + item_key + '][description]" class="form-control" rows="5">' + data.description + '</textarea></td>';

        table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" class="form-control item_long_description" rows="5">' + data.long_description.replace(regex, "\n") + '</textarea></td>';

        table_row += '<td><div class="form-group">';
        table_row += '<div class="input-group quantity">';
        table_row += '<input type="number" class="form-control" data-quantity onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][qty]" min="0" value="' + data.qty + '">';
        table_row += '<span class="input-group-addon unit">' + data.unit + '</span>';
        table_row += '</div>';
        table_row += '</div></td>';

        if (!data.unit || typeof(data.unit) == 'undefined') {
            data.unit = '';
        }

        table_row += '<input type="text" name="newitems[' + item_key + '][unit]" class="form-control input-transparent text-right" value="' + data.unit + '">';
        table_row += '</td>';
        table_row += '<td class="rate"><input type="number" data-toggle="tooltip" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][rate]" value="' + data.rate + '" class="form-control"></td>';

        table_row += '<td><input type="text" class="form-control taxname" readonly name="newitems[' + item_key + '][taxname]" value="' + data.taxname + '" />';
        table_row += '<input type="hidden" class="taxid" name="newitems[' + item_key + '][taxid]" value="' + data.taxid + '" />';
        table_row += '<input type="hidden" class="taxrate" name="newitems[' + item_key + '][taxrate]" value="' + data.taxrate + '" /></td>';

        table_row += '<td>';
        table_row += '<input name="newitems[' + item_key + '][discount]" class="form-control discount-item" value="">';
        table_row += '</td>';

        table_row += '<td class="amount" align="right" data-id="'+data.itemid+'" data-gid="'+data.group_itemid+'">';
        table_row += '<div class="price_w1 hide"><span class="old_price">' + format_money(amount, true) + '</span></div>';
        table_row += '<div class="price_w2 hide d-grid"><span class="new_price"></span><br><span class="line-throught old_price">' + format_money(amount, true) + '</span></div>';
        table_row += '</td>';

        table_row += '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' + itemid + '); return false;"><i class="fa fa-trash"></i></a></td>';
        table_row += '</tr>';

        $('select.tax').removeAttr('multiple'); 

        $('table.items tbody').append(table_row);
        $(document).trigger({
            type: "item-added-to-table",
            data: data,
            row: table_row
        });
        setTimeout(function() {
            calculate_total();
        }, 15);
        $('.selectpicker').selectpicker('refresh');
        $('.main textarea[name="description"]').val('');
        $('.main textarea[name="long_description"]').val('');
        $('.main input[name="product_id"]').val('');
        $('body').find('#items-warning').remove();
        $("body").find('.dt-loader').remove();
        return true;
    }
    else{
        alert_float('warning', 'Please select a item');
    }
}
// Reoder the items in table edit for estimate and invoices
function reorder_items() {
    "use strict";
    var rows = $('.table.has-calculations tbody tr.item');
    var i = 1;
    $.each(rows, function() {
        $(this).find('input.order').val(i);
        i++;
    });
}
// Get the preview main values
function get_item_preview_values() {
    "use strict";
    var response = {};
    response.itemid = $('.main input[name="product_id"]').val();
    response.group_itemid = $('.main input[name="group_id"]').val();
    response.description = $('.main textarea[name="description"]').val();
    response.long_description = $('.main textarea[name="long_description"]').val();
    response.qty = $('.main input[name="quantity"]').val();
    response.taxname = $('.main input[name="tax"]').val();
    response.taxid = $('.main input[name="taxid"]').val();
    response.taxrate = $('.main input[name="taxrate"]').val();
    response.rate = $('.main input[name="rate"]').val();
    response.unit = $('.main .quantity .unit').text();
    return response;
}
// Get taxes dropdown selectpicker template / Causing problems with ajax becuase is fetching from server
function get_taxes_dropdown_template(name, taxname) {
    "use strict";
    jQuery.ajaxSetup({
        async: false
    });
    var d = $.post(site_url + 'omni_sales/get_taxes_dropdown_template/', {
        name: name,
        taxname: taxname,
        csrf_token_name: $('#csrf_token_name').val()
    });
    jQuery.ajaxSetup({
        async: true
    });
    return d;
}
// Calculate invoice total - NOT RECOMENDING EDIT THIS FUNCTION BECUASE IS VERY SENSITIVE
function calculate_total() {
    "use strict";
    if ($('body').hasClass('no-calculate-total')) {
        return false;
    }
    $('.tax-area').remove();
    $('td .discount-item').removeAttr('readonly');
    $('.add-discount-fr').removeClass('hide');
    $('.discount-title-fr').removeClass('col-md-12').addClass('col-md-7');
    $('.discount-type-fr').removeClass('hide');
    $('.discount-column').removeClass('hide');
    $('.discount-column').removeClass('hide');
    var add_discount_type = $('select[name="add_discount_type"]').val();
    if(parseInt(add_discount_type) == 2){
        $('.discount-column').addClass('hide');
    }
    var shipping_fee = $('input[name="shipping"]').val();
    if(shipping_fee == ''){
        shipping_fee = 0;
        $('input[name="shipping"]').val(0);
    }
    var cal_subtotal = 0;
    var total_discount_item = 0;
    var discount_by_trade = 0;    
    var calculated_tax;
    var discount_type = $('select[name="discount_type"]').val();
    var taxes = {};

    var rows = $('.table.has-calculations tbody tr.item');
    var item_taxrate = $('.item .taxrate');
    var item_taxes = $('.item .taxname');
    var item_taxid = $('.item .taxid');

    $.each(rows, function(i, row_item) {
        var quantity = $(this).find('[data-quantity]').val();
        if (quantity === '') {
            quantity = 1;
        }
        _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
        _amount = parseFloat(_amount);
        cal_subtotal += _amount;
        var name = item_taxes.eq(i).val();
        var tax_id = item_taxid.eq(i).val();
        var tax_rate = item_taxrate.eq(i).val();
        calculated_tax = (tax_rate * _amount / 100);
        var obj_key = tax_rate.replace(/\./g, '_');
        if (!taxes.hasOwnProperty(obj_key)) {
            if (tax_rate != 0) {
                var tax_row = '<tr class="tax-area"><td>'+name+'</td><td id="tax_id_' + obj_key + '">'+format_money(calculated_tax)+'</td></tr>';
                $('#discount_area').after(tax_row);
                taxes[obj_key] = calculated_tax;
            }
        } else {
            var new_val = taxes[obj_key] + calculated_tax;
            taxes[obj_key] = new_val;
            $('td#tax_id_'+obj_key+'').text(format_money(new_val));
        }
    });

    if(discount_type == 'after_tax'){
      $.each(taxes, function(id, total_tax) {
        cal_subtotal += total_tax;
      });
    }
    if(list_discount != undefined && list_discount['minimum_order_value'] != undefined && (cal_subtotal >= list_discount['minimum_order_value'])){
        if(list_discount['item'] == ""){
            discount_by_trade++;
            if (parseInt(list_discount['formal']) == 1) {
                total_discount_item = parseFloat(list_discount['discount']) * cal_subtotal / 100;
                $('input[name="discount"]').val(total_discount_item);
            } else {
                total_discount_item = parseFloat(list_discount['discount']);
                $('input[name="discount"]').val(total_discount_item);
            }
        } 
    }
    else{
        var discount_value = $('input[name="add_discount"]').val();
        if(discount_value != '' && parseFloat(discount_value) > 0){
            $('td .discount-item').attr('readonly', true);
            if (parseInt(add_discount_type) == 1) {
                total_discount_item = parseFloat(discount_value) * cal_subtotal / 100;
                $('input[name="discount"]').val(total_discount_item);
            } else {
                total_discount_item = parseFloat(discount_value);
                $('input[name="discount"]').val(total_discount_item);
            }
        }
    }



    $('.price_w2').addClass('hide');
    $('.price_w1').removeClass('hide'); 
    var subtotal = 0;
    var quantity = 1;
    var taxrate,
    _amount,
    _tax_name,
    taxes_rows = [],
    total = 0,
    discount_area = $('#discount_area'),
    adjustment = $('input[name="adjustment"]').val(),
    discount_percent = $('input[name="discount_percent"]').val(),
    discount_fixed = $('input[name="discount_total"]').val(),
    discount_total_type = $('.discount-total-type.selected');


    $.each(rows, function(i, row_item) {
        quantity = $(this).find('[data-quantity]').val();
        if (quantity === '') {
            quantity = 1;
            $(this).find('[data-quantity]').val(1);
        }
        _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
        _amount = parseFloat(_amount);
        var discount_value = $(this).find('.discount-item').val();
        var amount_data = check_trade_discount_item($(this).find('td.amount'), cal_subtotal, _amount, quantity, discount_value);
        _amount = amount_data.old_amount;
        total_discount_item += amount_data.discount_item;
        if(amount_data.discount_by != 'admin'){
            $(this).find('.discount-item').val(amount_data.discount_value);
            discount_by_trade++;
        }
        subtotal += _amount;  
    });
    total = (total + subtotal);
   
    total = total - total_discount_item;
    $.each(taxes, function(id, total_tax) {
        total += total_tax;
    });
    adjustment = parseFloat(adjustment);
    // Check if adjustment not empty
    if (!isNaN(adjustment)) {
        total = total + adjustment;
    };
    var discount_html = '-' + format_money(total_discount_item);
    total+= parseFloat(shipping_fee);
    // Append, format to html and display
    $('input[name="discount"]').val(total_discount_item);
    $('.discount-total').html(discount_html);
    $('.adjustment').html(format_money(adjustment));
    $('.subtotal').html(format_money(subtotal) + hidden_input('subtotal', accounting.toFixed(subtotal, app.options.decimal_places)));
    $('.shiping_fee').html(format_money(shipping_fee));
    $('.total').html(format_money(total) + hidden_input('total', accounting.toFixed(total, app.options.decimal_places)));
    if(discount_by_trade > 0){
        $('td .discount-item').attr('readonly', true);
        $('.add-discount-fr').addClass('hide');
        $('input[name="add_discount"]').val('0');
        $('.discount-title-fr').addClass('col-md-12').removeClass('col-md-7');
        $('.discount-type-fr').addClass('hide');
        $('select[name="add_discount_type"]').val('1');
    }
}
// Clear billing and shipping inputs for invoice,estimate etc...
function clear_billing_and_shipping_details() {
    "use strict";
    for (var f in billingAndShippingFields) {
        if (billingAndShippingFields[f].indexOf('country') > -1) {
            $('select[name="' + billingAndShippingFields[f] + '"]').selectpicker('val', '');
        } else {
            $('input[name="' + billingAndShippingFields[f] + '"]').val('');
            $('textarea[name="' + billingAndShippingFields[f] + '"]').val('');
        }
        if (billingAndShippingFields[f] == 'billing_country') {
            $('input[name="include_shipping"]').prop("checked", false);
            $('input[name="include_shipping"]').change();
        }
    }
    init_billing_and_shipping_details();
}
// Init billing and shipping details for invoice, estimate etc...
function init_billing_and_shipping_details() {
    "use strict";
    var _f;
    var include_shipping = $('input[name="include_shipping"]').prop('checked');
    // console.log(billingAndShippingFields);
    for (var f in billingAndShippingFields) {
        _f = '';
        if (billingAndShippingFields[f].indexOf('country') > -1) {
            _f = $("#" + billingAndShippingFields[f] + " option:selected").data('subtext');
        } else if (billingAndShippingFields[f].indexOf('shipping_street') > -1 || billingAndShippingFields[f].indexOf('billing_street') > -1) {
            if ($('textarea[name="' + billingAndShippingFields[f] + '"]').length) {
                _f = $('textarea[name="' + billingAndShippingFields[f] + '"]').val().replace(/(?:\r\n|\r|\n)/g, "<br />");
            }
        } else {
            _f = $('input[name="' + billingAndShippingFields[f] + '"]').val();
        }
        if (billingAndShippingFields[f].indexOf('shipping') > -1) {
            if (!include_shipping) {
                _f = '';
            }
        }
        if (typeof(_f) == 'undefined') {
            _f = '';
        }
        _f = (_f !== '' ? _f : '--');
        $('.' + billingAndShippingFields[f]).html(_f);
    }
    $('#billing_and_shipping_details').modal('hide');
}
// Deletes invoice items
function delete_item(row, itemid) {
    "use strict";
    $(row).parents('tr').addClass('animated fadeOut');
    
    setTimeout(function() {
        $(row).parents('tr').remove();
        calculate_total();
    }, 50);
    
    // If is edit we need to add to input removed_items to track activity
    if ($('input[name="isedit"]').length > 0) {
        $('#removed-items').append(hidden_input('removed_items[]', itemid));
    }
}

var list_discount = {};
function get_trade_discount(){
  "use strict";
  var id = $('#customer').val();
  if(id != ''){
     $.ajax({
       url: admin_url+"omni_sales/get_trade_discount",
       type: "post",
       data: {'id':id, 'channel': 4},
       success: function(){
       },
       error:function(){
        alert_float('danger', 'Failure');
    }
}).done(function(response) {
    response = JSON.parse(response);
    $('td .discount-item').removeAttr('readonly');
    $('td .discount-item').val('');

    list_discount = {};
    if(response[0].length >= 1){
      for(var i = 0; i < response[0].length;i++){
        list_discount = {item:response[0][i]['items'], formal:response[0][i]['formal'],group_list:response[0][i]['group_items'], discount:response[0][i]['discount'], voucher:response[0][i]['voucher'], minimum_order_value:response[0][i]['minimum_order_value']};
    }
    $('.input-discount-fixed').val(0);
    $('.input-discount-percent').val(0);
}  
    if (!isNaN(estimate_id)) {
        wh_calculate_total();
    }else{            
        calculate_total();
    }
}); 
} 
}


    function total_cart(){  
        "use strict";
        var list_qty = $('.tab-pane.active').find('input[name="list_qty_product"]').val();
        var list_price = $('.tab-pane.active').find('input[name="list_price_product"]').val();
        var list_price_discount = $('.tab-pane.active').find('input[name="list_price_discount_product"]').val();
        var list_price_tax = $('.tab-pane.active').find('input[name="list_price_tax"]').val();
        var discount_voucher = $('.tab-pane.active').find('input[name="discount_voucher"]').val();
        var discount_type = $('.tab-pane.active').find('input[name="discount_type"]').val();                  
        var discount_auto = $('.tab-pane.active').find('input[name="discount_auto"]').val(); 
        var other_discount = $('.tab-pane.active').find('input[name="other_discount"]').val(); 


        var qty = JSON.parse('['+list_qty+']');
        var prices = JSON.parse('['+list_price+']');
        var price_discount = JSON.parse('['+list_price_discount+']');  
        var total_tax = JSON.parse('['+list_price_tax+']');

        var total = 0;
        var discount = 0;  
        var tax = 0;
        $.each(qty, function( key, value ) {
            total += parseFloat(value)*prices[key];
            tax += parseFloat(value)*parseFloat(total_tax[key]);
        });
        var new_discount_customer = 0;
        var new_discount_voucher = 0;
        if(discount_type == 1){
            new_discount_voucher = total * discount_voucher / 100;
        }
        if(discount_type == 2){
            new_discount_voucher = discount_voucher;      
        }
        var discount_client = get_discount_client(total);
        discount +=round(parseFloat(new_discount_voucher) + parseFloat(new_discount_customer) + parseFloat(discount_client)) + parseFloat(other_discount);

        $('.tab-pane.active').find('input[name="discount_total"]').val(discount);
        $('.tab-pane.active').find('.discount-total').text('-'+numberWithCommas(discount));
        total = round(total);
        $('.tab-pane.active').find('.subtotal').text(numberWithCommas(total));
        var total_s = round(total-discount+tax);
        $('.tab-pane.active').find('.total').text(numberWithCommas(total_s));
        $('.tab-pane.active').find('.promotions_tax_price').text(numberWithCommas(round(tax)));
        $('.tab-pane.active').find('input[name="sub_total_cart"]').val(total);
        $('.tab-pane.active').find('input[name="total_cart"]').val(total_s);   
        $('.tab-pane.active').find('input[name="tax"]').val(tax);        
        $('.tab-pane.active').find('input[name="discount_auto_event"]').val(new_discount_customer);    
        $('.tab-pane.active').find('input[name="discount_voucher_event"]').val(discount_voucher); 
        var list_customers_pay = $('input[name="customers_pay[]"]');
        var total_customer_pay = 0;
        for(let  i = 0; i < list_customers_pay.length; i++){
            var val = list_customers_pay.eq(i).val().replace(new RegExp(',', 'g'),"");
            if(val != '' && val != 0){
                total_customer_pay += parseFloat(val.trim());
            }
        }
        if(total_customer_pay != 0){      
            var total = $('.tab-pane.active').find('input[name="total_cart"]').val();  
            $('.tab-pane.active').find('input[name="amount_returned"]').val(numberWithCommas(round(parseFloat(total_customer_pay) - parseFloat(total)))); 
        }
    }


    function check_trade_discount_item(el, subtotal, amount, quantity, discount_value){
        "use strict";
        var obj = $(el);    
        obj.find('.price_w2').addClass('hide');
        obj.find('.price_w1').removeClass('hide');
        obj.find('.old_price').text(ReplaceNumberWithCommas(amount));

        var old_amount = amount;
        var discount_by = 'admin';
        var discount_type = '';
        var discount_item = 0;
        if(list_discount != undefined && list_discount['minimum_order_value'] != undefined && (subtotal >= list_discount['minimum_order_value'])){
            var array = list_discount['item'].split(',');
            var array_group = list_discount['group_list'].split(',');
            var id = obj.data('id').toString();
            var gid = obj.data('gid').toString();    
            if(array.includes(id) || array_group.includes(gid)){
                discount_by = 'trade_discount';
                discount_type = parseInt(list_discount['formal']);   
                discount_value = list_discount['discount'];         
                if(parseInt(discount_type) == 1){
                    $('.discount-column').removeClass('hide');
                    discount_item = parseFloat(amount * list_discount['discount'] / 100);
                    amount = amount - discount_item; 
                    if(amount < 0){
                        amount = 0;
                    }
                    obj.find('.price_w2').removeClass('hide'); 
                    obj.find('.price_w2 .new_price').text(ReplaceNumberWithCommas(amount)); 
                    obj.find('.price_w1').addClass('hide');
                    obj.find('.price_w2 .old_price').text(ReplaceNumberWithCommas(old_amount)); 
                }
                else{
                    $('.discount-column').addClass('hide');
                    discount_item = (list_discount['discount'] * quantity);
                    amount = amount - discount_item;
                    if(amount < 0){
                        amount = 0;
                    }                                 
                    obj.find('.price_w2').removeClass('hide'); 
                    obj.find('.price_w2 .new_price').text(ReplaceNumberWithCommas(amount)); 
                    obj.find('.price_w1').addClass('hide');
                    obj.find('.price_w2 .old_price').text(ReplaceNumberWithCommas(old_amount)); 
                }
            }
            else{ 
                if(discount_value != '' && parseFloat(discount_value) > 0){
                    $('.add-discount-fr').addClass('hide');
                    $('.discount-title-fr').removeClass('col-md-7').addClass('col-md-10');
                    return discount_by_admin(el, amount, discount_value, quantity);
                }
            }
        }
        else{
            if(discount_value != '' && parseFloat(discount_value) > 0){
                $('.add-discount-fr').addClass('hide');
                $('.discount-title-fr').removeClass('col-md-7').addClass('col-md-10');
                return discount_by_admin(el, amount, discount_value, quantity);
            }
        }
        return { 'amount':amount, 'old_amount': old_amount, 'discount_item': discount_item , 'discount_value': discount_value , 'discount_by': discount_by};
    }

    function discount_by_admin(el, amount, discount_value, quantity){
        "use strict";
        var discount_item = 0;
        var old_amount = amount;
        var obj = $(el);    
        var discount_type = $('select[name="add_discount_type"]').val();
        if(discount_type == 1){
            discount_item = parseFloat(amount * discount_value / 100);
            amount = amount - discount_item; 
            if(amount < 0){
                amount = 0;
            }
            obj.find('.price_w2').removeClass('hide'); 
            obj.find('.price_w2 .new_price').text(ReplaceNumberWithCommas(amount)); 
            obj.find('.price_w1').addClass('hide');
            obj.find('.price_w2 .old_price').text(ReplaceNumberWithCommas(old_amount)); 
        }
        else{
            discount_item = (discount_value * quantity);
            amount = amount - discount_item;
            if(amount < 0){
                amount = 0;
            }                                 
            obj.find('.price_w2').removeClass('hide'); 
            obj.find('.price_w2 .new_price').text(ReplaceNumberWithCommas(amount)); 
            obj.find('.price_w1').addClass('hide');
            obj.find('.price_w2 .old_price').text(ReplaceNumberWithCommas(old_amount)); 
        }
        return { 'amount':amount, 'old_amount': old_amount, 'discount_item': discount_item , 'discount_by': 'admin'};
    }


// Set the currency for accounting
function init_order_currency(id, callback) {
    var $accountingTemplate = $("body");

    if ($accountingTemplate.length || id) {
        var selectedCurrencyId = !id ? $accountingTemplate.find('select[name="currency"]').val() : id;
        if(selectedCurrencyId != ''){

            requestGetJSON(admin_url+'misc/get_currency/' + selectedCurrencyId)
            .done(function (currency) {
                // Used for formatting money
                accounting.settings.currency.decimal = currency.decimal_separator;
                accounting.settings.currency.thousand = currency.thousand_separator;
                accounting.settings.currency.symbol = currency.symbol;
                accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
                if (!isNaN(estimate_id) && estimate_id != '' ) {

                    wh_calculate_total();
                }else{
                    calculate_total();
                }

                if(callback) {
                    callback();
                }
            });
        }
    }
}

