<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * load error page
 * @param  string $title  
 * @param  string $content
 * @return view         
 */
function infor_page($title = '',$content = '',$previous_link=''){
	$data['title'] = $title;
	$data['content'] = $content;  
	$data['previous_link'] = $previous_link;  
	$CI = & get_instance();                  
	$CI->data($data);
	$CI->view('client/info_page');
	$CI->layout();
}

/**
 * get all email contacts
 * @return $data_email
 */
function get_all_email_contacts(){
	$CI = & get_instance();                  
	$data = $CI->db->get(db_prefix() . 'contacts')->result_array();
	$data_email = [];
	foreach ($data as $key => $value) {
		$data_email[] = $value['email'];
	}
	return $data_email;
}
/**
 * cron job sync woo
 * @param  string $type    
 * @param  int $store   
 * @param  int $minutes 
 * @return  bolean         
 */
function cron_job_sync_woo($store = ''){

	$CI = & get_instance();      

	$CI->load->model('omni_sales/omni_sales_model');
	$CI->load->library('omni_sales/asynclibrary');
	$hour = date("H:i:s", time());
	$hour_cron = get_option('time_cron_woo');
    //records
	$records_time1 = get_option('records_time1');
	$records_time2 = get_option('records_time2');
	$records_time3 = get_option('records_time3');
	$records_time4 = get_option('records_time4');
	$records_time5 = get_option('records_time5');
	$records_time6 = get_option('records_time6');
	$records_time7 = get_option('records_time7');
	$records_time8 = get_option('records_time8');
	

	$config_store = $CI->omni_sales_model->get_setting_auto_sync_store($store);

	$sync_omni_sales_inventorys = $config_store[0]['sync_omni_sales_inventorys'];
	$sync_omni_sales_products = $config_store[0]['sync_omni_sales_products'];
	$sync_omni_sales_orders = $config_store[0]['sync_omni_sales_orders'];
	$sync_omni_sales_description = $config_store[0]['sync_omni_sales_description'];
	$sync_omni_sales_images = $config_store[0]['sync_omni_sales_images'];
	$price_crm_woo = $config_store[0]['price_crm_woo'];
	$product_info_enable_disable = $config_store[0]['product_info_enable_disable'];
	$product_info_image_enable_disable = $config_store[0]['product_info_image_enable_disable'];

	$minute_sync_inventory_info_time2 = $config_store[0]['time2'];
	$minute_sync_price_time3 = $config_store[0]['time3'];
	$minute_sync_decriptions_time4 = $config_store[0]['time4'];
	$minute_sync_images_time5 = $config_store[0]['time5'];
	$minute_sync_orders_time6 = $config_store[0]['time6'];
	$minute_sync_product_info_time7 = $config_store[0]['time7'];
	$minute_sync_product_info_images_time8 = $config_store[0]['time8'];
	if($store != ''){

		if($sync_omni_sales_orders == "1"){
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_orders_time6.' minutes', strtotime($records_time6)))){
				try {
					$result = $CI->omni_sales_model->process_orders_woo($store);
				} catch (Exception $e) {
					
				} finally {
					update_option('records_time6', date("H:i:s"));
				}
				update_option('records_time6', date("H:i:s"));
			}
		}
		if($sync_omni_sales_inventorys == "1"){
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_inventory_info_time2.' minutes', strtotime($records_time2)))){
				try {
					$result = $CI->omni_sales_model->process_inventory_synchronization_detail($store);
				} catch (Exception $e) {
					
				} finally {
					update_option('records_time2', date("H:i:s"));
				}
				update_option('records_time2', date("H:i:s"));
			}
		}

		if($sync_omni_sales_description == "1"){
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_decriptions_time4.' minutes', strtotime($records_time4)))){
				if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_orders_time6.' minutes', strtotime($records_time6)))){
					try {
						$result = $CI->omni_sales_model->process_decriptions_synchronization_detail($store);
					} catch (Exception $e) {
					} finally {
						update_option('records_time4', date("H:i:s"));
					}
					update_option('records_time4', date("H:i:s"));
				}

			}
		}
		if($sync_omni_sales_images == "1"){
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_images_time5.' minutes', strtotime($records_time5)))){
				try {
					$result = $CI->omni_sales_model->process_images_synchronization_detail($store);
				} catch (Exception $e) {
				} finally {
					update_option('records_time5', date("H:i:s"));
				}
				update_option('records_time5', date("H:i:s"));
			}
		}
		if($price_crm_woo == "1")
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_price_time3.' minutes', strtotime($records_time3)))){
				try {
					$result = $CI->omni_sales_model->process_price_synchronization($store);
				} catch (Exception $e) {
				} finally {
					update_option('records_time3', date("H:i:s"));
				}
				update_option('records_time3', date("H:i:s"));
			}
		}

		if($product_info_image_enable_disable == "1"){
			if(strtotime($hour) >= date('H:i:s', strtotime('+'.$minute_sync_product_info_images_time8.' minutes', strtotime($records_time8)))){
				$url = site_url()."omni_sales/omni_sales_client/sync_products_from_store/".$store;
				$success = $CI->asynclibrary->do_in_background($url, array());
				update_option('records_time8', date("H:i:s"));
			}
		}


		
		return true;
	}

