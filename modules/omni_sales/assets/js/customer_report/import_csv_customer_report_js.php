<script>
	(function($) {
		"use strict";
		appValidateForm($('#import_form'),{file_csv:{required:true,extension: "csv"},source:'required',status:'required'});
		$( "#dowload_file_sample" ).append( '<a href="'+ site_url+'modules/omni_sales/uploads/file_sample/Sample_import_customer_report_file_en_new.csv" class="btn btn-primary" ><?php echo _l('download_sample') ?></a><hr>' );
	})(jQuery);
</script>