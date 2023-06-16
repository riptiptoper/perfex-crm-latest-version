<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Is affiliate logged in
 * @return boolean
 */
function is_sale_agent_logged_in()
{
    return get_instance()->session->has_userdata('sale_agent_logged_in');
}


/**
 * init affiliate area assets.
 */
function init_sale_agent_area_assets()
{
    // Used by themes to add assets
    hooks()->do_action('app_sale_agent_assets');

    hooks()->do_action('app_sale_agent_assets_added');
}

/**
 * { register theme affiliate assets hook }
 *
 * @param      <type>   $function  The function
 *
 * @return     boolean
 */
function register_theme_sale_agent_assets_hook($function)
{
    if (hooks()->has_action('app_sale_agent_assets', $function)) {
        return false;
    }

    return hooks()->add_action('app_sale_agent_assets', $function, 1);
}

/**
 * Return logged affiliate User ID from session
 * @return mixed
 */
function get_sale_agent_user_id()
{
    if (!is_sale_agent_logged_in()) {
        return false;
    }

    return get_instance()->session->userdata('sale_agent_user_id');
}

/**
 * Get contact user id
 * @return mixed
 */
function get_sa_contact_user_id()
{
    $CI = &get_instance();
    if (!$CI->session->has_userdata('sale_agent_contact_user_id')) {
        return false;
    }

    return $CI->session->userdata('sale_agent_contact_user_id');
}

/**
 * Gets the template part.
 *
 * @param      string   $name    The name
 * @param      array    $data    The data
 * @param      boolean  $return  The return
 *
 * @return     string   The template part.
 */
function get_sale_agent_template_part($name, $data = [], $return = false)
{
    if ($name === '') {
        return '';
    }

    $CI   = &get_instance();
    $path = 'portal/template_parts/';

    if ($return == true) {
        return $CI->load->view($path . $name, $data, true);
    }

    $CI->load->view($path . $name, $data);
}

/**
 * app affiliate footer
 */
function app_sale_agent_footer()
{
    /**
     * Registered scripts
     */
    echo compile_theme_scripts();

    /**
     * @deprecated 2.3.0
     * Moved from themes/[THEME]/views/scripts.php
     * Use app_sale_agent_footer hook instead
     */
    do_action_deprecated('sale_agent_after_js_scripts_load', [], '2.3.0', 'app_sale_agent_footer');

    hooks()->do_action('app_sale_agent_footer');
}

/**
 * affiliates area head
 * @param  string $language @deprecated 2.3.0
 * @return null
 */
function app_sale_agent_head($language = null)
{
    // $language param is deprecated
    if (is_null($language)) {
        $language = $GLOBALS['language'];
    }

    if (file_exists(FCPATH . 'assets/css/custom.css')) {
        echo '<link href="' . base_url('assets/css/custom.css') . '" rel="stylesheet" type="text/css" id="custom-css">' . PHP_EOL;
    }

    hooks()->do_action('app_sale_agent_head');
}

/**
 * { app theme head hook }
 */
