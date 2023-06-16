<script>
(function(){
   "use strict";
  var fnServerParams = {
  	"from_date": 'input[name="from_date"]',
    "to_date": 'input[name="to_date"]',
    "status": '[name="status"]',
    "client": '[name="customer"]',
  };
 initDataTable('.table-sales-invoices', site_url + 'sales_agent/portal/table_sale_invoices', false, false, fnServerParams, [0, 'desc']);

$.each(fnServerParams, function(i, obj) {
    $('select' + obj).on('change', function() {  
        $('.table-sales-invoices').DataTable().ajax.reload()
            .columns.adjust();
    });
});

$('input[name="from_date"]').on('change', function() {
    $('.table-sales-invoices').DataTable().ajax.reload()
            .columns.adjust();
});

$('input[name="to_date"]').on('change', function() {
    $('.table-sales-invoices').DataTable().ajax.reload()
            .columns.adjust();
});

})(jQuery);


</script>