var billingAndShippingFields = ['billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];
var list_discount = [];

(function($) {
    "use strict";
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    calculate_total();
    init_item_js();

    $('.create_pre_order_btn').on('click', function(){
        let length = $('table.items tbody tr.item').length;
        if(length == 0){
            alert_float('warning', 'Please select a item');
            return false;
        }

        if($('select[name="allowed_payment_modes[]"]').val() == ''){
           alert_float('warning', 'Please select payment modes');
           return false;
        }
   });

    $(document).on("change", 'input[type="number"]', function () {
        var max = $(this).attr('max');
        var obj = $(this);

        if(max != 'undefined'){
            if(obj.val() != ''){
                if(parseFloat(obj.val()) > parseFloat(max)){
                    obj.val(max);
                }
            }
        }
        if(obj.val() == '' || obj.val() == 0){
            obj.val(1);
        }
    });

    get_trade_discount();

$('[name="voucher"]').on('change', function(){
    var voucher = $(this).val();
    if(voucher != ''){
          var id = $('input[name="customer"]').val();
          var data = {};
          data.voucher = voucher; 
          data.client = id; 
          data.channel = 6;
          $.post(site_url + 'omni_sales/omni_sales_client/voucher_apply', data).done(function(response){
              response = JSON.parse(response);
              if(response[0] != null){

                  var qty = $('.quantity_item_row');
                  var rate = $('.rate_item_row');
                  let sub_total = 0;
                  let i;
                  for(i = 0; i < qty.length; i++){
                    var quantity = parseFloat(qty.eq(i).val());
                    var price = parseFloat(rate.eq(i).val());
                    var amount = quantity * price;
                    sub_total += amount;
                  }

                  var  test = 0;
                  if(parseFloat(response[0].minimum_order_value)>0){
                    test = 1;
                    if(sub_total >= parseFloat(response[0].minimum_order_value)){
                      test = 0;
                    }
                  }
                  if(test == 0){
                    if(response[0].formal == 1){
                      var total_price_discount = parseFloat(((sub_total * response[0].discount)/100));
                      var total_cal = sub_total - total_price_discount;
                      $('input[name="discount_voucher"]').val(total_price_discount);  
                    }
                    if(response[0].formal == 2){
                      var total_price_discount = parseFloat(response[0].discount);
                      var total_cal = sub_total - total_price_discount;
                      $('input[name="discount_voucher"]').val(total_price_discount);           
                    }
                     calculate_total();  
                     alert_float('success','Voucher applied');                  
                  }
                  else{
                     alert_float('warning','Your order is not eligible for this code');  
                      $('input[name="discount_voucher"]').val('0');           
                       calculate_total();                 
                      $(this).val('')
                  }
            }else{
                alert_float('warning', 'Voucher does not exist');
                $('input[name="discount_voucher"]').val('0');           
                 calculate_total(); 
                $(this).val('')
            }
          })
    }  
    else{
        $('input[name="discount_voucher"]').val('0');           
         calculate_total();   
    } 
  });

})(jQuery);
// Items add/edit
function init_item_js() {
    "use strict";
    // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function() {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            add_item_to_preview(itemid);
        }
    });
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
    requestGetJSON('omni_sales/omni_sales_client/get_item_by_id/' + id).done(function(response) {
        clear_item_preview_values();
        $('.main input[name="product_id"]').val(response.id);
        $('.main textarea[name="description"]').val(response.description);
        if(response.long_description != null){
            $('.main textarea[name="long_description"]').val(response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
        }
        $('.main input[name="quantity"]').val(1);
        if(response.without_checking_warehouse == 1){
            $('.main input[name="quantity"]').attr('max', 1000);
        }
        else{
            $('.main input[name="quantity"]').removeAttr('max');
        }
        $('.main input[name="rate"]').val(response.rate_text);
        $('.main input[name="tax"]').val(response.taxname);
        $('.main .quantity .unit').text(response.unitname);
        $('.main input[name="discount"]').val(response.discount_price);
        data = response;
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
    previewArea.find('input[name="quantity"]').val(1);
    previewArea.find('input[name="rate"]').val('');
    previewArea.find('input[name="unit"]').val('');
}


// Append the added items to the preview to the table as items
function add_item_to_table() {
   "use strict";
   var description = $('.main textarea[name="description"]').val();
   var long_description = $('.main textarea[name="long_description"]').val();
   if(description.trim()){
    // If not custom data passed get from the preview

    var table_row = '';
    var item_key = $("body").find('tbody .item').length + 1;
    table_row += '<tr class="sortable item">';
    table_row += '<td class="dragger">';
    // Check if quantity is number
    if (isNaN(data.qty)) {
        data.qty = 1;
    }
    // Check if rate is number
    if (data.rate === '' || isNaN(data.rate)) {
        data.rate = 0;
    }


    var qty = $('.main input[name="quantity"]').val();
    var amount = data.rate * qty;
    var tax_name = 'newitems[' + item_key + '][taxname][]';
    $("body").append('<div class="dt-loader"></div>');
    var regex = /<br[^>]*>/gi;

        // order input
        table_row += '<input type="hidden" class="order" name="newitems[' + item_key + '][order]">';
        table_row += '</td>';

        table_row += '<td class="bold description"><input type="hidden" name="newitems[' + item_key + '][id]" value="">';
        table_row += '<input type="hidden" name="newitems[' + item_key + '][product_id]" value="' + data.id + '">';
        table_row += '<textarea name="newitems[' + item_key + '][description]" class="form-control" rows="5">' + data.description + '</textarea></td>';
        var long_description = '';
        if(data.long_description != null){
            long_description = data.long_description;
        }
        table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" class="form-control item_long_description" rows="5">' + long_description + '</textarea></td>';

        table_row += '<td><div class="form-group">';
        table_row += '<div class="input-group quantity">';

        var max = '';
        if(data.without_checking_warehouse == 1){
            max = 'max="1000"';
        }

        table_row += '<input type="number" class="form-control quantity_item_row" data-quantity onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][qty]" value="'+qty+'" min="1" '+max+'>';
        table_row += '<span class="input-group-addon unit">' + data.unit_name + '</span>';
        table_row += '</div>';
        table_row += '</div></td>';

        table_row += '<input type="text" name="newitems[' + item_key + '][unit]" class="form-control input-transparent text-right" value="' + data.unit_name + '">';
        table_row += '</td>';
        table_row += '<td class="rate"><input type="hidden" name="rate" class="rate_item_row" value="'+data.rate+'"><input data-toggle="tooltip" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' + item_key + '][rate]" value="' + data.rate_text + '" class="form-control"></td>';
        table_row += '<td class="rate"><input type="hidden" name="tax_name" class="tax_name_item_row" value="'+data.taxname+'"><input type="hidden" name="tax_rate" class="tax_rate_item_row" value="'+data.taxrate+'"><input data-toggle="tooltip" name="newitems[' + item_key + '][rate]" value="' + data.taxname + '" class="form-control"></td>';


        table_row += '<td class="amount" align="right" data-id="'+data.id+'" data-gid="'+data.group_id+'">';
        table_row += '<div class="price_w1 hide"><span class="old_price">' + ReplaceNumberWithCommas(amount) + '</span></div>';
        table_row += '<div class="price_w2 hide d-grid"><span class="new_price"></span><br><span class="line-throught old_price">' + ReplaceNumberWithCommas(amount) + '</span></div>';
        table_row += '</td>';


        table_row += '<td><input type="hidden" name="discount" value="'+data.discount_price+'"><a href="#" class="btn btn-danger pull-right" onclick="delete_item(this,' + data.id + '); return false;"><i class="fa fa-trash"></i></a></td>';
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
        $('.main textarea[name="description"]').val('');
        $('.main textarea[name="long_description"]').val('');
        $('.main input[name="product_id"]').val('');
        $('.main input[name="quantity"]').val(1);
        $('.main input[name="rate"]').val('');
        $('.main input[name="tax"]').val('');
        $('.main .unit').text($('input[name="unit_text"]').val());
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
var data = {};
// Calculate invoice total - NOT RECOMENDING EDIT THIS FUNCTION BECUASE IS VERY SENSITIVE

function calculate_total() {
    "use strict";
    $('.price_w2').addClass('hide');
    $('.price_w1').removeClass('hide'); 
    $('.tax-area').remove();
    var qty = $('.quantity_item_row');
    var rate = $('.rate_item_row');
    var list_amount = $('.item .amount');
    var list_tax_name = $('.item .tax_name_item_row');
    var list_tax_rate = $('.item .tax_rate_item_row');
    let check_subtotal = 0;
    let subtotal = 0;
    let total_tax = 0;
    let discount = 0;
    let total_discount_item = 0;
    var taxes = {};

    let i;
    for(i = 0; i < qty.length; i++){
        var quantity = parseFloat(qty.eq(i).val());
        var price = parseFloat(rate.eq(i).val());
        var amount = quantity * price;
        check_subtotal += amount;
    }

  

    for(i = 0; i < qty.length; i++){
        var quantity = parseFloat(qty.eq(i).val());
        var price = parseFloat(rate.eq(i).val());
        // var discount_price = parseFloat(list_discount.eq(i).val());
        var amount = quantity * price;
        var amount_data = check_trade_discount_item(list_amount.eq(i), check_subtotal, amount, quantity);
        amount = amount_data.amount;
        total_discount_item += amount_data.discount_item;
        // discount += quantity * discount_price;
        // list_amount.eq(i).text(ReplaceNumberWithCommas(amount));
        subtotal += amount;
        var name = list_tax_name.eq(i).val();
        var tax_rate = list_tax_rate.eq(i).val();

        let calculated_tax = (tax_rate * amount / 100);
        total_tax += calculated_tax;
        var obj_key = tax_rate.replace(/\./g, '_');
        if (!taxes.hasOwnProperty(obj_key)) {
            if (tax_rate != 0) {
                var tax_row = '<tr class="tax-area"><td>'+name+'</td><td id="tax_id_' + obj_key + '">'+ReplaceNumberWithCommas(calculated_tax)+'</td></tr>';
                $('#discount_area').after(tax_row);
                taxes[obj_key] = calculated_tax;
            }
        } else {
            var new_val = taxes[obj_key] + calculated_tax;
            taxes[obj_key] = new_val;
            $('td#tax_id_'+obj_key+'').text(ReplaceNumberWithCommas(new_val));
        }
    }
    if(list_discount != undefined && (check_subtotal >= parseFloat(list_discount['minimum_order_value']))){
        if(list_discount['item'] == ""){
            if (parseInt(list_discount['formal']) == 1) {
                discount = subtotal * parseFloat(list_discount['discount']) / 100;
            } else {
                discount = parseFloat(list_discount['discount']);
            }
        } 
    }
    var discount_voucher = $('input[name="discount_voucher"]').val();
    if(discount_voucher == ''){
        discount_voucher = 0;
    }
    discount = discount + total_discount_item + parseFloat(discount_voucher);
    $('input[name="discount"]').val(discount);
    $('table td#sub_total').text(ReplaceNumberWithCommas(round(subtotal)));
    $('table td#discount').text('-'+ReplaceNumberWithCommas(round(discount)));
    var shipping = $('input[name="shipping"]').val();
    var total_all = subtotal + total_tax - discount + parseFloat(shipping);
    $('table td#shipping_fee').text(ReplaceNumberWithCommas(round(shipping)));
    $('table td#total').text(ReplaceNumberWithCommas(round(total_all)));
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
function formatNumber(n) {
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function round(val){
  "use strict";
  return Math.round(val * 100) / 100;
}
function get_trade_discount(){
  "use strict";
  var id = $('input[name="customer"]').val();
  if(id != ''){
   $.ajax({
     url: "get_trade_discount",
     type: "post",
     data: {'id':id, 'channel': 6},
     success: function(){
     },
     error:function(){
        alert_float('danger', 'Failure');
    }
}).done(function(response) {
    response = JSON.parse(response);
    if(response[0].length >= 1){
      list_discount = [];
      for(var i = 0; i < response[0].length;i++){
        list_discount = {item:response[0][i]['items'], formal:response[0][i]['formal'],group_list:response[0][i]['group_items'], discount:response[0][i]['discount'], voucher:response[0][i]['voucher'], minimum_order_value:response[0][i]['minimum_order_value']};
    }
    $('.input-discount-fixed').val(0);
    $('.input-discount-percent').val(0);
}              
calculate_total();
}); 
} 
}

function check_trade_discount_item(obj, subtotal, amount, quantity){
    "use strict";
    var old_amount = amount;
    var discount_item = 0;
    if(list_discount != undefined && (subtotal >= list_discount['minimum_order_value'])){
        var array = list_discount['item'].split(',');
        var array_group = list_discount['group_list'].split(',');
        var id = obj.data('id').toString();
        var gid = obj.data('gid').toString();    
        if(array.includes(id) || array_group.includes(gid)){
            if(parseInt(list_discount['formal']) == 1){
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
            obj.find('.price_w2').addClass('hide');
            obj.find('.price_w1').removeClass('hide');
            obj.find('.old_price').text(ReplaceNumberWithCommas(amount)); 
        }
    }
    else{
        obj.find('.price_w2').addClass('hide');
        obj.find('.price_w1').removeClass('hide');
        obj.find('.old_price').text(ReplaceNumberWithCommas(amount)); 
    }
    return { 'amount':amount, 'old_amount': old_amount, 'discount_item': discount_item };
}