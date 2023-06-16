(function(){
  "use strict";
  $(document).on("click",".add_cart, .added",function() {
    var ids = $(this).attr('data-id');
    var parent = $(this).parent();
    // For variation 
    var has_variation = parent.find('input[name="has_variation"]').val();
    if(has_variation == 1){
      var image = $(this).parents('.product-cell').find('.product-image img').attr('src');
      var name = $(this).parents('.product-cell').find('.product-content').html();
      $('#select_variation').find('.content').html('');
      $('#select_variation').find('.image img').attr('src',image);
      $('#select_variation').find('.prices').html(name);
      var sub_prices = '<span class="price sub hide"></span>';
      $('#select_variation').find('.prices').append(sub_prices);

      $('#select_variation').find('input[name="parent_id"]').val(ids);
      $('#select_variation').modal('show');
      get_variation_list(ids);
      return false;
    }
    else{
      var checking_classify = parent.find('input[name="check_classify"]').val();
      if(checking_classify == 1){
        var msg = $('input[name="msg_classify"]').val();
        var row_variation = $('#select_variation .variation-row');
        let count_row = row_variation.length;
        let count_effect = 0;
        for(let i=0;i<count_row;i++){
          var row = row_variation.eq(i);
          var find_selected = row.find('.selected');
          row.find('.variation-items .alert-variation').remove();
          var variation_name = row.find('.variation-items label').text();
          if(find_selected.length == 1){
            count_effect++;
          }
          else{
            row.find('.variation-items').append('<div class="alert-variation mtop5"><span class="text-danger">'+msg+' '+variation_name+'</span></div>');
          }
        }
        if(count_effect != count_row){
          return false;
        }
      }
    }
    // For variation 
    var qty_obj = parent.find('.qty');
    var qtys = qty_obj.val();
    var w_quantity = qty_obj.attr('data-w_quantity');
    if(w_quantity == 0){
      alert_float('warning', $('input[name="msg_amount_not_available"]').val());
      return false;
    }
    var cart_id_list = getCookie('cart_id_list'), cart_qty_list;
    if(typeof cart_id_list != ""){
      if(cart_id_list.trim()){
        var id_list = JSON.parse('['+cart_id_list+']');
        cart_qty_list = getCookie('cart_qty_list');
        var qty_list = JSON.parse('['+cart_qty_list+']');
        var index_id = -1;
        $.each(id_list, function( key, value ) {
          if(value == ids){
            index_id = key;
          }
        }); 
        if(index_id == -1){
          if(ids != '' &&qtys != ''){
            id_list.push(ids);
            qty_list.push(qtys);
            add_to_cart(id_list,qty_list);
            alert_float('success', $('input[name="msg_add"]').val());
          }
        }
        else{
          var new_list_qty = [];
          var enoungh = 1;
          $.each(qty_list, function( key, value ) {
            if(index_id == key){
              var temp_qty = 0;
              if(qtys != ''){
                temp_qty = parseInt(value)+parseInt(qtys);
                if(temp_qty > w_quantity){
                  enoungh = 0;
                  temp_qty = w_quantity;                           
                }
                new_list_qty.push(temp_qty);                        
              }
              else{
                temp_qty = parseInt(value)+1;   
                if(temp_qty > w_quantity){
                  enoungh = 0;
                  temp_qty = w_quantity;                         
                }                     
                new_list_qty.push(temp_qty);
              }  
            }
            else{
              new_list_qty.push(value);
            }
          });
          add_to_cart(id_list,new_list_qty);
          if(enoungh == 0){
            alert_float('warning', $('input[name="msg_amount_not_available"]').val());
          }
          else{
            alert_float('success', $('input[name="msg_add"]').val());
          }
        }
      }
      else{
        var id_list = [ids];
        var qtys_list = [qtys];
        add_to_cart(id_list,qtys_list);
        alert_float('success', $('input[name="msg_add"]').val());
      }
    }
    count_product_cart();
  });


$(window).on('load', function() {  
  count_product_cart();
});
$('.btn_page').click(function(){
  $('.btn_page').removeClass('active');
  $(this).addClass('active');
  $('.product_list').html(''); 
  var page = $(this).data('page');
  var group_id = $('input[name="group_id"]').val();
  var keyword = $('input[name="keyword"]').val();
  if(keyword != ''){
    keyword = '/'+keyword;
  }
  ChangeUrlWithIndex(page,group_id);
  if(page!=''){
    $.post(site_url+'omni_sales/omni_sales_client/get_product_by_group/'+page+'/'+group_id+'/0'+keyword).done(function(response){
      response = JSON.parse(response);
      $('.product_list').html(response.data);
    });   
  }
});

$(document).on("click","#select_variation .variation-items .product-variation",function() {
  var this_obj = $(this);
  if(!this_obj.hasClass('selected')){
    this_obj.parent().find('.product-variation').removeClass('selected');
    this_obj.addClass('selected');
  }else{
    this_obj.removeClass('selected');
  }
  $('#select_variation input[name="qty"]').attr('data-w_quantity', '0').attr('max', '0');
  $('#select_variation .add_cart').attr('data-id', '0');
  $('#select_variation #amount_available').text('0');
  var option_list = [];
  var row_variation = $('.variation-row');
  let count_row = row_variation.length;
  let count_effect = 0;
  for(let i=0;i<count_row;i++){
    var row = row_variation.eq(i);
    var find_selected = row.find('.selected');
    if(find_selected.length == 1){
     var variation_item = {};
     variation_item.variation_name = row.find('label').text();
     variation_item.variation_option = find_selected.text().trim();
     option_list.push(variation_item);
     count_effect++;
   }
 }
 if(count_effect == count_row){
   get_data_variation_product($('#select_variation input[name="parent_id"]').val(), option_list);
 }
});

})(jQuery);
function ChangeUrlWithIndex(page, group_id) {
  "use strict";
  var url = window.location.href, url = url.split("/");
  var keyword = $('input[name="keyword"]').val();
  if(keyword != ''){
    keyword = '/'+keyword;
  }
  url = url[0]+'/'+url[1]+'/'+url[2]+'/'+url[3]+'/'+url[4]+'/'+url[5]+'/'+page+'/'+group_id+'/0'+keyword;
  window.history.pushState({}, document.title, url);
}
function add_to_cart(cart_id_list,cart_qty_list){
  "use strict";
  add_cookie('cart_id_list',cart_id_list,30);
  add_cookie('cart_qty_list',cart_qty_list,30);
}
function add_cookie(cname, cvalue, exdays) {
  "use strict";
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
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

function count_product_cart(){
  "use strict";
  var cart_qty_list = getCookie('cart_qty_list'),count = 0;
  if(cart_qty_list.trim()){
    var qty_list = JSON.parse('['+cart_qty_list+']');
    $.each(qty_list, function( key, value ) {
      count+=value;
    });   
  }
  if(count > 0){
    $('.qty_total').text(count).fadeIn(500);
  }
  else{
    $('.qty_total').text('').fadeOut(500);
  }
}

function numberWithCommas(x) {
  "use strict";
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function get_variation_list(product_id){
  $.get(site_url+"omni_sales/omni_sales_client/get_variation_list/"+product_id).done(function(response){
    response = JSON.parse(response);
    if(response != ''){
      $('#select_variation').find('.content').html(response);
    }
  });
}

function get_data_variation_product(product_id, option_list){
  var data = {};
  data.product_id = product_id;
  data.option_list = option_list;
  $.post(site_url+"omni_sales/omni_sales_client/get_product_variation",data).done(function(response){
    response = JSON.parse(response);
    if(response.product_id == ''){
      $('#select_variation input[name="qty"]').attr('data-w_quantity', '0').attr('max', '0');
      $('#select_variation .add_cart').attr('data-id', '0');
      $('#select_variation #amount_available').text('0');
      $('#select_variation').find('.prices .price').removeClass('hide');
      $('#select_variation').find('.prices .price.sub').addClass('hide');
    }
    else{
      $('#select_variation input[name="qty"]').attr('data-w_quantity', response.w_quantity).attr('max', response.w_quantity);
      $('#select_variation .add_cart').attr('data-id', response.product_id);
      $('#select_variation #amount_available').text(response.w_quantity);
      $('#select_variation').find('.prices .price').addClass('hide');
      $('#select_variation').find('.prices .price.sub').removeClass('hide').text(response.rate);
    }
  });
}