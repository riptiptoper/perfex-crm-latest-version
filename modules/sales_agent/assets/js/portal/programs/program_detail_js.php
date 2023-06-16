<script>
(function(){
   "use strict";

initDataTable('.table-program-items', site_url+'sales_agent/portal/table_program_items/'+"<?php echo html_entity_decode($program->id); ?>", false, false,'undefined',[1,'asc']);

})(jQuery);
</script>