<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Project Roadmap
Description: Advanced reporting for projects. Track and manage the project progress overview, milestones progress and many more.
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('PROJECT_ROADMAP_MODULE_NAME', 'project_roadmap');

hooks()->add_action('admin_init', 'project_roadmap_module_init_menu_items');
hooks()->add_action('admin_init', 'project_roadmap_permissions');
hooks()->add_action('app_admin_head', 'project_roadmap_header_static_css_js');
hooks()->add_action('app_admin_footer', 'project_roadmap_load_js');
hooks()->add_action('app_admin_footer', 'project_roadmap_footer_static_js');
hooks()->add_filter('before_dashboard_render', 'project_roadmap_load_progress_js',10, 2);
hooks()->add_filter('get_dashboard_widgets', 'project_roadmap_add_dashboard_widget');

/**
* Register activation module hook
*/
register_activation_hook(PROJECT_ROADMAP_MODULE_NAME, 'project_roadmap_module_activation_hook');


function project_roadmap_load_js($dashboard_js) {
        $CI = &get_instance();
        $dashboard_js .=  $CI->load->view('project_roadmap/project_roadmap_dashboard_js');
        return $dashboard_js;
}

function project_roadmap_load_progress_js($data) {
        $CI = &get_instance();
        $CI->app_scripts->add('circle-progress-js','assets/plugins/jquery-circle-progress/circle-progress.min.js');
        return $data;
}

function project_roadmap_module_activation_hook() {
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}


function project_roadmap_add_dashboard_widget($widgets) {
    $widgets[] = [
            'path'      => 'project_roadmap/widget',
            'container' => 'top-12',
        ];
    return $widgets;
}

function project_roadmap_header_static_css_js(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	echo '<link href="' . base_url('modules/project_roadmap/assets/css/main.css') .'"  rel="stylesheet" type="text/css" />';
	
	if ($viewuri == '/admin/' || $viewuri == '/admin') {
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/variable-pie.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/export-data.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/accessibility.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/exporting.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts-3d.js') .'"></script>';
	}
	
	if (strpos($viewuri, '/admin/project_roadmap/view_project_roadmap/') !== false) {
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/variable-pie.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/export-data.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/accessibility.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/exporting.js') .'"></script>';
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/plugins/highcharts/highcharts-3d.js') .'"></script>';
	}
	
}

function project_roadmap_footer_static_js(){
    $CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	
	if (strpos($viewuri, '/admin/project_roadmap') !== false) {
		echo '<script src="' . base_url('modules/project_roadmap/assets/js/project_roadmap.js') .'"></script>';
	}
	
}


/**
* Register language files, must be registered if the module is using languages
*/

register_language_files(PROJECT_ROADMAP_MODULE_NAME, [PROJECT_ROADMAP_MODULE_NAME]);


/**
 * Init project_roadmap module menu items in setup in admin_init hook
 * @return null
 */
 
function project_roadmap_module_init_menu_items() {
    if (has_permission('project_roadmap', '', 'view')) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('project_roadmap', [
                'name'     => _l('project_roadmap'),
                'href'     => admin_url('project_roadmap'),
                'icon'     => 'fa fa-line-chart',
                'position' => 30
        ]);
    }
}

function project_roadmap_permissions() {
    $capabilities = [];
    $capabilities['capabilities'] = [
            'view'   => _l('permission_view_own'),
    ];

    register_staff_capabilities('project_roadmap', $capabilities, _l('project_roadmap'));
}



hooks()->add_action('app_init',PROJECT_ROADMAP_MODULE_NAME.'_actLib');
function project_roadmap_actLib()
{
    $CI = & get_instance();
    $CI->load->library(PROJECT_ROADMAP_MODULE_NAME.'/Envapi');
    $envato_res = $CI->envapi->validatePurchase(PROJECT_ROADMAP_MODULE_NAME);
    if (!$envato_res) {
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }
}

hooks()->add_action('pre_activate_module', PROJECT_ROADMAP_MODULE_NAME.'_sidecheck');
function project_roadmap_sidecheck($module_name)
{
    if ($module_name['system_name'] == PROJECT_ROADMAP_MODULE_NAME) {
        if (!option_exists(PROJECT_ROADMAP_MODULE_NAME.'_verified') && empty(get_option(PROJECT_ROADMAP_MODULE_NAME.'_verified')) && !option_exists(PROJECT_ROADMAP_MODULE_NAME.'_verification_id') && empty(get_option(PROJECT_ROADMAP_MODULE_NAME.'_verification_id'))) {
            $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/env_ver/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.PROJECT_ROADMAP_MODULE_NAME); 
            $data['module_name'] = PROJECT_ROADMAP_MODULE_NAME; 
            $data['title'] = "Module activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }
    }
}

hooks()->add_action('pre_deactivate_module', PROJECT_ROADMAP_MODULE_NAME.'_deregister');
function project_roadmap_deregister($module_name)
{
    if ($module_name['system_name'] == PROJECT_ROADMAP_MODULE_NAME) {
        delete_option(PROJECT_ROADMAP_MODULE_NAME."_verified");
        delete_option(PROJECT_ROADMAP_MODULE_NAME."_verification_id");
        delete_option(PROJECT_ROADMAP_MODULE_NAME."_last_verification");
        if(file_exists(__DIR__."/config/token.php")){
            unlink(__DIR__."/config/token.php");
        }
    }
}

