<?php

/**
 * Ensures that the module init file can"t be accessed directly, only within the application.
 */
defined("BASEPATH") or exit("No direct script access allowed");

/*
Module Name: App FreeSMS - Perfex Module
Description: Send sms via App FreeSMS API.
Author: App FreeSMS
Author URI: https://app.freesms.ro
Version: 1.0
Requires at least: 2.3.5
*/

/**
 * Module libraries path
 * e.q. modules/module_name/libraries
 * @param string $module module name
 * @param string $concat append additional string to the path
 * @return string
 */
require(__DIR__ . "/vendor/autoload.php");

define("APP_FREESMS_PERFEX_MODULE_NAME", "app_freesms_perfex");
define("SMS_TRIGGER_INVOICE_SEND_TO_CUSTOMER", "invoice_send_to_customer");

hooks()->add_filter("sms_gateways", "app_freesms_perfex_sms_gateways");
hooks()->add_filter("sms_triggers", "app_freesms_perfex_triggers");
hooks()->add_filter("sms_gateway_available_triggers", "app_freesms_perfex_triggers");
hooks()->add_action("invoice_sent", "customerInvoice");

function app_freesms_perfex_sms_gateways($gateways)
{
    $gateways[] = "app_freesms_perfex/sms_app_freesms_perfex";
    return $gateways;
}

function app_freesms_perfex_triggers($triggers)
{
    
    $invoice_fields = [
        "{contact_firstname}",
        "{contact_lastname}",
        "{client_company}",
        "{client_vat_number}",
        "{client_id}",
        "{invoice_link}",
        "{invoice_number}",
        "{invoice_duedate}",
        "{invoice_date}",
        "{invoice_status}",
        "{invoice_subtotal}",
        "{invoice_total}",
    ];
    

    $triggers[SMS_TRIGGER_INVOICE_SEND_TO_CUSTOMER] = [
        "merge_fields" => $invoice_fields,
        "label" => "Send Invoice To Customer",
        "info" => "Trigger when invoice is created or sent to customer contacts.",
    ];

    return $triggers;
}

function customerInvoice($id)
{
    $CI = &get_instance();
    $CI->load->helper("sms_helper");

    $invoice = $CI->invoices_model->get($id);
    $where = ["active" => 1, "invoice_emails" => 1];
    $contacts = $CI->clients_model->get_contacts($invoice->clientid, $where);

    foreach($contacts as $contact):
        $template = mail_template("invoice_overdue_notice", $invoice, $contact);
        $merge_fields = $template->get_merge_fields();
        if(is_sms_trigger_active(SMS_TRIGGER_INVOICE_SEND_TO_CUSTOMER)):
            $CI->app_sms->trigger(SMS_TRIGGER_INVOICE_SEND_TO_CUSTOMER, $contact["phonenumber"], $merge_fields);
        endif;
    endforeach;
}