<script>
var  invoices_rp, po_rp, report_from_choose, total_income, total_expenses, fnServerParams;
 var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
var date_range = $('#date-range');
(function($) {
  "use strict";
  invoices_rp = $('#list_invoices_rp');
  po_rp = $('#list_po_rp');
  total_income = $('#total-income-report');
  total_expenses = $('#total-expenses-report');
  report_from_choose = $('#report-time');
  fnServerParams = {
    "products_services": '[name="products_services"]',
    "report_months": '[name="months-report"]',
    "report_from": '[name="report-from"]',
    "report_to": '[name="report-to"]',
    "year_requisition": "[name='year_requisition']",
    "report_currency": '[name="currency"]',
  }
  
  $('select[name="products_services"]').on('change', function() {
    gen_reports();
  });

  $('select[name="currency"]').on('change', function() {
    gen_reports();
  });


  $('select[name="months-report"]').on('change', function() {
    if($(this).val() != 'custom'){
     gen_reports();
    }
   });

   $('select[name="year_requisition"]').on('change', function() {
     gen_reports();
   });

   report_from.on('change', function() {
     var val = $(this).val();
     var report_to_val = report_to.val();
     if (val != '') {
       report_to.attr('disabled', false);
       if (report_to_val != '') {
         gen_reports();
       }
     } else {
       report_to.attr('disabled', true);
     }
   });

   report_to.on('change', function() {
     var val = $(this).val();
     if (val != '') {
       gen_reports();
     }
   });

   $('.table-list-inv-report').on('draw.dt', function() {
     var poReportsTable = $(this).DataTable();
     var sums = poReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
     $(this).find('tfoot td.total').html(sums.total);
     $(this).find('tfoot td.total_tax').html(sums.total_tax);
     $(this).find('tfoot td.total_value').html(sums.total_value);
   });

   $('.table-po-report').on('draw.dt', function() {
     var poReportsTable = $(this).DataTable();
     var sums = poReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?> (<?php echo _l('per_page'); ?>)");
     $(this).find('tfoot td.total').html(sums.total);
     $(this).find('tfoot td.total_tax').html(sums.total_tax);
     $(this).find('tfoot td.total_value').html(sums.total_value);
   });

   $('select[name="months-report"]').on('change', function() {
     var val = $(this).val();
     report_to.attr('disabled', true);
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
     gen_reports();
   });
})(jQuery);


 function init_report(e, type) {
  "use strict";

   var report_wrapper = $('#report');

   if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
   }

   $('head title').html($(e).text());
   

   report_from_choose.addClass('hide');

   $('#year_requisition').addClass('hide');


   invoices_rp.addClass('hide');
   po_rp.addClass('hide');
   total_income.addClass('hide');
   total_expenses.addClass('hide');
 
  $('select[name="months-report"]').val('this_month').change();
    // Clear custom date picker
      $('#currency').removeClass('hide');

      if (type == 'invoices_rp') {
        invoices_rp.removeClass('hide');
        report_from_choose.removeClass('hide');
      }else if(type == 'po_rp'){
        po_rp.removeClass('hide');
        report_from_choose.removeClass('hide');
      }else if(type == 'total_income'){
        total_income.removeClass('hide');
        $('#year_requisition').removeClass('hide');
      }else if(type == 'total_expenses'){
        total_expenses.removeClass('hide');
        $('#year_requisition').removeClass('hide');
      }

      gen_reports();
}


function list_inv_report() {
   "use strict";

 if ($.fn.DataTable.isDataTable('.table-list-inv-report')) {
   $('.table-list-inv-report').DataTable().destroy();
 }
 initDataTable('.table-list-inv-report', site_url + 'sales_agent/portal/list_inv_report', [], [], fnServerParams, [0, 'desc']);


}

function po_report() {
  "use strict";

 if ($.fn.DataTable.isDataTable('.table-po-report')) {
   $('.table-po-report').DataTable().destroy();
 }
 initDataTable('.table-po-report', site_url + 'sales_agent/portal/po_report', false, false, fnServerParams);
}

