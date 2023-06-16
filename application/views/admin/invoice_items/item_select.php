<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div
    class="form-group row mbot25 items-wrapper " style="display: flex;">
    <div class="col-md-6 " style="margin-right: 20px">
        <div class="items-select-wrapper">
            <select onchange="get_offer()"
                class="selectpicker no-margin"
                data-width="false" id="offer_select" data-none-selected-text="<?php echo _l('select_offer'); ?>"
                data-live-search="true">
                <option value=""></option>
                <option value="Bucatarie custom">Bucatarie custom</option>
                <option value="Bucatarie modulara">Bucatarie modulara</option>
                <option value="Dressing">Dressing</option>
                <option value="Dressing 2">Dressing 2</option>
                <option value="Dressing 3">Dressing 3</option>
                <option value="Dressing 4">Dressing 4</option>
                <option value="Birouri reglabile">Birouri reglabile</option>
                <option value="Mobilier birou">Mobilier birou</option>
                <option value="Scaune">Scaune</option>
                <option value="Electrocasnice">Electrocasnice</option>
                <option value="Diverse">Diverse</option>
                <option value="Servicii">Servicii</option>
            </select>
        </div>
    </div>
    <div class="col-md-6" style="margin-right: 20px">
        <div class="items-select-wrapper">
            <select onchange="get_items()"
                class="selectpicker no-margin"
                data-width="false" id="category_select" data-none-selected-text="<?php echo _l('select_category'); ?>"
                data-live-search="true">
                <option value=""></option>
            </select>
        </div>
    </div>
</div>
<div>
    <br/>
    <div class="col-md-11 <?php echo staff_can('create', 'items') ? 'input-group input-group-select' : ''; ?>">
        <div class="items-select-wrapper  items_lists">
            <select name="item_select"
                class="selectpicker no-margin <?php echo staff_can('create', 'items') ? ' _select_input_group' : ''; ?>"
                data-width="false" id="item_select" data-none-selected-text="<?php echo _l('add_item'); ?>"
                data-live-search="true">
                <option value=""></option>
            </select>
        </div>
        <?php if (staff_can('items', '', 'create')) { ?>
        <div class="input-group-btn">
            <a href="#" data-toggle="modal" class="btn btn-default" data-target="#sales_item_modal">
                <i class="fa fa-plus"></i>
            </a>
        </div>
        <?php } ?>
    </div>
    <style>
        .dropdown {
            width: 100% !important;
        }
    </style>
    <script>
        function get_offer() {
            $.ajax({
                type: 'post',
                url: admin_url + 'proposals/get_categories_by_offer',
                data: {
                    'offer': $('#offer_select').val(),
                    'token': csrfData['hash']
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    var categoryHtml = '<option value=""></option>';
                    if (result) {
                        result.forEach((res, index) => {
                            var man;
                            if (res.is_mandatory === '1') man = " (Mandatory)";
                            else man="";

                            categoryHtml += '<option value="' + res.id + '">' + res.name +  man + '</option>'                        
                        });
                        $('#category_select').html(categoryHtml);
                        $('#category_select').selectpicker("refresh");
                    }
                    $('.items_list .dropdown').css('width', '100%', '!important')
                    
                },
            });
        }

        function get_items() {
            $.ajax({
                type: 'post',
                url: admin_url + 'proposals/get_items_by_category',
                data: {
                    'category_id': $('#category_select').val(),
                    'token': csrfData['hash']
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    var itemHtml = '<option value=""></option>';
                    if (result) {
                        result.forEach((res, index) => {
                            itemHtml += '<option value="' + res.id + '"data-subtext="' + res.long_description.substr(0,60) + '">(' + res.rate + ') ' + res.description + '</span></option>'                        
                        });
                        $('#item_select').html(itemHtml);
                        $('#item_select').selectpicker("refresh");
                        $('.dropdown .items_lists').css('width', '100%')
                    }
                },
            });
        }
    </script>
    
</div>