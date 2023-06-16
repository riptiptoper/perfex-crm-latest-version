<?php hooks()->do_action('head_element_client'); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'invoice-form','class'=>'_transaction_form invoice-form'));
			?>
			<input type="hidden" name="id">
			<input type="hidden" name="customer" value="<?php echo html_entity_decode($userid); ?>">

			<div class="col-md-12">
				<?php $this->load->view('client/pre_order/includes/invoice_template'); ?>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<?php hooks()->do_action('client_pt_footer_js'); ?>
<script>
  $(function(){

    init_ajax_search('pre_order','#item_select',{keywork: 'abc'}, site_url+'omni_sales/omni_sales_client/get_item_pre_order');

    function init_ajax_search(type, selector, server_data, url){
	    var ajaxSelector = $('body').find(selector);
	    if(ajaxSelector.length){
	      var options = {
	        ajax: {
	          url: url,
	          data: function () {
	            var data = {};
	            data.type = type;
	            data.rel_id = '';
	            data.q = '{{{q}}}';
	            if(typeof(server_data) != 'undefined'){
	              jQuery.extend(data, server_data);
	            }
	            return data;
	          }
	        },
	        locale: {
	          emptyTitle: "<?php echo _l('search_ajax_empty'); ?>",
	          statusInitialized: "<?php echo _l('search_ajax_initialized'); ?>",
	          statusSearching:"<?php echo _l('search_ajax_searching'); ?>",
	          statusNoResults:"<?php echo _l('not_results_found'); ?>",
	          searchPlaceholder:"<?php echo _l('search_ajax_placeholder'); ?>",
	          currentlySelected:"<?php echo _l('currently_selected'); ?>",
	        },
	        requestDelay:500,
	        cache:false,
	        preprocessData: function(processData){
	          var bs_data = [];
	          var len = processData.length;
	          for(var i = 0; i < len; i++){
	            var tmp_data =  {
	              'value': processData[i].id,
	              'text': processData[i].name,
	            };
	            if(processData[i].subtext){
	              tmp_data.data = {subtext:processData[i].subtext}
	            }
	            bs_data.push(tmp_data);
	          }
	          return bs_data;
	        },
	        preserveSelectedPosition:'after',
	        preserveSelected:true
	      }
	      if(ajaxSelector.data('empty-title')){
	        options.locale.emptyTitle = ajaxSelector.data('empty-title');
	      }
	      ajaxSelector.selectpicker().ajaxSelectPicker(options);
	    }
  }

  });
</script>