<script>
(function(){
   "use strict";	
  var table_warehouse_name = $('table.table-table_warehouse_name');
  var _table_api = initDataTable(table_warehouse_name, site_url+'sales_agent/portal/table_warehouse_name', [0], [0], '',  [3, 'asc']);

})(jQuery);


function add_one_warehouse(){
    "use strict";

    $('#a_warehouse').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#warehouse_id').html('');

    $('#a_warehouse input[name="warehouse_code"]').val('');
    $('#a_warehouse input[name="warehouse_name"]').val('');
    $('#a_warehouse input[name="order"]').val('');

    $('#a_warehouse textarea[name="warehouse_address"]').val('');
    $('#a_warehouse textarea[name="note"]').val('');

    $('#a_warehouse input[name="display"]').prop("checked", true);


}


  function edit_warehouse_type(invoker,id){
      "use strict";

      var $warehouseModal = $('#a_warehouse');

     $warehouseModal.find('input[name="warehouse_code"]').val('');
     $warehouseModal.find('input[name="warehouse_name"]').val('');
     $warehouseModal.find('input[name="order"]').val('');

     $warehouseModal.find('textarea[name="warehouse_address"]').val('');
     $warehouseModal.find('textarea[name="note"]').val('');

     $warehouseModal.find('input[name="display"]').prop("checked", false);

      
      $('#a_warehouse').modal('show');
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');

      $('#warehouse_id').html('');
      $('#warehouse_id').append(hidden_input('id',id));

        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            requestGetJSON('sales_agent/portal/get_warehouse_by_id/' + id).done(function (response) {

                $warehouseModal.find('input[name="warehouse_code"]').val(response.warehouse_code);
                $warehouseModal.find('input[name="warehouse_name"]').val(response.warehouse_name);
                $warehouseModal.find('input[name="order"]').val(response.order);
                $warehouseModal.find('input[name="city"]').val(response.city);
                $warehouseModal.find('input[name="state"]').val(response.state);
                $warehouseModal.find('input[name="zip_code"]').val(response.zip_code);

                if(response.country != ''){
                  $("select[name='country']").val(response.country).change();
                }else{
                  $("select[name='country']").val('').change();

                }

                if(response.display == 1){
                    $warehouseModal.find('input[name="display"]').prop("checked", true);
                  }else{
                    $warehouseModal.find('input[name="display"]').prop("checked", false);

                  }

                $warehouseModal.find('textarea[name="warehouse_address"]').val(response.warehouse_address.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $warehouseModal.find('textarea[name="note"]').val(response.note.replace(/(<|<)br\s*\/*(>|>)/g, " "));

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();

            });

        }
   
       
  }

</script>