function app_theme_sale_agent_head_hook()
{
    $CI = &get_instance();
    ob_start();
    echo get_custom_fields_hyperlink_js_function();

    if (get_option('use_recaptcha_customers_area') == 1
        && get_option('recaptcha_secret_key') != ''
        && get_option('recaptcha_site_key') != '') {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    $isRTL = 'false';

    $locale = get_locale_key($GLOBALS['language']);

    $maxUploadSize = file_upload_max_size();

    $date_format = get_option('dateformat');
    $date_format = explode('|', $date_format);
    $date_format = $date_format[0];?>
    <script>
        <?php if (is_sale_agent_logged_in()) {
        ?>
        var admin_url = '<?php echo admin_url(); ?>';
        <?php
}?>

        var site_url = '<?php echo site_url(''); ?>',
        app = {},
        cfh_popover_templates  = {};

        var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;

        if (typeof(jQuery) == 'undefined') {
            window.deferAfterjQueryLoaded.push(function() {
                csrf_jquery_ajax_setup();
            });
            window.addEventListener('load', function() {
                csrf_jquery_ajax_setup();
            }, true);
        } else {
            csrf_jquery_ajax_setup();
        }

        function csrf_jquery_ajax_setup() {
            $.ajaxSetup({
                data: csrfData.formatted
            });

            $(document).ajaxError(function(event, request, settings) {
                if (request.status === 419) {
                    alert_float('warning', 'Page expired, refresh the page make an action.')
                }
            });
        }

        app.isRTL = '<?php echo html_entity_decode($isRTL); ?>';
        app.is_mobile = '<?php echo is_mobile(); ?>';
        app.months_json = '<?php echo json_encode([_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December')]); ?>';

        app.browser = "<?php echo strtolower($CI->agent->browser()); ?>";
        app.max_php_ini_upload_size_bytes = "<?php echo html_entity_decode($maxUploadSize); ?>";
        app.locale = "<?php echo html_entity_decode($locale); ?>";

        app.options = {
            calendar_events_limit: "<?php echo get_option('calendar_events_limit'); ?>",
            calendar_first_day: "<?php echo get_option('calendar_first_day'); ?>",
            tables_pagination_limit: "<?php echo get_option('tables_pagination_limit'); ?>",
            enable_google_picker: "<?php echo get_option('enable_google_picker'); ?>",
            google_client_id: "<?php echo get_option('google_client_id'); ?>",
            google_api: "<?php echo get_option('google_api_key'); ?>",
            default_view_calendar: "<?php echo get_option('default_view_calendar'); ?>",
            timezone: "<?php echo get_option('default_timezone'); ?>",
            allowed_files: "<?php echo get_option('allowed_files'); ?>",
            date_format: "<?php echo html_entity_decode($date_format); ?>",
            time_format: "<?php echo get_option('time_format'); ?>",
        };

        app.lang = {
            file_exceeds_maxfile_size_in_form: "<?php echo _l('file_exceeds_maxfile_size_in_form'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            file_exceeds_max_filesize: "<?php echo _l('file_exceeds_max_filesize'); ?>" + ' (<?php echo bytesToSize('', $maxUploadSize); ?>)',
            validation_extension_not_allowed: "<?php echo _l('validation_extension_not_allowed'); ?>",
            sign_document_validation: "<?php echo _l('sign_document_validation'); ?>",
            dt_length_menu_all: "<?php echo _l('dt_length_menu_all'); ?>",
            drop_files_here_to_upload: "<?php echo _l('drop_files_here_to_upload'); ?>",
            browser_not_support_drag_and_drop: "<?php echo _l('browser_not_support_drag_and_drop'); ?>",
            confirm_action_prompt: "<?php echo _l('confirm_action_prompt'); ?>",
            datatables: <?php echo json_encode(get_datatables_language_array()); ?>,
            discussions_lang: <?php echo json_encode(get_project_discussions_language_array()); ?>,
        };
        window.addEventListener('load',function(){
            custom_fields_hyperlink();
        });
    </script>
    <?php

    _do_clients_area_deprecated_js_vars($date_format, $locale, $maxUploadSize, $isRTL);

    $contents = ob_get_contents();
    ob_end_clean();
    echo html_entity_decode($contents);
}

/**
 * Gets the sale agent ct full name.
 *
 * @param        $ct_id  The ct identifier
 *
 * @return     string  The sale agent ct full name.
 */
function get_sale_agent_ct_full_name($ct_id){
    $CI   = &get_instance();

    $CI->db->where('id', $ct_id);
    $contact = $CI->db->get(db_prefix().'contacts')->row();    

    if($contact){
        return $contact->firstname.' '.$contact->lastname;
    }
    return '';
}

/**
 * { check agent not in program }
 */
function check_agent_not_in_program($program_id, $agent_id = ''){
    $CI   = &get_instance();
    if($agent_id == ''){
        $agent_id = get_sale_agent_user_id();
    }
    $agent_group_ids = [];

    $CI->load->model('client_groups_model');
    $groups = $CI->client_groups_model->get_customer_groups($agent_id);

    if(count($groups) > 0){
        foreach($groups as $gr){
            $agent_group_ids[] = $gr['groupid'];
        }
    }

    $CI->db->where('id', $program_id);
    $program = $CI->db->get(db_prefix().'sa_programs')->row();

    if( ($program->agent == '' || $program->agent == null) && ($program->agent_group == '' || $program->agent_group == null)){
        return true;
    }else{
        if($program->agent != '' && $program->agent_group == ''){
            $pg_agent = explode(',', $program->agent);
            if(in_array($agent_id, $pg_agent)){
                return false;
            }
        }else if($program->agent == '' && $program->agent_group != ''){
            $pg_agent_gr = explode(',', $program->agent_group);
            foreach($pg_agent_gr as $gr_id){
                if( in_array($gr_id, $agent_group_ids)){
                    return false;
                }
            }

        }else if($program->agent != '' && $program->agent_group != ''){
            $pg_agent = explode(',', $program->agent);
            if(in_array($agent_id, $pg_agent)){
                return false;
            }

            $pg_agent_gr = explode(',', $program->agent_group);
            foreach($pg_agent_gr as $gr_id){
                if( in_array($gr_id, $agent_group_ids)){
                    return false;
                }
            }
        }
    }

    return true;
}

/**
 * Gets the program name by identifier.
 */
function get_program_name_by_id($program_id){
    $CI   = &get_instance();

    $CI->db->where('id', $program_id);
    $program = $CI->db->get(db_prefix().'sa_programs')->row();
    if($program){
        return $program->name;
    }
    return '';
}

/**
 * Get primary contact user id for specific customer
 * @param  mixed $userid
 * @return mixed
 */
function sa_get_primary_contact_user_id($userid)
{
    $CI = &get_instance();
    $CI->db->where('userid', $userid);
    $CI->db->where('is_primary', 1);
    $row = $CI->db->get(db_prefix() . 'contacts')->row();

    if ($row) {
        return $row;
    }

    return false;
}

