var input_name = '';
var obj_input;
(function(){
  "use strict";
  $('.exits_show').on('click', function(){ $('body').find('.show').removeClass('show'); })

  $(window).on('load', function() {
   $('#all_product').click();
   $('#tab1').html($('#tab_content_template').html());
    total_cart();
 });
  $('input[name="keyword"]').keypress(function(event) {
    if (event.keyCode == 13) {
     $('.search_btn').click();
   }
 });

  $('.item-cash').click(function(){
    var obj = $(this);
    var val = obj.data('value');
    if(val == 0){
      this_obj.val('0');
      $('.amount-cash').remove();
    }
    else{
      if(obj.find('.amount-cash').length == 0){
        obj.append('<span class="amount-cash">1</span>');
      }
      else{
        var amount = obj.find('.amount-cash').text();
        amount = parseInt(amount) + 1;
        obj.find('.amount-cash').text(amount);
      }
      var val_input = this_obj.val();
      if(val_input!=''){
        val_input = val_input.replace(new RegExp(',', 'g'),"");
        this_obj.val(numberWithCommas(round(parseFloat(val_input) + parseFloat(val))));
      }
      else{
        this_obj.val(numberWithCommas(round(val)));
      }      
    }
    cal_price();
  });
  $('.dropdown').on('show.bs.dropdown', function() {
    $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
  });

  $('.dropdown').on('hide.bs.dropdown', function() {
    $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
  });
  $('.table-menu a').click(function(){
    var id = $(this).data('id');
    if(id == 'setting'){

    }
    if(id == 'customer'){

    }
    if(id == 'calculator'){

    }
  });
  setInterval(updateClock, 1000);
  $('.billing-same-as-customer').on('click', function(e) {
    e.preventDefault();
    $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
    $('input[name="billing_city"]').val($('input[name="city"]').val());
    $('input[name="billing_state"]').val($('input[name="state"]').val());
    $('input[name="billing_zip"]').val($('input[name="zip"]').val());
    $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
  });

  $('.customer-copy-billing-address').on('click', function(e) {
    e.preventDefault();
    $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
    $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
    $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
    $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
    $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
  });

  $(document).on("click",'input[type="text"], input[type="number"], input[type="email"], textarea, input[data-type="currency"]',function() {
    init_keyboard(this);
  });

  // Images upload and edit
  document.addEventListener("DOMContentLoaded", init, false);
  // End Image upload and edit
  $('.add_new_payment').click(function(){
    var parent_row = $(this).parents('.payment_row').clone();
    parent_row.insertAfter($('.payment_row').last());
    parent_row.find('input[name="customers_pay[]"]').val('').removeClass('danger');
    parent_row.find('.payment_methods_alert').addClass('hide');
    parent_row.find('select[name="payment_methods[]"]').val('').change();

    parent_row.find('button[role="combobox"]').remove();
    parent_row.find('select[name="payment_methods[]"]').selectpicker('refresh');
    parent_row.find('.add_new_payment').html('&#45;').addClass('remove_payment').removeClass('add_new_payment');
  });

  $(document).on("click",".remove_payment",function() {
    $(this).closest('.payment_row').remove();
    cal_price();
  });
  
})(jQuery);

var selDiv = "";
function init() {
  "use strict";
  document.querySelector('#files').addEventListener('change', handleFileSelect, false);
  selDiv = document.querySelector("#selectedFiles");
}

function handleFileSelect(e) {
  "use strict";
  if(!e.target.files || !window.FileReader) return;
  selDiv.innerHTML = "";
  var files = e.target.files;
  var filesArr = Array.prototype.slice.call(files);

  jQuery.each(filesArr,function(key, file){
    if(!file.type.match("image.*")) {
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      var html = "<div class=\"col-md-3\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" + file.name + "\"><div class=\"contain_image\"><img src=\"" + e.target.result + "\"></div><div class=\"file-name\">" + file.name + "<div></div>";
      selDiv.innerHTML += html;       
    }
    reader.readAsDataURL(file); 

  });  
}