/**
 * get all store 
 * @return  stores
 */
function get_all_store(){
	$CI = & get_instance();      
	$CI->load->model('omni_sales/omni_sales_model');
	return $CI->omni_sales_model->get_woocommere_store();
}

function get_name_store($id){
	$CI = & get_instance();      
	$CI->db->where('id', $id);
	return $CI->db->get(db_prefix().'omni_master_channel_woocommere')->row()->name_channel;
}
hooks()->add_action('after_email_templates', 'add_purchase_receipt_email_templates');

if (!function_exists('add_purchase_receipt_email_templates')) {
    /**
     * Init inventory email templates and assign languages
     * @return void
     */
    function add_purchase_receipt_email_templates()
    {
    	$CI = &get_instance();

    	$data['purchase_receipt_templates'] = $CI->emails_model->get(['type' => 'omni_sales', 'language' => 'english']);

    	$CI->load->view('omni_sales/purchase_receipt_email_template', $data);
    }
}


/**
 * omni sales reformat currency j
 * @param  [type] $value 
 * @return [type]        
 */
function omni_sales_reformat_currency_j($value)
{

	$f_dot = str_replace(',','', $value);
	return ((float)$f_dot + 0);
}

/**
 * omni sales get payment name
 * @param  integer $id 
 * @return [type]     
 */
function omni_sales_get_payment_name($id)
{
	$CI = & get_instance(); 

	$payment_name ='';
	$CI->db->where('id',$id);               
	$data = $CI->db->get(db_prefix() . 'payment_modes')->row();

	if($data){
		$payment_name .= $data->name;
	}
	return $payment_name;
}

/**
 * omni sales get customer name
 * @param  [type] $id 
 * @return [type]     
 */
function omni_sales_get_customer_name($id, $name)
{
	$customer_name ='';

	$CI = & get_instance(); 

	if(isset($id) && $id != ''){
		$CI->db->where(db_prefix() . 'clients.userid', $id);
		$client = $CI->db->get(db_prefix() . 'clients')->row();

		if($client){
			$customer_name .= $client->company;
		}
	}else{
		$customer_name .= $name;
	}

	return $customer_name;
}

/**
 * omni get user group name
 * @return  
 */
function omni_get_user_group_name($user_id){
	$CI = & get_instance(); 
	$data = $CI->db->query('select name from '.db_prefix().'customer_groups a left join '.db_prefix().'customers_groups b on a.groupid = b.id where customer_id = '.$user_id)->result_array();
	$result = '';
	foreach ($data as $item) {
		$result .= $item['name'].', ';
	}
	if($result != ''){
		$result = rtrim($result, ', ');
	}
	return $result;
}
/**
 * omni channel exists
 * @param  string $channel 
 * @return boolean          
 */
function omni_channel_exists($channel){
	$CI = & get_instance(); 
	$CI->db->where('channel', $channel);
	$sales_channel = $CI->db->get(db_prefix().'sales_channel')->row();
	if($sales_channel){	
		return true;
	}
	return false;
}

/**
 * get status modules wh
 * @param  string $module_name 
 * @return boolean             
 */