/**
 * Gets the sa option.
 */
function get_sa_option($name, $agent_id){
    $CI = &get_instance();

    $CI->db->where('agent_id', $agent_id);
    $CI->db->where('name', $name);

    $option = $CI->db->get(db_prefix().'sa_options')->row();

    if($option){
        return $option->value;
    }

    return '';
}

/**
 * Gets the product identifiers of program by agent.
 */
function get_product_ids_of_program_by_agent($agent_id){
    $product_ids = [];

    $CI = &get_instance();

    $CI->load->model('sales_agent/sales_agent_model');

    $programs = get_programs_of_agent($agent_id);

    foreach($programs as $program){
        $program_details = $CI->sales_agent_model->get_program_detail($program['id']);
        $program_product_ids = [];

        foreach($program_details as $detail){
            if($detail['product'] != ''){
                $products = explode(',', $detail['product']);
                foreach($products as $pd_id){
                    if(!in_array($pd_id, $program_product_ids)){
                        $program_product_ids[] = $pd_id;
                    }
                }
            }
        }

        foreach($program_product_ids as $product_id){
            if(!in_array($product_id, $product_ids)){
                $product_ids[] = $product_id;
            }
        }
        
    }

    return $product_ids;
}

/**
 * Gets the product identifiers of program by agent.
 */
function get_product_group_ids_of_program_by_agent($agent_id){
    $product_group_ids = [];

    $CI = &get_instance();

    $CI->load->model('sales_agent/sales_agent_model');

    $programs = get_programs_of_agent($agent_id);

    foreach($programs as $program){
        $program_details = $CI->sales_agent_model->get_program_detail($program['id']);
        $program_product_group_ids = [];

        foreach($program_details as $detail){
            if($detail['product_group'] != ''){
                $product_groups = explode(',', $detail['product_group']);
                foreach($product_groups as $gr_id){
                    if(!in_array($gr_id, $program_product_group_ids)){
                        $program_product_group_ids[] = $gr_id;
                    }
                }
            }
        }

        foreach($program_product_group_ids as $group_id){
            if(!in_array($group_id, $product_group_ids)){
                $product_group_ids[] = $group_id;
            }
        }
        
    }

    return $product_group_ids;
}

/**
 * Gets the program of agent.
 */
function get_programs_of_agent($agent_id){
    $CI = &get_instance();
    $programs = [];

    $CI->db->where('((from_date <= "'.date('Y-m-d').'" AND to_date >= "'.date('Y-m-d').'") OR indefinite = 1)');
    $list_programs = $CI->db->get(db_prefix().'sa_programs')->result_array();
    foreach($list_programs as $program){
        if(!check_agent_not_in_program($program['id'], $agent_id)){
            $programs[] = $program;
        }
    }

    return $programs;
}

/**
 * Gets the program of product.
 */
function get_program_of_product($product_id, $group_id, $agent_id = ''){
    if($agent_id == ''){
        $agent_id = get_sale_agent_user_id();
    }



    $CI = &get_instance();
    $CI->db->where('(find_in_set('.$product_id.', product) OR (find_in_set('.$group_id.', product_group)))');
    $list_program_dt = $CI->db->get(db_prefix().'sa_program_detail')->result_array();
    $program_ids = [];

    foreach($list_program_dt as $program_dt){


        if(!check_agent_not_in_program($program_dt['program_id'], $agent_id) && !in_array($program_dt['program_id'], $program_ids)){
            $program_ids[] = $program_dt['program_id'];
        }
    }

    return $program_ids;

}

/**
 * Gets the sa client group name.
 */
function get_sa_client_group_name($group_id){
    $CI = &get_instance();

    $CI->db->where('id', $group_id);
    $group = $CI->db->get(db_prefix().'sa_client_groups')->row();

    if($group){
        return $group->name;
    }
    return '';
}

/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function sa_render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
    }
  
    $input .= '<input type="date" id="' . $name . '" name="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '" autocomplete="off">';

 
    $input .= '</div>';

    return $input;
}

/**
 * { sa_convert_item_taxes }
 *
 * @param        $tax       The tax
 * @param        $tax_rate  The tax rate
 * @param        $tax_name  The tax name
 *
 * @return     array   ( description_of_the_return_value )
 */
function sa_convert_item_taxes($tax, $tax_rate, $tax_name)
{
    /*taxrate taxname
    5.00    TAX5
    id      rate        name
    2|1 ; 6.00|10.00 ; TAX5|TAX10%*/
    $CI           = & get_instance();
    $taxes = [];
    if($tax != null && strlen($tax) > 0){
        $arr_tax_id = explode('|', $tax);
        if($tax_name != null && strlen($tax_name) > 0){
            $arr_tax_name = explode('|', $tax_name);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_name as $key => $value) {
                $taxes[]['taxname'] = $value . '|' .  $arr_tax_rate[$key];
            }
        }elseif($tax_rate != null && strlen($tax_rate) > 0){
            $CI->load->model('purchase/purchase_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->purchase_model->get_tax_name($value);
                if(isset($arr_tax_rate[$key])){
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                }else{
                    $taxes[]['taxname'] = $_tax_name . '|' .  $CI->purchase_model->tax_rate_by_id($value);

                }
            }
        }else{
            $CI->load->model('purchase/purchase_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->purchase_model->get_tax_name($value);
                $_tax_rate = $CI->purchase_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            } 
        }

    }

    return $taxes;
}

