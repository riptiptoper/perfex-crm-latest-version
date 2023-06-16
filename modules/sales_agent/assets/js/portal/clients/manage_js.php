<script>
(function(){
   "use strict";
  var fnServerParams = {
  	 "group": "[name='group']",
  };
 initDataTable('.table-client', site_url + 'sales_agent/portal/clients_table', false, false, fnServerParams, [0, 'desc']);
$.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            $('.table-client').DataTable().ajax.reload()
                .columns.adjust();
        });
    });
})(jQuery);


</script>