function omni_get_status_modules($module_name){
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
 * omni ppc get image file name
 * @param  integer $id 
 * @return object     
 */
function omni_ppc_get_image_file_name($id){
	$CI             = &get_instance();
	$CI->db->where('rel_id',$id);
	$CI->db->where('rel_type','commodity_item_file');
	$CI->db->select('file_name');
	$CI->db->order_by('dateadded', 'desc');
	return $CI->db->get(db_prefix().'files')->row();
}

/**
   * get image items
   * @param  integer $item_id 
   * @return string          
   */
function omni_get_image_items($item_id){
	$file_path_rs  = site_url('modules/omni_sales/assets/images/no_image.jpg');
	$data_file = omni_ppc_get_image_file_name($item_id);
	if($data_file){
		$file_path_rs = omni_check_image_items($item_id, $data_file->file_name);
	}
	return $file_path_rs;
}

/**
 * omni check image items
 * @param  integer $item_id   
 * @param  string $file_name 
 * @return string            
 */
function omni_check_image_items($item_id, $file_name){
	$file_path  = 'modules/omni_sales/assets/images/no_image.jpg';
	$check_img = false;
	if(omni_get_status_modules('warehouse') == true){
		$fp  = 'modules/warehouse/uploads/item_img/'.$item_id.'/'.$file_name;
		if(file_exists(FCPATH.$file_path) ){
			$file_path = $fp;
			$check_img = true;
		}
	}
	if(!$check_img && omni_get_status_modules('purchase') == true){
		$fp  = 'modules/purchase/uploads/item_img/'.$item_id.'/'.$file_name;
		if(file_exists(FCPATH.$file_path) ){
			$file_path = $fp;
			$check_img = true;
		}
	}
	if(!$check_img && omni_get_status_modules('manufacturing') == true){
		$fp  = 'modules/manufacturing/uploads/products/'.$item_id.'/'.$file_name;
		if(file_exists(FCPATH.$file_path) ){
			$file_path = $fp;
			$check_img = true;
		}
	}
	return site_url($file_path);
}


/**
 * email staff
 *
 * @param        $staffid  The staffid
 *
 */
function omni_email_staff($staffid){
	$CI = & get_instance();
	$CI->db->where('staffid', $staffid);
	return $CI->db->get(db_prefix().'staff')->row()->email;
}
/**
 * get status by index
 * @param  integer $index 
 * @return string        
 */
function get_status_by_index($index, $return_obj = false){
	$status = '';
	$slug = '';
	switch ($index) {
		case 0:
		$status = _l('omni_draft');
		$slug = 'draft';
		break;  
		case 1:
		$status = _l('processing');
		$slug = 'processing';
		break;      
		case 2:
		$status = _l('pending_payment');
		$slug = 'pending_payment';
		break;
		case 3:
		$status = _l('confirm');
		$slug = 'confirm';
		break;
		case 4:
		$status = _l('shipping');
		$slug = 'shipping';
		break;
		case 5:
		$status = _l('finish');
		$slug = 'finish';
		break;
		case 6:
		$status = _l('refund');
		$slug = 'refund';
		break;
		case 7:
		$status = _l('omni_return');
		$slug = 'return';
		break; 
		case 8:
		$status = _l('cancelled');
		$slug = 'cancelled';
		break;  
		case 9:
		$status = _l('omni_on_hold');
		$slug = 'on-hold';
		break;  
		case 10:
		$status = _l('omni_failed');
		$slug = 'failed';
		break; 
		case 11:
		$status = _l('omni_return');
		$slug = 'return';
		break; 
		case 12:
		$status = _l('omni_partial_return');
		$slug = 'partial_return';
		break; 
		case 13:
		$status = _l('omni_partial_refund');
		$slug = 'partial_refund';
		break; 
		case 14:
		$status = _l('paid');
		$slug = 'paid';
		break; 
	}
	if($return_obj){
		$obj = new stdClass();
		$obj->status = $status;
		$obj->slug = $slug;
		return $obj;
	}
	return $status;
}

/**
 * get index by status
 * @param  string $status 
 * @return integer        
 */
function get_index_by_status($status){
	$index = 0;
	switch ($status) {
		case 'draft':
		$index = 0;
		break;  
		case 'processing':
		$index = 1;
		break;  
		case 'pending':
		$index = 2;
		break;     
		case 'pending_payment':
		$index = 2;
		break;
		case 'confirm':
		$index = 3;
		break;
		case 'shipping':
		$index = 4;
		break;
		case 'finish':
		$index = 5;
		break;
		case 'completed':
		$index = 5;
		break;
		case 'refund':
		$index = 6;
		break;
		case 'refunded':
		$index = 6;
		break;
		case 'return':
		$index = 7;
		break; 
		case 'cancelled':
		$index = 8;
		break; 
		case 'on-hold':
		$index = 9;
		break;
		case 'failed':
		$index = 10;
		break;
	}
	return $index;
}

/**
 * get status by index woo
 * @param  integer $index 
 * @return string        
 */
function get_status_by_index_woo($index){
	$status = '';
	switch ($index) {
		case 1:
		$status = 'processing';
		break;
		case 2:
		$status = 'pending';//pending_payment
		break;
		case 5:
		$status = "completed";//finish
		break;
		case 6:
		$status = 'refunded';//refund
		break;
		case 8:
		$status = 'cancelled';
		break;
		case 9:
		$status = 'on-hold';
		break;
		case 10:
		$status = 'failed';
		break;
	}
	return $status;
}

/**
 * get all woo_customer_id
 * @return $woo_customer_ids
 */
function get_all_woo_customer_id($store_id){
	$CI = & get_instance();                  
	$CI->db->where('woo_channel_id', $store_id);
	$data = $CI->db->get(db_prefix() . 'clients')->result_array();
	$woo_customer_ids = [];
	foreach ($data as $key => $value) {
		$woo_customer_ids[] = $value['woo_customer_id'];
	}
	return $woo_customer_ids;
}
/**
 * get taxes
 * @param  integer $id
 * @return array or row
 */
function omni_get_taxes($id =''){
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id',$id);

        return $CI->db->get(db_prefix().'taxes')->row();
    }
    $CI->db->order_by('taxrate', 'ASC');
    return $CI->db->get(db_prefix().'taxes')->result_array();

}

