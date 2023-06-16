<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Omni Sales
Description: Omni-Channel selling is the process of selling your products on more than one sales channel. Multichannel management includes a mix of your own website and shopping cart, online marketplaces, mobile marketplaces and/or bricks and mortar store
Version: 1.1.5
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('OMNI_SALES_MODULE_NAME', 'omni_sales');
define('OMNI_SALES_MODULE_UPLOAD_FOLDER', module_dir_path(OMNI_SALES_MODULE_NAME, 'uploads'));
hooks()->add_action('app_admin_head', 'omni_sales_add_head_component');
hooks()->add_action('app_admin_footer', 'omni_sales_load_js');
hooks()->add_action('admin_init', 'omni_sales_permissions');
define('OMNI_SALES_PATH', 'modules/omni_sales/uploads/');
define('OMNI_SALES_APP_PATH', 'modules/omni_sales/');
hooks()->add_action('admin_init', 'omni_sales_module_init_menu_items');
hooks()->add_action('customers_navigation_end', 'omni_sales_module_init_client_menu_items');
hooks()->add_action('client_pt_footer_js','client_portal_foot_js');
hooks()->add_action('after_contact_login','redirect_to_pages');
hooks()->add_action('head_element_client','head_element');
hooks()->add_action('before_cron_run', 'scan_server_woo');
define('WAREHOUSE_CUS_IMG', module_dir_path('warehouse', 'uploads/item_img/'));

register_merge_fields('omni_sales/merge_fields/purchase_receipt_merge_fields');
register_merge_fields('omni_sales/merge_fields/pre_orders_handover_merge_fields');
register_merge_fields('omni_sales/merge_fields/pre_orders_notify_merge_fields');

hooks()->add_filter('other_merge_fields_available_for', 'purchase_receipt_register_other_merge_fields');
hooks()->add_filter('admin_body_class', 'admin_body_class_return_order');
hooks()->add_action('delete_item_on_woocommerce', 'delete_item_all_store');

// change order status after invoice statsus changed
hooks()->add_action('invoice_status_changed', 'after_invoice_paid');

// log to shipment when payment added.
hooks()->add_action('after_payment_added', 'payment_added_action');

// change status after delivered
hooks()->add_action('affter_wh_logged', 'after_product_delivered_action');

hooks()->add_action('omni_sales_init',OMNI_SALES_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', OMNI_SALES_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', OMNI_SALES_MODULE_NAME.'_predeactivate');
hooks()->add_action('pre_uninstall_module', OMNI_SALES_MODULE_NAME.'_uninstall');

define('VERSION_POS', 1158);

/*Flag active menu customer report*/
define('CUSTOMER_REPORT', false);

/**
* Register activation module hook
*/

register_activation_hook(OMNI_SALES_MODULE_NAME, 'omni_sales_module_activation_hook');
$CI = & get_instance();

$CI->load->helper(OMNI_SALES_MODULE_NAME . '/Omni_sales');
/**
 * team password module activation hook
 */
function omni_sales_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(OMNI_SALES_MODULE_NAME, [OMNI_SALES_MODULE_NAME]);

