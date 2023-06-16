<script>
var billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];	
(function(){
   "use strict";

   	init_pi_currency();

	$("body").on('click', '#include_shipping', function () {
        var $sd = $('#shipping_details');
        $(this).prop('checked') === true ? $sd.removeClass('hide') : $sd.addClass('hide');
    });

    // Init the billing and shipping details in the field - estimates and invoices
    $("body").on('click', '.save-shipping-billing', function (e) {
        init_billing_and_shipping_details();
    });


    $("body").on('click', '#get_shipping_from_customer_profile', function (e) {
        e.preventDefault();
        var include_shipping = $('#include_shipping');
        if (include_shipping.prop('checked') === false) {
            include_shipping.prop('checked', true);
            $('#shipping_details').removeClass('hide');
        }
        var clientid = $('#clientid').val();
        if (clientid === '') {
            return;
        }
        requestGetJSON('sales_agent/portal/get_customer_billing_and_shipping_details/' + clientid).done(function (response) {
            $('textarea[name="shipping_street"]').val(response[0]['shipping_street']);
            $('input[name="shipping_city"]').val(response[0]['shipping_city']);
            $('input[name="shipping_state"]').val(response[0]['shipping_state']);
            $('input[name="shipping_zip"]').val(response[0]['shipping_zip']);
            $('select[name="shipping_country"]').selectpicker('val', response[0]['shipping_country']);
        });
    });

    $("body").on('change', '#clientid', function () {
    	var val = $(this).val();
      
        clear_billing_and_shipping_details();
        if (!val) {
            $('#merge').empty();
            $('#expenses_to_bill').empty();
            $('#invoice_top_info').addClass('hide');
            projectsWrapper.addClass('hide');
            return false;
        }

        var currentInvoiceID = $("body").find('input[name="merge_current_invoice"]').val();
        currentInvoiceID = typeof (currentInvoiceID) == 'undefined' ? '' : currentInvoiceID;

        requestGetJSON('sales_agent/portal/client_change_data/' + val + '/' + currentInvoiceID).done(function (response) {
            for (var f in billingAndShippingFields) {
                if (billingAndShippingFields[f].indexOf('billing') > -1) {
                    if (billingAndShippingFields[f].indexOf('country') > -1) {
                        $('select[name="' + billingAndShippingFields[f] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[f]]);
                    } else {
                        if (billingAndShippingFields[f].indexOf('billing_street') > -1) {
                            $('textarea[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
                        } else {
                            $('input[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
                        }
                    }
                }
            }

            if (!empty(response['billing_shipping'][0]['shipping_street'])) {
                $('input[name="include_shipping"]').prop("checked", true).change();
            }

            for (var fsd in billingAndShippingFields) {
                if (billingAndShippingFields[fsd].indexOf('shipping') > -1) {
                    if (billingAndShippingFields[fsd].indexOf('country') > -1) {
                        $('select[name="' + billingAndShippingFields[fsd] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[fsd]]);
                    } else {
                        if (billingAndShippingFields[fsd].indexOf('shipping_street') > -1) {
                            $('textarea[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
                        } else {
                            $('input[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
                        }
                    }
                }
            }

            init_billing_and_shipping_details();
    
        });
    });


    pur_calculate_total();

    $("body").on('change', 'select[name="item_select"]', function () {
      var itemid = $(this).selectpicker('val');
      var program_id = $('select[name="disount_program"]').val();
      if (itemid != '') {
        pur_add_item_to_preview(itemid, program_id);
      }
    });


   	$("body").on('change', 'select.taxes', function () {
      pur_calculate_total();
    });

})(jQuery);

var lastAddedItemKey = null;

function init_billing_and_shipping_details() {
  "use strict"; 
    var _f;
    var include_shipping = $('input[name="include_shipping"]').prop('checked');

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
        if (typeof (_f) == 'undefined') {
            _f = '';
        }
        _f = (_f !== '' ? _f : '--');
        $('.' + billingAndShippingFields[f]).html(_f);
    }
    $('#billing_and_shipping_details').modal('hide');
}


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

function pur_calculate_total(from_discount_money){
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
    item_total_payment,
    rows = $('.table.has-calculations tbody tr.item'),
    subtotal_area = $('#subtotal'),
    discount_area = $('#discount_area'),
    adjustment = $('input[name="adjustment"]').val(),
    // discount_percent = $('input[name="discount_percent"]').val(),
    discount_percent = 'before_tax',
    discount_fixed = $('input[name="discount_total"]').val(),
    discount_total_type = $('.discount-total-type.selected'),
    discount_type = $('select[name="discount_type"]').val(),
    additional_discount = $('input[name="additional_discount"]').val(),
    add_discount_type = $('select[name="add_discount_type"]').val();

    var shipping_fee = $('input[name="shipping_fee"]').val();
    if(shipping_fee == ''){
      shipping_fee = 0;
      $('input[name="shipping_fee"]').val(0);
    }

  $('.wh-tax-area').remove();

    $.each(rows, function () {
    var item_discount = 0;
    var item_discount_money = 0;
    var item_discount_from_percent = 0;
    var item_discount_percent = 0;
    var item_tax = 0,
        item_amount  = 0;

    quantity = $(this).find('[data-quantity]').val();
    if (quantity === '') {
      quantity = 1;
      $(this).find('[data-quantity]').val(1);
    }
    item_discount_percent = $(this).find('td.discount input').val();
    item_discount_money = $(this).find('td.discount_money input').val();

    if (isNaN(item_discount_percent) || item_discount_percent == '') {
      item_discount_percent = 0;
    }

    if (isNaN(item_discount_money) || item_discount_money == '') {
      item_discount_money = 0;
    }

    if(from_discount_money == 1 && item_discount_money > 0){
      $(this).find('td.discount input').val('');
    }

    _amount = accounting.toFixed($(this).find('td.rate input').val() * quantity, app.options.decimal_places);
    item_amount = _amount;
    _amount = parseFloat(_amount);

    $(this).find('td.into_money').html(format_money(_amount));
    $(this).find('td._into_money input').val(_amount);

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
            $(subtotal_area).after(tax_row);
            taxes[taxname] = calculated_tax;
          }
        } else {
                    // Increment total from this tax
                    taxes[taxname] = taxes[taxname] += calculated_tax;
                }
            });
    }
    var after_tax = _amount + item_tax;

    $(this).find('td._total').html(format_money(after_tax));
    $(this).find('td._total_after_tax input').val(after_tax);

    $(this).find('td.tax_value input').val(item_tax);
      //Discount of item
      if( item_discount_percent > 0 && from_discount_money != 1){
        item_discount_from_percent = (parseFloat(item_amount) + parseFloat(item_tax) ) * parseFloat(item_discount_percent) / 100;
        if(item_discount_from_percent != item_discount_money){
          item_discount_money = item_discount_from_percent;
        }
      }

      if( item_discount_money > 0){
        item_discount = parseFloat(item_discount_money);
      }

      item_total_payment = parseFloat(item_amount) + parseFloat(item_tax) - parseFloat(item_discount);
      // Append value to item
      total_discount_calculated += item_discount;
      $(this).find('td.discount_money input').val(item_discount);
      $(this).find('td.total_after_discount input').val(item_total_payment);

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

  var order_discount_percent = $('input[name="order_discount"]').val();
  var order_discount_percent_val = 0;
  if(order_discount_percent != ''){
    if(add_discount_type == 'percent'){
      order_discount_percent_val = (total * order_discount_percent) / 100;
    }else if(add_discount_type == 'amount'){
      order_discount_percent_val = parseFloat(order_discount_percent);
    }
  }

  total_discount_calculated = total_discount_calculated + order_discount_percent_val;

  total = total - total_discount_calculated - parseFloat(additional_discount);
  adjustment = parseFloat(adjustment);

  // Check if adjustment not empty
  if (!isNaN(adjustment)) {
    total = total + adjustment;
  }

  total+= parseFloat(shipping_fee);

  var discount_html = '-' + format_money(parseFloat(total_discount_calculated)+ parseFloat(additional_discount));
    $('input[name="discount_total"]').val(accounting.toFixed(total_discount_calculated, app.options.decimal_places));
    
  // Append, format to html and display
  $('.shiping_fee').html(format_money(shipping_fee));
  $('.order_discount_value').html(format_money(order_discount_percent_val));
  $('.wh-total_discount').html(discount_html + hidden_input('dc_total', accounting.toFixed(order_discount_percent_val, app.options.decimal_places))  );
  $('.adjustment').html(format_money(adjustment));
  $('.wh-subtotal').html(format_money(subtotal) + hidden_input('total_mn', accounting.toFixed(subtotal, app.options.decimal_places)));
  $('.wh-total').html(format_money(total) + hidden_input('grand_total', accounting.toFixed(total, app.options.decimal_places)));

  $(document).trigger('purchase-quotation-total-calculated');

}