/**
 * omni status list
 * @return array 
 */
function omni_status_list($is_return_order = false){
	if(!$is_return_order){
		return [
			['id' => 0, 'label' => _l('omni_draft'), 'key' => 'draft'],
			['id' => 1, 'label' => _l('processing'), 'key' => 'processing'],
			['id' => 2, 'label' => _l('pending_payment'), 'key' => 'pending_payment'],
			['id' => 14, 'label' => _l('paid'), 'key' => 'paid'],
			['id' => 3, 'label' => _l('confirm'), 'key' => 'confirm'],
			['id' => 4, 'label' => _l('shipping'), 'key' => 'shipping'],
			['id' => 5, 'label' => _l('finish'), 'key' => 'finish'],
			['id' => 10, 'label' => _l('omni_failed'), 'key' =>'failed'],
			['id' => 11, 'label' => _l('omni_return'), 'key' =>'return'],
			['id' => 6, 'label' => _l('refund'), 'key' => 'refund'],
			['id' => 12, 'label' => _l('omni_partial_return'), 'key' => 'partial_return'],
			['id' => 13, 'label' => _l('omni_partial_refund'), 'key' => 'partial_refund'],
			['id' => 8, 'label' => _l('omni_canceled'), 'key' => 'canceled'],
			['id' => 9, 'label' => _l('omni_on_hold'), 'key' => 'on_hold']
		];
	}
	else{
		return [
			['id' => 0, 'label' => _l('omni_draft'), 'key' => 'draft'],
			['id' => 1, 'label' => _l('processing'), 'key' => 'processing'],
			['id' => 2, 'label' => _l('pending_payment'), 'key' => 'pending_payment'],
			['id' => 3, 'label' => _l('confirm'), 'key' => 'confirm'],
			['id' => 4, 'label' => _l('shipping'), 'key' => 'shipping'],
			['id' => 5, 'label' => _l('finish'), 'key' => 'finish'],
			['id' => 10, 'label' => _l('omni_failed'), 'key' =>'failed'],
			['id' => 8, 'label' => _l('omni_canceled'), 'key' => 'canceled'],
			['id' => 9, 'label' => _l('omni_on_hold'), 'key' => 'on_hold']
		];
	}
	
}

