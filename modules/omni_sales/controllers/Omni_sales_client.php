<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Team password client controller
 */
class Omni_sales_client extends ClientsController
{
	/**
	 * __construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('omni_sales_model');
	}
		/**
		 * index 
		 * @param  int $page 
		 * @param  int $id   
		 * @param  string $key  
		 * @return view       
		 */
		public function index($page='',$id = '', $warehouse = '',$key = ''){  
			if($page == ''  || $id == ''){
				access_denied('Projects');
			}
			if($warehouse == ''|| !is_numeric($warehouse)){
				$warehouse = 0;
			}
			if($page == '' || !is_numeric($page)){
				$page = 1;
			}
			if($id == ''|| !is_numeric($id)){
				$id = 0;
			}
			if($key != ''){
				$key = trim(urldecode($key));
				$data['keyword'] = $key;
			}
			
			$data['ofset'] = 24;
			$data['title'] = _l('sales');
			$data['group_product'] = $this->omni_sales_model->get_group_product();      
			$data['group_id'] = $id;
			$data_product = $this->omni_sales_model->get_list_product_by_group(2, $id, $warehouse, $key,($page-1)*$data['ofset'], $data['ofset']);
			$data['product'] = [];
			$date = date('Y-m-d');
			foreach ($data_product['list_product'] as $item) {
				$discount_percent = 0;
				$data_discount = $this->omni_sales_model->check_discount($item['id'], $date, 2);
				if($data_discount){
					$discount_percent = $data_discount->discount;
				}
				$price = 0;
				$data_prices = $this->omni_sales_model->get_price_channel($item['id'],2);
				if($data_prices){
					$price = $data_prices->prices;
				}
				array_push($data['product'], array(
					'id' => $item['id'],
					'name' => $item['description'],
					'without_checking_warehouse' => $item['without_checking_warehouse'],
					'price' => $price,
					'w_quantity' => ($item['without_checking_warehouse'] == 1 ? 1000 : $this->get_stock($item['id'])), 
					'discount_percent' => $discount_percent,
					'has_variation' => $this->omni_sales_model->has_variation($item['parent_attributes']),
					'price_discount' => $this->get_price_discount($price, $discount_percent)
				));
			}
			$data['title_group'] = _l('all_products');
			$data['page'] = $page;
			$data['ofset_count'] = $data_product['count'];
			$data['total_page'] = ceil($data['ofset_count']/$data['ofset']);
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$this->data($data);
			$this->view('client/sales');
			$this->layout();
		} 

