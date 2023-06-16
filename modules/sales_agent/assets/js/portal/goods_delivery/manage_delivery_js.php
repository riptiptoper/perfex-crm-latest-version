<script>

(function(){
  "use strict";	

	var InvoiceServerParams = {
	    "invoice_id": '',
	    "day_vouchers": "input[name='date_add']",
	};

	var table_manage_delivery = $('.table-table_manage_delivery');

	initDataTable(table_manage_delivery, site_url+'sales_agent/portal/table_manage_delivery',[],[], InvoiceServerParams, [0 ,'desc']);


	$('#date_add').on('change', function() {
	    table_manage_delivery.DataTable().ajax.reload();
	});

})(jQuery);

function delivery_status_mark_as(status, task_id, type) {
  "use strict"; 
  var  url = 'sales_agent/portal/delivery_status_mark_as/' + status + '/' + task_id + '/' + type;
    var taskModalVisible = $('#task-modal').is(':visible');
    url += '?single_task=' + taskModalVisible;
    $("body").append('<div class="dt-loader"></div>');

    requestGetJSON(url).done(function (response) {
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            
          var av_tasks_tables = ['.table-table_manage_delivery', '.table-table_manage_packing_list'];
          $.each(av_tasks_tables, function (i, selector) {
            if ($.fn.DataTable.isDataTable(selector)) {
              $(selector).DataTable().ajax.reload(null, false);
            }
          });
          alert_float('success', response.message);
        }
    });
}

</script>