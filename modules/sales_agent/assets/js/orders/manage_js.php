<script>
(function(){
   "use strict";
  var fnServerParams = {
  	 "agent": "[name='agent_filter']",
  };
 initDataTable('.table-orders', admin_url + 'sales_agent/orders_table', false, false, fnServerParams, [0, 'desc']);

 $.each(fnServerParams, function(i, obj) {
    $('select' + obj).on('change', function() {  
        $('.table-orders').DataTable().ajax.reload()
            .columns.adjust();
    });
});
})(jQuery);


</script>