(function(){
  "use strict";

  var requisitionServerParams = {
    "order_id": "[name='order_id']"
  };
  var table_refund_list = $('.table-refund_list');
  initDataTable(table_refund_list, admin_url + 'omni_sales/table_refund', [0], [0],requisitionServerParams, [0, 'asc']);

  $('.change_status').click(function(){
   var status = $(this).data('status'), order_number;
   order_number = $('input[name="order_number"]').val();
   if(status == 8){
    $('#chosse').modal();
    return false;
  }
  var data = {};
  data.cancelReason = '';
  data.status = status;
  change_status(order_number, data);      
}); 
  $('.cancell_order').click(function(){
    $('#chosse').modal('hide');
    var status = $(this).data('status'), order_number;
    order_number = $('input[name="order_number"]').val();
    var data = {};
    data.cancelReason = $('textarea[name="cancel_reason"]').val();
    data.status = status;
    change_status(order_number, data);
  });  

  // Manually add goods delivery activity
  $("#wh_enter_activity").on('click', function() {
    "use strict"; 
    var message = $('#wh_activity_textarea').val();
    var goods_delivery_id = $('input[name="goods_delivery_id"]').val();
    if (message === '') {
      alert_float('danger', 'Please enter activity');
      return; 
    }
    if (goods_delivery_id === '') { return; }
    $.post(admin_url + 'omni_sales/wh_add_activity', {
      goods_delivery_id: goods_delivery_id,
      activity: message,
      rel_type: 'omni_order',
    }).done(function(response) {
      response = JSON.parse(response);
      if(response.status == true){
        alert_float('success', response.message);
        location.reload();
      }else{
        alert_float('danger', response.message);
      }
    }).fail(function(data) {
      alert_float('danger', data.message);
    });
  });

  $('.reject_order').on('click', function(){
    var reason = $('textarea[name="return_reason"]').val();
    if(reason != ''){
      change_status_return_order(-1);
    }
  });

  $('.create_import_stock').on('click', function(){
    $('#create_import_stock_modal').modal('show');
  });

  $('.create_import_stock_btn').on('click', function(){
    var order_id = $('input[name="order_id"]').val();
    var warehouse_id = $('#create_import_stock_modal #warehouse_id').val();
    if(warehouse_id == ''){
      alert_float('danger', $('input[name="please_select_a_warehouse"]').val());
    }
    else{
      window.location.href = admin_url+"omni_sales/create_import_stock/"+order_id+'/'+warehouse_id;
    }
  });

  $('.btn_add_refund').on('click', function(){
    $('#create_refund_modal .edit-title').addClass('hide');
    $('#create_refund_modal .add-title').removeClass('hide');
    get_refund_modal_content('');
 });
})(jQuery);
function change_status(order_number,data){
  "use strict";
  $.post(admin_url+'omni_sales/admin_change_status/'+order_number,data).done(function(response){
   response = JSON.parse(response);
   if(response.success == true) {
    alert_float('success','Status changed');
    setTimeout(function(){location.reload();},1500);
  }

});
}
function inventory_check(order_number){
  "use strict";
  $.get(admin_url+'omni_sales/preview_inventory_check/'+order_number).done(function(response){
   response = JSON.parse(response);
   if(response.success == true) {
    $('#inventory_check').modal('show');
    $('.inventory_check_table tbody').html(response.html);
    if(response.active_convert_button == 1){
      $('#form_create_purchase_request button[type="submit"]').removeAttr('disabled');
    }
    else{
      $('#form_create_purchase_request button[type="submit"]').attr('disabled', 'disabled');
    }
  }
});
}



function delete_wh_activitylog(wrapper, id) {
  "use strict"; 

  if (confirm_delete()) {
    requestGetJSON('warehouse/delete_activitylog/' + id).done(function(response) {
      if (response.success === true || response.success == 'true') { $(wrapper).parents('.feed-item').remove(); }
    }).fail(function(data) {
      alert_float('danger', data.responseText);
    });
  }
}


function approve_return_order(status){
  "use strict";
  if(status == 1){
    if (confirm($('input[name="are_you_sure_you_want_to_accept_returns"]').val()) == true) {
      change_status_return_order(status);
    }
  }
  else{
    $('#reject_reason').modal('show');
  }
}

function change_status_return_order(status){
  "use strict";
    var data = {};
    data.order_id = $('input[name="order_id"]').val();
    data.status = status;
    data.cancel_reason = $('textarea[name="return_reason"]').val();
    $.post(admin_url+'omni_sales/change_status_return_order',data).done(function(response){
     response = JSON.parse(response);
     if(response.success == true) {
         alert_float('success', response.message);
     }
     else{
         alert_float('danger', response.message);
     }
     setTimeout(function(){location.reload();},1500);     
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

function get_refund_modal_content(refund_id){
  "use strict";
  var order_id = $('input[name="order_id"]').val();
   $.get(site_url+'omni_sales/get_refund_modal_content/'+order_id+'/'+refund_id, function(response){
    $('#create_refund_modal').modal('show');
    $('#create_refund_modal .modal-body').html(response);
    $('.selectpicker').selectpicker('refresh');
    init_datepicker();
    appValidateForm($('#omni_sale_refund_form'), {
     'amount': 'required',
     'refunded_on': 'required',
     'payment_mode': 'required'
   })
  });
}

function edit_refund(refund_id){
  "use strict";
  $('#create_refund_modal .edit-title').removeClass('hide');
  $('#create_refund_modal .add-title').addClass('hide');
  get_refund_modal_content(refund_id);
}


