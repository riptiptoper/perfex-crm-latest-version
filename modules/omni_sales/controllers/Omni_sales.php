<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Omni sales 
 */
class Omni_sales extends AdminController
{
		/**
		 * __construct
		 */
		public function __construct()
		{
			parent::__construct();
			$this->load->model('omni_sales_model');
			$this->load->library('asynclibrary');
			hooks()->do_action('omni_sales_init'); 

		}

		/* List all announcements */

		public function omni_sales_channel()
		{
			$data['title'] = _l('omni_sales_channel');
			$this->load->view('sales_channel_mgt', $data);
		}
		/**
		 * add omni sales channel
		 * @return view
		 */
		public function add_omni_sales_channel(){

			$data['title'] = _l('add_omni_sales_channel');


			$this->load->view('sales_channel/add_omni_sales_channel', $data);
		}
		/**
		 * invoice list
		 * @param  int $id 
		 * @return view    
		 */
		public function invoice_list($id = ''){
			$this->load->model('payment_modes_model');
			$this->load->model('invoices_model');
			$data['payment_modes']        = $this->payment_modes_model->get('', [], true);
			$data['invoiceid']            = $id;
			$data['title']                = _l('invoices');
			$data['invoices_years']       = $this->invoices_model->get_invoices_years();
			$data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
			$data['invoices_statuses']    = $this->invoices_model->get_statuses();
			$data['bodyclass']            = 'invoices-total-manual';

			$data['title'] = _l('invoice_list');
			$this->load->view('invoice_list/invoice_list_mgt', $data);
		}
		/**
		 * diary sync 
		 * @param  int $id 
		 * @return view
		 */
		public function diary_sync($id = ''){
			$data['title'] = _l('diary_sync');
			$this->load->view('diary_sync/diary_sync_mgt', $data);
		}
		/**
		 * report
		 * @param  int $id
		 * @return view
		 */
		public function report($id = ''){
			$data['group'] = $this->input->get('group');

			$data['title'] = _l('report');

			$data['tab'][] = 'results_trade_discount';



			if($data['group'] == ''){
				$data['group'] = 'results_trade_discount';
			}

			$data['tabs']['view'] = 'includes/'.$data['group'];
			$data['id'] = $id;
			$data['list_group'] = $this->omni_sales_model->get_group_product();
			$data['product'] = $this->omni_sales_model->get_product();
			$this->load->view('report/manage', $data);
		}
		/**
		 * pos channel
		 * @return view
		 */
		public function pos(){
			$data['title'] = _l('pos');
			$this->load->model('currencies_model');
			$this->load->model('warehouse/warehouse_model');
			$data['shift'] = $this->input->get('shift');      
			$flag_check = false;
			if(!$data['shift']){
				$flag_check = true;
			}
			else{
				$data_shift = $this->omni_sales_model->get_shift($data['shift']);        
				if($data_shift){
					if($data_shift->status == 2){
						$flag_check = true;
					}
				}
				else{
					$flag_check = true;
				}
			}

			if($flag_check){
				set_alert('warning', _l('please_create_a_shift'));
				redirect(admin_url('omni_sales/shift')); 
			}

			$data['units'] = $this->warehouse_model->get_unit_add_commodity();
			$data['commodity_groups'] = $this->warehouse_model->get_commodity_group_add_commodity();

			$data['taxes'] = omni_get_taxes();

			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$data['list_group'] = $this->omni_sales_model->get_group_product();
			$data['client'] = $this->clients_model->get();
			$data['staff'] = $this->staff_model->get();
			$data['groups'] = $this->clients_model->get_groups();
			$data['warehouse'] =  $this->warehouse_model->get_warehouse();

			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();

			$this->load->model('payment_modes_model');
			$this->load->model('payments_model');
			$data['payment_modes'] = $this->payment_modes_model->get('', [
				'expenses_only !=' => 1,
			]);
			$data['list_payment'] = $this->payment_modes_model->get();


			$this->load->view('pos', $data);
		}