/**
 * Gets the agent group name.
 */
function get_agent_group_name($group_id){
    $CI = &get_instance();

    $CI->db->where('id', $group_id);
    $group = $CI->db->get(db_prefix().'customers_groups')->row();

    if($group){
        return $group->name;
    }

    return '';
}

/**
 * Render <select> field optimized for admin area and bootstrap-select plugin
 * @param  string  $name             select name
 * @param  array  $options          option to include
 * @param  array   $option_attrs     additional options attributes to include, attributes accepted based on the bootstrap-selectp lugin
 * @param  string  $label            select label
 * @param  string  $selected         default selected value
 * @param  array   $select_attrs     <select> additional attributes
 * @param  array   $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string  $form_group_class <div class="form-group"> additional class
 * @param  string  $select_class     additional <select> class
 * @param  boolean $include_blank    do you want to include the first <option> to be empty
 * @return string
 */
function sa_render_select($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
{
    $callback_translate = '';
    if (isset($options['callback_translate'])) {
        $callback_translate = $options['callback_translate'];
        unset($options['callback_translate']);
    }
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';
    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    if (!isset($select_attrs['data-none-selected-text'])) {
        $select_attrs['data-none-selected-text'] = _l('dropdown_non_selected_tex');
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_select_attrs = rtrim($_select_attrs);

    $form_group_attr['app-field-wrapper'] = $name;
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }
    $_form_group_attr = rtrim($_form_group_attr);
    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $select .= '<label for="' . $name . '" >' . _l($label, '', false) . '</label>';
    }
    $select .= '<select id="' . $name . '" name="' . $name . '" class="form-control selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    if ($include_blank == true) {
        $select .= '<option value=""></option>';
    }
    foreach ($options as $option) {
        $val       = '';
        $_selected = '';
        $key       = '';
        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }
        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val = trim($val);

        if ($callback_translate != '') {
            if (function_exists($callback_translate) && is_callable($callback_translate)) {
                $val = call_user_func($callback_translate, $key);
            }
        }

        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }
        if (isset($option_attrs[2])) {
            if (strpos($option_attrs[2], ',') !== false) {
                $sub_text = '';
                $_temp    = explode(',', $option_attrs[2]);
                foreach ($_temp as $t) {
                    if (isset($option[$t])) {
                        $sub_text .= $option[$t] . ' ';
                    }
                }
            } else {
                if (isset($option[$option_attrs[2]])) {
                    $sub_text = $option[$option_attrs[2]];
                } else {
                    $sub_text = $option_attrs[2];
                }
            }
            $data_sub_text = ' data-subtext=' . '"' . $sub_text . '"';
        }
        $data_content = '';
        if (isset($option['option_attributes'])) {
            foreach ($option['option_attributes'] as $_opt_attr_key => $_opt_attr_val) {
                $data_content .= $_opt_attr_key . '=' . '"' . $_opt_attr_val . '"';
            }
            if ($data_content != '') {
                $data_content = ' ' . $data_content;
            }
        }
        $select .= '<option value="' . $key . '"' . $_selected . $data_content . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}

/**
 * Gets the status approve.
 *
 * @param      integer|string  $status  The status
 *
 * @return     string          The status approve.
 */
function get_sa_status_approve($status){
    $result = '';
    if($status == 1){
        $result = '<span class="label label-primary"> '._l('purchase_draft').' </span>';
    }elseif($status == 2){
        $result = '<span class="label label-success"> '._l('purchase_approved').' </span>';
    }elseif($status == 3){
        $result = '<span class="label label-warning"> '._l('pur_rejected').' </span>';
    }elseif($status == 4){
        $result = '<span class="label label-danger"> '._l('pur_canceled').' </span>';
    }

    return $result;

}

/**
 * pur get unit name
 * @param  boolean $id 
 * @return [type]      
 */
function sa_get_unit_name($id = false)
{
    $CI           = & get_instance();
    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);

        $unit = $CI->db->get(db_prefix() . 'ware_unit_type')->row();
        if($unit){
            return $unit->unit_name;
        }
        return '';
    }
}

/**
 * wh get item variatiom
 * @param  [type] $id 
 * @return [type]     
 */
