<script>
var pur_order_id = '<?php echo html_entity_decode($estimate->id); ?>';
(function($) {
  "use strict"; 

})(jQuery);

 function send_po(id) {
  "use strict"; 
  $('#additional_po').html('');
  $('#additional_po').append(hidden_input('po_id',id));
  $('#send_po').modal('show');
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
      $("#purorder_file_data").load(admin_url + 'sales_agent/file_pur_order/' + id + '/' + rel_id, function(response, status, xhr) {
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
        requestGet('sales_agent/delete_purorder_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#purorder_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }

</script>