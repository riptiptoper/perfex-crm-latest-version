(function(){
  "use strict";
  var fnServerParams = {
  }
  initDataTable('.table-trade-discount', admin_url + 'omni_sales/table_trade_discount', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-voucher', admin_url + 'omni_sales/table_voucher', false, false, fnServerParams, [0, 'desc']);

  $('#select-option-client').on('change', function(){
    if($('#select-option-client').is(":checked")) {
      $('.client').removeClass('hide');
      $('.gr-clients').removeClass('col-md-11');
      $('.gr-clients').addClass('col-md-6');
    }else{
      $('.client').addClass('hide');
      $('.gr-clients').removeClass('col-md-6');
      $('.gr-clients').addClass('col-md-11');
    }
  })

  $('#select-option-items').on('change', function(){
    if($('#select-option-items').is(":checked")) {
      $('.item').removeClass('hide');
      $('.gr-items').removeClass('col-md-11');
      $('.gr-items').addClass('col-md-6');
    }else{
      $('.item').addClass('hide');
      $('.gr-items').removeClass('col-md-6');
      $('.gr-items').addClass('col-md-11');
    }
  })
  appValidateForm($('#new_voucher'), {
           'start_time': 'required',
           'end_time': 'required',
           'formal': 'required',
           'discount': 'required',
           'name_trade_discount': 'required',
           'voucher': 'required',
  })

  appValidateForm($('#new_trade_discount_form'), {
           'start_time': 'required',
           'end_time': 'required',
           'formal': 'required',
           'discount': 'required',
           'channel': 'required',
           'name_trade_discount': 'required',
  })

  

  $('[name="channel"]').on('change', function(){
      var val = $(this).val();
      if(val == 3){
        $('.store_woo').removeClass('hide');
        $.post(admin_url+'omni_sales/get_store_woo').done(function(response){
          response = JSON.parse(response);
          $('[name="store"]').html(response);
          $('[name="store"]').selectpicker("refresh", true);
        });
      }else{
        $('.store_woo').addClass('hide');
        $('[name="store"]').html('<option value=""></option>');
        $('[name="store"]').val('');
      }
  })


})(jQuery);
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
function check_start_date(el){
  "use strict";
  var val = $(el).val();
  var fullDate = new Date();

  var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
   
  var currentDate = fullDate.getDate() + "-" + twoDigitMonth + "-" + fullDate.getFullYear();

  var select_date = new Date(val.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
  var cur_date = new Date(currentDate.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));

  if(Math.floor(select_date / 1000) < Math.floor(cur_date / 1000)){
    $(el).val(cur_date);
    alert_float('warning','The start date must be greater than or equal to the current date');
  } 
}