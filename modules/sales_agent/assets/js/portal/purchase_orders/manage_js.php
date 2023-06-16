<script>
(function(){
   "use strict";
  var fnServerParams = {
  	"from_date": 'input[name="from_date"]',
    "to_date": 'input[name="to_date"]',
    "approve_status": '[name="approve_status"]',
    "order_status": '[name="order_status"]',
  };
 initDataTable('.table-purchase-order', site_url + 'sales_agent/portal/table_purchase_orders', false, false, fnServerParams, [0, 'desc']);

$.each(fnServerParams, function(i, obj) {
    $('select' + obj).on('change', function() {  
        $('.table-purchase-order').DataTable().ajax.reload()
            .columns.adjust();
    });
});

$('input[name="from_date"]').on('change', function() {
    $('.table-purchase-order').DataTable().ajax.reload()
            .columns.adjust();
});

$('input[name="to_date"]').on('change', function() {
    $('.table-purchase-order').DataTable().ajax.reload()
            .columns.adjust();
});

})(jQuery);


</script>