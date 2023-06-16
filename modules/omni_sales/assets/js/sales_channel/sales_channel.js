(function(){
  "use strict";   
  $('.channel a').click(function(event){
   var status = $(this).closest('.channel').hasClass('active');
   if(status == false){
    event.preventDefault();
  }
});

  var fnServerParams = {
    "product_filter": "[name='product_filter[]']",
    "department_filter": "[name='department_filter[]']",
    "id_channel": "[name='sales_channel_id']",
    "channel": "[name='channel']"
  }
  initDataTable('.table-add_product_management', admin_url + 'omni_sales/add_product_management_table', [0], [0], fnServerParams, [1, 'desc']);
  $('select[name="product_filter[]"], select[name="department_filter[]"]').on('change', function(){
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

  appValidateForm($('#form_add_product'), {
   'product_id[]': 'required',
 })

  $(".channel").each(function(i, obj) {
    $(obj).on("contextmenu",function(){
     return false;
   }); 
  });

  //selecte all
  $('#mass_select_all').on('click', function(){
    var favorite = [];
    if($(this).is(':checked')){
      $('.individual').attr('checked', this.checked);
      $.each($(".ckb-add-product"), function(){ 
          favorite.push($(this).data('id'));
      });
    }else{
      $('.individual').removeAttr('checked');
      favorite = [];
    }

    $("input[name='check_id']").val(favorite);
  })
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
  $('select[name="group_product_id"]').val($(el).data('groupid')).change();

  var department_id = [];
  try {
    jQuery.each($(el).data('department').split(','), function(key, value){
      department_id.push(value);
    });
  }
  catch(err) {
    department_id = $(el).data('department');
  }
  $('select[name="department_id[]"]').val(department_id).change();
  $.post(admin_url+'omni_sales/get_list_product/'+$(el).data('groupid')).done(function(response){
    response = JSON.parse(response);
    if(response.success == true) {
      $('select[name="product_id[]"]').html(response.html);
      $('select[name="product_id[]"]').selectpicker('refresh');
      $('select[name="product_id[]"]').val([$(el).data('productid')]).change();
      var prices = $(el).data('prices').substring(0,$(el).data('prices').length - 3);
      $('input[name="prices"]').val(prices);
    }
  });    
  $('#chose_product').modal($(el).data('productid'));
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

function price_update(){
  var channel =  $("input[name='sales_channel_id']").val();
  var check_detail =  $("input[name='check_id']").val();
  var arr_val = check_detail.split(',');
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.channel = channel;
      data.arr_val = arr_val;
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      setTimeout(function() {
        $('#box-loadding').html('');
        alert_float('warning', 'The price update process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/portal_price_update', data).done(function(response){
        response = JSON.parse(response);
        if(response){
          $('#box-loadding').html('');
          alert_float('success', 'Update successfully');
          $('.table-add_product_management').DataTable().ajax.reload();
        }else{
          $('#box-loadding').html('');
          alert_float('warning', 'Update unsuccessful');
        }
      });
  }else{
    Confirm('Confirm price updates for all products', 'Are you sure to update prices for all products?', 'Yes', 'Cancel', channel,'price_update'); /*change*/
  }
}


function Confirm(title, msg, $true, $false, channel ,type = "") { 
  /*change*/
  var content =  "<div class='dialog-ovelay'>" +
                "<div class='dialog'><header>" +
                 " <h3> " + title + " </h3> " +
                 "<i class='fa fa-close'></i>" +
             "</header>" +
             "<div class='dialog-msg'>" +
                 " <p> " + msg + " </p> " +
             "</div>" +
             "<footer>" +
                 "<div class='controls'>" +
                     " <button class='button button-danger doAction'>" + $true + "</button> " +
                     " <button class='button button-default cancelAction'>" + $false + "</button> " +
                 "</div>" +
             "</footer>" +
          "</div>" +
        "</div>";
  $('#popup_confirm').prepend(content);

  if(type == ""){
    $('.doAction').click(function () {
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      setTimeout(function() {
        $('#box-loadding').html('');
        alert_float('warning', 'The price update process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/portal_price_update/'+channel).done(function(response){
            $('#box-loadding').html('');
            alert_float('success', 'Update successfully');
            $('.table-add_product_management').DataTable().ajax.reload();
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
      $(this).parents('.dialog-ovelay').fadeOut(500, function () {
        $(this).remove();
      });
    });

    $('.cancelAction, .fa-close').click(function () {
      $(this).parents('.dialog-ovelay').fadeOut(500, function () {
        $(this).remove();
      });
    });
      
  }else if(type == "price_update"){
    $('.doAction').click(function () {
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      setTimeout(function() {
        $('#box-loadding').html('');
        alert_float('warning', 'The price update process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/portal_price_update/'+channel).done(function(response){
            $('#box-loadding').html('');
            alert_float('success', 'Update successfully');
            $('.table-add_product_management').DataTable().ajax.reload();
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
      $(this).parents('.dialog-ovelay').fadeOut(500, function () {
        $(this).remove();
      });
    });

    $('.cancelAction, .fa-close').click(function () {
      $(this).parents('.dialog-ovelay').fadeOut(500, function () {
        $(this).remove();
      });
    });
  }
  
}