/**
* init add head component
*/
function omni_sales_add_head_component(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/sales') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/omni_sales.css') . '?v='.VERSION_POS.'" rel="stylesheet" type="text/css" />';
    } 
    
    if(!(strpos($viewuri,'/admin/omni_sales/detail_channel_wcm') === false) || !(strpos($viewuri,'/admin/omni_sales/add_woocommerce_store') === false) || !(strpos($viewuri,'/admin/omni_sales/setting') === false)){
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/woocommerce.css') . '?v='.VERSION_POS.'" rel="stylesheet" type="text/css" />';
    }
    
    if (!(strpos($viewuri,'/admin/omni_sales/view_order_detailt') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/invoice.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri,'/admin/omni_sales/omni_sales_channel') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/omni_sales.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri,'/admin/omni_sales/view_customer_report_detail') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/customer_report/customer_report.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri,'/omni_sales/view_pre_order_detailt/') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/invoice.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri,'/omni_sales/add_product_channel') === false)) {
        echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/omni_sales.css') . '?v='.VERSION_POS.'" rel="stylesheet" type="text/css" />';
    } 
}

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function omni_sales_module_init_menu_items()
{
    $CI = &get_instance();    
    if(is_admin()|| has_permission('omni_sales','','view')|| has_permission('omni_order_list', '', 'view') || has_permission('omni_order_list', '', 'view_own') || has_permission('omni_sales','','view_sales_channel')|| has_permission('omni_sales','','view_trade_discount')|| has_permission('omni_sales','','view_diary_sync')|| has_permission('omni_sales','','view_report')|| has_permission('omni_sales','','view_pos')){
        $CI->app_menu->add_sidebar_menu_item('omni_sales', [
            'name'     => _l('omni_sales'),
            'icon'     => 'fa fa-shopping-basket',
            'href'     => admin_url('#'),
        ]);  

        if(is_admin()|| has_permission('omni_order_list', '', 'view') || has_permission('omni_order_list', '', 'view_own')){
         $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_order_list',
            'name'     => _l('order_list'),
            'icon'     => 'fa fa-sticky-note',                
            'href'     => admin_url('omni_sales/order_list'),
        ]);  

     }
     if(is_admin()|| has_permission('omni_sales','','view_sales_channel')){

        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_channel',
            'name'     => _l('omni_sales_channel'),
            'icon'     => 'fa fa-tasks',                
            'href'     => admin_url('omni_sales/omni_sales_channel'),
        ]);
    }  
    if(is_admin()|| has_permission('omni_sales','','view_trade_discount')){

        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'trade_discount',
            'name'     => _l('trade_discount'),
            'icon'     => 'fa fa-percent',                
            'href'     => admin_url('omni_sales/trade_discount'),
        ]);
    } 
    if(is_admin()|| has_permission('omni_sales','','view_diary_sync')){

        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_diary_sync',
            'name'     => _l('diary_sync'),
            'icon'     => 'fa fa-spinner',                
            'href'     => admin_url('omni_sales/diary_sync'),
        ]);
    }
    if(is_admin()|| has_permission('omni_sales','','view_report')){

        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_report',
            'name'     => _l('report'),
            'icon'     => 'fa fa-signal',                
            'href'     => admin_url('omni_sales/report'),
        ]);
    }  
    if(is_admin()|| has_permission('omni_sales','','view_pos')){            
        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_pos',
            'name'     => 'POS',
            'icon'     => 'fa fa-list-alt',                
            'href'     => admin_url('omni_sales/shift'),
        ]);
    }

    $CI->app_menu->add_sidebar_children_item('omni_sales', [
        'slug'     => 'omni_sales_portal',
        'name'     => _l('portal'),
        'icon'     => 'fa fa-bookmark',                
        'href'     => site_url('omni_sales/omni_sales_client/index/1/0/0'),
    ]);

    if(CUSTOMER_REPORT == true){
        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_customer_reports',
            'name'     => _l('customer_report'),
            'icon'     => 'fa fa-th-list menu-icon',                
            'href'     => site_url('omni_sales/manage_customer_report'),
        ]);
    }

    if(CUSTOMER_REPORT == true){
        $CI->app_menu->add_sidebar_children_item('omni_sales', [
            'slug'     => 'omni_sales_manage_importing_transaction',
            'name'     => _l('Mpesatrans'),
            'icon'     => 'fa fa-list-alt menu-icon menu-icon',                
            'href'     => site_url('omni_sales/manage_importing_transaction'),
        ]);
    }
    
    hooks()->do_action('omni_sale_add_menu');
    if(is_admin()){             
     $CI->app_menu->add_sidebar_children_item('omni_sales', [
        'slug'     => 'omni_setting',
        'name'     => 'setting',
        'icon'     => 'fa fa-cog',                
        'href'     => admin_url('omni_sales/setting'),
    ]);
 }         
}

}


/**
 * team password permissions
 */
function omni_sales_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),           
        // 'view_order_list'   => _l('permission_view') . '(' . _l('order_list') . ')',           
        'view_sales_channel'   => _l('permission_view') . '(' . _l('omni_sales_channel') . ')',           
        'view_trade_discount'   => _l('permission_view') . '(' . _l('trade_discount') . ')',           
        'view_diary_sync'   => _l('permission_view') . '(' . _l('diary_sync') . ')',           
        'view_report'   => _l('permission_view') . '(' . _l('report') . ')',           
        'view_pos'   => _l('permission_view') . '(POS)',          
    ];

    $capabilities_basic['capabilities'] = [
        'view_own' => _l('permission_view_own'),
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('omni_sales', $capabilities, _l('omni_sales'));
    register_staff_capabilities('omni_order_list', $capabilities_basic, _l('order_list'));    

}

/**
 * init add footer component
 */