/**
 * count portal order
 * @param  integer $status 
 * @return integer          
 */
function count_portal_order($userid, $status = 0, $channel_id = '', $where = ''){
	if(is_numeric($userid)){
		$CI           = & get_instance();
		$CI->db->select('id');
		if(is_numeric($channel_id)){
			$CI->db->where('channel_id', $channel_id);   
		}
		if($where != ''){
			$CI->db->where($where);   
		}
		$CI->db->where('userid', $userid);
		$CI->db->where('status', $status);
		return $CI->db->get(db_prefix().'cart')->num_rows();
	}
	return 0;
}
/**
 * AES_256 Encrypt
 * @param string $str
 * @return string
 */
function omni_aes_256_encrypt($str) {
	$key = get_option('omni_3des_key');
	if ($key == '' || $key == null) {
		$key = 'g8934fuw9843hwe8rf9*5bhv';
	}
	$method = 'aes-256-cbc';
	$key = substr(hash('sha256', $key, true), 0, 32);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	return base64_encode(openssl_encrypt($str, $method, $key, OPENSSL_RAW_DATA, $iv));
}
/**
 * AES_256 Decrypt
 * @param string $str
 * @return string
 */
function omni_aes_256_decrypt($str) {
	$key = get_option('omni_3des_key');
	if ($key == '' || $key == null) {
		$key = 'g8934fuw9843hwe8rf9*5bhv';
	}
	$method = 'aes-256-cbc';
	$key = substr(hash('sha256', $key, true), 0, 32);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	return openssl_decrypt(base64_decode($str), $method, $key, OPENSSL_RAW_DATA, $iv);
}

 /**
     * Format customer address info
     * @param  object  $data        customer object from database
     * @param  string  $for         where this format will be used? Eq statement invoice etc
     * @param  string  $type        billing/shipping
     * @param  boolean $companyLink company link to be added on customer company/name, this is used in admin area only
     * @return string
     */
    function public_format_customer_info($data, $for, $type, $companyLink = false)
    {
        $format   = get_option('customer_info_format');
        $clientId = '';

        if ($for == 'statement') {
            $clientId = $data->userid;
        } elseif ($type == 'billing') {
            $clientId = $data->clientid;
        }

        $filterData = [
            'data'         => $data,
            'for'          => $for,
            'type'         => $type,
            'client_id'    => $clientId,
            'company_link' => $companyLink,
        ];

        $companyName = '';
        if ($for == 'statement') {
            $companyName = get_company_name($clientId);
        } elseif ($type == 'billing') {
            $companyName = $data->client->company;
        }

        $acceptsPrimaryContactDisplay = ['invoice', 'estimate', 'payment', 'credit_note'];

        if (in_array($for, $acceptsPrimaryContactDisplay) &&
            isset($data->client->show_primary_contact) &&
            $data->client->show_primary_contact == 1 &&
            $primaryContactId = get_primary_contact_user_id($clientId)) {
            $companyName = get_contact_full_name($primaryContactId) . '<br />' . $companyName;
        }

        $companyName = hooks()->apply_filters('customer_info_format_company_name', $companyName, $filterData);

        $street  = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_street'} : '';
        $city    = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_city'} : '';
        $state   = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_state'} : '';
        $zipCode = in_array($type, ['billing', 'shipping']) ? $data->{$type . '_zip'} : '';

        $countryCode = '';
        $countryName = '';

        if ($country = in_array($type, ['billing', 'shipping']) ? get_country($data->{$type . '_country'}) : '') {
            $countryCode = $country->iso2;
            $countryName = $country->short_name;
        }

        $phone = '';
        if ($for == 'statement' && isset($data->phonenumber)) {
            $phone = $data->phonenumber;
        } elseif ($type == 'billing' && isset($data->client->phonenumber)) {
            $phone = $data->client->phonenumber;
        }

        $vat = '';
        if ($for == 'statement' && isset($data->vat)) {
            $vat = $data->vat;
        } elseif ($type == 'billing' && isset($data->client->vat)) {
            $vat = $data->client->vat;
        }

        if ($companyLink && (!isset($data->deleted_customer_name) ||
            (isset($data->deleted_customer_name) &&
                empty($data->deleted_customer_name)))) {
        	if(is_staff_logged_in()){
        		$companyName = '<a href="' . admin_url('clients/client/' . $clientId) . '" target="_blank"><b>' . $companyName . '</b></a>';
        	}
        } elseif ($companyName != '') {
            $companyName = '<b>' . $companyName . '</b>';
        }

        $format = _info_format_replace('company_name', $companyName, $format);
        $format = _info_format_replace('customer_id', $clientId, $format);
        $format = _info_format_replace('street', $street, $format);
        $format = _info_format_replace('city', $city, $format);
        $format = _info_format_replace('state', $state, $format);
        $format = _info_format_replace('zip_code', $zipCode, $format);
        $format = _info_format_replace('country_code', $countryCode, $format);
        $format = _info_format_replace('country_name', $countryName, $format);
        $format = _info_format_replace('phone', $phone, $format);
        $format = _info_format_replace('vat_number', $vat, $format);
        $format = _info_format_replace('vat_number_with_label', $vat == '' ? '' : _l('client_vat_number') . ': ' . $vat, $format);

        $customFieldsCustomer = [];

        // On shipping address no custom fields are shown
        if ($type != 'shipping') {
            $whereCF = [];

            if (is_custom_fields_for_customers_portal()) {
                $whereCF['show_on_client_portal'] = 1;
            }

            $customFieldsCustomer = get_custom_fields('customers', $whereCF);
        }

        foreach ($customFieldsCustomer as $field) {
            $value  = get_custom_field_value($clientId, $field['id'], 'customers');
            $format = _info_format_custom_field($field['id'], $field['name'], $value, $format);
        }

        // If no custom fields found replace all custom fields merge fields to empty
        $format = _info_format_custom_fields_check($customFieldsCustomer, $format);
        $format = _maybe_remove_first_and_last_br_tag($format);

        // Remove multiple white spaces
        $format = preg_replace('/\s+/', ' ', $format);
        $format = trim($format);

        return hooks()->apply_filters('customer_info_text', $format, $filterData);
    }
    /**
     * can refund order
     * @param  integer $order_id 
     * @return boolean           
     */
    function can_refund_order($order_id){
    	$CI = & get_instance();
    	$check_valid = true;
    	$max_day = get_option('omni_return_request_within_x_day');
    	if($max_day && (float)$max_day > 0 && $max_day = (float)$max_day){
    		$check_valid = false;
    		$cart_data = $CI->omni_sales_model->get_cart($order_id);
    		if($cart_data){
    			$CI->db->where('cart_id', $order_id);
    			$shipments = $CI->db->get(db_prefix().'wh_omni_shipments')->row();
    			// Check status is delivered
    			if($shipments && $shipments->shipment_status == 'product_delivered'){
    				$activity_logs = $CI->db->query('select date FROM '.db_prefix().'wh_goods_delivery_activity_log where rel_id = '.$shipments->id.' and rel_type = "shipment" order by date desc limit 0,1')->row();
    				if($activity_logs){
    					$delivered_date = $activity_logs->date;
    					$now = strtotime(date('Y-m-d')); // or your date as well
    					$your_date = strtotime(date('Y-m-d', strtotime($delivered_date)));
    					$datediff = $now - $your_date;
    					$number_of_date = round($datediff / (60 * 60 * 24))+1;
    					// Check number of day
    					if($number_of_date <= $max_day){
    						$check_valid = true;
    					}
    				}
    			}
    			else{
    				if($cart_data->status == 5){
    					$check_valid = true;    					
    				}
    			}
    		}
    	}
    	return $check_valid;
    }

    /**
 * Gets the vendor company name.
 *
 * @param      string   $userid                 The userid
 * @param      boolean  $prevent_empty_company  The prevent empty company
 *
 * @return     string   The vendor company name.
 */
