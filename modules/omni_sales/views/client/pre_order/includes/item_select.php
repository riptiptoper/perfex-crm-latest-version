<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="form-group mbot25 items-wrapper select-placeholder">
    <div class="form-group select-placeholder" id="ticket_contact_w">
      <label for="item_select"><?php echo _l('add_item'); ?></label>
      <select name="item_select" required="true" id="item_select" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <option value=""></option>
      </select>
    </div>
</div>
