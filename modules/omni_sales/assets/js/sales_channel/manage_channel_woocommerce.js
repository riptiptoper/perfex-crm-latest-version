(function(){
  "use strict";
  var fnServerParams = {
    "id_store" : "input[name='id']"
  }

  initDataTable('.table-channel-woocommerce', admin_url + 'omni_sales/table_channel_woocommerce', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-product-woocommerce', admin_url + 'omni_sales/table_product_woocommerce', '', '', fnServerParams);
  initDataTable('.table-store_sync_v2', admin_url + 'omni_sales/table_store_sync_v2', false, false, fnServerParams, [0, 'desc']);

  $('#add_channel_woocommerce').click(function(){
    $('.add-title').removeClass('hide');
    $('.update-title').addClass('hide');
    $('.test_connect').addClass('hide');
    $('#channel_woocommerce').modal('show');
    $('input[name="id"]').val('');
    $('input[name="name_channel"]').val('');
    $('input[name="consumer_key"]').val('')
    $('input[name="consumer_secret"]').val('');
  })
  appValidateForm($('#form_add_channel_woocommerce'), {
           'name_channel': 'required',
           'consumer_key': 'required',
           'consumer_secret': 'required',
           'url': 'required'
  });


  $('.sync_products_woo').click(function(){
    $('.status-sync').removeClass('label-primary');
    $('.status-sync').addClass('label-danger');
    $('.status-sync').text('Wait for sync');
    var id = $(this).data('id');
    var check_detail =  $("input[name='check']").val();
    var arr_val = check_detail.split(',');
    if(arr_val.length > 0 && arr_val[0] != ""){
        var data = {};
        data.id = id;
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
        }, 60*1000);
        $.post(admin_url+'omni_sales/sync_products_to_store_detail/', data).done(function(response){
         $('.status-sync').removeClass('label-danger');
          $('.status-sync').addClass('label-success');
          $('.status-sync').text('Sync success');
          response = JSON.parse(response);
          if(response){
            $('#box-loadding').html('');
            alert_float('success', 'Sync successfully');
          }else{
            $('#box-loadding').html('');
            alert_float('warning', 'Sync unsuccessful');
          }
        }).fail(function(data) {
          $('#box-loadding').html('');
        });
    }else{
      Confirm('Product synchronization confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id); /*change*/
    }
  })

  $('.sync_products_from_woo').click(function(){
    $('.status-sync').removeClass('label-primary');
    $('.status-sync').addClass('label-danger');
    $('.status-sync').text('Wait for sync');
    var id = $(this).data('id');
    var html = '';
    html += '<div class="Box">';
    html += '<span>';
    html += '<span></span>';
    html += '</span>';
    html += '</div>';
    $('#box-loadding').html(html);
    setTimeout(function() {
      $('#box-loadding').html('');
      alert_float('warning', 'The synchronization all process can take a long time to complete');
    }, 60*1000);
    $.post(admin_url+'omni_sales/process_asynclibrary_info_full/'+id).done(function(response){
      $('.status-sync').removeClass('label-danger');
      $('.status-sync').addClass('label-success');
      $('.status-sync').text('Sync success');
      $('#box-loadding').html('');
      $('.table-product-woocommerce').DataTable().ajax.reload();
      alert_float('success', 'Sync successfully');
    }).fail(function(data) {
      $('#box-loadding').html('');
    });
  })

  $('.sync_products_from_info_woo').click(function(){
    $('.status-sync').removeClass('label-primary');
    $('.status-sync').addClass('label-danger');
    $('.status-sync').text('Wait for sync');
    var id = $(this).data('id');
    var html = '';
    html += '<div class="Box">';
    html += '<span>';
    html += '<span></span>';
    html += '</span>';
    html += '</div>';
    $('#box-loadding').html(html);
    setTimeout(function() {
      $('#box-loadding').html('');
    }, 60*1000);
    $.post(admin_url+'omni_sales/process_asynclibrary_info_basic/'+id).done(function(response){
      $('#box-loadding').html('');
      $('.table-product-woocommerce').DataTable().ajax.reload();
      $('.status-sync').removeClass('label-danger');
      $('.status-sync').addClass('label-success');
      $('.status-sync').text('Sync success');
      alert_float('success', 'Sync successfully');
    }).fail(function(data) {
      $('#box-loadding').html('');
    });
    
  })

  $('.test_connect').click(function(){
    var url = $('input[name="url"]').val();
    var consumer_key = $('input[name="consumer_key"]').val();
    var consumer_secret = $('input[name="consumer_secret"]').val();
    var html = '';
    html += '<div class="Box">';
    html += '<span>';
    html += '<span></span>';
    html += '</span>';
    html += '</div>';
    $('#box-loadding').html(html);
    var data = {};
    data.url = url;
    data.consumer_key = consumer_key;
    data.consumer_secret = consumer_secret;
    setTimeout(function() {
      $('#box-loadding').html('');
    }, 60*1000);
    $.post(admin_url+'omni_sales/test_connect', data).done(function(response){
      response = JSON.parse(response);
      if(response.check == true){
        alert_float('success', response.message);
      }else{
        alert_float('warning', response.message);
      }
      $('#box-loadding').html('');
    }).fail(function(data) {
      $('#box-loadding').html('');
    });
  })
$("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
 });
//----- OPEN
    $('[data-popup-open]').on('click', function(e)  {
        var targeted_popup_class = jQuery(this).attr('data-popup-open');
        $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
 
        e.preventDefault();
    });
 
    //----- CLOSE
    $('[data-popup-close]').on('click', function(e)  {
        var targeted_popup_class = jQuery(this).attr('data-popup-close');
        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
 
        e.preventDefault();
    });
  $('.new_setting_wcm_auto_store_sync').on('click', function(){
    $('input[name="id"]').val('');
    $('select[name="store"]').val('').change();

    $('input[name="time1"]').val(10);
    $('input[name="time2"]').val(10);    
    $('input[name="time3"]').val(10);
    $('input[name="time4"]').val(10);
    $('input[name="time5"]').val(10);
    $('input[name="time6"]').val(10);
    $('input[name="time7"]').val(10);
    $('input[name="time8"]').val(10);
    $('input[name="sync_omni_sales_products"]').removeAttr('checked');
    $('input[name="sync_omni_sales_inventorys"]').removeAttr('checked');
    $('input[name="price_crm_woo"]').removeAttr('checked');
    $('input[name="sync_omni_sales_description"]').removeAttr('checked');
    $('input[name="sync_omni_sales_images"]').removeAttr('checked');
    $('input[name="sync_omni_sales_orders"]').removeAttr('checked');
    $('input[name="product_info_enable_disable"]').removeAttr('checked');
    $('input[name="product_info_image_enable_disable"]').removeAttr('checked');
     $('.add_title').removeClass('hide');
     $('.edit_title').addClass('hide');
       
     $('#myModal').modal();
  })  

  $('#toggle_popup_crm').on('click', function() {
      $('#popup_approval').toggle();
  });
  $('#toggle_popup_woo').on('click', function() {
      $('#popup_woo').toggle();
  });
  //selecte all
  $('#mass_select_all').on('click', function(){
    var favorite = [];
    var favorite_product = [];
    if($(this).is(':checked')){
      $('.individual').attr('checked', this.checked);
      $.each($(".individual"), function(){ 
          favorite.push($(this).data('id'));
      });
    }else{
      $('.individual').removeAttr('checked');
      favorite = [];
    }

    $("input[name='check']").val(favorite);
    $("input[name='check_product']").val(favorite_product);
  })
  
  

})(jQuery);