function sa_get_item_variatiom($id)
{
    $CI           = & get_instance();

    $CI->db->where('id', $id);
    $item_value = $CI->db->get(db_prefix() . 'items')->row();

    $name = '';
    if($item_value){
        $CI->load->model('sales_agent/sales_agent_model');
        $new_item_value = $CI->sales_agent_model->row_item_to_variation($item_value);

        $name .= $item_value->commodity_code.'_'.$new_item_value->new_description;
    }

    return $name;
}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function sa_handle_purchase_order_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'sa_pur_order', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * { purchase process digital signature image }
 *
 * @param      <type>   $partBase64  The part base 64
 * @param      <type>   $path        The path
 * @param      string   $image_name  The image name
 *
 * @return     boolean  
 */
function sa_process_digital_signature_image($partBase64, $path, $image_name)
{
    if (empty($partBase64)) {
        return false;
    }

    _maybe_create_upload_path($path);
    $filename = unique_filename($path, $image_name.'.png');

    $decoded_image = base64_decode($partBase64);

    $retval = false;

    $path = rtrim($path, '/') . '/' . $filename;

    $fp = fopen($path, 'w+');

    if (fwrite($fp, $decoded_image)) {
        $retval                                 = true;
        $GLOBALS['processed_digital_signature'] = $filename;
    }

    fclose($fp);

    return $retval;
}

/**
 * { pur get currency by id }
 *
 * @param        $id     The identifier
 */
function sa_get_currency_by_id($id){
    $CI   = & get_instance();

    $CI->db->where('id', $id);
    return  $CI->db->get(db_prefix().'currencies')->row();
}

/**
 * Gets the product identifiers of program.
 */
function get_product_ids_of_program($program_id){
    $CI   = & get_instance();
    $product_ids = [];

    $program_details = $CI->sales_agent_model->get_program_detail($program_id);
    $program_product_ids = [];

    foreach($program_details as $detail){
        if($detail['product'] != ''){
            $products = explode(',', $detail['product']);
            foreach($products as $pd_id){
                if(!in_array($pd_id, $program_product_ids)){
                    $program_product_ids[] = $pd_id;
                }
            }
        }
    }

    foreach($program_product_ids as $product_id){
        if(!in_array($product_id, $product_ids)){
            $product_ids[] = $product_id;
        }
    }

    return $product_ids;
}

/**
 * Gets the product identifiers of program.
 */
function get_product_group_ids_of_program($program_id){
    $CI   = & get_instance();
    $product_group_ids = [];

    $program_details = $CI->sales_agent_model->get_program_detail($program_id);
    $program_group_product_ids = [];

    foreach($program_details as $detail){
        if($detail['product_group'] != ''){
            $products = explode(',', $detail['product_group']);
            foreach($products as $pd_id){
                if(!in_array($pd_id, $program_group_product_ids)){
                    $program_group_product_ids[] = $pd_id;
                }
            }
        }
    }

    foreach($program_group_product_ids as $product_group_id){
        if(!in_array($product_group_id, $product_group_ids)){
            $product_group_ids[] = $product_group_id;
        }
    }

    return $product_group_ids;
}

/**
 * Gets the discount by item quantity.
 */
function get_discount_by_item_quantity($quantity, $program, $item_id){
    $CI   = & get_instance();
    

    $program_details = get_program_detail_of_item($program, $item_id);

    $discount_arr = [];
    foreach($program_details as $detail){
        if(($detail['from_amount'] <= $quantity && $detail['to_amount'] >= $quantity) || $detail['to_amount'] < $quantity){
            $discount_arr[] = $detail['discount'];
        }
    }

    if(count($discount_arr) > 0){
        return max($discount_arr);
    }

    return 0;
}

/**
 * Gets the program detail of item.
 */
function get_program_detail_of_item($program, $item_id){
    $CI   = & get_instance();

    $CI->db->where('id', $item_id);
    $item = $CI->db->get(db_prefix().'items')->row();

    $CI->load->model('sales_agent/sales_agent_model');

    $detail_program = [];
    $program_details = $CI->sales_agent_model->get_program_detail($program);

    foreach ($program_details as $key => $detail) {
        if($detail['product'] != ''){
            $product_ids = explode(',', $detail['product']);
            if(in_array($item_id, $product_ids)){
                $detail_program[] = $detail;
            }
        }

        if($detail['product_group'] != ''){
            $product_group_ids = explode(',', $detail['product_group']);
            if(in_array($item->group_id, $product_group_ids)){ 
                $detail_program[] = $detail;    
            }
        }
    }

    return $detail_program;
}

function no_index_sales_agent_area()
{
    hooks()->add_action('app_sale_agent_head', '_inject_no_index');
}

/**
 * get status modules wh
 * @param  string $module_name 
 * @return boolean             
 */
function sa_get_status_modules($module_name){
    $CI             = &get_instance();

    $sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
    $module = $CI->db->query($sql)->row();
    if($module){
        return true;
    }else{
        return false;
    }
}

/**
 * { function_description }
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  ( description_of_the_return_value )
 */
function sa_get_product_by_id($id){
    $CI             = &get_instance();

    $CI->db->where('id', $id);
    $item = $CI->db->get(db_prefix().'items')->row();

    if($item){
        return $item;
    }
    return '';
}

/**
 * Purchase get currency name symbol
 * @param  [type] $id     
 * @param  string $column 
 * @return [type]         
 */