// Set the currency for accounting
function init_pi_currency(id, callback) {
  "use strict"; 
    var $accountingTemplate = $("body").find('.accounting-template');

    if ($accountingTemplate.length || id) {
        var selectedCurrencyId = !id ? $accountingTemplate.find('select[name="currency"]').val() : id;

        requestGetJSON('sales_agent/portal/get_currency/' + selectedCurrencyId)
            .done(function (currency) {
                // Used for formatting money
                accounting.settings.currency.decimal = currency.decimal_separator;
                accounting.settings.currency.thousand = currency.thousand_separator;
                accounting.settings.currency.symbol = currency.symbol;
                accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';

                pur_calculate_total();

                if(callback) {
                    callback();
                }
            });
    }
}

function pur_add_item_to_preview(id) {
  "use strict";
  var currency_rate = $('input[name="currency_rate"]').val();
  requestGetJSON('sales_agent/portal/get_item_by_id_sale_inv/' + id +'/'+currency_rate).done(function (response) {
    pur_clear_item_preview_values();

    $('.main input[name="item_code"]').val(response.itemid);
    $('.main textarea[name="item_name"]').val(response.code_description);
    $('.main textarea[name="description"]').val(response.long_description);
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
      $rateInputPreview.val(response.purchase_price);
    } else {
      var itemCurrencyRate = response['rate_currency_' + selectedCurrency];
      if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
        $rateInputPreview.val(response.purchase_price);
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

function pur_add_item_to_table(data, itemid) {
  "use strict";

  data = typeof (data) == 'undefined' || data == 'undefined' ? pur_get_item_preview_values() : data;

  if (data.quantity == ""  ) {
    
    return;
  }
  var currency_rate = $('input[name="currency_rate"]').val();
  var to_currency = $('select[name="currency"]').val();
  var table_row = '';
  var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('.invoice-items-table tbody .item').length + 1;
  lastAddedItemKey = item_key;
  $("body").append('<div class="dt-loader"></div>');
  pur_get_item_row_template('newitems[' + item_key + ']',data.item_name, data.description, data.quantity, data.unit_name, data.unit_price, data.taxname, data.item_code, data.unit_id, data.tax_rate, data.discount, itemid, currency_rate, to_currency).done(function(output){
    table_row += output;

    $('.invoice-item table.invoice-items-table.items tbody').append(table_row);

    setTimeout(function () {
      pur_calculate_total();
    }, 15);
    init_selectpicker();
    pur_reorder_items('.invoice-item');
    pur_clear_item_preview_values('.invoice-item');
    $('body').find('#items-warning').remove();
    $("body").find('.dt-loader').remove();
        $('#item_select').selectpicker('val', '');

    return true;
  });
  return false;
}

function pur_get_item_preview_values() {
  "use strict";

  var response = {};
  response.item_name = $('.invoice-item .main textarea[name="item_name"]').val();
  response.description = $('.invoice-item .main textarea[name="description"]').val();
  response.quantity = $('.invoice-item .main input[name="quantity"]').val();
  response.unit_name = $('.invoice-item .main input[name="unit_name"]').val();
  response.unit_price = $('.invoice-item .main input[name="unit_price"]').val();
  response.taxname = $('.main select.taxes').selectpicker('val');
  response.item_code = $('.invoice-item .main input[name="item_code"]').val();
  response.unit_id = $('.invoice-item .main input[name="unit_id"]').val();
  response.tax_rate = $('.invoice-item .main input[name="tax_rate"]').val();
  response.discount = $('.invoice-item .main input[name="discount"]').val();


  return response;
}


function pur_clear_item_preview_values(parent) {
  "use strict";

  var previewArea = $(parent + ' .main');
  previewArea.find('input').val('');
  previewArea.find('textarea').val('');
  previewArea.find('select').val('').selectpicker('refresh');
}

function pur_reorder_items(parent) {
  "use strict";

  var rows = $(parent + ' .table.has-calculations tbody tr.item');
  var i = 1;
  $.each(rows, function () {
    $(this).find('input.order').val(i);
    i++;
  });
}

function pur_delete_item(row, itemid,parent) {
  "use strict";

  $(row).parents('tr').addClass('animated fadeOut', function () {
    setTimeout(function () {
      
    }, 50);
  });
  $(row).parents('tr').remove();
  pur_calculate_total();
  if (itemid && $('input[name="isedit"]').length > 0) {
    $(parent+' #removed-items').append(hidden_input('removed_items[]', itemid));
  }
}

function pur_get_item_row_template(name, item_name, description, quantity, unit_name, unit_price, taxname,  item_code, unit_id, tax_rate, discount, item_key, currency_rate, to_currency)  {
  "use strict";

  jQuery.ajaxSetup({
    async: false
  });

  var d = $.post(site_url + 'sales_agent/portal/get_sale_invoice_row_template', {
    name: name,
    item_name : item_name,
    item_description : description,
    quantity : quantity,
    unit_name : unit_name,
    unit_price : unit_price,
    taxname : taxname,
    item_code : item_code,
    unit_id : unit_id,
    tax_rate : tax_rate,
    discount : discount,
    item_key : item_key,
    currency_rate: currency_rate,
    to_currency: to_currency
  });
  jQuery.ajaxSetup({
    async: true
  });
  return d;
}

</script>