function edit(el){
  "use strict";
  var id = $(el).data("id");
  var name = $(el).data("name");
  var key = $(el).data("key");
  var secret= $(el).data("secret");
  var url= $(el).data("url");
  
  $('.update-title').removeClass('hide');
  $('.add-title').addClass('hide');
  $('.test_connect').removeClass('hide');
  $('input[name="id"]').val(id);
  $('input[name="name_channel"]').val(name);
  $('input[name="consumer_key"]').val(key)
  $('input[name="consumer_secret"]').val(secret);
  $('input[name="url"]').val(url);
  $('#channel_woocommerce').modal('show');
}

function add_product(){
  "use strict";
  $('.update-title').addClass('hide');
  $('.add-title').removeClass('hide');
  $('#chose_product').modal();
}

function get_list_product(el){
  "use strict";
  var id = $(el).val();
  $.post(admin_url+'omni_sales/get_list_product/'+id).done(function(response){
        response = JSON.parse(response);
        if(response.success == true) {
          $('select[name="product_id[]"]').html(response.html);
          $('select[name="product_id[]"]').selectpicker('refresh');
        }
    });
}

function sync_store(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var html = '';
  html += '<div class="Box">';
  html += '<span>';
  html += '<span></span>';
  html += '</span>';
  html += '</div>';
  $('#box-loadding').html(html);
  $.post(admin_url+'omni_sales/process_orders_woo/'+id).done(function(response){
    $('#box-loadding').html('');
    $('.status-sync').removeClass('label-danger');
    $('.status-sync').addClass('label-success');
    $('.status-sync').text('Sync success');
      response = JSON.parse(response);
      if(response){
        $('#box-loadding').html('');
        alert_float('success', 'sync store successfully');
      }

  }).fail(function(data) {
    $('#box-loadding').html('');
  });
}

