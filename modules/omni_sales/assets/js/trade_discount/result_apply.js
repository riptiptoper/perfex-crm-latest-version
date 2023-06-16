(function(){
  "use strict";
  
  var fnServerParams = {
  }

  initDataTable('.table-log_discount', admin_url + 'omni_sales/table_log_discount', false, false, fnServerParams, [0, 'desc']);
})(jQuery);
