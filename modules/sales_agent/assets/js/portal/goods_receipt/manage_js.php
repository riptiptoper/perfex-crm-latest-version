<script>

(function(){
  "use strict";	

 var GoodsreceiptParams = {
    "day_vouchers": "input[name='date_add']",
 };

 var table_manage_goods_receipt = $('.table-table_manage_goods_receipt');

 initDataTable(table_manage_goods_receipt, site_url+'sales_agent/portal/table_manage_goods_receipt', [], [], GoodsreceiptParams, [0, 'desc']);


 $('#date_add').on('change', function() {
    table_manage_goods_receipt.DataTable().ajax.reload();
});


})(jQuery);

</script>