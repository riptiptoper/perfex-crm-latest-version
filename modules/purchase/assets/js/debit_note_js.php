<script>
(function($) {
  "use strict";
   validate_debit_note_form();
       // Init accountacy currency symbol
    init_currency();

    <?php if(get_purchase_option('item_by_vendor') != 1){ ?>
      init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search');
    <?php } ?>

    $('select[name="vendorid"]').on('change', function(){
    	var vendor = $(this).val();
    	if(vendor != '' && vendor != null && vendor != undefined){
	    	requestGetJSON('purchase/vendor_change_data/' + vendor ).done(function (response) {

	            for (var f in billingAndShippingFields) {
	                if (billingAndShippingFields[f].indexOf('billing') > -1) {
	                    if (billingAndShippingFields[f].indexOf('country') > -1) {
	                        $('select[name="' + billingAndShippingFields[f] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[f]]);
	                    } else {
	                        if (billingAndShippingFields[f].indexOf('billing_street') > -1) {
	                            $('textarea[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
	                        } else {
	                            $('input[name="' + billingAndShippingFields[f] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[f]]);
	                        }
	                    }
	                }
	            }

	            if (!empty(response['billing_shipping'][0]['shipping_street'])) {
	                $('input[name="include_shipping"]').prop("checked", true).change();
	            }

	            for (var fsd in billingAndShippingFields) {
	                if (billingAndShippingFields[fsd].indexOf('shipping') > -1) {
	                    if (billingAndShippingFields[fsd].indexOf('country') > -1) {
	                        $('select[name="' + billingAndShippingFields[fsd] + '"]').selectpicker('val', response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                    } else {
	                        if (billingAndShippingFields[fsd].indexOf('shipping_street') > -1) {
	                            $('textarea[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                        } else {
	                            $('input[name="' + billingAndShippingFields[fsd] + '"]').val(response['billing_shipping'][0][billingAndShippingFields[fsd]]);
	                        }
	                    }
	                }
	            }

	            init_billing_and_shipping_details();

	            var vendor_currency = response['vendor_currency'];
	            var s_currency = $("body").find('.accounting-template select[name="currency"]');
	            vendor_currency = parseInt(vendor_currency);
	            vendor_currency != 0 ? s_currency.val(vendor_currency) : s_currency.val(s_currency.data('base'));
	           
	            s_currency.selectpicker('refresh');

	            <?php if(get_purchase_option('item_by_vendor') == 1){ ?>
			        if(response.option_html != ''){
			         $('#item_select').html(response.option_html);
			         $('.selectpicker').selectpicker('refresh');
			        }else if(response.option_html == ''){
			          init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'purchase/pur_commodity_code_search/purchase_price/can_be_purchased/'+invoker.value);
			        }
			        
			    <?php } ?>


	            init_currency();
	        });
	    }
    });
})(jQuery);

function validate_debit_note_form(selector) {
	"use strict";
    selector = typeof (selector) == 'undefined' ? '#debit-note-form' : selector;

    appValidateForm($(selector), {
        vendorid: 'required',
        date: 'required',
        currency: 'required',
        number: {
            required: true,
        }
    });

    $("body").find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "purchase/validate_debit_note_number",
            type: 'post',
            data: {
                number: function () {
                    return $('input[name="number"]').val();
                },
                isedit: function () {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function () {
                    return $('input[name="number"]').data('original-number');
                },
                date: function () {
                    return $(".debit_note input[name='date']").val();
                },
            }
        },
        messages: {
            remote: app.lang.debit_note_number_exists,
        }
    });
}

</script>