			/**
			 * view_cart
			 * @param  string $id 
			 * @return      
			 */
			public function view_cart($id = ''){
				$this->load->model('currencies_model');
				$data['base_currency'] = $this->currencies_model->get_base_currency();
				$data['title'] = _l('cart');
				$data['logged'] = $id;
				$this->data($data);
				$this->view('client/cart/cart');
				$this->layout();
			}
		/**
		 * form contact
		 * @param  int $contact_id 
		 * @return            
		 */
		public function form_contact($contact_id = '')
		{      
			$data['customer_id'] = '';
			$data['contactid']   = '';
			if ($this->input->post()) {
				$data             = $this->input->post();
				$data['password'] = $this->input->post('password', false);

				unset($data['contactid']);
				if ($contact_id == '') {
					$id      = $this->omni_sales_model->add_contact($data);
					$message = '';
					if ($id) {
						handle_contact_profile_image_upload($id);
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
					redirect(site_url('omni_sales/omni_sales_client/view_cart'));
				}
			}            
		}
		/**
		 * check out
		 * @param  int $id 
		 * @return redirect 
		 */
		public function check_out($id = '')
		{   
			if(is_client_logged_in()) {
				if($id == ''){
					redirect(site_url('omni_sales/omni_sales_client/view_cart/1'));         
				}
				else{
					redirect(site_url('omni_sales/omni_sales_client/view_overview'));        
				}
			}
			else{
				redirect_after_login_to_current_url();
				redirect(site_url('authentication/login'));
			}  
		}
		/**
		 * order success
		 * @return view
		 */
		public function order_success(){  
			infor_page(_l('order_success'),_l('you_have_successfully_placed_an_order'),site_url('omni_sales/omni_sales_client'));
		}

		/**
		 * { view overview }
		 *
		 * @param      string  $id     The identifier
		 * @return  redirect
		 */
		public function view_overview($id = ''){
			if($this->input->post()){
				$data = $this->input->post();
				$number_invoice = $this->omni_sales_model->check_out($data);                                    
				if($number_invoice){
					$data_invoice = $this->omni_sales_model->get_invoice($number_invoice);
					if($data_invoice){
						redirect(site_url('invoice/'.$data_invoice->id.'/'.$data_invoice->hash));         
					}
				}               
			}
			if(is_client_logged_in()){
				$data_userid = get_client_user_id();
				$data_profile = $this->clients_model->get($data_userid);
				if($data_profile){
					if($data_profile->shipping_street!='' && $data_profile->shipping_city!='' && $data_profile->shipping_street!='' && $data_profile->shipping_state!=''){
						if(isset($_COOKIE['cart_id_list'])){
							$list_id = $_COOKIE['cart_id_list'];
							$array_id = explode(',', $list_id);
							$list_group = [];
							$list_prices = [];
							$list_tax = [];
							foreach ($array_id as $key => $id) {
								$data_group = $this->omni_sales_model->get_product($id);
								if($data_group){
									$list_group[] = $data_group->group_id;
									$list_prices[] = $this->omni_sales_model->get_price_channel($id,2)->prices;
									$tax_value = 0;
									$tax_data = $this->omni_sales_model->get_tax_info_by_product($id);
									if($tax_data){
										$tax_value = $tax_data->taxrate;
									}
									$list_tax[] = $tax_value;
								}
							}
							$data['list_group'] = implode(',', $list_group);
							$data['list_prices'] = implode(',', $list_prices);
							$data['list_tax'] = implode(',', $list_tax);
							$data['tax'] = $this->omni_sales_model->check_tax_product($list_id);
							$this->load->model('payment_modes_model');
							$this->load->model('payments_model');
							$data['payment_modes'] = $this->payment_modes_model->get('', [
								'expenses_only !=' => 1,
							]);
							$this->load->model('currencies_model');
							$data['base_currency'] = $this->currencies_model->get_base_currency();
							$data['title'] = _l('cart');
							$this->data($data);
							$this->view('client/cart/overview_cart');
							$this->layout();
						}
						else{
							redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
						}

					}
					else{
						redirect(site_url('omni_sales/omni_sales_client/client/'.$data_userid));
					}
				}
				else{
					redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
				}
			}
			else{
				redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
			}
		}
		/**
		 * order successfull
		 * @param  int $order_number 
		 * @return   view            
		 */
		public function order_successfull($order_number){
			$this->load->model('currencies_model');

			$base_currency = $this->currencies_model->get_base_currency();
			$currency_name = '';
			if(isset($base_currency)){
				$currency_name = $base_currency->name;
			}
			$order = $this->omni_sales_model->get_cart_by_order_number($order_number);

			$data['order_detait'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($order->id);
			$address = $order->shipping_street.', '.$order->shipping_city.', '.$order->shipping_state.', '.get_country_short_name($order->shipping_country).', '.$order->shipping_zip;
			$data['content'] = '<div class="head_content"><span><i class="fa fa-check"></i></span></div>'._l('you_have_successfully_placed_an_order_with_a_code').' '.$order->order_number.', '._l('order_value_is').' '.app_format_money($order->total,'').' '.$currency_name.'.</br></br>'._l('please_wait_for_our_order_confirmation_and_delivery_to_the_address').': '.$address.'.</br></br>'._l('we_are_honored_to_serve_you').'!</br></br></br>';

			$data['previous_link'] = site_url('omni_sales/omni_sales_client');
			$data['link_text'] = _l('continue_shopping');
			$data['custom_link'] = site_url('omni_sales/omni_sales_client/view_order_detail/'.$order_number);
			$data['custom_link_text'] = _l('order_details');
			$this->data($data);
			$this->view('client/info_page');
			$this->layout();

		}
		/**
		 * view order detail
		 * @param  int $order_number 
		 * @return  view             
		 */
		public function view_order_detail($order_number){
			$this->load->model('currencies_model');
			$this->load->model('warehouse/warehouse_model');
			$data['order'] = $this->omni_sales_model->get_cart_by_order_number($order_number);
			if($data['order']){
				$data['base_currency'] = $this->currencies_model->get_base_currency();
				if(is_numeric($data['order']->currency) && $data['order']->currency > 0){
					$data['base_currency'] = $this->currencies_model->get($data['order']->currency);
				}
				$order_id = $data['order']->id;
				$data['order_detait'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($order_id);
				$shipment = $this->warehouse_model->get_shipment_by_order($order_id);	
				if($shipment){
					$data['cart'] = $data['order'];
					$data['title']          = $data['cart']->order_number;
					$data['shipment']          = $shipment;
					$data['order_id']          = $order_id;
					if($data['cart']->number_invoice != ''){
						$data['invoice'] = $this->omni_sales_model->get_invoice($data['cart']->number_invoice);
					}
				//get activity log
					$data['arr_activity_logs'] = $this->warehouse_model->wh_get_shipment_activity_log($shipment->id);
					$new_activity_log = [];
					foreach($data['arr_activity_logs'] as $key => $value){
						if($value['rel_type'] == 'delivery'){
							$value['description'] = preg_replace("/<a[^>]+\>[a-z]+/i", "", $value['description']);
						}
						$new_activity_log[] = $value;					
					}
					$data['arr_activity_logs'] = $new_activity_log;
					$wh_shipment_status = wh_shipment_status();
					$shipment_staus_order='';
					foreach ($wh_shipment_status as $shipment_status) {
						if($shipment_status['name'] ==  $data['shipment']->shipment_status){
							$shipment_staus_order = $shipment_status['order'];
						}
					}

					foreach ($wh_shipment_status as $shipment_status) {
						if((int)$shipment_status['order'] <= (int)$shipment_staus_order){
							$data[$shipment_status['name']] = ' completed';
						}else{
							$data[$shipment_status['name']] = '';
						}
					}
					$data['shipment_staus_order'] = $shipment_staus_order;
				//get delivery note
					if(is_numeric($data['cart']->stock_export_number)){
						$this->db->where('id', $data['cart']->stock_export_number);
						$data['goods_delivery'] = $this->db->get(db_prefix() . 'goods_delivery')->result_array();
						$data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($data['cart']->stock_export_number);
					}
				}			

				$data['title'] = _l('omni_orders_detail');
				$this->data($data);
				$this->view('client/cart/order_detailt');
				$this->layout();
			}
			else{
				redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));         
			}
		}
		/**
		 * change status order
		 * @param  int $order_number 
		 * @return   redirect             
		 */
		public function change_status_order($order_number){
			if($this->input->post()){
				$data = $this->input->post();
				$insert_id = $this->omni_sales_model->change_status_order($data,$order_number);
				if ($insert_id) {
					redirect(site_url('omni_sales/omni_sales_client/view_order_detail/'.$order_number));         
				}               
			}
		}
		/**
		 * order list
		 * @param  int $tab 
		 * @return   view    
		 */
		public function order_list($tab = ''){
			$data['title'] = _l('order_list');

			if($tab == ''){
				$data['tab'] = 0;
			}
			else{
				$data['tab'] = $tab;
			}
			$data['status'] = $tab;        
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$data['cart_list'] = [];
			$userid = get_client_user_id();
			if(is_numeric($userid)){
				$data['cart_list'] = $this->omni_sales_model->get_cart_of_client_by_status($userid,$tab,'', '(channel_id = 2 OR channel_id = 6  OR channel_id = 4) and original_order_id is null');
			}
			$this->data($data);
			$this->view('client/cart/order_list');
			$this->layout();
		}
		/**
		 * detailt 
		 * @param  int  $id 
		 * @return    view  
		 */
		public function detailt($id){
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();          
			$date = date('Y-m-d');
			$data['detailt_product'] = $this->omni_sales_model->get_product($id);

			$group_id = 0;
			$group_name = '';

			if($data['detailt_product']){
				$parent_id = $data['detailt_product']->parent_id;
				if($parent_id != null && $parent_id > 0){
					$data['detailt_product'] = $this->omni_sales_model->get_product($parent_id);
					$group_id = $data['detailt_product']->group_id;

				}
			}
			$data['group_id'] = $group_id;
			if(is_numeric($group_id) && $group_id > 0){
				$data_group = $this->omni_sales_model->get_group_product($group_id);
				if($data_group){
					$group_name = $data_group->name;
				}
			}

			$max_product = 15;
			$count_product = 0;
			$data_product  = $this->omni_sales_model->get_list_product_by_group_s(2,$group_id,$id,0,$max_product);
			
			$data['group'] = $group_name;
			$data['product'] = [];
			$data['price']  = 0;
			$data_prices = $this->omni_sales_model->get_price_channel($id,2);
			if($data_prices){
				$data['price']  = $data_prices->prices;
			}

			$discount_percent = 0;
			$data['discount'] = $this->omni_sales_model->check_discount($id, $date, 2);
			if($data['discount']){
				$discount_percent = $data['discount']->discount;
			}
			$data['discount_percent'] = $discount_percent;

			$data['price_discount'] = $this->get_price_discount($data['price'], $discount_percent);
			$data['amount_in_stock'] = (($data['detailt_product']->without_checking_warehouse == 0) ? $this->get_stock($id) : 1000);


			$date = date('Y-m-d');
			if($data_product){
				foreach ($data_product['list_product'] as $item) {
					$discount_percent = 0;
					$data_discount = $this->omni_sales_model->check_discount($item['id'], $date);
					if($data_discount){
						$discount_percent = $data_discount->discount;
					}
					$price = 0;
					$data_prices = $this->omni_sales_model->get_price_channel($item['id'],2);
					if($data_prices){
						$price = $data_prices->prices;
					}
					array_push($data['product'], array(
						'id' => $item['id'],
						'name' => $item['description'],
						'price' => $price,
						'w_quantity' => $this->get_stock($item['id']),
						'discount_percent' => $discount_percent,
						'price_discount' => $this->get_price_discount($price, $discount_percent)
					));
				}

				$count_product = $data_product['count'];

				if($count_product<$max_product){
					$data_group = $this->omni_sales_model->get_group_product_s($group_id);
					foreach ($data_group as $key => $group) {
						$data_product  = $this->omni_sales_model->get_list_product_by_group_s(2,$group['id'],$id,0,$max_product);

						foreach ($data_product['list_product'] as $item) {
							$discount_percent = 0;
							$data_discount = $this->omni_sales_model->check_discount($item['id'], $date);
							if($data_discount){
								$discount_percent = $data_discount->discount;
							}
							$price = 0;
							$data_prices = $this->omni_sales_model->get_price_channel($item['id'],2);
							if($data_prices){
								$price = $data_prices->prices;
							}
							array_push($data['product'], array(
								'id' => $item['id'],
								'name' => $item['description'],
								'price' => $price,
								'w_quantity' => $this->get_stock($item['id']),
								'discount_percent' => $discount_percent,
								'price_discount' => $this->get_price_discount($price, $discount_percent)
							));
						}
						$count_product += $data_product['count'];
						if($count_product > $max_product){
							break;
						}
					}
				}          
			}
			$this->data($data);
			$this->view('client/detailt_product');
			$this->layout();
		}
		/**
		 * get product by group 
		 * @param  int $page 
		 * @param  int $id   
		 * @return    json    
		 */
		public function get_product_by_group($page='',$id = '',$warehouse='',$key = ''){  

			$data['ofset'] = 24;          
			$data_product = $this->omni_sales_model->get_list_product_by_group(2,$id, $warehouse, $key,($page-1)*$data['ofset'],$data['ofset']);
			$data['product'] = [];
			$date = date('Y-m-d');
			foreach ($data_product['list_product'] as $item) {
				$discount_percent = 0;
				$data_discount = $this->omni_sales_model->check_discount($item['id'], $date);
				if($data_discount){
					$discount_percent = $data_discount->discount;
				}
				$price = 0;
				$data_prices = $this->omni_sales_model->get_price_channel($item['id'],2);
				if($data_prices){
					$price = $data_prices->prices;
				}
				array_push($data['product'], array(
					'id' => $item['id'],
					'name' => $item['description'],
					'without_checking_warehouse' => $item['without_checking_warehouse'],
					'price' => $price,
					'w_quantity' => ($item['without_checking_warehouse'] == 1 ? 1000 : $this->get_stock($item['id'])), 
					'discount_percent' => $discount_percent,
					'has_variation' => $this->omni_sales_model->has_variation($item['parent_attributes']),
					'price_discount' => $this->get_price_discount($price, $discount_percent)
				));
			}         
			$data['title_group'] = '';
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$html = $this->load->view('client/list_product/list_product_partial',$data,true);

			echo json_encode([
				'data'=>$html
			]);
			die;
		} 
			/**
			 * search product 
			 * @param  int  $group_id 
			 * @return            
			 */
			public function search_product($group_id){
				if($this->input->post()){
					$data = $this->input->post();
					redirect(site_url('omni_sales/omni_sales_client/index/1/'.$group_id.'/0/'.$data['keyword']));                    
				}
			}
			/**
			 * get stock 
			 * @param  int $product_id 
			 * @return   $w_qty           
			 */
			public function get_stock($product_id){
				$w_qty = 0;
				$wh = $this->omni_sales_model->get_total_inventory_commodity($product_id);
				if($wh){
					if($wh->inventory_number){
						$w_qty = $wh->inventory_number;
					}
				}
				return $w_qty;
			}
			/**
			 * get price discount
			 * @param  int $prices           
			 * @param   $discount_percent 
			 * @return      discount_percent              
			 */
			public function get_price_discount($prices, $discount_percent){
				return ($discount_percent * $prices) / 100;
			}
			/**
			 * voucher_apply 
			 * @return  json
			 */
			public function voucher_apply(){
				$data = $this->input->post();           
				$return = $this->omni_sales_model->get_discount_list($data['channel'],$data['client'],$data['voucher']);
				echo json_encode([$return]);
			}


		/**
		 * edit client info
		 * @param int $id
		 * @return view
		 */
		public function client($id = '')
		{
			if ($this->input->post() && !$this->input->is_ajax_request()) {
				if ($id == '') {
					redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
				} else {
					$success = $this->clients_model->update($this->input->post(), $id);
					if ($success == true) {
						set_alert('success', _l('updated_successfully', _l('client')));
					}
					redirect(site_url('omni_sales/omni_sales_client/view_overview'));
				}
			}

			$group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
			$data['group'] = $group;

			if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
				redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));
			}

			$data['groups'] = $this->clients_model->get_groups();

			if ($id == '') {
				$title = _l('add_new', _l('client_lowercase'));
			} else {
				$client                = $this->clients_model->get($id);
				$data['customer_tabs'] = get_customer_profile_tabs();

				if (!$client) {
					show_404();
				}

				$data['contacts'] = $this->clients_model->get_contacts($id);
				$data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

				

				if ($group == 'profile') {
					$data['customer_groups'] = $this->clients_model->get_customer_groups($id);
					$data['customer_admins'] = $this->clients_model->get_admins($id);

				} elseif ($group == 'attachments') {
					$data['attachments'] = get_all_customer_attachments($id);
				} elseif ($group == 'vault') {


					$data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

					if ($data['vault_entries'] === -1) {
						$data['vault_entries'] = [];
					}
				} elseif ($group == 'estimates') {
					$this->load->model('estimates_model');
					$data['estimate_statuses'] = $this->estimates_model->get_statuses();
				} elseif ($group == 'invoices') {
					$this->load->model('invoices_model');
					$data['invoice_statuses'] = $this->invoices_model->get_statuses();
				} elseif ($group == 'credit_notes') {
					$this->load->model('credit_notes_model');
					$data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
					$data['credits_available']     = $this->credit_notes_model->total_remaining_credits_by_customer($id);
				} elseif ($group == 'payments') {
					$this->load->model('payment_modes_model');
					$data['payment_modes'] = $this->payment_modes_model->get();
				} elseif ($group == 'notes') {
					$data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
				} elseif ($group == 'projects') {
					$this->load->model('projects_model');
					$data['project_statuses'] = $this->projects_model->get_project_statuses();
				} elseif ($group == 'statement') {
					if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
						set_alert('danger', _l('access_denied'));
						redirect(admin_url('clients/client/' . $id));
					}

					$data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));
				} elseif ($group == 'map') {
					if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {

						$this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());

						$this->app_scripts->add('google-maps-api-js', [
							'path'       => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',
							'attributes' => [
								'async',
								'defer',
								'latitude'       => "$client->latitude",
								'longitude'      => "$client->longitude",
								'mapMarkerTitle' => "$client->company",
							],
						]);
					}
				}
				$data['staff'] = $this->staff_model->get('', ['active' => 1]);
				$data['client'] = $client;
				$title          = $client->company;
				$data['members'] = $data['staff'];
				if (!empty($data['client']->company)) {
					if (is_empty_customer_company($data['client']->userid)) {
						$data['client']->company = '';
					}
				}
			}

			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();

			if ($id != '') {

				$customer_currency = $data['client']->default_currency;

				foreach ($data['currencies'] as $currency) {
					if ($customer_currency != 0) {
						if ($currency['id'] == $customer_currency) {
							$customer_currency = $currency;

							break;
						}
					} else {
						if ($currency['isdefault'] == 1) {
							$customer_currency = $currency;

							break;
						}
					}
				}

				if (is_array($customer_currency)) {
					$customer_currency = (object) $customer_currency;
				}

				$data['customer_currency'] = $customer_currency;

				$slug_zip_folder = (
					$client->company != ''
					? $client->company
					: get_contact_full_name(get_primary_contact_user_id($client->userid))
				);

				$data['zip_in_folder'] = slug_it($slug_zip_folder);
			}

			$data['bodyclass'] = 'customer-profile dynamic-create-groups';
			$data['title']     = $title;
			$this->data($data);
			$this->view('client/cart/client_info');
			$this->layout();
		}
		 /**
		 * get trade discount
		 * @return json 
		 */
		 public function get_trade_discount(){
		 	$data = $this->input->post();
		 	$channel = 1;
		 	if(isset($data['channel'])){
		 		$channel = $data['channel'];
		 	}    
		 	$list_discount = $this->omni_sales_model->get_discount_list($channel, $data['id']);
		 	$result = [];        
		 	if($list_discount != false){
		 		$result = $list_discount;
		 	}
		 	echo json_encode([$result]);
		 }

		 public function process_inventory_synchronization($store_id){
		 	$result = $this->omni_sales_model->process_inventory_synchronization_detail($store_id);
		 	echo json_encode($result);
		 }

		 public function process_images_synchronization($store_id){
		 	$result = $this->omni_sales_model->process_images_synchronization_detail($store_id);
		 	echo json_encode($result);
		 }

		 public function sync_products_from_info_woo($store_id){
		 	$success = $this->omni_sales_model->sync_products_from_info_woo($store_id);
		 	echo json_encode($success);
		 }

		 public function sync_products_from_store($store_id, $omni_warehouse){
		 	$success = $this->omni_sales_model->sync_from_the_store_to_the_system($store_id, $omni_warehouse);
		 	echo json_encode($success);
		 }
		/**
		 * get product variation
		 * @return json 
		 */
		public function get_product_variation(){
			$product_id = $this->input->post('product_id');
			$option_list = $this->input->post('option_list');
			$this->load->model('currencies_model');
			$base_currency = $this->currencies_model->get_base_currency();
			$currency_name = '';
			if(isset($base_currency)){
				$currency_name = $base_currency->name;
			}
			$get_product['product_id'] = '';
			$child_product_list = $this->omni_sales_model->get_list_child_products($product_id);

			foreach ($child_product_list as $key => $product) {
				$variation_list = json_decode($product['attributes']);
				$count_option = count($variation_list);
				$count_effect = 0;
				foreach ($variation_list as $variation) {
					foreach ($option_list as $option_selected) {
						if((trim($option_selected['variation_name']) == trim($variation->name)) && (trim($option_selected['variation_option']) == trim($variation->option))){    
							$count_effect++;
						}
					}
				}
				if($count_option == $count_effect){
					$get_product['image_url'] = $this->omni_sales_model->get_image_items($product['id']);
					$get_product['product_name'] = $product['description'];
					$get_product['long_description'] = $product['long_descriptions'];
					$get_product['description'] = $product['long_description'];
					$prices  = 0;
					$data_prices = $this->omni_sales_model->get_price_channel($product['id'],2);
					if($data_prices){
						$prices  = $data_prices->prices;
					}
					$get_product['rate'] = app_format_money($prices, $currency_name);
					$get_product['product_id'] = $product['id'];
					$get_product['w_quantity'] = $this->get_stock($product['id']);
					break;
				}
			}
			echo json_encode($get_product);
			die;
		}
