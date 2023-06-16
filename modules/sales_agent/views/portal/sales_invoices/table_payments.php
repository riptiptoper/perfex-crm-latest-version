<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix().'sa_sale_invoice_payment.id',
    'sale_invoice',
    'paymentmode',
    'transactionid',
    db_prefix().'sa_sale_invoices.clientid', 
    'amount', 
    db_prefix().'sa_sale_invoice_payment.date',
    'note',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'sa_sale_invoice_payment';
$join         = [ 
    'LEFT JOIN '.db_prefix().'sa_sale_invoices ON '.db_prefix().'sa_sale_invoices.id = '.db_prefix().'sa_sale_invoice_payment.sale_invoice',
    'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'sa_sale_invoice_payment.paymentmode',
    'LEFT JOIN ' . db_prefix() . 'sa_clients ON ' . db_prefix() . 'sa_clients.id = ' . db_prefix() . 'sa_sale_invoices.clientid',
];

$where = [];

$agent_id = get_sale_agent_user_id();

array_push($where, 'AND '.db_prefix().'sa_sale_invoices.agent_id = '.$agent_id);


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['currency', 'inv_number', db_prefix() . 'payment_modes.name as payment_mode_name', db_prefix().'sa_clients.name' ]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];

        $base_currency = get_base_currency();
        if($aRow['currency'] != 0){
            $base_currency = sa_get_currency_by_id($aRow['currency']);
        }

        if($aColumns[$i] == 'sale_invoice'){
            $_data = '<a href="'.site_url('sales_agent/portal/sale_invoice_detail/'.$aRow['sale_invoice']).'">'.$aRow['inv_number'].'</a>';
        }else if($aColumns[$i] == 'paymentmode'){
            $_data = $aRow['payment_mode_name'];
        }else if($aColumns[$i] == db_prefix().'sa_sale_invoices.clientid'){
            $_data = $aRow['name'];
        }else if($aColumns[$i] == 'amount'){
            $_data = app_format_money($aRow['amount'], $base_currency);
        }
        
        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
