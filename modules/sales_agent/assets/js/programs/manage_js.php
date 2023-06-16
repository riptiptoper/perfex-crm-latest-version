<script>
(function(){
   "use strict";
  var fnServerParams = {
      "group": "[name='group_filter']",
      "agent": "[name='agent_filter']",
  }
 initDataTable('.table-agent-program', admin_url + 'sales_agent/program_table', false, false, fnServerParams, [0, 'desc']);

 $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            $('.table-agent-program').DataTable().ajax.reload()
                .columns.adjust();
        });
    });
})(jQuery);


</script>