function omni_get_vendor_company_name($userid, $prevent_empty_company = false)
{
    if ($userid !== '') {
        $_userid = $userid;
    }
    $CI = & get_instance();

    $client = $CI->db->select('company')
    ->where('userid', $_userid)
    ->from(db_prefix() . 'pur_vendor')
    ->get()
    ->row();
    if ($client) {
        return $client->company;
    }

    return '';
}

/**
 * get sales order code
 * @param  integer $id 
 * @return string     
 */
function omni_get_sales_order_code($id)
{
    $CI           = & get_instance();
    $sales_order_code = '';
    if (is_numeric($id)) {
        $CI->db->where('id', $id);
        $sales_order = $CI->db->get(db_prefix() . 'cart')->row();
        if($sales_order){
            $sales_order_code = $sales_order->order_number;
        }
    }
    return $sales_order_code;
}

/**
 * get warehouse name
 * @param  integer $id
 * @return array or row
 */
function omni_get_warehouse_name($id = false)
{
    $CI           = & get_instance();

    if ($id != false) {
        $CI->db->where('warehouse_id', $id);

        return $CI->db->get(db_prefix() . 'warehouse')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblwarehouse')->result_array();
    }

}


/**
 * get commodity name
 * @param  integer $id
 * @return array or row
 */
