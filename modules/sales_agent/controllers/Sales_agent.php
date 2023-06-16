<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * SALES AGENT
 */
class Sales_agent extends AdminController
{
	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('Sales_agent_model');
        hooks()->do_action('sales_agent_init');
	}


	/**
	 * { management }
	 */
	public function management(){


        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        $data['groups']         = $this->clients_model->get_groups();
        $data['title']          = _l('sales_agent');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->clients_model->get_customers_admin_unique_ids();

    
        $whereContactsLoggedIn = ' AND userid IN (SELECT userid FROM ' . db_prefix() . 'clients WHERE client_type = "agent")';
        

        $data['contacts_logged_in_today'] = $this->clients_model->get_contacts('', 'last_login LIKE "' . date('Y-m-d') . '%"' . $whereContactsLoggedIn);

        $data['countries'] = $this->clients_model->get_clients_distinct_countries();

        $this->load->view('management/manage', $data);
	}

	/**
	 * { table }
	 */
	public function table()
    {

        $this->app->get_table_data(module_views_path('sales_agent', 'management/table_sales_agent'));
    }

	/* Edit client or add new client*/
    public function sale_agent($id = '')
    {

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
            

                $data = $this->input->post();

                $save_and_add_contact = false;
                if (isset($data['save_and_add_contact'])) {
                    unset($data['save_and_add_contact']);
                    $save_and_add_contact = true;
                }
                $data['client_type'] = 'agent';
                $id = $this->clients_model->add($data);
          
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('client')));
                    if ($save_and_add_contact == false) {
                        redirect(admin_url('sales_agent/sale_agent/' . $id));
                    } else {
                        redirect(admin_url('sales_agent/sale_agent/' . $id . '?group=contacts&new_contact=true'));
                    }
                }
            } else {
                 $success = $this->clients_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('agent')));
                }
                redirect(admin_url('sales_agent/sale_agent/' . $id));
            }
        }

        $group         = !$this->input->get('group') ? 'profile' : $this->input->get('group');
        $data['group'] = $group;

        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {
            redirect(admin_url('sales_agent/sale_agent/' . $id . '?group=contacts&contactid=' . $contact_id));
        }

        // Customer groups
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

            if (!$data['tab']) {
                show_404();
            }

            // Fetch data based on groups
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

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
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

        $this->load->view('sale_agent/sale_agent', $data);
    }

    /**
     * { programs }
     */
    public function programs(){
    	$data['title'] = _l('agent_programs');

    	$data['agent_groups'] = $this->client_groups_model->get_groups();
    	$data['agents'] = $this->Sales_agent_model->get_agents();

    	$this->load->view('programs/manage', $data);
    }

    /**
     * { program table }
     */
    public function program_table(){
    	$this->app->get_table_data(module_views_path('sales_agent', 'programs/table_programs'));
    }

    /**
     * { agent programs }
     */
    public function agent_program($id = ''){

    	if($this->input->post()){
    		$_data = $this->input->post();
     		if($id == ''){
     			$insert_id = $this->Sales_agent_model->add_program($_data);

     			if($insert_id){
     				set_alert('success', _l('added_successfully'));
     			}
    		}else{
    			$success = $this->Sales_agent_model->update_program($id, $_data);
    			if($success){
    				set_alert('success', _l('updated_successfully'));
    			}
    		}

    		redirect(admin_url('sales_agent/programs'));
    	}

    	if($id == ''){
    		$data['title'] = _l('add_program');

    		
	            $data['items'] = $this->Sales_agent_model->sa_get_grouped('can_be_sold');
	       

    	}else{
    		$data['title'] = _l('edit_program');
    		$data['agent_program'] = $this->Sales_agent_model->get_program($id);
    		$data['program_detail'] = $this->Sales_agent_model->get_program_detail($id);
    		$data['title'] = _l('edit_program');

    	
	        $data['items'] = $this->Sales_agent_model->sa_get_grouped('can_be_sold');
	       
    	}

    	$data['agent_groups'] = $this->Sales_agent_model->get_agent_groups();
    	$data['agents'] = $this->Sales_agent_model->get_agents();

    	
        $data['commodity_groups'] = $this->Sales_agent_model->get_item_groups();
    

    	$this->load->view('programs/program', $data);
    	
    }

    /**
     * { group item change }
     */
    public function group_it_change($group = ''){
        if($group != ''){
            $html = '';
            if (total_rows(db_prefix() . 'items', [ 'group_id' => $group ]) <= ajax_on_total_items()) {
                $list_items = $this->Sales_agent_model->get_item_by_group($group);
                if(count($list_items) > 0){
                    foreach($list_items as $item){
                        $html .= '<option value="'.$item['id'].'" selected>'.$item['commodity_code'].' - '.$item['description'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }else{

            $html = '';
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $items = $this->Sales_agent_model->get_item();
                if(count($items) > 0){
                    foreach($items as $it){
                        $html .= '<option value="'.$it['id'].'">'.$it['commodity_code'].' - '.$it['description'].'</option>';
                    }
                }
            }

            echo json_encode([
                'html' => $html,
            ]);
        }   

    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function commodity_code_search($type = 'rate', $can_be = 'can_be_sold')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->Sales_agent_model->commodity_code_search($this->input->post('q'), $type, $can_be, false));
        }
    }

    /**
     * wh commodity code search
     * @return [type] 
     */
    public function commodity_code_search_vendor_item($type = 'rate', $can_be = 'can_be_sold', $group = '')
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->Sales_agent_model->commodity_code_search($this->input->post('q'), $type, $can_be, false,$group));
        }
    }

    /**
     * { delete_program }
     */
    public function delete_program($id){
    	if(!$id){
    		redirect(admin_url('sales_agent/programs'));
    	}

    	$success = $this->Sales_agent_model->delete_program($id);
    	if($success){
    		set_alert('success', 'deleted_successfully');
    	}

    	redirect(admin_url('sales_agent/programs'));
    }

    /**
     * { program detail }
     */
    public function program_detail($id){
    	$data['program'] = $this->Sales_agent_model->get_program($id);
    	$data['program_detail'] = $this->Sales_agent_model->get_program_detail($id);
    	$data['title'] = $data['program']->name;

    	$this->load->view('programs/program_detail', $data);
    }

    /**
     * { table program client }
     */
    public function table_program_client($program_id){
    	$this->app->get_table_data(module_views_path('sales_agent', 'programs/table_program_clients'), ['program_id' => $program_id]);
    }

    /* Delete client */
    public function delete_sale_agent($id)
    {
        if (!has_permission('customers', '', 'delete')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('sales_agent/management'));
        }
        $response = $this->clients_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('agent_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('agent')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('agent')));
        }
        redirect(admin_url('sales_agent/management'));
    }

    /**
     * { table program items }
     */
    public function table_program_items($program_id){
    	$this->app->get_table_data(module_views_path('sales_agent', 'programs/table_program_items'), ['program_id' => $program_id]);
    }

    /**
     * { join program requests }
     */
    public function join_program_requests($program_id){
    	$data['program_id'] = $program_id;
    	$data['program'] = $this->Sales_agent_model->get_program($program_id);
    	$data['program_detail'] = $this->Sales_agent_model->get_program_detail($program_id);
    	$data['requests'] = $this->Sales_agent_model->get_join_program_request($program_id);

    	$data['title'] = _l('join_requests');

    	$this->load->view('programs/join_requests', $data);
    }

    /**
     * { approve join program request }
     */
    public function approve_join_program_request($program_id, $request_id, $status){
    	$success = $this->Sales_agent_model->approve_join_request($request_id, $status);
    	if($success){
    		if($status == 'approved'){
    			set_alert('success', _l('approved_request_successfully'));
    		}else{
    			set_alert('success', _l('rejected_request_successfully'));
    		}
    	}
    	redirect(admin_url('sales_agent/join_program_requests/'.$program_id));
    }

    /**
     * { settings }
     */
    public function settings(){

        $data['group'] = $this->input->get('group');
        $data['unit_tab'] = $this->input->get('tab');

        $data['title']                 = _l('setting');
       

        $data['tab'][] = 'currency_rates';

        if($data['group'] == ''){
            $data['group'] = 'currency_rates';
        }

        if($data['group'] == 'currency_rates'){
            $this->load->model('currencies_model');
            $this->Sales_agent_model->check_auto_create_currency_rate();

            $data['currencies'] = $this->currencies_model->get();
            if($data['unit_tab'] == ''){
                $data['unit_tab'] = 'general';
            }
        }


        $data['tabs']['view'] = 'settings/includes/'.$data['group'];
       
        $this->load->view('settings/manage', $data);
    }


    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'settings/includes/currencies/currency_rate_table'));
    }

    /**
     * update automatic conversion
     */
    public function update_setting_currency_rate(){
        $data = $this->input->post();
        $success = $this->Sales_agent_model->update_setting_currency_rate($data);
        if($success == true){
            $message = _l('updated_successfully', _l('setting'));
            set_alert('success', $message);
        }
        redirect(admin_url('sales_agent/settings?group=currency_rates'));
    }

    /**
     * Gets all currency rate online.
     */
    public function get_all_currency_rate_online()
    {
        $result = $this->Sales_agent_model->get_all_currency_rate_online();
        if($result){
            set_alert('success', _l('updated_successfully', _l('pur_currency_rates')));
        }
        else{
            set_alert('warning', _l('no_data_changes', _l('pur_currency_rates')));                  
        }

        redirect(admin_url('sales_agent/settings?group=currency_rates'));
    }

    /**
     * currency rate table
     * @return [type] 
     */
    public function currency_rate_logs_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'settings/includes/currencies/currency_rate_logs_table'));
    }

    /**
     * delete currency
     * @param  [type] $id 
     * @return [type]     
     */
    public function delete_currency_rate($id){
        if($id != ''){
            $result =  $this->Sales_agent_model->delete_currency_rate($id);
            if($result){
                set_alert('success', _l('deleted_successfully', _l('pur_currency_rates')));
            }
            else{
                set_alert('danger', _l('deleted_failure', _l('pur_currency_rates')));                   
            }
        }
        redirect(admin_url('sales_agent/settings?group=currency_rates'));
    }

    /**
     * { orders }
     */
    public function orders(){
    	$data['title'] = _l('sa_orders');
    	$data['agents'] = $this->Sales_agent_model->get_agents();

    	$this->load->view('orders/manage', $data);
    }


    /**
     * currency rate table
     * @return [type] 
     */
    public function orders_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'orders/table_orders'));
    }

    /**
     * { order_detail }
     */
    public function order_detail($id){
    	$data['estimate'] = $this->Sales_agent_model->get_pur_order($id);

        if(!$data['estimate']){
            show_404();
        }

        if($data['estimate']->approve_status != 2){
            show_404();
        }

        $data['title'] = $data['estimate']->order_number;

        $data['tab'] = $this->input->get('tab');
        if($data['tab'] == ''){
            $data['tab'] = 'tab_estimate';
        }

        $data['pur_order_attachments'] = $this->Sales_agent_model->get_purchase_order_attachments($id);
        $data['estimate_detail'] = $this->Sales_agent_model->get_pur_order_detail($id);
        $data['tax_data'] = $this->Sales_agent_model->get_html_tax_pur_order($id);

        if(sa_get_status_modules('warehouse') == 1 && $data['estimate']->invoice_id > 0 && $data['estimate']->stock_export_id > 0){
            $this->load->model('warehouse/warehouse_model');
            $this->load->model('invoices_model');

            $data['cart'] = $data['estimate'];
            $shipment = $this->Sales_agent_model->get_shipment_by_order($id); 

            if($shipment){
                $data['shipment'] = $shipment;
                $data['order_id']          = $id;

                $data['invoice'] = $this->invoices_model->get($data['cart']->invoice_id);

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
                if(is_numeric($data['cart']->stock_export_id)){
                    $this->db->where('id', $data['cart']->stock_export_id);
                    $data['goods_delivery'] = $this->db->get(db_prefix() . 'goods_delivery')->result_array();
                    $data['packing_lists'] = $this->warehouse_model->get_packing_list_by_deivery_note($data['cart']->stock_export_id);
                }
            }
        }

        $this->load->view('orders/order_detail', $data);
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_order_attachment($id){

        sa_handle_purchase_order_file($id);

        redirect(admin_url('sales_agent/order_detail/'.$id));
    }

    /**
     * { file_purorder }
     */
    public function file_pur_order($id, $rel_id){


        $data['file'] = $this->Sales_agent_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('orders/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purorder_attachment($id)
    {
        $this->load->model('misc_model');
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo html_entity_decode($this->Sales_agent_model->delete_purorder_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /**
     * { update delivery status }
     *
     * @param      <type>  $pur_order  The pur order
     * @param      <type>  $status     The status
     */
    public function mark_pur_order_as( $status, $pur_order){
        
        $this->db->where('id', $pur_order);
        $this->db->update(db_prefix().'sa_pur_orders', ['order_status' => $status]);
        if ($this->db->affected_rows() > 0) {
            if($status = 'delivered'){
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix().'sa_pur_orders', ['delivery_status' => 1, 'delivery_date' => date('Y-m-d')]);
            }else{
                $this->db->where('id', $pur_order);
                $this->db->update(db_prefix().'sa_pur_orders', ['delivery_status' => 0]);
            }

            set_alert('success', _l('updated_successfully', _l('order_status')));
        }

        redirect(admin_url('sales_agent/order_detail/' . $pur_order));
    }

    /**
     * Creates an invoice.
     *
     * @param        $order_id  The order identifier
     */
    public function create_invoice($order_id){
    	$invoice_id = $this->Sales_agent_model->create_invoice_for_order($order_id);
    	if($invoice_id){
    		set_alert('successs', _l('create_invoice_successfully'));
    	}

    	redirect(admin_url('sales_agent/order_detail/' . $order_id));
    }

    /**
     * { item detail }
     */
    public function item_detail($item_id){

    	$data['id'] = $item_id;
        $data['item'] = $this->Sales_agent_model->get_item($item_id);
        $data['item_file'] = $this->Sales_agent_model->get_item_attachments($item_id);
        $data['title'] =  $data['item']->description;

    	$this->load->view('item_detail', $data);
    }

    /**
     * Creates an export stock.
     */
    public function create_export_stock($invoice_id, $order_id){

        if(sa_get_status_modules('warehouse')){
            $this->load->model('warehouse/warehouse_model');

            $check_inventory = $this->Sales_agent_model->check_inventory_delivery_voucher($order_id);

            if($check_inventory['flag_export_warehouse'] == 1){
                $success = $this->warehouse_model->auto_create_goods_delivery_with_invoice($invoice_id);

                if($success == true){
                    set_alert('success', _l('create_export_stock_successfully'));
                }
            }else{
                set_alert('warning', $check_inventory['str_error']);
            }
        }else{
            set_alert('warning', _l('please_active_warehouse_module_to_use_this_function'));
        }

        redirect(admin_url('sales_agent/order_detail/'.$order_id ));
    }

    /**
     * { dashboard }
     */
    public function dashboard(){
        $data['title'] = _l('sa_dashboard');



        $this->load->view('dashboard/index', $data);
    }

    /**
     * { table orders value dashboard }
     */
    public function table_orders_value_dashboard(){
        $this->app->get_table_data(module_views_path('sales_agent', 'dashboard/table_orders_value_by_agent'));
    }
}