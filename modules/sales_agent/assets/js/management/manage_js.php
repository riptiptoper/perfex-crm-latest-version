<script>
(function(){
   "use strict";
  var fnServerParams = {
      "category_filter": "[name='category_filter']"
  }
 initDataTable('.table-sa_management', admin_url + 'sales_agent/sa_management_table', false, false, fnServerParams, [0, 'desc']);


})(jQuery);

</script>