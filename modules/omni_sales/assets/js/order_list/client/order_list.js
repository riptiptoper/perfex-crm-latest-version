(function(){
  "use strict";

  $('select[name="type_filter"]').on('change', function() {
    $('#list_cart').html('');
    var status = $('input[name="status"]').val();
    var type = $(this).val();
    var ofset = 0;
    var limit = 20;
    if(status == ''){
      status = 0;
    }
    $.ajax({
     url: "/omni_sales/omni_sales_client/get_order_list/"+status+'/'+type+'/'+ofset+'/'+limit,
     type: "post",
     data: {'get_csrf_token_name': $('input[name="token"]').val()},
     success: function(){
     },
     error:function(){
    }
  }).done(function(response) {
    response = JSON.parse(response);
    $('#list_cart').html(response.html);
  });


});

$('.scroller.arrow-left').on('click', function(){
  var scroll_frame = $('.order-tab .horizontal-tabs ul');
  var frame_width = scroll_frame.width() - 20;
  scroll_frame.animate({
    scrollLeft: "-="+frame_width+"px"
  }, "slow");
});
$('.scroller.arrow-right').on('click', function(){
  var scroll_frame = $('.order-tab .horizontal-tabs ul');
  var frame_width = scroll_frame.width() - 20;
  scroll_frame.animate({
    scrollLeft: "+="+frame_width+"px"
  }, "slow");
});

})(jQuery);
