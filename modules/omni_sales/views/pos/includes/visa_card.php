 <div class="row payment_ui kcb_visa_card hide">
  <div class="col-md-6">
    <?php echo render_input('card_number', 'card_number','','', array('maxlength' => 20), [], 'required'); ?>
  </div>
  <div class="col-md-6">
    <?php echo render_input('holder_name', 'holder_name','', '', array('maxlength' => 100), [], 'required'); ?>
  </div>
  <div class="col-md-6">

    <div class="row">
      <div class="col-md-6">
        <?php echo render_input('month_expire', 'expire_date', '','number', array('min' => 1, 'max' => 12 , 'placeholder' => _l('month')), [], 'required'); ?>
      </div>
      <div class="col-md-6 pt-7">
        <br>
        <?php $cur_year = date('Y'); echo render_input('year_expire', '', '','number', array('min' => $cur_year, 'max' => $cur_year + 20, 'placeholder' => _l('year')), [], 'required'); ?>
      </div>
    </div>

  </div>
  <div class="col-md-6">
    <?php echo render_input('security_code', 'security_code','', 'password',  array('maxlength' => 6), [], 'required'); ?>
  </div>
</div>