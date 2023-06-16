(function(){
  "use strict";
  $('[name="voucher"]').on('change', function(){
    var voucher = $(this).val();
    if(voucher != ''){
      var id = $('input[name="userid"]').val();
      var data = {};
      data.voucher = voucher; 
      data.client = id; 
      data.channel = 2;
      $.post(site_url + 'omni_sales/omni_sales_client/voucher_apply', data).done(function(response){
        response = JSON.parse(response);
        if(response[0] != null){
          var  test = 0;
          if(parseFloat(response[0].minimum_order_value)>0){
            test = 1;
            if(sub_total >= parseFloat(response[0].minimum_order_value)){
              test = 0;
            }
          }
          if(test == 0){
            $('input[name="discount_type"]').val(response[0].formal);
            if(response[0].formal == 1){
              var total_price_discount = parseFloat(((sub_total * response[0].discount)/100));
              var total_cal = sub_total - total_price_discount + parseFloat(tax);
              $('input[name="discount_total"]').val(total_price_discount);  
              $('input[name="discount"]').val(response[0].discount); 
            }
            if(response[0].formal == 2){
              var total_price_discount = parseFloat(response[0].discount);
              var total_cal = sub_total - total_price_discount + parseFloat(tax);
              $('input[name="discount_total"]').val(total_price_discount);           
              $('input[name="discount"]').val(response[0].discount);
            }
            total_cart();  
            alert_float('success','Voucher applied');                  
          }
          else{
           alert_float('warning','Your order is not eligible for this code');  
           $('input[name="discount_total"]').val('0');           
           $('input[name="discount"]').val('0');
           total_cart();                 
           $(this).val('')
         }
       }else{
        alert_float('warning', 'voucher does not exist');
        $('input[name="discount_total"]').val('0');           
        $('input[name="discount"]').val('0');
        total_cart(); 
        $(this).val('')
      }
    })
    }  
    else{
      $('input[name="discount_total"]').val('0');           
      $('input[name="discount"]').val('0');
      total_cart();   
    } 
  });

  if(discount_price && discount && discount_type && tax != 0){

    var kq_discount = (discount * sub_total)/100;
    var kq_discount_price = discount_price;
    var kq_tax = tax;
    var final_dis = Number(kq_discount) + Number(kq_discount_price);
    var total_cal = Number(sub_total) - (Number(kq_discount) + Number(kq_discount_price)) + Number(kq_tax);

    $('input[name="discount"]').val(final_dis);

    $('.promotions_discount_price').html('-' + numberWithCommas(final_dis));
    $('.promotions_tax_price').html(numberWithCommas(round(kq_tax)));
    $('.total').html(numberWithCommas(total_cal));
    return true;
  }

  
  get_trade_discount();

})(jQuery);

var discount = $('input[name="discount"]').val();
var discount_type = $('input[name="discount_type"]').val();
var sub_total = $('input[name="sub_total"]').val();
var total = $('input[name="total"]').val();
var discount_price = $('input[name="discount_price"]').val();
var tax = $('input[name="tax"]').val();
var list_discount = [];


function get_trade_discount(){
  "use strict";
  var id = $('input[name="userid"]').val();
  var list_id = $('input[name="list_id_product"]').val();
  if(id != ''){
    if(list_id != ''){
      var data = {};
      data.id = id;
      data.list_id = list_id;
      data.channel = 2;
      $.post(site_url+'omni_sales/omni_sales_client/get_trade_discount',data).done(function(response){
        response = JSON.parse(response);
        for(var i = 0; i < response[0].length;i++){               
          list_discount.push({item:response[0][i]['items'], formal:response[0][i]['formal'],group_list:response[0][i]['group_items'], discount:response[0][i]['discount'], voucher:response[0][i]['voucher'], minimum_order_value:response[0][i]['minimum_order_value']});
        }
        total_cart();
      });
    } 
    else{
      total_cart();
    }  
  } 
}

