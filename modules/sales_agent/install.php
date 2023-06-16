<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->field_exists('client_type', db_prefix() .'clients')) {
 	 $CI->db->query('ALTER TABLE `'.db_prefix() . 'clients` 
	ADD COLUMN `client_type` varchar(20) NULL;');            
}

if (!$CI->db->table_exists(db_prefix() . 'sa_programs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_programs` (
    `id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`from_date` DATE NULL,
	`to_date` DATE NULL,
	`indefinite` INT(1) NULL,
	`created_by` INT(11) NULL,
	`created_at` DATETIME NULL,
	`descriptions` TEXT NULL,
	`active` INT(1) NULL,
	`agent` TEXT NULL,
	`agent_group` TEXT NULL,
	`product` TEXT NULL,
	`product_group` TEXT NULL,
	`discount_type` varchar(20) NULL,

	PRIMARY KEY (`id`));');
}


if (!$CI->db->table_exists(db_prefix() . 'sa_program_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_program_detail` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `from_amount` INT(11) NULL,
    `to_amount` INT(11) NULL,
    `discount` DECIMAL(15,2) NULL,
	PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('program_id', db_prefix() .'sa_program_detail')) {
 	 $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_program_detail` 
	ADD COLUMN `program_id` INT(11) NULL;');            
}

if (!$CI->db->field_exists('product_group', db_prefix() .'sa_program_detail')) {
   $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_program_detail` 
  ADD COLUMN `product_group` TEXT NULL;');            
}

if (!$CI->db->field_exists('product', db_prefix() .'sa_program_detail')) {
   $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_program_detail` 
  ADD COLUMN `product` TEXT NULL;');            
}

if (!$CI->db->field_exists('commodity_code' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `commodity_code` varchar(100) NOT NULL,
  ADD COLUMN `commodity_barcode` text NULL,
  ADD COLUMN `unit_id` int(11) NULL,
  ADD COLUMN `sku_code` varchar(200)  NULL,
  ADD COLUMN `sku_name` varchar(200)  NULL,
  ADD COLUMN `purchase_price` decimal(15,2)  NULL
  ;");
}

if (!$CI->db->field_exists('can_be_sold' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_sold` VARCHAR(100) NULL DEFAULT 'can_be_sold'
  ;");
}
if (!$CI->db->field_exists('can_be_purchased' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_purchased` VARCHAR(100) NULL DEFAULT 'can_be_purchased' 
  ;");
}
if (!$CI->db->field_exists('can_be_manufacturing' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_manufacturing` VARCHAR(100) NULL DEFAULT 'can_be_manufacturing' 
  ;");
}

if (!$CI->db->field_exists('can_be_inventory' ,db_prefix() . 'items')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items`
  ADD COLUMN `can_be_inventory` VARCHAR(100) NULL DEFAULT 'can_be_inventory' 
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_join_program_request')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_join_program_request` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `program_id` INT(11) NULL,
    `agent_id` INT(11) NULL,
    `status` varchar(20) NULL,
    `note` TEXT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_clients')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_clients` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `agent_id` INT(11) NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(191) NULL,
    `vat` varchar(50) NULL,
    `phonenumber` varchar(100) NULL,
    `country` INT(11) NULL,
    `city` varchar(100) NULL,
    `zip` varchar(20) NULL,
    `state` varchar(50) NULL,
    `address` text NULL,
    `website` varchar(150) NULL,
    `created_at` datetime NULL,
    `created_by` INT(11) NULL,
    `group` INT(11) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_approval_setting')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_approval_setting` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `related` VARCHAR(255) NOT NULL,
  `setting` LONGTEXT NOT NULL,
  `type` VARCHAR(20) NULL,
  `agent_id` INT(11) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_approval_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_approval_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `rel_id` INT(11) NOT NULL,
  `rel_type` VARCHAR(45) NOT NULL,
  `staffid` VARCHAR(45) NULL,
  `approve` VARCHAR(45) NULL,
  `note` TEXT NULL,
  `date` DATETIME NULL,
  `approve_action` VARCHAR(255) NULL,
  `reject_action` VARCHAR(255) NULL,
  `approve_value` VARCHAR(255) NULL,
  `reject_value` VARCHAR(255) NULL,
  `staff_approve` INT(11) NULL,
  `action` VARCHAR(45) NULL,
  `agent_id` INT(11) NULL,
  `type` VARCHAR(20) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('sender', db_prefix() .'sa_approval_details')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_approval_details` 
ADD COLUMN `sender` INT(11) NULL AFTER `action`,
ADD COLUMN `date_send` DATETIME NULL AFTER `sender`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'sa_client_groups')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_client_groups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `agent_id` INT(11) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_options')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_options` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `value` TEXT NOT NULL,
  `agent_id` INT(11) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_pur_orders')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_pur_orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_name` VARCHAR(255) NOT NULL,
  `agent_id` INT(11) NULL,
  `order_number` VARCHAR(100) NOT NULL,
  `order_date` DATE NULL,
  `status` INT(11) NULL,
  `approve_status` INT(11) NULL,
  `datecreated` DATETIME NULL,
  `delivery_date` DATE NULL,
  `subtotal` DECIMAL(15,2) NULL,
  `total_tax` DECIMAL(15,2) NULL,
  `total` DECIMAL(15,2) NULL,
  `addedfrom` INT(11) NULL,
  `vendornote` TEXT NULL,
  `terms` TEXT NULL,
  `discount_percent` DECIMAL(15,2) NULL,
  `discount_total` DECIMAL(15,2) NULL,
  `discount_type` DECIMAL(15,2) NULL,
  `buyer` INT(11) NULL,
  `number` INT(11) NULL,
  `hash` VARCHAR(32) NULL,
  `delivery_status` INT(2) NULL,
  `type` VARCHAR(30) NULL,
  `currency` INT(11) NULL,
  `order_status` VARCHAR(30) NULL,
  `shipping_note` TEXT NULL,
  `currency_rate` DECIMAL(15,6) NULL,
  `from_currency` VARCHAR(20) NULL,
  `to_currency` VARCHAR(20) NULL,
  `shipping_fee` DECIMAL(15,2) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rates')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rates` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'currency_rate_logs')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "currency_rate_logs` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_currency_id` int(11) NULL,
    `from_currency_name` VARCHAR(100) NULL,
    `from_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `to_currency_id` int(11) NULL,
    `to_currency_name` VARCHAR(100) NULL,
    `to_currency_rate` decimal(15,6) NOT NULL DEFAULT '0.000000',
    `date` DATE NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'ware_unit_type')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "ware_unit_type` (
      `unit_type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `unit_code` varchar(100) NULL,
      `unit_name` text NULL,
      `unit_symbol` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      PRIMARY KEY (`unit_type_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('commodity_group_code' ,db_prefix() . 'items_groups')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "items_groups`
  ADD COLUMN `commodity_group_code` varchar(100) NULL AFTER `name`,
  ADD COLUMN `order` int(10) NULL AFTER `commodity_group_code`,
  ADD COLUMN `display` int(1)  NULL AFTER `order` ,
  ADD COLUMN `note` text NULL AFTER `display`
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_pur_order_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_pur_order_detail` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pur_order` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NOT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,0) NULL,
  `quantity` int(11) NOT NULL,
  `into_money` DECIMAL(15,0) NULL,
  `tax` text NULL,
  `total` DECIMAL(15,0) NULL,
  `discount_%` DECIMAL(15,0) NULL,
  `discount_money` DECIMAL(15,0) NULL,
  `total_money` DECIMAL(15,0) NULL,
  PRIMARY KEY (`id`));');
}

// Version 1.0.4
if (!$CI->db->field_exists('description' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    ADD COLUMN `description` TEXT NULL AFTER `item_code`
  ;");
}

//purchase order detail
if ($CI->db->field_exists('unit_price' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `unit_price` `unit_price` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('into_money' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `into_money` `into_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('total' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `total` `total` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('discount_%' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `discount_%` `discount_%` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('discount_money' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `discount_money` `discount_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if ($CI->db->field_exists('total_money' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
    CHANGE COLUMN `total_money` `total_money` DECIMAL(15,2) NULL DEFAULT NULL
  ;");
}

if (!$CI->db->field_exists('tax_value' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
      ADD COLUMN `tax_value` DECIMAL(15,2) NULL
  ;");
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if ($CI->db->field_exists('quantity' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
      CHANGE COLUMN `quantity` `quantity` DECIMAL(15,2) NOT NULL 
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'sa_pur_order_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
  ADD COLUMN `tax_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('item_name' ,db_prefix() . 'sa_pur_order_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
  ADD COLUMN `item_name` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('invoice_id' ,db_prefix() . 'sa_pur_orders')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_orders`
  ADD COLUMN `invoice_id` INT(11) NULL 
  ;");
}

if (!$CI->db->field_exists('program_id' ,db_prefix() . 'sa_pur_order_detail')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
  ADD COLUMN `program_id` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('agent_group_can_view' ,db_prefix() . 'sa_programs')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_programs`
  ADD COLUMN `agent_group_can_view` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('agent_can_view' ,db_prefix() . 'sa_programs')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_programs`
  ADD COLUMN `agent_can_view` TEXT NULL 
  ;");
}

if (!$CI->db->field_exists('stock_export_id' ,db_prefix() . 'sa_pur_orders')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_orders`
  ADD COLUMN `stock_export_id` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('order_id' ,db_prefix() . 'wh_omni_shipments')){
    $CI->db->query('ALTER TABLE `' . db_prefix() . "wh_omni_shipments`
  ADD COLUMN `order_id` INT(11) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_sale_invoices')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_sale_invoices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `inv_number` TEXT NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT "0",
  `datesend` datetime NULL,
  `clientid` int(11) NOT NULL,
  `deleted_customer_name` TEXT NULL,
  `number` int(11) NOT NULL,
  `prefix` TEXT NULL,
  `number_format` int(11) NOT NULL DEFAULT "0",
  `datecreated` datetime NOT NULL,
  `date` date NOT NULL,
  `duedate` date NULL,
  `currency` int(11) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `total_tax` decimal(15,2) NOT NULL DEFAULT "0.00",
  `total` decimal(15,2) NOT NULL,
  `adjustment` decimal(15,2) NULL,
  `addedfrom` int(11) NULL,
  `hash` TEXT NOT NULL,
  `status` TEXT NULL,
  `clientnote` text NULL,
  `adminnote` text NULL,
  `last_overdue_reminder` date NULL,
  `last_due_reminder` date NULL,
  `cancel_overdue_reminders` int(11) NOT NULL DEFAULT "0",
  `allowed_payment_modes` mediumtext NULL,
  `token` mediumtext NULL,
  `discount_percent` decimal(15,2) NULL DEFAULT "0.00",
  `discount_total` decimal(15,2) NULL DEFAULT "0.00",
  `discount_type` TEXT NOT NULL,
  `recurring` int(11) NOT NULL DEFAULT "0",
  `recurring_type` TEXT NULL,
  `custom_recurring` tinyint(1) NOT NULL DEFAULT "0",
  `cycles` int(11) NOT NULL DEFAULT "0",
  `total_cycles` int(11) NOT NULL DEFAULT "0",
  `is_recurring_from` int(11) NULL,
  `last_recurring_date` date NULL,
  `terms` text NULL,
  `seller` int(11) NOT NULL DEFAULT "0",
  `billing_street` TEXT NULL,
  `billing_city` TEXT NULL,
  `billing_state` TEXT NULL,
  `billing_zip` TEXT NULL,
  `billing_country` int(11) NULL,
  `shipping_street` TEXT NULL,
  `shipping_city` TEXT NULL,
  `shipping_state` TEXT NULL,
  `shipping_zip` TEXT NULL,
  `shipping_country` int(11) NULL,
  `include_shipping` tinyint(1) NULL,
  `show_shipping_on_invoice` tinyint(1) NOT NULL DEFAULT "1",
  `show_quantity_as` int(11) NOT NULL DEFAULT "1",
  `short_link` TEXT NULL,
  `shipping_fee` decimal(15,2) NULL DEFAULT "0.00",
  PRIMARY KEY (`id`));');

}

if (!$CI->db->field_exists('currency_rate' ,db_prefix() . 'sa_sale_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_sale_invoices`
  ADD COLUMN `currency_rate` DECIMAL(15,6) NULL
  ');
}

if (!$CI->db->field_exists('from_currency' ,db_prefix() . 'sa_sale_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_sale_invoices`
  ADD COLUMN `from_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->field_exists('to_currency' ,db_prefix() . 'sa_sale_invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_sale_invoices`
  ADD COLUMN `to_currency` VARCHAR(20) NULL
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_sale_invoice_details')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() .'sa_sale_invoice_details` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sale_invoice` INT(11) NOT NULL,
  `item_code` VARCHAR(100) NULL,
  `description` TEXT NULL,
  `unit_id` INT(11) NULL,
  `unit_price` DECIMAL(15,2) NULL,
  `quantity` DECIMAL(15,2) NULL,
  `into_money` DECIMAL(15,2) NULL,
  `tax` TEXT NULL,
  `total` DECIMAL(15,2) NULL,
  `discount_percent` DECIMAL(15,2) NULL,
  `discount_money` DECIMAL(15,2) NULL,
  `total_money` DECIMAL(15,2) NULL,
  `tax_value` DECIMAL(15,2) NULL,
  `tax_rate` TEXT NULL,
  `tax_name` TEXT NULL,
  `item_name` TEXT NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('street', db_prefix() .'sa_clients')) {
   $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_clients` 
  ADD COLUMN `street` TEXT NULL;');            
}

if (!$CI->db->table_exists(db_prefix() . 'sa_sale_invoice_payment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_sale_invoice_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `sale_invoice` int(11) NOT NULL,
      `amount` DECIMAL(15,2) NOT NULL,
      `paymentmode` LONGTEXT NULL,
      `date` DATE NOT NULL,
      `daterecorded` DATETIME NOT NULL,
      `note` TEXT NOT NULL,
      `transactionid` MEDIUMTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('requester' ,db_prefix() . 'sa_sale_invoice_payment')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_sale_invoice_payment`
    ADD COLUMN `requester` INT(11) NULL
  ;");
}


if (!$CI->db->table_exists(db_prefix() . 'sa_warehouse')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_warehouse` (
      `warehouse_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `warehouse_code` TEXT NULL,
      `warehouse_name` text NULL,
      `warehouse_address` text NULL,
      `order` int(10) NULL,
      `display` int(1) NULL COMMENT  'display 1: display (yes)  0: not displayed (no)',
      `note` text NULL,
      `agent_id` int(11) NULL,
      PRIMARY KEY (`warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_inventory_manage')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_inventory_manage` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `warehouse_id` int(11) NOT NULL ,
      `commodity_id` int(11) NOT NULL,
      `inventory_number` TEXT NULL,
      `agent_id` int(11) NOT NULL ,

      PRIMARY KEY (`id`, `commodity_id`, `warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('date_manufacture', 'sa_inventory_manage')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_inventory_manage` 
    ADD COLUMN `date_manufacture` date NULL AFTER `inventory_number`,
    ADD COLUMN `expiry_date` date NULL AFTER `date_manufacture`;');            

}
if (!$CI->db->field_exists('lot_number', 'sa_inventory_manage')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_inventory_manage` 
    ADD COLUMN `lot_number` TEXT
    ;');            
}

if (!$CI->db->field_exists('purchase_price' ,db_prefix() . 'sa_inventory_manage')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_inventory_manage`
  ADD COLUMN `purchase_price` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}

if (!$CI->db->field_exists('city' ,db_prefix() . 'sa_warehouse')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_warehouse`
      ADD COLUMN `city` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('state' ,db_prefix() . 'sa_warehouse')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_warehouse`
      ADD COLUMN `state` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('zip_code' ,db_prefix() . 'sa_warehouse')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_warehouse`
      ADD COLUMN `zip_code` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('country' ,db_prefix() . 'sa_warehouse')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_warehouse`
      ADD COLUMN `country` TEXT  NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_goods_receipt')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_receipt` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `agent_id` int(11) NULL,
      `supplier_code` TEXT NULL,
      `supplier_name` text NULL,
      `deliver_name` text NULL,
      `buyer_id` int(11) NULL,
      `description` text NULL,
      `pr_order_id` int(11) NULL COMMENT 'code puchase request agree',
      `date_c` date NULL ,
      `date_add` date NULL,
      `goods_receipt_code` TEXT NULL,
      `total_tax_money` TEXT NULL,
      `total_goods_money` TEXT NULL,
      `value_of_inventory` TEXT NULL,
      `total_money` TEXT NULL COMMENT 'total_money = total_tax_money +total_goods_money ',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('approval', 'sa_goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt` 
ADD COLUMN `approval` INT(11) NULL DEFAULT 0 AFTER `total_money`;');            
}

if (!$CI->db->field_exists('addedfrom', 'sa_goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt` 
ADD COLUMN `addedfrom` INT(11) NULL AFTER `total_money`;');            
}

if (!$CI->db->field_exists('warehouse_id', 'sa_goods_receipt')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt` 
    ADD COLUMN `warehouse_id` int(11) NULL AFTER `goods_receipt_code`
    ;');            
}

if (!$CI->db->field_exists('type' ,db_prefix() . 'sa_goods_receipt')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_receipt`
      ADD COLUMN `type` TEXT  NULL
  ;");
}

if (!$CI->db->field_exists('requester' ,db_prefix() . 'sa_goods_receipt')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_receipt`
      ADD COLUMN `requester` int(11)  NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_goods_receipt_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_receipt_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `goods_receipt_id` int(11) NOT NULL,
      `commodity_code` varchar(100) NULL,
      `commodity_name` text NULL,
      `warehouse_id` text NULL,
      `unit_id` text NULL,
      `quantities` text NULL,
      `unit_price` varchar(100) NULL,
      `tax` varchar(100) NULL,
      `tax_money` varchar(100) NULL,
      `goods_money` varchar(100) NULL ,
      `note` text NULL ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('date_manufacture', 'sa_goods_receipt_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt_detail` 
    ADD COLUMN `date_manufacture` date NULL AFTER `goods_money`,
    ADD COLUMN `expiry_date` date NULL AFTER `date_manufacture`;');            
}

if (!$CI->db->field_exists('discount', 'sa_goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt_detail` 
  ADD COLUMN `discount` TEXT
  ;');            
}

if (!$CI->db->field_exists('discount_money', 'sa_goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt_detail` 
  ADD COLUMN `discount_money` TEXT
  ;');            
}

if (!$CI->db->field_exists('lot_number', 'sa_goods_receipt_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_receipt_detail` 
    ADD COLUMN `lot_number` TEXT
    ;');            
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'sa_goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_receipt_detail`
      ADD COLUMN `tax_rate` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'sa_goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_receipt_detail`
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('tax_name' ,db_prefix() . 'sa_goods_receipt_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_receipt_detail`
      ADD COLUMN `tax_name` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'sa_goods_receipt_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_goods_receipt_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}


if (!$CI->db->table_exists(db_prefix() . 'sa_goods_transaction_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_transaction_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `agent_id` int(11) NOT NULL,
      `goods_receipt_id` int(11)  NULL COMMENT 'id_goods_receipt_id or goods_delivery_id',
      `goods_id` int(11) NOT NULL COMMENT ' is id commodity',
      `quantity` varchar(100) NULL,
      `date_add` DATETIME NULL,
      `commodity_id` int(11) NOT NULL,
      `warehouse_id` int(11) NOT NULL,
      `note`  text null,
      `status` int(2) NULL COMMENT '1:Goods receipt note 2:Goods delivery note',
      PRIMARY KEY (`id`,`goods_id`, `commodity_id`, `warehouse_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if ($CI->db->field_exists('goods_id', 'sa_goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_transaction_detail` 
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`, `commodity_id`);');            
}

if (!$CI->db->field_exists('old_quantity', 'sa_goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_transaction_detail` 
    ADD COLUMN `old_quantity` varchar(100) NULL AFTER `goods_id`
    ;');            
}

if (!$CI->db->field_exists('purchase_price', 'sa_goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_transaction_detail` 
  ADD COLUMN `purchase_price` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('price', 'sa_goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_transaction_detail` 
  ADD COLUMN `price` varchar(100)
  ;');            
}

if (!$CI->db->field_exists('expiry_date', 'sa_goods_transaction_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_transaction_detail` 
    ADD COLUMN `expiry_date` text NULL ,
    ADD COLUMN `lot_number` text NULL
    ;');            
}

if ($CI->db->field_exists('warehouse_id' ,db_prefix() . 'sa_goods_transaction_detail')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_transaction_detail`
        CHANGE COLUMN `warehouse_id` `warehouse_id` TEXT NOT NULL ,
        DROP PRIMARY KEY,
        ADD PRIMARY KEY (`id`, `commodity_id`)
    ;");
}

if (!$CI->db->field_exists('from_stock_name' ,db_prefix() . 'sa_goods_transaction_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_transaction_detail`
      ADD COLUMN `from_stock_name` int(11),
      ADD COLUMN `to_stock_name` int(11)
  ;");
}

if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'sa_goods_transaction_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_goods_transaction_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}

add_option('sa_wh_products_by_serial', 0, 1);


if (!$CI->db->field_exists('status_goods' ,db_prefix() . 'sa_pur_orders')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_orders`
      ADD COLUMN `status_goods` INT(11) NOT NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('wh_quantity_received' ,db_prefix() . 'sa_pur_order_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_pur_order_detail`
      ADD COLUMN `wh_quantity_received` TEXT  NULL
  ;");
}

if (!$CI->db->table_exists(db_prefix() . 'sa_goods_delivery')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_delivery` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `agent_id` int(11) NOT NULL,
      `rel_type` int(11) NULL COMMENT 'type goods delivery',
      `rel_document` int(11) NULL COMMENT 'document id of goods delivery',
      `customer_code` text NULL,
      `customer_name` TEXT NULL,
      `to_` TEXT NULL,
      `address` TEXT NULL,
      `description` text NULL COMMENT 'the reason delivery',
      `staff_id` int(11) NULL COMMENT 'salesman',
      `date_c` date NULL ,
      `date_add` date NULL,
      `goods_delivery_code` TEXT NULL COMMENT 'số chứng từ xuất kho',
      `approval` INT(11) NULL DEFAULT 0 COMMENT 'status approval ',
      `addedfrom` INT(11) ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->field_exists('total_money', 'sa_goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery` 
    ADD COLUMN `total_money` TEXT NULL AFTER `goods_delivery_code`
    ;');            
}

if (!$CI->db->field_exists('warehouse_id', 'sa_goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery` 
    ADD COLUMN `warehouse_id` int(11) NULL AFTER `goods_delivery_code`
    ;');            
}

if (!$CI->db->field_exists('total_discount', 'sa_goods_delivery')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery` 
  ADD COLUMN `total_discount` TEXT
  ;');            
}

if (!$CI->db->field_exists('after_discount', 'sa_goods_delivery')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery` 
  ADD COLUMN `after_discount` TEXT
  ;');            
}

if (!$CI->db->field_exists('invoice_id', 'sa_goods_delivery')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery` 
    ADD COLUMN `invoice_id` TEXT
    ;');            
}

 if (!$CI->db->field_exists('type' ,db_prefix() . 'sa_goods_delivery')) { 
    $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
        ADD COLUMN `type` TEXT  NULL
    ;");
  }

if (!$CI->db->field_exists('invoice_no' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
      ADD COLUMN `invoice_no` text NULL
  ;");
}

if (!$CI->db->field_exists('pr_order_id' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
      ADD COLUMN `pr_order_id` int(11) NULL
  ;");
}


if (!$CI->db->field_exists('type_of_delivery' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
    ADD COLUMN `type_of_delivery` VARCHAR(100) NULL DEFAULT 'total'
    ;");
}

if (!$CI->db->field_exists('additional_discount' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
      ADD COLUMN `additional_discount` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('delivery_status' ,db_prefix() . 'sa_goods_delivery')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery`
      ADD COLUMN `delivery_status` VARCHAR(100)  NULL DEFAULT 'ready_for_packing'
  ;");
}

if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'sa_goods_delivery')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_goods_delivery`
  ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_goods_delivery_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_delivery_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `goods_delivery_id` int(11) NOT NULL,
      `commodity_code` TEXT NULL,
      `commodity_name` text NULL,
      `warehouse_id` text NULL,
      `unit_id` text NULL,
      `quantities` text NULL,
      `unit_price` TEXT NULL,
      `note` text NULL ,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('total_money', 'sa_goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
    ADD COLUMN `total_money` TEXT NULL AFTER `unit_price`
    ;');            
}

if (!$CI->db->field_exists('discount', 'sa_goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
  ADD COLUMN `discount` TEXT
  ;');            
}

if (!$CI->db->field_exists('discount_money', 'sa_goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
  ADD COLUMN `discount_money` TEXT
  ;');            
}

if (!$CI->db->field_exists('available_quantity', 'sa_goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
  ADD COLUMN `available_quantity` TEXT
  ;');            
}

if (!$CI->db->field_exists('tax_id', 'sa_goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
    ADD COLUMN `tax_id` TEXT
    ;');            
}

if (!$CI->db->field_exists('total_after_discount', 'sa_goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
    ADD COLUMN `total_after_discount` TEXT
    ;');            
}

if (!$CI->db->field_exists('expiry_date', 'sa_goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
    ADD COLUMN `expiry_date` text  NULL ,
    ADD COLUMN `lot_number` text NULL
    ;');            
}

if (!$CI->db->field_exists('guarantee_period', 'sa_goods_delivery_detail')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'sa_goods_delivery_detail` 
    ADD COLUMN `guarantee_period` text  NULL 
    
    ;');            
}

if (!$CI->db->field_exists('tax_rate' ,db_prefix() . 'sa_goods_delivery_detail')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "sa_goods_delivery_detail`
      ADD COLUMN `tax_rate` TEXT NULL,
      ADD COLUMN `tax_name` TEXT NULL,
      ADD COLUMN `sub_total` DECIMAL(15,2) NULL DEFAULT '0'
  ;");
}

if (!$CI->db->field_exists('serial_number' ,db_prefix() . 'sa_goods_delivery_detail')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sa_goods_delivery_detail`
  ADD COLUMN `serial_number` VARCHAR(255) NULL
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'sa_goods_delivery_invoices_pr_orders')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "sa_goods_delivery_invoices_pr_orders` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `rel_id` int(11) NULL COMMENT  'goods_delivery_id',
      `rel_type` int(11) NULL COMMENT 'invoice_id or purchase order id',

      `type` varchar(100) NULL COMMENT'invoice,  purchase_orders',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}