function init_keyboard(el){
  "use strict";
  var ckeyboard = getCookie('enable_keyboard');
  if(ckeyboard == 1){
    var has_object = true;
    if(typeof obj_input != "object"){
      has_object = false;
    }
    obj_input = $(el);
    if(has_object == false){
      active_keyboard();
    }
    $('.modal').addClass('margin_bottom290');
    $('#keyboard').removeClass('hide');
  }
}
function add_cart(el){
  "use strict";
  var id = $(el).data('id');
  var gid = $(el).data('gid');
  var title = $(el).find('.title').text();
  var price = $(el).find('.price').data('price');
  var price_discount = $(el).find('.price').data('price_discount');
  var discount_percent = $(el).find('.price').data('discount_percent');
  var percent_tax = $(el).data('percent-tax');
  var tax_name = $(el).data('tax_name');
  var total_tax = $(el).data('total-tax');
  var w_quantity = $(el).data('w_quantity');
  if(w_quantity<=0){
    $('#alert').modal('show').find('.alert_content').text('This product is out of stock');
    setTimeout(function(){ $('#alert').modal('hide'); },1500);
    return false;
  } 

  var list_id = $('.tab-pane.active').find('input[name="list_id_product"]').val();
  var list_qty = $('.tab-pane.active').find('input[name="list_qty_product"]').val();
  var list_price = $('.tab-pane.active').find('input[name="list_price_product"]').val();
  var list_price_discount = $('.tab-pane.active').find('input[name="list_price_discount_product"]').val();
  var list_percent_discount = $('.tab-pane.active').find('input[name="list_percent_discount_product"]').val();
  var list_price_tax = $('.tab-pane.active').find('input[name="list_price_tax"]').val();
  var cart_qty_list = getCookie('type_input_qty');
  var qty = 1;
  if(typeof cart_qty_list != ""){
    if(cart_qty_list.trim()){
      if(cart_qty_list != ''){
        qty = parseFloat(cart_qty_list);
      }
    }
  }

  var new_value = 1;
  if(list_id != ''){
    var id_list = JSON.parse('['+list_id+']');
    var qty_list = JSON.parse('['+list_qty+']');
    var index_id = -1;
    $.each(id_list, function( key, value ) {
      if(value == id){
        index_id = key;
      }
    }); 

    if(index_id == -1){
      if(id != '' && price != ''){
        list_id = list_id+','+id;
        list_qty = list_qty+','+qty;
        list_price = list_price+','+price;
        list_price_discount = list_price_discount+','+price_discount;
        list_price_tax = list_price_tax+','+total_tax;
        list_percent_discount = list_percent_discount+','+discount_percent;
        add_cart_data(list_id,list_qty,list_price,list_price_discount,list_price_tax,list_percent_discount);
        add_item_cart(title, price,price_discount,discount_percent, id, tax_name,w_quantity,gid);
      }
    }
    else{
      var new_list_qty = '';
      $.each(qty_list, function( key, value ) {
        if(index_id == key){
          var val = round(parseFloat(value)+qty);
          if(w_quantity < val){
            val  = val - 1;
            $('#alert').modal('show').find('.alert_content').text('The quantity limit is only from 1 to '+w_quantity);
            setTimeout(function(){ $('#alert').modal('hide'); },1500);
          }
          new_list_qty += val+',';
          new_value = val;
        }
        else{
          new_list_qty += value+',';
        }
      });


      add_cart_data(list_id,new_list_qty.replace(/,+$/, ''),list_price,list_price_discount, list_price_tax,list_percent_discount);

      var list_input = $('.tab-pane.active').find('.quantity');
      for (var i = 0; i < list_input.length; i++) {
        if(list_input.eq(i).data('id') == id){
          list_input.eq(i).val(new_value);
        }
      }         
    }
  }
  else{
    add_cart_data(id,qty,price,price_discount,total_tax,discount_percent);
    add_item_cart(title, price,price_discount,discount_percent, id, tax_name,w_quantity,gid);
  }
  total_cart();

}