function omni_get_commodity_name($id = false)
{
    $CI           = & get_instance();

    if (is_numeric($id)) {
        $CI->db->where('id', $id);

        return $CI->db->get(db_prefix() . 'items')->row();
    }
    if ($id == false) {
        return $CI->db->query('select * from tblitems')->result_array();
    }

}

/**
 * get unit type
 * @param  integer $id
 * @return array or row
 */
function omni_get_unit_type($id = false)
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
 * render taxes html
 * @param  [type] $item_tax 
 * @param  [type] $width    
 * @return [type]           
 */
function omni_render_taxes_html($item_tax, $width)
{
    $itemHTML = '';
    $itemHTML .= '<td align="right" width="' . $width . '%">';

    if(is_array($item_tax) && isset($item_tax)){
        if (count($item_tax) > 0) {
            foreach ($item_tax as $tax) {

                $item_tax = '';
                if ( get_option('remove_tax_name_from_item_table') == false || multiple_taxes_found_for_item($item_tax)) {
                    $tmp      = explode('|', $tax['taxname']);
                    $item_tax = $tmp[0] . ' ' . app_format_number($tmp[1]) . '%<br />';
                } else {
                    $item_tax .= app_format_number($tax['taxrate']) . '%';
                }
                $itemHTML .= $item_tax;
            }
        } else {
            $itemHTML .=  app_format_number(0) . '%';
        }
    }
    $itemHTML .= '</td>';

    return $itemHTML;
}

function omni_convert_item_taxes($tax, $tax_rate, $tax_name)
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
            $CI->load->model('warehouse/warehouse_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->warehouse_model->get_tax_name($value);
                if(isset($arr_tax_rate[$key])){
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                }else{
                    $taxes[]['taxname'] = $_tax_name . '|' .  $CI->warehouse_model->tax_rate_by_id($value);

                }
            }
        }else{
            $CI->load->model('warehouse/warehouse_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->warehouse_model->get_tax_name($value);
                $_tax_rate = $CI->warehouse_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            } 
        }

    }

    return $taxes;
}


/**
 * check approval setting
 * @param  integer $type 
 * @return [type]       
 */
function omni_check_approval_setting($type)
{   
    $CI = &get_instance();
    $CI->load->model('warehouse/warehouse_model');

    $check_appr = $CI->warehouse_model->get_approve_setting($type);

    return $check_appr;
}
/**
 * get order number
 * @param  integer $order_id 
 * @return string           
 */
function get_order_number($order_id){
    $CI = &get_instance();
	$CI->db->select('order_number');
	$CI->db->where('id', $order_id);
	$data = $CI->db->get(db_prefix().'cart')->row();
	if($data){
		return $data->order_number;
	}
	return '';
}
/**
 * omni get total refund
 * @param  integer $order_id 
 * @return integer           
 */
