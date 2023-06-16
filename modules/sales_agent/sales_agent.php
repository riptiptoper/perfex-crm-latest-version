<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Sales Agent
Description: Sales Agent Management
Version: 1.0.0
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/
define('SALES_AGENT_MODULE_NAME', 'sales_agent');
define('SALES_AGENT_MODULE_UPLOAD_FOLDER', module_dir_path(SALES_AGENT_MODULE_NAME, 'uploads'));
define('SALES_AGENT_REVISION', 100);

hooks()->add_action('admin_init', 'sales_agent_module_init_menu_items');
hooks()->add_action('app_admin_head', 'sale_agent_add_head_components');
hooks()->add_action('app_sale_agent_head', 'sale_agent_portal_add_head_components');
hooks()->add_action('app_sale_agent_footer', 'sale_agent_portal_add_footer_components');
hooks()->add_action('app_admin_footer', 'sale_agent_add_footer_components');
hooks()->add_action('after_add_goods_delivery_from_invoice', 'update_export_stock_id_to_order');
hooks()->add_action('after_create_shipment_from_delivery_note', 'update_order_id_to_shipment');
hooks()->add_action('affter_wh_logged', 'sa_after_product_delivered_action');
hooks()->add_action('sa_contract_head_element', 'init_head_contract_element');
hooks()->add_action('sales_agent_init',SALES_AGENT_MODULE_NAME.'_appint');
hooks()->add_action('pre_activate_module', SALES_AGENT_MODULE_NAME.'_preactivate');
hooks()->add_action('pre_deactivate_module', SALES_AGENT_MODULE_NAME.'_predeactivate');
define('SA_PATH', 'modules/sales_agent/uploads/');
/**
* Register activation module hook
*/
register_activation_hook(SALES_AGENT_MODULE_NAME, 'sales_agent_module_activation_hook');
/**
* Load the module helper
*/
$CI = & get_instance();
$CI->load->helper(SALES_AGENT_MODULE_NAME . '/sales_agent');


/**
 * { sales agent module activation hook }
 */
function sales_agent_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(SALES_AGENT_MODULE_NAME, [SALES_AGENT_MODULE_NAME]);

/**
 * { sales agent module init menu items }
 */
function sales_agent_module_init_menu_items(){
	$CI = &get_instance();

	$CI->app_menu->add_sidebar_menu_item('sales_agent', [
        'name' => _l('sales_agent'),
        'icon' => 'fa fa-user-circle',
        'position' => 10,
    ]);

	$CI->app_menu->add_sidebar_children_item('sales_agent', [
        'slug' => 'sales_agent-dashboard',
        'name' => _l('sa_dashboard'),
        'icon' => 'fa fa-tachometer menu-icon',
        'href' => admin_url('sales_agent/dashboard'),
        'position' => 1,
    ]);


    $CI->app_menu->add_sidebar_children_item('sales_agent', [
        'slug' => 'sales_agent-manage',
        'name' => _l('sa_management'),
        'icon' => 'fa fa-list menu-icon',
        'href' => admin_url('sales_agent/management'),
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('sales_agent', [
        'slug' => 'sales_agent-programs',
        'name' => _l('sa_programs'),
        'icon' => 'fa fa-product-hunt menu-icon',
        'href' => admin_url('sales_agent/programs'),
        'position' => 3,
    ]);


    $CI->app_menu->add_sidebar_children_item('sales_agent', [
        'slug' => 'sales_agent-order',
        'name' => _l('sa_orders'),
        'icon' => 'fa fa-balance-scale menu-icon',
        'href' => admin_url('sales_agent/orders'),
        'position' => 4,
    ]);


}

/**
 * { sale agent add head components }
 */
function sale_agent_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if(!(strpos($viewuri, '/admin/sales_agent/') === false) ){
        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/css/style.css') .'?v=' . SALES_AGENT_REVISION.'"  rel="stylesheet" type="text/css" />';
    }

    if(!(strpos($viewuri, '/admin/sales_agent/item_detail') === false)){

        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }

}

/**
 * { sale agent portal add head components }
 */
function sale_agent_portal_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/sales_agent/portal') === false)) {
        echo '<script type="text/javascript" src="' . site_url('assets/plugins/accounting.js/accounting.js') . '?v=' . SALES_AGENT_REVISION . '"></script>';
        echo '<script src="' . site_url( 'assets/plugins/datatables/datatables.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/js/portal/main.js') . '"></script>';
        echo '<link rel="stylesheet" href="'. site_url('assets/css/style.css'). '?v='.SALES_AGENT_REVISION.'">';
        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/css/portal_style.css') .'?v=' . SALES_AGENT_REVISION.'"  rel="stylesheet" type="text/css" />';

        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/sales_agent/portal/item_detail') === false)) {
        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';

        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
    }
}

