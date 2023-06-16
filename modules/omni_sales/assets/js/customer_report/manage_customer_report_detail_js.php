
<script>

"use strict";
    var InvoiceServerParams = {
       "id": "input[name='id']",
     };

var table_view_customer_report_detail = $('.table-table_view_customer_report_detail');

 initDataTable(table_view_customer_report_detail, admin_url+'omni_sales/table_view_customer_report_detail',[null],[null], InvoiceServerParams, [1 ,'desc']);

$('.import_customer_reports').DataTable().columns([0]).visible(false, false);

    $.each(InvoiceServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_view_customer_report_detail.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });

</script>