function omni_sales_load_js(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri,'/admin/omni_sales/add_product_channel') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_channel/sales_channel.js').'?v='.VERSION_POS.'"></script>';
    }elseif(!(strpos($viewuri,'/admin/omni_sales/add_woocommerce_store') === false) || !(strpos($viewuri,'/admin/omni_sales/detail_channel_wcm') === false) || !(strpos($viewuri,'/admin/omni_sales/setting') === false)){
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_channel/manage_channel_woocommerce.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/admin/omni_sales/order_list') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/order_list/order_list.js').'?v='.VERSION_POS.'"></script>';
    }

    if (!(strpos($viewuri,'/admin/omni_sales/omni_sales_channel') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_channel/sales_channel.js').'?v='.VERSION_POS.'"></script>';       
    } 

    if (!(strpos($viewuri,'/admin/omni_sales/trade_discount') === false) || !(strpos($viewuri,'/admin/omni_sales/new_trade_discount') === false) || !(strpos($viewuri,'/admin/omni_sales/new_voucher') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/trade_discount/trade_discount.js').'?v='.VERSION_POS.'"></script>';       
    } 
    if (!(strpos($viewuri,'/admin/omni_sales/view_order_detail') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/order/view_order.js').'?v='.VERSION_POS.'"></script>';
    }

    if (!(strpos($viewuri,'/admin/omni_sales/report') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/trade_discount/result_apply.js').'?v='.VERSION_POS.'"></script>';       
    } 

    if (!(strpos($viewuri,'/admin/omni_sales/diary_sync') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/diary_sync/diary_sync.js').'?v='.VERSION_POS.'"></script>';       
    }

    if(!(strpos($viewuri,'/admin/omni_sales/report') === false)){
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    } 
    if (!(strpos($viewuri,'/admin/omni_sales/shift') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/shift/shift_management.js').'?v='.VERSION_POS.'"></script>';       
    }
    if (!(strpos($viewuri,'/admin/omni_sales/order_manual') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/order_list/manual_order.js').'?v='.VERSION_POS.'"></script>';       
    }
    if (!(strpos($viewuri,'/admin/omni_sales/pre_order_list') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/pre_order/pre_order_list.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'admin/omni_sales/pre_order') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_channel/pre_order_setting.js').'?v='.VERSION_POS.'"></script>';
    }
}
/**
 *  add menu item and js file to client
*/
function omni_sales_module_init_client_menu_items()
{
    $CI = &get_instance();
    $status_channel = '';
    $viewuri = $_SERVER['REQUEST_URI'];
    $data_channel = $CI->db->query('select status from '.db_prefix().'sales_channel where channel="portal"')->row();
    if($data_channel){
        $status_channel = $data_channel->status;
    }
    if($status_channel == 'active' && get_option('omni_display_shopping_cart') == 1){
        if (!(strpos($viewuri,'/omni_sales/omni_sales_client') === false)) {
            echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/omni_sales.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
        }

        echo '<li class="customers-nav-item-Insurances-plan">
        <a href="'.site_url('omni_sales/omni_sales_client/view_cart').'">
        <i class="fa fa-shopping-cart"></i>
        <span class="text-white qty_total"></span>
        </a>
        </li>';

        echo '<li class="customers-nav-item-Insurances-plan">
        <a href="'.site_url('omni_sales/omni_sales_client/index/1/0/0').'">
        <i class="fa fa-tags"></i>
        </a>
        </li>'; 

        if(is_client_logged_in()){
            echo '<li class="customers-nav-item-Insurances-plan">
            <a href="'.site_url('omni_sales/omni_sales_client/order_list').'">'._l('order_list').'
            </a>
            </li>';
        } 
    }
}
/**
 * add element for footer portal 
 */
function client_portal_foot_js(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/index') === false) || !(strpos($viewuri,'/omni_sales/omni_sales_client') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_client/sales_client.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/view_cart') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/sales_client/cart/invoice.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/view_order_detail') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/order_list/order_detailt_client.js').'?v='.VERSION_POS.'"></script>';
    }

    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/detailt') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/detailt_product/detailt_product.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/admin/omni_sales/pos') === false)) {
        echo '<script src="'.site_url().'assets/plugins/jquery/jquery.min.js"></script>';

        echo '<script type="text/javascript" src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/pos/pos.js').'?v='.VERSION_POS.'"></script>';
        echo '<script type="text/javascript" src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/zbar_processor/zbar-processor.js').'"></script>';
        echo '<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>';
        echo '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>';
        echo '<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>';
        echo '<script type="text/javascript" src="'.site_url().'assets/builds/vendor-admin.js?v=2.4.0"></script>';
        echo '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>';
        echo '<script type="text/javascript" src="'.site_url().'assets/plugins/jquery/jquery-migrate.js?v=2.4.0"></script>';
        echo '<script type="text/javascript" src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/tool/calculator.js').'?v='.VERSION_POS.'"></script>';
        echo '<script type="text/javascript" src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/tool/webcame.js').'?v='.VERSION_POS.'"></script>';
        echo '<script type="text/javascript" src="'.site_url().'assets/plugins/jquery/jquery-migrate.js?v='.VERSION_POS.'"></script>';
        echo '<script type="text/javascript" src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/confirm_dialog/js/jQueryDialog.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/view_overview') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/trade_discount/view_overview.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/client') === false)) {
        echo '<script src="'.site_url().'/assets/plugins/jquery/jquery.min.js"></script>';
    }

    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/view_cart') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/trade_discount/view_overview.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/create_pre_order') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/pre_order/pre_order.js').'?v='.VERSION_POS.'"></script>';
    }
    if (!(strpos($viewuri,'/omni_sales/omni_sales_client/order_list') === false)) {
        echo '<script src="'.module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/js/order_list/client/order_list.js').'?v='.VERSION_POS.'"></script>';
    }
}
/**
 * add head_element
 */
