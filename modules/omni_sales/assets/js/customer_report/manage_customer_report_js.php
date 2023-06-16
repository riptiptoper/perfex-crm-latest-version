
<script>

"use strict";
    var InvoiceServerParams = {
       "filter_authorized_by": "[name='filter_authorized_by[]']",
       "filter_shift_type": "[name='filter_shift_type[]']",
       "from_date": "input[name='from_date']",
       "to_date": "input[name='to_date']",
     };

var table_manage_import_customer_reports = $('.table-table_manage_import_customer_reports');

 initDataTable(table_manage_import_customer_reports, admin_url+'omni_sales/table_manage_import_customer_reports',[0],[0], InvoiceServerParams, [0 ,'desc']);

$('.import_customer_reports').DataTable().columns([0]).visible(false, false);

    $.each(InvoiceServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_manage_import_customer_reports.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

    $('#from_date').on('change', function() {
      table_manage_import_customer_reports.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });

    $('#to_date').on('change', function() {
      table_manage_import_customer_reports.DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });
    

 function staff_bulk_actions(){
  "use strict";
  $('#table_manage_import_customer_reports_bulk_actions').modal('show');
}


 // Leads bulk action
  function customer_report_delete_bulk_action(event) {
    "use strict";

      if (confirm_delete()) {
          var mass_delete = $('#mass_delete').prop('checked');

          if(mass_delete == true){
              var ids = [];
              var data = {};

              data.mass_delete = true;
              data.rel_type = 'customer_report';

              var rows = $('#table-table_manage_import_customer_reports').find('tbody tr');
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
                      $('#table_manage_import_customer_reports_bulk_actions').modal('hide');
                      alert_float('danger', data.responseText);
                  });
              }, 200);
          }else{
              window.location.reload();
          }

      }
  }
  
  function edit_customer_reports(invoker,id){
      "use strict";
      
      $('#edit_customer_reports').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#edit_customer_reports_id_t').html('');
      $('#edit_customer_reports_id_t').append(hidden_input('id',id));

      var data_pump_sales={};
          data_pump_sales.id = id;

      $.post(admin_url + 'omni_sales/get_customer_report',data_pump_sales).done(function(response){
         response = JSON.parse(response);

         if(response.customer_report != ''){
            $("input[name='ser_no']").val(response.customer_report.ser_no);
            $("input[name='authorized_by']").val(response.customer_report.authorized_by);
            $("input[name='date']").val(response.customer_report.date);
            $("input[name='time']").val(response.customer_report.time);
            $("input[name='transaction_id']").val(response.customer_report.transaction_id);
            
            $("input[name='receipt']").val(response.customer_report.receipt);
            $("input[name='nozzle']").val(response.customer_report.nozzle);
            $("input[name='product']").val(response.customer_report.product);
            $("input[name='quantity']").val(response.customer_report.quantity);

            $("input[name='total_sale']").val(response.customer_report.total_sale);
            $("input[name='ref_slip_no']").val(response.customer_report.ref_slip_no);
            $("input[name='payment_id']").val(response.customer_report.payment_id);

            $("select[name='customer_id'] option[value='']").remove();
            $("select[name='customer_id']").selectpicker('refresh');

            if(response.customer_status == 'true'){
              $("select[name='customer_id']").val(response.customer_report.customer_id).change();
            }else{
              $("select[name='customer_id']").append(new Option(response.customer_report.authorized_by, ''));
              $("select[name='customer_id']").selectpicker('refresh');

              $("select[name='customer_id']").val('').change();

            }

            if(response.pay_mode_id != ''){
              $("select[name='pay_mode_id']").val(response.customer_report.pay_mode_id).change();
            }else{
              $("select[name='pay_mode_id']").val('').change();

            }
            
         }
          
       });

      init_selectpicker();

        $("input[data-type='currency']").on({
            keyup: function() {        
              formatCurrency($(this));
            },
            blur: function() { 
              formatCurrency($(this), "blur");
            }
        });

  }

function formatNumber(n) {
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

  function formatCurrency(input, blur) {
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

 // create invoice
  function create_invoice_from_customer_report_bulk_action(event) {
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

            $(event).addClass('disabled');

              $.post(admin_url + 'omni_sales/create_invoice_from_customer_report_bulk_action', data).done(function(responsec) {
                 responsec = JSON.parse(responsec);

                  if(responsec.status){
                    alert_float('success', responsec.message);
                 }else{
                    alert_float('warning', responsec.message);
                 }
                    window.location.reload();

              });
          }else{
            alert_float('warning', "<?php echo _l('no_transaction_selected') ?>");
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

            $('.id_customer_report').html('');
            $('.id_customer_report').append(hidden_input('customer_report_id',ids));

            $('.id_from_date').html('');
            $('.id_from_date').append(hidden_input('customer_report_from_date',$("input[name='from_date']").val()));

            $('.id_to_date').html('');
            $('.id_to_date').append(hidden_input('customer_report_to_date', $("input[name='to_date']").val()));
            
            $( "#create_report_transation_bulk_action" ).submit();

          }else{
            alert_float('warning', "<?php echo _l('no_report_selected') ?>");
          }
      }
  }



</script>