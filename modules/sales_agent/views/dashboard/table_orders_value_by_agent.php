<?php

defined('BASEPATH') or exit('No direct script access allowed');


$aColumns = [
    db_prefix().'clients.userid as userid',
    'company',
    'firstname',
    'email',
    db_prefix().'clients.phonenumber as phonenumber',
    db_prefix().'clients.active',

];

$sIndexColumn = 'userid';
$sTable       = db_prefix().'clients';
$where        = [];
// Add blank where all filter can be stored
$filter = [];

$join = [
    'LEFT JOIN '.db_prefix().'contacts ON '.db_prefix().'contacts.userid='.db_prefix().'clients.userid AND '.db_prefix().'contacts.is_primary=1',
];

array_push($where, 'AND client_type = "agent"');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix().'contacts.id as contact_id',
    'lastname',
    db_prefix().'clients.zip as zip',
    'default_currency',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

$base_currency = get_base_currency();

foreach ($rResult as $aRow) {
    $row = [];

    $currency = $base_currency;
    if($aRow['default_currency'] != 0){
        $currency = sa_get_currency_by_id($aRow['default_currency']);
    }

    // Company
    $company  = $aRow['company'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('no_company_view_profile');
        $isPerson = true;
    }

    $url = admin_url('sales_agent/sale_agent/' . $aRow['userid']);

    if ($isPerson && $aRow['contact_id']) {
        $url .= '?contactid=' . $aRow['contact_id'];
    }

    $company = '<a href="' . $url . '">' . $company . '</a>';

    $row[] = $company;

    // Primary contact
    $row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('sales_agent/sale_agent/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>' : '');

    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');


    $row[] = total_rows(db_prefix().'sa_pur_orders', ['approve_status' => 2, 'agent_id' => $aRow['userid']]);

    $row[] = app_format_money(get_total_order_value_by_agent($aRow['userid'], $currency->id), $currency);

    $row[] = app_format_money(get_total_paid_amount_by_agent($aRow['userid'], $currency->id), $currency);;


    $row['DT_RowClass'] = 'has-row-options';


    $output['aaData'][] = $row;
}
