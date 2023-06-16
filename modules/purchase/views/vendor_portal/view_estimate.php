<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<?php if($estimate->currency != 0){
  $base_currency = pur_get_currency_by_id($estimate->currency);
}
 ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">

       
            <div class="horizontal-scrollable-tabs preview-tabs-top">
            
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="<?php if($this->input->get('tab') != 'discussion' ){echo 'active';} ?>">
                     <a href="#general_infor" aria-controls="general_infor" role="tab" data-toggle="tab">
                     <?php echo _l('general_infor'); ?>
                     </a>
                  </li>
                  
                  <li role="presentation" class="tab-separator <?php if($this->input->get('tab') === 'discussion'){echo 'active';} ?>">
                    <?php
                              $totalComments = total_rows(db_prefix().'pur_comments',['rel_id' => $estimate->id, 'rel_type' => 'pur_quotation']);
                              ?>
                     <a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab">
                     <?php echo _l('pur_discuss'); ?>
                      <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                     </a>
                  </li> 
                  
               </ul>
            </div>
         </div>
          <div class="tab-content">
             <div role="tabpanel" class="tab-pane ptop10 <?php if($this->input->get('tab') != 'discussion' ){echo 'active';} ?>" id="general_infor">
              <div class="row">


               <div class="col-md-6">
                  
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('estimate_add_edit_number'); ?></td>
                      <td><?php echo format_pur_estimate_number($estimate->id) ?></td>
                    </tr>
                   
                    <tr>
                      <td><?php echo _l('status'); ?></td>
                      <td><?php echo get_status_approve($estimate->status) ?></td>
                    </tr>

                    <?php if($estimate->pur_request != ''){ 
                        $this->load->model('purchase/purchase_model');
                        $pur_request = $this->purchase_model->get_purchase_request($estimate->pur_request->id);
                        if($pur_request && !is_array($pur_request)){
                      ?>
                      <tr>
                        <td><?php echo _l('purchase_request'); ?></td>
                        <td><a href="<?php echo site_url('purchase/vendors_portal/pur_request/'.$estimate->pur_request->id.'/'.$pur_request->hash) ?>"><?php echo html_entity_decode($pur_request->pur_rq_code); ?></a></td>
                      </tr>
                    <?php } } ?>
                  </table>
               </div>
               <div class="col-md-6">
                  <table class="table table-striped table-bordered">
                    <tr>
                      <td width="30%"><?php echo _l('estimate_add_edit_date'); ?></td>
                      <td><?php echo _d($estimate->date) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('expiry_date'); ?></td>
                      <td><?php echo _d($estimate->expirydate) ?></td>
                    </tr>
                    <tr>
                      <td><?php echo _l('total'); ?></td>
                      <td><?php echo app_format_money($estimate->total, $base_currency) ?></td>
                    </tr>
                  </table>
               </div>
               </div>
            </div>

            <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') === 'discussion'){echo ' active';} ?>" id="discuss">
              <?php echo form_open($this->uri->uri_string()) ;?>
               <div class="contract-comment">
                  <textarea name="content" rows="4" class="form-control"></textarea>
                  <button type="submit" class="btn btn-info mtop10 pull-right" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('proposal_add_comment'); ?></button>
                  <?php echo form_hidden('action','quo_comment'); ?>
               </div>
               <?php echo form_close(); ?>
               <div class="clearfix"></div>
               <?php
                  $comment_html = '';
                  foreach ($comments as $comment) {
                   $comment_html .= '<div class="contract_comment mtop10 mbot20" data-commentid="' . $comment['id'] . '">';
                   if($comment['staffid'] != 0){
                    $comment_html .= staff_profile_image($comment['staffid'], array(
                     'staff-profile-image-small',
                     'media-object img-circle pull-left mright10'
                  ));
                  }
                  $comment_html .= '<div class="media-body valign-middle">';
                  $comment_html .= '<div class="mtop5">';
                  $comment_html .= '<b>';
                  if($comment['staffid'] != 0){
                    $comment_html .= get_staff_full_name($comment['staffid']);
                  } else {
                    $comment_html .= get_vendor_company_name(get_vendor_user_id());
                  }
                  $comment_html .= '</b>';
                  $comment_html .= ' - <small class="mtop10 text-muted">' . time_ago($comment['dateadded']) . '</small>';
                  $comment_html .= '</div>';
              
                  $comment_html .= check_for_links($comment['content']) . '<br />';
                  $comment_html .= '</div>';
                  $comment_html .= '</div>';
                  $comment_html .= '<hr />';
                  }
                  echo $comment_html; ?>
            </div>

          </div>
          
        </div>
        <div class="panel-body mtop10">
        <div class="row">
        <div class="col-md-12">
          <div class="table-responsive">
             <table class="table items items-preview estimate-items-preview" data-type="estimate">
                <thead>
                   <tr>
                      <th align="center">#</th>
                      <th class="description" width="25%" align="left"><?php echo _l('items'); ?></th>
                      <th align="right"><?php echo _l('purchase_quantity'); ?></th>
                      <th align="right"><?php echo _l('purchase_unit_price'); ?></th>
                      <th align="right"><?php echo _l('into_money'); ?></th>
                      <?php if(get_option('show_purchase_tax_column') == 1){ ?>
                      <th align="right"><?php echo _l('tax'); ?></th>
                      <?php } ?>
                      <th align="right"><?php echo _l('sub_total'); ?></th>
                      <th align="right"><?php echo _l('discount(%)'); ?></th>
                      <th align="right"><?php echo _l('discount(money)'); ?></th>
                      <th align="right"><?php echo _l('total'); ?></th>
                   </tr>
                </thead>
                <tbody class="ui-sortable">

                   <?php if(count($estimate_detail) > 0){
                      $count = 1;
                      $t_mn = 0;
                   foreach($estimate_detail as $es) { ?>
                   <tr nobr="true" class="sortable">
                      <td class="dragger item_no ui-sortable-handle" align="center"><?php echo html_entity_decode($count); ?></td>
                      <td class="description" align="left;"><span><strong><?php 
                      $item = get_item_hp2($es['item_code']); 
                      if(isset($item) && isset($item->commodity_code) && isset($item->description)){
                         echo html_entity_decode($item->commodity_code.' - '.$item->description);
                      }else{
                         echo html_entity_decode($es['item_name']);
                      }
                      ?></strong></td>
                      <td align="right"  width="12%"><?php echo html_entity_decode($es['quantity']); ?></td>
                      <td align="right"><?php echo app_format_money($es['unit_price'],$base_currency->symbol); ?></td>
                      <td align="right"><?php echo app_format_money($es['into_money'],$base_currency->symbol); ?></td>
                      <?php if(get_option('show_purchase_tax_column') == 1){ ?>
                      <td align="right"><?php echo app_format_money(($es['total'] - $es['into_money']),$base_currency->symbol); ?></td>
                      <?php } ?>
                      <td class="amount" align="right"><?php echo app_format_money($es['total'],$base_currency->symbol); ?></td>
                      <td class="amount" width="12%" align="right"><?php echo ($es['discount_%'].'%'); ?></td>
                      <td class="amount" align="right"><?php echo app_format_money($es['discount_money'],$base_currency->symbol); ?></td>
                      <td class="amount" align="right"><?php echo app_format_money($es['total_money'],$base_currency->symbol); ?></td>
                   </tr>
                <?php 
                $t_mn += $es['total_money'];
                $count++; } } ?>
                </tbody>
             </table>
          </div>
         <div class="col-md-6 col-md-offset-6">
           <table class="table text-right">
               <tbody>
                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('subtotal'); ?></span>
                     </td>
                     <td class="subtotal">
                        <?php echo app_format_money($estimate->subtotal,$base_currency->symbol); ?>
                     </td>
                  </tr>

                  <?php if($tax_data['preview_html'] != ''){
                    echo html_entity_decode($tax_data['preview_html']);
                  } ?>


                  <?php if($estimate->discount_total > 0){ ?>
                  
                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('discount_total(money)'); ?></span>
                     </td>
                     <td class="subtotal">
                        <?php echo '-'.app_format_money($estimate->discount_total, $base_currency->symbol); ?>
                     </td>
                  </tr>
                  <?php } ?>

                  <?php if($estimate->shipping_fee > 0){ ?>
                    <tr id="subtotal">
                      <td><span class="bold"><?php echo _l('pur_shipping_fee'); ?></span></td>
                      <td class="subtotal">
                        <?php echo app_format_money($estimate->shipping_fee, $base_currency->symbol); ?>
                      </td>
                    </tr>
                  <?php } ?>

                  <tr id="subtotal">
                     <td><span class="bold"><?php echo _l('total'); ?></span>
                     </td>
                     <td class="subtotal bold">
                        <?php echo app_format_money($estimate->total, $base_currency->symbol); ?>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div> 

         <?php if($estimate->vendornote != ''){ ?>
           <div class="col-md-12 mtop15">
              <p class="bold text-muted"><?php echo _l('estimate_note'); ?></p>
              <p><?php echo html_entity_decode($estimate->vendornote); ?></p>
           </div>
           <?php } ?>
                                                  
           <?php if($estimate->terms != ''){ ?>
           <div class="col-md-12 mtop15">
              <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
              <p><?php echo html_entity_decode($estimate->terms); ?></p>
           </div>
           <?php } ?>

        </div>
      </div>
        </div>
      
        </div>

      </div>
    
      
    </div>
  </div>
</div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>
</body>
</html>