function omni_get_total_refund($order_id){
	$CI = &get_instance();
	$CI->db->select('original_order_id');
	$CI->db->where('id', $order_id);
	$data = $CI->db->get(db_prefix().'cart')->row();
	// Caculate total refund only for return order
	if($data && is_numeric($data->original_order_id)){
		$total = 0;
		$CI->db->select('amount');
		$CI->db->where('order_id', $order_id);
		$data_refund = $CI->db->get(db_prefix().'omni_refunds')->result_array();
		if($data_refund){
			foreach ($data_refund as $key => $value) {
				$total += $value['amount'];
			}

		}
		return $total;
	}
	return 0;
}
/**
 * Sql currency
 * @param  string $value
 * @return 
 */
function omni_sql_currency($value)
{
	return str_replace(',','', $value);
}
/**
 * get return order of parent
 * @param  integer $order_id 
 * @return object           
 */
function get_return_order_of_parent($order_id){
	$CI = &get_instance();
	$CI->db->where('original_order_id', $order_id);
	return $CI->db->get(db_prefix().'cart')->row();
}

/**
     * check format date ymd
     * @param  date $date 
     * @return boolean       
     */
  function omni_check_format_date_ymd($date) {
  	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
  		return true;
  	} else {
  		return false;
  	}
  }
    /**
     * check format date
     * @param  date $date 
     * @return boolean 
     */
    function omni_check_format_date($date){
    	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/",$date)) {
    		return true;
    	} else {
    		return false;
    	}
    }
/**
* format date
* @param  date $date     
* @return date           
*/
function omni_format_date($date){
	if(!omni_check_format_date_ymd($date)){
		$date = to_sql_date($date);
	}
	return $date;
}            

/**
 * format date time
 * @param  date $date     
 * @return date           
 */
function omni_format_date_time($date){
	if(!omni_check_format_date($date)){
		$date = to_sql_date($date, true);
	}
	return $date;
}
/**
 * count refund
 * @param  integer $order_id 
 * @return integer           
 */
function omni_count_refund($order_id){
	$CI = &get_instance();
	$CI->db->where('order_id', $order_id);
	return $CI->db->get(db_prefix().'omni_refunds')->num_rows();
}

/**
 * get list woo status
 * @return array 
 */
function get_list_woo_status(){
	$list = [];
	$list[] = ['id' => 'processing', 'label' => _l('omni_processing')];
	$list[] = ['id' => 'pending', 'label' => _l('omni_pending')];
	$list[] = ['id' => 'completed', 'label' => _l('omni_completed')];
	$list[] = ['id' => 'refunded', 'label' => _l('omni_refunded')];
	$list[] = ['id' => 'cancelled', 'label' => _l('omni_cancelled')];
	$list[] = ['id' => 'on-hold', 'label' => _l('omni_on_hold')];
	$list[] = ['id' => 'failed', 'label' => _l('omni_failed')];
	return $list;
}

/**
 * { omni get order id by hash }
 *
 * @param        $hash   The hash
 *
 * @return     string 
 */
function omni_get_order_id_by_hash($hash){
	$CI = &get_instance();
	$CI->db->where('hash', $hash);
	$cart = $CI->db->get(db_prefix().'cart')->row();
	if($cart && !is_array($cart)){
		return $cart->id;
	}
	return '';
}

/**
 * omni_convert_item_taxes_v2
 * @param  [type] $tax      
 * @param  [type] $tax_rate 
 * @param  [type] $tax_name 
 * @return [type]           
 */
function omni_convert_item_taxes_v2($tax, $tax_rate, $tax_name)
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
            $CI->load->model('omni_sales/omni_sales_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->omni_sales_model->get_tax_name($value);
                if(isset($arr_tax_rate[$key])){
                    $taxes[]['taxname'] = $_tax_name . '|' .  $arr_tax_rate[$key];
                }else{
                    $taxes[]['taxname'] = $_tax_name . '|' .  $CI->omni_sales_model->tax_rate_by_id($value);

                }
            }
        }else{
            $CI->load->model('omni_sales/omni_sales_model');
            $arr_tax_id = explode('|', $tax);
            $arr_tax_rate = explode('|', $tax_rate);
            foreach ($arr_tax_id as $key => $value) {
                $_tax_name = $CI->omni_sales_model->get_tax_name($value);
                $_tax_rate = $CI->omni_sales_model->tax_rate_by_id($value);
                $taxes[]['taxname'] = $_tax_name . '|' .  $_tax_rate;
            } 
        }

    }

    return $taxes;
}