function delete_item(el,id){
  "use strict";
  delete_element(id);
  $(el).closest('.ritem').remove();
}
function add_cart_data(list_id, list_qty, list_price, price_discount, list_price_tax,list_percent_discount){
  "use strict";
  $('.tab-pane.active').find('input[name="list_id_product"]').val(list_id);
  $('.tab-pane.active').find('input[name="list_qty_product"]').val(list_qty);
  $('.tab-pane.active').find('input[name="list_price_product"]').val(list_price);
  $('.tab-pane.active').find('input[name="list_price_discount_product"]').val(price_discount);
  $('.tab-pane.active').find('input[name="list_price_tax"]').val(list_price_tax);
  $('.tab-pane.active').find('input[name="list_percent_discount_product"]').val(list_percent_discount);
}
function add_item_cart(title, price,price_discount, discount, id, tax_name, w_quantity, group_id){
  "use strict";
  var price_html = '';

  price_html += '<div class="price_w2 hide"><span class="new_prices"></span></br><span class="old_prices">'+numberWithCommas(price)+'</span></div>';

  price_html += '<div class="price_w1"><span class="new_prices">'+numberWithCommas(price)+'</span></div>';

  var qty = 1;
  var cart_qty_list = getCookie('type_input_qty');
  if(typeof cart_qty_list != ""){
    if(cart_qty_list.trim()){
      if(cart_qty_list != ''){
        qty = parseFloat(cart_qty_list);
      }
    }
  }
  if(w_quantity<=0){
    qty = 0;
  }
  var html = '<div class="col-md-12 ritem items" data-id="'+id+'" data-gid="'+group_id+'">';
  html +='<div class="col-md-12 items">';
  html +='<div class="row row_item_cart">';
  html +='<div class="col-md-12 title">'+title+' <div class="pull-right tax-title">'+tax_name+'</div></div>';
  html +='<div class="clearfix"></div>';
  html +='<div class="clearfix"></div>';
  html +='<div class="co-md-12 w-100"><br>';
  html +='<div class="row m-0">';
  html +='<div class="col-md-5 m-0 prices p-2 p-0">'+price_html+'</div>';
  html +='<input type="hidden" name="productid" >';
  html +='<div class="col-md-5 m-0">';
  html +='<div class="input_groups">';
  html +='<span class="append_left minus" onclick="change_qty('+id+',-1);">';
  html +='<i class="fa fa-minus"></i>';
  html +='</span>';
  html +='<input name="quantity" onchange="change_total_by_item('+id+',this)" data-id="'+id+'" class="form-control input-md text-center quantity" type="number" min="1" max="'+w_quantity+'" data-w_quantity="'+w_quantity+'" value="'+qty+'">';
  html +='<span class="append_right plus" onclick="change_qty('+id+',1);">';
  html +='<i class="fa fa-plus"></i>';
  html +='</span>';
  html +='</div>';
  html +='</div>';
  html +='<div class="col-md-2 m-0 p-0 delete_item">';
  html +='<span onclick="delete_item(this,'+id+');">&#10008;</span>';
  html +='</div>';
  html +='</div>';
  html +='</div>';
  html +='<br>';

  html +='</div>';

  html +='<div class="clearfix"></div>';
  html +='</div>';




  $('.tab-pane.active').find('.content_cart .list_item').prepend(html);
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
  var shipping = $('.tab-pane.active').find('input[name="shipping"]').val(); 
  if(shipping == ''){
    shipping = 0;
    $('.tab-pane.active').find('input[name="shipping"]').val(0);
  }
  else{
    shipping = parseFloat(shipping.replace(new RegExp(',', 'g'),""));
  }

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
  var total_s = round(total-discount+tax+shipping);
  $('.tab-pane.active').find('.total').text(numberWithCommas(total_s));
  $('.tab-pane.active').find('.promotions_tax_price').text(numberWithCommas(round(tax)));
  $('.tab-pane.active').find('input[name="sub_total_cart"]').val(total);
  $('.tab-pane.active').find('input[name="total_cart"]').val(total_s);   
  $('.tab-pane.active').find('input[name="tax"]').val(tax);        
  $('.tab-pane.active').find('input[name="discount_auto_event"]').val(new_discount_customer);    
  $('.tab-pane.active').find('input[name="discount_voucher_event"]').val(discount_voucher); 
  $('.tab-pane.active').find('input[name="discount_voucher_value"]').val(new_discount_voucher); 
  
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
function numberWithCommas(x) {
  "use strict";
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
var length = 2;
function general_tab(el){
  "use strict";
  var html = '<li role="presentation" onclick="open_tab(this);" class="tab_cart wtab_'+length+'">';
  html += '<a href="#tab'+length+'" class="exits_show" aria-controls="tab'+length+'" role="tab" data-toggle="tab">';
  html += length;
  html += '</a>';
  html += '</li>';
  html += '<li role="presentation" onclick="general_tab(this);" class="tab" id="general_tab">';
  html += '<a href="#" role="tab">';
  html += '<i class="fa fa-plus"></i>';
  html += '</a>';
  html += '</li>';
  $(el).remove();
  $('.exits_show').on('click', function(){ $('body').find('.show').removeClass('show'); })
  $('.gen_cart').append(html);
  var content = $('.cart-tab');
  var tab_content = $('#tab_content_template').clone();
  var dropdown = tab_content.find('.dropdown');
  dropdown.find('button').remove();
  dropdown.find('.dropdown-menu').remove();
  tab_content.find('.customerfr').html(dropdown.html());
  var newselect = '<select name="client_id" class="selectpicker input_groups" onchange="get_trade_discount(this);" data-width="100%" data-none-selected-text="Customer" data-live-search="true" tabindex="-98"></select>';
  content.append('<div role="tab'+length+'" class="tab-pane item-tab exits_show client_tab_content client_tab_content_'+length+'" id="tab'+length+'">'+tab_content.html()+'</div>');
  length++;
  $('select[name="client_id"]').selectpicker('refresh');
  scroll_tab_list(1, '.gen_cart');
  $('.gen_cart .tab_cart').find('a').click();
  total_cart();
}
function change_qty(id, val){
  "use strict";
  var cart_qty_list = getCookie('type_input_qty');
  if(typeof cart_qty_list != ""){
    if(cart_qty_list.trim()){
      if(cart_qty_list != ''){
        if(parseInt(val)<0){
          val = parseFloat('-'+cart_qty_list);
        }
        else{
          val = parseFloat(cart_qty_list);
        }
      }
    }
  }

  var list_input = $('.tab-pane.active').find('.quantity');
  for (var i = 0; i < list_input.length; i++) {
   if(list_input.eq(i).data('id') == id){
    var w_quantity = parseInt(list_input.eq(i).data('w_quantity'));
    var quantity = round(parseFloat(list_input.eq(i).val())+val);

    if(quantity<=0){
      quantity = 1;
      $('#alert').modal('show').find('.alert_content').text('The quantity limit is only from 1 to '+w_quantity);
      setTimeout(function(){ $('#alert').modal('hide'); },1500);
      return false;
    }
    if(w_quantity < quantity){
      quantity = w_quantity;
      $('#alert').modal('show').find('.alert_content').text('The quantity limit is only from 1 to '+w_quantity);
      setTimeout(function(){ $('#alert').modal('hide'); },1500);
      return false;
    }
    list_input.eq(i).val(quantity);
    update_quantity(id, quantity);
  }
}  
}
function update_quantity(id,qty){
  "use strict";
  var list_id = $('.tab-pane.active').find('input[name="list_id_product"]').val();
  var list_qty = $('.tab-pane.active').find('input[name="list_qty_product"]').val();
  if(list_id != ''){
   var id_list = JSON.parse('['+list_id+']');
   var qty_list = JSON.parse('['+list_qty+']');
   var index_id = -1;
   $.each(id_list, function( key, value ) {
    if(value == id){
      index_id = key;
    }
  }); 

   var new_list_qty = '';
   $.each(qty_list, function( key, value ) {
    if(index_id == key){
     new_list_qty += qty+',';
   }
   else{
    new_list_qty += value+',';
  }
});
   $('.tab-pane.active').find('input[name="list_qty_product"]').val(new_list_qty.replace(/,+$/, ''));
   total_cart();
 }  
}
function delete_element(id){
  "use strict";
  var list_id = $('.tab-pane.active').find('input[name="list_id_product"]').val();
  var list_qty = $('.tab-pane.active').find('input[name="list_qty_product"]').val();
  var list_price = $('.tab-pane.active').find('input[name="list_price_product"]').val();
  var list_price_discount = $('.tab-pane.active').find('input[name="list_price_discount_product"]').val();
  var list_percent_discount = $('.tab-pane.active').find('input[name="list_percent_discount_product"]').val();
  var list_price_tax = $('.tab-pane.active').find('input[name="list_price_tax"]').val();
  if(list_id != ''){
   var id_list = JSON.parse('['+list_id+']');
   var qty_list = JSON.parse('['+list_qty+']');
   var price_list = JSON.parse('['+list_price+']');
   var price_discount_list = JSON.parse('['+list_price_discount+']');     
   var percent_discount_list = JSON.parse('['+list_percent_discount+']');     
   var price_tax = JSON.parse('['+list_price_tax+']');

   var index_id = -1;
   $.each(id_list, function( key, value ) {
    if(value == id){
      index_id = key;
    }
  }); 

   var new_list_id = '';
   $.each(id_list, function( key, value ) {
    if(index_id != key){
     new_list_id += value+',';
   }           
 });

   var new_list_qty = '';
   $.each(qty_list, function( key, value ) {
    if(index_id != key){
     new_list_qty += value+',';
   }           
 });

   var new_list_prices = '';
   $.each(price_list, function( key, value ) {
    if(index_id != key){
     new_list_prices += value+',';
   }           
 });

   var new_list_prices_discount = '';
   $.each(price_discount_list, function( key, value ) {
    if(index_id != key){
      new_list_prices_discount += value+',';
    }           
  });
   var new_price_tax = '';
   $.each(price_tax, function( key, value ) {
    if(index_id != key){
      new_price_tax += value+',';
    }           
  });
   var new_list_percent_discount = '';
   $.each(percent_discount_list, function( key, value ) {
    if(index_id != key){
      new_list_percent_discount += value+',';
    }           
  });
   add_cart_data(new_list_id.replace(/,+$/, ''),new_list_qty.replace(/,+$/, ''),new_list_prices.replace(/,+$/, ''),new_list_prices_discount.replace(/,+$/, ''),new_price_tax.replace(/,+$/, ''),new_list_percent_discount.replace(/,+$/, ''));
   total_cart();
 }  
}
function cal_price(){
  "use strict";
  var list_customers_pay = $('input[name="customers_pay[]"]');
  var total_customer_pay = 0;
  for(let  i = 0; i < list_customers_pay.length; i++){
    var val = list_customers_pay.eq(i).val().replace(new RegExp(',', 'g'),"");
    if(val != '' && val != 0){
      total_customer_pay += parseFloat(val.trim());
    }
  }
  var total = $('.tab-pane.active input[name="total_cart"]').val();  

  if(total!=''){
    $('.total_paying_s').text(numberWithCommas(round(total_customer_pay)));
    $('.balance_s').text(numberWithCommas(round(parseFloat(total_customer_pay) - parseFloat(total)))); 
  }else{
    $('.total_paying_s').text('');
    $('.balance_s').text(''); 
  }
}

function formatNumber(n) {
  "use strict";
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict";
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf(".") >= 0) {
    var decimal_pos = input_val.indexOf(".");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);

    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "." + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;
  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}
function open_tab(el){
  "use strict";
  $('.cart-tab .tab-pane').removeClass('show');  
}
function remove_tab(el){
  "use strict";
  var tab_id = $(el).closest('.client_tab_content').attr('id');
  $('#'+tab_id).remove();
  var tab = $(el).closest('li').find('a').attr('href');
  var list_tab = $('.tab_cart');
  for (var i = 0; i < list_tab.length; i++) {
    var element = list_tab.eq(i);
    var a_tag = element.find('a').attr('href');
    if(a_tag == '#'+tab_id){
      element.remove();
    }
  }
}
function change_result(el){
  "use strict";
  if($(el).val() == ''){
    $('.search_btn').click();
  }
}
function get_discount_client(total){
  "use strict";
  var result = 0;
  var customers_id = $('.tab-pane.active').find('select[name="client_id"]').val();
  $('.price_w2').addClass('hide');
  $('.price_w1').removeClass('hide'); 
  if(customers_id!=''){
    $.each(list_discount[customers_id], function( key, value ) {

      if(value['item']!=''){

        var array = value['item'].split(',');
        var array_group = value['group_list'].split(',');
        var list_id_product_cart =  $('.tab-pane.active').find('.ritem.items'); 
        for(var i = 0;i<list_id_product_cart.length;i++){
          var obj = list_id_product_cart.eq(i);
          var id = obj.data('id').toString();
          var gid = obj.data('gid').toString();                  
          if(array.includes(id) || array_group.includes(gid)){

            var data_info_item = get_infor_item(parseInt(id));
            var price_discount = 0;
            if(parseFloat(value['minimum_order_value'])>0){
              if(total>=parseFloat(value['minimum_order_value'])){
                if(parseInt(value['formal']) == 1){
                  var discount_item = parseFloat(data_info_item.prices * value['discount'] / 100);
                  price_discount = parseFloat(data_info_item.prices) - discount_item; 
                  result += discount_item * parseInt(data_info_item.qty);
                }
                else{
                  price_discount = parseFloat(data_info_item.prices) - value['discount'];                                  
                  result += parseFloat(value['discount']) * parseInt(data_info_item.qty);
                }
              }
            }
            else{
              if(parseInt(value['formal']) == 1){
                var discount_item = parseFloat(data_info_item.prices * value['discount'] / 100);
                price_discount = parseFloat(data_info_item.prices) - discount_item; 
                result += discount_item * parseInt(data_info_item.qty);
              }
              else{
                price_discount = parseFloat(data_info_item.prices) - value['discount'];                                  
                result += parseFloat(value['discount']) * parseInt(data_info_item.qty);
              }
            }
            if(parseFloat(price_discount) > 0){
             obj.find('.price_w2').removeClass('hide'); 
             var new_price = numberWithCommas(price_discount);
             obj.find('.price_w2 .new_prices').text(new_price); 
             obj.find('.price_w1').addClass('hide');
           }

         }
       }
     }
     else{

       if(parseFloat(value['minimum_order_value'])>0){
        if(total>=parseFloat(value['minimum_order_value'])){
          if(parseInt(value['formal']) == 1){
            result += parseFloat(total * value['discount'] / 100);
          }
          else{
            result += parseFloat(value['discount']);
          }
        }                    
      }
      else{
        if(parseInt(value['formal']) == 1){
          result += parseFloat(total * value['discount'] / 100);
        }
        else{
          result += parseFloat(value['discount']);

        }
      }
    }        
  });  
  }    
  else{
    $('.price_w2').addClass('hide');
    $('.price_w1').removeClass('hide');  
  }
  return result;
}
function get_infor_item(id){
  "use strict";
  var data_result = {};
  var list_id = $('.tab-pane.active').find('input[name="list_id_product"]').val();
  var list_qty = $('.tab-pane.active').find('input[name="list_qty_product"]').val();
  var list_price = $('.tab-pane.active').find('input[name="list_price_product"]').val();  
  if(list_id != ''){
    var id_list = JSON.parse('['+list_id+']');
    var qty_list = JSON.parse('['+list_qty+']');
    var price_list = JSON.parse('['+list_price+']');

    var index_id = -1;
    $.each(id_list, function( key, value ) {
      if(value == id){
        index_id = key;
      }
    }); 
    var qty = 0;
    $.each(qty_list, function( key, value ) {
      if(index_id == key){
        qty = value;
        return false;
      }           
    });

    var prices = 0;
    $.each(price_list, function( key, value ) {
      if(index_id == key){
        prices = value;
        return false;
      }           
    });
    data_result.qty = qty;
    data_result.prices = prices;
    return data_result;
  }
  return false;
}

function scroll_list(val){
  "use strict";
  var offset_l = $('.header-tab-group ul').get(0).scrollLeft;
  var width = $('.header-tab-group ul').width();
  var index_scroll = offset_l + (val*(width-100));
  if(index_scroll<0){
    index_scroll = 0;
  }
  $('.header-tab-group ul').animate({ scrollLeft: index_scroll }, 1000);
  if(index_scroll>offset_l){
    index_scroll = offset_l;
  }
}
function round(val){
  "use strict";
  return Math.round(val * 100) / 100;
}

function init_selectpicker() {
  appSelectPicker();
}

function appSelectPicker(element) {

  if (typeof(element) == 'undefined') {
    element = $("body").find('select.selectpicker');
  }

  if (element.length) {
    element.selectpicker({
      showSubtext: true
    });
  }
}
function change_total_by_item(id, el){
  "use strict";
  var max = $(el).attr('max');
  var qty = $(el).val();
  max = parseFloat(max);
  qty = parseFloat(qty);
  if(isNaN(qty)){
    qty = 1;
    var cart_qty_list = getCookie('type_input_qty');
    if(typeof cart_qty_list != ""){
      if(cart_qty_list.trim()){
        if(cart_qty_list != ''){
          qty = parseFloat(cart_qty_list);
        }
      }
    }
    $(el).val(qty);
  }
  if(qty > max){
    qty = max;
    $(el).val(max);
  }
  update_quantity(id, qty);
}
function show_iframe(el){
  "use strict";
  var h = window.innerHeight;
  var html = $(el).data('html');
  $("#content_print").contents().find('body').html(html);
  var h = window.innerHeight;
  $('.check_out_success').css({'height': (h-200)+'px'});
  $('#modal_iframe').modal('show');
  window.frames["content_print"].focus();
  window.frames["content_print"].print();
}
function staff_profile(el){
  "use strict";
  $('#modal_staff').modal('show');
}
function logout() {
  "use strict";
  window.location.href = '../authentication/logout';
}
function registration_client(){
  "use strict";
  $('#myModal').modal('show');
  $('input[name="company"]').val('');
  $('input[name="phonenumber"]').val('');
  $('textarea[name="address"]').val('');
  $('input[name="city"]').val('');
  $('input[name="state"]').val('');

  $('textarea[name="billing_street"]').val('');
  $('input[name="billing_city"]').val('');
  $('input[name="billing_state"]').val('');

  $('textarea[name="shipping_street"]').val('');
  $('input[name="shipping_city"]').val('');
  $('input[name="shipping_state"]').val('');
}
function save_cart_setting(){
  "use strict";
  // Type input quantity
  var type = $('input[name="type_input_qty"]:checked').val(); 
  add_cookie('type_input_qty',type,30);

  // Enable keyboard
  var enable_keyboard = $('input[name="enable_keyboard"]:checked').val(); 
  if(typeof enable_keyboard == "undefined"){
    enable_keyboard = 0;
  }
  add_cookie('enable_keyboard',enable_keyboard,365);

  // Auto open new tab
  var auto_open_new_tab = $('input[name="auto_open_new_tab"]:checked').val(); 
  if(typeof auto_open_new_tab == "undefined"){
    auto_open_new_tab = 0;
  }
  add_cookie('auto_open_new_tab',auto_open_new_tab,365);
}
function save_setting(el){
  "use strict";
  var id = $(el).attr('id');
  switch(id){
    case 'type_input_qty':
    save_cart_setting();
    break;
  }
  $('#modal_setting').modal('hide')
}
function getCookie(cname) {
  "use strict";
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
function add_cookie(cname, cvalue, exdays) {
  "use strict";
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
function round(val){
  "use strict";
  return Math.round(val * 100) / 100;
}

function updateClock() {
 "use strict";
 var currentTime = new Date();
 var currentHoursAP = currentTime.getHours();
 var currentHours = currentTime.getHours();
 var currentMinutes = currentTime.getMinutes();
 var currentSeconds = currentTime.getSeconds();
 currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
 currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
 var timeOfDay = (currentHours < 12) ? "AM" : "PM";
 currentHoursAP = (currentHours > 12) ? currentHours - 12 : currentHours;
 currentHoursAP = (currentHoursAP == 0) ? 12 : currentHoursAP;
 var currentTimeString =  currentHours + ":" + currentMinutes + ":" + currentSeconds;
 $('#current_time').text(currentTimeString);
}
function ui_check(){
  var create_invoice = $('input[name="create_invoice"]').is(":checked");
  if(create_invoice == true){
    $('.container_checkbox.stock_export').removeClass("hide");
  }
  else{
    $('input[name="stock_export"]').prop('checked', false);
    $('.container_checkbox.stock_export').addClass("hide");
  }
}

function ui_check_debit_order(){
  var debit_order = $('input[name="debit_order"]').is(":checked");
  if(debit_order == true){
    $('input[name="customers_pay"]').val('').attr('disabled', '');
    $('select[name="payment_methods"]').val('').change().attr('disabled', '');
    $('.payment_methods_alert').addClass('hide');
  }
  else{
    $('input[name="customers_pay"]').val('').removeAttr('disabled');
    $('select[name="payment_methods"]').val('').change().removeAttr('disabled');
  }
}


function active_keyboard(){
  "use strict";
  var isShift = false,
  capsLock = false,
  isNumSymbols = false,
  isMoreSymbols = false;
  var kbdMode = ["lowercase", "uppercase", "numbers", "symbols"];
  /* typing */
  for (var i = 0; i < kbdMode.length; i++) {
    $("#" + kbdMode[i] + " .row .white:not(:last)").click(function(){

      var value = obj_input.val();
      var add_value = $(this).find("span").html().substring(0,1);
      var new_value = value + add_value;
      if(add_value == "0"){
        new_value = new_value + "0";
      }
      obj_input.val(new_value);
      obj_input.focus();          
    });

    $("#" + kbdMode[i] + " .row .white:last").click(function(){
      var value = obj_input.val();
      obj_input.val(value + " ");
    });
  }
  /*end*/

  /*start*/
  $(".white").mouseup(function(){
    obj_input.focus();
    $(this).find("span").css("color", "#3071A9");
    if (isShift == true && capsLock == false) {
      $("#lowercase").css("display", "block");
      $("#uppercase").css("display", "none");
      isShift = false;
    }
  });
  /*end*/

  /* toggle shift */
      // lowercase to uppercase
      $("#lowercase .row:eq(2) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isShift == false) {
          $("#lowercase").css("display", "none");
          $("#uppercase").css("display", "block");
          isShift = true;
        }
      });
      // uppercase to lowercase
      $("#uppercase .row:eq(2) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isShift == true) {
          $("#lowercase").css("display", "block");
          $("#uppercase").css("display", "none");
          isShift = false;
        }
      });
      // lowercase to uppercase
      $("#lowercase .row:eq(2) .gray:last").click(function(){
        obj_input.focus();
        if (isShift == false) {
          $("#lowercase").css("display", "none");
          $("#uppercase").css("display", "block");
          isShift = true;
        }
      });
      // uppercase to lowercase
      $("#uppercase .row:eq(2) .gray:last").click(function(){
        obj_input.focus();
        if (isShift == true) {
          $("#lowercase").css("display", "block");
          $("#uppercase").css("display", "none");
          isShift = false;
        }
      });
      // caps lock on
      $("#uppercase .row:eq(2) .gray:eq(0)").dblclick(function(){
        obj_input.focus();
        if (capsLock == false) {
          $("#lowercase").css("display", "none");
          $("#uppercase").css("display", "block");
          $(this).children("span").html("&#8682;");
          capsLock = true;
        }
      });
      // caps lock off
      $("#uppercase .row:eq(2) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (capsLock == true) {
          $("#lowercase").css("display", "block");
          $("#uppercase").css("display", "none");
          $(this).children("span").html("&#11014;");
          capsLock = false;
        }
      });
      // caps lock on
      $("#uppercase .row:eq(2) .gray:last").dblclick(function(){
        obj_input.focus();
        if (capsLock == false) {
          $("#lowercase").css("display", "none");
          $("#uppercase").css("display", "block");
          $(this).children("span").html("&#8682;");
          capsLock = true;
        }
      });
      // caps lock off
      $("#uppercase .row:eq(2) .gray:last").click(function(){
        obj_input.focus();
        if (capsLock == true) {
          $("#lowercase").css("display", "block");
          $("#uppercase").css("display", "none");
          $(this).children("span").html("&#11014;");
          capsLock = false;
        }
      });
      /*end*/
      $(".gray-enter").click(function(){
        var obj_name  = obj_input.attr('name');
        if(obj_name == 'voucher'){
          get_voucher(obj_input);
        }
        if(obj_name == 'customers_pay'){
          cal_price();
        }
        if(obj_name == 'keyword'){
          $('.search_btn').click();
        }   
        if(obj_name == 'quantity'){
          var data_id = $(obj_input).data('id');
          change_total_by_item(data_id, obj_input);
        }          
        $('#keyboard').addClass('hide');
        $('.modal').removeClass('margin_bottom290');
      });
      function backspace(){
        obj_input.focus();
        obj_input.val(obj_input.val().substring(0,obj_input.val().length-1));
      };
      function callback_number(){
        obj_input.focus();
        if(isNumSymbols == true){
          isNumSymbols = false;
        }
      };
      for (var j = 0; j < kbdMode.length; ++j) {
        $("#" + kbdMode[j] + " .row:eq(0) .key:last").click(backspace);
        $("#" + kbdMode[j] + " .row:eq(2) .delete_orange").click(backspace);
      };  

      /* toggle numbers */
      // lowercase/uppercase to numbers
      for (var k = 0; k < kbdMode.length-2; ++k) {
        $("#" + kbdMode[k] + " .row:eq(3) .gray:eq(0)").click(function(){
          obj_input.focus();
          if (isNumSymbols == false) {
            $("#numbers").css("display", "inherit");
            $("#lowercase").css("display", "none");
            $("#uppercase").css("display", "none");
            $("#uppercase .row:eq(2) .gray:eq(0)").children("span").html("&#11014;");
            $("#numbers .row:eq(3) .white").addClass('custom');
            isNumSymbols = true;
            isShift = false;
            capsLock = false;
          }
        });
      }
      // numbers to lowercase
      $("#numbers .row:eq(3) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isNumSymbols == true) {
          $("#numbers").css("display", "none");
          $("#lowercase").css("display", "block");
          $("#lowercase .row:eq(3) .gray:eq(0)").click(callback_number);
          isNumSymbols = false;
        }else{
          $("#numbers").css("display", "none");
          $("#lowercase").css("display", "block");
          $("#lowercase .row:eq(3) .gray:eq(0)").click(callback_number);
          if(isNumSymbols == true){
           isNumSymbols  = false;
         }
       }
     });

      /* toggle symbols */
      // numbers to symbols
      $("#numbers .row:eq(2) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isMoreSymbols == false) {
          $("#numbers").css("display", "none");
          $("#symbols").css("display", "block");
          $("#symbols .row:eq(3) .white").addClass('custom');
          isMoreSymbols = true;
        }else{
          $("#numbers").css("display", "none");
          $("#symbols").css("display", "block");
          $("#symbols .row:eq(3) .white").addClass('custom');
          isMoreSymbols = true;
        }
      });

      // symbols to lowercase
      $("#symbols .row:eq(3) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isMoreSymbols == true) {
          $("#lowercase").css("display", "block");
          $("#symbols").css("display", "none");
          $("#lowercase .row:eq(3) .gray:eq(0)").click(callback_number);
          isMoreSymbols = false;
        }
      });

      $("#symbols .row:eq(2) .gray:eq(0)").click(function(){
        obj_input.focus();
        if (isNumSymbols == false) {
          $("#numbers").css("display", "block");
          $("#symbols").css("display", "none");
          $("#symbols .row:eq(3) .gray:eq(0)").click(callback_number);
          isNumSymbols = true;
          isMoreSymbols = true;
        }else{
          $("#numbers").css("display", "block");
          $("#symbols").css("display", "none");
          $("#symbols .row:eq(3) .gray:eq(0)").click(callback_number);
          isNumSymbols = true;
          isMoreSymbols = true;
        }
      });

      /* cancel */
      $(".gray-cancel").click(function(){
       var obj_name  = obj_input.attr('name');
       if(obj_name == 'voucher'){
        get_voucher(obj_input);
      }
      if(obj_name == 'customers_pay'){
        cal_price();
      }
      if(obj_name == 'keyword'){
        $('.search_btn').click();
      }   
      if(obj_name == 'quantity'){
        var data_id = $(obj_input).data('id');
        change_total_by_item(data_id, obj_input);
      }         
      $('#keyboard').addClass('hide');
      $('.modal').removeClass('margin_bottom290');
    });
      // delete
      $(".delete_orange").click(function(){
        obj_input.focus();
        var obj_name  = obj_input.attr('name');
        if(obj_name == 'customers_pay'){
          cal_price();
        }
      });
      /* return (line break) */
      for (var l = 0; l < kbdMode.length; l++) {
        $("#" + kbdMode[l] + " .row:eq(3) .gray:eq(1)").click(function(){
          var value = obj_input.val();
          obj_input.focus();
          obj_input.val(value + " ");
        });
      }
    }

    function change_payment_ui(el){
      "use strict";
      var value = $(el).val();
      $('.payment_ui').addClass('hide');
      switch(value){
        case 'kcb_visa_card':
        $('.kcb_visa_card').removeClass('hide');
        break;
        case 'equity_visa_card':
        $('.kcb_visa_card').removeClass('hide');
        break;
      }
    }
    function getCookie(cname) {
      "use strict";
      var name = cname + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var ca = decodedCookie.split(';');
      for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }

    function scroll_tab_list(val, object){
      "use strict";
      var offset_l = $(object).get(0).scrollLeft;
      var width = $(object).width();
      var index_scroll = offset_l + (val*(width-20));
      if(index_scroll<0){
        index_scroll = 0;
      }
      $(object).animate({ scrollLeft: index_scroll }, 500);
      if(index_scroll>offset_l){
        index_scroll = offset_l;
      }
    }

    function alert_float(alert_type, content){
      "use strict";
      var alert = $('.alert_float');
      if(alert_type == 'success'){
        alert.addClass('alert-success').removeClass('alert-danger').find('.content').html(content);
        alert.removeClass('hide');
        setTimeout(function(){ alert.addClass('hide'); },1500);
      }
      else{
        alert.removeClass('alert-success').addClass('alert-danger').find('.content').html(content);
        alert.removeClass('hide');
        setTimeout(function(){ alert.addClass('hide'); },1500);
      }
    }
    var this_obj = $('input[name="customers_pay[]"]').eq(0);
    function get_obj(el){
      "use strict";
      this_obj = $(el);
    }