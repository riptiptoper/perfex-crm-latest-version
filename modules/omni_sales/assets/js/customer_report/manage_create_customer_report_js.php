
<script>

"use strict";
    var InvoiceServerParams = {
     };

var table_manage_create_customer_reports = $('.table-table_manage_create_customer_reports');

 initDataTable(table_manage_create_customer_reports, admin_url+'omni_sales/table_manage_create_customer_reports',[0],[0], InvoiceServerParams, [0 ,'desc']);

$('.import_customer_reports').DataTable().columns([0]).visible(false, false);

    $.each(InvoiceServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_manage_create_customer_reports.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });


 function staff_bulk_actions(){
  "use strict";
  $('#table_manage_create_customer_reports_bulk_actions').modal('show');
}


 // Leads bulk action
  function create_customer_report_delete_bulk_action(event) {
    "use strict";

      if (confirm_delete()) {
          var mass_delete = $('#mass_delete').prop('checked');

          if(mass_delete == true){
              var ids = [];
              var data = {};

              data.mass_delete = true;
              data.rel_type = 'create_customer_report';

              var rows = $('#table-table_manage_create_customer_reports').find('tbody tr');
              $.each(rows, function() {
                  var checkbox = $($(this).find('td').eq(0)).find('input');
                  if (checkbox.prop('checked') === true) {
                      ids.push(checkbox.val());
                  }
              });

              data.ids = ids;
              $(event).addClass('disabled');
              setTimeout(function() {
                  $.post(admin_url + 'omni_sales/customer_report_delete_bulk_action', data).done(function() {
                      window.location.reload();
                  }).fail(function(data) {
                      $('#table_manage_create_customer_reports_bulk_actions').modal('hide');
                      alert_float('danger', data.responseText);
                  });
              }, 200);
          }else{
              window.location.reload();
          }

      }
  }

   // create report
  function create_report_transaction_bulk_action(event) {
    "use strict";

      if (confirm_delete()) {

          var ids = [];
          var data = {};

          var rows = $('#table-table_manage_import_customer_reports').find('tbody tr');
          $.each(rows, function() {
              var checkbox = $($(this).find('td').eq(0)).find('input');
              if (checkbox.prop('checked') === true) {
                  ids.push(checkbox.val());
              }
          });

          if( ids.length > 0){
            data.ids = ids;
            data.from_date =  $("input[name='from_date']").val();
            data.to_date =  $("input[name='to_date']").val();

            // $(event).addClass('disabled');

              $.post(admin_url + 'omni_sales/create_report_transation_bulk_action', data).done(function(responsec) {
                 responsec = JSON.parse(responsec);

                  if(responsec.status){
                    alert_float('success', responsec.message);
                 }else{
                    alert_float('warning', responsec.message);
                 }
                    window.location.reload();

              });
          }else{
            alert_float('warning', "<?php echo _l('no_report_selected') ?>");
          }
      }
  }



</script>