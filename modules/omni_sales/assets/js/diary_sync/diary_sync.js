(function(){
  "use strict";
  
  var fnServerParams = {
  }

  initDataTable('.table-diary-sync-products', admin_url + 'omni_sales/table_diary_sync_products', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-diary-sync-orders', admin_url + 'omni_sales/table_diary_sync_orders', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-diary-sync-inventory-manage', admin_url + 'omni_sales/table_diary_sync_inventory_manage', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-sync-products-from-the-store-information', admin_url + 'omni_sales/table_sync_products_from_the_store_information', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-sync-products-from-the-store-information-images', admin_url + 'omni_sales/table_sync_products_from_the_store_information_images', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-sync-price', admin_url + 'omni_sales/table_sync_price', false, false, fnServerParams, [0, 'desc']);
  initDataTable('.table-synchronize-products-desctiption', admin_url + 'omni_sales/table_log_description', false, false, fnServerParams, [0, 'desc']);


})(jQuery);
