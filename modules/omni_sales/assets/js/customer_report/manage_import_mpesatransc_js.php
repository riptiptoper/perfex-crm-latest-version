
<script>

"use strict";
    var InvoiceServerParams = {
      "group_by": "[name='group_by[]']",
      "filter_by_phone": "input[name='filter_by_phone']",
      "from_date": "input[name='from_date']",
      "to_date": "input[name='to_date']",
     };

var table_manage_import_mpesatransc = $('.table-table_manage_import_mpesatransc');

 initDataTable(table_manage_import_mpesatransc, admin_url+'omni_sales/table_manage_import_mpesatransc',[0],[0], InvoiceServerParams, [1 ,'desc']);

$('.manage_import_mpesatransc').DataTable().columns([0]).visible(false, false);

    $.each(InvoiceServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_manage_import_mpesatransc.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });
    
 $('#filter_by_phone').on('change', function() {
    table_manage_import_mpesatransc.DataTable().ajax.reload().columns.adjust().responsive.recalc();
});
 $('#from_date').on('change', function() {
    table_manage_import_mpesatransc.DataTable().ajax.reload().columns.adjust().responsive.recalc();
});
 $('#to_date').on('change', function() {
    table_manage_import_mpesatransc.DataTable().ajax.reload().columns.adjust().responsive.recalc();
});


 function staff_bulk_actions_mpesatransc(){
  "use strict";
  $('#table_manage_import_mpesatransc_bulk_actions').modal('show');
}


 // Leads bulk action
  function warehouse_delete_bulk_action_mpesatransc(event) {
    "use strict";

      if (confirm_delete()) {
          var mass_delete = $('#mass_delete1').prop('checked');

          if(mass_delete == true){
              var ids = [];
              var data = {};

              data.mass_delete = true;
              data.rel_type = 'importing_mpesatransc';

              var rows = $('#table-table_manage_import_mpesatransc').find('tbody tr');
              $.each(rows, function() {
                  var checkbox = $($(this).find('td').eq(0)).find('input');
                  if (checkbox.prop('checked') === true) {
                      ids.push(checkbox.val());
                  }
              });

              data.ids = ids;
              $(event).addClass('disabled');
              setTimeout(function() {
                  $.post(admin_url + 'omni_sales/warehouse_delete_import_transation_bulk_action', data).done(function() {
                      window.location.reload();
                  }).fail(function(data) {
                      $('#table_manage_import_mpesatransc_bulk_actions').modal('hide');
                      alert_float('danger', data.responseText);
                  });
              }, 200);
          }else{
              window.location.reload();
          }

      }
  }


//edit 
  function edit_mpesatransc(invoker,id){
      "use strict";
      
      $('#edit_mpesatransc').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#edit_mpesatransc_id_t').html('');
      $('#edit_mpesatransc_id_t').append(hidden_input('id',id));

      var data_mpesatransc={};
          data_mpesatransc.id = id;

      $.post(admin_url + 'omni_sales/get_mpesatransc',data_mpesatransc).done(function(response){
         response = JSON.parse(response);

         if(response.mpesatransc != ''){
            $("input[name='sale_id']").val(response.mpesatransc.sale_id);
            $("input[name='pumpId']").val(response.mpesatransc.pumpId);
            $("input[name='employee_name']").val(response.mpesatransc.employee_name);
            $("input[name='mpesaType']").val(response.mpesatransc.mpesaType);
            $("input[name='short_code']").val(response.mpesatransc.short_code);
            $("input[name='bill_ref_number']").val(response.mpesatransc.bill_ref_number);

            $("input[name='trans_date']").val(response.mpesatransc.trans_date);
            $("input[name='trans_time']").val(response.mpesatransc.trans_time);
            $("input[name='first_name']").val(response.mpesatransc.first_name);
            $("input[name='middle_name']").val(response.mpesatransc.middle_name);
            $("input[name='last_name']").val(response.mpesatransc.last_name);
            $("input[name='phone']").val(response.mpesatransc.phone);
            $("input[name='trans_amount']").val(response.mpesatransc.trans_amount);
            $("input[name='transc_id']").val(response.mpesatransc.transc_id);
            $("input[name='trans_type']").val(response.mpesatransc.trans_type);
            $("input[name='trans_id']").val(response.mpesatransc.trans_id);

            if(response.mpesatransc.customer_id != '' && response.mpesatransc.customer_id != 0){
              $("select[name='customer_id']").val(response.mpesatransc.customer_id).change();
            }else{
              $("select[name='customer_id']").val('').change();

            }
            
            if(response.mpesatransc.staffname != '' && response.mpesatransc.staffname != 0){
              $("select[name='staffname']").val(response.mpesatransc.staffname).change();
            }else{
              $("select[name='staffname']").val('').change();

            }



         }
          
       });

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


</script>