		/**
		 * change active channel
		 * @return json
		 */
		public function change_active_channel(){
			if($this->input->post()){
				$data = $this->input->post();
				$success = $this->omni_sales_model->change_active_channel($data);
				echo json_encode([
					'success' => $success
				]);
			}
		}
		/**
		 * add product channel
		 * @param int $channel
		 * @return view 
		 */
		public function add_product_channel($channel){
			$data['title'] =_l($channel).' > '. _l('add_product');
			$data_chanel = $this->omni_sales_model->get_sales_channel_by_channel($channel);
			$data['id_channel'] = $data_chanel->id;
			$data['channel'] = $channel;
			$data['products'] = $this->omni_sales_model->get_product_by_group('', $channel);
			$data['group_product'] = $this->omni_sales_model->get_group_product();

			$this->load->model('departments_model');
			$data['departments'] = $this->departments_model->get();
			$this->load->view('sales_channel/add_product_channel', $data);
		}
		/**
		 * add product management table
		 * @return table
		 */
		public function add_product_management_table(){
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->load->model('departments_model');
					$product_filter = $this->input->post('product_filter'); 
					$id_channel = $this->input->post('id_channel'); 
					$channel = $this->input->post('channel'); 
					$department_filter = $this->input->post('department_filter'); 
					$product_filter = $this->input->post('product_filter'); 
					$select = [
						db_prefix() . 'sales_channel_detailt.id',
						db_prefix() . 'items.commodity_code',
						db_prefix() . 'items.description',
						db_prefix() . 'sales_channel_detailt.id',
						db_prefix() . 'sales_channel_detailt.id',
						db_prefix() . 'sales_channel_detailt.id'         
					];
					if($channel == 'pos'){
						$select[] = db_prefix() . 'sales_channel_detailt.id';
					}

					$aColumns     = $select;
					$sIndexColumn = 'id';
					$sTable       = db_prefix() . 'sales_channel_detailt';
					$where        = [];
					$join         = [' left join '.db_prefix() . 'items on '.db_prefix() . 'items.id = '.db_prefix() . 'sales_channel_detailt.product_id'];
					array_push($where, ' AND '.db_prefix().'sales_channel_detailt.sales_channel_id = '.$id_channel);

					if(isset($department_filter)){
						$dep_query = '';
						foreach ($department_filter as $key => $departmentid) {
							$dep_query .= 'find_in_set('.$departmentid.', department) or ';
						}

						if($dep_query != ''){
							$dep_query = rtrim($dep_query, ' or ');
							array_push($where, ' AND ('.$dep_query.')');
						}
					}

					if(isset($product_filter)){
						if($product_filter != ''){
							$product_id = implode(',', $product_filter);
							array_push($where, ' AND product_id in ('.$product_id.')');          
						}
					}

					$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
						db_prefix() . 'sales_channel_detailt.id',
						'group_product_id',
						'product_id',
						'sales_channel_id',
						'department',
						'prices',
					]);


					$output  = $result['output'];
					$rResult = $result['rResult'];
					foreach ($rResult as $aRow) {
						$row = [];
						$row[] = '<input type="checkbox" class="ckb-add-product" data-id="'.$aRow['id'].'" onchange="checked_add(this); return false;"/>';                     
						$data_product =  $this->omni_sales_model->get_product_by_id($aRow['product_id']);
						$name ='';
						$code ='';
						$rate =0;
						if($data_product){
							$name = $data_product->description;
							$code = $data_product->commodity_code;
							$rate = $data_product->rate;
						}
						$rate = app_format_money($rate,'');
						$price_on_channel = app_format_money($aRow['prices'],'');
						$row[] = $code;             
						$row[] = $name;             
						$row[] = $rate;
						$row[] = $price_on_channel;
						if($channel == 'pos'){
							$dep_name = '';
							if($aRow['department'] && $aRow['department'] != ''){
								$dep_list = explode(',', $aRow['department']);
								foreach ($dep_list as $key => $dep) {
									$data_dep = $this->departments_model->get($dep);
									if($data_dep){
										$dep_name .= $data_dep->name.', ';
									}
								}
								if($dep_name != ''){
									$dep_name = rtrim($dep_name, ', ');
								}
							}
							$row[] = $dep_name;
						}


						$option = '';
						if(omni_get_status_modules('warehouse')){
							$option .= '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['product_id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('view').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
							$option .= '<i class="fa fa-eye"></i>';
							$option .= '</a>';
						}
						$option .= '<a href="#" onclick="update_product(this);" data-groupid="'.$aRow['group_product_id'].'" data-toggle="tooltip" data-placement="top" data-title="'._l('edit').'"  data-prices="'.app_format_money($aRow['prices'],'').'" data-price_on_channel="'.$price_on_channel.'" data-productid="'.$aRow['product_id'].'" data-department="'.$aRow['department'].'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
						$option .= '<i class="fa fa-edit"></i>';
						$option .= '</a>';
						$option .= '<a href="' . admin_url('omni_sales/delete_product/'.$channel.'/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('delete').'" class="btn btn-danger btn-icon _delete">';
						$option .= '<i class="fa fa-remove"></i>';
						$option .= '</a>';
						$row[] = $option; 
						$output['aaData'][] = $row;                                      
					}
					echo json_encode($output);
					die();
				}
			}
		}
		/**
		 * get list product
		 * @param  int $id
		 * @return json
		 */
		public function get_list_product($id='', $channel = ''){
			$list = $this->omni_sales_model->get_product_by_group($id, $channel);
			$html = '';
			foreach ($list as $key => $value) {
				$html .= '<option value="'.$value['id'].'">'.$value['commodity_code'].' # '.$value['description'].'</option>';
			}
			echo json_encode([
				'success' => true,
				'html' => $html
			]);
			die;          
		}
		/**
		 * add product
		 * @return redirect
		 */
		public function add_product(){
			if($this->input->post()){
				$data = $this->input->post();    
				if($data['sales_channel_id'] != ''){
					$channel = $data['channel'];
					unset($data['channel']);
					$insert_id = $this->omni_sales_model->add_product($data);
					if ($insert_id) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
					if($channel == 'pre_order'){
						redirect(admin_url('omni_sales/'.$channel));            
					}
					else{
						redirect(admin_url('omni_sales/add_product_channel/'.$channel));            
					}
				}           
			}
		}
		/**
		 * delete product
		 * @param  int $channel 
		 * @param  int $id      
		 * @return redirect
		 */
		public function delete_product($channel,$id){
			$response = $this->omni_sales_model->delete_product($id);
			if($response == true){
				set_alert('success', _l('deleted', _l('category')));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			if($channel == 'pre_order'){
				redirect(admin_url('omni_sales/pre_order'));        
			}
			else{
				redirect(admin_url('omni_sales/add_product_channel/'.$channel));        
			}
		}
		/**
		 * order list
		 * @param  int $id
		 * @return view
		 */
		public function order_list($id = ''){
			if (!has_permission('omni_order_list', '', 'view') && !has_permission('omni_order_list', '', 'view_own') && !is_admin()) {
				access_denied('omni_order_list');
			}
			$this->load->model('clients_model');
			$this->load->model('invoices_model');
			$this->load->model('staff_model');
			$data['customers'] = $this->clients_model->get();
			$data['invoices'] = $this->invoices_model->get();
			$data['prefix'] = get_option('invoice_prefix');
			$data['title'] = _l('order_list');
			$data['staff'] = $this->staff_model->get();
			$this->load->view('order_list/order_list', $data);
		}
		/**
		 * order list table
		 * @return table
		 */
		public function order_list_table(){
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->load->model('payment_modes_model');
					$product_filter = $this->input->post('product_filter'); 
					$channel = $this->input->post('channel');
					$customers = $this->input->post('customers');
					$invoices = $this->input->post('invoices');
					$status = $this->input->post('status');
					$order_type = $this->input->post('order_type');

					$end_date = $this->input->post('end_date');
					$start_date = $this->input->post('start_date');
					$seller = $this->input->post('seller');

					$query = '';      

					$select = [
						'id',
						'id',
						'id',
						'id',
						'id',
						'id',
						'id',
						'id',
						'id'          
					];
					$where              = [(($query!='')?$query:'')];
					if(isset($channel) && $channel != ''){
						if($channel == 1 || $channel == 2 || $channel == 4|| $channel == 6){
							array_push($where, ' where channel_id = '.$channel);
						}else{
							array_push($where, ' where channel_id not in (1,2,4,6)');
						}
					}
					if(isset($order_type) && $order_type != ''){
						if(count($where) > 1){
							if($order_type == 'sale_order'){
								array_push($where, ' and original_order_id is null');
							}
							else{
								array_push($where, ' and original_order_id is not null');
							}
						}else{
							if($order_type == 'sale_order'){
								array_push($where, 'where original_order_id is null');
							}
							else{
								array_push($where, 'where original_order_id is not null');
							}
						}
					}
					if(isset($customers) && $customers != ''){
						if(count($where) > 1){
							array_push($where, ' and userid = '.$customers);
						}else{
							array_push($where, ' where userid = '.$customers);
						}
					}

					if(isset($invoices) && $invoices != ''){
						if(count($where) > 1){

							array_push($where, ' and number_invoice = '.$this->omni_sales_model->get_number_invoice($invoices));
						}else{
							array_push($where, ' where number_invoice = '.$this->omni_sales_model->get_number_invoice($invoices));
						}
					}

					if(isset($status) && $status != ''){
						if(count($where) > 1){
							array_push($where, ' and status = '.$status);
						}else{
							array_push($where, ' where status = '.$status);
						}
					}
					if(is_admin() || has_permission('omni_order_list', '', 'view')){
						if(isset($seller) && $seller != ''){
							if(count($where) > 1){
								array_push($where, ' and seller = '.$seller);
							}else{
								array_push($where, ' where seller = '.$seller);
							}
						}
					}
					else{
						if(count($where) > 1){
							array_push($where, ' and seller = '.get_staff_user_id());
						}else{
							array_push($where, ' where seller = '.get_staff_user_id());
						}
					}


					if($end_date!='' && $start_date!=''){
						if(!$this->omni_sales_model->check_format_date($start_date)){
							$start_date = to_sql_date($start_date);
						}else{
							$start_date = $start_date;
						}

						if(!$this->omni_sales_model->check_format_date($end_date)){
							$end_date = to_sql_date($end_date);
						}else{
							$end_date = $end_date;
						}

						if(count($where) > 1){
							array_push($where, ' and date(datecreator) between \''.$start_date.'\' and \''.$end_date.'\'');
						}else{
							array_push($where, ' where date(datecreator) between \''.$start_date.'\' and \''.$end_date.'\'');
						}
					}

					$aColumns     = $select;
					$sIndexColumn = 'id';
					$sTable       = db_prefix() . 'cart';
					$join         = [];
					$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
						'id',
						'name',
						'address',
						'phone_number',
						'voucher',
						'status',
						'datecreator',
						'channel',
						'channel_id',
						'company',
						'number_invoice',
						'invoice',
						'userid',
						'allowed_payment_modes',
						'payment_method_title',
						'original_order_id',
						'order_number'
					]);


					$output  = $result['output'];
					$rResult = $result['rResult'];
					foreach ($rResult as $aRow) {
						if($aRow['number_invoice'] != ''){
							$id = $this->omni_sales_model->get_id_invoice($aRow['number_invoice']);
						}
						$row = [];
						$row[] = $aRow['id'];              
						$row[] = $aRow['order_number'];              
						$row[] = $aRow['datecreator'];              
						$row[] = $aRow['company']; 
						$row[] = (is_numeric($aRow['userid']) ? omni_get_user_group_name($aRow['userid']) : ''); 
						$row[] = (is_numeric($aRow['original_order_id']) ? '<span class="label label-danger">'._l('omni_return_order').'</span>' : '<span class="label label-primary">'._l('omni_sale_order').'</span>');


						$channel = strtoupper(_l('omni_'.$aRow['channel']));
						$payment_mode = '';
						if ($aRow['channel_id'] == 1 || $aRow['channel_id'] == 2 || $aRow['channel_id'] == 4 || $aRow['channel_id'] == 6) {
							$data_multi_payment = $this->omni_sales_model->get_order_multi_payment($aRow['id']);
							if($data_multi_payment){
								foreach ($data_multi_payment as $key => $mtpayment) {
									if($key == 3){
										$payment_mode .= '<span class="label label-primary">...</span>&nbsp;';
										break;
									}
									$payment_mode .= '<span class="label label-primary">'.$mtpayment['payment_name'].'</span>&nbsp;';
								}
							}
							else{
								$data_payment = $this->payment_modes_model->get($aRow['allowed_payment_modes']);
								if($data_payment){
									$name = isset($data_payment->name) ? $data_payment->name : '';
									if($name !=''){
										$payment_mode = '<span class="label label-primary">'.$name.'</span>&nbsp;';              
									}            
								}
							}
						}
						else{
							$this->db->where('id', $aRow['id']);
							$data_payment = $this->db->get(db_prefix().'cart')->row();
							if($data_payment->payment_method_title != null || $data_payment->payment_method_title != ""){
								$payment_mode = '<span class="label label-primary">'.$data_payment->payment_method_title.'</span>&nbsp;';
							}else{
								$payment_mode = "";
							}
						} 

						$row[] = $payment_mode;              
						$row[] = $channel;   
						$status = get_status_by_index($aRow['status']);

						$row[] = '<span class="label label-success">'.$status.'</span>';              

						$row[] = ($aRow['invoice'] != '' ? '<a href="' . admin_url('invoices#'. $id) . '" >'.$aRow['invoice'].'</a>' : '');

						$option = '';

						if (has_permission('omni_order_list', '', 'view') || has_permission('omni_order_list', '', 'view_own') || is_admin()) {
						$option .= '<a href="' . admin_url('omni_sales/view_order_detailt/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('view').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
						$option .= '<i class="fa fa-eye"></i>';
						$option .= '</a>';
						$option .= '<a href="' . admin_url('omni_sales/hash_view_public_order/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('view_public').'" class="btn btn-success btn-icon" data-id="'.$aRow['id'].'" >';
						$option .= '<i class="fa fa-eye"></i>';
						$option .= '</a>';
					}

						if(is_admin() || has_permission('omni_order_list', '', 'edit')){                

							if($aRow['status'] == 0 && $aRow['channel_id'] == 4){
								$option .= '<a href="' . admin_url('omni_sales/order_manual/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('edit').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
								$option .= '<i class="fa fa-pencil"></i>';
								$option .= '</a>';
							}
						}              
						if (is_admin() || has_permission('omni_order_list', '', 'delete')){ 
							$option .= '<a href="' . admin_url('omni_sales/delete_order/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('delete').'" class="btn btn-danger btn-icon _delete">';
							$option .= '<i class="fa fa-remove"></i>';
							$option .= '</a>';  
						}  
						$row[] = $option; 
						$output['aaData'][] = $row;                                      
					}
					echo json_encode($output);
					die();
				}
			}
		}

		/**
		 * get cart data 
		 * @param  int $id 
		 * @return json
		 */
		public function get_cart_data($id = ''){
			$data_cart = $this->omni_sales_model->get_cart($id);
			if($data_cart){
				$data['id_contact'] = $data_cart->id_contact; 
				$data['contact'] = $this->omni_sales_model->get_contact($data['id_contact']);
				$this->load->model('currencies_model');
				$data['base_currency'] = $this->currencies_model->get_base_currency(); 
				$data['cart_detailt'] = $this->omni_sales_model->get_cart_detailt_by_master($id);
				$html = $this->load->view('order_list/cart_detailt',$data, true);
				echo json_encode([
					'data' =>  $html,
					'success' => true
				]); 
			}         
		}
		/**
		 * view order detailt
		 * @param  int $id
		 * @return view
		 */
		public function view_order_detailt($id){
			$data_cart = $this->omni_sales_model->get_cart($id);
			if($data_cart){
				if(is_admin() || $data_cart->seller == get_staff_user_id() || has_permission('omni_order_list', '', 'view')|| has_permission('omni_order_list', '', 'view_own')){
					$data['id'] = $id;
					$this->load->model('currencies_model');
					$this->load->model('warehouse/warehouse_model');
					$data['base_currency'] = $this->currencies_model->get_base_currency();
					if(is_numeric($data_cart->currency) && $data_cart->currency > 0){
						$data['base_currency'] = $this->currencies_model->get($data_cart->currency);
					}
					$data['order'] = $data_cart;
					$data['order_detait'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($id);
					if($data['order']->number_invoice != ''){
						$data['invoice'] = $this->omni_sales_model->get_invoice($data['order']->number_invoice);
					}           
					$this->load->model('staff_model');
					$data['staffs'] = $this->staff_model->get('staff');   
					$data['title'] = $data_cart->name;   
					$data['activity_log'] = [];
					if($this->db->table_exists(db_prefix() . 'wh_goods_delivery_activity_log')){
						$data['activity_log'] = $this->warehouse_model->wh_get_activity_log($data['order']->stock_export_number,'omni_order');						
					}
					$data['warehouses'] = $this->omni_sales_model->omnisales_get_warehouse();
					$data['tax_data'] = $this->omni_sales_model->get_html_tax_manual_order($id);

					//check delivery note exist
					$goods_delivery_exist = false;
					if(is_numeric($data['order']->stock_export_number)){
						$get_goods_delivery = $this->warehouse_model->get_goods_delivery($data['order']->stock_export_number);
						if($get_goods_delivery){
							$goods_delivery_exist = true;
						}
					}

					$data['goods_delivery_exist'] = $goods_delivery_exist;

					$this->load->view('order_list/cart_detailt', $data); 
				} 
				else{
					access_denied('order');
				}
			} 
		}
		/**
		 * add_woocommerce_store 
		 * @return view
		 */
		public function add_woocommerce_store(){
			$data['title'] = _l('channel_woocommerce');
			$this->load->view('sales_channel/manage_channel_woocommerce', $data);
		}
		/**
		 * add channel woocommerce
		 * @return redirect
		 */
		public function add_channel_woocommerce(){
			if($this->input->post()){
				$data = $this->input->post();
				if($data['id'] == ''){
					$insert_id = $this->omni_sales_model->add_channel_woocommerce($data);
					if ($insert_id) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
				}else{
					$id = $data['id'];
					unset($data['id']);
					$success = $this->omni_sales_model->update_channel_woocommerce($data, $id);
					if ($success) {
						$message = _l('updated_successfully');
						set_alert('success', $message);
					}
				}
				redirect(admin_url('omni_sales/add_woocommerce_store/'));
			}
		}
		/**
		 * table channel woocommerce
		 * @return table
		 */
		public function table_channel_woocommerce(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_channel_woocommerce'));
		}
		/**
		 * detail channel wcm 
		 * @param  int $id
		 * @return view
		 */
		public function detail_channel_wcm($id){
			$data['group'] = $this->input->get('group');

			$data['title'] = _l('detail_channel_wcm');

			$data['tab'][] = 'product';

			if($data['group'] == ''){
				$data['group'] = 'product';
			}

			$data['tabs']['view'] = 'includes/'.$data['group'];
			$data['id'] = $id;
			$data['group_product'] = $this->omni_sales_model->get_group_product();
			$store_choose = $this->omni_sales_model->get_ids_woocommere_store_detailt($id);
			$data['omni_warehouse'] = $this->omni_sales_model->omnisales_get_warehouse();
			$data['products'] = [];
			$products = $this->omni_sales_model->get_product_parent_id();
			if(count($products) > 0){
				foreach ($products as $key => $value) {
					if(!in_array($value['id'], $store_choose)){
						array_push($data['products'], $value);
					}
				}
			}
			$data['status'] = get_option('status_sync');
			$this->load->view('sales_channel/detail_channel_woocommerce', $data);
		}
		/**
		 * delete channel wcm
		 * @param  int $id
		 * @return  redirect
		 */
		public function delete_channel_wcm($id){
			$response = $this->omni_sales_model->delete_channel_woocommerce($id);
			if($response == true){
				set_alert('success', _l('deleted'));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			redirect(admin_url('omni_sales/add_woocommerce_store/'));
		}
		/**
		 * table product woocommerce
		 * @return table
		 */
		public function table_product_woocommerce(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_product_woocommerce'));
		}
		/**
		 * add product channel wcm
		 * @return redirect
		 */
		public function add_product_channel_wcm(){
			if($this->input->post()){
				$data = $this->input->post();
				if($data['woocommere_store_id'] != ''){
					$channel = $data['woocommere_store_id'];
					$insert_id = $this->omni_sales_model->add_product_channel_wcm($data);
					if ($insert_id) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
					redirect(admin_url('omni_sales/detail_channel_wcm/'.$data['woocommere_store_id'].'?group=product'));
				}           
			}
		}
		/**
		 * register
		 * @return view
		 */
		public function register()
		{
			if (get_option('allow_registration') != 1 || is_client_logged_in()) {
				redirect(site_url());
			}

			if (get_option('company_is_required') == 1) {
				$this->form_validation->set_rules('company', _l('client_company'), 'required');
			}

			if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions') == 1) {
				$this->form_validation->set_rules(
					'accept_terms_and_conditions',
					_l('terms_and_conditions'),
					'required',
					['required' => _l('terms_and_conditions_validation')]
				);
			}

			$this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
			$this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');
			$this->form_validation->set_rules('email', _l('client_email'), 'trim|required|is_unique[' . db_prefix() . 'contacts.email]|valid_email');
			$this->form_validation->set_rules('password', _l('clients_register_password'), 'required');
			$this->form_validation->set_rules('passwordr', _l('clients_register_password_repeat'), 'required|matches[password]');

			if (get_option('use_recaptcha_customers_area') == 1
				&& get_option('recaptcha_secret_key') != ''
				&& get_option('recaptcha_site_key') != '') {
				$this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
		}

		$custom_fields = get_custom_fields('customers', [
			'show_on_client_portal' => 1,
			'required'              => 1,
		]);

		$custom_fields_contacts = get_custom_fields('contacts', [
			'show_on_client_portal' => 1,
			'required'              => 1,
		]);

		foreach ($custom_fields as $field) {
			$field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
			if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
				$field_name .= '[]';
			}
			$this->form_validation->set_rules($field_name, $field['name'], 'required');
		}
		foreach ($custom_fields_contacts as $field) {
			$field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
			if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
				$field_name .= '[]';
			}
			$this->form_validation->set_rules($field_name, $field['name'], 'required');
		}
		if ($this->input->post()) {
			if ($this->form_validation->run() !== false) {
				$data = $this->input->post();

				define('CONTACT_REGISTERING', true);

				$clientid = $this->clients_model->add([
					'billing_street'      => $data['address'],
					'billing_city'        => $data['city'],
					'billing_state'       => $data['state'],
					'billing_zip'         => $data['zip'],
					'billing_country'     => is_numeric($data['country']) ? $data['country'] : 0,
					'firstname'           => $data['firstname'],
					'lastname'            => $data['lastname'],
					'email'               => $data['email'],
					'contact_phonenumber' => $data['contact_phonenumber'] ,
					'website'             => $data['website'],
					'title'               => $data['title'],
					'password'            => $data['passwordr'],
					'company'             => $data['company'],
					'vat'                 => isset($data['vat']) ? $data['vat'] : '',
					'phonenumber'         => $data['phonenumber'],
					'country'             => $data['country'],
					'city'                => $data['city'],
					'address'             => $data['address'],
					'zip'                 => $data['zip'],
					'state'               => $data['state'],
					'custom_fields'       => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
				], true);

				if ($clientid) {
					hooks()->do_action('after_client_register', $clientid);

					if (get_option('customers_register_require_confirmation') == '1') {
						send_customer_registered_email_to_administrators($clientid);

						$this->clients_model->require_confirmation($clientid);
						set_alert('success', _l('customer_register_account_confirmation_approval_notice'));
						redirect(site_url('authentication/login'));
					}

					$this->load->model('authentication_model');

					$logged_in = $this->authentication_model->login(
						$this->input->post('email'),
						$this->input->post('password', false),
						false,
						false
					);

					$redUrl = site_url();

					if ($logged_in) {
						hooks()->do_action('after_client_register_logged_in', $clientid);
						set_alert('success', _l('clients_successfully_registered'));
					} else {
						set_alert('warning', _l('clients_account_created_but_not_logged_in'));
						$redUrl = site_url('authentication/login');
					}

					send_customer_registered_email_to_administrators($clientid);
					redirect($redUrl);
				}
			}
		}

		$data['title']     = _l('clients_register_heading');
		$data['bodyclass'] = 'register';
		$this->data($data);
		$this->view('register');
		$this->layout();
	}
		/**
		 * sync products to store
		 * @param  int $store_id
		 * @return json
		 */
		public function sync_products_to_store($store_id){
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_from_the_system_to_the_store_single($store_id);
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		/**
		 * process orders woo
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_orders_woo($store_id){
			update_option('status_sync', 1);
			$result = $this->omni_sales_model->process_orders_woo($store_id);
			update_option('status_sync', 2);
			echo json_encode($result);
		}
		/**
		 * get total order
		 * @param  int  $id
		 * @param  boolean $voucher 
		 * @return array
		 */
		public function get_total_order($id ='',$voucher = false){        
			$data_detailt = $this->get_cart_detailt_by_master($id);
			$total = 0;
			foreach ($data_detailt as $key => $value) {
				$total += $value['quantity'] * $value['prices'];
			}
			return ['total' => $total,'sub_total' => $total,'discount' => '0'];
		}
		/**
		 * add invoice and out of stock
		 * @param int $orderid
		 * @return redirect
		 */
		public function create_invoice_detail_order($orderid){
			$success = $this->omni_sales_model->create_invoice_detail_order($orderid);
			if ($success) {
				$message = _l('added_successfully');
				set_alert('success', $message);
			}
			redirect(admin_url('omni_sales/view_order_detailt/'.$orderid));
		}

		/**
		 * { admin change status }
		 *
		 * @param  $order_number  The order number
		 * @return json
		 */
		public function admin_change_status($order_number){
			if($this->input->post()){
				$data = $this->input->post();
				$message = '';
				$insert_id = $this->omni_sales_model->change_status_order($data,$order_number,1);
				if ($insert_id) {
					echo json_encode([
						'message' => $message,
						'success' => true
					]);
					die;
				}               
			}
		}
		/**
		 *  process inventory synchronization
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_inventory_synchronization(){
			update_option('email_signature', 1);
			$store_id = $this->input->post('store_id'); 
			$result = $this->omni_sales_model->process_inventory_synchronization($store_id);
			echo json_encode($result);
		}
		/* Add new invoice or update existing */
		/**
		 * invoice
		 * @param  int $id
		 * @return view    
		 */
		public function invoice($id = '')
		{
			$this->load->model('invoices_model');
			if ($this->input->post()) {
				$invoice_data = $this->input->post();
				if ($id != '') {
					if (!has_permission('invoices', '', 'create')) {
						access_denied('invoices');
					}
					$newitems = [];     
					foreach ($invoice_data['items'] as $key => $value) {
						array_push($newitems, array('order' => $key, 'description' => $value['description'], 'long_description' => $value['long_description'], 'qty' => $value['qty'], 'unit' => $value['unit'], 'rate'=> $value['rate']));
					}
					$invoice_data['newitems'] = $newitems;
					unset($invoice_data['items']);
					$id_invoice = $this->invoices_model->add($invoice_data);
					if ($id_invoice) {
						set_alert('success', _l('create_successfully', _l('invoice')));

						if (isset($invoice_data['save_and_record_payment'])) {
							$this->session->set_userdata('record_payment', true);
						}                    
						redirect(admin_url('omni_sales/view_order_detailt/'.$id));
					}
					die;
				}
			}

			if($id!=''){
				$data_order = $this->omni_sales_model->get_cart($id);
				$data_order_detailt = $this->omni_sales_model->get_cart_detailt_by_master($id);
				$list_item = [];
				$total = 0;

				foreach ($data_order_detailt as $key => $value) {
					$data_product = $this->omni_sales_model->get_product($value['product_id']);
					$long_description = '';
					$unit = '';
					if($data_product){
						$long_description = $data_product->long_description;
						$data_unit = $this->omni_sales_model->get_unit($data_product->unit_id);
						if($data_unit){
							$unit = $data_unit->unit_name;
						}
					}

					array_push($list_item, array(
						"id"=> '',
						"rel_id"=> $value['id'],
						"rel_type"=> 'invoice',
						"description"=> $value['product_name'],
						"long_description"=> $long_description,
						"qty"=> $value['quantity'],
						"rate"=> $value['prices'],
						"unit"=> $unit,
						"taxname"=> 0,
						"item_order"=> $key,
					));
					$total+= $value['quantity'] * $value['prices'];
				}

				$data["invoices_to_merge"] = [];

				$client_std = new stdClass; 
				$client_std->userid = $data_order->userid;
				$client_std->company = $data_order->company;
				$client_std->vat =0;
				$client_std->phonenumber = $data_order->phonenumber;
				$client_std->country = $data_order->country;
				$client_std->city = $data_order->city;
				$client_std->zip = $data_order->zip;
				$client_std->state = $data_order->state;
				$client_std->address = $data_order->address;
				$client_std->website = '';
				$client_std->datecreated = $data_order->datecreator;
				$client_std->active = 1;
				$client_std->leadid = 0;
				$client_std->billing_street = $data_order->billing_street;
				$client_std->billing_city = $data_order->billing_city;
				$client_std->billing_state = $data_order->billing_state;
				$client_std->billing_zip = $data_order->billing_zip;
				$client_std->billing_country = $data_order->billing_country;
				$client_std->shipping_street = $data_order->shipping_street;
				$client_std->shipping_city = $data_order->shipping_city;
				$client_std->shipping_state = $data_order->shipping_state;
				$client_std->shipping_zip = $data_order->shipping_zip;
				$client_std->shipping_country = $data_order->shipping_country;
				$client_std->longitude = '';
				$client_std->latitude ='';
				$client_std->default_language ='';
				$client_std->default_currency = '';
				$client_std->show_primary_contact = '';
				$client_std->stripe_id = '';
				$client_std->registration_confirmed = '';
				$client_std->addedfrom = get_staff_user_id();


				$data_object = new stdClass; 
				$data_object->id = '';
				$data_object->sent = '0';
				$data_object->datesend = date('Y-m-d');
				$data_object->clientid = $data_order->userid;
				$data_object->deleted_customer_name = '';
				$data_object->number = $data_order->order_number;
				$data_object->prefix = '';
				$data_object->number_format = '';
				$data_object->datecreated = date('Y-m-d');
				$data_object->date = date('Y-m-d');
				$data_object->duedate = date('Y-m-d');
				$data_object->currencies = 1;
				$data_object->subtotal = 1223;
				$data_object->total_tax = 1;
				$data_object->total = 1234;
				$data_object->adjustment = '';
				$data_object->addedfrom = get_staff_user_id();
				$data_object->hash = '';
				$data_object->status = 1;
				$data_object->clientnote = '';
				$data_object->adminnote = '';
				$data_object->last_overdue_reminder = '';
				$data_object->cancel_overdue_reminders = '';
				$data_object->allowed_payment_modes = '';
				$data_object->token = '';
				$data_object->discount_percent = '';
				$data_object->discount_total = '';
				$data_object->discount_type = '';
				$data_object->recurring = '';
				$data_object->recurring_type = '';
				$data_object->custom_recurring = '';
				$data_object->cycles = '';
				$data_object->total_cycles = '';
				$data_object->is_recurring_from = '';
				$data_object->last_recurring_date = '';
				$data_object->terms = '';
				$data_object->sale_agent = get_staff_user_id();
				$data_object->billing_street = $data_order->billing_street;
				$data_object->billing_city = $data_order->billing_city;
				$data_object->billing_state = $data_order->billing_state;
				$data_object->billing_zip = $data_order->billing_zip;
				$data_object->billing_country = $data_order->billing_country;
				$data_object->shipping_street = $data_order->shipping_street;
				$data_object->shipping_city = $data_order->shipping_city;
				$data_object->shipping_state = $data_order->shipping_state;
				$data_object->shipping_zip = $data_order->shipping_zip;
				$data_object->shipping_country = $data_order->userid;
				$data_object->include_shipping = '';
				$data_object->show_shipping_on_invoice = '';
				$data_object->show_quantity_as = '';
				$data_object->project_id = '';
				$data_object->subscription_id = '';
				$data_object->percent_company = '';
				$data_object->percent_company_total = '';
				$data_object->percent_patient = '';
				$data_object->company_select = '';
				$data_object->percent_patient_total = '';
				$data_object->symbol = '';
				$data_object->name = '';
				$data_object->decimal_separator = '';
				$data_object->thousand_separator = '';
				$data_object->placement = '';
				$data_object->isdefault = '';
				$data_object->currencyid = '';
				$data_object->currency_name = '';
				$data_object->total_left_to_pay = $total;
				$data_object->items =$list_item;
				$data_object->attachments = [];
				$data_object->visible_attachments_to_customer_found = false;
				$data_object->client = $client_std;
				$data_object->payments =  [];
				$data_object->currency =  '1';
				$data['invoice'] = $data_object;
				array_push($data["invoices_to_merge"],$data_object);
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
			$this->load->model('invoice_items_model');

			$data['ajaxItems'] = false;
			if (total_rows(db_prefix().'items') <= ajax_on_total_items()) {
				$data['items'] = $this->invoice_items_model->get_grouped();
			} else {
				$data['items']     = [];
				$data['ajaxItems'] = true;
			}
			$data['items_groups'] = $this->invoice_items_model->get_groups();

			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();

			$data['base_currency'] = $this->currencies_model->get_base_currency();

			$data['staff']     = $this->staff_model->get('', ['active' => 1]);
			$data['title'] = _l('create_invoice');
			$data['bodyclass'] = 'invoice';


			$this->load->view('omni_sales/invoice/invoice', $data);
		}
		/**
		 * trade discount
		 * @return view
		 */
		public function trade_discount(){
			$data['title'] = _l('trade_discount_title');
			$this->load->view('trade_discount/manage', $data);
		}
		/**
		 * table trade discount
		 * @return table
		 */
		public function table_trade_discount(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_trade_discount'));
		}
		/**
		 * new trade discount
		 * @param  int $id 
		 * @return view     
		 */
		public function new_trade_discount($id = ''){
			$this->load->model('client_groups_model');
			$this->load->model('clients_model');
			$this->load->model('warehouse/warehouse_model');
			$data['title'] = _l('new_trade_discount');
			$data['group_clients'] = $this->client_groups_model->get_groups();
			$data['clients'] = $this->clients_model->get();
			$data['group_items'] = $this->warehouse_model->get_commodity_group_type();
			$data['items'] = $this->warehouse_model->get_commodity_code_name();

			if($id != ''){
				$data['title'] = _l('update_trade_discount');
				$data['stores'] = $this->omni_sales_model->get_woocommere_store();
				$data['id'] = $id;
				$data['discount'] = $this->omni_sales_model->get_discount($id);
			}
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('trade_discount/new_trade_discount', $data);
		}
		/**
		 * add discount form
		 * @return redirect
		 */
		public function add_discount_form(){
			$data = $this->input->post();
			if($data['id'] == ''){
				$success = $this->omni_sales_model->add_discount_form($data);
				if ($success) {
					$message = _l('added_successfully');
					set_alert('success', $message);
				}
			}else{
				$id = $data['id'];
				unset($data['id']);
				$success = $this->omni_sales_model->update_discount_form($data, $id);
				if ($success) {
					$message = _l('updated_successfully');
					set_alert('success', $message);
				}
			}
			redirect(admin_url('omni_sales/trade_discount'));
		}
		/**
		 * delete trade discount
		 * @param  int $id 
		 * @return  redirect
		 */
		public function delete_trade_discount($id){
			$delete_success = $this->omni_sales_model->delete_trade_discount($id);
			if($delete_success == true){
				set_alert('success', _l('deleted', _l('trade_discount')));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			redirect(admin_url('omni_sales/trade_discount'));
		}
		/**
		 * get product by group pos channel
		 * @param  int $page 
		 * @param  int $id   
		 * @param  string $key  
		 * @return json
		 */
		public function get_product_by_group_pos_channel($page = '', $id = '',$warehouse = '',$key=''){ 
			$data['ofset'] = 48;     
			$channel = 1;     
			$date = date('Y-m-d');
			$data_product = $this->omni_sales_model->get_list_product_by_group($channel,$id,$warehouse,$key,($page-1)*$data['ofset'],$data['ofset']);
			$data['product'] = [];
			foreach ($data_product['list_product'] as $item) {
				$discount_percent = 0;
				$total_tax = 0;
				$temp = 0;

				$data_discount = $this->omni_sales_model->check_discount($item['id'], $date, 1);
				if($data_discount){
					$discount_percent = $data_discount->discount;
				}
				$price = 0;
				$data_prices = $this->omni_sales_model->get_price_channel($item['id'],$channel);
				if($data_prices){
					$price = $data_prices->prices;
				}
				$tax_name = '';
				$percent_tax = 0;
				$total_tax = 0;
				$tax_info = $this->omni_sales_model->get_tax($item['tax']);
				if($tax_info){
					$tax_name = $tax_info->name.' ('.$tax_info->taxrate.'%)';
					$total_tax = ($tax_info->taxrate * $price)/100;
					$percent_tax = $tax_info->taxrate;
				}
				$inventory_number = 0;
				$data_inventory = $this->omni_sales_model->get_quantity_inventory($item['id'], $warehouse);
				if($data_inventory){
					$inventory_number = $data_inventory->inventory_number;
				}
				array_push($data['product'], array(
					'id' => $item['id'],
					'name' => $item['description'],
					'sku_code' => $item['sku_code'],
					'price' => $price,
					'w_quantity' => $inventory_number,
					'without_checking_warehouse' => $item['without_checking_warehouse'],
					'discount_percent' => $discount_percent,
					'price_discount' => $this->get_price_discount($price, $discount_percent),
					'tax' => $item['tax'],
					'tax_name' => $tax_name,
					'percent_tax' => $percent_tax,
					'total_tax' => $total_tax,
					'group_id' => $item['group_id'],
					'commodity_barcode' => $item['commodity_barcode'],
				));
			}      

			$data['group_id'] = $id;
			$data['page'] = $page;
			$data['ofset_count'] = $data_product['count'];
			$data['total_page'] = ceil($data['ofset_count']/$data['ofset']);

			$data['title_group'] = '';
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();

			$html = $this->load->view('pos/list_product_with_page',$data,true);

			echo json_encode([
				'data'=>$html
			]);
			die;
		}
		/**
		 * get stock
		 * @param  int $product_id 
		 * @return $w_qty         
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
		 * new voucher
		 * @param  int $id
		 * @return view
		 */
		public function new_voucher($id = ''){
			$this->load->model('client_groups_model');
			$this->load->model('clients_model');
			$this->load->model('warehouse/warehouse_model');
			$data['title'] = _l('new_voucher');
			$data['group_clients'] = $this->client_groups_model->get_groups();
			$data['clients'] = $this->clients_model->get();
			$data['group_items'] = $this->warehouse_model->get_commodity_group_type();
			$data['items'] = $this->warehouse_model->get_commodity_code_name();
			if($id != ''){
				$data['id'] = $id;
				$data['discount'] = $this->omni_sales_model->get_discount($id);
				$data['title'] = _l('update_voucher');

			}
			$this->load->model('currencies_model');
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('trade_discount/voucher', $data);
		}  
		/**
		 * table voucher
		 * @return table
		 */
		public function table_voucher(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_voucher'));
		}
		/**
		 *  create invoice pos 
		 * @param  int $client 
		 * @return json         
		 */
		public function create_invoice_pos($client =''){ 
			$data = $this->input->post();
			$data['userid'] = $client;
			$data['discount'] =  '';
			$data['discount_type'] =  '';
			if($client != ''){
				$success = false;
				$id_inv = $this->omni_sales_model->check_out_pos($data);
				$id = '';
				$stock_export_number = '';
				$number_invoice = '';
				$payment = '';
				$html_bill = '';
				$insert_id = '';
				$warehouse_id = '';
				if($id_inv){
					$number_invoice = $id_inv['number_invoice'];
					$html_bill = $id_inv['html_bill'];
					$stock_export_number = $id_inv['stock_export_number'];
					$id = $id_inv['id_invoice'];
					$payment = $id_inv['payment'];
					$insert_id = $id_inv['insert_id'];
					$success = true;
					$warehouse_id = $id_inv['warehouse_id'];
				}
				echo json_encode([
					'success'=>$success,
					'id'=>$id,
					'warehouse_id'=>$warehouse_id,
					'payment'=>$payment,
					'stock_export_number'=>$stock_export_number,
					'number_invoice'=>$number_invoice,
					'html_bill' => $html_bill,
					'insert_id' => $insert_id
				]);
				die;
			}
		}
	/**
	 * table log discount
	 * @return table
	 */
	public function table_log_discount(){
		$this->app->get_table_data(module_views_path('omni_sales', 'table/table_log_discount'));
	}
		/**
		 * create export stock
		 * @param int $orderid
		 * @return redirect
		 */
		public function create_export_stock($orderid){
			$success = $this->omni_sales_model->create_export_stock($orderid, 2);
			if ($success) {
				$message = _l('create_successfully');
				set_alert('success', $message);
			}
			redirect(admin_url('omni_sales/view_order_detailt/'.$orderid));
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
		 * setting sync management
		 * @return view
		 */
		public function setting(){
			$data['tab'] = $this->input->get('tab');
			$data['items'] = $this->input->get('items');

			if($data['tab'] == ''){
				$data['tab'] = 'automatic_sync_config';
			}
			if($data['tab'] == 'order_notificaiton'){
				$data['tab'] = 'notification_recipient';
			}

			if($data['tab'] == 'automatic_sync_config'){
				if($data['items'] == ''){
					$data['items'] = 'crm_to_woocommerce_store';
				}
				switch ($data['items']) {
					case 'crm_to_woocommerce_store':
					$data['items'] = 'crm_to_woocommerce_store';
					break;
					case 'woocommerce_store_to_crm':
					$data['items'] = 'woocommerce_store_to_crm';
					break;

					default:
					$data['items'] = 'crm_to_woocommerce_store';
					break;
				}
			}
			$data['staff'] = [];
			$staff_sync_orders = get_option('staff_sync_orders');
			if($staff_sync_orders){
				$data['staff'] = explode(',',$staff_sync_orders);
			}
			$data['time_minute'] = get_option('minute_sync');
			$data['minute'] = get_option('minute_sync_orders');
			$data['sync_omni_sales_products'] = get_option('sync_omni_sales_products');
			$data['sync_omni_sales_orders'] = get_option('sync_omni_sales_orders');
			$data['sync_omni_sales_inventorys'] = get_option('sync_omni_sales_inventorys');
			$data['sync_omni_sales_description'] = get_option('sync_omni_sales_description');
			$data['sync_omni_sales_images'] = get_option('sync_omni_sales_images');
			$data['price_crm_woo'] = get_option('price_crm_woo');
			$data['product_info_enable_disable'] = get_option('product_info_enable_disable');
			$data['product_info_image_enable_disable'] = get_option('product_info_image_enable_disable');

			$data['minute_sync_product_info_time1'] = get_option('minute_sync_product_info_time1');
			$data['minute_sync_inventory_info_time2'] = get_option('minute_sync_inventory_info_time2');
			$data['minute_sync_price_time3'] = get_option('minute_sync_price_time3');
			$data['minute_sync_decriptions_time4'] = get_option('minute_sync_decriptions_time4');
			$data['minute_sync_images_time5'] = get_option('minute_sync_images_time5');
			$data['minute_sync_product_info_time7'] = get_option('minute_sync_product_info_time7');
			$data['minute_sync_product_info_images_time8'] = get_option('minute_sync_product_info_images_time8');
			
			$data['number_of_days_to_save_diary_sync'] = get_option('number_of_days_to_save_diary_sync');

			$data['invoice_sync_configuration'] = get_option('invoice_sync_configuration');

			$data['store'] = $this->omni_sales_model->get_woocommere_store();

			$this->load->model('staff_model');
			$data['staffs'] = $this->staff_model->get('staff');

			$data['title'] = _l($data['tab']);
			$this->load->view('setting/setting', $data);
		}
		/**
		 * setting sync
		 * @return view
		 */
		public function save_setting($type){
			$data =  $this->input->post();
			switch ($type) {
				case 'sync_orders':
				update_option('minute_sync_orders',$data['minute']);
				redirect(admin_url('omni_sales/setting?tab='.$type));
				break;
				case 'sync_inventory':
				update_option('minute_sync',$data['minute']);
				redirect(admin_url('omni_sales/setting?tab='.$type));
				break;
				case 'notification_recipient':
				if (is_array($data['staff'])) {
					update_option('staff_sync_orders', implode(',', $data['staff']));
				}else{
					update_option('staff_sync_orders', '');
				}

				if (isset($data['omni_sales_invoice_setting']) && $data['omni_sales_invoice_setting']) {
					update_option('invoice_sync_configuration', $data['omni_sales_invoice_setting']);
				}else{
					update_option('invoice_sync_configuration', 0);
				}
				if ($data['omni_allow_showing_shipment_in_public_link']) {
					update_option('omni_allow_showing_shipment_in_public_link', $data['omni_allow_showing_shipment_in_public_link']);
				}else{
					update_option('omni_allow_showing_shipment_in_public_link', 0);
				}
				// Order Return
				if ($data['omni_return_order_prefix']) {
					update_option('omni_return_order_prefix', $data['omni_return_order_prefix']);
				}else{
					update_option('omni_return_order_prefix', '');
				}

				if ($data['omni_return_request_within_x_day']) {
					update_option('omni_return_request_within_x_day', $data['omni_return_request_within_x_day']);
				}else{
					update_option('omni_return_request_within_x_day', 0);
				}

				if ($data['omni_fee_for_return_order']) {
					update_option('omni_fee_for_return_order', $data['omni_fee_for_return_order']);
				}else{
					update_option('omni_fee_for_return_order', 0);
				}

				if (isset($data['omni_refund_loyaty_point'])) {
					update_option('omni_refund_loyaty_point', $data['omni_refund_loyaty_point']);
				}else{
					update_option('omni_refund_loyaty_point', 0);
				}

				if ($data['omni_return_policies_information']) {
					update_option('omni_return_policies_information', $data['omni_return_policies_information']);
				}else{
					update_option('omni_return_policies_information', '');
				}

				if (is_array($data['omni_order_statuses_are_allowed_to_sync'])) {
					update_option('omni_order_statuses_are_allowed_to_sync', implode(',', $data['omni_order_statuses_are_allowed_to_sync']));
				}else{
					update_option('omni_order_statuses_are_allowed_to_sync', '');
				}

				if (isset($data['omni_pos_shipping_fee'])) {
					update_option('omni_pos_shipping_fee', $data['omni_pos_shipping_fee']);
				}else{
					update_option('omni_pos_shipping_fee', 0);
				}

				if ($data['omni_portal_shipping_fee']) {
					update_option('omni_portal_shipping_fee', $data['omni_portal_shipping_fee']);
				}else{
					update_option('omni_portal_shipping_fee', 0);
				}

				if ($data['omni_manual_shipping_fee']) {
					update_option('omni_manual_shipping_fee', $data['omni_manual_shipping_fee']);
				}else{
					update_option('omni_manual_shipping_fee', 0);
				}

				if (isset($data['omni_display_shopping_cart'])) {
					update_option('omni_display_shopping_cart', $data['omni_display_shopping_cart']);
				}else{
					update_option('omni_display_shopping_cart', 0);
				}

				redirect(admin_url('omni_sales/setting?tab='.$type));
				break;
				case 'automatic_sync_config':
				if($data['sync_omni_sales_products']){
					update_option('sync_omni_sales_products', 1);
				}else{
					update_option('sync_omni_sales_products', 0);
				}
				if($data['sync_omni_sales_inventorys']){
					update_option('sync_omni_sales_inventorys', 1);
				}else{
					update_option('sync_omni_sales_inventorys', 0);
				}   
				if($data['sync_omni_sales_description']){
					update_option('sync_omni_sales_description', 1);
				}else{
					update_option('sync_omni_sales_description', 0);
				}   
				if($data['sync_omni_sales_images']){
					update_option('sync_omni_sales_images', 1);
				}else{
					update_option('sync_omni_sales_images', 0);
				}   
				if($data['price_crm_woo']){
					update_option('price_crm_woo', 1);
				}else{
					update_option('price_crm_woo', 0);
				}
				if($data['time1']){
					update_option('minute_sync_product_info_time1', $data['time1']);
				}
				if($data['time2']){
					update_option('minute_sync_inventory_info_time2', $data['time2']);
				}
				if($data['time3']){
					update_option('minute_sync_price_time3', $data['time3']);
				}
				if($data['time4']){
					update_option('minute_sync_decriptions_time4', $data['time4']);
				}
				if($data['time5']){
					update_option('minute_sync_images_time5', $data['time5']);
				}

				if($data['time6']){
					update_option('minute_sync_orders', $data['time6']);
				}
				if($data['time7']){
					update_option('minute_sync_product_info_time7', $data['time7']);
				}
				if($data['time8']){
					update_option('minute_sync_product_info_images_time8', $data['time8']);
				}
				if($data['sync_omni_sales_orders']){
					update_option('sync_omni_sales_orders', 1);
				}else{
					update_option('sync_omni_sales_orders', 0);
				}   
				if($data['product_info_enable_disable']){
					update_option('product_info_enable_disable', 1);
				}else{
					update_option('product_info_enable_disable', 0);
				}
				if($data['product_info_image_enable_disable']){
					update_option('product_info_image_enable_disable', 1);
				}else{
					update_option('product_info_image_enable_disable', 0);
				}
				redirect(admin_url('omni_sales/setting?tab=automatic_sync_config'));
				break;  
				case 'default_setting':
				if($data['omni_show_products_by_department']){
					update_option('omni_show_products_by_department', 1);
				}else{
					update_option('omni_show_products_by_department', 0);
				}  
				$data['bill_header_pos'] = $this->input->post('bill_header_pos', false);
				if(isset($data['bill_header_pos'])){
					update_option('bill_header_pos', $data['bill_header_pos']);
				}
				$data['bill_footer_pos'] = $this->input->post('bill_footer_pos', false);
				if(isset($data['bill_footer_pos'])){
					update_option('bill_footer_pos', $data['bill_footer_pos']);
				} 
				redirect(admin_url('omni_sales/setting?tab=default_setting'));
				break;
				default:
				break;
			}
		}
		/**
		 * total_quantity_sold
		 * @return json 
		 */
		public function total_quantity_sold_table(){
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$select = [
						'id',           
						'id',           
						'id',           
						'id',           
						'id',           
						'id',           
					];
					$where              = [];
					$aColumns     = $select;
					$sIndexColumn = 'id';
					$sTable       = db_prefix() . 'emr_medical_visit';
					$join         = [];
					$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [ 
						'datecreator', 'id','status'               
					]);

					$output  = $result['output'];
					$rResult = $result['rResult'];
					foreach ($rResult as $aRow) {
						$row = [];
						$row[] = $aRow['id']; 

						if($aRow['name_medical_visit']){
							$data_r = '<a href="'.admin_url('emr/add_medical/'.$aRow['id']).'">'.$aRow['name_medical_visit'].'</a>';

							$data_r .= '<div class="row-options">
							<a href="' . admin_url('emr/add_medical/' . $aRow['id'] ).'" >' . _l('view') . '</a>';
							if(is_admin() || has_permission('medical_visit', '', 'edit')){
								$data_r .= ' | <a href="#" data-id="'.$aRow['id'].'" onclick="edit(this); return false;" data-name="'.$aRow['name_medical_visit'].'" >' ._l('edit') . '</a> ';   
							}   

							if(is_admin() || has_permission('medical_visit', '', 'delete')){
								$data_r .= '| <a href="' . admin_url('emr/delete_medical_visit/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
							}

							$row[] = $data_r;
						}

						$row[] = $this->emr_model->get_client($aRow['patients_id'])->company; 
						$row[] = _d($aRow['registration_date']); 
						$row[] = $aRow['datecreator']; 

						if($aRow['status'] == 0){
							$row[] = '<span class="label label-primary  s-status status-'.$aRow['status'].'">NEW</span>';
						}else if($aRow['status'] == 1){
							$row[] = '<span class="label label-warning  s-status status-'.$aRow['status'].'">TRACKING DISEASE</span>';
						}else if($aRow['status'] == 2){
							$row[] = '<span class="label label-success  s-status status-'.$aRow['status'].'">HEALED</span>';
						}else{
							$row[] = '<span class="label label-danger  s-status status-'.$aRow['status'].'">CLOSED</span>';
						}
						$row[] = $aRow['status']; 
						$output['aaData'][] = $row;                                      
					}

					echo json_encode($output);
				}
			}
		}
		/**
		 * total statitic
		 * @return json 
		 */
		public function total_statitic(){
			if($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				$report_from = $this->input->post('report_from');
				$by_year = $this->input->post('by_year');
				$by_month = $this->input->post('by_month');
				$group = $this->input->post('by_group');
				$product = $this->input->post('by_product');
				$channel = $this->input->post('by_channel');
				$report_from = $this->input->post('report_from');
				$report_to = $this->input->post('report_to');
				$type = $this->input->post('type');


				$channel_s = array('POS' => 1, 'PORTAL' => 2, 'WOOCOMMERCE' => 3);
				$list_channel = [];
				$chann_s = [];
				if($channel){
					$list_temp = [];

					foreach ($channel_s as $kk => $ch) {
						foreach ($channel as $kkk => $ch_s) {
							if($ch_s == $ch){
								$list_temp[$kk] = $ch;
							}
						}
					}
					$list_channel = $list_temp;
				}
				else{
					$list_channel = $channel_s;
				}

				if($type == 0){
					$category = array('Su', 'Mon', 'Tu', 'We', 'Th', 'Fr', 'Sa');
					$day_list = []; 
					$day_list[] = date('d', strtotime("Sunday last week")); 
					$day_list[] = date('d', strtotime("Monday this week")); 
					$day_list[] = date('d', strtotime("Tuesday this week")); 
					$day_list[] = date('d', strtotime("Wednesday this week")); 
					$day_list[] = date('d', strtotime("Thursday this week")); 
					$day_list[] = date('d', strtotime("Friday this week")); 
					$day_list[] = date('d', strtotime("Saturday this week")); 

					$first = date('d', strtotime("Sunday last week"));
					$end = date('d', strtotime("Saturday this week"));
					$start_date = date('Y-m-d', strtotime("Sunday last week")).' 00:00:00';
					$end_date = date('Y-m-d', strtotime("Saturday this week")).' 23:59:59';

					$new_array = [];
					foreach ($list_channel as $key => $value) {
						$list_value = [];
						$data_count = $this->omni_sales_model->get_data_by_week($start_date, $end_date, $value);
						foreach ($day_list as $day) {
							$c = 0;
							foreach ($data_count as $k => $item) {
								if((int)$day == (int)$item['dayonly']){
									$c = (int)$item['count'];
								}
							}
							$list_value[] = $c;                     
						}
						array_push($new_array, array('name' => $key, 'data' => $list_value));
					}

					if($group){

						$list_id = implode(',', $group);
						$q = 'select * from tblitems_groups where id in ('.$list_id.')';                      


						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                
						}

						$list_group = $this->omni_sales_model->get_data($q,true);


						$new_array = [];
						foreach ($list_group as $key => $value) {                         
							$list_value = [];
							$query = 'select day(datecreator) as dayonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id in (SELECT id FROM tblitems where group_id ='.$value['id'].')) and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by dayonly';
							$data_count = $this->omni_sales_model->get_data($query,true);
							foreach ($day_list as $day) {
								$c = 0;
								foreach ($data_count as $k => $item) {
									if((int)$day == (int)$item['dayonly']){
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;                     
							}
							array_push($new_array, array('name' => $value['name'], 'data' => $list_value));
						}
					}
					if($product){

						$list_id = [];
						$q = 'select * from tblitems';
						$con_q = '';
						if($product){
							$list_id = implode(',', $product);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.')';                                
						}

						$q_group = '';
						if($group){
							$list_id = implode(',', $group);
							$q_group .= ' and product_id in (SELECT id FROM tblitems where group_id in ('.$list_id.')) ';
						}

						$q = $q.$con_q;
						$list_product = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_product as $key => $value) { 
							$list_value = [];

							$query = 'select day(datecreator) as dayonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id = '.$value['id'].$q_group.') and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by dayonly';
							$data_count = $this->omni_sales_model->get_data($query,true);
							foreach ($day_list as $day) {
								$c = 0;
								foreach ($data_count as $k => $item) {
									if((int)$day == (int)$item['dayonly']){
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;                     
							}
							array_push($new_array, array('name' => $value['description'], 'data' => $list_value));
						}

					}
				}  

				if($type == 1){

					$start_date = $by_month.'-01-01 00:00:00';
					$end_date = $by_month.'-12-31 23:59:59';

					$category = array('Jan',
						'Feb',
						'Mar',
						'Apr',
						'May',
						'Jun',
						'Jul',
						'Aug',
						'Sep',
						'Oct',
						'Nov',
						'Dec');            
					$new_array = [];
					foreach ($list_channel as $key => $value) {
						$list_value = [];
						$query = 'select month(datecreator) as monthonly, count(1) as count
						from tblcart 
						where  channel_id = '.$value.'  and datecreator between \''.$start_date.'\' and \''.$end_date.'\' group by monthonly';
						$data_count = $this->omni_sales_model->get_data($query,true);
						for($i = 1; $i <= 12; $i++){
							$c = 0;
							foreach ($data_count as $k => $item) {
								if($i == $item['monthonly']){
									$c = (int)$item['count'];
								}
							}
							$list_value[] = $c;
						}
						array_push($new_array, array('name' => $key, 'data' => $list_value));
					}
					if($group){

						$list_id = [];
						$q = 'select * from tblitems_groups';
						$con_q = '';
						if($group){
							$list_id = implode(',', $group);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                
						}

						$q = $q.$con_q;
						$list_group = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_group as $key => $value) { 
							$list_value = [];

							$query = 'select month(datecreator) as monthonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id in (SELECT id FROM tblitems where group_id ='.$value['id'].')) and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by monthonly';

							$data_count = $this->omni_sales_model->get_data($query,true);
							for($i = 1; $i <= 12; $i++){
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['monthonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['name'], 'data' => $list_value));
						}
					}
					if($product){

						$list_id = [];
						$q = 'select * from tblitems';
						$con_q = '';
						if($product){
							$list_id = implode(',', $product);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                     
						}

						$q_group = '';
						if($group){
							$list_id = implode(',', $group);
							$q_group .= ' and product_id in (SELECT id FROM tblitems where group_id in ('.$list_id.'))';
						}

						$q = $q.$con_q;
						$list_product = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_product as $key => $value) { 
							$list_value = [];

							$query = 'select month(datecreator) as monthonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id = '.$value['id'].$q_group.') and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by monthonly';
							$data_count = $this->omni_sales_model->get_data($query,true);
							for($i = 1; $i <= 12; $i++){
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['monthonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['description'], 'data' => $list_value));
						}

					}
				} 

				if($type == 2){
					$min = min($by_year);
					$max = max($by_year);
					$start_date = $min.'-01-01 00:00:00';
					$end_date = $max.'-12-31 23:59:59';
					$list_cat = implode(',', $by_year);
					$category = $by_year;

					$new_array = [];
					foreach ($list_channel as $key => $value) {
						$list_value = [];
						$query = 'SELECT year(datecreator) as yearonly,count(*) as count FROM '.db_prefix().'cart where datecreator between \''.$start_date.'\' and \''.$end_date.'\' and channel_id = '.$value.' group by yearonly';
						$data_count = $this->omni_sales_model->get_data($query,true);
						foreach ($by_year as $i) {
							$c = 0;
							foreach ($data_count as $k => $item) {
								if($i == $item['yearonly']){
									$c = (int)$item['count'];
								}
							}
							$list_value[] = $c;
						}
						array_push($new_array, array('name' => $key, 'data' => $list_value));
					}

					if($group){

						$list_id = [];
						$q = 'select * from tblitems_groups';
						$con_q = '';
						if($group){
							$list_id = implode(',', $group);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                
						}

						$q = $q.$con_q;
						$list_group = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_group as $key => $value) { 
							$list_value = [];

							$query = 'select year(datecreator) as yearonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id in (SELECT id FROM tblitems where group_id ='.$value['id'].')) and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by yearonly';

							$data_count = $this->omni_sales_model->get_data($query,true);
							foreach ($by_year as $i) {
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['yearonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['name'], 'data' => $list_value));
						}
					}
					if($product){

						$list_id = [];
						$q = 'select * from tblitems';
						$con_q = '';
						if($product){
							$list_id = implode(',', $product);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                     
						}

						$q_group = '';
						if($group){
							$list_id = implode(',', $group);
							$q_group .= ' and product_id in (SELECT id FROM tblitems where group_id in ('.$list_id.'))';
						}


						$q = $q.$con_q;
						$list_product = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_product as $key => $value) { 
							$list_value = [];


							$query = 'select year(datecreator) as yearonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id = '.$value['id'].$q_group.') and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by yearonly';
							$data_count = $this->omni_sales_model->get_data($query,true);
							foreach ($by_year as $i) {
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['yearonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['description'], 'data' => $list_value));
						}

					}
				} 

				if($type == 3){


					$start_date = to_sql_date($report_from).' 00:00:00';
					$end_date = to_sql_date($report_to).' 23:59:59';

					$timestamp1 = strtotime($report_from);
					$timestamp2 = strtotime($report_to);

					$min = date('d', $timestamp1);
					$max = date('d', $timestamp2);

					$category = []; 
					for($i = (int)$min; $i <= (int)$max; $i++){
						$category[] = $i;                          
					}


					$new_array = [];
					foreach ($list_channel as $key => $value) {
						$list_value = [];
						$query = 'SELECT day(datecreator) as dayonly,count(*) as count FROM '.db_prefix().'cart where datecreator between \''.$start_date.'\' and \''.$end_date.'\' and channel_id = '.$value.' group by dayonly';

						$data_count = $this->omni_sales_model->get_data($query,true);
						for($i = (int)$min; $i <= (int)$max; $i++){
							$c = 0;

							foreach ($data_count as $k => $item) {
								if($i == $item['dayonly']){
									$c = (int)$item['count'];
								}
							}
							$list_value[] = $c;
						}
						array_push($new_array, array('name' => $key, 'data' => $list_value));
					}

					if($group){

						$list_id = [];
						$q = 'select * from tblitems_groups';
						$con_q = '';
						if($group){
							$list_id = implode(',', $group);
							$con_q = ' where id in ('.$list_id.')';
						}

						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.') ';                                
						}

						$q = $q.$con_q;
						$list_group = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_group as $key => $value) { 
							$list_value = [];

							$query = 'select day(datecreator) as dayonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id in (SELECT id FROM tblitems where group_id ='.$value['id'].')) and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by dayonly';

							$data_count = $this->omni_sales_model->get_data($query,true);
							for($i = (int)$min; $i <= (int)$max; $i++){
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['dayonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['name'], 'data' => $list_value));
						}
					}
					if($product){

						$list_id = [];
						$q = 'select * from tblitems';
						$con_q = '';
						if($product){
							$list_id = implode(',', $product);
							$con_q = ' where id in ('.$list_id.')';
						}


						$q_channel = '';
						$list_channel = [];
						if($channel){
							$list_channel = implode(',', $channel);
							$q_channel .= ' and channel_id in ('.$list_channel.')';                                     
						}

						$q_group = '';
						if($group){
							$list_id = implode(',', $group);
							$q_group .= ' and product_id in (SELECT id FROM tblitems where group_id in ('.$list_id.')) ';
						}
						$q = $q.$con_q;
						$list_product = $this->omni_sales_model->get_data($q,true); 
						$new_array = [];
						foreach ($list_product as $key => $value) { 
							$list_value = [];

							$query = 'select day(datecreator) as dayonly, count(1) as count
							from tblcart 
							where id in (SELECT cart_id
							FROM tblcart_detailt 
							where product_id = '.$value['id'].$q_group.') and datecreator between \''.$start_date.'\' and \''.$end_date.'\''.$q_channel.'group by dayonly';
							$data_count = $this->omni_sales_model->get_data($query,true);
							for($i = (int)$min; $i <= (int)$max; $i++){
								$c = 0;
								foreach ($data_count as $k => $item) {
									if($i == $item['dayonly']){                                                
										$c = (int)$item['count'];
									}
								}
								$list_value[] = $c;
							}
							array_push($new_array, array('name' => $value['description'], 'data' => $list_value));
						}

					}

				} 


				echo json_encode([
					'category' => $category,
					'data' => $new_array,
				]);

			}
		}
	/**
		 * get store woocommerce
		 * @return json
		 */
	public function get_store_woo(){
		$stores = $this->omni_sales_model->get_woocommere_store();
		$html = '';
		$html .= '<option value=""></option>';
		foreach ($stores as $key => $value) {
			$html .= '<option value="'.$value['id'].'">'.$value['name_channel'].'</option>';
		}
		echo json_encode([$html]);
	}

		/**
		 * table diary sync products
		 * @return table
		 */
		public function table_diary_sync_products(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_diary_sync_products'));
		}

		/**
		 * table diary sync orders
		 * @return table
		 */
		public function table_diary_sync_orders(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_diary_sync_orders'));
		}

		/**
		 * table diary sync inventory manage
		 * @return table
		 */
		public function table_diary_sync_inventory_manage(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_diary_sync_inventory_manage'));
		}

		/**
		 * { delete product store }
		 *
		 * @param  $store  The store
		 * @param  $id     The identifier
		 * @return redirect
		 */
		public function delete_product_store($store ,$id){
			$response = $this->omni_sales_model->delete_product_store($store, $id);
			if($response == true){
				set_alert('success', _l('deleted', _l('product_store')));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			redirect(admin_url('omni_sales/detail_channel_wcm/'.$store));
		}

		/**
		 *  process decriptions synchronization
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_decriptions_synchronization($store_id){
			update_option('status_sync', 1);
			$result = $this->omni_sales_model->process_decriptions_synchronization_detail($store_id);
			update_option('status_sync', 2);
			echo json_encode($result);
		}


		/**
		 *  process images synchronization
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_images_synchronization($store_id){
			$result = $this->omni_sales_model->process_images_synchronization($store_id);
			echo json_encode($result);
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

		/**
		 * sync products from store
		 * @param  int $store_id
		 * @return json
		 */
		public function sync_products_from_store($store_id){
			$success = $this->omni_sales_model->sync_from_the_store_to_the_system($store_id);
			echo json_encode([true]);
		}
		/**
		 * test connect
		 * @return json
		 */
		public function test_connect()
		{
			$data = $this->input->post();
			if($data['url'] != '' && $data['consumer_key'] != '' && $data['consumer_secret'] != ''){
				$success = $this->omni_sales_model->test_connect($data);
				if($success){
					$message = _l('connection_successful');         
				}else{
					$message = _l('connection_failed');         
				}
			}else{
				$success = false;   
				$message = _l('connection_failed');         
			}

			echo json_encode(['check' => $success, 'message' => $message]);
		}

		/**
		 * sync products from store
		 * @param  int $store_id
		 * @return json
		 */
		public function sync_products_from_info_woo($store_id){
			$success = $this->omni_sales_model->sync_products_from_info_woo($store_id);
			if($success){
				echo json_encode([true]);
			}
		}

		/**
		 * table sync products from the store basic
		 * @return table
		 */
		public function table_sync_products_from_the_store_information(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_sync_products_from_the_store'));
		}

		/**
		 * table sync products from the store full
		 * @return table
		 */
		public function table_sync_products_from_the_store_information_images(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_sync_products_from_the_store_information_images'));
		}

		/**
		 * table sync price
		 * @return table
		 */
		public function table_sync_price(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_sync_price'));
		}
		public function table_store_sync_v2(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_store_setting'));
		}
		public function sync_auto_store(){
			if($this->input->post()){
				$arr_store_exit = $this->omni_sales_model->get_setting_auto_sync_store_exit();
				$data = $this->input->post();
				if($data['id'] == ''){
					unset($data['id']);
					if(in_array($data['store'], $arr_store_exit)){
						$message = _l('config_store_exit');
						set_alert('warning', $message);
						redirect(admin_url('omni_sales/setting'));
					}
					$insert_id = $this->omni_sales_model->add_setting_auto_sync_store($data);
					if ($insert_id) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
				}else{
					$id = $data['id'];
					unset($data['id']);
					$success = $this->omni_sales_model->update_setting_auto_sync_store($data, $id);
					if ($success) {
						$message = _l('updated_successfully');
						set_alert('success', $message);
					}
				}
				redirect(admin_url('omni_sales/setting'));
			}
		}
		public function delete_sync_auto_store($id){
			$response = $this->omni_sales_model->delete_sync_auto_store($id);
			if($response == true){
				set_alert('success', _l('deleted', _l('sync_auto_store')));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			redirect(admin_url('omni_sales/setting'));
		}
		/**
		 * pos channel
		 * @return view
		 */
		public function create_pos_customer(){ 
			$data = $this->input->post();
			$id = $this->clients_model->add($data); 
			$success = false;
			$html = '';
			if($id > 0){
				$success = true;
				$data_contact['firstname'] = $data['company'];
				$data_contact['lastname'] = '';
				$data_contact['email'] = $data['email'];
				$data_contact['phonenumber'] = $data['phonenumber'];
				$data_contact['password'] = '123456a@';

				$data_contact['title'] = '';
				$data_contact['direction'] = '';
				$data_contact['fakeusernameremembered'] = '';
				$data_contact['fakepasswordremembered'] = '';
				$data_contact['is_primary'] = 'on';
				$data_contact['permissions'] = array('1','2','3','4','5','6');
				$data_contact['invoice_emails'] = 'invoice_emails';
				$data_contact['estimate_emails'] = 'estimate_emails';
				$data_contact['credit_note_emails'] = 'credit_note_emails';
				$data_contact['project_emails'] = 'project_emails';
				$data_contact['ticket_emails'] = 'ticket_emails';
				$data_contact['task_emails'] = 'task_emails';
				$data_contact['contract_emails'] = 'contract_emails';
				$rs = $this->create_contact($data_contact, $id);
				$data_client = $this->clients_model->get();
				$html .= '<option></option>';
				foreach ($data_client as $key => $value) {
					$html .= '<option value="'.$value['userid'].'">'.$value['company'].'</option>';
				}
			}
			echo json_encode([
				'success' => $success,
				'html' => $html,
				'id' => $id
			]);
			die
			;    }
			public function create_contact($data, $customer_id){
				if ($customer_id != '') {
					$id      = $this->clients_model->add_contact($data, $customer_id);
					$success = false;
					if ($id) {
						handle_contact_profile_image_upload($id);
						return true;
					}
				}
				return false;
			}
			public function check_exist_email_contact(){
				$data = $this->input->post();
				$exist = false;
				$result = $this->omni_sales_model->get_contact_by_email($data['email']);
				if($result){
					$exist = true;
				}
				echo json_encode([
					'exist' => $exist
				]);
				die;
			}

		/**
		 *  process asynclibrary image
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_asynclibrary_image($store_id){
			update_option('status_sync', 1);
			$url = site_url()."omni_sales/omni_sales_client/process_images_synchronization/".$store_id;
			$success = $this->asynclibrary->do_in_background($url, array());
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 *  process asynclibrary inventory
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_asynclibrary_inventory($store_id){
			update_option('status_sync', 1);
			$url = site_url()."omni_sales/omni_sales_client/process_inventory_synchronization/".$store_id;
			$success = $this->asynclibrary->do_in_background($url, array());
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		public function process_asynclibrary_info_basic($store_id){
			update_option('status_sync', 1);
			$url = site_url()."omni_sales/omni_sales_client/sync_products_from_info_woo/".$store_id;
			$success = $this->asynclibrary->do_in_background($url, array());
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		public function process_asynclibrary_info_full($store_id){
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_from_the_store_to_the_system($store_id, '');
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		public function payment_pos_after(){
			$data_payment = $this->input->post();
			$success = false;
			$res = $this->omni_sales_model->create_payment_cart($data_payment['invoice_id'],
				$data_payment['total_payment'],
				$data_payment['payment_methods'],
				$data_payment['order_number'],
				$data_payment['note']
			);
			if($res){
				$success = true;
			}
			echo json_encode([
				'success'=>$success
			]);
			die;  
		}

		/**
		 * sync products to store
		 * @param  int $store_id
		 * @return json
		 */
		public function sync_products_to_store_detail(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_from_the_system_to_the_store_single($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 *  process asynclibrary inventory
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_asynclibrary_inventory_detail(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->process_inventory_synchronization_detail($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 *  process asynclibrary image
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_asynclibrary_image_detail(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->process_images_synchronization_detail($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		/**
		 *  process decriptions synchronization
		 * @param  int $store_id 
		 * @return json
		 */
		public function process_decriptions_synchronization_detail(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$result = $this->omni_sales_model->process_decriptions_synchronization_detail($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($result);
		}



		public function omi_sales_delete_bulk_action()
		{
			if (!is_staff_member()) {
				ajax_access_denied();
			}

			$total_deleted = 0;

			if ($this->input->post()) {

				$ids                   = $this->input->post('ids');
				$rel_type                   = $this->input->post('rel_type');
				$store                   = $this->input->post('store');
				$arr_id                   = $this->input->post('arr_id');
				$arr_id = explode(',', $arr_id);


				/*check permission*/
				switch ($rel_type) {
					case 'omni_sales':
					if (!has_permission('omni_sales', '', 'delete') && !is_admin()) {
						access_denied('omni_sales');
					}
					break;


					default:
					break;
				}

				/*delete data*/
				if ($this->input->post('mass_delete')) {
					if (is_array($arr_id)) {
						foreach ($arr_id as $id) {
							switch ($rel_type) {
								case 'omni_sales':
								if ($this->omni_sales_model->delete_product_store_all($store, $id)) {
									$total_deleted++;
									break;
								}else{
									break;
								}

								default:

								break;
							}
						}
					}

					/*return result*/
					switch ($rel_type) {
						case 'omni_sales':
						set_alert('success', _l('total_omni_sales_list'). ": " .$total_deleted);
						break;

						default:
						break;

					}
				}
			}
		}

		/**
		 *  Sync price store
		 * @return json
		 */
		public function sync_price(){
			$data = $this->input->post();
			$detail = isset($data["arr_val"]) ? $data["arr_val"] : null;
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->process_price_synchronization($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 *  Sync price all of store
		 * @param  int $store_id 
		 * @return json
		 */
		public function sync_price_all($store_id){
			$data = $this->input->post();
			$detail = isset($data["arr_val"]) ? $data["arr_val"] : null;
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->process_price_synchronization($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 * sync all info products to store
		 * @return json
		 */
		public function sync_all(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_all($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}

		/**
		 * sync all info products to store
		 * @param  int $store_id 
		 * @return json
		 */
		public function sync_all_not_selected($store_id){
			$data = $this->input->post();
			$detail = isset($data["arr_val"]) ? $data["arr_val"] : null;
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_all($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		/**
	 * table log description
	 * @return table
	 */
		public function table_log_description(){
			$this->app->get_table_data(module_views_path('omni_sales', 'table/table_log_description'));
		}

	/**
	 * delete product
	 * @param  int $channel 
	 * @param  int $id      
	 * @return redirect
	 */
	public function delete_order($id){
		if (!has_permission('omni_order_list', '', 'delete') && !is_admin()) {
			access_denied('omni_order_list');
		}
		$response = $this->omni_sales_model->delete_order($id);

		if($response == true){
			set_alert('success', _l('deleted'));
		}
		else{
			set_alert('warning', _l('problem_deleting'));            
		}
		redirect(admin_url('omni_sales/order_list'));
	}
		/*
		*update for client kenya ------------------------------------------------------------------------------------------------------------------
		*/

	/**
	 * customer report
	 * @return [type] 
	 */
	public function customer_report()
	{

		if(!has_permission('omni_sales', '', 'view')  &&  !is_admin()) {
			access_denied('omni_sales');
		}
		
		$tab_active = $this->input->get('tab');
		if($tab_active){
			$data['tab_active'] = $tab_active;

		}else{
			$data['tab_active'] = 'pump_sales';

		}

		$this->load->model('departments_model');
		$data['title'] = _l('customer_report');
		$data['authorized'] = $this->omni_sales_model->get_distinct_authorized_customer_report();

		$data['customer_code'] = $this->clients_model->get();
		$this->load->view('import_customer_report/manage_customer_report', $data);

	}

	 /**
		* import customer report csv
		* @return [type] 
		*/
		public function import_customer_report_csv(){

			$this->load->model('staff_model');
			$data_staff = $this->staff_model->get(get_staff_user_id());

			/*get language active*/
			if ($data_staff) {
				if ($data_staff->default_language != '') {
					$data['active_language'] = $data_staff->default_language;

				} else {

					$data['active_language'] = get_option('active_language');
				}

			} else {
				$data['active_language'] = get_option('active_language');
			}
			$data['title'] = _l('import_csv');

			$this->load->view('import_customer_report/import_csv_customer_report', $data);
		}


		public function import_transaction_csv()
		{
			$array_payment_mode=[];
			$array_payment_mode['0'] = 'Cash';
			$array_payment_mode['1'] = 'Mobile';
			$array_payment_mode['2'] = 'Bank';
			$array_payment_mode['3'] = 'Card';
			$array_payment_mode['4'] = 'Invoice';

			$array_payment_id=[];
			foreach ($array_payment_mode as $pm_value) {
				$payment_id = $this->omni_sales_model->check_payment_mode_exist($pm_value);
				$array_payment_id[$pm_value] = (int)$payment_id;
			}

			$db_temp_fields = array();

			$db_temp_fields[] = 'ser_no';
			$db_temp_fields[] = 'authorized_by';
			$db_temp_fields[] = 'date';
			$db_temp_fields[] = 'time';
			$db_temp_fields[] = 'transaction_id';
			$db_temp_fields[] = 'receipt';
			$db_temp_fields[] = 'pay_mode';
			$db_temp_fields[] = 'nozzle';
			$db_temp_fields[] = 'product';
			$db_temp_fields[] = 'quantity';
			$db_temp_fields[] = 'total_sale';
			$db_temp_fields[] = 'ref_slip_no';


			$total_imported = 0;
			if ($this->input->post()) {
				$simulate = $this->input->post('simulate');
				if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

								// Get the temp file path
					$tmpFilePath = $_FILES['file_csv']['tmp_name'];
								// Make sure we have a filepath
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

						if (!file_exists(TEMP_FOLDER)) {
							mkdir(TEMP_FOLDER, 0755);
						}

						if (!file_exists($tmpDir)) {
							mkdir($tmpDir, 0755);
						}

										// Setup our new file path
						$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

						if (move_uploaded_file($tmpFilePath, $newFilePath)) {
							$import_result = true;
							$fd            = fopen($newFilePath, 'r');
							$rows          = [];
							while ($row = fgetcsv($fd)) {
								$rows[] = $row;
							}


							fclose($fd);
							$data['total_rows_post'] = count($rows);
							if (count($rows) <= 1) {
								set_alert('warning', 'Not enought rows for importing');
								redirect(admin_url('omni_sales/customer_report'));
							}

							unset($rows[0]);
							unset($rows[1]);


							$db_fields = [];
							foreach ($db_temp_fields as $field) {

								$db_fields[] = $field;
							}

							/*get max version for each row data*/
							$customer_report_version = $this->omni_sales_model->get_max_version_omni_customer_report();

							/*insert data*/
							$data_insert=[];
							foreach ($rows as $row) {
								$insert = [];
														// do for db fields
								if(!is_numeric($row[0])){
									continue;
								}
								for ($i = 0; $i < count($db_fields); $i++) {
																// Avoid errors on nema field. is required in database

									if ($row[$i] === 'NULL' || $row[$i] === 'null') {
										$row[$i] = '';
									}

									if($i == 2){

										$temp_date = str_replace('/', '-', $row[$i]);

										$insert[$db_fields[$i]] = date('Y-m-d', strtotime($temp_date));

									}elseif($i == 6){

										$insert[$db_fields[$i]] = $row[$i];
										$insert['pay_mode_id'] = $array_payment_id[$row[$i]];
									}else{
										$insert[$db_fields[$i]] = $row[$i];

									}

									if($i == 3){
										$insert['shift_type'] = $this->get_shift_type($row[$i]);
									}
								}

								$insert['version'] = $customer_report_version;
								$insert['date_add'] = date('Y-m-d H:i:s');
								$insert['date_time_transaction'] = $insert['date'].' '.$insert['time'];

								array_push($data_insert, $insert);

							}
							$total_imported = $this->db->insert_batch_on_duplicate('omni_customer_report',$data_insert);
							@delete_dir($tmpDir);
						}
					} else {
						set_alert('warning', _l('import_upload_failed'));
					}
				}
			}

			if (isset($import_result)) {
				set_alert('success', _l('import_total_imported', $total_imported));
			}

			redirect(admin_url('omni_sales/manage_customer_report'));
		}

 /**
	* table manage import customer reports
	* @return [type] 
	*/
	public function table_manage_import_customer_reports() {

		$this->app->get_table_data(module_views_path('omni_sales', 'import_customer_report/table_customer_report'));
	}

	/**
	 * customer report delete bulk action
	 * @return [type] 
	 */
	public function customer_report_delete_bulk_action()
	{
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids                   = $this->input->post('ids');
			$rel_type                   = $this->input->post('rel_type');

			/*check permission*/

			if (!has_permission('omni_sales', '', 'delete') && !is_admin()) {
				access_denied('omni_sales');
			}

			/*delete data*/
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {

					switch ($rel_type) {

						case 'customer_report':
						$sql_where = " id  IN ( '" . implode( "', '" , $ids ) . "' ) ";
						$this->db->where($sql_where);
						$total_deleted = $this->db->delete(db_prefix() . 'omni_customer_report');
						break;

						case 'create_customer_report':
						$sql_where = " id  IN ( '" . implode( "', '" , $ids ) . "' ) ";
						$this->db->where($sql_where);
						$total_deleted = $this->db->delete(db_prefix() . 'omni_create_customer_report');

						$sql_where_1 = " create_customer_report_id  IN ( '" . implode( "', '" , $ids ) . "' ) ";
						$this->db->where($sql_where_1);
						$total_deleted = $this->db->delete(db_prefix() . 'omni_create_customer_report_detail');

						break;


						default:

						break;
					}

				}

				/*return result*/
				switch ($rel_type) {
					case 'customer_report':
					if($total_deleted){
						set_alert('success', _l('delete_customer_transaction_success'));
					}else{
						set_alert('warning', _l('delete_customer_transaction_false'));
					}
					break;

					case 'create_customer_report':
					if($total_deleted){
						set_alert('success', _l('delete_customer_report_success'));
					}else{
						set_alert('warning', _l('delete_customer_report_false'));
					}
					break;


					default:
					break;

				}

			}

		}


	}

		/**
		 * get customer report
		 * @return [type] 
		 */
		public function get_customer_report()
		{
			$this->load->model('staff_model');

			$customer_report = $this->input->post();
			$customer_report_data = $this->omni_sales_model->get_customer_report($customer_report['id']);

			//check staff_id, customer_id exist
			$customer_status='false';
			$staff_status='false';

			if($customer_report_data){
				$customer_code = $this->clients_model->get($customer_report_data->customer_id);

				if(!is_array($customer_code) && isset($customer_code)){
					$customer_status ='true';
				}

			}

			echo json_encode([
				'customer_report' => $customer_report_data,
				'customer_status' => $customer_status,

			]);
		}

		/**
		 * edit customer report
		 * @param  string $id 
		 * @return [type]     
		 */
		public function edit_customer_report($id = '') {
			if ($this->input->post()) {
				$message = '';
				$data = $this->input->post();

				$id = $data['id'];
				unset($data['id']);
				$success = $this->omni_sales_model->update_customer_report($data, $id);
				if ($success) {
					set_alert('success', _l('updated_successfully').' ' . _l('customer_report'));
				} else {
					set_alert('warning', _l('updated_customer_report_false'));
				}
				redirect(admin_url('omni_sales/manage_customer_report'));
			}
		}


		/**
		 * check shift type
		 * @param  [type] $shift_time 
		 * @return [type]             
		 */
		public function get_shift_type($shift_time)
		{
			if($shift_time != '' && isset($shift_time)){
				$shift_time_explode = explode(":", $shift_time);

				if((int)$shift_time_explode[0] == 16){
					if((int)$shift_time_explode[1] < 30){
						return 'day_shift';
					}else{
						return 'night_shift';
					}
				}elseif((int)$shift_time_explode[0] == 7){
					if((int)$shift_time_explode[1] >= 30){
						return 'day_shift';
					}else{
						return 'night_shift';
					}
				}else{

					if( (8 <= (int)$shift_time_explode[0]) && ((int)$shift_time_explode[0] <= 16) ){
						return 'day_shift';
					}else{
						return 'night_shift';
					}
				}

			}else{
				return '';
			}

		}


		/**
		 * manage customer report
		 * @return [type] 
		 */
		public function manage_customer_report() {
			if (!has_permission('omni_sales', '', 'edit') && !is_admin() && !has_permission('omni_sales', '', 'create')) {
				access_denied('omni_sales');
			}

			$this->load->model('payment_modes_model');
			$data['group'] = $this->input->get('group');

			$data['title'] = _l('customer_report');
			$data['tab'][] = 'manage_customer_report';
			$data['tab'][] = 'manage_create_customer_report';

			if ($data['group'] == '') {
				$data['group'] = 'manage_customer_report';


			}

			$data['authorized'] = $this->omni_sales_model->get_distinct_authorized_customer_report();
			$data['customer_code'] = $this->clients_model->get();
			$data['payment_modes'] = $this->payment_modes_model->get();

			$data['tabs']['view'] = 'import_customer_report/' . $data['group'];
			$this->load->view('import_customer_report/manage', $data);
		}

		/**
		 * create report transation bulk action
		 * @return [type] 
		 */
		public function create_report_transation_bulk_action()
		{

			if ($this->input->post()) {
				$data = $this->input->post();
				$ids                   = $this->input->post('customer_report_id');
				$data['ids'] = $ids;

				if (strlen($ids) > 0) {
					$data_return = $this->omni_sales_model->create_report_from_transaction_bulk_action($data);

					if(isset($data_return['insert_id'])){
						set_alert('success', _l('create_report_success'));
						redirect(admin_url('omni_sales/view_customer_report_detail/'.$data_return['insert_id']));
					}else{
						set_alert('success', _l('create_report_false'));
						redirect(admin_url('omni_sales/manage_customer_report'));

					}

				}

			}
		}


		/**
		 * table manage create customer reports
		 * @return [type] 
		 */
		public function table_manage_create_customer_reports() {

			$this->app->get_table_data(module_views_path('omni_sales', 'import_customer_report/table_create_customer_report'));
		}


		/**
		 * table manage customer reports detail
		 * @return [type] 
		 */
		public function table_view_customer_report_detail() {

			$this->app->get_table_data(module_views_path('omni_sales', 'import_customer_report/table_view_customer_report_detail'));
		}


		/**
		 * view customer report detail
		 * @return [type] 
		 */
		public function view_customer_report_detail($id) {

			if (!has_permission('omni_sales', '', 'view') && !is_admin() ) {
				access_denied('omni_sales');
			}

			$data['arr_customer_report'] = $this->omni_sales_model->get_list_customer_report_by_id($id);
			$data['customer_report'] = $this->omni_sales_model->get_create_customer_report($id);

			$data['title'] = _l('customer_report');
			$data['id'] = $id;

			$this->load->view('import_customer_report/view_customer_report_detail', $data);
		}

		/**
		 * create invoice from customer report bulk action
		 * @return [type] 
		 */
		public function create_invoice_from_customer_report_bulk_action()
		{
			if (!is_staff_member()) {
				ajax_access_denied();
			}

			if ($this->input->post()) {

				$ids                   = $this->input->post('ids');

				/*create invoice from cr data*/
				if (is_array($ids)) {
					$this->omni_sales_model->create_invoice_from_customer_report_bulk_action($ids);
					$status =  'true';
					$message =  _l('create_invoice_successfully');
				}else{
					$status =  'false';
					$message = _l('no_transaction_selected');

				}
				echo json_encode([
					'message' => $message,
					'status' => $status,

				]);
				die;
			}

		}

		/**
		 * table_create_invoice_from_customer_report
		 * @param  [type] $id 
		 * @return [type]     
		 */
		public function table_create_invoice_from_customer_report($id)
		{
			$customer_report = $this->omni_sales_model->get_create_customer_report($id);

			if($customer_report){
				$arr_customer_report_id = explode(",",$customer_report->list_customer_report_id);
				$this->omni_sales_model->create_invoice_from_customer_report_bulk_action($arr_customer_report_id);

				set_alert('success', _l('create_invoice_successfully'));
				redirect(admin_url('omni_sales/manage_customer_report?group=manage_create_customer_report'));
			}else{

				set_alert('warning', _l('create_invoice_false'));
				redirect(admin_url('omni_sales/manage_customer_report?group=manage_create_customer_report'));
			}

		}


		/**
		 * table create invoice from customer report temp
		 * @param  [type] $id 
		 * @return [type]     
		 */
		public function table_create_invoice_from_customer_report_temp($id)
		{

			$customer_report = $this->omni_sales_model->get_create_customer_report($id);

			if($customer_report){
				$arr_customer_report_id = explode(",",$customer_report->list_customer_report_id);
				$this->omni_sales_model->create_invoice_from_customer_report_bulk_action($arr_customer_report_id);

				set_alert('success', _l('create_invoice_successfully'));
				redirect(admin_url('omni_sales/view_customer_report_detail/'.$id));
			}else{

				set_alert('warning', _l('create_invoice_false'));
				redirect(admin_url('omni_sales/view_customer_report_detail/'.$id));
			}


		}


		/*Move from inventory module to omni sales module*/
		/**
	 * manage importing transaction
	 * @param  string $transaction_id 
	 * @return [type]                 
	 */
		public function manage_importing_transaction($transaction_id = ''){
			if(!has_permission('omni_sales', '', 'view')  &&  !is_admin()) {
				access_denied('omni_sales');
			}
			$tab_active = $this->input->get('tab');
			if($tab_active){
				$data['tab_active'] = $tab_active;

			}else{
				$data['tab_active'] = 'pump_sales';

			}

			$this->load->model('departments_model');
			$data['title'] = _l('mpesatransc');
			$data['version_values'] = $this->omni_sales_model->get_distinct_version_import_transaction();

			$data['customer_code'] = $this->clients_model->get();
			$data['staffs'] = $this->omni_sales_model->get_staff();

			$this->load->view('import_customer_report/manage_importing_transaction', $data);

		}

		/**
	 * table manage import mpesatransc
	 * @return [type] 
	 */
		public function table_manage_import_mpesatransc() {

			$this->app->get_table_data(module_views_path('omni_sales', 'import_customer_report/table_manage_import_mpesatransc'));
		}

	/**
	 * warehouse delete import transation bulk action
	 * @return [type] 
	 */
	public function warehouse_delete_import_transation_bulk_action()
	{
		if (!is_staff_member()) {
			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {

			$ids                   = $this->input->post('ids');
			$rel_type                   = $this->input->post('rel_type');

			/*check permission*/

			if (!has_permission('omni_sales', '', 'delete') && !is_admin()) {
				access_denied('omni_sales');
			}

			/*delete data*/
			if ($this->input->post('mass_delete')) {
				if (is_array($ids)) {

					switch ($rel_type) {

						case 'importing_transaction':
						$sql_where = " id  IN ( '" . implode( "', '" , $ids ) . "' ) ";
						$this->db->where($sql_where);
						$total_deleted = $this->db->delete(db_prefix() . 'pump_sales');

						break;
						case 'importing_mpesatransc':
						$this->db->where_in('id', $ids);
						$total_deleted = $this->db->delete(db_prefix() . 'mpesatransc');
						break;

						default:

						break;
					}


				}

				/*return result*/
				switch ($rel_type) {
					case 'importing_transaction':
					if($total_deleted){
						set_alert('success', _l('delete_transation_success'));
					}else{
						set_alert('warning', _l('delete_transation_false'));
					}
					break;
					case 'importing_mpesatransc':
					if($total_deleted){
						set_alert('success', _l('delete_mpesatransc_success'));
					}else{
						set_alert('warning', _l('delete_mpesatransc_false'));
					}
					break;

					default:
					break;

				}


			}

		}


	}


		/**
		 * get mpesatransc
		 * @return [type] 
		 */
		public function get_mpesatransc()
		{
			$mpesatransc = $this->input->post();
			$mpesatransc_data = $this->omni_sales_model->get_mpesatransc($mpesatransc['id']);

			echo json_encode([

				'mpesatransc' => $mpesatransc_data,

			]);

		}

			/**
		 * edit mpesatransc
		 * @param  string $id 
		 * @return [type]     
		 */
			public function edit_mpesatransc($id = '') {
				if ($this->input->post()) {
					$message = '';
					$data = $this->input->post();

					$id = $data['id'];
					unset($data['id']);
					$success = $this->omni_sales_model->update_mpesatransc($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully') .' '. _l('mpesatransc'));
					} else {
						set_alert('warning', _l('updated_mpesatransc_false'));
					}


					redirect(admin_url('omni_sales/manage_importing_transaction?tab=mpesatransc'));
				}
			}



			public function setting_sync_invoice(){
				if ($this->input->post()) {
					$data = $this->input->post();
					update_option('invoice_sync_configuration', $data['omni_sales_invoice_setting']);
				}else{
					update_option('invoice_sync_configuration', 0);
				}
				redirect(admin_url('omni_sales/setting?tab=invoice_sync_configuration'));
			}


	/**
		 *  sync product name
		 * @return json
		 */
	public function sync_product_name(){
		$data = $this->input->post();
		$detail = $data["arr_val"];
		$store_id = $data["id"];
		update_option('status_sync', 1);
		$success = $this->omni_sales_model->sync_product_name($store_id, $detail);
		update_option('status_sync', 2);
		echo json_encode($success);
	}

	/**
		 *  sync product name all
		 * @param  int $store_id 
		 * @return json
		 */
	public function sync_product_name_all($store_id){
		update_option('status_sync', 1);
		$success = $this->omni_sales_model->sync_product_name($store_id);
		update_option('status_sync', 2);
		echo json_encode($success);
	}

		/**
		 *  sync short description
		 * @return json
		 */
		public function sync_short_description(){
			$data = $this->input->post();
			$detail = $data["arr_val"];
			$store_id = $data["id"];
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_short_description($store_id, $detail);
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		

		/**
		 *  sync short description all
		 * @param  int $store_id 
		 * @return json
		 */
		public function sync_short_description_all($store_id){
			update_option('status_sync', 1);
			$success = $this->omni_sales_model->sync_short_description($store_id);
			update_option('status_sync', 2);
			echo json_encode($success);
		}
		/**
		 * create invoice pos after
		 * @param  integer insert_id 
		 * @return json            
		 */
		public function create_invoice_pos_after(){
			$data = $this->input->post();
			$success =false;
			$id_invoice = $this->omni_sales_model->add_inv_when_order($data['insert_id'], 4);
			$number_invoice = '';
			$id_exp ='';
			if($id_invoice){
				$this->db->where('id', $id_invoice);
				$number_invoice = $this->db->get(db_prefix().'invoices')->row()->number;    
				$success = true;    
				$data_update['status'] = 4;
				$data_update['admin_action'] = 1;
				if(omni_get_status_modules('warehouse')){

					if(isset($data['warehouse_id']) && is_numeric($data['warehouse_id'])){
						$id_exp = $this->omni_sales_model->create_goods_delivery($id_invoice, $data['warehouse_id']);
					}else{
						$id_exp = $this->omni_sales_model->omnisales_auto_create_goods_delivery_with_invoice($id_invoice);
					}

					$data_update['stock_export_number'] = $id_exp;
				}
				$this->db->where('id', $data['insert_id']);
				$this->db->update(db_prefix().'cart', $data_update); 
			}
			echo json_encode([
				'success' => $success,
				'id_invoice' => $id_invoice,
				'stock_export_number' => $id_exp,
				'number_invoice' => $number_invoice
			]);
			die;
		}
		/**
		 * delete mass add product sales channel
		*/
		public function delete_mass_add_product_sales_channel(){
			if($this->input->post()){
				$data = $this->input->post();
				$redirect = '';
				if(isset($data['redirect'])){
					$redirect = $data['redirect'];
					unset($data['redirect']);
				}

				$channel = $data['channel'];
				if($data['mass_delete'] == 'on'){
					$res = $this->omni_sales_model->delete_mass_product_sales_channel($data);
					if ($res == true) {
						$message = _l('delete_successfully');
						set_alert('success', $message);
					}
					else{
						$message = _l('delete_failed');
						set_alert('warning', $message);
					}
				}
				if($redirect != ''){
					redirect(admin_url('omni_sales/'.$redirect)); 
				}
				redirect(admin_url('omni_sales/add_product_channel/'.$channel)); 
			}
		}

	 /**
		 * shift
		 * @return view
		 */
	 public function shift(){
	 	$data['title'] = _l('omni_shift_management');
	 	$this->load->model('staff_model');
	 	$data['staff'] = $this->staff_model->get();
	 	$this->load->model('currencies_model');
	 	$base_currency = $this->currencies_model->get_base_currency();
	 	$data['currency_name'] = '';
	 	if(isset($base_currency)){
	 		$data['currency_name'] = $base_currency->name;
	 	}
	 	$this->load->view('pos/shift_list', $data);
	 }

	 public function add_shift(){
	 	if($this->input->post()){
	 		$data = $this->input->post();
	 		if($data['staff_id'] != ''){
	 			$valid = true;
	 			$result_check = $this->omni_sales_model->get_shift_staff($data['staff_id'], 1);
	 			if($result_check){
	 				$valid = false;
	 				set_alert('danger', get_staff_full_name($data['staff_id']).' '._l('had_a_work_shift_could_not_create_a_new_shift'));
	 			}
	 			if($valid){
					// Add or edit shift
	 				if($data['id'] == ''){
	 					unset($data['id']);
	 					$insert_id = $this->omni_sales_model->add_shift($data);
	 					if(is_numeric($insert_id)){
	 						$granted_amount = str_replace(',','',$data['granted_amount']);
	 						$this->omni_sales_model->add_shift_transactions($insert_id, 'granted', '+'.$data['granted_amount'], $granted_amount, $granted_amount, 0, 0, 0, $data['staff_id'], null);
	 						set_alert('success', _l('added_successfully'));
	 					}
	 					else{
	 						set_alert('danger', _l('added_fail'));
	 					}
	 					redirect(admin_url('omni_sales/pos?shift='.$insert_id)); 
	 				}
	 				else{
	 					$success = $this->omni_sales_model->update_shift($data);
	 					if($success){
	 						set_alert('success', _l('updated_successfully'));
	 					}
	 					else{
	 						set_alert('danger', _l('updated_fail'));
	 					}
	 				}
					// End add or edit shift
	 			}
	 			redirect(admin_url('omni_sales/shift')); 
	 		}
	 	}
	 }
	 public function get_transaction_history(){
	 	if($this->input->post()){
	 		$data = $this->input->post();
	 		$this->load->model('currencies_model');
	 		$base_currency = $this->currencies_model->get_base_currency();
	 		$currency_name = '';
	 		if(isset($base_currency)){
	 			$currency_name = $base_currency->name;
	 		}

	 		$html_view = '';
	 		$granted_amount = 0;
	 		$incurred_amount = 0;
	 		$closing_amount = 0;
	 		$revenue_amount = 0;
	 		if($data['shift'] != ''){
	 			$shift_history_data = $this->omni_sales_model->get_shift_history($data['shift']);
	 			if($shift_history_data){

	 				$html_view .= '<ul class="timeline">';
	 				foreach ($shift_history_data as $key => $value) {

	 					$html_view .= '<li>';

	 					$action = '';
	 					if($value['type'] == 'granted'){
	 						$content = _l('granted').': '.app_format_money($value['granted_amount'], $currency_name);
	 						$action .= '<span class="float-right">'.$content.'</span>';
	 					}
	 					else if($value['type'] == 'customer_pay'){
	 						$order_id = 0;
	 						$order_data = $this->omni_sales_model->get_cart_by_order_number($value['action']);
	 						if($order_data){
	 							$order_id = $order_data->id;
	 						}
	 						$content =  _l('order_value').': '.app_format_money($value['order_value'], $currency_name).' - '._l('customer_pay').': '.app_format_money($value['customer_amount'], $currency_name).' - '._l('return').': '.app_format_money($value['balance_amount'], $currency_name);
	 						$action .= '<a target="_blank" href="'.admin_url('omni_sales/view_order_detailt/'.$order_id).'">'.'#'.$value['action'].'</a>';
	 						$action .= '<span class="float-right"> '.$content.'</span>';
	 					}

	 					$html_view .= $action;
	 					$html_view .= '<p class="date pull-right">'.time_ago($value['created_at']).'</p>';
	 					$html_view .= '</li>';

	 					$granted_amount = $value['granted_amount'];
	 					$incurred_amount += $value['balance_amount'];
	 					$revenue_amount += $value['order_value'];
	 					$closing_amount = $value['current_amount'];
	 				}
	 				$html_view .= '</ul>';

	 			}
	 			else{
	 				$html_view = '<center class="empty_transaction">'._l('no_transaction_history').'</center>';
	 			}
	 		}
	 		echo json_encode([
	 			'view' => $html_view,
	 			'granted_amount' => app_format_money($granted_amount, $currency_name),
	 			'incurred_amount' => app_format_money($incurred_amount, $currency_name),
	 			'revenue_amount' => app_format_money($revenue_amount, $currency_name),
	 			'closing_amount' => app_format_money($closing_amount, $currency_name)
	 		]);
	 		die;
	 	}
	 }

	 public function close_shift(){
	 	if($this->input->post()){
	 		$data = $this->input->post();
	 		if($data['shift'] != ''){
	 			$message = '';
	 			$result = $this->omni_sales_model->change_shift_status($data['shift'], 2);
	 			if($result == true){
	 				$message = _l('change_status_successfully');
	 			}
	 			else{
	 				$message = _l('change_status_fail');
	 			}
	 			echo json_encode([
	 				'success' => $result,
	 				'message' => $message
	 			]);
	 			die;
	 		}
	 	}
	 }

	 public function shift_list_table(){
	 	if ($this->input->is_ajax_request()) {
	 		if($this->input->post()){
	 			$this->load->model('currencies_model');
	 			$start_date = $this->input->post("start_date");
	 			$end_date = $this->input->post("end_date");
	 			$seller = $this->input->post("seller");
	 			$status = $this->input->post("status");

	 			$query = '';      

	 			$select = [
	 				'id',
	 				'id',
	 				'id',
	 				'id',
	 				'id',
	 				'id',
	 				'id',
	 				'id',
	 				'id'          
	 			];

	 			$where = [];


	 			if(($start_date != '') && ($end_date == '')){
	 				$start_date = $this->omni_sales_model->format_date($start_date);
	 				array_push($where, ' AND date(created_at) = "'.$start_date.'"');
	 			}

	 			if(($start_date == '') && ($end_date != '')){
	 				$end_date = $this->omni_sales_model->format_date($end_date);
	 				array_push($where, ' AND date(created_at) = "'.$end_date.'"');
	 			}

	 			if(($start_date != '') && ($end_date != '')){
	 				$end_date = $this->omni_sales_model->format_date($end_date);
	 				$start_date = $this->omni_sales_model->format_date($start_date);
	 				array_push($where, ' AND date(created_at) between "'.$start_date.'" and "'.$end_date.'"');
	 			}

	 			if(is_admin()){
	 				if(isset($seller) && $seller != ''){
	 					$list_id = implode(',', $seller);
	 					array_push($where, ' AND staff_id in ('.$list_id.')');
	 				}
	 			}
	 			else{
	 				array_push($where, ' AND staff_id = "'.get_staff_user_id().'"');
	 			}
	 			if(isset($status) && $status != ''){
	 				array_push($where, ' AND status = '.$status.'');
	 			}
	 			$base_currency = $this->currencies_model->get_base_currency();
	 			$currency_name = '';
	 			if(isset($base_currency)){
	 				$currency_name = $base_currency->name;
	 			}

	 			$aColumns     = $select;
	 			$sIndexColumn = 'id';
	 			$sTable       = db_prefix() . 'omni_shift';
	 			$join         = [' left join '.db_prefix().'staff on '.db_prefix().'staff.staffid = '.db_prefix().'omni_shift.staff_id'];

	 			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	 				db_prefix().'omni_shift.id',
	 				db_prefix().'omni_shift.staff_id',
	 				db_prefix().'staff.firstname',
	 				db_prefix().'staff.lastname',
	 				'status',
	 				'created_at',
	 				'granted_amount',
	 				'incurred_amount',
	 				'closing_amount',
	 				'order_value'
	 			]);


	 			$output  = $result['output'];
	 			$rResult = $result['rResult'];
	 			foreach ($rResult as $aRow) {
	 				$row = [];
	 				$row[] = $aRow['id'];              
	 				$row[] = $aRow['firstname'].' '.$aRow['lastname'];              
	 				$row[] = _dt($aRow['created_at']);

	 				$row[] = app_format_money($aRow['granted_amount'], $currency_name);              
	 				$row[] = app_format_money($aRow['incurred_amount'], $currency_name);              
	 				$row[] = app_format_money($aRow['closing_amount'], $currency_name);   
	 				$row[] = app_format_money($aRow['order_value'], $currency_name);   

	 				$status = '';
	 				if($aRow['status'] == 1){
	 					$status = '<span class="btn btn-default">'._l('open').'</span>';
	 				}          
	 				else if($aRow['status'] == 2){
	 					$status = '<span class="btn btn-default">'._l('closed').'</span>';
	 				}    
	 				$row[] = $status;              
	 				$option = '';
	 				if(get_staff_user_id() == $aRow['staff_id']){
	 					if($aRow['status'] == 1){
	 						$option .= '<a href="' . admin_url('omni_sales/pos?shift='. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('continue').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
	 						$option .= '<i class="fa fa-play"></i>';
	 						$option .= '</a>';          
	 					}
	 				}

	 				if(is_admin()){
	 					if($aRow['status'] == 2){
	 						$option .= '<a href="' . admin_url('omni_sales/delete_shift/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('delete').'" class="btn btn-danger btn-icon _delete">';
	 						$option .= '<i class="fa fa-remove"></i>';
	 						$option .= '</a>';
	 					}
	 				}
	 				$row[] = $option; 
	 				$output['aaData'][] = $row;                                      
	 			}
	 			echo json_encode($output);
	 			die();
	 		}
	 	}
	 }

	 public function delete_shift($shift_id){
	 	if($shift_id != ''){
	 		$data_shift = $this->omni_sales_model->get_shift($shift_id);
	 		if($data_shift){
	 			if($data_shift->status == 2){
	 				if($shift_id != ''){
	 					$success = $this->omni_sales_model->delete_shift($shift_id);
	 					if($success){
	 						set_alert('success', _l('deleted_successfully'));
	 					}
	 					else{
	 						set_alert('danger', _l('deleted_fail'));
	 					}
	 				}
	 			}
	 			else{
	 				set_alert('danger', _l('please_close_the_shift_so_this_action_can_be_taken'));
	 			}
	 		}
	 		redirect(admin_url('omni_sales/shift')); 
	 	}
	 }
	 /**
	  * order manual
	  * @param  string $order_id 
	  */
	 public function order_manual($order_id = ''){
	 	$this->load->model('taxes_model');
	 	$this->load->model('clients_model');
	 	$this->load->model('warehouse/warehouse_model');
	 	$this->load->model('invoice_items_model');
	 	$this->load->model('payment_modes_model');
	 	if($this->input->post()){
	 		$data = $this->input->post();
	 		if($data['id'] == ''){
	 			unset($data['id']);

	 			if (!has_permission('omni_order_list', '', 'create') && !is_admin()) {
					access_denied('omni_order_list');
				}

	 			$res = $this->omni_sales_model->create_new_order($data);
	 			if ($res) {
	 				$message = _l('added_successfully');
	 				set_alert('success', $message);
	 			}
	 			else{
	 				$message = _l('added_fail');
	 				set_alert('danger', $message);
	 			}
	 			redirect(admin_url('omni_sales/order_list'));
	 		}
	 		else{

	 			if (!has_permission('omni_order_list', '', 'edit') && !is_admin()) {
					access_denied('omni_order_list');
				}

	 			$res = $this->omni_sales_model->update_order($data);
	 			if ($res) {
	 				$message = _l('updated_successfully');
	 				set_alert('success', $message);
	 			}
	 			else{
	 				$message = _l('update_fail');
	 				set_alert('danger', $message);
	 			}
	 			redirect(admin_url('omni_sales/order_list'));

	 		}	 	}
	 		$data['payment_modes'] = $this->payment_modes_model->get('', [
	 			'expenses_only !=' => 1,
	 		]);
	 		$data['id'] = $order_id; 
	 		$data['taxes'] = $this->taxes_model->get();
	 		$data['customers'] = $this->clients_model->get();
	 		$data['items'] = $this->invoice_items_model->get_grouped();  
	 		if($order_id == ''){
	 			$data['title'] = _l('create_manual_orders');
	 		}
	 		else{
	 			$data['title'] = _l('edit_manual_orders');
	 			$data['order'] = $this->omni_sales_model->get_cart($order_id);
	 			$data['add_items'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($order_id);
	 		}

	 		$order_manual_row_template = '';
	 		if(is_numeric($order_id)){
	 		
	 			$this->load->model('estimates_model');
	 			// check is approval
	 			$this->load->model('estimates_model');
	 			$data['estimate_data'] = $this->omni_sales_model->omni_get_estimates_data($data['order']->estimate_id);

	 			if(isset($data['order']) && is_numeric($data['order']->estimate_id)){
	 				if(isset($data['add_items'])){
	 					$index_cart_detail = 0;
	 					foreach ($data['add_items'] as $cart_detail) {
	 						$index_cart_detail++;
	 						$unit_name = $cart_detail['unit_name'];
	 						$commodity_name = $cart_detail['product_name'];

	 						$order_manual_row_template .= $this->omni_sales_model->create_order_manual_row_template('newitems[' . $index_cart_detail . ']', $commodity_name, $cart_detail['quantity'], $cart_detail['unit_name'], $cart_detail['prices'], $cart_detail['long_description'], $cart_detail['sku'], $cart_detail['product_id'], $cart_detail['unit_id'] , $cart_detail['percent_discount'], $cart_detail['prices_discount'], $cart_detail['tax_rate'],$cart_detail['tax_name'],$cart_detail['tax_id'], $cart_detail['id'], true);

	 					}
	 				}
	 			}

	 		}else{
	 			$this->load->model('estimates_model');
	 			$data['estimate_data'] = $this->omni_sales_model->omni_get_estimates_data();
	 		}

	 		$data['staff']     = $this->staff_model->get('', ['active' => 1]);
	 		$this->load->model('currencies_model');
	 		$data['currencies'] = $this->currencies_model->get();
	 		$data['base_currency'] = $this->currencies_model->get_base_currency();
	 		$data['order_manual_row_template'] = $order_manual_row_template;
	 		$this->load->view('order_list/manual_orders', $data);
	 	}

		/**
		 * get taxes dropdown template
		 * @return view
		 */
		public function get_taxes_dropdown_template()
		{
			$this->load->model('misc_model');
			$name    = $this->input->post('name');
			$taxname = $this->input->post('taxname');
			echo html_entity_decode($this->misc_model->get_taxes_dropdown_template($name, $taxname));
		}
		/* Get item by id / ajax */
		public function get_item_by_id($id)
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('invoice_items_model');
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
				echo json_encode($item);
			}
		}
		/**
		 * client change data
		 * @param  integer $customer_id
		 * @return json
		 */
		public function client_change_data($customer_id = '')
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('projects_model');
				$this->load->model('clients_model');
				$data                     = [];
				$data['status'] = true;
				if($customer_id == ''){
					$customer_id = 0;
					$data['status'] = false;
				}


				$base_currency = get_base_currency();
				$data['currency_id'] = (int)$base_currency->id;
				$data_customer = $this->clients_model->get($customer_id);
				if(isset($data_customer)){
					if($data_customer->default_currency){
						$data['currency_id'] = $data_customer->default_currency;						
					}
				}
				$data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
				if($data['billing_shipping']){
					$data_billing_country = get_country($data['billing_shipping'][0]['billing_country']);
					if($data_billing_country){
						$data['billing_shipping'][0]['billing_country'] = $data_billing_country->iso2;
					}
					$data_shiping_country = get_country($data['billing_shipping'][0]['shipping_country']);
					if($data_shiping_country){
						$data['billing_shipping'][0]['shipping_country'] = $data_shiping_country->iso2;
					}
				}
				echo json_encode($data);
			}
		}
		/**
		 * add product pos 
		*/
		public function add_product_pos(){
			if ($this->input->is_ajax_request()) {
				$data = $this->input->post();
				$message = '';
				$success = false;
				if($data['id'] == ''){
					$insert_id = $this->omni_sales_model->add_product_pos($data);
					if(is_numeric($insert_id)){
						$success = true;
						if(omni_get_status_modules('warehouse')){
							$list_file = $_FILES;
							unset($_FILES);
							foreach ($list_file['file']['name'] as $key => $file) {
								$_FILES = [];
								$_FILES['file']['name'] = $file;
								$_FILES['file']['type'] = $list_file['file']['type'][$key];
								$_FILES['file']['tmp_name'] = $list_file['file']['tmp_name'][$key];
								$_FILES['file']['error'] = $list_file['file']['error'][$key];
								$_FILES['file']['size'] = $list_file['file']['size'][$key];
								handle_commodity_attachments($insert_id);
							}
						}
						$message = _l('added_successfully');
					}
					else{
						$success = false;
						$message = _l('added_fail');            
					}
				}
				echo json_encode([
					'success' => $success,
					'message' => $message
				]);
				die;
			}
		}
		/**
		 * pre order setting
		 */
		public function pre_order(){
			$data['title'] = _l('omni_pre_order_setting');

			$data['id_channel'] = 6;
			$data['channel'] = 'pre_order';        

			$this->load->model('staff_model');
			$this->load->model('clients_model');
			$this->load->model('client_groups_model');
			$this->load->model('warehouse/warehouse_model');

			$data['staff']     = $this->staff_model->get('', ['active' => 1]);
			$data['group_product'] = $this->omni_sales_model->get_group_product();

			$data['omni_customer_group'] = $this->client_groups_model->get_groups();
			$data['omni_customer'] = $this->clients_model->get();

			$data['group_items'] = $this->warehouse_model->get_commodity_group_type();
			$data['items'] = $this->warehouse_model->get_commodity_code_name();

			$data['omni_customer'] = $this->clients_model->get();


			$this->load->view('pre_order/setting', $data);
		}
/**
 * pre order default setting
 */
public function pre_order_default_setting(){
	$data = $this->input->post();
	$affected = 0;
	if(isset($data['omni_default_seller'])){
		$res = update_option('omni_default_seller', $data['omni_default_seller']);
		if($res){
			$affected++;
		}
	}
	else{
		$res = update_option('omni_default_seller', '');
		if($res){
			$affected++;
		}
	}
	if($affected != 0){
		set_alert('success', _l('omni_saved_success', _l('omni_setting')));
	}
	else{
		set_alert('danger', _l('omni_save_fail', _l('omni_setting')));
	}
	redirect(admin_url('omni_sales/pre_order')); 
}
/**
 * pre order list
 */
public function pre_order_list(){
	$this->load->model('clients_model');
	$this->load->model('invoices_model');
	$this->load->model('staff_model');
	$data['customers'] = $this->clients_model->get();
	$data['invoices'] = $this->invoices_model->get();
	$data['prefix'] = get_option('invoice_prefix');
	$data['title'] = _l('omni_pre_order_list');
	$data['staff'] = $this->staff_model->get();
	$this->load->view('pre_order/order_list', $data);
}

 /**
		 * pre order list table
		 * @return table
		 */
 public function pre_order_list_table(){
 	if ($this->input->is_ajax_request()) {
 		if($this->input->post()){
 			$this->load->model('payment_modes_model');
 			$product_filter = $this->input->post('product_filter'); 
 			$customers = $this->input->post('customers');
 			$invoices = $this->input->post('invoices');
 			$status = $this->input->post('status');

 			$end_date = $this->input->post('end_date');
 			$start_date = $this->input->post('start_date');
 			$seller = $this->input->post('seller');

 			$query = '';      

 			$select = [
 				'id',
 				'id',
 				'id',
 				'id',
 				'id',
 				'id',
 				'id'          
 			];
 			$where              = [(($query!='')?$query:'')];

 			array_push($where, 'AND channel_id = 6');
 			array_push($where, 'AND enable = 0');

 			if(isset($customers) && $customers != ''){
 				if(count($where) > 1){
 					array_push($where, 'AND userid = '.$customers);
 				}else{
 					array_push($where, 'AND userid = '.$customers);
 				}
 			}

 			if(isset($invoices) && $invoices != ''){
 				if(count($where) > 1){

 					array_push($where, 'AND number_invoice = '.$this->omni_sales_model->get_number_invoice($invoices));
 				}else{
 					array_push($where, 'AND number_invoice = '.$this->omni_sales_model->get_number_invoice($invoices));
 				}
 			}

 			if(isset($status) && $status != ''){
 				if(count($where) > 1){
 					array_push($where, 'AND status = '.$status);
 				}else{
 					array_push($where, 'AND status = '.$status);
 				}
 			}
 			if(isset($seller) && $seller != ''){
 				if(count($where) > 1){
 					array_push($where, 'AND seller = '.$seller);
 				}else{
 					array_push($where, 'AND seller = '.$seller);
 				}
 			}
 			if($end_date!='' && $start_date!=''){
 				if(!$this->omni_sales_model->check_format_date($start_date)){
 					$start_date = to_sql_date($start_date);
 				}else{
 					$start_date = $start_date;
 				}

 				if(!$this->omni_sales_model->check_format_date($end_date)){
 					$end_date = to_sql_date($end_date);
 				}else{
 					$end_date = $end_date;
 				}

 				if(count($where) > 1){
 					array_push($where, ' and date(datecreator) between \''.$start_date.'\' and \''.$end_date.'\'');
 				}else{
 					array_push($where, ' where date(datecreator) between \''.$start_date.'\' and \''.$end_date.'\'');
 				}
 			}

 			$aColumns     = $select;
 			$sIndexColumn = 'id';
 			$sTable       = db_prefix() . 'cart';
 			$join         = [];

 			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
 				'id',
 				'name',
 				'address',
 				'phone_number',
 				'voucher',
 				'status',
 				'datecreator',
 				'channel',
 				'channel_id',
 				'company',
 				'number_invoice',
 				'invoice',
 				'userid',
 				'allowed_payment_modes',
 				'order_number'
 			]);


 			$output  = $result['output'];
 			$rResult = $result['rResult'];
 			foreach ($rResult as $aRow) {
 				if($aRow['number_invoice'] != ''){
 					$id = $this->omni_sales_model->get_id_invoice($aRow['number_invoice']);
 				}
 				$row = [];
 				$row[] = $aRow['id'];              
 				$row[] = $aRow['order_number'];              
 				$row[] = $aRow['datecreator'];              
 				$row[] = $aRow['company']; 
 				$row[] = omni_get_user_group_name($aRow['userid']); 
 				$payment_mode = '';
 				if ($aRow['channel_id'] == 1 || $aRow['channel_id'] == 2 || $aRow['channel_id'] == 4) {
 					$data_multi_payment = $this->omni_sales_model->get_order_multi_payment($aRow['id']);
 					if($data_multi_payment){
 						foreach ($data_multi_payment as $key => $mtpayment) {
 							$payment_mode .= '<span class="label label-primary">'.$mtpayment['payment_name'].'</span>&nbsp;';
 						}
 					}
 					else{
 						$data_payment = $this->payment_modes_model->get($aRow['allowed_payment_modes']);
 						if($data_payment){
 							$name = isset($data_payment->name) ? $data_payment->name : '';
 							if($name !=''){
 								$payment_mode = '<span class="label label-primary">'.$name.'</span>&nbsp;';              
 							}            
 						}
 					}
 				}
 				else{
 					$this->db->where('id', $aRow['id']);
 					$data_payment = $this->db->get(db_prefix().'cart')->row();
 					if($data_payment->payment_method_title != null || $data_payment->payment_method_title != ""){
 						$payment_mode = '<span class="label label-primary">'.$data_payment->payment_method_title.'</span>&nbsp;';
 					}else{
 						$payment_mode = "";
 					}
 				} 

 				$row[] = $payment_mode;              
 				$status = get_status_by_index($aRow['status']);

 				$row[] = '<span class="label label-success">'.$status.'</span>';              

 				$option = '';
 				$option .= '<a href="' . admin_url('omni_sales/view_pre_order_detailt/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('view').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
 				$option .= '<i class="fa fa-eye"></i>';
 				$option .= '</a>';

 				if($aRow['status'] == 0){
 					$option .= '<a href="' . admin_url('omni_sales/order_manual/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('view').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
 					$option .= '<i class="fa fa-pencil"></i>';
 					$option .= '</a>';
 				}
 				$option .= '<a href="' . admin_url('omni_sales/delete_pre_order/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('delete').'" class="btn btn-danger btn-icon _delete">';
 				$option .= '<i class="fa fa-remove"></i>';
 				$option .= '</a>';    
 				$row[] = $option; 
 				$output['aaData'][] = $row;                                      
 			}
 			echo json_encode($output);
 			die();
 		}
 	}
 }

	/**
		 * pre order product list table
		 * @return table
		 */
	public function pre_order_product_list_table(){
		if ($this->input->is_ajax_request()) {
			if($this->input->post()){
				$this->load->model('departments_model');

				$customer_group_filter = $this->input->post('customer_group_filter'); 
				$customer_filter = $this->input->post('customer_filter'); 

				$select = [
					'id',
					'id',
					'id',
					'id'         
				];


				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'omni_pre_order_product_setting';
				$where        = [];
				$join         = [];



				if(isset($customer_filter)){
					$customer_query = '';
					foreach ($customer_filter as $key => $customerid) {
						$customer_query .= 'find_in_set('.$customerid.', customer) or ';
					}

					if($customer_query != ''){
						$customer_query = rtrim($customer_query, ' or ');
						array_push($where, ' AND ('.$customer_query.')');
					}
				}


				if(isset($customer_group_filter)){
					$customer_group_query = '';
					foreach ($customer_group_filter as $key => $customer_group_id) {
						$customer_group_query .= 'find_in_set('.$customer_group_id.', customer_group) or ';
					}

					if($customer_group_query != ''){
						$customer_group_query = rtrim($customer_group_query, ' or ');
						array_push($where, ' AND ('.$customer_group_query.')');
					}
				}


				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					'id',
					'group_product_id',
					'channel_id',
					'customer_group',
					'customer'
				]);


				$output  = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];
					$row[] = '<input type="checkbox" class="ckb-add-product" data-id="'.$aRow['id'].'" onchange="checked_add(this); return false;"/>';                     
					$group_product = '';

					if(is_numeric($aRow['group_product_id'])){
						$data_group = $this->omni_sales_model->get_group_product($aRow['group_product_id']);
						if($data_group){
							$group_product = $data_group->name;
						}
					}
					$row[] = $group_product;

					$list_product_id = '';
					$count_item = 0;
					$data_list_product = $this->omni_sales_model->list_product_pre_order_setting($aRow['id']);
					foreach ($data_list_product as $item) {
						$list_product_id .= $item['product_id'].',';
						$count_item++;
					}
					if($list_product_id != ''){
						$list_product_id = rtrim($list_product_id, ',');
					}
					$row[] = $count_item;

					$customer = '';
					if($aRow['customer'] != '' && $aRow['customer'] != null){
						$list_customer_id = explode(',', $aRow['customer']);
						foreach ($list_customer_id as $ck => $id) {
							if($ck == 3){
								$customer .= '<span class="label label-primary mright10" >...</span>';
								break;              
							}
							$cus_name = '';
							$data_customer = $this->clients_model->get($id);
							if($data_customer){
								$cus_name = '<span class="label label-primary mright10" >'.$data_customer->company.'</span>';
							}
							$customer .= $cus_name;
						}
					}
					$row[] = $customer;

					$customer_group = '';
					if($aRow['customer_group'] != '' && $aRow['customer_group'] != null){
						$list_customer_group_id = explode(',', $aRow['customer_group']);
						foreach ($list_customer_group_id as $ck => $id) {
							if($ck == 3){
								$customer_group .= '<span class="label label-primary mright10" >...</span>';
								break;              
							}
							$cus_name = '';
							$data_customer_group = $this->client_groups_model->get_groups($id);
							if($data_customer_group){
								$cus_name = '<span class="label label-primary mright10" >'.$data_customer_group->name.'</span>';
							}
							$customer_group .= $cus_name;
						}
					}
					$row[] = $customer_group;

					$option = '';
					$option .= '<a href="#" onclick="update_product(this);" data-groupid="'.$aRow['group_product_id'].'" data-productid="'.$list_product_id.'" data-customer_group="'.$aRow['customer_group'].'" data-customer="'.$aRow['customer'].'" data-toggle="tooltip" data-placement="top" data-title="'._l('edit').'" class="btn btn-default btn-icon" data-id="'.$aRow['id'].'" >';
					$option .= '<i class="fa fa-edit"></i>';
					$option .= '</a>';
					$option .= '<a href="' . admin_url('omni_sales/delete_pre_order_product/'. $aRow['id']) . '" data-toggle="tooltip" data-placement="top" data-title="'._l('delete').'" class="btn btn-danger btn-icon _delete">';
					$option .= '<i class="fa fa-remove"></i>';
					$option .= '</a>';
					$row[] = $option; 
					$output['aaData'][] = $row;                                      
				}
				echo json_encode($output);
				die();
			}
		}
	}



	/**
		 * delete pre order
		 * @param  int $channel 
		 * @param  int $id      
		 * @return redirect
	*/
	public function delete_pre_order($id){
		$response = $this->omni_sales_model->delete_order($id);
		if($response == true){
			set_alert('success', _l('deleted'));
		}
		else{
			set_alert('warning', _l('problem_deleting'));            
		}
		redirect(admin_url('omni_sales/pre_order_list'));
	}
/**
 * view pre order detailt
 * @param  integer $id 
 * @return integer     
 */
public function view_pre_order_detailt($id){
	$data_cart = $this->omni_sales_model->get_cart($id);
	if($data_cart){
		$data['id'] = $id;
		$this->load->model('currencies_model');
		$data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['order'] = $this->omni_sales_model->get_cart_by_order_number($data_cart->order_number);
		$data['order_detait'] = $this->omni_sales_model->get_cart_detailt_by_cart_id($data['order']->id);
		if($data['order']->number_invoice != ''){
			$data['invoice'] = $this->omni_sales_model->get_invoice($data['order']->number_invoice);
		}              
		$data['title'] = $data_cart->name;  
		$data['staffs'] = $this->omni_sales_model->get_staff();
		$this->load->view('pre_order/cart_detailt', $data); 
	} 
}
/**
 * pre order hand over
 */
public function pre_order_hand_over(){
	if($this->input->post()){
		$data =  $this->input->post();
		if($data['id'] != ''){
			$res =  $this->omni_sales_model->update_cart($data['id'], ['status' => 1, 'seller' => $data['seller']]);
			if($res == true){
				if($data['seller'] != ''){
					$email = omni_email_staff($data['seller']);
					if($email != ''){
						$url = admin_url('omni_sales/view_order_detailt/'.$data['id']);
						$link = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
						$from_name = get_staff_full_name();
						$this->omni_sales_model->notifications($data['seller'], 'omni_sales/view_order_detailt/'.$data['id'], $from_name.' '._l('omni_has_handed_over_an_order_to_you'));
						$data_send_mail->email = $email;
						$data_send_mail->to_name = get_staff_full_name($data['seller']);
						$data_send_mail->from_name = $from_name;
						$data_send_mail->link = $link;
						$template = mail_template('pre_orders_handover', 'omni_sales', $data_send_mail);
						$template->send();
					}
				}
				set_alert('success', _l('omni_handover_successful'));     
			}
			else{
				set_alert('warning', _l('omni_handover_fail'));            
			}
		}
	}
	redirect(admin_url('omni_sales/order_list'));
}
/**
 * preview inventory check
 * @param  string $order_number 
 */
public function preview_inventory_check($order_number){
	$active_convert_button = 0;
	$html = '';
	$data_cart = $this->omni_sales_model->get_cart_by_order_number($order_number);
	if($data_cart){
		$data_detail = $this->omni_sales_model->get_cart_detailt_by_master($data_cart->id);
		if($data_detail){
			foreach ($data_detail as $key => $detail) {
				$quantity = $detail['quantity'];
				$inventory_number = 0;
				$data_inventory = $this->omni_sales_model->get_quantity_inventory($detail['product_id'], '0');
				if($data_inventory){
					$inventory_number = $data_inventory->inventory_number;
				}
				
				$product = $this->omni_sales_model->get_product_by_id($detail['product_id']);
				if($product && $product->without_checking_warehouse == 1){
					$inventory_number = 1000;
				}

				$different_s = '';
				$different = $inventory_number - $quantity;
				if($different < 0){
					$different_s = '<span class="label label-danger">'.$different.'</span>';
					$active_convert_button = 1;
				}
				else{
					$different_s = '<span class="label label-success">'.$different.'</span>';
				}

				$image = '<img class="img pull-left img-thumbnail preview_inventory_check_img mright15" src="'.$this->omni_sales_model->get_image_items($detail['product_id']).'">';
				$html .= '<tr><td>'.$image.'</td><td class="middle"><span class="preview_product_name">'.$detail['product_name'].'</span></td><td class="line-height50">'.$quantity.'</td><td class="line-height50">'.$inventory_number.'</td><td class="line-height50">'.$different_s.'</td></tr>';
			}
		}
	}

	echo json_encode([
		'success' => true,
		'html' => $html,
		'active_convert_button' => $active_convert_button
	]);
	die;
}

		/**
		 * add product pre order
		 * @return redirect
		 */
		public function add_product_pre_order(){
			if($this->input->post()){
				$data = $this->input->post(); 
				if($data['id'] == ''){
					unset($data['id']);
					$res = $this->omni_sales_model->add_product_pre_order($data);
					if ($res) {
						$message = _l('added_successfully');
						set_alert('success', $message);
					}
					else{
						$message = _l('added_fail');
						set_alert('danger', $message);
					}
				}    
				else{
					$res = $this->omni_sales_model->update_product_pre_order($data);
					if ($res) {
						$message = _l('updated_successfully');
						set_alert('success', $message);
					}
					else{
						$message = _l('updated_fail');
						set_alert('danger', $message);
					}
				}    

				redirect(admin_url('omni_sales/pre_order'));            
			}
		}

		/**
		 * delete pre order product
		 * @param  int $channel 
		 * @param  int $id      
		 * @return redirect
		 */
		public function delete_pre_order_product($id){
			$response = $this->omni_sales_model->delete_pre_order_product($id);
			if($response == true){
				set_alert('success', _l('deleted'));
			}
			else{
				set_alert('warning', _l('problem_deleting'));            
			}
			redirect(admin_url('omni_sales/pre_order'));        
		}
/**
 * create purchase request
 */
public function create_purchase_request(){
	$id = $this->input->post('id');
	$response = $this->omni_sales_model->create_purchase_request($id);
	if(is_numeric($response)){
		set_alert('success', _l('omni_purchase_request_has_been_created_successfully'));
	}
	else{
		set_alert('warning', _l('omni_request_failed'));            
	}
	redirect(admin_url('omni_sales/view_order_detailt/'.$id));   
}
	
	/**
	 * clear diary sync data
	 * @return redirect 
	 */
	public function clear_diary_sync_data(){
		$response = $this->omni_sales_model->clear_diary_sync_data();

		if($response){
			set_alert('success', _l('clear_data'));
		}
		else{
			set_alert('warning', _l('clear_data'));            
		}
		redirect(admin_url('omni_sales/setting?tab=order_notificaiton'));   
	}

	/**
	 * Update prices for store products
	 * @return json
	 */
	public function woo_price_update($store_id = ''){
		$data = $this->input->post();
		$detail = isset($data["arr_val"]) ? $data["arr_val"] : null;
		if(isset($data['id'])){
			$store_id = $data["id"];
		}
		$success = $this->omni_sales_model->woo_price_update($store_id, $detail);
		echo json_encode($success);
	}

	/**
	 * Update prices for portal products
	 * @return json
	 */
	public function portal_price_update($channel = ''){
		$data = $this->input->post();
		$detail = isset($data["arr_val"]) ? $data["arr_val"] : null;
		if(isset($data['channel'])){
			$channel = $data["channel"];
		}

		$success = $this->omni_sales_model->portal_price_update($channel, $detail);
		echo json_encode($success);
	}

	/**
	* delete mass add product pre order channel
	*/
	public function delete_mass_product_pre_order_channel(){
		if($this->input->post()){
			$data = $this->input->post();
			$redirect = '';
			if(isset($data['redirect'])){
				$redirect = $data['redirect'];
				unset($data['redirect']);
			}

			$channel = $data['channel'];
			if($data['mass_delete'] == 'on'){
				$res = $this->omni_sales_model->delete_mass_product_pre_order_channel($data);
				if ($res == true) {
					$message = _l('delete_successfully');
					set_alert('success', $message);
				}
				else{
					$message = _l('delete_failed');
					set_alert('warning', $message);
				}
			}
			if($redirect != ''){
				redirect(admin_url('omni_sales/'.$redirect)); 
			}
			redirect(admin_url('omni_sales/add_product_channel/'.$channel)); 
		}
	}
	
	/**
	 * add activity
	 */
	public function wh_add_activity()
    {
    	$this->load->model('warehouse/warehouse_model');
        $goods_delivery_id = $this->input->post('goods_delivery_id');
        if ($this->input->post()) {
            $description = $this->input->post('activity');
            $rel_type = $this->input->post('rel_type');
            $aId     = $this->warehouse_model->log_wh_activity($goods_delivery_id, $rel_type, $description);
            
            if($aId){
            	$status = true;
            	$message = _l('added_successfully');
            }else{
            	$status = false;
            	$message = _l('added_failed');
            }

            echo json_encode([
            	'status' => $status,
            	'message' => $message,
            ]);
        }
    }
    /**
     * hash view public order
     * @param  integer $order_id 
     */
    public function hash_view_public_order($order_id){
    	//Old script :
    	//$hash = omni_aes_256_encrypt($order_id);
		//redirect(site_url('omni_sales/omni_sales_client/view_detail/'.urlencode($hash))); 
		$hash = '';
    	$cart = $this->omni_sales_model->get_cart($order_id);
    	if($cart && !is_array($cart)){
    		if($cart->hash != null && $cart->hash != ''){
    			$hash = $cart->hash;
    		}else{
    			$new_hash = app_generate_hash();
    			$this->db->where('id', $order_id);
    			$this->db->update(db_prefix().'cart', ['hash' => $new_hash ]);
    			if($this->db->affected_rows() > 0){
    				$hash = $new_hash;
    			}
    		}
    	}

    	if($hash != ''){
    		redirect(site_url('omni_sales/omni_sales_client/view_detail/'.$hash));
    	}else{
    		set_alert('order_not_found');
    	}
    }

    /**
	 * sales order manage order return
	 * @param  string $id 
	 * @return [type]     
	 */
	public function sales_order_manage_order_return($id = '')
	{
		$data['delivery_id'] = $id;
		$data['title'] = _l('omni_order_return_management');

		$data['from_date'] = _d(date('Y-m-d', strtotime( date('Y-m-d') . "-15 day")));
		$data['to_date'] = _d(date('Y-m-d'));
		$data['get_goods_delivery'] = $this->omni_sales_model->get_goods_delivery(false);
		$data['staffs'] = $this->omni_sales_model->get_staff();
		//display packing list not yet approval
		$data['rel_type'] = 'sales_return_order';

		$this->load->view('order_returns/manage_order_return', $data);
	}

	/**
	 * table manage packing list
	 * @return [type] 
	 */
	public function table_manage_order_return()
	{
		$this->app->get_table_data(module_views_path('omni_sales', 'order_returns/table_order_return'));
	}

		/**
	 * view order return
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function view_order_return($id)
	{
		//approval
		$send_mail_approve = $this->session->userdata("send_mail_approve");
		if ((isset($send_mail_approve)) && $send_mail_approve != '') {
			$data['send_mail_approve'] = $send_mail_approve;
			$this->session->unset_userdata("send_mail_approve");
		}
		$this->load->model('clients_model');

		$data['get_staff_sign'] = $this->omni_sales_model->get_staff_sign($id, 6);
		$data['check_approve_status'] = $this->omni_sales_model->check_approval_details($id, 6);
		$data['list_approve_status'] = $this->omni_sales_model->get_list_approval_details($id, 6);
		$data['payslip_log'] = $this->omni_sales_model->get_activity_log($id, 6);

		//get vaule render dropdown select
		$data['commodity_code_name'] = $this->omni_sales_model->get_commodity_code_name();
		$data['units_code_name'] = $this->omni_sales_model->get_units_code_name();
		$data['units_warehouse_name'] = $this->omni_sales_model->get_warehouse_code_name();

		$data['order_return_detail'] = $this->omni_sales_model->get_order_return_detail($id);
		$data['order_return'] = $this->omni_sales_model->get_order_return($id);
		$data['activity_log'] = $this->omni_sales_model->wh_get_activity_log($id,'order_return');

		$data['title'] = _l('wh_order_return');
		$check_appr = $this->omni_sales_model->get_approve_setting('6');
		$data['check_appr'] = $check_appr;
		$data['tax_data'] = $this->omni_sales_model->get_html_tax_order_return($id);
		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();
		$data['base_currency'] = $base_currency;

		$this->load->view('order_returns/view_order_return', $data);

	}


	/**
	 * order return
	 * @param  string $id                
	 * @param  string $order_retrun_type : have 3 type "manual"; "sales_return_order"; "purchasing_return_order"
	 * @return [type]                    
	 */
	public function order_return($id ='') {

		$this->load->model('clients_model');
		$this->load->model('taxes_model');
		$order_return_type = 'sales_return_order';		
		if ($this->input->post()) {
			$message = '';
			$data = $this->input->post();

			if (!$this->input->post('id')) {
				if (!has_permission('omni_order_list', '', 'create') && !is_admin()) {
					access_denied('omni_order_list');
				}
				$mess = $this->omni_sales_model->add_order_return($data, $data['rel_type']);
				if ($mess) {
					if($data['save_and_send_request'] == 'true'){
						$this->save_and_send_request_send_mail(['rel_id' => $mess, 'rel_type' => '6', 'addedfrom' => get_staff_user_id()]);
					}
					set_alert('success', _l('added_successfully'));
				} else {
					set_alert('warning', _l('added_fail'));
				}
				redirect(admin_url('omni_sales/order_list'));
			}else{
				if (!has_permission('omni_order_list', '', 'edit') && !is_admin()) {
					access_denied('omni_order_list');
				}
				$id = $this->input->post('id');
				$mess = $this->omni_sales_model->update_order_return($data, $data['rel_type'], $id);
				if($data['save_and_send_request'] == 'true'){
					$this->save_and_send_request_send_mail(['rel_id' => $id, 'rel_type' => '6', 'addedfrom' => get_staff_user_id()]);
				}
				if ($mess) {
					set_alert('success', _l('updated_successfully'));
				} else {
					set_alert('warning', _l('update_fail'));
				}
				redirect(admin_url('omni_sales/order_list'));
			}
		}
		//get vaule render dropdown select
		$data['title'] = _l('omni_add_order_return');
		$data['taxes'] = $this->taxes_model->get();
		
		$data['currency'] = get_base_currency();
        //sample
		$order_return_row_template = $this->omni_sales_model->create_order_return_row_template($order_return_type);
		$data['clients'] = $this->clients_model->get();

		$data['staffs'] = $this->omni_sales_model->get_staff();
		$data['current_day'] = date('Y-m-d');


		//edit note after approval
		$data['order_return_row_template'] = $order_return_row_template;
		$data['order_return_type'] = $order_return_type;
		$this->load->view('order_returns/add_edit_order_return', $data);

	}

	/**
	 * order return get item data
	 * @param  string $delivery_id 
	 * @return [type]              
	 */
	public function order_return_get_item_data($rel_id = 0, $rel_type = '')
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->omni_sales_model->omni_sale_detail_order_return($rel_id);
			if($data){
				$currency = get_base_currency()->id;
				if(is_numeric($data['currency']) && $data['currency'] > 0){
					$currency = $data['currency'];
				}
				$result = [
					'company_id' => $data['company_id'] ? $data['company_id'] : '',
					'email' => $data['email'] ? $data['email'] : '',
					'phonenumber' => $data['phonenumber'] ? $data['phonenumber'] : '',
					'order_number' => $data['order_number'] ? $data['order_number'] : '',
					'order_date' => $data['order_date'] ? $data['order_date'] : '',
					'number_of_item' => $data['number_of_item'] ? $data['number_of_item'] : '',
					'order_total' => $data['order_total'] ? $data['order_total'] : '',
					'datecreated' => $data['datecreated'] ? $data['datecreated'] : '',
					'additional_discount' => $data['additional_discount'] ? $data['additional_discount'] : '',
					'main_additional_discount' => $data['main_additional_discount'] ? $data['main_additional_discount'] : '',
					'total_item_qty' => $data['total_item_qty'] ? $data['total_item_qty'] : '',
					'result' => $data['result'] ? $data['result'] : '',
					'currency' => $currency
				];
				echo json_encode($result);
				die;
			}
		}
	}

	/**
	 * change status return order
	 * @return json 
	 */
	public function change_status_return_order(){
		if($this->input->post()){
			$data = $this->input->post();
			$success = false;
			$message = '';

			$success = $this->omni_sales_model->change_status_return_order($data);
			if($success){
				if($data['status'] == 1){
					$message = _l('omni_order_accepted');				
				}
				else{
					$message = _l('omni_order_has_been_rejected');					
				}
			}
			else{
				$message = _l('omni_order_approval_failed');
			}

			echo json_encode([
				'success' => $success,
				'message' => $message
			]);
		}
	}
	/**
	 * cancel invoice
	 * @param  integer $order_id          
	 * @param  integer $original_order_id 
	 */
	public function cancel_invoice($order_id, $original_order_id){
		$order_data = $this->omni_sales_model->get_cart($original_order_id);
		if($order_data){
			$response = $this->omni_sales_model->cancel_invoice($order_id, $order_data->number_invoice);
			if($response){
				set_alert('success', _l('omni_canceled_successfully'));
			}
			else{
				set_alert('warning', _l('omni_cancel_failed'));            
			}
		}
		redirect(admin_url('omni_sales/view_order_detailt/'.$order_id));        
	}

	/**
	 * update invoice
	 * @param  integer $order_id          
	 * @param  integer $original_order_id 
	 */
	public function update_invoice($order_id, $original_order_id){
		$order_data =$this->omni_sales_model->get_cart($original_order_id);
		if($order_data && $order_data->number_invoice){
			$response = $this->omni_sales_model->update_invoice($order_id, $order_data->number_invoice);
			if($response == true){
				set_alert('success', _l('omni_updated_successfully'));
			}
			else{
				set_alert('warning', _l('omni_update_failed'));            
			}
		}

		redirect(admin_url('omni_sales/view_order_detailt/'.$order_id));        
	}

	/**
	 * create import stock
	 * @param  integer $order_id     
	 * @param  integer $warehouse_id 
	 */
	public function create_import_stock($order_id, $warehouse_id){
		$response = $this->omni_sales_model->create_import_stock($order_id, $warehouse_id);
		if($response == true){
			set_alert('success', _l('omni_created_successfully'));
		}
		else{
			set_alert('warning', _l('omni_create_failed'));            
		}
		redirect(admin_url('omni_sales/view_order_detailt/'.$order_id));        
	}

	/**
	 * get refund modal content
	 * @param  integer $id        
	 * @param  integer $refund_id 
	 */
	 public function get_refund_modal_content($order_id, $refund_id = null)
	 {
	 	$this->load->model('payment_modes_model');
	 	$max = 0;
	 	 $data['payment_modes'] = $this->payment_modes_model->get('', [
                    'expenses_only !=' => 1,
                ]);
	 	$order = $this->omni_sales_model->get_cart($order_id);
	 	if($order){
	 		$total_s = $order->total;
	 		if($total_s < 0){
	 			$total_s = 0;
	 		}
	 		$total_refund = omni_get_total_refund($order_id);
	 		$max = $total_s - $total_refund;
	 		if ($refund_id) {
	 			$data['refund']    = $this->omni_sales_model->get_refund($refund_id);
	 		}
	 	}

	 	$data['max'] = $max;
	 	$this->load->view('order_list/refund_modal_content', $data);
	 }
	 /**
	  * add edit refund
	  */
	 public function add_edit_refund(){
	 	if($this->input->post()){
	 		$data = $this->input->post();
	 		if($data['id'] == ''){
	 			unset($data['id']);
	 			$insert_id = $this->omni_sales_model->create_refund($data);
	 			if($insert_id) {
	 				set_alert('success', _l('omni_created_successfully'));
	 			}
	 			else 
	 			{
	 				set_alert('danger', _l('omni_create_failed'));
	 			}
	 			redirect(admin_url('omni_sales/view_order_detailt/'.$data['order_id']));    
	 		}
	 		else{
	 			$response = $this->omni_sales_model->edit_refund($data);
	 			if($response == true){
	 				set_alert('success', _l('omni_updated_successfully'));
	 			}
	 			else{
	 				set_alert('danger', _l('omni_update_failed'));            
	 			}
	 			redirect(admin_url('omni_sales/view_order_detailt/'.$data['order_id']));    
	 		}
	 	}
	 	redirect(admin_url('omni_sales/order_list'));    
	 }

	 /**
	  * table refund
	  */
	 public function table_refund(){
		$this->app->get_table_data(module_views_path('omni_sales', 'table/table_refund'));
	 }

	 /**
	  * delete refund
	  * @param  integer $refund_id 
	  * @param  integer $order_id  
	  */
	 public function delete_refund($refund_id, $order_id){
	 	if (!has_permission('omni_order_list', '', 'delete') && !is_admin()) {
			access_denied('omni_refund');
		}
	 	$response = $this->omni_sales_model->delete_refund($refund_id);
	 	if($response == true){
	 		set_alert('success', _l('deleted'));
	 	}
	 	else{
	 		set_alert('warning', _l('problem_deleting'));            
	 	}
	 	redirect(admin_url('omni_sales/view_order_detailt/'.$order_id));    
	 }

	 public function order_list_get_estimate_data($estimate_id = '') {

	 	$invoices_detail = $this->omni_sales_model->order_list_get_estimate_data($estimate_id);
	 	echo json_encode([
	 		'cart_detail' => $invoices_detail['cart_detail'],
	 		'cart' => $invoices_detail['cart'],
	 		'status' => $invoices_detail['status'],
	 	]);
	 }

	/**
	 * check approval sign
	 * @return json 
	 */
	public function check_create_delivery_note() 
	{
		$data = $this->input->post();

		$success = true;
		$message = '';

		/*check send request with type =2 , inventory delivery voucher*/
		$check_r = $this->omni_sales_model->check_inventory_delivery_voucher($data);

		if($check_r['flag_export_warehouse'] == 1){
			$message = 'approval success';
		}else{
			$message = $check_r['str_error'];
			$success = false;
		}

		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
		die;
	}

	/**
	 * create export stock ajax
	 * @return [type] 
	 */
	public function create_export_stock_ajax(){
		$orderid = $this->input->post('orderid');
		$success = $this->omni_sales_model->create_export_stock($orderid, 2);

		$status = false;
		$message = '';
		if ($success) {
			$message = _l('create_successfully');
			$status = true;
		}

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
		die;
	}


}
