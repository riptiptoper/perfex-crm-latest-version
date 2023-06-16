(function(){
  "use strict";
  $('.add_to_cart,.added_to_cart').click(function(){
    var has_classify = $('input[name="has_classify"]').val();
    if(has_classify == 1){
      var msg = $('input[name="msg_classify"]').val();
      var row_variation = $('.variation-row');
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

    var amount_in_stock = $('input[name="quantity_available"]').val();
    var qtys = $('#quantity').val(), ids = $('input[name="id"]').val();
    if(parseInt(amount_in_stock) < parseInt(qtys)){
      $('#alert_add').modal('show');
      $('.add_success').addClass('hide');
      $('.add_error').removeClass('hide');
      setTimeout(function(){ $('#alert_add').modal('hide'); },1000);
      return false;
    }
    var valid = 1;
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
          }
        }
        else{
          var new_list_qty = [];
          $.each(qty_list, function( key, value ) {
            if(index_id == key){
              var checks_qtys = 0; 
              if(qtys != ''){
                if(qtys >= 0){  
                  checks_qtys = parseInt(value)+parseInt(qtys);                     
                  new_list_qty.push(checks_qtys);                            
                }
                else{
                  checks_qtys = parseInt(value)+1;                     
                  new_list_qty.push(checks_qtys);                           
                }
              }
              else{
                checks_qtys = parseInt(value)+1;                     
                new_list_qty.push(parseInt(value)+1);                          
              }
              if(parseInt(checks_qtys) > parseInt(amount_in_stock)){
                valid = 0;                      
              }
            }
            else{
              new_list_qty.push(value);
            }                    
          });

          if(valid == 1){
            add_to_cart(id_list,new_list_qty);
          }
        }
      }
      else{
        var id_list = [ids];
        var qtys_list = [qtys];
        add_to_cart(id_list,qtys_list);
        $('#alert_add').modal('show');
        $('.add_success').removeClass('hide');
        $('.add_error').addClass('hide');
        setTimeout(function(){ $('#alert_add').modal('hide'); },1000);
        valid = 1;
      }
    }   
    count_product_cart();
    if(valid == 0){
      $('#alert_add').modal('show');
      $('.add_success').addClass('hide');
      $('.add_error').removeClass('hide');
      setTimeout(function(){ $('#alert_add').modal('hide'); },1000);
      return false;
    }
    else{
      $('#alert_add').modal('show');
      $('.add_success').removeClass('hide');
      $('.add_error').addClass('hide');
      setTimeout(function(){ $('#alert_add').modal('hide'); },1000);
    }
  });  

  $(window).on('load', function() {  
    count_product_cart();
  });
  $('.variation-items .product-variation').click(function(){
    var this_obj = $(this);
    if(!this_obj.hasClass('selected')){
      this_obj.parent().find('.product-variation').removeClass('selected');
      this_obj.addClass('selected');
    }else{
      this_obj.removeClass('selected');
    }
    $('input[name="quantity_available"]').val('0');
    $('input[name="id"]').val('0');
    $('#amount_available').text('0');
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
    get_data_variation_product($('input[name="parent_id"]').val(), option_list);
  }
});

})(jQuery);
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
function scroll_slide(val){
  "use strict";
  var offset_l = $('#frameslide').get(0).scrollLeft;
  var width = $('#frameslide').width();
  var index_scroll = offset_l + (val*(width-80));
  if(index_scroll<0){
    index_scroll = 0;
  }
  $('#frameslide').animate({ scrollLeft: index_scroll }, 1000);
  if(index_scroll>offset_l){
    index_scroll = offset_l;
  }
}
function change_qty(val){
  "use strict";
  var qty = $('#quantity').val();
  var newQty = parseInt(qty)+parseInt(val);
  if(newQty<1){
    newQty = 1;
  }
  $('#quantity').val(newQty);
}

function get_data_variation_product(product_id, option_list){
  var token_hash = $('input[name="token_hash"]').val();
  $.ajax({
   url: site_url+"omni_sales/omni_sales_client/get_product_variation",
   type: "post",
   data: {'csrf_token_name':token_hash,'product_id':product_id,'option_list':option_list},
   success: function(){

   },
   error:function(){

   }
 }).done(function(response) {
   response = JSON.parse(response);
   if(response.product_id == ''){
     $('.product-title').removeClass('hide');
     $('.product-title.sub').addClass('hide');

     $('.product-description').removeClass('hide');
     $('.product-description.sub').addClass('hide');

     $('.new-price').removeClass('hide');
     $('.new-price.sub').addClass('hide');

     $('.long_descriptions').removeClass('hide');
     $('.long_descriptions.sub').addClass('hide');

     $('input[name="quantity_available"]').val('0');
     $('input[name="id"]').val('0');
     $('#amount_available').text('0');
   }
   else{
     $('.product-title').addClass('hide');
     $('.product-title.sub').removeClass('hide').text(response.product_name);

     $('.product-description').addClass('hide');
     $('.product-description.sub').removeClass('hide').html(response.description);

     $('.new-price').addClass('hide');
     $('.new-price.sub').removeClass('hide').text(response.rate);

     $('.long_descriptions').addClass('hide');
     $('.long_descriptions.sub').removeClass('hide').html(response.long_description);

     $('input[name="quantity_available"]').val(response.w_quantity);
     $('input[name="id"]').val(response.product_id);

     $('.amount_available').removeClass('hide');
     $('#amount_available').text(response.w_quantity);

     $('.preview .contain_image img').attr('src',response.image_url);
   }
 }); 
}