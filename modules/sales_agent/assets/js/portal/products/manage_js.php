<script>
(function(){
   "use strict";

    var fnServerParams = {
    	"group": "[name='product_group']",
        "program": "[name='program']",
	};
	initDataTable('.table-products', site_url + 'sales_agent/portal/products_table', false, false, fnServerParams, [0, 'desc']);

	$.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            $('.table-products').DataTable().ajax.reload()
                .columns.adjust();
        });
    });
})(jQuery);
</script>