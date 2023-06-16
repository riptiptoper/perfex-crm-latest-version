(function(){
  "use strict";
  $("input[data-type='currency']").on({
    keyup: function() {        
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
  });
  var fnServerParams = {
    "start_date": "[name='start_date']",
    "end_date": "[name='end_date']",
    "seller": "[name='seller[]']",
    "status": "[name='status']"
  }
  initDataTable('.table-shift_list', admin_url + 'omni_sales/shift_list_table', false, false, fnServerParams, [0, 'desc']);

  $('input[name="start_date"], input[name="end_date"], select[name="seller[]"], select[name="status"]').on('change', function() {
   $('.table-shift_list').DataTable().ajax.reload()
   .columns.adjust()
   .responsive.recalc();
 });
  appValidateForm($('#form_add_shift'), {
   'granted_amount': 'required'
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
function create_shift() {
  "use strict";
  $('#add_edit_shift').modal('show');
  $('#add_edit_shift').find('.add-title').removeClass('hide');
  $('#add_edit_shift').find('.update-title').addClass('hide');
  $('#add_edit_shift input[name="id"]').val('');
}
function edit_shift(el) {
  "use strict";
  $('#add_edit_shift').modal('show');
  $('#add_edit_shift').find('.update-title').removeClass('hide');
  $('#add_edit_shift').find('.add-title').addClass('hide');
}