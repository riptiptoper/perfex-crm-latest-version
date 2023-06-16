<?php

defined('BASEPATH') or exit('No direct script access allowed');
add_option('staff_sync_orders');
add_option('minute_sync_orders');
add_option('time_cron_woo');
add_option('minute_sync');

add_option('sync_omni_sales_products', 1);
add_option('sync_omni_sales_orders', 1);
add_option('sync_omni_sales_inventorys', 1);
add_option('sync_omni_sales_description', 0);
add_option('sync_omni_sales_images', 0);

add_option('price_crm_woo', 0);
add_option('product_info_enable_disable', 0);
add_option('product_info_image_enable_disable', 0);

add_option('minute_sync_product_info_time1');
add_option('minute_sync_inventory_info_time2');
add_option('minute_sync_price_time3');
add_option('minute_sync_decriptions_time4');
add_option('minute_sync_images_time5');
add_option('minute_sync_product_info_time7');
add_option('minute_sync_product_info_images_time8');

add_option('records_time1', date('H:i:s'));
add_option('records_time2', date('H:i:s'));
add_option('records_time3', date('H:i:s'));
add_option('records_time4', date('H:i:s'));
add_option('records_time5', date('H:i:s'));
add_option('records_time6', date('H:i:s'));
add_option('records_time7', date('H:i:s'));
add_option('records_time8', date('H:i:s'));

add_option('status_sync', 0);

add_option('invoice_sync_configuration', 1);

