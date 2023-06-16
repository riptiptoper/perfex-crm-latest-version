<script>

	function create_export_stock(id){
		"use strict";

		var data = {};
		data.rel_id = id;

		$.post(admin_url + 'omni_sales/check_create_delivery_note', data).done(function(response){
			response = JSON.parse(response); 
			if (response.success == true || response.success == 'true') {

				var order_data = {};
				order_data.orderid = id;
				$.post(admin_url + 'omni_sales/create_export_stock_ajax', order_data).done(function(response){
					response = JSON.parse(response); 
					if (response.status == true || response.status == 'true') {
						alert_float('success', response.message);
						window.location.reload();
					}
				});
			}else{
			  //check approval false
			  alert_float('warning', response.message);
			}
		});

	}

</script>