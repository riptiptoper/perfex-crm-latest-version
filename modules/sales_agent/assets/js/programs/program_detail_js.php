<script>
(function(){
   "use strict";
  var fnServerParams = {
  };

 initDataTable('.table-table_program_client', admin_url + 'sales_agent/table_program_client/'+ "<?php echo html_entity_decode($program->id); ?>", false, false, fnServerParams, [0, 'desc']);


initDataTable('.table-program-items', admin_url+'sales_agent/table_program_items/'+"<?php echo html_entity_decode($program->id); ?>", false, false,'undefined',[1,'asc']);

})(jQuery);
</script>