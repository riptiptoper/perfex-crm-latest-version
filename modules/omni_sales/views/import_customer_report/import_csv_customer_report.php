<?php defined('BASEPATH') or exit('No direct script access allowed'); 
?>
<?php 
  $file_header = array();

    $file_header[] = _l('ser_no');
    $file_header[] = _l('authorized_by');
    $file_header[] = _l('date');
    $file_header[] = _l('time');
    $file_header[] = _l('transaction_id');
    $file_header[] = _l('receipt');
    $file_header[] = _l('pay_mode');
    $file_header[] = _l('nozzle');
    $file_header[] = _l('product');
    $file_header[] = _l('quantity');
    $file_header[] = _l('total_sale');
    $file_header[] = _l('ref_slip_no');

 ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div id ="dowload_file_sample">
            </div>
            <?php if(!isset($simulate)) { ?>
            <div class="table-responsive no-dt">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <?php
                      $total_fields = 0;
                      
                      for($i=0;$i<count($file_header);$i++){
                       
                          ?>
                          <th class="bold"><?php echo html_entity_decode($file_header[$i]) ?> </th>
                          <?php 
                          
                          $total_fields++;
                      }
                    ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php for($i = 0; $i<1;$i++){
                      echo '<tr>';
                      for($x = 0; $x<count($file_header);$x++){
                        echo '<td>- </td>';
                      }
                      echo '</tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <hr>
              <?php } ?>
            <div class="row">
              <div class="col-md-4">
               <?php echo form_open_multipart(admin_url('omni_sales/import_transaction_csv'),array('id'=>'import_form')) ;?>
                    <?php echo form_hidden('leads_import','true'); ?>
                    <?php echo render_input('file_csv','choose_csv_file','','file'); ?> 

                    <div class="form-group">
                      <button id="uploadfile" type="submit" class="btn btn-info import"><?php echo _l('import'); ?></button>
                    </div>
                  <?php echo form_close(); ?>
              </div>
              <div class="col-md-8">
                <div class="form-group" id="file_upload_response">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<?php require 'modules/omni_sales/assets/js/customer_report/import_csv_customer_report_js.php';?>
</body>
</html>