function sync_inventory_synchronization(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
      data.arr_val = arr_val;
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      $.post(admin_url+'omni_sales/process_asynclibrary_inventory_detail/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
  }else{
    Confirm('Inventory sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'inventory'); /*change*/
  } 
}

function sync_decriptions_synchronization(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
      data.arr_val = arr_val;
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      $.post(admin_url+'omni_sales/process_decriptions_synchronization_detail/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
  }else{
    Confirm('Long decriptions sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'long_decriptions'); /*change*/
  } 
}

function sync_images_synchronization(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');

  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
      data.arr_val = arr_val;
      var html = '';
      html += '<div class="Box">';
      html += '<span>';
      html += '<span></span>';
      html += '</span>';
      html += '</div>';
      $('#box-loadding').html(html);
      $.post(admin_url+'omni_sales/process_asynclibrary_image_detail/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
  }else{

    Confirm('Image sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'images'); /*change*/
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
function update_product_woo(el){
     "use strict";

     $('input[name="id"]').val($(el).data('id'));
     $('.pricefr').removeClass('hide');
     $('select[name="group_product_id"]').val($(el).data('groupid')).change();
     $('.update-title').removeClass('hide');
     $('.add-title').addClass('hide');
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
}


function update_setting_woo_store(el){
     "use strict";
     $('input[name="id"]').val($(el).data('id'));
     $('select[name="store"]').val($(el).data('store')).change();

     $('input[name="time1"]').val($(el).data('time1')).trigger('change');
     $('input[name="time2"]').val($(el).data('time2')).trigger('change');
     $('input[name="time3"]').val($(el).data('time3')).trigger('change');
     $('input[name="time4"]').val($(el).data('time4')).trigger('change');
     $('input[name="time5"]').val($(el).data('time5')).trigger('change');
     $('input[name="time6"]').val($(el).data('time6')).trigger('change');
     $('input[name="time7"]').val($(el).data('time7')).trigger('change');
     $('input[name="time8"]').val($(el).data('time8')).trigger('change');
     if($(el).data('sync_omni_sales_products') == 1){
      $('input[name="sync_omni_sales_products"]').prop('checked','checked');
     }
     if($(el).data('sync_omni_sales_inventorys') == 1){
      $('input[name="sync_omni_sales_inventorys"]').prop('checked', 'checked');
     }
     if($(el).data('price_crm_woo') == 1){
      $('input[name="price_crm_woo"]').prop('checked', 'checked');
     }
     if($(el).data('sync_omni_sales_description') == 1){
      $('input[name="sync_omni_sales_description"]').prop('checked', 'checked');
     }
     if($(el).data('sync_omni_sales_images') == 1){
      $('input[name="sync_omni_sales_images"]').prop('checked','checked');
     }
     if($(el).data('sync_omni_sales_orders') == 1){
      $('input[name="sync_omni_sales_orders"]').prop('checked', 'checked');
     }
     if($(el).data('product_info_enable_disable') == 1){
      $('input[name="product_info_enable_disable"]').prop('checked', 'checked');
     }
     if($(el).data('product_info_image_enable_disable') == 1){
      $('input[name="product_info_image_enable_disable"]').prop('checked', 'checked');
     }

     $('.edit_title').removeClass('hide');
     $('.add_title').addClass('hide');
       
     $('#myModal').modal();
}

function Confirm(title, msg, $true, $false, id, type = "") { 
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);

      $.post(admin_url+'omni_sales/sync_products_to_store/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        response = JSON.parse(response);
        if(response){
          $('#box-loadding').html('');
          alert_float('success', 'Sync successfully');
        }else{
          $('#box-loadding').html('');
          alert_float('warning', 'Sync unsuccessful');
        }
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
      
  }else if(type == "inventory"){

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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);

      $.post(admin_url+'omni_sales/process_asynclibrary_inventory/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
            $('#box-loadding').html('');
            alert_float('success', 'Sync successfully');
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
  }else if(type == "images"){
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/process_asynclibrary_image/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
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
  }else if(type == "long_decriptions"){

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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/process_decriptions_synchronization/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
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
  }else if(type == "decriptions"){

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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/process_decriptions_synchronization/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
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
  }else if(type == "price"){
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_price_all/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
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
  }else if(type == "sync_all"){
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_all_not_selected/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
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
  }else if(type == "product_name"){
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_product_name_all/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
            $('#box-loadding').html('');
            alert_float('success', 'Sync successfully');
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
  }else if(type == "short_description"){
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_short_description_all/'+id).done(function(response){
        $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
            $('#box-loadding').html('');
            alert_float('success', 'Sync successfully');
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
      $.post(admin_url+'omni_sales/woo_price_update/'+id).done(function(response){
            $('#box-loadding').html('');
            alert_float('success', 'Update successfully');
            $('.table-product-woocommerce').DataTable().ajax.reload();
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

function checked_add(el){
    var id = $(el).data("id");
    var id_product = $(el).data("product");
    if ($(".individual").length == $(".individual:checked").length) {
        $("#mass_select_all").attr("checked", "checked");
        var value = $("input[name='check']").val();
        if(value != ''){
          value = value + ',' + id;
        }else{
          value = id;
        }
    } else {
        $("#mass_select_all").removeAttr("checked");
        var value = $("input[name='check']").val();
        var value_product = $("input[name='check_product']").val();
        var arr_val = value.split(',');
        if(arr_val.length > 0){
          $.each( arr_val, function( key, value ) {
            if(value == id){
              arr_val.splice(key, 1);
              value = arr_val.toString();
              $("input[name='check']").val(value);
            }
          });
        }
    }
    if($(el).is(':checked')){
      var value = $("input[name='check']").val();
      if(value != ''){
        value = value + ',' + id;
        value_product = value_product + ',' + id_product;
      }else{
        value = id;
        value_product = id_product;
      }
      $("input[name='check']").val(value);
      $("input[name='check_product']").val(value_product);
    }else{
      var value = $("input[name='check']").val();
      var value_product = $("input[name='check_product']").val();
      var arr_val = value.split(',');
      var arr_val_product = value_product.split(',');
      if(arr_val.length > 0){
        $.each( arr_val, function( key, value ) {
          if(value == id){
            arr_val.splice(key, 1);
            value = arr_val.toString();
            $("input[name='check']").val(value);
          }
        });

        $.each( arr_val_product, function( key, value_ ) {
          if(value_ == id_product){
            arr_val_product.splice(key, 1);
            value_ = arr_val_product.toString();
            $("input[name='check_product']").val(value_);
          }
        });
      }
    }
}


function staff_bulk_actions(){
  "use strict";
  $('#product-woocommerce_bulk_actions').modal('show');
}


 // Leads bulk action
  function omi_sales_delete_bulk_action(event) {
    "use strict";
      var store = $("input[name='id']").val();
      var arr_id = $("input[name='check']").val();
      if (confirm_delete()) {
          var mass_delete = $('#mass_delete').prop('checked');

          if(mass_delete == true){

              var ids = [];
              var data = {};

              data.mass_delete = true;
              data.rel_type = 'omni_sales';
              data.store = store;
              data.arr_id = arr_id;
              var rows = $('#table-product-woocommerce').find('tbody tr');
              $.each(rows, function() {
                  var checkbox = $($(this).find('td').eq(0)).find('input');
                  if (checkbox.prop('checked') === true) {
                      ids.push(checkbox.val());
                  }
              });

              data.ids = ids;
              $(event).addClass('disabled');
              setTimeout(function() {
                  $.post(admin_url + 'omni_sales/omi_sales_delete_bulk_action', data).done(function() {
                      window.location.reload();
                  }).fail(function(data) {
                      $('#product-woocommerce_bulk_actions').modal('hide');
                      alert_float('danger', data.responseText);
                  });
              }, 200);
          }else{
              window.location.reload();
          }

      }
  }

  function sync_price(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_price/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      });
  }else{
    Confirm('Price sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'price'); /*change*/
  } 
}
function sync_all(el){
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');

  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_all/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        response = JSON.parse(response);
        if(response){
          $('#box-loadding').html('');
          alert_float('success', 'Sync successfully');
        }else{
          $('#box-loadding').html('');
          alert_float('warning', 'Sync unsuccessful');
        }
      });
  }else{
    Confirm('Confirm all product information synchronously', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'sync_all'); /*change*/
  }
}


function sync_product_name(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_product_name/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
  }else{
    Confirm('Product name sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'product_name'); /*change*/
  } 
}


function sync_short_description(el){
  "use strict";
  $('.status-sync').removeClass('label-primary');
  $('.status-sync').addClass('label-danger');
  $('.status-sync').text('Wait for sync');
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
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
        alert_float('warning', 'The synchronization all process can take a long time to complete');
      }, 60*1000);
      $.post(admin_url+'omni_sales/sync_short_description/', data).done(function(response){
       $('.status-sync').removeClass('label-danger');
        $('.status-sync').addClass('label-success');
        $('.status-sync').text('Sync success');
        $('#box-loadding').html('');
        alert_float('success', 'Sync successfully');
      }).fail(function(data) {
        $('#box-loadding').html('');
      });
  }else{
    Confirm('Short description sync confirmation', 'Are you sure all products are synchronized?', 'Yes', 'Cancel', id, 'short_description'); /*change*/
  } 
}

function price_update(el){
  var id = $(el).data('id');
  var check_detail =  $("input[name='check']").val();
  var arr_val = check_detail.split(',');
  if(arr_val.length > 0 && arr_val[0] != ""){
      var data = {};
      data.id = id;
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
      $.post(admin_url+'omni_sales/woo_price_update/', data).done(function(response){
        response = JSON.parse(response);
        if(response){
          $('#box-loadding').html('');
          alert_float('success', 'Update successfully');
          $('.table-product-woocommerce').DataTable().ajax.reload();
        }else{
          $('#box-loadding').html('');
          alert_float('warning', 'Update unsuccessful');
        }
      });
  }else{
    Confirm('Confirm price updates for all products', 'Are you sure to update prices for all products?', 'Yes', 'Cancel', id, 'price_update'); /*change*/
  }
}