function sa_get_currency_name_symbol($id, $column='')
{
    $CI   = & get_instance();
    $currency_value='';

    if($column == ''){
        $column = 'name';
    }

    $CI->db->select($column);
    $CI->db->from(db_prefix() . 'currencies');
    $CI->db->where('id', $id);
    $currency = $CI->db->get()->row();
    if($currency){
        $currency_value = $currency->$column;
    }

    return $currency_value;
}

/**
 * get currency rate
 * @param  [type] $from
 * @param  [type] $to
 * @return [type]           
 */
function sa_get_currency_rate($from, $to)
{
    $CI   = & get_instance();
    if($from == $to){
        return 1;
    }

    $amount_after_convertion = 1;

    $CI->db->where('from_currency_name', strtoupper($from));
    $CI->db->where('to_currency_name', strtoupper($to));
    $currency_rates = $CI->db->get(db_prefix().'currency_rates')->row();
    
    if($currency_rates){
        $amount_after_convertion = $currency_rates->to_currency_rate;
    }

    return $amount_after_convertion;
}

/**
 * Gets the sa customer name by identifier.
 */
function get_sa_customer_name_by_id($client_id){
    $CI   = & get_instance();

    $CI->db->where('id', $client_id);
    $customer = $CI->db->get(db_prefix().'sa_clients')->row();

    if($customer){
        return $customer->name;
    }
    return '';
}

/**
 * { saleinvoice_left_to_pay }
 *
 * @param        $invoice_id  The invoice identifier
 */
function saleinvoice_left_to_pay($id){
    $CI = & get_instance();

    
    $CI->db->select('total')
        ->where('id', $id);
        $invoice_total = $CI->db->get(db_prefix() . 'sa_sale_invoices')->row()->total;


    $CI->db->where('sale_invoice',$id);
    $payments = $CI->db->get(db_prefix().'sa_sale_invoice_payment')->result_array();

    
    $totalPayments = 0;


    foreach ($payments as $payment) {
        
        $totalPayments += $payment['amount'];
        
    }

    return ($invoice_total - $totalPayments);

}

/**
 * { handle purchase order file }
 *
 * @param      string   $id     The identifier
 *
 * @return     boolean  
 */
function sa_handle_sale_invoice_file($id)
{
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
        hooks()->do_action('before_upload_contract_attachment', $id);
        $path = SALES_AGENT_MODULE_UPLOAD_FOLDER .'/sale_invoice/'. $id . '/';
        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path($path);
            $filename    = unique_filename($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $CI           = & get_instance();
                $attachment   = [];
                $attachment[] = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['file']['type'],
                    ];
                $CI->misc_model->add_attachment_to_database($id, 'sa_sale_invoice', $attachment);

                return true;
            }
        }
    }

    return false;
}

/**
 * Gets the item hp.
 *
 * @param      string  $id     The identifier
 *
 * @return     <type>  a item or list item.
 */
function sa_get_item_hp($id = ''){
    $CI           = & get_instance();
    if($id != ''){
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix().'items')->row();
    }elseif ($id == '') {
        return $CI->db->get(db_prefix().'items')->result_array();
    }
}

/**
 * Gets the total order value by agent.
 */
function get_total_order_value_by_agent($agent_id, $currency){
    $CI           = & get_instance();

    $CI->db->where('agent_id', $agent_id);
    $CI->db->where('currency', $currency);
    $CI->db->where('approve_status', 2);
    $orders = $CI->db->get(db_prefix().'sa_pur_orders')->result_array();

    $order_value = 0;

    foreach($orders as $order){
        $order_value += $order['total'];
    }

    return $order_value;

}

/**
 * Gets the total order value by agent.
 */
function get_total_paid_amount_by_agent($agent_id, $currency){
    $CI           = & get_instance();

    $CI->db->where('agent_id', $agent_id);
    $CI->db->where('currency', $currency);
    $orders = $CI->db->get(db_prefix().'sa_pur_orders')->result_array();

    $amount_paid = 0;

    foreach($orders as $order){
        if(is_numeric($order['invoice_id']) && $order['invoice_id'] > 0){
            $CI->db->where('invoiceid', $order['invoice_id']);
            $payments = $CI->db->get(db_prefix().'invoicepaymentrecords')->result_array();
            foreach($payments as $payment){
                $amount_paid += $payment['amount'];
            }
        }
    }

    return $amount_paid;

}


/**
 * get commodity name
 * @param  integer $id
 * @return array or row
 */
function sa_get_commodity_name($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'items')->result_array();
    }

}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
function sa_get_unit_type($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('unit_type_id', $id);

        return $CI->db->get(db_prefix() . 'ware_unit_type')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'ware_unit_type')->result_array();
    }
}

/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
function sa_get_tax_rate($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'taxes')->result_array();
    }

}

/**
 * get group name
 * @param  integer $id
 * @return array or row
 */
function sa_get_group_name_item($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
    $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items_groups')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'items_groups')->result_array();
    }

}