/**
 * get variation list
 * @param  integer $product_id 
 * @return json             
 */
public function get_variation_list($product_id){
	$html = '';
	$data = $this->omni_sales_model->get_variation_product($product_id);
	if($data){
		$html = $this->load->view('client/list_product/list_variation', $data, true);
	}
	echo json_encode($html);
	die;
}

/**
 * pre order list
 */
public function pre_order_list(){
	$data['title'] = _l('omni_pre_order');
	$data_userid = get_client_user_id();
	$data['userid'] = $data_userid;
	$data['pre_oreders'] = $this->omni_sales_model->get_pre_orders_list_by_client($data_userid);

	$this->data($data);
	$this->view('client/pre_order/pre_order_list');
	$this->layout();
}

/**
 * create pre order
 * @param  string $id 
 */
public function create_pre_order($id = ''){
	$data['title'] = _l('omni_create_pre_order');
	if(is_client_logged_in()){ 
		$userid = get_client_user_id();
		$data['userid'] = $userid;
		if($this->input->post()){
			$data = $this->input->post();
			if($data['id'] == ''){
				unset($data['id']);
				$res = $this->omni_sales_model->create_new_pre_order($data);
				if ($res) {
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
				else{
					$message = _l('added_fail');
					set_alert('danger', $message);
				}
				redirect(site_url('omni_sales/omni_sales_client/order_list/draft'));
			}
		}

		if($id != ''){
			$data['pre_order'] = $this->omni_sales_model->get_pre_order($id);
		}

		if ($this->input->get('customer_id')) {
			$data['customer_id'] = $this->input->get('customer_id');
		}

		$this->load->model('payment_modes_model');
		$data['payment_modes'] = $this->payment_modes_model->get('', [
			'expenses_only !=' => 1,
		]);

		$this->load->model('taxes_model');
		$data['taxes'] = $this->taxes_model->get();

		$this->load->model('currencies_model');
		$data['currencies'] = $this->currencies_model->get();

		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['staff']     = $this->staff_model->get('', ['active' => 1]);
		$data['bodyclass'] = 'invoice';

		$data['client'] = $this->clients_model->get($userid);
		$data['products'] = $this->omni_sales_model->get_list_product_pre_order($userid);

		$this->data($data);
		$this->view('client/pre_order/create_pre_order');
		$this->layout();
	}
	else{
		redirect_after_login_to_current_url();
		redirect(site_url('authentication/login'));
	}
}



	/**
		 * client info pre order
		 * @param int $id
		 * @return view
		 */
	public function client_info_pre_order($id = '')
	{
		if(is_client_logged_in()){ 
			if ($this->input->post() && !$this->input->is_ajax_request()) {
				if ($id == '') {
					redirect(site_url('omni_sales/omni_sales_client/index/1/0/0'));
				} else {
					$success = $this->clients_model->update($this->input->post(), $id);
					if ($success == true) {
						set_alert('success', _l('updated_successfully', _l('client')));
					}
					redirect(site_url('omni_sales/omni_sales_client/create_pre_order'));
				}
			}
			$group = !$this->input->get('group') ? 'profile' : $this->input->get('group');
			$data['group'] = $group;

			if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
				redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));
			}

			$data['groups'] = $this->clients_model->get_groups();

			if ($id == '') {
				$title = _l('add_new', _l('client_lowercase'));
			} else {
				$client                = $this->clients_model->get($id);
				$data['customer_tabs'] = get_customer_profile_tabs();

				if (!$client) {
					show_404();
				}

				$data['contacts'] = $this->clients_model->get_contacts($id);
				$data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

				

				if ($group == 'profile') {
					$data['customer_groups'] = $this->clients_model->get_customer_groups($id);
					$data['customer_admins'] = $this->clients_model->get_admins($id);

				} elseif ($group == 'attachments') {
					$data['attachments'] = get_all_customer_attachments($id);
				} elseif ($group == 'vault') {


					$data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));

					if ($data['vault_entries'] === -1) {
						$data['vault_entries'] = [];
					}
				} elseif ($group == 'estimates') {
					$this->load->model('estimates_model');
					$data['estimate_statuses'] = $this->estimates_model->get_statuses();
				} elseif ($group == 'invoices') {
					$this->load->model('invoices_model');
					$data['invoice_statuses'] = $this->invoices_model->get_statuses();
				} elseif ($group == 'credit_notes') {
					$this->load->model('credit_notes_model');
					$data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
					$data['credits_available']     = $this->credit_notes_model->total_remaining_credits_by_customer($id);
				} elseif ($group == 'payments') {
					$this->load->model('payment_modes_model');
					$data['payment_modes'] = $this->payment_modes_model->get();
				} elseif ($group == 'notes') {
					$data['user_notes'] = $this->misc_model->get_notes($id, 'customer');
				} elseif ($group == 'projects') {
					$this->load->model('projects_model');
					$data['project_statuses'] = $this->projects_model->get_project_statuses();
				} elseif ($group == 'statement') {
					if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
						set_alert('danger', _l('access_denied'));
						redirect(admin_url('clients/client/' . $id));
					}

					$data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));
				} elseif ($group == 'map') {
					if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {

						$this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());

						$this->app_scripts->add('google-maps-api-js', [
							'path'       => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',
							'attributes' => [
								'async',
								'defer',
								'latitude'       => "$client->latitude",
								'longitude'      => "$client->longitude",
								'mapMarkerTitle' => "$client->company",
							],
						]);
					}
				}
				$data['staff'] = $this->staff_model->get('', ['active' => 1]);
				$data['client'] = $client;
				$title          = $client->company;
				$data['members'] = $data['staff'];
				if (!empty($data['client']->company)) {
					if (is_empty_customer_company($data['client']->userid)) {
						$data['client']->company = '';
					}
				}
			}

			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();

			if ($id != '') {

				$customer_currency = $data['client']->default_currency;

				foreach ($data['currencies'] as $currency) {
					if ($customer_currency != 0) {
						if ($currency['id'] == $customer_currency) {
							$customer_currency = $currency;

							break;
						}
					} else {
						if ($currency['isdefault'] == 1) {
							$customer_currency = $currency;

							break;
						}
					}
				}

				if (is_array($customer_currency)) {
					$customer_currency = (object) $customer_currency;
				}

				$data['customer_currency'] = $customer_currency;

				$slug_zip_folder = (
					$client->company != ''
					? $client->company
					: get_contact_full_name(get_primary_contact_user_id($client->userid))
				);

				$data['zip_in_folder'] = slug_it($slug_zip_folder);
			}

			$data['bodyclass'] = 'customer-profile dynamic-create-groups';
			$data['title']     = $title;
			$this->data($data);
			$this->view('client/cart/client_info');
			$this->layout();
		}
		else
		{
			redirect_after_login_to_current_url();
			redirect(site_url('authentication/login'));
		}
	}

	/**
	 * Get item by id
	 * @param  integer $id 
	 * @return json     
	 */
	public function get_item_by_id($id)
	{
		if ($this->input->is_ajax_request()) {
			$this->load->model('invoice_items_model');
			$this->load->model('currencies_model');
			$date = date('Y-m-d');
			$base_currency = $this->currencies_model->get_base_currency();

			$item = $this->omni_sales_model->get_product($id);

			$tax_name = '';
			$taxrate = '';
			$tax = $this->omni_sales_model->get_tax_info_by_product($id);
			if($tax){
				$tax_name = $tax->name.' ('.$tax->taxrate.'%)';
				$taxrate = $tax->taxrate;
			}
			$item->taxname = $tax_name;
			$item->taxrate = $taxrate;

			$unit_name = '';
			if($item->unit_id){
				$data_unit = $this->omni_sales_model->get_unit($item->unit_id);
				if($data_unit){
					$unit_name = $data_unit->unit_name;
				}          
			}
			$item->unitname = $unit_name;

			$data_product_channel = $this->omni_sales_model->get_product_channel($id, 6);
			$prices = '';
			if($data_product_channel){
				$prices = $data_product_channel->prices;
			}

			if($prices == ''){
				$prices = $item->rate;
			}

			$discount_percent = 0;
			$discount_price = 0;
			$discountss = $this->omni_sales_model->check_discount($id, $date, 2);
			if($discountss){
				if($taxrate > 0){
					$price = $prices * (1 + ($taxrate / 100));
					$discount_percent = $discountss->discount;
					$discount_price += ($discount_percent * $price) / 100;
				}
			}

			$item->rate_text = app_format_money($prices, '');
			$item->rate = $prices;
			$item->discount_price = $discount_price;
			$item->without_checking_warehouse = $item->without_checking_warehouse;
			echo json_encode($item);
		}
	}