/**
 * { sale agent portal add footer components }
 */
function sale_agent_portal_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/sales_agent/portal') === false)) {
        echo '<script src="' . site_url( 'assets/plugins/metisMenu/metisMenu.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/js/portal/menu.js') . '"></script>';
    }

}

/**
 * { sale agent add footer components }
 */
function sale_agent_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    if(!(strpos($viewuri, '/admin/sales_agent/item_detail') === false)){ 
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
    }
}

/**
 * { update export stock id to order }
 *
 * @param        $stock_export_id  The stock export identifier
 */
function update_export_stock_id_to_order($stock_export_id){
    $CI = &get_instance();

    if(sa_get_status_modules('warehouse')){
        $CI->db->where('id', $stock_export_id);
        $stock_export = $CI->db->get(db_prefix().'goods_delivery')->row();

        if($stock_export){
            if(is_numeric($stock_export->invoice_id) && $stock_export->invoice_id > 0){
                if(total_rows(db_prefix().'goods_delivery_detail', ['goods_delivery_id' => $stock_export_id] ) > 0){

                    $CI->db->where('invoice_id', $stock_export->invoice_id);
                    $CI->db->update(db_prefix().'sa_pur_orders', [
                        'stock_export_id' => $stock_export_id
                    ]);
                }
            }
        }
    }
}

/**
 * { update_order_id_to_shipment }
 */
function update_order_id_to_shipment($shipment_id){
    $CI = &get_instance();
    if(sa_get_status_modules('warehouse')){
        $CI->db->where('id', $shipment_id);
        $shipment = $CI->db->get(db_prefix().'wh_omni_shipments')->row();

        if($shipment){
            $CI->db->where('id', $shipment->goods_delivery_id);
            $goods_delivery = $CI->db->get(db_prefix().'goods_delivery')->row();

            if($goods_delivery){
                $CI->db->where('invoice_id', $goods_delivery->invoice_id);
                $order = $CI->db->get(db_prefix().'sa_pur_orders')->row();

                if($order){
                    $CI->db->where('id', $shipment_id);
                    $CI->db->update(db_prefix().'wh_omni_shipments', [
                        'order_id' => $order->id,
                    ]);
                }
            }
        }
    }
}

/**
 * { after product delivered action }
 *
 * @param        $log_id  The log identifier
 */
function sa_after_product_delivered_action($log_id){
    $CI = &get_instance();
    if(sa_get_status_modules('warehouse')){
        $CI->load->model('sales_agent/sales_agent_model');
        $CI->load->sales_agent_model->change_order_status_when_product_delivered($log_id);
    }
}

/**
 * Initializes the head contract element.
 */
function init_head_contract_element(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if(!(strpos($viewuri, '/sales_agent/purchase_contract/index') === false)){ 
         echo '<link href="' . module_dir_url(SALES_AGENT_MODULE_NAME, 'assets/css/style.css') .'?v=' . SALES_AGENT_REVISION.'"  rel="stylesheet" type="text/css" />';
    }
}

function sales_agent_appint(){
    $CI = & get_instance();    
    require_once 'libraries/gtsslib.php';
    $sales_agent_api = new SalesAgentLic();
    $sales_agent_gtssres = $sales_agent_api->verify_license(true);    
    if(!$sales_agent_gtssres || ($sales_agent_gtssres && isset($sales_agent_gtssres['status']) && !$sales_agent_gtssres['status'])){
         $CI->app_modules->deactivate(SALES_AGENT_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }    
}

function sales_agent_preactivate($module_name){
    if ($module_name['system_name'] == SALES_AGENT_MODULE_NAME) {             
        require_once 'libraries/gtsslib.php';
        $sales_agent_api = new SalesAgentLic();
        $sales_agent_gtssres = $sales_agent_api->verify_license();          
        if(!$sales_agent_gtssres || ($sales_agent_gtssres && isset($sales_agent_gtssres['status']) && !$sales_agent_gtssres['status'])){
             $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/gtsverify/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.SALES_AGENT_MODULE_NAME); 
            $data['module_name'] = SALES_AGENT_MODULE_NAME; 
            $data['title'] = "Module License Activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }        
    }
}

function sales_agent_predeactivate($module_name){
    if ($module_name['system_name'] == SALES_AGENT_MODULE_NAME) {
        require_once 'libraries/gtsslib.php';
        $sales_agent_api = new SalesAgentLic();
        $sales_agent_api->deactivate_license();
    }
}