function head_element(){
 $CI = &get_instance();
 $viewuri = $_SERVER['REQUEST_URI'];
 if (!(strpos($viewuri,'/omni_sales/omni_sales_client/index') === false) ){
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/omni_sales.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';

}
if (!(strpos($viewuri,'/admin/omni_sales/view_order_detailt') === false) || !(strpos($viewuri,'/omni_sales/omni_sales_client/view_cart') === false) || !(strpos($viewuri,'/omni_sales/view_pre_order_detailt/') === false) || !(strpos($viewuri,'/omni_sales/omni_sales_client/view_overview') === false) || !(strpos($viewuri,'/omni_sales/omni_sales_client/view_order_detail') === false) || !(strpos($viewuri,'/omni_sales/omni_sales_client/view_detail') === false)) {
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/invoice.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/order_status.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
} 
if (!(strpos($viewuri,'/omni_sales/omni_sales_client/order_list') === false)) {
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/order_list.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
} 
if (!(strpos($viewuri,'/omni_sales/omni_sales_client/detailt') === false)) {
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/detailt_product/detailt_product.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
} 
if (!(strpos($viewuri,'/admin/omni_sales/pos') === false)) {
    echo '<link rel="stylesheet" type="text/css" id="fontawesome-css" href="'.site_url().'/assets/plugins/font-awesome/css/font-awesome.min.css?v=2.4.0">';
    echo '<link rel="stylesheet" type="text/css" id="datetimepicker-css" href="'.site_url().'/assets/plugins/datetimepicker/jquery.datetimepicker.min.css?v=2.4.0">';
    echo '<link rel="stylesheet" type="text/css" id="bootstrap-select-css" href="'.site_url().'/assets/plugins/bootstrap-select/css/bootstrap-select.min.css?v=2.4.0">';
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/pos/pos.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
    echo '<link rel="stylesheet" type="text/css" id="bootstrap-select-css" href="' . site_url().'assets/css/reset.min.css?v=2.4.0">';
    echo '<link rel="stylesheet" type="text/css" id="bootstrap-select-css" href="' . site_url().'assets/plugins/roboto/roboto.css?v=2.4.0">';
    echo '<link rel="stylesheet" type="text/css" id="bootstrap-select-css" href="' . site_url().'assets/css/style.min.css?v=1595491188">';
    echo '<link rel="stylesheet" type="text/css" id="bootstrap-css" href="'.site_url().'/assets/plugins/bootstrap/css/bootstrap.min.css?v=2.4.0">';
    echo '<link media="screen and (max-width: 1300px)" href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/pos/responsive_1300.css') . '?v='.VERSION_POS.'"  rel="stylesheet" />';
    echo '<link rel="stylesheet" type="text/css" href="'. module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/plugins/confirm_dialog/css/jQueryDialog.css') . '?v='.VERSION_POS.'">';
}     
if (!(strpos($viewuri,'/omni_sales/omni_sales_client/return') === false)) {
    echo '<link href="' . module_dir_url(OMNI_SALES_MODULE_NAME, 'assets/css/cart/invoice.css') . '?v='.VERSION_POS.'"  rel="stylesheet" type="text/css" />';
} 
}
/**
 * redirect to pages 
 */
function redirect_to_pages(){
    if(get_option('omni_display_shopping_cart') == 1){
        maybe_redirect_to_previous_url();
        redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
    }else{
        redirect(site_url('clients/invoices'));
    }
}

