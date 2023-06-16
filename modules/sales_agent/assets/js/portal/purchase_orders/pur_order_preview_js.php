<script>
var pur_order_id = '<?php echo html_entity_decode($estimate->id); ?>';
(function($) {
  "use strict"; 
   var data_send_mail = {};
  <?php if(isset($send_mail_approve)){ 
    ?>
    data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
    data_send_mail.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data_send_mail.rel_type = 'pur_order';
    data_send_mail.addedfrom = <?php echo html_entity_decode($estimate->addedfrom); ?>;
    $.post(admin_url+'purchase/send_mail', data_send_mail).done(function(response){
    });
  <?php } ?>


})(jQuery);

 function send_po(id) {
  "use strict"; 
  $('#additional_po').html('');
  $('#additional_po').append(hidden_input('po_id',id));
  $('#send_po').modal('show');
 }

function add_payment(id){
  "use strict"; 
   appValidateForm($('#purorder-add_payment-form'),{amount:'required', date:'required'});
   $('#payment_record_pur').modal('show');
   $('.edit-title').addClass('hide');
   $('#additional').html('');
}

function add_payment_with_inv(id){
  "use strict"; 
  appValidateForm($('#purorder-add_payment_with_inv-form'),{pur_invoice:'required', amount:'required', date:'required'});
  $('#payment_record_pur_with_inv').modal('show');
  $('#inv_additional').html('');
}


function pur_inv_payment_change(el){
  "use strict"; 
  var invoice = $(el).val();
  if(invoice != '' ){
    $.post(admin_url+'purchase/pur_inv_payment_change/'+invoice).done(function(reponse){
      reponse = JSON.parse(reponse);
      $('#payment_record_pur_with_inv input[name="amount"]').val(reponse.amount);
      $('#payment_record_pur_with_inv input[name="amount"]').attr('max', reponse.amount);
    });
  }else{
    $('#payment_record_pur_with_inv input[name="amount"]').val(0);
    $('#payment_record_pur_with_inv input[name="amount"]').attr('max', 0);

    alert_float('warning', '<?php echo _l('please_select_purchase_invoice'); ?>');
  }
}

   
function change_status_pur_order(invoker,id){
  "use strict"; 
   $.post(admin_url+'purchase/change_status_pur_order/'+invoker.value+'/'+id).done(function(reponse){
    reponse = JSON.parse(reponse);
    window.location.href = admin_url + 'purchase/purchase_order/'+id;
    alert_float('success',reponse.result);
  });
}

//preview purchase order attachment
function preview_purorder_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purorder_file(id, rel_id);
}

function view_purorder_file(id, rel_id) {
  "use strict"; 
      $('#purorder_file_data').empty();
      $("#purorder_file_data").load(site_url + 'sales_agent/portal/file_pur_order/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }

          $('._project_file').modal('show');
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purorder_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet('sales_agent/portal/delete_purorder_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#purorder_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }

  


function send_request_approve(id){
  "use strict";
    var data = {};
    data.rel_id = <?php echo html_entity_decode($estimate->id); ?>;
    data.rel_type = 'pur_order';
    data.addedfrom = <?php echo html_entity_decode($estimate->addedfrom); ?>;
  $("body").append('<div class="dt-loader"></div>');
    $.post(site_url + 'sales_agent/portal/send_request_approve', data).done(function(response){
        response = JSON.parse(response);
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }else{
          alert_float('warning', response.message);
            window.location.reload();
        }
    });
}



function sign_request(id){
  "use strict";
    change_request_approval_status(id,2, true);
}
function approve_request(id){
  "use strict";
  change_request_approval_status(id,2);
}
function deny_request(id){
  "use strict";
    change_request_approval_status(id,3);
}
function change_request_approval_status(id, status, sign_code){
  "use strict";
    var data = {};
    data.rel_id = id;
    data.rel_type = 'pur_order';
    data.approve = status;
    if(sign_code == true){
      data.signature = $('input[name="signature"]').val();
    }else{
      data.note = $('textarea[name="reason"]').val();
    }
    $.post(site_url + 'sales_agent/portal/approve_request/' + id, data).done(function(response){
        response = JSON.parse(response); 
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }
    });
}
function accept_action() {
  "use strict";
  $('#add_action').modal('show');
}


</script>