function get_discount_client(total){
  "use strict";
  var result = 0;
  if($('input[name="userid"]').val() != ''){
    console.log(list_discount);
    $.each(list_discount, function( key, value ) {
      if(value['item']=='' && value['group_list']==''){
        if(parseFloat(value['minimum_order_value'])>0){
          if(total>=parseFloat(value['minimum_order_value'])){
            if(parseInt(value['formal']) == 1){

              result += total * value['discount'] / 100;
            }
            else{

              result += parseFloat(value['discount']);                          
            }
          }                    
        }
        else{
          if(parseInt(value['formal']) == 1){
            result += total * value['discount'] / 100;
          }
          else{
            result += parseFloat(value['discount']);                          
          }
        }
      }
      else{
        var array = value['item'].split(',');
        var array_group = value['group_list'].split(',');

        var list_id_product_cart =  $('input[name="list_id_product"]').val();     
        var list_group_product =  $('input[name="list_group_product"]').val();     
        var list_id = JSON.parse('['+list_id_product_cart+']');
        var list_group_product = JSON.parse('['+list_group_product+']');


        $.each(list_id, function( k, idp ) {
         var gid = list_group_product[k];   
         if(array.includes(idp.toString()) || array_group.includes(gid.toString())){

          $('.prices_'+idp.toString()+' .price_w2').addClass('hide');
          $('.prices_'+idp.toString()+' .price_w1').removeClass('hide');
          var data_info_item = get_infor_item(parseInt(idp));
          var price_discount = 0;
          var price_discount2 = 0;

          if(parseFloat(value['minimum_order_value'])>0){
            if(total>=parseFloat(value['minimum_order_value'])){
              if(parseInt(value['formal']) == 1){
                var discount_item = parseFloat(data_info_item.prices) * value['discount'] / 100;
                var discount_item2 = parseFloat(data_info_item.true_prices) * value['discount'] / 100;
                price_discount = parseFloat(data_info_item.prices) - discount_item; 
                price_discount2 = parseFloat(data_info_item.true_prices) - discount_item2; 
                result += discount_item * parseInt(data_info_item.qty);  
              }
              else{
                price_discount = parseFloat(data_info_item.prices) - value['discount'];                                  
                price_discount2 = parseFloat(data_info_item.true_prices) - value['discount'];                                  
                result += parseFloat(value['discount']) * parseFloat(data_info_item.qty);
              }
            }
          }
          else{
            if(parseInt(value['formal']) == 1){
              var discount_item = parseFloat(data_info_item.prices) * value['discount'] / 100;
              var discount_item2 = parseFloat(data_info_item.true_prices) * value['discount'] / 100;
              price_discount = parseFloat(data_info_item.prices) - discount_item; 
              price_discount2 = parseFloat(data_info_item.true_prices) - discount_item2; 
              console.log(data_info_item.true_prices);
              result += discount_item * parseInt(data_info_item.qty); 
            }
            else{
              price_discount = parseFloat(data_info_item.prices) - value['discount'];                                  
              price_discount2 = parseFloat(data_info_item.true_prices) - value['discount'];                                  
              result += parseFloat(value['discount']) * parseFloat(data_info_item.qty);  
            }
          }
          if(parseFloat(price_discount) > 0){
           $('.prices_'+idp.toString()+' .price_w2').removeClass('hide'); 
           var new_price = numberWithCommas(price_discount2);
           $('.prices_'+idp.toString()+' .price_w2 .new_prices').text(new_price); 
           $('.prices_'+idp.toString()+' .price_w1').addClass('hide');
         }
       }    

     });
      }        
    });      
}
return result;
}
function total_cart(){
  "use strict"; 
  var new_discount_voucher = 0;
  var discount_type = $('input[name="discount_type"]').val();
  var discount = $('input[name="discount"]').val();
  var other_discount = $('input[name="other_discount"]').val();
  var shipping = $('input[name="shipping"]').val();
  if(shipping == ''){
    shipping = 0;
  }
  
  if(discount_type == 1){
    new_discount_voucher = sub_total * discount / 100;
  }
  if(discount_type == 2){
    new_discount_voucher = discount;
  }
  var discount = round(parseFloat(new_discount_voucher) +  parseFloat(get_discount_client(sub_total))) + parseFloat(other_discount);
  $('input[name="discount"]').val(discount);
  $('.promotions_discount_price').html('-' + numberWithCommas(discount));

  var total_s = round(parseFloat(sub_total) + parseFloat(tax) - parseFloat(discount) + parseFloat(shipping));
  $('input[name="total"]').val(total_s);
  $('.total').html(numberWithCommas(total_s));
  $('.total_payable').html(numberWithCommas(total_s));
}
function numberWithCommas(x) {
  "use strict";
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function get_infor_item(id){
  "use strict";
  var data_result = {};
  var list_id = $('input[name="list_id_product"]').val();
  var list_qty = $('input[name="list_qty_product"]').val();
  var list_price = $('input[name="list_prices_product"]').val();  
  var list_tax = $('input[name="list_tax_product"]').val();  
  if(list_id != ''){
    var id_list = JSON.parse('['+list_id+']');
    var qty_list = JSON.parse('['+list_qty+']');
    var price_list = JSON.parse('['+list_price+']');
    var tax_list = JSON.parse('['+list_tax+']');

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

    var tax = 0;
    $.each(tax_list, function( key, value ) {
      if(index_id == key){
        tax = value;
        return false;
      }           
    });
    data_result.true_prices = prices;
    data_result.qty = qty;
    if(tax > 0){
      prices = prices * (1 + (tax/100));
    }
        // Prices with tax
        data_result.prices = prices;
        return data_result;
      }
      return false;
    }
    function removeCommas(str) {
      "use strict";
      return(str.replace(/,/g,''));
    }
    function round(val){
      "use strict";
      return Math.round(val * 100) / 100;
    }