/**
 * get puchase order aproved on module purchase
 * get purchae order
 * @param  integer $id
 * @return array or row
 */
function sa_get_pr_order($id = false)
{
    $CI           = & get_instance();
    $agent_id = get_sale_agent_user_id();
    if (is_numeric($id)) {
        $CI->db->where('id', $id);
        return $CI->db->get(db_prefix() . 'sa_pur_orders')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'sa_pur_orders where approve_status = 2 AND status_goods = 0 AND agent_id = '.$agent_id)->result_array();
    }

}

/**
 * reformat currency
 * @param  string  $value
 * @return float
 */
function sa_reformat_currency_j($value)
{

    $f_dot = str_replace(',','', $value);
    return ((float)$f_dot + 0);
}

/**
 * { sa get pur order name }
 *
 * @param        $order_id  The order identifier
 *
 * @return     string 
 */
function sa_get_pur_order_name($order_id){
    $CI           = & get_instance();

    $CI->db->where('id', $order_id);
    $order = $CI->db->get(db_prefix().'sa_pur_orders')->row();

    if($order){
        return $order->order_number;
    }

    return '';
}


/**
 * render delivery status html
 * @param  string $status 
 * @return [type]         
 */
function sa_render_delivery_status_html($id, $type, $status_value = '', $ChangeStatus = true)
{
    $status          = sa_get_delivery_status_by_id($status_value, $type);

    if($type == 'delivery'){
        $task_statuses = sa_delivery_list_status();
    }else{
        $task_statuses = sa_packing_list_status();
    }
    $outputStatus    = '';

    $outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $status_value . '">';
    $outputStatus .= $status['name'];
    $canChangeStatus = true;

    if ($canChangeStatus && $ChangeStatus) {
        $outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
        $outputStatus .= '<a href="#" class="dropdown-toggle text-dark dropdown-st" id="tableTaskStatus-' . $id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $outputStatus .= '<span data-toggle="tooltip" title="' . _l('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
        $outputStatus .= '</a>';

        $outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="tableTaskStatus-' . $id . '">';
        foreach ($task_statuses as $taskChangeStatus) {
            if ($status_value != $taskChangeStatus['id']) {
                $outputStatus .= '<li>
                <a href="#" onclick="delivery_status_mark_as(\'' . $taskChangeStatus['id'] . '\',' . $id . ',\'' . $type . '\'); return false;">
                ' . _l('task_mark_as', $taskChangeStatus['name']) . '
                </a>
                </li>';
            }
        }
        $outputStatus .= '</ul>';
        $outputStatus .= '</div>';
    }

    $outputStatus .= '</span>';

    return $outputStatus;
}

/**
 * get delivery status by id
 * @param  [type] $id 
 * @return [type]     
 */
function sa_get_delivery_status_by_id($id, $type)
{
    $CI       = &get_instance();
    $statuses = sa_delivery_list_status();

    if($type == 'delivery'){
        $status = [
            'id'         => 0,
            'color'   => '#989898',
            'color' => '#989898',
            'name'       => _l('wh_ready_for_packing'),
            'order'      => 1,
        ];
    }else{
        $status = [
            'id'         => 0,
            'color'   => '#989898',
            'color' => '#989898',
            'name'       => _l('wh_ready_to_deliver'),
            'order'      => 1,
        ];
    }

    foreach ($statuses as $s) {
        if ($s['id'] == $id) {
            $status = $s;

            break;
        }
    }

    return $status;
}

/**
 * packing list status
 * @param  string $status 
 * @return [type]         
 */
function sa_delivery_list_status($status='')
{

    $statuses = [
        [
            'id'             => 'ready_for_packing',
            'color'          => '#28b8daed',
            'name'           => _l('wh_ready_for_packing'),
            'order'          => 1,
            'filter_default' => true,
        ],
        [
            'id'             => 'ready_to_deliver',
            'color'          => '#03A9F4',
            'name'           => _l('wh_ready_to_deliver'),
            'order'          => 2,
            'filter_default' => true,
        ],
        [
            'id'             => 'delivery_in_progress',
            'color'          => '#2196f3',
            'name'           => _l('wh_delivery_in_progress'),
            'order'          => 3,
            'filter_default' => true,
        ],
        [
            'id'             => 'delivered',
            'color'          => '#3db8da',
            'name'           => _l('wh_delivered'),
            'order'          => 4,
            'filter_default' => true,
        ],
        [
            'id'             => 'received',
            'color'          => '#84c529',
            'name'           => _l('wh_received'),
            'order'          => 5,
            'filter_default' => false,
        ],
        [
            'id'             => 'returned',
            'color'          => '#d71a1a',
            'name'           => _l('wh_returned'),
            'order'          => 6,
            'filter_default' => false,
        ],
        [
            'id'             => 'not_delivered',
            'color'          => '#ffa500',
            'name'           => _l('wh_not_delivered'),
            'order'          => 7,
            'filter_default' => false,
        ],
    ];

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}

/**
 * packing list status
 * @param  string $status 
 * @return [type]         
 */
function sa_packing_list_status($status='')
{

    $statuses = [

        [
            'id'             => 'ready_to_deliver',
            'color'          => '#03A9F4',
            'name'           => _l('wh_ready_to_deliver'),
            'order'          => 2,
            'filter_default' => true,
        ],
        [
            'id'             => 'delivery_in_progress',
            'color'          => '#2196f3',
            'name'           => _l('wh_delivery_in_progress'),
            'order'          => 3,
            'filter_default' => true,
        ],
        [
            'id'             => 'delivered',
            'color'          => '#3db8da',
            'name'           => _l('wh_delivered'),
            'order'          => 4,
            'filter_default' => true,
        ],
        [
            'id'             => 'received',
            'color'          => '#84c529',
            'name'           => _l('wh_received'),
            'order'          => 5,
            'filter_default' => false,
        ],
        [
            'id'             => 'returned',
            'color'          => '#d71a1a',
            'name'           => _l('wh_returned'),
            'order'          => 6,
            'filter_default' => false,
        ],
        [
            'id'             => 'not_delivered',
            'color'          => '#ffa500',
            'name'           => _l('wh_not_delivered'),
            'order'          => 7,
            'filter_default' => false,
        ],
    ];

    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;

}

/**
 * Gets the sa invoice number.
 *
 * @param        $invoice_id  The invoice identifier
 */
function get_sa_invoice_number($invoice_id){
    $CI       = &get_instance();

    $CI->db->where('id', $invoice_id);
    $invoice = $CI->db->get(db_prefix().'sa_sale_invoices')->row();
    if($invoice){
        return $invoice->inv_number;
    }

    return '';
}

/**
 * { sa get invoice company }
 *
 * @param        $invoice_id  The invoice identifier
 *
 * @return     string  
 */
function sa_get_invoice_company($invoice_id){
    $CI       = &get_instance();

    $CI->db->where('id', $invoice_id);
    $invoice = $CI->db->get(db_prefix().'sa_sale_invoices')->row();
    if($invoice){
        return get_sa_client_name($invoice->clientid);
    }

    return '';
}

/**
 * Gets the sa client name.
 */
function get_sa_client_name($client_id){
    $CI       = &get_instance();

    $CI->db->where('id', $client_id);
    $client = $CI->db->get(db_prefix().'sa_clients')->row();

    if($client){
        return $client->name;
    }

    return '';
}

/**
 * Gets the payment mode by identifier.
 *
 * @param      <type>  $id     The identifier
 *
 * @return     string  The payment mode by identifier.
 */
function sa_get_payment_mode_by_id($id){
    $CI = & get_instance();
    $CI->db->where('id',$id);
    $mode = $CI->db->get(db_prefix().'payment_modes')->row();
    if($mode){
        return $mode->name;
    }else{
        return '';
    }
}

/**
 * get warehouse name
 * @param  integer $id
 * @return array or row
 */
function sa_get_warehouse_name($id = false)
{
    $CI           = & get_instance();

    if ($id != false) {
        $CI->db->where('warehouse_id', $id);

        return $CI->db->get(db_prefix() . 'sa_warehouse')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'sa_warehouse')->result_array();
    }

}