/**
 * get order list
 * @param  integer $status 
 * @param  string $type   
 * @param  integer $ofset  
 * @param  integer $limit  
 * @return json         
 */
public function get_order_list($status = 0, $type, $ofset, $limit){
	$where = '';
	if($type == -1){
		$type = '';
		$where = '(channel_id = 2 OR channel_id = 6  OR channel_id = 4) and original_order_id is null';
	}
	$userid = get_client_user_id();
	$html = '';
	if(is_numeric($userid)){
		$data['cart_list'] = $this->omni_sales_model->get_cart_of_client_by_status($userid, $status, $type, $where);
		$html = $this->load->view('client/cart/cancelled', $data, true);
	}
	echo json_encode([
		'success' => true,
		'html' => $html
	]);
	die;
}

/**
 * get item pre order
 * @param  string $id 
 */
public function get_item_pre_order(){
	if(is_client_logged_in()){ 
		$userid = get_client_user_id();
		$keywork = $this->input->post('q');
		$products = $this->omni_sales_model->get_list_product_pre_order($userid, 'and description like "%'.$keywork.'%"');
		$result = [];
		foreach($products as $value){
			$result[] = ['id' => $value['id'], 'name' => $value['description'], 'subtext' => $value['description'], 'type' => 'item', 'link' => ''];
		}
		echo json_encode($result);
		die;
	}
}

	/**
	* { index }
	*
	* @param      string  $id     The identifier
	* @param        $hash   The hash
	*/
	public function view_detail($hash)
	{
	    //Old script:  $id = omni_aes_256_decrypt(urldecode($hash));
	    $id = omni_get_order_id_by_hash($hash);
	    $data_cart = $this->omni_sales_model->get_cart($id);
	    if($data_cart && !is_array($data_cart)){
	    	$this->load->model('currencies_model');
			$this->load->model('warehouse/warehouse_model');
			$data['id'] = $id;
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$data['order'] = $this->omni_sales_model->get_cart_by_order_number($data_cart->order_number);

			$data['order_detait'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($data['order']->id);
			$data['activity_log'] = [];
			if($this->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log')){
				$data['activity_log'] = $this->warehouse_model->wh_get_activity_log($data['order']->stock_export_number,'omni_order');            
			}		

			$show_shipment = get_option('omni_allow_showing_shipment_in_public_link');
			if($show_shipment && $show_shipment == 1){
				$shipment = $this->warehouse_model->get_shipment_by_order($id);	
				if($shipment){	
					$data['cart'] = $data_cart;
					$data['title']          = $data['cart']->order_number;
					$data['shipment']          = $shipment;
					$data['order_id']          = $id;

					if($data['cart']->number_invoice != ''){
						$data['invoice'] = $this->omni_sales_model->get_invoice($data['cart']->number_invoice);
					}

					//get activity log
					$data['arr_activity_logs'] = $this->warehouse_model->wh_get_shipment_activity_log($shipment->id);
					$new_activity_log = [];
					foreach($data['arr_activity_logs'] as $key => $value){
						if($value['rel_type'] == 'delivery'){
							$value['description'] = preg_replace("/<a[^>]+\>[a-z]+/i", "", $value['description']);
						}
						$new_activity_log[] = $value;					
					}
					$data['arr_activity_logs'] = $new_activity_log;
					$wh_shipment_status = wh_shipment_status();
					$shipment_staus_order='';
					foreach ($wh_shipment_status as $shipment_status) {
						if($shipment_status['name'] ==  $data['shipment']->shipment_status){
							$shipment_staus_order = $shipment_status['order'];
						}
					}

					foreach ($wh_shipment_status as $shipment_status) {
						if((int)$shipment_status['order'] <= (int)$shipment_staus_order){
							$data[$shipment_status['name']] = ' completed';
						}else{
							$data[$shipment_status['name']] = '';
						}
					}
					$data['shipment_staus_order'] = $shipment_staus_order;

					//get delivery note
					if(is_numeric($data['cart']->stock_export_number)){
						$this->db->where('id', $data['cart']->stock_export_number);
						$data['goods_delivery'] = $this->db->get(db_prefix() . 'goods_delivery')->result_array();
						$data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($data['cart']->stock_export_number);
					}
				}
			}
			$data['tax_data'] = $this->omni_sales_model->get_html_tax_manual_order($id);

			$data['title'] = _l('omni_orders_detail');
			$this->data($data);
			$this->view('view_order/index');
			no_index_customers_area();
			$this->layout();
	    }
	    else{
			blank_page(_l('omni_order_not_found'));
	    }
	}
	/**
	 * view delivery voucher
	 * @param  string $hash 
	 */
	public function view_delivery_voucher($hash){
		$hash_expl = explode('_', $hash);
		$id = $hash_expl[1];
		$this->load->model('currencies_model');
		$this->load->model('warehouse/warehouse_model');

		$data['check_approve_status'] = $this->warehouse_model->check_approval_details($id, 2);
		$data['list_approve_status'] = $this->warehouse_model->get_list_approval_details($id, 2);

		//get vaule render dropdown select
		$data['commodity_code_name'] = $this->warehouse_model->get_commodity_code_name();
		$data['units_code_name'] = $this->warehouse_model->get_units_code_name();
		$data['units_warehouse_name'] = $this->warehouse_model->get_warehouse_code_name();

		$data['goods_delivery_detail'] = $this->warehouse_model->get_goods_delivery_detail($id);

		$data['goods_delivery'] = $this->warehouse_model->get_goods_delivery($id);
		$data_invoice = $this->omni_sales_model->get_invoices($data['goods_delivery']->invoice_id);
		$data['goods_delivery']->hash = $data_invoice->hash;
		$data['activity_log'] = $this->warehouse_model->wh_get_activity_log($id,'delivery');
		$data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($id);

		$data['title'] = _l('stock_export_info');
		$check_appr = $this->warehouse_model->get_approve_setting('2');
		$data['check_appr'] = $check_appr;
		$data['tax_data'] = $this->warehouse_model->get_html_tax_delivery($id);
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$data['base_currency'] = $base_currency;
		$data['title'] = _l('omni_orders_detail');
		$this->data($data);
		$this->view('view_delivery/index');
		no_index_customers_area();
		$this->layout();
	}
	
	/**
	 * view staff profile
	 * @param  string $hash 
	 */
	public function view_staff_profile($hash){
		$id = omni_aes_256_decrypt(urldecode($hash));
		$data['staff_p']     = $this->staff_model->get($id);
		if (!$data['staff_p']) {
			die;
		}
		$this->load->model('departments_model');
		$data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
		$data['departments']       = $this->departments_model->get();
		$data['title']             = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
		$this->data($data);
		$this->view('view_staff_profile/index');
		no_index_customers_area();
		$this->layout();
	}	

	/**
	 * view packing list
	 * @param  string $hash 
	 */
	public function view_packing_list($hash){
		$hash_expl = explode('_', $hash);
		$id = $hash_expl[1];
		$this->load->model('clients_model');
		$this->load->model('warehouse/warehouse_model');

		$data['get_staff_sign'] = $this->warehouse_model->get_staff_sign($id, 5);
		$data['check_approve_status'] = $this->warehouse_model->check_approval_details($id, 5);
		$data['list_approve_status'] = $this->warehouse_model->get_list_approval_details($id, 5);
		$data['payslip_log'] = $this->warehouse_model->get_activity_log($id, 5);

		//get vaule render dropdown select
		$data['commodity_code_name'] = $this->warehouse_model->get_commodity_code_name();
		$data['units_code_name'] = $this->warehouse_model->get_units_code_name();
		$data['units_warehouse_name'] = $this->warehouse_model->get_warehouse_code_name();

		$data['packing_list_detail'] = $this->warehouse_model->get_packing_list_detail($id);
		$data['packing_list'] = $this->warehouse_model->get_packing_list($id);
		$data['packing_list']->client = $this->clients_model->get($data['packing_list']->clientid);
		$data['activity_log'] = $this->warehouse_model->wh_get_activity_log($id,'packing_list');

		$data['title'] = _l('wh_packing_list');
		$check_appr = $this->warehouse_model->get_approve_setting('5');
		$data['check_appr'] = $check_appr;
		$data['tax_data'] = $this->warehouse_model->get_html_tax_packing_list($id);
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$data['base_currency'] = $base_currency;
		$this->data($data);
		$this->view('view_packing_list/index');
		no_index_customers_area();
		$this->layout();
	}

	/**
	 * create return request
	 * @param  string $order_number 
	 */
	public function create_return_request($order_number){
		if($this->input->post()){
			$data = $this->input->post();
			$insert_id = $this->omni_sales_model->create_return_request_portal($data, $order_number);
			if ($insert_id) {
				set_alert('success', _l('create_successfully'));
			}  
			else{
				set_alert('danger', _l('create_failed'));
			}             
			redirect(site_url('omni_sales/omni_sales_client/view_order_detail/'.$order_number));         
		}
	}

}