if (!$CI->db->table_exists(db_prefix() . 'sales_channel')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_channel` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `channel` varchar(150) NOT NULL,
    `status` varchar(15) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  $data['channel'] = 'pos';
  $data['status'] = 'active';
  $CI->db->insert(db_prefix().'sales_channel' , $data);
  $data['channel'] = 'portal';
  $data['status'] = 'active';
  $CI->db->insert(db_prefix().'sales_channel' , $data);
  $data['channel'] = 'woocommerce';
  $data['status'] = 'deactive';
  $CI->db->insert(db_prefix().'sales_channel' , $data);
}

if (!$CI->db->table_exists(db_prefix() . 'sales_channel_detailt')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "sales_channel_detailt` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_product_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `sales_channel_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}


if (!$CI->db->table_exists(db_prefix() . 'woocommere_store')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "woocommere_store` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(150) NULL,
    `ip` varchar(30) NULL,
    `url` varchar(350) NULL,
    `port` varchar(10) NULL,
    `token` varchar(250) NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'cart')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "cart` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_contact` int(11) NOT NULL,
    `name` varchar(120) NOT NULL,
    `address` varchar(250) NOT NULL,
    `phone_number` varchar(20) NOT NULL,
    `voucher` varchar(100) NOT NULL,
    `status` int(11) null DEFAULT 0,
    `complete` int(11) null DEFAULT 0,
    `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'cart_detailt')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "cart_detailt` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `classify` varchar(30) NULL,      
    `cart_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('product_name' ,db_prefix() . 'cart_detailt')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
    ADD COLUMN `product_name` VARCHAR(150) NULL,
    ADD COLUMN `prices` DECIMAL(15,2) NULL,
    ADD COLUMN `long_description` text NULL
    ');
}

if (!$CI->db->field_exists('order_number' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `order_number` varchar(100) NULL
    ');
}

if (!$CI->db->field_exists('channel_id' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `channel_id` int(11) NULL,
    ADD COLUMN `channel` varchar(150) NULL,
    ADD COLUMN `first_name` varchar(60) NULL,
    ADD COLUMN `last_name` varchar(60) NULL,
    ADD COLUMN `email` varchar(150) NULL
    ');
}

if (!$CI->db->table_exists(db_prefix() . 'omni_master_channel_woocommere')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_master_channel_woocommere` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name_channel` TEXT NOT NULL,
    `consumer_key` TEXT NOT NULL,
    `consumer_secret` TEXT NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('url' ,db_prefix() . 'omni_master_channel_woocommere')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_master_channel_woocommere`
    ADD COLUMN `url` TEXT NOT NULL
    ');
}
if (!$CI->db->field_exists('company' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `company` varchar(150) null,                  
    ADD COLUMN `phonenumber` varchar(15) null,                 
    ADD COLUMN `city` varchar(50) null,
    ADD COLUMN `state` varchar(50) null,                  
    ADD COLUMN `country` varchar(50) null,
    ADD COLUMN `zip` varchar(50) null,          
    ADD COLUMN `billing_street` varchar(150) null,                 
    ADD COLUMN `billing_city` varchar(50) null, 
    ADD COLUMN `billing_state` varchar(50) null,                 
    ADD COLUMN `billing_country` varchar(50) null,
    ADD COLUMN `billing_zip` varchar(50) null,
    ADD COLUMN `shipping_street` varchar(150) null,
    ADD COLUMN `shipping_city` varchar(50) null,
    ADD COLUMN `shipping_state` varchar(50) null,                
    ADD COLUMN `shipping_country` varchar(50) null,
    ADD COLUMN `shipping_zip` varchar(50) null
    ');
}
if (!$CI->db->field_exists('userid' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `userid` int(11) null                
    
    ');
}
if (!$CI->db->field_exists('notes' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `notes` text null                
    
    ');
}

if (!$CI->db->field_exists('reason' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `reason` varchar(250) NULL,
    ADD COLUMN `admin_action` int NULL DEFAULT 0
    ');
}

if (!$CI->db->field_exists('sku' ,db_prefix() . 'cart_detailt')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
    ADD COLUMN `sku` text not null                
    ');
}

if (!$CI->db->table_exists(db_prefix() . 'omni_trade_discount')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_trade_discount` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name_trade_discount` varchar(250) NOT NULL,
    `start_time` date NOT NULL,
    `end_time` date NOT NULL,      
    `group_clients` TEXT NOT NULL,
    `clients` TEXT NOT NULL,
    `group_items` TEXT NOT NULL,
    `items` TEXT NOT NULL,
    `formal` int(11) NOT NULL,
    `discount` int(11) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('voucher' ,db_prefix() . 'omni_trade_discount')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_trade_discount`
    ADD COLUMN `voucher` text null                
    ');
}

if (!$CI->db->field_exists('discount' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `discount` varchar(250) NULL,
    ADD COLUMN `discount_type` int NULL DEFAULT 0
    ');
}

if (!$CI->db->field_exists('total' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `total` varchar(250) NULL
    ');
}

if (!$CI->db->field_exists('sub_total' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `sub_total` varchar(250) NULL
    ');
}
if (!$CI->db->field_exists('prices' ,db_prefix() . 'sales_channel_detailt')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
    ADD COLUMN `prices` DECIMAL(15,2)
    ');
}
if (!$CI->db->table_exists(db_prefix() . 'woocommere_store_detailt')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "woocommere_store_detailt` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_product_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `woocommere_store_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}
if (!$CI->db->field_exists('prices' ,db_prefix() . 'woocommere_store_detailt')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'woocommere_store_detailt`
    ADD COLUMN `prices` DECIMAL(15,2)
    ');}

  if (!$CI->db->field_exists('discount_total' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `discount_total` varchar(250) NOT NULL DEFAULT ""
      ');
  }

  if (!$CI->db->field_exists('invoice' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `invoice` varchar(250) NOT NULL DEFAULT ""
      ');
  }
  if (!$CI->db->field_exists('number_invoice' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `number_invoice` varchar(250) NOT NULL DEFAULT ""
      ');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_log_discount')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_log_discount` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name_discount` varchar(250) NOT NULL,
      `client` int(11) NOT NULL,
      `price` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `product_quality` int(11) NOT NULL,
      `total_product` int(11) NOT NULL,
      `date_apply` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }
  if (!$CI->db->field_exists('stock_export_number' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `stock_export_number` varchar(250) NOT NULL DEFAULT ""
      ');
  }

  if (!$CI->db->field_exists('create_invoice' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `create_invoice` varchar(5) NOT NULL DEFAULT "off",
      ADD COLUMN `stock_export` varchar(5) NOT NULL DEFAULT "off"
      ');
  }

  if (!$CI->db->field_exists('customers_pay' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `customers_pay` DECIMAL(15,2) NOT NULL DEFAULT 0,
      ADD COLUMN `amount_returned` DECIMAL(15,2) NOT NULL DEFAULT 0
      ');
  }

  if (!$CI->db->field_exists('channel' ,db_prefix() . 'omni_trade_discount')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_trade_discount`
      ADD COLUMN `channel` int(11) NOT NULL DEFAULT 0,
      ADD COLUMN `store` varchar(11) NOT NULL DEFAULT ""
      ');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_log_sync_woo')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_log_sync_woo` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` varchar(250) NOT NULL,
      `regular_price` int(11) NOT NULL,
      `sale_price` int(11) NOT NULL,
      `date_on_sale_from` date NULL,
      `date_on_sale_to` date NULL,
      `short_description` TEXT NULL,
      `stock_quantity` int(11) NULL,
      `sku` TEXT NOT NULL,
      `type` varchar(225) NOT NULL,
      `date_sync` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('stock_quantity_history' ,db_prefix() . 'omni_log_sync_woo')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_log_sync_woo`
      ADD COLUMN `stock_quantity_history` int(11) NOT NULL DEFAULT 0,
      ADD COLUMN `order_id` int(11) NOT NULL,
      ADD COLUMN `chanel` varchar(250) NOT NULL DEFAULT "",
      ADD COLUMN `company` varchar(250) NOT NULL DEFAULT ""
      ');
  }

  if (!$CI->db->field_exists('tax' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `tax` DECIMAL(15,2) NOT NULL DEFAULT 0
      ');
  }
  if (!$CI->db->field_exists('percent_discount' ,db_prefix() . 'cart_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
      ADD COLUMN `percent_discount`  DECIMAL(15,0) not null,                
      ADD COLUMN `prices_discount`  DECIMAL(15,2) not null                
      ');
  }
  if (!$CI->db->field_exists('seller' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `seller` int(11) NULL
      ');
  }
  if (!$CI->db->field_exists('minimum_order_value' ,db_prefix() . 'omni_trade_discount')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_trade_discount`
      ADD COLUMN `minimum_order_value` DECIMAL(15,2) null              
      ');
  }

  if (!$CI->db->field_exists('voucher_coupon' ,db_prefix() . 'omni_log_discount')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_log_discount`
      ADD COLUMN `voucher_coupon` varchar(250) null
      ');
  }

  if (!$CI->db->field_exists('order_number' ,db_prefix() . 'omni_log_discount')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_log_discount`
      ADD COLUMN `order_number` varchar(100) null,
      ADD COLUMN `total_order` varchar(100) null,
      ADD COLUMN `discount` varchar(100) null,
      ADD COLUMN `tax` varchar(100) null,
      ADD COLUMN `total_after` varchar(100) null
      ');
  }

  if (!$CI->db->field_exists('channel_id' ,db_prefix() . 'omni_log_discount')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_log_discount`
      ADD COLUMN `channel_id` int(11) null
      ');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_setting_woo_store')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_setting_woo_store` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `store` int(11) NOT NULL,
      `sync_omni_sales_products` int(11) NOT NULL default 0,
      `time1` int(11) NOT NULL default 50,
      `sync_omni_sales_inventorys` int(11) NOT NULL default 0,
      `time2` int(11) NOT NULL default 50,
      `price_crm_woo` int(11) NOT NULL default 0,
      `time3` int(11) NOT NULL default 50,
      `sync_omni_sales_description` int(11) NOT NULL default 0,
      `time4` int(11) NOT NULL default 50,
      `sync_omni_sales_images` int(11) NOT NULL default 0,
      `time5` int(11) NOT NULL default 50,
      `sync_omni_sales_orders` int(11) NOT NULL default 0,
      `time6` int(11) NOT NULL default 50,
      `product_info_enable_disable` int(11) NOT NULL default 0,
      `time7` int(11) NOT NULL default 50,
      `product_info_image_enable_disable` int(11) NOT NULL default 0,
      `time8` int(11) NOT NULL default 50,
      `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }
  if (!$CI->db->field_exists('staff_note' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `staff_note` text null,                  
      ADD COLUMN `payment_note` text null                  
      ');
  }
  if (!$CI->db->field_exists('allowed_payment_modes' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `allowed_payment_modes` varchar(200) null                 
      ');
  }

  create_email_template('Purchase receipt', 'Hi {staff_name}! <br /><br />Thank you for shopping in our store.<br />
    We send a receipt of your purchase below.<br />{<span 12pt="">notification_content</span>}. <br /><br />Kind Regards.<br/>Very pleased to serve you!', 'purchase_receipt', 'Purchase receipt (Sent to customer)', 'purchase-receipt');

  if ($CI->db->field_exists('quantity' ,db_prefix() . 'cart_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
      MODIFY `quantity` float not null                 
      ');
  }
  if (!$CI->db->field_exists('warehouse_id' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `warehouse_id` INT null                 
      ');
  }
  if (!$CI->db->field_exists('description' ,db_prefix() . 'omni_log_sync_woo')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_log_sync_woo`
      ADD COLUMN `description` TEXT NULL
      ');
  }


  /*update for client in Kenya*/
  /*create table save importing CSV CustomerReport*/
  if (!$CI->db->table_exists(db_prefix() . 'omni_customer_report')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_customer_report` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `ser_no`  varchar(100) null,
      `authorized_by` text null,
      `date` date null,
      `time` varchar(100) null,
      `transaction_id` varchar(100) null,
      `receipt` varchar(100) null,
      `pay_mode` text null,
      `nozzle` text null,
      `product` text null,
      `quantity` double null,
      `total_sale` double null,
      `ref_slip_no` text null,
      `date_add` datetime null,
      `version` int(11) null,
      `customer_id` varchar(100) null,
      `payment_id` int(11) null,
      `shift_type` varchar(100) null,
      `date_time_transaction` datetime null,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_create_customer_report')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_create_customer_report` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `m_date_report` text,
      
      `m_total_diesel` double null,
      `m_total_pertrol` double null,
      `m_total_other` double null,
      `m_total_by_cash` double null,
      `m_total_by_mpesa` double null,
      `m_total_by_card` double null,
      `m_total_by_invoice` double null,

      `m_total_amount` double null,
      `m_total_quantity` double null,
      `date_time_transaction` datetime null,
      `list_customer_report_id` LONGTEXT,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }


  if (!$CI->db->table_exists(db_prefix() . 'omni_create_customer_report_detail')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_create_customer_report_detail` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `create_customer_report_id` int(11) not null,
      `date_add` datetime null,
      `attendant_name`  text null,

      `shift_type` varchar(100) null,
      `date_report` text,
      `total_diesel` double null,
      `total_pertrol` double null,
      `total_other_product` double null,
      `total_by_cash` double null,
      `total_by_mpesa` double null,
      `total_by_card` double null,
      `total_by_invoice` double null,
      `total_sales` double null,
      `list_customer_report_id` LONGTEXT null,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('shipping' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `shipping` DECIMAL(15,2) not null default "0.00",                
      ADD COLUMN `payment_method_title` varchar(250) null                
      ');
  }
  add_option('omni_show_products_by_department', 0);
  if (!$CI->db->field_exists('department' ,db_prefix() . 'sales_channel_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
      ADD COLUMN `department` text
      ');
  }
  add_option('bill_header_pos', '<div class="cls_003" style="text-align: center;"><span class="cls_003"><strong>PURCHASE RECEIPT</strong></span></div>');
  add_option('bill_footer_pos', '<div class="cls_004"><span class="cls_004">Thank you for shopping with us. Please come again</span></div>');

  if (!$CI->db->table_exists(db_prefix() . 'omni_shift')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_shift` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) not null,
      `shift_code` varchar(150) null,
      `granted_amount` DECIMAL(15,2) not null default 0.00,
      `incurred_amount` DECIMAL(15,2) not null default 0.00,
      `closing_amount` DECIMAL(15,2) not null default 0.00,
      `status` int not null DEFAULT 1,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_shift_history')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_shift_history` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `shift_id` int(11) not null,
      `action` varchar(150) null,
      `granted_amount` DECIMAL(15,2) not null default 0.00,
      `current_amount` DECIMAL(15,2) not null default 0.00,
      `customer_amount` DECIMAL(15,2) not null default 0.00,
      `balance_amount` DECIMAL(15,2) not null default 0.00,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('staff_id' ,db_prefix() . 'omni_shift_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_shift_history`
      ADD COLUMN `staff_id` int(11) null           
      ');
  }
  if (!$CI->db->field_exists('customer_id' ,db_prefix() . 'omni_shift_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_shift_history`
      ADD COLUMN `customer_id` int(11) null           
      ');
  }
  if (!$CI->db->field_exists('type' ,db_prefix() . 'omni_shift_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_shift_history`
      ADD COLUMN `type` varchar(50) null           
      ');
  }
  if (!$CI->db->field_exists('order_value' ,db_prefix() . 'omni_shift_history')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_shift_history`
      ADD COLUMN order_value DECIMAL(15,2) not null default 0.00          
      ');
  }
  if (!$CI->db->field_exists('order_value' ,db_prefix() . 'omni_shift')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'omni_shift`
      ADD COLUMN order_value DECIMAL(15,2) not null default 0.00          
      ');
  }

  if (!$CI->db->field_exists('tax' ,db_prefix() . 'cart_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
      ADD COLUMN `tax` text NULL
      ');
  }
  
  if (!$CI->db->field_exists('discount_type_str' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `discount_type_str` text null
      ');
  }
  
  if (!$CI->db->field_exists('discount_percent' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `discount_percent` DECIMAL(15,2) null
      ');
  }

  if (!$CI->db->field_exists('adjustment' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `adjustment` DECIMAL(15,2) null
      ');
  }

  if (!$CI->db->field_exists('currency' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `currency` INT(11) null
      ');
  }

  if (!$CI->db->field_exists('discount_total' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      CHANGE COLUMN `discount_total` `discount_total` DECIMAL(15,2) NULL;
      ');
  }

  if (!$CI->db->field_exists('currency' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `currency` INT(11) null
      ');
  }

  if (!$CI->db->field_exists('terms' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `terms` TEXT null
      ');
  }
  
  if (!$CI->db->table_exists(db_prefix() . 'omni_cart_payment')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_cart_payment` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `cart_id` int(11) not null,
      `payment_id` varchar(30) not null,
      `payment_name` varchar(100) null,
      `customer_pay` DECIMAL(15,2) not null default \"0.00\",
      `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('shipping_tax' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `shipping_tax` DECIMAL(15,2) null
      ');
  }

  if (!omni_channel_exists('pre_order')) {
    $data['channel'] = 'pre_order';
    $data['status'] = 'deactive';
    $CI->db->insert(db_prefix().'sales_channel' , $data);
  }
  if (!$CI->db->field_exists('enable' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `enable` int not null default 1
      ');
  }
  add_option('omni_default_seller', '');


  if (!$CI->db->field_exists('duedate' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `duedate` date NULL
      ');
  }

  if (!$CI->db->table_exists(db_prefix() . 'omni_pre_order_product_setting')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_pre_order_product_setting` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `channel_id` int(11) not null,
      `customer_group` text null,
      `customer` text null,
      `group_product_id` int(11) NULL,
      `datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
  }

  if (!$CI->db->field_exists('pre_order_product_st_id' ,db_prefix() . 'sales_channel_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
      ADD COLUMN `pre_order_product_st_id` int(11) null
      ');
  }
  if (!$CI->db->field_exists('customer_group' ,db_prefix() . 'sales_channel_detailt')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'sales_channel_detailt`
      ADD COLUMN `customer_group` text null,
      ADD COLUMN `customer` text null
      ');
  }
  if ($CI->db->field_exists('type' ,db_prefix() . 'emailtemplates')) {
    $CI->db->where('type', "purchase_receipt");
    $CI->db->update( db_prefix() . 'emailtemplates', ['type' => "omni_sales"]);
  }
  create_email_template('Pre-orders notify', 'Hi {seller_name}! <br /><br />You have a new order from {buyer_name}, the order is created at {create_at}. View order details: {link}.<br />', 'omni_sales', 'Pre-orders notify (Sent to seller)', 'pre-orders-notify');
  create_email_template('Pre-orders handover', 'Hi {to_name}! <br /><br />{from_name} has handed over an order to you. View order details: {link}.<br />', 'omni_sales', 'Pre-orders handover', 'pre-orders-handover');

add_option('number_of_days_to_save_diary_sync', 30);

  if (!$CI->db->field_exists('shipping_tax_json' ,db_prefix() . 'cart')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
      ADD COLUMN `shipping_tax_json` varchar(150) NULL
      ');
  }


if (!$CI->db->field_exists('woo_customer_id' ,db_prefix() . 'clients')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'clients`
    ADD COLUMN `woo_customer_id` int NULL DEFAULT 0,
    ADD COLUMN `woo_channel_id` int NULL DEFAULT 0
    ');
}
if (!$CI->db->field_exists('percent_discount' ,db_prefix() . 'cart_detailt')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart_detailt`
      CHANGE COLUMN `percent_discount` `percent_discount` float NULL;
  ');
}
add_option('omni_3des_key', '3des1213141516ahiocrth');
add_option('omni_allow_showing_shipment_in_public_link', 1);

if (!$CI->db->field_exists('discount_voucher' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `discount_voucher` varchar(150) NULL
  ');
}
add_option('omni_return_order_prefix', 'RE');
add_option('omni_return_request_within_x_day', 30);
add_option('omni_fee_for_return_order', 0);
add_option('omni_refund_loyaty_point', 1);
add_option('omni_return_policies_information', '');

if (!$CI->db->field_exists('original_order_id' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `original_order_id` int(11) NULL
  ');
}

if (!$CI->db->field_exists('return_reason' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `return_reason` longtext NULL
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_order_returns')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_returns` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `rel_id` INT(11) NULL,
    `rel_type` VARCHAR(50) NOT NULL COMMENT'manual, sales_return_order, purchasing_return_order',
    `return_type` VARCHAR(50) NULL COMMENT'manual, partially, fully',
    `company_id` INT(11) NULL,
    `company_name` VARCHAR(500) NULL,
    `email` VARCHAR(100) NULL,
    `phonenumber` VARCHAR(20) NULL,
    `order_number` VARCHAR(500) NULL,
    `order_date` DATETIME NULL,
    `number_of_item` DECIMAL(15,2) NULL DEFAULT '0.00',
    `order_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `order_return_number` VARCHAR(200) NULL,
    `order_return_name` VARCHAR(500) NULL,
    `fee_return_order` DECIMAL(15,2) NULL DEFAULT '0.00',
    `refund_loyaty_point` INT(11) NULL DEFAULT '0',
    `subtotal` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `additional_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `adjustment_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `return_policies_information` TEXT NULL,
    `admin_note` TEXT NULL,
    `approval` INT(11) NULL DEFAULT 0,
    `datecreated` DATETIME NULL,
    `staff_id` INT(11) NULL,
    `receipt_delivery_id` INT(1) NULL DEFAULT 0,
    `return_reason` longtext NULL,
    `receipt_delivery_type` VARCHAR(100) NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'wh_order_return_details')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . "wh_order_return_details` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_return_id` INT(11) NOT NULL,
    `rel_type_detail_id` INT(11) NULL,
    `commodity_code` INT(11) NULL,
    `commodity_name` TEXT NULL,
    `quantity` DECIMAL(15,2) NULL DEFAULT '0.00',
    `unit_id` INT(11) NULL,
    `unit_price` DECIMAL(15,2) NULL DEFAULT '0.00',
    `sub_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `tax_id`  TEXT NULL,
    `tax_rate`  TEXT NULL,
    `tax_name`  TEXT NULL,
    `total_amount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `discount_total` DECIMAL(15,2) NULL DEFAULT '0.00',
    `total_after_discount` DECIMAL(15,2) NULL DEFAULT '0.00',
    `reason_return` VARCHAR(200) NULL,

    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

if (!$CI->db->field_exists('approve_status' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `approve_status` int(11) NOT NULL DEFAULT 0;
  ');
}


if (!$CI->db->field_exists('process_invoice' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `process_invoice` varchar(5) NOT NULL DEFAULT "off";
  ');
}

if (!$CI->db->field_exists('stock_import_number' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
    ADD COLUMN `stock_import_number` int(11) NOT NULL DEFAULT 0
    ');
}
  
if (!$CI->db->field_exists('fee_for_return_order' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `fee_for_return_order` DECIMAL(15,2) NULL;
  ');
}

if (!$CI->db->table_exists(db_prefix() . 'omni_refunds')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "omni_refunds` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `order_id` INT(11) NULL,
      `staff_id` INT(11) NULL,
      `refunded_on` date NULL,
      `payment_mode` varchar(40) NULL,
      `note` text NULL,
      `amount` decimal(15,2) NULL,
      `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');
}

add_option('omni_pos_shipping_fee', 0);
add_option('omni_portal_shipping_fee', 0);
add_option('omni_manual_shipping_fee', 0);

if ($CI->db->table_exists(db_prefix() . 'goods_delivery')) {
  if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'goods_delivery')) {
    $CI->db->query('ALTER TABLE `' . db_prefix() . 'goods_delivery`
    ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
    ');
  }
}

if (!$CI->db->field_exists('shipping_fee' ,db_prefix() . 'invoices')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'invoices`
  ADD COLUMN `shipping_fee` DECIMAL(15,2) NULL DEFAULT "0.00"
  ');
}
add_option('omni_order_statuses_are_allowed_to_sync', '');

if (!$CI->db->field_exists('hash' ,db_prefix() . 'cart')) {
  $CI->db->query('ALTER TABLE `' . db_prefix() . 'cart`
  ADD COLUMN `hash` VARCHAR(32) NULL;
  ');
}

add_option('omni_display_shopping_cart', 1, 1);

if (!$CI->db->field_exists('estimate_id' ,db_prefix() . 'cart')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "cart`
      ADD COLUMN `estimate_id` int(11) NULL
  ;");
}

if (!$CI->db->field_exists('tax_id' ,db_prefix() . 'cart_detailt')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "cart_detailt`
      ADD COLUMN `tax_id` TEXT NULL,
      ADD COLUMN `tax_rate` TEXT NULL,
      ADD COLUMN `tax_name` TEXT NULL
  ;");
}

if (!$CI->db->field_exists('unit_id' ,db_prefix() . 'cart_detailt')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "cart_detailt`
      ADD COLUMN `unit_id` int(11) NULL,
      ADD COLUMN `unit_name` VARCHAR(255) NULL
  ;");
}

if (!$CI->db->field_exists('add_discount' ,db_prefix() . 'cart')) { 
  $CI->db->query('ALTER TABLE `' . db_prefix() . "cart`
      ADD COLUMN `add_discount` DECIMAL(15,2) DEFAULT '0.00'
  ;");
}