/**
 * cron job sync data
 * @return 
 */

function scan_server_woo(){  

    $CI = &get_instance();
    $stores = get_all_store();
    $records_time1 = get_option('records_time1');
    $hour = date("H:i:s", time());
    foreach ($stores as $key => $store) {
        $config_store = $CI->omni_sales_model->get_setting_auto_sync_store($store['id']);
        if(isset($config_store[0])){
            $sync_omni_sales_products = $config_store[0]['sync_omni_sales_products'];
            $minute_sync_product_info_time1 = $config_store[0]['time1'];
            
            if($sync_omni_sales_products == "1"){
                if($hour >= strtotime($records_time1)){
                    $CI->omni_sales_model->sync_from_the_system_to_the_store_single($store['id']);
                    update_option('records_time1', date("H:i:s"));
                }
            }
            cron_job_sync_woo($store['id']);
        }
    }
    
    $CI->omni_sales_model->cron_clear_diary_sync();

    return true;
    
}



/**
 * Register other merge fields for inventory warning
 *
 * @param [array] $for
 * @return void
 */
function purchase_receipt_register_other_merge_fields($for) {
    $for[] = 'omni_sales';

    return $for;
}

/**
 * delete item all store
 * @return [type] [description]
 */
function delete_item_all_store($item) {
    $CI = &get_instance();
    $CI->load->model('omni_sales/omni_sales_model');
    $CI->omni_sales_model->proccess_sku_item_delete($item);
}

function admin_body_class_return_order($class){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri,'/omni_sales/order_return') === false))
    {
        $class[] = 'no-calculate-total';
    }
    return $class;
}

/**
 * { after invoice paid }
 *
 * @param        $data   The data
 */
function after_invoice_paid($data){
    $CI = &get_instance();
    $CI->load->model('invoices_model');

    $invoice = $CI->invoices_model->get($data['invoice_id']);
    if($invoice && !is_array($invoice)){
        if($data['status'] == Invoices_model::STATUS_PAID ){
            $CI->db->where('number_invoice', $invoice->number);
            $CI->db->update(db_prefix().'cart', ['status' => 14]);
        }else if($data['status'] == Invoices_model::STATUS_PARTIALLY || $data['status'] == Invoices_model::STATUS_UNPAID || $data['status'] == Invoices_model::STATUS_OVERDUE){
            $CI->db->where('number_invoice', $invoice->number);
            $CI->db->update(db_prefix().'cart', ['status' => 2]);
        }
    }

}

/**
 * { payment added action }
 */
function payment_added_action($payment_id){
    $CI = &get_instance();

    if(omni_get_status_modules('warehouse')){
        $CI->load->model('omni_sales/omni_sales_model');
        $CI->load->omni_sales_model->add_shipment_log_after_payment($payment_id);
    }
}

/**
 * { after product delivered action }
 *
 * @param        $log_id  The log identifier
 */
function after_product_delivered_action($log_id){
    $CI = &get_instance();

    if(omni_get_status_modules('warehouse')){
        $CI->load->model('omni_sales/omni_sales_model');
        $CI->load->omni_sales_model->change_order_status_when_product_delivered($log_id);
    }
}

function omni_sales_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $omni_sales_api = new OmniSalesLic();
    $omni_sales_gtssres = $omni_sales_api->verify_license(true);    
    if(!$omni_sales_gtssres || ($omni_sales_gtssres && isset($omni_sales_gtssres['status']) && !$omni_sales_gtssres['status'])){
         $CI->app_modules->deactivate(OMNI_SALES_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}
function omni_sales_preactivate($module_name){
    if ($module_name['system_name'] == OMNI_SALES_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $omni_sales_api = new OmniSalesLic();
        $omni_sales_gtssres = $omni_sales_api->verify_license();          
        if(!$omni_sales_gtssres || ($omni_sales_gtssres && isset($omni_sales_gtssres['status']) && !$omni_sales_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.OMNI_SALES_MODULE_NAME); 
            $data['module_name'] = OMNI_SALES_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}
function omni_sales_predeactivate($module_name){
    if ($module_name['system_name'] == OMNI_SALES_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $omni_sales_api = new OmniSalesLic();
        $omni_sales_api->deactivate_license();
    }
}
function omni_sales_uninstall($module_name){
    if ($module_name['system_name'] == OMNI_SALES_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $omni_sales_api = new OmniSalesLic();
        $omni_sales_api->deactivate_license();
    }
}