/**
 * get goods receipt code
 * @param  integer $id
 * @return array or row
 */
function sa_get_goods_receipt_code($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'sa_goods_receipt')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'sa_goods_receipt')->result_array();
    }

}

/**
 * get goods delivery code
 * @param  integer $id
 * @return array or row
 */
function sa_get_goods_delivery_code($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'sa_goods_delivery')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from '.db_prefix().'sa_goods_delivery')->result_array();
    }

}


/**
 * Get clients area ticket summary statuses data
 * @since  2.3.2
 * @param  array $statuses  current statuses
 * @return array
 */
function sa_get_agent_area_tickets_summary($statuses)
{
    foreach ($statuses as $key => $status) {
        $where = ['userid' => get_sale_agent_user_id(), 'status' => $status['ticketstatusid']];
        if (!can_logged_in_contact_view_all_tickets()) {
            $where[db_prefix() . 'tickets.contactid'] = get_sa_contact_user_id();
        }
        $statuses[$key]['total_tickets']   = total_rows(db_prefix() . 'tickets', $where);
        $statuses[$key]['translated_name'] = ticket_status_translate($status['ticketstatusid']);
        $statuses[$key]['url']             = site_url('sales_agent/portal/tickets/' . $status['ticketstatusid']);
    }

    return hooks()->apply_filters('clients_area_tickets_summary', $statuses);
}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
 function sa_get_unit_type_item($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
    $CI->db->where('unit_type_id', $id);
        return $CI->db->get(db_prefix() . 'ware_unit_type')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblware_unit_type')->result_array();
    }

}

/**
 * get tax rate
 * @param  integer $id
 * @return array or row
 */
 function sa_get_tax_rate_item($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
    $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'taxes')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tbltaxes')->result_array();
    }

}