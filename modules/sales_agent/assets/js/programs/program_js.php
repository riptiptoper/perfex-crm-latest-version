<script>
(function($) {
  "use strict";

  group_it_change();
})(jQuery);

function group_it_change() {
"use strict";
var group = $('select[name="group_item"]').val();
if(group != ''){
  requestGet(admin_url + 'purchase/group_it_change/'+group).done(function(response){
    response = JSON.parse(response);
    if(response.html != ''){
      $('select[id="items"]').html('');
      $('select[id="items"]').append(response.html);
      $('select[id="items"]').selectpicker('refresh');
    }else{
      init_ajax_search('items','#items.ajax-search',undefined,admin_url+'sales_agent/commodity_code_search/purchase_price/can_be_sold/'+group);
      $('select[id="items"]').html('');
      $('select[id="items"]').selectpicker('refresh');
    }
  });
}else{
  init_ajax_search('items','#items.ajax-search',undefined,admin_url+'sales_agent/commodity_code_search');
  requestGet(admin_url + 'sales_agent/group_it_change/'+group).done(function(response){
    response = JSON.parse(response);
    if(response.html != ''){
      $('select[id="items"]').html('');
      $('select[id="items"]').append(response.html);
      $('select[id="items"]').selectpicker('refresh');
    }else{
      $('select[id="items"]').html('');
      $('select[id="items"]').selectpicker('refresh');
    }
  });
}
}

 var addMoreLadderInputKey = $('.discount_list_ladder_setting #discount_item_ladder_setting').length;

  $("body").on('click', '.new_discount_item_ladder', function() {
    if ($(this).hasClass('disabled')) { return false; }

    addMoreLadderInputKey++;
    var newItem = $('.discount_list_ladder_setting').find('#discount_item_ladder_setting').eq(0).clone().appendTo('.discount_list_ladder_setting');
    newItem.find('button[role="combobox"]').remove();
    newItem.find('select').selectpicker('refresh');


    newItem.find('button[data-id="product[0][]"]').attr('data-id', 'product[' + addMoreLadderInputKey + '][]');
    newItem.find('label[for="product[0][]"]').attr('for', 'product[' + addMoreLadderInputKey + '][]');
    newItem.find('select[name="product[0][]"]').attr('name', 'product[' + addMoreLadderInputKey + '][]');
    newItem.find('select[id="product[0][]"]').attr('id', 'product[' + addMoreLadderInputKey + '][]').selectpicker('refresh');
    

    newItem.find('button[data-id="product_group[0][]"]').attr('data-id', 'product_group[' + addMoreLadderInputKey + '][]');
    newItem.find('label[for="product_group[0][]"]').attr('for', 'product_group[' + addMoreLadderInputKey + '][]');
    newItem.find('select[name="product_group[0][]"]').attr('name', 'product_group[' + addMoreLadderInputKey + '][]');
    newItem.find('select[id="product_group[0][]"]').attr('id', 'product_group[' + addMoreLadderInputKey + '][]').selectpicker('refresh');
    
    newItem.find('input[id="from_amount[0]"]').attr('name', 'from_amount[' + addMoreLadderInputKey + ']').val('');
    newItem.find('input[id="from_amount[0]"]').attr('id', 'from_amount[' + addMoreLadderInputKey + ']').val('');

    newItem.find('input[id="to_amount[0]"]').attr('name', 'to_amount[' + addMoreLadderInputKey + ']').val('');
    newItem.find('input[id="to_amount[0]"]').attr('id', 'to_amount[' + addMoreLadderInputKey + ']').val('');

    newItem.find('input[id="discount[0]"]').attr('name', 'discount[' + addMoreLadderInputKey + ']').val('');
    newItem.find('input[id="discount[0]"]').attr('id', 'discount[' + addMoreLadderInputKey + ']').val('');

    newItem.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
    newItem.find('button[name="add"]').removeClass('new_discount_item_ladder').addClass('remove_item_ladder').removeClass('btn-success').addClass('btn-danger');


  });


   $("body").on('click', '.remove_item_ladder', function() {

      $(this).parents('#discount_item_ladder_setting').remove();
  });

</script>