function total_income_rp() {
  "use strict";

  var data = {};
   data.year = $('select[name="year_requisition"]').val();
   data.report_currency = $('select[name="currency"]').val();
  $.post(site_url + 'sales_agent/portal/total_income_report', data).done(function(response) {
     response = JSON.parse(response);
        Highcharts.setOptions({
      chart: {
          style: {
              fontFamily: 'inherit !important',
              fill: 'black'
          }
      },
      colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
     });
        Highcharts.chart('container_total_income', {
         chart: {
             type: 'column'
         },
         title: {
             text: '<?php echo _l('total_income_by_sale_invoices') ?>'
         },
         subtitle: {
             text: ''
         },
         credits: {
            enabled: false
          },
         xAxis: {
             categories: ['<?php echo _l('month_1') ?>',
                '<?php echo _l('month_2') ?>',
                '<?php echo _l('month_3') ?>',
                '<?php echo _l('month_4') ?>',
                '<?php echo _l('month_5') ?>',
                '<?php echo _l('month_6') ?>',
                '<?php echo _l('month_7') ?>',
                '<?php echo _l('month_8') ?>',
                '<?php echo _l('month_9') ?>',
                '<?php echo _l('month_10') ?>',
                '<?php echo _l('month_11') ?>',
                '<?php echo _l('month_12') ?>'],
             crosshair: true,
         },
         yAxis: {
             min: 0,
             title: {
              text: response.name
             }
         },
         tooltip: {
             headerFormat: '<span >{point.key}</span><table>',
             pointFormat: '<tr>' +
                 '<td><b>{point.y:.0f} {series.name}</b></td></tr>',
             footerFormat: '</table>',
             shared: true,
             useHTML: true
         },
         plotOptions: {
             column: {
                 pointPadding: 0.2,
                 borderWidth: 0
             }
         },

         series: [{
            type: 'column',
            colorByPoint: true,
            name: response.unit,
            data: response.data,
            showInLegend: false,
         }]
     });
        
  })
}

function total_expenses_rp() {
  "use strict";

  var data = {};
   data.year = $('select[name="year_requisition"]').val();
   data.report_currency = $('select[name="currency"]').val();
  $.post(site_url + 'sales_agent/portal/total_expense_report', data).done(function(response) {
     response = JSON.parse(response);
        Highcharts.setOptions({
      chart: {
          style: {
              fontFamily: 'inherit !important',
              fill: 'black'
          }
      },
      colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
     });
        Highcharts.chart('container_total_expenses', {
         chart: {
             type: 'column'
         },
         title: {
             text: '<?php echo _l('total_expenses_by_purchase_orders') ?>'
         },
         subtitle: {
             text: ''
         },
         credits: {
            enabled: false
          },
         xAxis: {
             categories: ['<?php echo _l('month_1') ?>',
                '<?php echo _l('month_2') ?>',
                '<?php echo _l('month_3') ?>',
                '<?php echo _l('month_4') ?>',
                '<?php echo _l('month_5') ?>',
                '<?php echo _l('month_6') ?>',
                '<?php echo _l('month_7') ?>',
                '<?php echo _l('month_8') ?>',
                '<?php echo _l('month_9') ?>',
                '<?php echo _l('month_10') ?>',
                '<?php echo _l('month_11') ?>',
                '<?php echo _l('month_12') ?>'],
             crosshair: true,
         },
         yAxis: {
             min: 0,
             title: {
              text: response.name
             }
         },
         tooltip: {
             headerFormat: '<span >{point.key}</span><table>',
             pointFormat: '<tr>' +
                 '<td><b>{point.y:.0f} {series.name}</b></td></tr>',
             footerFormat: '</table>',
             shared: true,
             useHTML: true
         },
         plotOptions: {
             column: {
                 pointPadding: 0.2,
                 borderWidth: 0
             }
         },

         series: [{
            type: 'column',
            colorByPoint: true,
            name: response.unit,
            data: response.data,
            showInLegend: false,
         }]
     });
        
  })
}

// Main generate report function
function gen_reports() {
  "use strict";

 if (!invoices_rp.hasClass('hide')) {
   list_inv_report();
 }else if(!po_rp.hasClass('hide')){
    po_report();
 }else if(!total_income.hasClass('hide')){
    total_income_rp();
 }else if(!total_expenses.hasClass('hide')){
    total_expenses_rp();
 }
}
</script>


