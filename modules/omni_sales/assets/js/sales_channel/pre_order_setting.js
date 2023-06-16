(function(){
  "use strict";   
  $('.channel a').click(function(event){
   var status = $(this).closest('.channel').hasClass('active');
   if(status == false){
    event.preventDefault();
  }
});

  var fnServerParams = {
    "customer_group_filter": "[name='customer_group_filter[]']",
    "customer_filter": "[name='customer_filter[]']"
  }
  initDataTable('.table-add_product_management', admin_url + 'omni_sales/pre_order_product_list_table', [0], [0], fnServerParams, [1, 'desc']);

  $('select[name="product_filter[]"], select[name="customer_group_filter[]"], select[name="customer_filter[]"]').on('change', function(){
    $('.table-add_product_management').DataTable().ajax.reload();
  })
  
  $("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
  });

  $(".channel").each(function(i, obj) {
    $(obj).on("contextmenu",function(){
     return false;
   }); 
  });
  $('#mass_select_all').on('change', function(){
    if($(this).is(':checked')){
      var list = $('.ckb-add-product');
      var list_id = '';
      let length = list.length;
      for(let i = 0; i < length; i++){
        list_id += list.eq(i).data('id');
        if(i+1 < length){
          list_id += ',';
        }
      }
      $("input[name='check_id']").val(list_id);
    }
    else{
      $("input[name='check_id']").val('');
    }
  });
})(jQuery);

function change_active_ch(el){
	"use strict";
	var channel = $(el).data('channel') , status;
	if($(el).is(':checked')){
		$(el).closest('.channel').addClass('active');
		status = 'active';
  }
  else{
    $(el).closest('.channel').removeClass('active'); 
    status = 'deactivate';   	
  }
  var data = {};
  data.channel = channel;
  data.status = status;
  $.post(admin_url+'omni_sales/change_active_channel',data).done(function(response){
    response = JSON.parse(response);
    if(response.success == true) {
     var message = '';
     if(status == 'active'){
      message = 'Activated';
    }
    if(status == 'deactivate'){
      message = 'Deactivated';
    }
    alert_float('success',message);
  }
});
}
function add_product(){
	"use strict";
  $('input[name="id"]').val('');
  $('#chose_product').modal();
  $('input[name="temp_id"]').val('');
  $('.pricefr').addClass('hide');
  $('select[name="group_product_id').val('').change();
  $('select[name="product_id[]"]').val('').change();
  $('select[name="department_id[]"]').val('').change();
  $('.update-title').addClass('hide');
  $('.add-title').removeClass('hide');
}
function get_list_product(el){
	"use strict";
	var id = $(el).val();
  if(id == ''){
    id = 0;
  }
  var channel = $('input[name="channel"]').val();
  $.post(admin_url+'omni_sales/get_list_product/'+id+'/'+channel).done(function(response){
    response = JSON.parse(response);
    if(response.success == true) {
     $('select[name="product_id[]"]').html(response.html);
     $('select[name="product_id[]"]').selectpicker('refresh');
   }
 });
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
function update_product(el){
  "use strict";
  $('input[name="id"]').val($(el).data('id'));
  $('.pricefr').removeClass('hide');

  var customer_group_id = [];
  try {
    jQuery.each($(el).data('customer_group').split(','), function(key, value){
      customer_group_id.push(value);
    });
  }
  catch(err) {
    customer_group_id = $(el).data('customer_group');
  }


  var customer_id = [];
  try {
    jQuery.each($(el).data('customer').split(','), function(key, value){
      customer_id.push(value);
    });
  }
  catch(err) {
    customer_id = $(el).data('customer');
  }

  $('select[name="customer_group[]"]').val(customer_group_id).change();
  $('select[name="customer[]"]').val(customer_id).change();

  var group_product_id = $(el).data('groupid');
  $('select[name="group_product_id').val(group_product_id).change();

    $.post(admin_url+'omni_sales/get_list_product/'+group_product_id).done(function(response){
      response = JSON.parse(response);
      if(response.success == true) {
        $('select[name="product_id[]"]').html(response.html);
        $('select[name="product_id[]"]').selectpicker('refresh');
        var list_product_id = [];
        try {
          list_product_id = $(el).data('productid').split(',');
        }
        catch(err) {
          list_product_id = $(el).data('productid');
        }
        $('select[name="product_id[]"]').val(list_product_id).change();
      }
    });    
  $('#chose_product').modal('show');
  $('.add-title').addClass('hide');
  $('.update-title').removeClass('hide');
}
function staff_bulk_actions(){
  "use strict";
  $('#product-add_product_management').modal('show');
}

function checked_add(el){
  "use strict";
  var id = $(el).data("id");
  if ($(".ckb-add-product").length == $(".ckb-add-product:checked").length) {
    $("#mass_select_all").prop("checked", true);
    var value = $("input[name='check_id']").val();
    if(value != ''){
      value = value + ',' + id;
    }else{
      value = id;
    }
  } else {
    $("#mass_select_all").prop("checked", false);
    var value = $("input[name='check_id']").val();
    var arr_val = value.split(',');
    if(arr_val.length > 0){
      $.each( arr_val, function( key, value ) {
        if(value == id){
          arr_val.splice(key, 1);
          value = arr_val.toString();
          $("input[name='check_id']").val(value);
        }
      });
    }
  }
  if($(el).is(':checked')){
    var value = $("input[name='check_id']").val();
    if(value != ''){
      value = value + ',' + id;
    }else{
      value = id;
    }
    $("input[name='check_id']").val(value);
  }else{
    var value = $("input[name='check_id']").val();
    var arr_val = value.split(',');
    if(arr_val.length > 0){
      $.each( arr_val, function( key, value ) {
        if(value == id){
          arr_val.splice(key, 1);
          value = arr_val.toString();
          $("input[name='check_id']").val(value);
        }
      });
    }
  }
}
