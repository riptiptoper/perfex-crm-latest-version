<script>

 var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var fnServerParams = {
   "report_months": '[name="months-report"]',
   "report_from": '[name="report-from"]',
   "report_to": '[name="report-to"]',
   "by_month": '[name="by_month"]',
 }


 $(function() {
  "use strict";
   $('select[name="channel[]"],select[name="group[]"],select[name="product[]"],select[name="by_month"],select[name="by_year"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"]').on('change', function() {
         gen_reports();
       });
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

  
$i = 0;
 function init_report(e, type) {
  "use strict";
  $('.filter_2').addClass('hide');
  $('.time_filter').addClass('hide');
  $('#container_total').addClass('hide')
  $('.table_history').addClass('hide');

  if(type == 'trade_discount_application_history'){
    $('.table_history').removeClass('hide');
  }
  $i = 0;
  if(type == 'sales_statistics_by_week'){
      $('.chart_title').text($(e).text());
      $('.filter_2').removeClass('hide');
      $('#container_total').removeClass('hide')
      $i = 0;
   }
   if(type == 'sales_statistics_by_month'){
      $('.by_month').removeClass('hide');
      $('.filter_2').removeClass('hide');
      $('.chart_title').text($(e).text());
      $('#container_total').removeClass('hide')
      $i = 1;
   }
   if(type == 'sales_statistics_by_year'){
      $('.by_year').removeClass('hide');
      $('.filter_2').removeClass('hide');
      $('.chart_title').text($(e).text());
      $('#container_total').removeClass('hide')
      $i = 2;
   }
   if(type == 'sales_statistics_by_stage'){
      $('.by_stage').removeClass('hide');
      $('.filter_2').removeClass('hide');
      $('.chart_title').text($(e).text());
      $('#container_total').removeClass('hide')
      $i = 3;
   }
   gen_reports();
}

   function gen_reports() {
    "use strict";
     if (!$('#container_total').hasClass('hide')) {
       total_statitic();
     } 
  }

  function total_statitic(){
    "use strict";
     var data = {};
     data.months_report = $('select[name="months-report"]').val();
     data.by_year = $('select[name="by_year"]').val();
     data.by_month = $('select[name="by_month"]').val();
     data.by_channel = $('select[name="channel[]"]').val();
     data.by_group = $('select[name="group[]"]').val();
     data.by_product = $('select[name="product[]"]').val();
     data.type = $i;

     data.report_from = report_from.val();
     data.report_to = report_to.val();
     $.post(admin_url + 'omni_sales/total_statitic', data).done(function(response) {
       response = JSON.parse(response);
          Highcharts.chart('container_total', {
            chart: {
              type: 'column'
            },
            title: {
              text: ''
            },
            subtitle: {
              text: ''
            },
            xAxis: {
              categories: response.category,
              crosshair: true
            },
            yAxis: {
              min: 0,
              title: {
                text: 'Order'
              }
            },
            tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y} order</b></td></tr>',
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
            credits:{
             enabled:false,
            },
            series: response.data
          });

     });
  }
</script>
