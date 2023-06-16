<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a purchase model.
 */
class Sales_agent_model extends App_Model
{   

	/**
	 * Constructs a new instance.
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Gets the program.
     */
    public function get_program($id = ''){
    	if(is_numeric($id)){
    		$this->db->where('id', $id);
    		return $this->db->get(db_prefix().'sa_programs')->row();

    	}else{
    		return $this->db->get(db_prefix().'sa_programs')->result_array();
    	}
    }

    /**
     * Gets the program detail.
     *
     * @param      <type>  $program_id  The program identifier
     */
    public function get_program_detail($program_id){
    	$this->db->where('program_id', $program_id);
    	return $this->db->get(db_prefix().'sa_program_detail')->result_array();
    }

    /**
     * Gets the agent groups.
     */
    public function get_agent_groups(){
    	return $this->db->get(db_prefix().'customers_groups')->result_array();
    }

    /**
     * Gets the agents.
     */
    public function get_agents(){
    	$this->db->where('client_type', 'agent');
    	$this->db->where('active', 1);
    	return $this->db->get(db_prefix().'clients')->result_array();
    }

    /**
     * wh get grouped
     * @return [type] 
     */
    public function pur_get_grouped($can_be = '', $search_all = false)
    {
        $items = [];
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get(db_prefix() . 'items_groups')->result_array();

        array_unshift($groups, [
            'id'   => 0,
            'name' => '',
        ]);

        foreach ($groups as $group) {
            $this->db->select('*,' . db_prefix() . 'items_groups.name as group_name,' . db_prefix() . 'items.id as id');
            if(strlen($can_be) > 0){
                $this->db->where(db_prefix().'items.can_be_sold', $can_be);
            }

            $this->db->where('group_id', $group['id']);
            $this->db->where(db_prefix().'items.active', 1);
            $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
            $this->db->order_by('description', 'asc');

            $_items = $this->db->get(db_prefix() . 'items')->result_array();

            if (count($_items) > 0) {
                $items[$group['id']] = [];
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * get commodity group add commodity
     * @return array
     */
    public function get_item_groups()
    {

        return $this->db->query('select * from tblitems_groups where display = 1 order by tblitems_groups.order asc ')->result_array();
    }

    /**
     * { purchase commodity code search }
     *
     * @param        $q           The quarter
     * @param        $type        The type
     * @param      string  $can_be      Indicates if be
     * @param      bool    $search_all  The search all
     */
    public function commodity_code_search($q, $type, $can_be = '', $search_all = false, $group = ''){
        $this->db->select('rate, id, description as name, long_description as subtext, commodity_code');
        
        $this->db->group_start();
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);
        $this->db->or_like('commodity_code', $q);
        $this->db->or_like('sku_code', $q);
        $this->db->group_end();
        if(strlen($can_be) > 0){
            $this->db->where($can_be, $can_be);
        }
        $this->db->where('active', 1);


        if($group != ''){
            $this->db->where('group_id', $group);
        }

        $this->db->order_by('id', 'desc');
        $this->db->limit(500);

        $items = $this->db->get(db_prefix() . 'items')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
            if($type == 'rate'){
                $items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' .$item['commodity_code'];
            }else{
                $items[$key]['name']    = $item['commodity_code'] .' '.$item['name'];
            }

        }

        return $items;
    }

    /**
     * Adds a program.
     *
     * @param        $data   The data
     */
    public function add_program($data){

    	$data['from_date'] = to_sql_date($data['from_date']);
    	$data['to_date'] = to_sql_date($data['to_date']);

    	$data['created_at'] = date('Y-m-d H:i:s');
    	$data['created_by'] = get_staff_user_id();
    	if(isset($data['indefinite'])){
    		$data['indefinite'] = 1;
    	}else{
            $data['indefinite'] = 0;
        }

        $product_group = [];
    	if(isset($data['product_group']) && count($data['product_group']) > 0){
            $product_group = $data['product_group'];
            unset($data['product_group']);
    	}


        $product = [];
    	if(isset($data['product']) && count($data['product']) > 0){
            $product = $data['product'];
            unset($data['product']);
    	}

    	if(isset($data['agent']) && count($data['agent']) > 0){
    		$data['agent'] = implode(',', $data['agent']);
    	}

    	if(isset($data['agent_group']) && count($data['agent_group']) > 0){
    		$data['agent_group'] = implode(',', $data['agent_group']);
    	}

        if(isset($data['agent_can_view']) && count($data['agent_can_view']) > 0){
            $data['agent_can_view'] = implode(',', $data['agent_can_view']);
        }

        if(isset($data['agent_group_can_view']) && count($data['agent_group_can_view']) > 0){
            $data['agent_group_can_view'] = implode(',', $data['agent_group_can_view']);
        }

    	$from_amount = [];
    	if(isset($data['from_amount']) ){
    		$from_amount = $data['from_amount'];
    		unset($data['from_amount']);
    	}

    	$to_amount = [];
    	if(isset($data['to_amount']) ){
    		$to_amount = $data['to_amount'];
    		unset($data['to_amount']);
    	}

    	$discount = [];
    	if(isset($data['discount']) ){
    		$discount = $data['discount'];
    		unset($data['discount']);
    	}

    	$this->db->insert(db_prefix().'sa_programs', $data);
    	$insert_id = $this->db->insert_id();
    	if($insert_id){

    		if(count($from_amount) > 0){
    			foreach($from_amount as $key => $f_amount){
    				$this->db->insert(db_prefix().'sa_program_detail',[
                        'product_group' => implode(',', $product_group[$key]),
                        'product' => implode(',', $product[$key]),
    					'program_id' => $insert_id,
    					'from_amount' => $f_amount,
    					'to_amount' => $to_amount[$key],
    					'discount' => $discount[$key],
    				]);
    			}
    		}

    		return $insert_id;
    	}
    	return false;

    }

    /**
     * { update_program }
     */
    public function update_program($id, $data){
    	$rs = 0;
    	$data['from_date'] = to_sql_date($data['from_date']);
    	$data['to_date'] = to_sql_date($data['to_date']);

    	if(isset($data['indefinite'])){
            $data['indefinite'] = 1;
        }else{
            $data['indefinite'] = 0;
        }

    	$product_group = [];
        if(isset($data['product_group']) && count($data['product_group']) > 0){
            $product_group = $data['product_group'];
            unset($data['product_group']);
        }

        $product = [];
        if(isset($data['product']) && count($data['product']) > 0){
            $product = $data['product'];
            unset($data['product']);
        }

    	if(isset($data['agent']) && count($data['agent']) > 0){
    		$data['agent'] = implode(',', $data['agent']);
    	}

    	if(isset($data['agent_group']) && count($data['agent_group']) > 0){
    		$data['agent_group'] = implode(',', $data['agent_group']);
    	}

        if(isset($data['agent_can_view']) && count($data['agent_can_view']) > 0){
            $data['agent_can_view'] = implode(',', $data['agent_can_view']);
        }

        if(isset($data['agent_group_can_view']) && count($data['agent_group_can_view']) > 0){
            $data['agent_group_can_view'] = implode(',', $data['agent_group_can_view']);
        }

    	$from_amount = [];
    	if(isset($data['from_amount']) ){
    		$from_amount = $data['from_amount'];
    		unset($data['from_amount']);
    	}

    	$to_amount = [];
    	if(isset($data['to_amount']) ){
    		$to_amount = $data['to_amount'];
    		unset($data['to_amount']);
    	}

    	$discount = [];
    	if(isset($data['discount']) ){
    		$discount = $data['discount'];
    		unset($data['discount']);
    	}
    	
    	$this->db->where('program_id', $id);
    	$this->db->delete(db_prefix().'sa_program_detail');

        
    	if(count($from_amount) > 0){
			foreach($from_amount as $key => $f_amount){
				$this->db->insert(db_prefix().'sa_program_detail', [
                    'product_group' => (isset($product_group[$key]) && count($product_group[$key]) > 0) ? implode(',', $product_group[$key]) : '',
                    'product' => (isset($product[$key]) && count($product[$key]) > 0) ? implode(',', $product[$key]) : '',
					'program_id' => $id,
					'from_amount' => $f_amount,
					'to_amount' => $to_amount[$key],
					'discount' => $discount[$key],
				]);

				$insert_id = $this->db->insert_id();
				if($insert_id){
					$rs ++;
				}
			}
		}
        

		$this->db->where('id', $id);
    	$this->db->update(db_prefix().'sa_programs', $data);
    	if($this->db->affected_rows() > 0){
    		$rs++;
    	}

    	if($rs > 0){
    		return true;
    	}
    	return false;
    }

    /**
     * { delete_program }
     *
     * @param        $id     The identifier
     */
    public function delete_program($id){
    	$this->db->where('id', $id);
    	$this->db->delete(db_prefix().'sa_programs');
    	if($this->db->affected_rows() > 0){
    		$this->db->where('program_id', $id);
    		$this->db->delete(db_prefix().'sa_program_detail');
    		return true;
    	}
    	return false;
    }

    /**
     * { function_description }
     */
    public function join_program_request($program_id, $agent_id){
        $this->db->insert(db_prefix().'sa_join_program_request',[
            'program_id' => $program_id,
            'agent_id' => $agent_id,
            'status' => 'new',
        ]);

        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * Gets the join program request.
     */
    public function get_join_program_request($program_id){
        $this->db->where('program_id', $program_id);
        return $this->db->get(db_prefix().'sa_join_program_request')->result_array();
    }

    /**
     * { approve join request }
     *
     * @param        $request_id  The request identifier
     * @param      string  $status      The status
     */
    public function approve_join_request($request_id, $status){

        $this->db->where('id', $request_id);
        $request = $this->db->get(db_prefix().'sa_join_program_request')->row();
        $rs = 0;
        if($status == 'approved'){
            $this->db->where('id', $request_id);
            $this->db->update(db_prefix().'sa_join_program_request', ['status' => $status]);
            if($this->db->affected_rows() > 0){
                $program = $this->get_program($request->program_id);
                if($program->agent == '' || $program->agent == null){
                    $this->db->where('id', $request->program_id);
                    $this->db->update(db_prefix().'sa_programs', ['agent' => $request->agent_id]);
                }else{
                    $agent_str = $program->agent.','.$request->agent_id;
                    $this->db->where('id', $request->program_id);
                    $this->db->update(db_prefix().'sa_programs', ['agent' => $agent_str]);
                }
                $rs++;
            }

        }else{
            $this->db->where('id', $request_id);
            $this->db->delete(db_prefix().'sa_join_program_request');
            if($this->db->affected_rows() > 0){
                $rs++;
            }
        }
        if($rs > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the sa client.
     */
    public function get_sa_client($id = ''){
        if(is_numeric($id)){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'sa_clients')->row();
        }

        $this->db->where('agent_id', get_sale_agent_user_id());
        return $this->db->get(db_prefix().'sa_clients')->result_array();
    }

    /**
     * Adds a client.
     */
    public function add_client($data){
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_sa_contact_user_id();
        $data['agent_id'] = get_sale_agent_user_id();

        $this->db->insert(db_prefix().'sa_clients', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
        return false;
    }

    /**
     * Adds a client.
     */
    public function update_client($id, $data){
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'sa_clients', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the agent approval setting.
     */
    public function get_agent_approval_setting($agent_id, $id = ''){
        $this->db->where('agent_id', $agent_id);
        if(is_numeric($id)){
            $this->db->where('id', $id);
            return $this->db->get(db_prefix().'sa_approval_setting')->row();
        }

        return $this->db->get(db_prefix().'sa_approval_setting')->result_array();
    }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array  $where       perform where in query
     * @return array
     */
    public function get_contacts($customer_id = '', $where = ['active' => 1])
    {
        $this->db->select('*, CONCAT(firstname,\' \',lastname) as full_name');
        $this->db->where($where);
        if ($customer_id != '') {
            $this->db->where('userid', $customer_id);
        }

        $this->db->order_by('is_primary', 'DESC');

        return $this->db->get(db_prefix() . 'contacts')->result_array();
    }


    /**
     * Adds an approval setting.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function add_approval_setting($data)
    {
        unset($data['approval_setting_id']);

        if(isset($data['approver'])){
            $setting = [];
            foreach ($data['approver'] as $key => $value) {
                $node = [];
                $node['approver'] = $data['approver'][$key];
                $node['staff'] = $data['staff'][$key];
                $node['action'] = $data['action'][$key];

                $setting[] = $node;
            }
            unset($data['approver']);
            unset($data['staff']);
            unset($data['action']);
        }
        $data['setting'] = json_encode($setting);

        $this->db->insert(db_prefix() .'sa_approval_setting', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return true;
        }
        return false;
    }

    /**
     * { edit approval setting }
     *
     * @param      <type>   $id     The identifier
     * @param      <type>   $data   The data
     *
     * @return     boolean  
     */
    public function edit_approval_setting($id, $data)
    {
        unset($data['approval_setting_id']);

        if(isset($data['approver'])){
            $setting = [];
            foreach ($data['approver'] as $key => $value) {
                $node = [];
                $node['approver'] = $data['approver'][$key];
                $node['staff'] = $data['staff'][$key];
                $node['action'] = $data['action'][$key];

                $setting[] = $node;
            }
            unset($data['approver']);
            unset($data['staff']);
            unset($data['action']);
        }
        $data['setting'] = json_encode($setting);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() .'sa_approval_setting', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean   
     */
    public function delete_approval_setting($id)
    {
        if(is_numeric($id)){
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() .'sa_approval_setting');

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the agent client group.
     */
    public function get_agent_client_group($agent_id){
        $this->db->where('agent_id', $agent_id);

        return $this->db->get(db_prefix().'sa_client_groups')->result_array();
    }

    /**
     * Adds a client group.
     */
    public function add_client_group($data){
        unset($data['client_group_id']);
        $this->db->insert(db_prefix().'sa_client_groups', $data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            return $insert_id;
        }
         return false;
    }

    /**
     * Adds a client group.
     */
    public function edit_client_group($id, $data){
        unset($data['client_group_id']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'sa_client_groups', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
         return false;
    }

    /**
     * { update sa setting }
     *
     * @param        $agent_id  The agent identifier
     * @param        $data      The data
     */
    public function update_sa_setting($agent_id, $data){

        $rs = 0;
        foreach($data as $key => $value){
            if(total_rows(db_prefix().'sa_options', ['agent_id' => $agent_id, 'name' => $key]) > 0 ){
                $this->db->where('agent_id', $agent_id);
                $this->db->where('name' , $key);
                $this->db->update(db_prefix().'sa_options', ['value' => $value]);
                if($this->db->affected_rows() > 0){
                    $rs++;
                }
            }else{
                $this->db->insert(db_prefix().'sa_options', [
                    'agent_id' => $agent_id,
                    'name' => $key,
                    'value' => $value
                ]);

                $insert_id = $this->db->insert_id();
                if($insert_id){
                    $rs++;
                }
            }
        }

        if($rs > 0){
            return true;
        }
        return false;
    }

    /**
     * { delete_client }
     */
    public function delete_client($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'sa_clients');
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the item attachments.
     */
    public function get_item_attachments($commodity_id){
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $commodity_id);
        $this->db->where('rel_type', 'commodity_item_file');

        return $this->db->get(db_prefix() . 'files')->result_array();

    }

    /**
     * check auto create currency rate
     * @return [type]
     */
    public function check_auto_create_currency_rate() {
        $this->load->model('currencies_model');
        $currency_rates = $this->get_currency_rate();
        $currencies = $this->currencies_model->get();
        $currency_generator = $this->currency_generator($currencies);

        foreach ($currency_rates as $key => $currency_rate) {
            if (isset($currency_generator[$currency_rate['from_currency_id'] . '_' . $currency_rate['to_currency_id']])) {
                unset($currency_generator[$currency_rate['from_currency_id'] . '_' . $currency_rate['to_currency_id']]);
            }
        }

        //if have API, will get currency rate from here
        if (count($currency_generator) > 0) {
            $this->db->insert_batch(db_prefix() . 'currency_rates', $currency_generator);
        }

        return true;
    }

    /**
     * currency generator
     * @param  $variants
     * @param  integer $i
     * @return 
     */
    public function currency_generator($currencies) {

        $currency_rates = [];

        foreach ($currencies as $key_1 => $value_1) {
            foreach ($currencies as $key_2 => $value_2) {
                if ($value_1['id'] != $value_2['id']) {
                    $currency_rates[$value_1['id'] . '_' . $value_2['id']] = [
                        'from_currency_id' => $value_1['id'],
                        'from_currency_name' => $value_1['name'],
                        'from_currency_rate' => 1,
                        'to_currency_id' => $value_2['id'],
                        'to_currency_name' => $value_2['name'],
                        'to_currency_rate' => 0,
                        'date_updated' => date('Y-m-d H:i:s'),
                    ];
                }

            }
        }

        return $currency_rates;
    }

    /**
     * get currency rate
     * @param  boolean $id
     * @return [type]
     */
    public function get_currency_rate($id = false) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'currency_rates')->row();
        }

        if ($id == false) {
            return $this->db->query('select * from ' . db_prefix() . 'currency_rates')->result_array();
        }
    }

    /**
     * update currency rate setting
     *
     * @param      array   $data   The data
     *
     * @return     boolean
     */
    public function update_setting_currency_rate($data) {
        $affectedRows = 0;
        if (!isset($data['cr_automatically_get_currency_rate'])) {
            $data['cr_automatically_get_currency_rate'] = 0;
        }

        foreach ($data as $key => $value) {
            $this->db->where('name', $key);
            $this->db->update(db_prefix() . 'options', [
                'value' => $value,
            ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets the currency rate online.
     *
     * @param        $id     The identifier
     *
     * @return     bool    The currency rate online.
     */
    public function get_currency_rate_online($id) {
        $currency_rate = $this->get_currency_rate($id);

        if ($currency_rate) {
            return $this->currency_converter($currency_rate->from_currency_name, $currency_rate->to_currency_name);
        }

        return false;
    }

    /**
     * Gets all currency rate online.
     *
     * @return     bool  All currency rate online.
     */
    public function get_all_currency_rate_online() {
        $currency_rates = $this->get_currency_rate();
        $affectedRows = 0;
        foreach ($currency_rates as $currency_rate) {
            $rate = $this->currency_converter($currency_rate['from_currency_name'], $currency_rate['to_currency_name']);

            $data_update = ['to_currency_rate' => $rate];
            $success = $this->update_currency_rate($data_update, $currency_rate['id']);

            if ($success) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            return true;
        }

        return true;
    }

    /**
     * update currency rate
     * @param  [type] $data
     * @return [type]
     */
    public function update_currency_rate($data, $id) {

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'currency_rates', ['to_currency_rate' => $data['to_currency_rate'], 'date_updated' => date('Y-m-d H:i:s')]);
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id', $id);
            $current_rate = $this->db->get(db_prefix() . 'currency_rates')->row();

            $data_log['from_currency_id'] = $current_rate->from_currency_id;
            $data_log['from_currency_name'] = $current_rate->from_currency_name;
            $data_log['to_currency_id'] = $current_rate->to_currency_id;
            $data_log['to_currency_name'] = $current_rate->to_currency_name;
            $data_log['from_currency_rate'] = isset($data['from_currency_rate']) ? $data['from_currency_rate'] : '';
            $data_log['to_currency_rate'] = isset($data['to_currency_rate']) ? $data['to_currency_rate'] : '';
            $data_log['date'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'currency_rate_logs', $data_log);
            return true;
        }
        return false;
    }

    /**
     * currency converter description
     * @param  string $from   Currency Code
     * @param  string $to     Currency Code
     * @param  float $amount
     * @return float
     */
    public function currency_converter($from, $to, $amount = 1) {
        $from = strtoupper($from);
        $to = strtoupper($to);

        $url = "https://api.frankfurter.app/latest?amount=$amount&from=$from&to=$to";

        $response = json_decode($this->api_get($url));

        if (isset($response->rates->$to)) {
            return $response->rates->$to;
        } elseif (isset($response->rates)) {
            return (array) $response->rates;
        }

        return false;
    }

    /**
     * api get
     * @param  string $url
     * @return string
     */
    public function api_get($url) {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);

        return curl_exec($curl);
    }

    /**
     * delete currency rate
     * @param  [type] $id
     * @return [type]
     */
    public function delete_currency_rate($id) {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'currency_rates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { cronjob currency rates }
     *
     * @param        $manually  The manually
     *
     * @return     bool    
     */
    public function cronjob_currency_rates($manually) {
        $currency_rates = $this->get_currency_rate();
        foreach ($currency_rates as $currency_rate) {
            $data_insert = $currency_rate;
            $data_insert['date'] = date('Y-m-d');
            unset($data_insert['date_updated']);
            unset($data_insert['id']);

            $this->db->insert(db_prefix() . 'currency_rate_logs', $data_insert);
        }

        if (get_option('cr_automatically_get_currency_rate') == 1) {
            $this->get_all_currency_rate_online();
        }

        $asm_global_amount_expiration = get_option('cr_global_amount_expiration');
        if ($asm_global_amount_expiration != 0 && $asm_global_amount_expiration != '') {
            $this->db->where('date < "' . date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $asm_global_amount_expiration . ' days')).'"');
            $this->db->delete(db_prefix() . 'currency_rate_logs');
        }
        update_option('cr_date_cronjob_currency_rates', date('Y-m-d'));

        return true;
    }

    /**
     * wh get tax rate
     * @param  [type] $taxname 
     * @return [type]          
     */
    public function sa_get_tax_rate($taxname)
    {   
        $tax_rate = 0;
        $tax_rate_str = '';
        $tax_id_str = '';
        $tax_name_str = '';
        //var_dump($taxname); die;
        if(is_array($taxname)){
            foreach ($taxname as $key => $value) {
                $_tax = explode("|", $value);
                if(isset($_tax[1])){
                    $tax_rate += (float)$_tax[1];
                    if(strlen($tax_rate_str) > 0){
                        $tax_rate_str .= '|'.$_tax[1];
                    }else{
                        $tax_rate_str .= $_tax[1];
                    }

                    $this->db->where('name', $_tax[0]);
                    $taxes = $this->db->get(db_prefix().'taxes')->row();
                    if($taxes){
                        if(strlen($tax_id_str) > 0){
                            $tax_id_str .= '|'.$taxes->id;
                        }else{
                            $tax_id_str .= $taxes->id;
                        }
                    }

                    if(strlen($tax_name_str) > 0){
                        $tax_name_str .= '|'.$_tax[0];
                    }else{
                        $tax_name_str .= $_tax[0];
                    }
                }
            }
        }
        return ['tax_rate' => $tax_rate, 'tax_rate_str' => $tax_rate_str, 'tax_id_str' => $tax_id_str, 'tax_name_str' => $tax_name_str];
    }


    /**
     * Creates a purchase order row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_purchase_order_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '', $program_id = '', $index_key = 0) {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';
        $name_program_id = 'program_id';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item" data-key="'.$index_key.'">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
            $name_program_id = $name.'[program_id]';
      
           
            $array_qty_attr = ['onchange' => 'pur_calculate_total(0,'.$index_key.');', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total(0, 0, '.$index_key.');', 'onchange' => 'pur_calculate_total(0, 0, '.$index_key.');', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1,0);', 'onchange' => 'pur_calculate_total(1,0);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = sa_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->sa_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = app_format_number($amount);

        }
 

        $row .= '<td class="">' . render_textarea($name_item_name, '', $item_name, ['rows' => 2, 'placeholder' => _l('pur_item_name'), 'readonly' => true] ) . '</td>';

        $row .= '<td class="">' . render_textarea($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);

        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . app_format_number($total_money) . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input($name_total, '', $total, 'number', []) . '</td>';

   
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide program_id">' . render_input($name_program_id, '', $program_id, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the tax name.
     *
     * @param        $tax    The tax
     *
     * @return     string  The tax name.
     */
    public function get_tax_name($tax){
        $this->db->where('id', $tax);
        $tax_if = $this->db->get(db_prefix().'taxes')->row();
        if($tax_if){
            return $tax_if->name;
        }
        return '';
    }

    /**
     * { tax rate by id }
     *
     * @param        $tax_id  The tax identifier
     */
    public function tax_rate_by_id($tax_id){
        $this->db->where('id', $tax_id);
        $tax = $this->db->get(db_prefix().'taxes')->row();
        if($tax){
            return $tax->taxrate;
        }
        return 0;
    }

    /**
     * get taxes dropdown template
     * @param  [type]  $name     
     * @param  [type]  $taxname  
     * @param  string  $type     
     * @param  string  $item_key 
     * @param  boolean $is_edit  
     * @param  boolean $manual   
     * @return [type]            
     */
    public function get_taxes_dropdown_template($name, $taxname, $type = '', $item_key = '', $is_edit = false, $manual = false)
    {
        // if passed manually - like in proposal convert items or project
        if($taxname != '' && !is_array($taxname)){
            $taxname = explode(',', $taxname);
        }

        if ($manual == true) {
            // + is no longer used and is here for backward compatibilities
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                // Multiple taxes found // possible option from default settings when invoicing project
                $taxname = [];
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                // isset tax rate
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        // First get all system taxes
        $this->load->model('taxes_model');
        $taxes = $this->taxes_model->get();
        $i     = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['id']);
            $taxes[$i]['name'] = $tax['name'] . '|' . $tax['taxrate'];
            $i++;
        }
        if ($is_edit == true) {

            // Lets check the items taxes in case of changes.
            // Separate functions exists to get item taxes for Invoice, Estimate, Proposal, Credit Note
            $func_taxes = 'get_' . $type . '_item_taxes';
            if (function_exists($func_taxes)) {
                $item_taxes = call_user_func($func_taxes, $item_key);
            }

            foreach ($item_taxes as $item_tax) {
                $new_tax            = [];
                $new_tax['name']    = $item_tax['taxname'];
                $new_tax['taxrate'] = $item_tax['taxrate'];
                $taxes[]            = $new_tax;
            }
        }

        // In case tax is changed and the old tax is still linked to estimate/proposal when converting
        // This will allow the tax that don't exists to be shown on the dropdowns too.
        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                // Check if tax empty
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['taxname'] == '') {
                    continue;
                };
                // Check if really the taxname NAME|RATE don't exists in all taxes
                if (!value_exists_in_array_by_key($taxes, 'name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array   = explode('|', $tax);
                    } else {
                        $tax_array   = explode('|', $tax['taxname']);
                        $tmp_taxname = $tax['taxname'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = ['name' => $tmp_taxname, 'taxrate' => $tax_array[1]];
                }
            }
        }

        // Clear the duplicates
        $taxes = $this->sa_uniqueByKey($taxes, 'name');

        $select = '<select class="selectpicker display-block taxes" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . _l('no_tax') . '">';

        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['taxname'] == $tax['name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['name'] . '" ' . $selected . ' data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * wh uniqueByKey
     * @param  [type] $array 
     * @param  [type] $key   
     * @return [type]        
     */
    public function sa_uniqueByKey($array, $key)
    {
        $temp_array = [];
        $i          = 0;
        $key_array  = [];

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i]  = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }

    /**
     * Gets the items of program.
     */
    public function get_items_of_program($program_id){
        
        $produt_ids = get_product_ids_of_program($program_id);
        $group_ids = get_product_group_ids_of_program($program_id);

        if(count($produt_ids) > 0 && count($group_ids) == 0){
            $where = db_prefix().'items.id IN ('.implode(',', $produt_ids).') ';
        }else if(count($produt_ids) == 0 && count($group_ids) > 0){
            $where = db_prefix().'items.group_id IN ('.implode(',', $group_ids).') ';
        }else if(count($produt_ids) > 0 && count($group_ids) > 0){
            $where = db_prefix().'items.id IN ('.implode(',', $produt_ids).') OR '.db_prefix().'items.group_id IN ('.implode(',', $group_ids).')';
        }else if(count($produt_ids) == 0 && count($group_ids) == 0){
            array_push($where, '1=2');
        }

        $this->db->where($where);
        return $this->db->get(db_prefix().'items')->result_array();
    }


    /**
     * Gets the item v 2.
     *
     * @param      string  $id     The identifier
     *
     * @return       The item v 2.
     */
    public function get_item_v2($id = '')
    {
        $columns             = $this->db->list_fields(db_prefix() . 'items');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== false) {
                $rateCurrencyColumns .= $column . ',';
            }
        }
        $this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2, description,
            CONCAT(commodity_code,"_",description) as code_description,long_description,group_id,' . db_prefix() . 'items_groups.name as group_name,unit,'.db_prefix().'ware_unit_type.unit_name as unit_name, purchase_price, unit_id, guarantee');
        $this->db->from(db_prefix() . 'items');
        $this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
        $this->db->join('' . db_prefix() . 'taxes t2', 't2.id = ' . db_prefix() . 'items.tax2', 'left');
        $this->db->join(db_prefix() . 'items_groups', '' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
        $this->db->join(db_prefix() . 'ware_unit_type', '' . db_prefix() . 'ware_unit_type.unit_type_id = ' . db_prefix() . 'items.unit_id', 'left');
        $this->db->order_by('description', 'asc');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'items.id', $id);

            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Adds a pur order.
     *
     * @param      <array>   $data   The data
     *
     * @return     boolean , int id purchase order
     */
    public function add_pur_order($data, $agent_id){

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        unset($data['disount_program']);
        unset($data['program_id']);

        $data['agent_id'] = $agent_id;

        $check_appr = $this->get_approve_setting('pur_order', $agent_id);
        $data['approve_status'] = 1;
        if($check_appr && $check_appr != false){
            $data['approve_status'] = 1;
        }else{
            $data['approve_status'] = 2;
        }

        $data['to_currency'] = $data['currency'];

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $prefix = get_sa_option('pur_order_prefix', $agent_id);

        $this->db->where('order_number',$data['order_number']);
        $this->db->where('agent_id',$agent_id);
        $check_exist_number = $this->db->get(db_prefix().'sa_pur_orders')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
      
          $data['order_number'] =  $prefix.'-'.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          
          $this->db->where('order_number',$data['order_number']);
          $this->db->where('agent_id',$agent_id);
          $check_exist_number = $this->db->get(db_prefix().'sa_pur_orders')->row();
        }

        $data['order_date'] = to_sql_date($data['order_date']);

        $data['delivery_date'] = to_sql_date($data['delivery_date']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_sa_contact_user_id();

        $data['hash'] = app_generate_hash();

        $data['order_status'] = 'new';
        $data['delivery_status'] = 0;

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }
            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $this->db->insert(db_prefix() . 'sa_pur_orders', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            // Update next purchase order number in settings
            $next_number = $data['number']+1;
            $this->db->where('agent_id', $agent_id);
            $this->db->where('name', 'next_po_number');
            $this->db->update(db_prefix() . 'sa_options',['value' =>  $next_number,]);

            $total = [];
            $total['total_tax'] = 0;
            
            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['pur_order'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = $rqd['item_description'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_%'] = $rqd['discount'];
                    $dt_data['program_id'] = $rqd['program_id'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $this->db->insert(db_prefix().'sa_pur_order_detail', $dt_data);


                    $total['total_tax'] += $rqd['tax_value'];
                }
            }

            $this->db->where('id',$insert_id);
            $this->db->update(db_prefix().'sa_pur_orders',$total);

            return $insert_id;
        }

        return false;
    }

    /**
     * Gets the approve setting.
     *
     * @param      <type>   $type    The type
     * @param      string   $status  The status
     *
     * @return     boolean  The approve setting.
     */
    public function get_approve_setting($type, $agent_id){
        $this->db->select('*');
        $this->db->where('related', $type);
        $this->db->where('agent_id', $agent_id);
        $approval_setting = $this->db->get(db_prefix().'sa_approval_setting')->row();
        if($approval_setting){
            return json_decode($approval_setting->setting);
        }else{
            return false;
        }
    }


    /**
     * Gets the pur order detail.
     *
     * @param      <int>  $pur_request  The pur request
     *
     * @return     <array>  The pur order detail.
     */
    public function get_pur_order_detail($po){
        $this->db->where('pur_order',$po);
        $pur_order_details = $this->db->get(db_prefix().'sa_pur_order_detail')->result_array();

        foreach($pur_order_details as $key => $detail){
            $pur_order_details[$key]['discount_money'] = (float) $detail['discount_money'];
            $pur_order_details[$key]['into_money'] = (float) $detail['into_money'];
            $pur_order_details[$key]['total'] = (float) $detail['total'];
            $pur_order_details[$key]['total_money'] = (float) $detail['total_money'];
            $pur_order_details[$key]['unit_price'] = (float) $detail['unit_price'];
            $pur_order_details[$key]['tax_value'] = (float) $detail['tax_value'];
        }

        return $pur_order_details;
    }

    /**
     * Gets the pur order.
     *
     * @param      <int>  $id     The identifier
     *
     * @return     <row>  The pur order.
     */
    public function get_pur_order($id){
        $this->db->where('id',$id);
        return $this->db->get(db_prefix().'sa_pur_orders')->row();
    }

    /**
     * row item to variation
     * @param  [type] $item_value 
     * @return [type]             
     */
    public function row_item_to_variation($item_value)
    {
        if($item_value){

                $name = '';
                if($item_value->attributes != null && $item_value->attributes != ''){
                    $attributes_decode = json_decode($item_value->attributes);

                    foreach ($attributes_decode as $value) {
                        if(strlen($name) > 0){
                            $name .= '#'.$value->name.' ( '.$value->option.' ) ';
                        }else{
                            $name .= ' #'.$value->name.' ( '.$value->option.' ) ';
                        }
                    }


                }

                $item_value->new_description = $item_value->description;
                
        }

        return $item_value;
    }


    /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_pur_order($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $this->db->where('pur_order', $id);
        $details = $this->db->get(db_prefix().'sa_pur_order_detail')->result_array();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->name).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * { update pur order }
     *
     * @param      <type>   $data   The data
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function update_pur_order($data, $id, $agent_id)
    {
        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        unset($data['disount_program']);
        unset($data['program_id']);

        unset($data['isedit']);

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        $data['to_currency'] = $data['currency'];

        $prefix = get_sa_option('pur_order_prefix', $agent_id);
        $data['order_number'] = $data['order_number'];

        $data['order_date'] = to_sql_date($data['order_date']);

        $data['delivery_date'] = to_sql_date($data['delivery_date']);

        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        if(isset($data['clients']) && count($data['clients']) > 0){
            $data['clients'] = implode(',', $data['clients']);
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }

        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }


        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'sa_pur_orders', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];
                $dt_data['program_id'] = $rqd['program_id'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->insert(db_prefix().'sa_pur_order_detail', $dt_data);
                $new_quote_insert_id = $this->db->insert_id();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
                
            }

        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['pur_order'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_%'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];
                $dt_data['program_id'] = $rqd['program_id'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->where('id', $rqd['id']);
                $this->db->update(db_prefix().'sa_pur_order_detail', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'sa_pur_order_detail')) {
                    $affectedRows++;
                }
            }
        }

        $order_detail_after_update = $this->get_pur_order_detail($id);
        $total = [];
        $total['total_tax'] = 0;
        if(count($order_detail_after_update) > 0){
            foreach($order_detail_after_update as $dt){
                $total['total_tax'] += $dt['tax_value'];
            }
        }
        
        $this->db->where('id',$id);
        $this->db->update(db_prefix().'sa_pur_orders',$total);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_purchase_order_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','sa_pur_order');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the file.
     *
     * @param      <type>   $id      The file id
     * @param      boolean  $rel_id  The relative identifier
     *
     * @return     boolean  The file.
     */
    public function get_file($id, $rel_id = false)
    {
        $this->db->where('id', $id);
        $file = $this->db->get(db_prefix().'files')->row();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }
        return $file;
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_purorder_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'sa_pur_order');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_purorder_attachment($id)
    {
        $attachment = $this->get_purorder_attachments('', $id);
        $deleted    = false;

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'files');  
        if ($this->db->affected_rows() > 0) {
            $deleted = true;
        }

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix().'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Gets the list approval details.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     <array>  The list approval details.
     */
    public function get_list_approval_details($rel_id, $rel_type){
        $this->db->select('*');
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        return $this->db->get(db_prefix().'sa_approval_details')->result_array();
    }

    /**
     * { purorder pdf }
     *
     * @param      <type>  $pur_request  The pur request
     *
     * @return     <type>  ( purorder pdf )
     */
    public function purorder_pdf($pur_order)
    {
        return app_pdf('sa_pur_order', module_dir_path(SALES_AGENT_MODULE_NAME, 'libraries/pdf/Sa_pur_order_pdf'), $pur_order);
    }

    /**
     * Gets the pur request pdf html.
     *
     * @param      <type>  $pur_request_id  The pur request identifier
     *
     * @return     string  The pur request pdf html.
     */
    public function get_purorder_pdf_html($pur_order_id){


        $pur_order = $this->get_pur_order($pur_order_id);
        $pur_order_detail = $this->get_pur_order_detail($pur_order_id);
        $list_approve_status = $this->get_list_approval_details($pur_order_id,'pur_order');

        $company_name = get_option('invoice_company_name'); 

        $tax_data = $this->get_html_tax_pur_order($pur_order_id);

        $base_currency = get_base_currency();
        if($pur_order->currency != 0){
            $base_currency = sa_get_currency_by_id($pur_order->currency);
        }

        $address = '';
        $vendor_name = '';
        $address = get_option('invoice_company_address') . ', '.  get_option('invoice_company_city').', '.get_option('invoice_company_city').', '.get_option('company_state').', '.get_option('invoice_company_country_code') ;

        $agent = get_client($pur_order->agent_id);

        $ship_to = format_customer_info($agent,'invoice', 'shipping');

        $day = _d($pur_order->order_date);
       
        
    $html = '<table class="table">
        <tbody>
          <tr>
            <td rowspan="6" class="text-left width70" >
            '.pdf_logo_url().'
             <br>'.format_customer_info($agent, 'invoice', 'shipping').'
            </td>
            <td class="text-right width30">
                <strong class="fsize20">'.mb_strtoupper(_l('purchase_order')).'</strong><br>
                <strong>'.mb_strtoupper($pur_order->order_number).'</strong><br>
            </td>
          </tr>

          <tr>
            <td class="text-right width30" >
                <br><strong>'._l('pur_vendor').'</strong>    
                <br>'. $company_name.'
                <br>'. $address.'
            </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right width30">
                <br><strong>'._l('pur_ship_to').'</strong>    
                <br>'. $ship_to.'
            </td>
            <td></td>
          </tr>

          <tr>
            <td></td>
          </tr>
          <tr>
            <td class="text-right">'. _l('order_date').': '. $day.'</td>
            <td></td>
          </tr>

        </tbody>
      </table>
      <br><br><br>
      ';

      $html .=  '<table class="table purorder-item">
        <thead>
          <tr>
            <th class="thead-dark width30">'._l('items').'</th>
            <th class="thead-dark width15" align="right">'._l('purchase_unit_price').'</th>
            <th class="thead-dark width15"  align="right">'._l('purchase_quantity').'</th>';
         
           

                $html .= '<th class="thead-dark width10" align="right" >'._l('tax').'</th>';
         
 
            $html .= '<th class="thead-dark width15" align="right" >'._l('discount').'</th>
            <th class="thead-dark width15" align="right" >'._l('total').'</th>
          </tr>
          </thead>
          <tbody>';
        $t_mn = 0;
        $item_discount = 0;
      foreach($pur_order_detail as $row){
        $items = $this->get_items_by_id($row['item_code']);
        $des_html = ($items) ? $items->commodity_code.' - '.$items->description : $row['item_name'];

        $units = $this->get_units_by_id($row['unit_id']);
        $unit_name = isset($units->unit_name) ? $units->unit_name : '';
        
        $html .= '<tr nobr="true" class="sortable">
            <td class="width30" ><strong>'.$des_html.'</strong><br><span>'.$row['description'].'</span></td>
            <td class="width15" align="right">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
            <td class="width15" align="right">'.app_format_number($row['quantity'],'').' '. $unit_name.'</td>';
         
          
                $html .= '<td class="width10" align="right">'.app_format_money($row['total'] - $row['into_money'],$base_currency->symbol).'</td>';
            
       
            $html .= '<td class="width15" align="right" >'.app_format_money($row['discount_money'],$base_currency->symbol).'</td>
            <td class="width15" align="right" >'.app_format_money($row['total_money'],$base_currency->symbol).'</td>
          </tr>';

        $t_mn += $row['total_money'];
        $item_discount += $row['discount_money'];
      }  
      $html .=  '</tbody>
      </table><br><br>';

      $html .= '<table class="table text-right"><tbody>';
      $html .= '<tr id="subtotal">
                    <td class="width33" ></td>
                     <td>'._l('subtotal').' </td>
                     <td class="subtotal">
                        '.app_format_money($pur_order->subtotal,$base_currency->symbol).'
                     </td>
                  </tr>';

      $html .= $tax_data['pdf_html'];

      if(($pur_order->discount_total + $item_discount) > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td class="width33" ></td>
                     <td>'._l('discount_total(money)').'</td>
                     <td class="subtotal">
                        '.app_format_money(($pur_order->discount_total + $item_discount), $base_currency->symbol).'
                     </td>
                  </tr>';
      }

      if($pur_order->shipping_fee  > 0){
        $html .= '
                  
                  <tr id="subtotal">
                  <td class="width33" ></td>
                     <td>'._l('pur_shipping_fee').'</td>
                     <td class="subtotal">
                        '.app_format_money($pur_order->shipping_fee, $base_currency->symbol).'
                     </td>
                  </tr>';
      }
      $html .= '<tr id="subtotal">
                 <td class="width33" ></td>
                 <td>'. _l('total').'</td>
                 <td class="subtotal">
                    '. app_format_money($pur_order->total, $base_currency->symbol).'
                 </td>
              </tr>';

      $html .= ' </tbody></table>';

      $html .= '<div class="col-md-12 mtop15">
                        <h4>'. _l('terms_and_conditions').':</h4><p>'. $pur_order->terms .'</p>
                       
                     </div>';
      if(count($list_approve_status) > 0){
          $html .= '<br>
          <br>
          <br>
          <br>';

          $html .=  '<table class="table">
            <tbody>
              <tr>';

            foreach ($list_approve_status as $value) {
         $html .= '<td class="td_appr">';
            if($value['action'] == 'sign'){
                $html .= '<h3>'.mb_strtoupper(get_contact_full_name($value['staffid'])).'</h3>';
                if($value['approve'] == 2){ 
                    $html .= '<img src="'.FCPATH. 'modules/sales_agent/uploads/pur_order/signature/'.$pur_order->id.'/signature_'.$value['id'].'.png" class="img_style">';
                }
                    
            }else{ 
            $html .= '<h3>'.mb_strtoupper(get_contact_full_name($value['staffid'])).'</h3>';
                  if($value['approve'] == 2){ 
            $html .= '<img src="'.FCPATH.'modules/sales_agent/uploads/approval/approved.png" class="img_style">';
                 }elseif($value['approve'] == 3){
            $html .= '<img src="'.FCPATH.'modules/sales_agent/uploads/approval/rejected.png" class="img_style">';
                 }
                  
                    }
           $html .= '</td>';
            }
           
         $html .= '</tr>
            </tbody>
          </table>';
        
    }
      $html .=  '<link href="' . FCPATH.'modules/sales_agent/assets/css/pur_order_pdf.css' . '"  rel="stylesheet" type="text/css" />';
      return $html;
    }


    /**
     * Gets the items by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The items by identifier.
     */
    public function get_items_by_id($id){
        $this->db->where('id',$id);
        return $this->db->get(db_prefix().'items')->row();
    }

    /**
     * Gets the units by identifier.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <row>  The units by identifier.
     */
    public function get_units_by_id($id){
        $this->db->where('unit_type_id',$id);
        return $this->db->get(db_prefix().'ware_unit_type')->row();
    }

    /**
     * Gets the staff sign.
     *
     * @param      <type>  $rel_id    The relative identifier
     * @param      <type>  $rel_type  The relative type
     *
     * @return     array   The staff sign.
     */
    public function get_staff_sign($rel_id, $rel_type, $agent_id){
        $this->db->select('*');

        $this->db->where('agent_id', $agent_id);
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('action', 'sign');    
        $approve_status = $this->db->get(db_prefix().'sa_approval_details')->result_array();
        if(isset($approve_status))
        {
            $array_return = [];
            foreach ($approve_status as $key => $value) {
               array_push($array_return, $value['staffid']);
            }
            return $array_return;
        }
        return [];
    }

     /**
     * { check approval details }
     *
     * @param      <type>          $rel_id    The relative identifier
     * @param      <type>          $rel_type  The relative type
     *
     * @return     boolean|string 
     */
    public function check_approval_details($rel_id, $rel_type, $agent_id){
        $this->db->where('agent_id', $agent_id);
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $approve_status = $this->db->get(db_prefix().'sa_approval_details')->result_array();
        if(count($approve_status) > 0){
            foreach ($approve_status as $value) {
                if($value['approve'] == -1){
                    return 'reject';
                }
                if($value['approve'] == 0){
                    $value['staffid'] = explode(', ',$value['staffid']);
                    return $value;
                }
            }
            return true;
        }
        return false;
    }


    /**
     * Sends a request approve.
     *
     * @param      <type>   $data   The data
     *
     * @return     boolean   
     */
    public function send_request_approve($data){
        if(!isset($data['status'])){
            $data['status'] = '';
        }
        $agent_id = get_sale_agent_user_id();
        $date_send = date('Y-m-d H:i:s');
        $data_new = $this->get_approve_setting($data['rel_type'], $agent_id);
        if(!$data_new){
            return false;
        }
        $this->delete_approval_details($data['rel_id'], $data['rel_type'], $agent_id);
        $list_staff = $this->staff_model->get();
        $list = [];
        $staff_addedfrom = $data['addedfrom'];
        $sender = get_sa_contact_user_id();
        
        
        foreach ($data_new as $value) {
            $row = [];
            
            if($value->approver !== 'staff'){
            $value->staff_addedfrom = $staff_addedfrom;
            $value->rel_type = $data['rel_type'];
            $value->rel_id = $data['rel_id'];
            
                $approve_value = $this->get_staff_id_by_approve_value($value, $value->approver);

                if(is_numeric($approve_value) && $approve_value != 0){
                    $approve_value = $this->staff_model->get($approve_value)->email;
                }else{

                    $this->db->where('rel_id', $data['rel_id']);
                    $this->db->where('rel_type', $data['rel_type']);
                    $this->db->delete(db_prefix().'sa_approval_details');


                    return $value->approver;
                }
                $row['approve_value'] = $approve_value;
            
            $staffid = $this->get_staff_id_by_approve_value($value, $value->approver);
            
            if(empty($staffid)){
                $this->db->where('agent_id', $agent_id);
                $this->db->where('rel_id', $data['rel_id']);
                $this->db->where('rel_type', $data['rel_type']);
                $this->db->delete(db_prefix().'sa_approval_details');


                return $value->approver;
            }

                $row['action'] = $value->action;
                $row['staffid'] = $staffid;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;
                $row['agent_id'] = $agent_id;
                $this->db->insert(db_prefix().'sa_approval_details', $row);

            }else if($value->approver == 'staff'){
                $row['action'] = $value->action;
                $row['staffid'] = $value->staff;
                $row['date_send'] = $date_send;
                $row['rel_id'] = $data['rel_id'];
                $row['rel_type'] = $data['rel_type'];
                $row['sender'] = $sender;
                $row['agent_id'] = $agent_id;
                $this->db->insert(db_prefix().'sa_approval_details', $row);
            }
        }
        return true;
    }


    /**
     * Gets the staff identifier by approve value.
     *
     * @param      <type>  $data           The data
     * @param      string  $approve_value  The approve value
     *
     * @return     array   The staff identifier by approve value.
     */
    public function get_staff_id_by_approve_value($data, $approve_value){
        $list_staff = $this->staff_model->get();
        $list = [];
        $staffid = [];

        $this->load->model('departments_model');
        $this->load->model('staff_model');
        
        if($approve_value == 'head_of_department'){
            $staffid = $this->departments_model->get_staff_departments($data->staff_addedfrom)[0]['manager_id'];
        }elseif($approve_value == 'direct_manager'){
            $staffid = $this->staff_model->get($data->staff_addedfrom)->team_manage;
        }
        
        return $staffid;
    }

    /**
     * { delete approval details }
     *
     * @param      <type>   $rel_id    The relative identifier
     * @param      <type>   $rel_type  The relative type
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public function delete_approval_details($rel_id, $rel_type, $agent_id)
    {
        $this->db->where('agent_id', $agent_id);
        $this->db->where('rel_id', $rel_id);
        $this->db->where('rel_type', $rel_type);
        $this->db->delete(db_prefix().'sa_approval_details');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { update approval details }
     *
     * @param      <int>   $id     The identifier
     * @param      <type>   $data   The data
     *
     * @return     boolean 
     */
    public function update_approval_details($id, $data){
        $data['date'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'sa_approval_details', $data);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * { update approve request }
     *
     * @param      <type>   $rel_id    The relative identifier
     * @param      <type>   $rel_type  The relative type
     * @param      <type>   $status    The status
     *
     * @return     boolean
     */
    public function update_approve_request($rel_id , $rel_type, $status){ 
        $data_update = [];
        
        switch ($rel_type) {
            case 'pur_order':
                $data_update['approve_status'] = $status;
                $this->db->where('id', $rel_id);
                $this->db->update(db_prefix().'sa_pur_orders', $data_update);

                return true;
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Creates an invoice for order.
     */
    public function create_invoice_for_order($order_id){
        $this->load->model('invoices_model');
        $this->load->model('clients_model');

        $order = $this->get_pur_order($order_id);
        $order_detail = $this->get_pur_order_detail($order_id);

        $agent = $this->clients_model->get($order->agent_id);

        $newitems = [];
        $count = 0;

        foreach ($order_detail as $key => $value) {
            $unit = 0;
            $unit_name = '';
            $this->db->where('id', $value['item_code']);
            $data_product = $this->db->get(db_prefix().'items')->row();


            $tax_arr = [];

            
            if($value['tax'] != '' && $value['tax'] != null){
                $value_taxes_name = explode('|', $value['tax_name']);
                $value_taxes_rate = explode('|', $value['tax_rate']);

                if(count($value_taxes_name) > 0){
                    foreach($value_taxes_name as $key => $_tax_name){
                        $tax_arr[] = $_tax_name.'|'.$value_taxes_rate[$key];
                    }
                }
            }

            $unit_name = "";
            if($data_product){        
                $unit = $data_product->unit_id;
                if($unit != 0 || $unit != null){
                    $this->db->where('unit_type_id', $unit);
                    $unit_parent = $this->db->get(db_prefix().'ware_unit_type')->row();
                    if($unit_parent){
                        $unit_name = $unit_parent->unit_name;
                    }
                }  
            }
            $count = $key;
            
            array_push($newitems, array('order' => $key, 'description' => $value['item_name'], 'long_description' => $value['description'], 'qty' => $value['quantity'], 'unit' => $unit_name, 'rate'=> $value['unit_price'], 'taxname' => $tax_arr));
        }

        $this->db->where('selected_by_default', 1);
        $payment_mode_arr = $this->db->get(db_prefix().'payment_modes')->result_array();
        $pm_arr = [];
        foreach($payment_mode_arr as $pm){
            $pm_arr[] = $pm['id'];
        }

        $data['clientid'] = $order->agent_id;
        $data['billing_street'] = $agent->billing_street;
        $data['billing_city'] = $agent->billing_city;
        $data['billing_state'] = $agent->state;
        $data['billing_zip'] = $agent->billing_zip;
        $data['billing_country'] = $agent->billing_country;
        $data['include_shipping'] = 1;
        $data['show_shipping_on_invoice'] = 1;
        $data['shipping_street'] = $agent->shipping_street;
        $data['shipping_city'] = $agent->shipping_city;
        $data['shipping_state'] = $agent->state;
        $data['shipping_zip'] = $agent->shipping_zip;
        $data['allowed_payment_modes'] = $pm_arr;
        $date_format   = get_option('dateformat');
        $date_format   = explode('|', $date_format);
        $date_format   = $date_format[0];       
        $data['date'] = date($date_format);

        $data['duedate'] = _d(date("Y-m-d", strtotime("+1 month", strtotime(date("Y-m-d")))));
        $data['terms'] = get_option('predefined_terms_invoice');

        $__number = get_option('next_invoice_number');
        $_invoice_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

        $data['currency'] = $order->currency;
        $data['newitems'] = $newitems;
        $data['number'] = $_invoice_number;
        $data['total'] = $order->total;
        $data['subtotal'] = $order->subtotal;      
        $data['total_tax'] = $order->total_tax;
        $data['discount_total'] = $order->discount_total;
        $data['shipping_fee'] = $order->shipping_fee;
       
        $data['discount_type'] = 'after_tax';
        $data['sale_agent'] = '';
        $data['adjustment'] = 0;

        $id = $this->invoices_model->add($data);
        if($id){

            $export_stock_id = 0;
            $this->db->where('invoice_id', $id);
            $export_stock = $this->db->get(db_prefix().'goods_delivery')->row();
            if($export_stock){
                $export_stock_id = $export_stock->id;
            }

            $this->db->where('id', $order_id);
            $this->db->update(db_prefix().'sa_pur_orders', ['invoice_id' => $id, 'stock_export_id' => $export_stock_id]);

            return $id;
        }

        return false;
    }


    /**
     * wh get grouped
     * @return [type] 
     */
    public function sa_get_grouped($can_be = '', $search_all = false)
    {
        $this->db->where(db_prefix().'items.active', 1);
        $this->db->order_by('description', 'asc');
        $items = $this->db->get(db_prefix() . 'items')->result_array();

        return $items;
    }

    /**
     * Gets the program product group.
     */
    public function get_program_product_group($program_id){
        $program_detail = $this->get_program_detail($program_id);

        $product_group_ids = [];
        foreach($program_detail as $detail){
            if($detail['product_group'] != ''){
                $groups = explode(',', $detail['product_group']);
                foreach($groups as $gr_id){
                    if(!in_array($gr_id, $product_group_ids)){
                        $product_group_ids[] = $gr_id;
                    }
                }
            }
        }

        $product_group_str = '';

        if(count($product_group_ids) > 0){
            $product_group_str = implode(',', $product_group_ids);
        }

        return $product_group_str;
    }

    /**
     * Gets the program product group.
     */
    public function get_program_product($program_id){
        $program_detail = $this->get_program_detail($program_id);

        $product_ids = [];
        foreach($program_detail as $detail){
            if($detail['product'] != ''){
                $products = explode(',', $detail['product']);
                foreach($products as $pd_id){
                    if(!in_array($pd_id, $product_ids)){
                        $product_ids[] = $pd_id;
                    }
                }
            }
        }

        $product_str = '';

        if(count($product_ids) > 0){
            $product_str = implode(',', $product_ids);
        }

        return $product_str;
    }

    /**
     * { get item }
     */
    public function get_item($id){
        if (is_numeric($id)) {
        $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'items')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from ' . db_prefix() . 'items where active = 1 order by id desc')->result_array();

        }
    }

    /**
     * { delete_sa_client_group }
     */
    public function delete_sa_client_group($id){
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'sa_client_groups');
        if($this->db->affected_rows() > 0){
            return true;
        }

        return false;
    }

    /**
     * check inventory delivery voucher
     * @param  array $data 
     * @return string       
     */
    public function check_inventory_delivery_voucher($order_id)
    {
        
        $flag_export_warehouse = 1;
        $str_error='';

        /*get goods delivery detail*/
        $this->db->where('pur_order', $order_id);
        $cart_details = $this->db->get(db_prefix().'sa_pur_order_detail')->result_array();

        if (count($cart_details) > 0) {

            foreach ($cart_details as $delivery_detail_key => $cart_detail) {

                $sku_code='';
                $commodity_code='';

                $item_value = $this->get_commodity($cart_detail['item_code']);
                if($item_value){
                    $sku_code .= $item_value->sku_code;
                    $commodity_code .= $item_value->commodity_code;
                }

                /*check export warehouse*/

                //checking Do not save the quantity of inventory with item
                if($this->check_item_without_checking_warehouse($cart_detail['item_code']) == true){

                    $inventory = $this->warehouse_model->get_inventory_by_commodity($cart_detail['item_code']);

                    if($inventory){
                        $inventory_number =  $inventory->inventory_number;

                        if((float)$inventory_number < (float)$cart_detail['quantity'] ){
                            $str_error .= _l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
                            $flag_export_warehouse =  0;
                        }

                    }else{
                        $str_error .=_l('item_has_sku_code'). $sku_code. ','. _l('commodity_code').' '. $commodity_code.':  '._l('not_enough_inventory');
                        $flag_export_warehouse =  0;
                    }
                }
            }
        }

        $result=[];
        $result['str_error'] = $str_error;
        $result['flag_export_warehouse'] = $flag_export_warehouse;

        return $result ;
    }

    /**
     * get shipment by order
     * @param  [type] $order_id 
     * @return [type]           
     */
    public function get_shipment_by_order($order_id)
    {
        if (is_numeric($order_id)) {
            $this->db->where('order_id', $order_id);
            return $this->db->get(db_prefix() . 'wh_omni_shipments')->row();
        }
        if ($order_id == false) {
            return $this->db->query('select * from '.db_prefix().'wh_omni_shipments')->result_array();
        }
    }

    /**
     * Gets the sales agent clients.
     *
     * @param        $agent_id  The agent identifier
     *
     * @return       The sales agent clients.
     */
    public function get_sales_agent_clients($agent_id){
        $this->db->where('agent_id', $agent_id);
        return $this->db->get(db_prefix().'sa_clients')->result_array();
    }


    /**
     * Gets the sale invoice.
     */
    public function get_sale_invoice($id){
        $this->db->where('id', $id);
        return $this->db->get(db_prefix().'sa_sale_invoices')->row();
    }


    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        $this->db->select('street,city,state,zip,country');
        $this->db->from(db_prefix() . 'sa_clients');
        $this->db->where('id', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street']  = clear_textarea_breaks($result[0]['street']);
            $result[0]['billing_city']  = $result[0]['city'];
            $result[0]['billing_state']  = $result[0]['state'];
            $result[0]['billing_zip']  = $result[0]['zip'];
            $result[0]['billing_country']  = $result[0]['country'];

            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['street']);
            $result[0]['shipping_city']  = $result[0]['city'];
            $result[0]['shipping_state']  = $result[0]['state'];
            $result[0]['shipping_zip']  = $result[0]['zip'];
            $result[0]['shipping_country']  = $result[0]['country'];
        }

        return $result;
    }

    /**
     * Gets the item by agent.
     */
    public function get_item_by_agent($agent_id){


        $produt_ids = get_product_ids_of_program_by_agent($agent_id);
        $group_ids = get_product_group_ids_of_program_by_agent($agent_id);

        $where = 'active = 1';

        if(count($produt_ids) > 0 && count($group_ids) == 0){
            $where .= ' AND id IN ('.implode(',', $produt_ids).') ';
        }else if(count($produt_ids) == 0 && count($group_ids) > 0){
            $where .= ' AND group_id IN ('.implode(',', $group_ids).') ';
        }else if(count($produt_ids) > 0 && count($group_ids) > 0){
            $where .= ' AND (id IN ('.implode(',', $produt_ids).') OR group_id IN ('.implode(',', $group_ids).'))';
        }else if(count($produt_ids) == 0 && count($group_ids) == 0){
            $where .= ' AND 1=2';
        }

        $this->db->where($where);
        return $this->db->get(db_prefix().'items')->result_array();
    }

    /**
     * Creates a purchase order row template.
     *
     * @param      string      $name              The name
     * @param      string      $item_name         The item name
     * @param      string      $item_description  The item description
     * @param      int|string  $quantity          The quantity
     * @param      string      $unit_name         The unit name
     * @param      int|string  $unit_price        The unit price
     * @param      string      $taxname           The taxname
     * @param      string      $item_code         The item code
     * @param      string      $unit_id           The unit identifier
     * @param      string      $tax_rate          The tax rate
     * @param      string      $total_money       The total money
     * @param      string      $discount          The discount
     * @param      string      $discount_money    The discount money
     * @param      string      $total             The total
     * @param      string      $into_money        Into money
     * @param      string      $tax_id            The tax identifier
     * @param      string      $tax_value         The tax value
     * @param      string      $item_key          The item key
     * @param      bool        $is_edit           Indicates if edit
     *
     * @return     string      
     */
    public function create_sale_invoice_row_template($name = '', $item_name = '', $item_description = '', $quantity = '', $unit_name = '', $unit_price = '', $taxname = '',  $item_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total = '', $into_money = '', $tax_id = '', $tax_value = '', $item_key = '',$is_edit = false, $currency_rate = 1, $to_currency = '') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_item_code = 'item_code';
        $name_item_name = 'item_name';
        $name_item_description = 'description';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantity = 'quantity';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total = 'total';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $name_tax_value = 'tax_value';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_into_money = 'into_money';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_money = 'total_money';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_money_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        $array_subtotal_attr = ['readonly' => true];
        $text_right_class = 'text-right';

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
         
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_item_code = $name . '[item_code]';
            $name_item_name = $name . '[item_name]';
            $name_item_description = $name . '[item_description]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantity = $name . '[quantity]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total = $name . '[total]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_into_money = $name .'[into_money]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_money = $name . '[total_money]';
            $name_tax_value = $name. '[tax_value]';
      
           
            $array_qty_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantity];
            

            $array_rate_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'pur_calculate_total();', 'onchange' => 'pur_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];

            $array_discount_money_attr = ['onblur' => 'pur_calculate_total(1);', 'onchange' => 'pur_calculate_total(1);', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = sa_convert_item_taxes($tax_id, $tax_rate, $taxname);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->sa_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantity * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantity + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantity + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantity;
                $amount = (float)$unit_price * (float)$quantity;
            }

            $sub_total = (float)$unit_price * (float)$quantity;
            $amount = app_format_number($amount);

        }
 

        $row .= '<td class="">' . render_textarea($name_item_name, '', $item_name, ['readonly'=> 1, 'rows' => 2, 'placeholder' => _l('pur_item_name')] ) . '</td>';

        $row .= '<td class="">' . render_textarea($name_item_description, '', $item_description, ['rows' => 2, 'placeholder' => _l('item_description')] ) . '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr, [], 'no-margin', $text_right_class);
        if( $unit_price != ''){
            $original_price = round( ($unit_price/$currency_rate), 2);
            $base_currency = get_base_currency();
            if($to_currency != 0 && $to_currency != $base_currency->id){
                $row .= render_input('original_price', '',app_format_money($original_price, $base_currency), 'text', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => _l('original_price'), 'disabled' => true], [], 'no-margin', 'input-transparent text-right pur_input_none');
            }

            $row .= '<input class="hide" name="og_price" disabled="true" value="'.$original_price.'">';
        }
       
        $row .= '<td class="quantities">' . 
        render_input($name_quantity, '', $quantity, 'number', $array_qty_attr, [], 'no-margin', $text_right_class) . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right pur_input_none').
        '</td>';
        
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';

        $row .= '<td class="tax_value">' . render_input($name_tax_value, '', $tax_value, 'number', $array_subtotal_attr, [], '', $text_right_class) . '</td>';

        $row .= '<td class="_total" align="right">' . $total . '</td>';

        if($discount_money > 0){
            $discount = '';
        }

        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr, [], '', $text_right_class) . '</td>';
        $row .= '<td class="discount_money" align="right">' . render_input($name_discount_money, '', $discount_money, 'number', $array_discount_money_attr, [], '', $text_right_class.' item_discount_money') . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . app_format_number($total_money) . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_item_code, '', $item_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';

        $row .= '<td class="hide _total_after_tax">' . render_input($name_total, '', $total, 'number', []) . '</td>';

     
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_money, '', $total_money, 'number', []) . '</td>';
        $row .= '<td class="hide _into_money">' . render_input($name_into_money, '', $into_money, 'number', []) . '</td>';

        if ($name == '') {
            $row .= '<td><button type="button" onclick="pur_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="pur_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * Gets the sale invoice detail.
     */
    public function get_sale_invoice_detail($invoice_id){
        $this->db->where('sale_invoice', $invoice_id);
        return $this->db->get(db_prefix().'sa_sale_invoice_details')->result_array();
    }

    /**
     * Adds a sale invoice.
     */
    public function add_sale_invoice($data){
        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        $agent_id = get_sale_agent_user_id();

        $data['agent_id'] = $agent_id;

        $data['allowed_payment_modes'] = isset($data['allowed_payment_modes']) ? serialize($data['allowed_payment_modes']) : serialize([]);

        $order_detail = [];
        if(isset($data['newitems'])){
            $order_detail = $data['newitems'];
            unset($data['newitems']);
        }

        $data['to_currency'] = $data['currency'];

        $data['addedfrom'] = get_sa_contact_user_id();

        $data['datecreated'] = date('Y-m-d');
        $data['status'] = 'unpaid';
        $prefix = get_sa_option('sale_invoice_prefix', $agent_id);

        $this->db->where('inv_number',$data['inv_number']);
        $check_exist_number = $this->db->get(db_prefix().'sa_sale_invoices')->row();

        while($check_exist_number) {
          $data['number'] = $data['number'] + 1;
          $data['inv_number'] =  $prefix.str_pad($data['number'],5,'0',STR_PAD_LEFT);
          $this->db->where('inv_number',$data['inv_number']);
          $check_exist_number = $this->db->get(db_prefix().'sa_sale_invoices')->row();
        }

        $data['hash'] = app_generate_hash();

        $data['date'] = to_sql_date($data['date']);
        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date($data['duedate']); 
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }
        unset($data['add_discount_type']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $this->db->insert(db_prefix().'sa_sale_invoices',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
             // Update next purchase order number in settings
            $next_number = $data['number']+1;
            $this->db->where('agent_id', $agent_id);
            $this->db->where('name', 'next_sale_invoice_number');
            $this->db->update(db_prefix() . 'sa_options',['value' =>  $next_number,]);

            $total = [];
            $total['total_tax'] = 0;

            if(count($order_detail) > 0){
                foreach($order_detail as $key => $rqd){ 
                    $dt_data = [];
                    $dt_data['sale_invoice'] = $insert_id;
                    $dt_data['item_code'] = $rqd['item_code'];
                    $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                    $dt_data['unit_price'] = $rqd['unit_price'];
                    $dt_data['into_money'] = $rqd['into_money'];
                    $dt_data['total'] = $rqd['total'];
                    $dt_data['tax_value'] = $rqd['tax_value'];
                    $dt_data['item_name'] = $rqd['item_name'];
                    $dt_data['description'] = $rqd['item_description'];
                    $dt_data['total_money'] = $rqd['total_money'];
                    $dt_data['discount_money'] = $rqd['discount_money'];
                    $dt_data['discount_percent'] = $rqd['discount'];

                    $tax_money = 0;
                    $tax_rate_value = 0;
                    $tax_rate = null;
                    $tax_id = null;
                    $tax_name = null;

                    if(isset($rqd['tax_select'])){
                        $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                        $tax_rate_value = $tax_rate_data['tax_rate'];
                        $tax_rate = $tax_rate_data['tax_rate_str'];
                        $tax_id = $tax_rate_data['tax_id_str'];
                        $tax_name = $tax_rate_data['tax_name_str'];
                    }

                    $dt_data['tax'] = $tax_id;
                    $dt_data['tax_rate'] = $tax_rate;
                    $dt_data['tax_name'] = $tax_name;

                    $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                    $this->db->insert(db_prefix().'sa_sale_invoice_details', $dt_data);


                    $total['total_tax'] += $rqd['tax_value'];
                }
            }

            $this->db->where('id',$insert_id);
            $this->db->update(db_prefix().'sa_sale_invoices',$total);

            return $insert_id;
        }
        return false;
    }

    /**
     * { update sale invoice }
     *
     * @param        $data   The data
     * @param        $id     The identifier
     */
    public function update_sale_invoice($data, $id){
        $data['date'] = to_sql_date($data['date']);
        if($data['duedate'] != ''){
           $data['duedate'] = to_sql_date($data['duedate']); 
        }

        $data['allowed_payment_modes'] = isset($data['allowed_payment_modes']) ? serialize($data['allowed_payment_modes']) : serialize([]);

        $affectedRows = 0;

        unset($data['item_select']);
        unset($data['item_name']);
        unset($data['description']);
        unset($data['total']);
        unset($data['quantity']);
        unset($data['unit_price']);
        unset($data['unit_name']);
        unset($data['item_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['into_money']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_money']);
        unset($data['additional_discount']);
        unset($data['tax_value']);
        if(isset($data['tax_select'])){
            unset($data['tax_select']);
        }

        unset($data['isedit']);

        if(isset($data['dc_total'])){
            $data['discount_total'] = $data['dc_total'];
            unset($data['dc_total']);
        }

        $data['to_currency'] = $data['currency'];

        if(isset($data['total_mn'])){
            $data['subtotal'] = $data['total_mn'];
            unset($data['total_mn']);
        }

        if(isset($data['grand_total'])){
            $data['total'] = $data['grand_total'];
            unset($data['grand_total']);
        }

        $new_order = [];
        if(isset($data['newitems'])){
            $new_order = $data['newitems'];
            unset($data['newitems']);
        }

        $update_order = [];
        if(isset($data['items'])) {
            $update_order = $data['items'];
            unset($data['items']);
        }

        $remove_order = [];
        if(isset($data['removed_items'])){
            $remove_order = $data['removed_items'];
            unset($data['removed_items']);
        }

        if(isset($data['order_discount'])){
            $order_discount = $data['order_discount'];
            if($data['add_discount_type'] == 'percent'){
                $data['discount_percent'] = $order_discount;
            }

            unset($data['order_discount']);
        }
        unset($data['add_discount_type']);

        if(count($new_order) > 0){
            foreach($new_order as $key => $rqd){

                $dt_data = [];
                $dt_data['sale_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->insert(db_prefix().'sa_sale_invoice_details', $dt_data);
                $new_quote_insert_id = $this->db->insert_id();
                if($new_quote_insert_id){
                    $affectedRows++;
                }
            }
        }

        if(count($update_order) > 0){
            foreach($update_order as $_key => $rqd){
                $dt_data = [];
                $dt_data['sale_invoice'] = $id;
                $dt_data['item_code'] = $rqd['item_code'];
                $dt_data['unit_id'] = isset($rqd['unit_id']) ? $rqd['unit_id'] : null;
                $dt_data['unit_price'] = $rqd['unit_price'];
                $dt_data['into_money'] = $rqd['into_money'];
                $dt_data['total'] = $rqd['total'];
                $dt_data['tax_value'] = $rqd['tax_value'];
                $dt_data['item_name'] = $rqd['item_name'];
                $dt_data['total_money'] = $rqd['total_money'];
                $dt_data['discount_money'] = $rqd['discount_money'];
                $dt_data['discount_percent'] = $rqd['discount'];
                $dt_data['description'] = $rqd['item_description'];

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;

                if(isset($rqd['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($rqd['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                $dt_data['tax'] = $tax_id;
                $dt_data['tax_rate'] = $tax_rate;
                $dt_data['tax_name'] = $tax_name;

                $dt_data['quantity'] = ($rqd['quantity'] != ''&& $rqd['quantity'] != null) ? $rqd['quantity'] : 0;

                $this->db->where('id', $rqd['id']);
                $this->db->update(db_prefix().'sa_sale_invoice_details', $dt_data);
                if($this->db->affected_rows() > 0){
                    $affectedRows++;
                }
            }
        }

        if(count($remove_order) > 0){ 
            foreach($remove_order as $remove_id){
                $this->db->where('id', $remove_id);
                if ($this->db->delete(db_prefix() . 'sa_sale_invoice_details')) {
                    $affectedRows++;
                }
            }
        }

        $order_detail_after_update = $this->get_sale_invoice_detail($id);
        $total = [];
        $data['total_tax'] = 0;
        if(count($order_detail_after_update) > 0){
            foreach($order_detail_after_update as $dt){
                $data['total_tax'] += $dt['tax_value'];
            }
        }

        $this->db->where('id',$id);
        $this->db->update(db_prefix().'sa_sale_invoices',$data);
        if($this->db->affected_rows() > 0){

            $affectedRows++;
        }

        if($affectedRows > 0){
            return true;
        }
        return false;

    }

    /**
     * { delete sale invoice }
     */
    public function delete_sale_invoice($id){
        $affectedRows = 0;

        $this->db->where('sale_invoice', $id);
        $this->db->delete(db_prefix().'sa_sale_invoice_details');
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        $this->db->where('sale_invoice', $id);
        $this->db->delete(db_prefix().'sa_sale_invoice_payment');
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'sa_sale_invoices');
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }

        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * Gets the html tax pur order.
     */
    public function get_html_tax_sale_invoice($id){
        $html = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $this->db->where('sale_invoice', $id);
        $details = $this->db->get(db_prefix().'sa_sale_invoice_details')->result_array();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['into_money']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td width="33%"></td><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').' '.($base_currency->name).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        return $rs;
    }

    /**
     * Gets the payment sale invoice.
     */
    public function get_payment_sale_invoice($invoice){
        $this->db->where('sale_invoice', $invoice);
        return $this->db->get(db_prefix().'sa_sale_invoice_payment')->result_array();
    }

    /**
     * Adds a invoice payment.
     *
     * @param         $data       The data
     * @param         $invoice  The invoice id
     *
     * @return     boolean  
     */
    public function add_invoice_payment($data, $invoice){
        $data['date'] = to_sql_date($data['date']);
        $data['daterecorded'] = date('Y-m-d H:i:s');
        
        $data['sale_invoice'] = $invoice;
        $data['requester'] = get_sa_contact_user_id();

        $this->db->insert(db_prefix().'sa_sale_invoice_payment',$data);
        $insert_id = $this->db->insert_id();
        if($insert_id){
            $sale_invoice = $this->get_sale_invoice($invoice);
            if($sale_invoice){
                $status_inv = $sale_invoice->status;
                if(saleinvoice_left_to_pay($invoice) > 0){
                    $status_inv = 'partially_paid';
                }else{
                    $status_inv = 'paid';
                }
                $this->db->where('id',$invoice);
                $this->db->update(db_prefix().'sa_sale_invoices', [ 'status' => $status_inv, ]);
            }
            return $insert_id;
        }
        return false;
    }

    /**
     * { delete_payment_sale_invoice }
     *
     * @param        $id          The identifier
     * @param        $invoice_id  The invoice identifier
     */
    public function delete_payment_sale_invoice($id, $invoice_id){

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'sa_sale_invoice_payment');
        if($this->db->affected_rows() > 0){
            $sale_invoice = $this->get_sale_invoice($invoice_id);
            if($sale_invoice){
                $status_inv = $sale_invoice->status;
                if(saleinvoice_left_to_pay($invoice_id) > 0){
                    $status_inv = 'partially_paid';
                    if(saleinvoice_left_to_pay($invoice_id) == $sale_invoice->total){
                        $status_inv = 'unpaid';
                    }
                }else{
                    $status_inv = 'paid';
                }
                $this->db->where('id',$invoice_id);
                $this->db->update(db_prefix().'sa_sale_invoices', [ 'status' => $status_inv, ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Gets the purchase order attachments.
     *
     * @param      <type>  $id     The purchase order
     *
     * @return     <type>  The purchase order attachments.
     */
    public function get_sale_invoice_attachments($id){
   
        $this->db->where('rel_id',$id);
        $this->db->where('rel_type','sa_sale_invoice');
        return $this->db->get(db_prefix().'files')->result_array();
    }

    /**
     * Gets the part attachments.
     *
     * @param      <type>  $surope  The surope
     * @param      string  $id      The identifier
     *
     * @return     <type>  The part attachments.
     */
    public function get_saleinvoice_attachments($surope, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $assets);
        }
        $this->db->where('rel_type', 'sa_sale_invoice');
        $result = $this->db->get(db_prefix().'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     * { delete purorder attachment }
     *
     * @param      <type>   $id     The identifier
     *
     * @return     boolean 
     */
    public function delete_sale_invoice_attachment($id)
    {
        $attachment = $this->get_saleinvoice_attachments('', $id);
        $deleted    = false;

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'files');  
        if ($this->db->affected_rows() > 0) {
            $deleted = true;
        }

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/sale_invoice/'. $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix().'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }

            if (is_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/sale_invoice/'. $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/sale_invoice/'. $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/sale_invoice/'. $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }


    /**
     * get warehouse
     * @param  boolean $id
     * @return array or object
     */
    public function get_warehouse($id = false) {

        if (is_numeric($id)) {
            $this->db->where('warehouse_id', $id);

            return $this->db->get(db_prefix() . 'sa_warehouse')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'sa_warehouse where display = 1 order by '.db_prefix().'sa_warehouse.order asc')->result_array();
        }

    }


    /**
     * add one warehouse
     * @param [type] $data 
     */
    public function add_one_warehouse($data) {

        $option = 'off';
        if (isset($data['display'])) {
            $option = $data['display'];
            unset($data['display']);
        }

        if ($option == 'on') {
            $data['display'] = 1;
        } else {
            $data['display'] = 0;
        }

        $this->db->insert(db_prefix() . 'sa_warehouse', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }


        return false;
    }

    /**
     * update color
     * @param  array $data
     * @param  integer $id
     * @return boolean
     */
    public function update_one_warehouse($data, $id) {
        $option = 'off';
        if (isset($data['display'])) {
            $option = $data['display'];
            unset($data['display']);
        }

        if ($option == 'on') {
            $data['display'] = 1;
        } else {
            $data['display'] = 0;
        }

        $affectedRows = 0;

        $this->db->where('warehouse_id', $id);
        $this->db->update(db_prefix() . 'sa_warehouse', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            return true;
        }

        return true;
    }

    /**
     * delete warehouse
     * @param  integer $id
     * @return boolean
     */
    public function delete_warehouse($id) {
        $this->db->where('warehouse_id', $id);
        $this->db->delete(db_prefix() . 'sa_warehouse');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get inventory by warehouse
     * @param  integer $warehouse_id 
     * @return array               
     */
    public function get_inventory_by_warehouse($warehouse_id) {
        
        $sql = 'SELECT sum(inventory_number) as inventory_number, commodity_id, warehouse_id FROM '.db_prefix().'sa_inventory_manage
        where '.db_prefix().'sa_inventory_manage.warehouse_id = '.$warehouse_id.'
        group by commodity_id
        order by '.db_prefix().'sa_inventory_manage.commodity_id asc';

        return $this->db->query($sql)->result_array();

    }


    /**
     * arr inventory number by item
     * @return [type] 
     */
    public function arr_inventory_number_by_item($agent_id)
    {   
        $arr_inventory_number = [];
        $sql = 'SELECT commodity_id, sum(inventory_number) as inventory_number FROM ' . db_prefix() . 'sa_inventory_manage where agent_id = '.$agent_id.'
         group by ' . db_prefix() . 'sa_inventory_manage.commodity_id';
        $data = $this->db->query($sql)->result_array(); 

        foreach ($data as $key => $value) {
            $arr_inventory_number[$value['commodity_id']] = $value;
        }
        return $arr_inventory_number;
    }


        /**
     * Gets the commodity code name.
     *
     * @return       The commodity code name.
     */
    public function get_commodity_code_name() {
        $arr_value = $this->db->query('select * from ' . db_prefix() . 'items where active = 1 order by id desc')->result_array();
        return $this->item_to_variation($arr_value);

    }

    /**
     * { item to variation }
     *
     * @param        $array_value  The array value
     *
     * @return     array   
     */
    public function item_to_variation($array_value)
    {
        $new_array=[];
        foreach ($array_value as $key =>  $values) {

            $name = '';
            if($values['attributes'] != null && $values['attributes'] != ''){
                $attributes_decode = json_decode($values['attributes']);

                foreach ($attributes_decode as $n_value) {
                    if(is_array($n_value)){
                        foreach ($n_value as $n_n_value) {
                            if(strlen($name) > 0){
                                $name .= '#'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }else{
                                $name .= ' #'.$n_n_value->name.' ( '.$n_n_value->option.' ) ';
                            }
                        }
                    }else{

                        if(strlen($name) > 0){
                            $name .= '#'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }else{
                            $name .= ' #'.$n_value->name.' ( '.$n_value->option.' ) ';
                        }
                    }
                }


            }
            array_push($new_array, [
                'id' => $values['id'],
                'label' => $values['commodity_code'].'_'.$values['description'],

            ]);
        }
        return $new_array;
    }

    /**
     * get unit code name
     * @return array
     */
    public function get_units_code_name() {
        return $this->db->query('select unit_type_id as id, unit_name as label from ' . db_prefix() . 'ware_unit_type')->result_array();
    }

    /**
     * get warehouse code name
     * @return array
     */
    public function get_warehouse_code_name($agent_id) {
        return $this->db->query('select warehouse_id as id, warehouse_name as label from ' . db_prefix() . 'sa_warehouse where agent_id = '.$agent_id.' AND display = 1 order by '.db_prefix().'sa_warehouse.order asc')->result_array();
    }

    /**
     * get commodity
     * @param  boolean $id
     * @return array or object
     */
    public function get_commodity($id = false) {

        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'items')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'items')->result_array();
        }

    }

    /**
     * get warehouse
     * @param  boolean $id
     * @return array or object
     */
    public function get_warehouse_by_agent($agent_id) {
        
        return $this->db->query('select * from '.db_prefix().'sa_warehouse where agent_id = '.$agent_id.' AND display = 1 order by '.db_prefix().'sa_warehouse.order asc')->result_array();
        
    }

    /**
     * create goods code
     * @return  string
     */
    public function create_goods_code($agent_id) {
        
        $goods_code = get_sa_option('inventory_received_number_prefix', $agent_id) . get_sa_option('next_inventory_received_mumber', $agent_id);
        
        return $goods_code;

    }


    /**
     * get goods receipt
     * @param  integer $id
     * @return array or object
     */
    public function get_goods_receipt($id) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'sa_goods_receipt')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'sa_goods_receipt')->result_array();
        }
    }


    /**
     * get goods receipt detail
     * @param  integer $id
     * @return array
     */
    public function get_goods_receipt_detail($id) {
        if (is_numeric($id)) {
            $this->db->where('goods_receipt_id', $id);

            return $this->db->get(db_prefix() . 'sa_goods_receipt_detail')->result_array();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'sa_goods_receipt_detail')->result_array();
        }
    }

    /**
     * Gets the html tax receip.
     */
    public function get_html_tax_receip($id){
        $html = '';
        $preview_html = '';
        $html_currency = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        
        $this->db->where('goods_receipt_id', $id);
        $details = $this->db->get(db_prefix().'sa_goods_receipt_detail')->result_array();

        foreach($details as $row){
            if($row['tax'] != ''){
                $tax_arr = explode('|', $row['tax']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['quantities']*$row_dt['unit_price']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
                $html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        $rs['html_currency'] = $html_currency;
        return $rs;
    }


    /**
     * create goods receipt row template
     * @param  array   $warehouse_data   
     * @param  string  $name             
     * @param  string  $commodity_name   
     * @param  string  $warehouse_id     
     * @param  string  $quantities       
     * @param  string  $unit_name        
     * @param  string  $unit_price       
     * @param  string  $taxname          
     * @param  string  $lot_number       
     * @param  string  $date_manufacture 
     * @param  string  $expiry_date      
     * @param  string  $commodity_code   
     * @param  string  $unit_id          
     * @param  string  $tax_rate         
     * @param  string  $tax_money        
     * @param  string  $goods_money      
     * @param  string  $note             
     * @param  string  $item_key         
     * @param  string  $sub_total        
     * @param  string  $tax_name         
     * @param  string  $tax_id           
     * @param  boolean $is_edit          
     * @return [type]                    
     */
    public function create_goods_receipt_row_template($warehouse_data = [], $name = '', $commodity_name = '', $warehouse_id = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '', $lot_number = '', $date_manufacture = '', $expiry_date = '', $commodity_code = '', $unit_id = '', $tax_rate = '', $tax_money = '', $goods_money = '', $note = '', $item_key = '', $sub_total = '', $tax_name = '', $tax_id = '', $is_edit = false, $serial_number = '') {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_commodity_code = 'commodity_code';
        $name_commodity_name = 'commodity_name';
        $name_warehouse_id = 'warehouse_id';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_quantities = 'quantities';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax';
        $name_tax_money = 'tax_money';
        $name_goods_money = 'goods_money';
        $name_date_manufacture = 'date_manufacture';
        $name_expiry_date = 'expiry_date';
        $name_note = 'note';
        $name_lot_number = 'lot_number';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_sub_total = 'sub_total';
        $name_serial_number = 'serial_number';

        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        if(count($warehouse_data) == 0){
            $warehouse_data = $this->get_warehouse_by_agent(get_sale_agent_user_id());
        }

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];

            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_commodity_code = $name . '[commodity_code]';
            $name_commodity_name = $name . '[commodity_name]';
            $name_warehouse_id = $name . '[warehouse_id]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_quantities = $name . '[quantities]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax]';
            $name_tax_money = $name . '[tax_money]';
            $name_goods_money = $name . '[goods_money]';
            $name_date_manufacture = $name . '[date_manufacture]';
            $name_expiry_date = $name . '[expiry_date]';
            $name_note = $name . '[note]';
            $name_lot_number = $name . '[lot_number]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_sub_total = $name .'[sub_total]';
            $name_serial_number = $name .'[serial_number]';

            $array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('unit_price')];

            $array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];

            //case for delivery note: only get warehouse available quantity
      

            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = sa_convert_item_taxes($tax_id, $tax_rate, $tax_name);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->sa_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantities;
                $amount = (float)$unit_price * (float)$quantities;
            }

            $sub_total = (float)$unit_price * (float)$quantities;
            $amount = app_format_number($amount);

        }
        $clients_attr = ["onchange" => "get_vehicle('" . $name_commodity_code . "','" . $name_unit_id . "','" . $name_warehouse_id . "');", "data-none-selected-text" => _l('customer_name'), 'data-customer_id' => 'invoice'];

        $row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';
        $row .= '<td class="warehouse_select">' .
       
        sa_render_select($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id,[], ["data-none-selected-text" => _l('warehouse_name')], 'no-margin').
        render_input($name_note, '', $note, 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').
        '</td>';
        $row .= '<td class="quantities">' . 
        render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin') . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
        '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
        $row .= '<td>' . render_input($name_lot_number, '', $lot_number, 'text', ['placeholder' => _l('lot_number')]) . '</td>';
        $row .= '<td>' . sa_render_date_input($name_date_manufacture, '', $date_manufacture, ['placeholder' => _l('date_manufacture')]) . '</td>';
        $row .= '<td>' . sa_render_date_input($name_expiry_date, '', $expiry_date, ['placeholder' => _l('expiry_date')]) . '</td>';
        $row .= '<td class="amount" align="right">' . $amount . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
        $row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', ['placeholder' => _l('serial_number')]) . '</td>';

        if(strlen($serial_number) > 0){
            $name_serial_number_tooltip = _l('wh_serial_number').': '.$serial_number;
        }else{
            $name_serial_number_tooltip = _l('wh_view_serial_number');
        }

        if ($name == '') {
            $row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;" data-toggle="tooltip" data-original-title="'._l('delete').'"><i class="fa fa-trash"></i></a></td>';

            if(get_option('sa_wh_products_by_serial') == 1){
                $row .= '<td><a href="javascript:void(0)" class="btn btn-success pull-right" onclick="wh_view_serial_number( \''. $name_quantities . '\', \''. $name_serial_number . '\',\''. $name . '\'); return false;" data-toggle="tooltip" data-original-title="'.$name_serial_number_tooltip.'"><i class="fa fa-eye"></i></a></td>';
            }

        }
        $row .= '</tr>';
        return $row;
    }


    /**
     * get commodity hansometable by barcode
     * @param  [type] $commodity barcode 
     * @return [type]                    
     */
    public function get_commodity_hansometable_by_barcode($commodity_barcode) {

        $item_value = $this->db->query('select description, rate, unit_id, taxrate, purchase_price, attributes, tax2,'.db_prefix().'items.tax,' . db_prefix() . 'taxes.name,'.db_prefix().'items.id ,'.db_prefix().'items.commodity_barcode,'.db_prefix().'items.commodity_code from ' . db_prefix() . 'items left join ' . db_prefix() . 'ware_unit_type on  ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id
            left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'items.tax = ' . db_prefix() . 'taxes.id where (' . db_prefix() . 'items.commodity_barcode = ' . $commodity_barcode.' OR '.db_prefix().'items.commodity_barcode = '.substr($commodity_barcode, 0, -1).')')->row();

        return $this->row_item_to_variation($item_value);
    }


    /**
     * get commodity warehouse
     * @param  boolean $id
     * @return array
     */
    public function get_commodity_warehouse($commodity_id = false) {
        if ($commodity_id != false) {

            $sql = 'SELECT ' . db_prefix() . 'sa_warehouse.warehouse_name, '.db_prefix().'sa_warehouse.warehouse_id, '.db_prefix().'sa_inventory_manage.inventory_number FROM ' . db_prefix() . 'sa_inventory_manage
            LEFT JOIN ' . db_prefix() . 'sa_warehouse on ' . db_prefix() . 'sa_inventory_manage.warehouse_id = ' . db_prefix() . 'sa_warehouse.warehouse_id
            where ' . db_prefix() . 'sa_inventory_manage.commodity_id = ' . $commodity_id.' and ' . db_prefix() . 'sa_inventory_manage.agent_id = '.get_sale_agent_user_id().' order by '.db_prefix().'sa_warehouse.order asc';

            return $this->db->query($sql)->result_array();
        }

    }

    /**
     * get adjustment stock quantity
     * @param  [type] $warehouse_id 
     * @param  [type] $commodity_id 
     * @param  [type] $lot_number   
     * @param  [type] $expiry_date  
     * @return [type]               
     */
    public function get_adjustment_stock_quantity($warehouse_id, $commodity_id, $lot_number, $expiry_date) {



        if(isset($lot_number) && $lot_number != '0' && $lot_number != ''){
            /*have value*/
            $this->db->where('lot_number', $lot_number);

        }else{

            /*lot number is 0 or ''*/
            $this->db->group_start();

            $this->db->where('lot_number', '0');
            $this->db->or_where('lot_number', '');
            $this->db->or_where('lot_number', null);

            $this->db->group_end();
        }

        $this->db->where('warehouse_id', $warehouse_id);
        $this->db->where('commodity_id', $commodity_id);
        $this->db->where('agent_id', get_sale_agent_user_id());

        if($expiry_date == ''){
            $this->db->where('expiry_date', null);
        }else{
            $this->db->where('expiry_date', $expiry_date);
        }

        return $this->db->get(db_prefix() . 'sa_inventory_manage')->row();


    }


    /**
     * get purchase request
     * @param  integer $pur_order
     * @return array
     */
    public function get_pur_request($pur_order) {

        $arr_pur_resquest = [];
        $total_goods_money = 0;
        $total_money = 0;
        $total_tax_money = 0;
        $value_of_inventory = 0;
        $list_item = '';
        $list_item = $this->create_goods_receipt_row_template();

        $sql = 'select item_code as commodity_code, ' . db_prefix() . 'items.description, ' . db_prefix() . 'items.unit_id, unit_price, quantity as quantities, ' . db_prefix() . 'sa_pur_order_detail.tax as tax, into_money, (' . db_prefix() . 'sa_pur_order_detail.total-' . db_prefix() . 'sa_pur_order_detail.into_money) as tax_money, total as goods_money, wh_quantity_received, tax_rate, tax_value, '.db_prefix().'sa_pur_order_detail.id as id from ' . db_prefix() . 'sa_pur_order_detail
        left join ' . db_prefix() . 'items on ' . db_prefix() . 'sa_pur_order_detail.item_code =  ' . db_prefix() . 'items.id
        left join ' . db_prefix() . 'taxes on ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'sa_pur_order_detail.tax where ' . db_prefix() . 'sa_pur_order_detail.pur_order = ' . $pur_order;
        $results = $this->db->query($sql)->result_array();

        $arr_results=[];
        $index=0;
        $warehouse_data = $this->get_warehouse_by_agent(get_sale_agent_user_id());
        foreach ($results as $key => $value) {

            if((float)$value['quantities'] - (float)$value['wh_quantity_received'] > 0){

                $index++;
                $unit_name = sa_get_unit_name($value['unit_id']);
                $taxname = '';
                $date_manufacture = null;
                $expiry_date = null;
                $lot_number = null;
                $note = null;
                $commodity_name = sa_get_item_variatiom($value['commodity_code']);
                $quantities = (float)$value['quantities'] - (float)$value['wh_quantity_received'];
                $sub_total = 0;

                $list_item .= $this->create_goods_receipt_row_template($warehouse_data, 'newitems[' . $index . ']', $commodity_name, '', $quantities, $unit_name, $value['unit_price'], $taxname, $lot_number, $date_manufacture, $expiry_date, $value['commodity_code'], $value['unit_id'] , $value['tax_rate'], $value['tax_value'], $value['goods_money'], $note, $value['id'], $sub_total, '', $value['tax'], true);

                $total_goods_money_temp = ((float)$value['quantities'] - (float)$value['wh_quantity_received'])*(float)$value['unit_price'];
                $total_goods_money += $total_goods_money_temp;
                $arr_results[$index]['quantities'] = (float)$value['quantities'] - (float)$value['wh_quantity_received'];
                $arr_results[$index]['goods_money'] = ((float)$value['quantities'] - (float)$value['wh_quantity_received'])*(float)$value['unit_price'];


                //get tax value
                $tax_rate = 0 ;
                if($value['tax'] != null && $value['tax'] != '') {
                    $arr_tax = explode('|', $value['tax']);
                    foreach ($arr_tax as $tax_id) {

                        $tax = $this->get_taxe_value($tax_id);
                        if($tax){
                            $tax_rate += (float)$tax->taxrate;              
                        }

                    }
                }

                $arr_results[$index]['tax_money'] = $total_goods_money_temp*(float)$tax_rate/100;
                $total_tax_money += (float)$total_goods_money_temp*(float)$tax_rate/100;

            }
            
        }


        $total_money = $total_goods_money + $total_tax_money;
        $value_of_inventory = $total_goods_money;

        $arr_pur_resquest[] = $arr_results;
        $arr_pur_resquest[] = $total_tax_money;
        $arr_pur_resquest[] = $total_goods_money;
        $arr_pur_resquest[] = $value_of_inventory;
        $arr_pur_resquest[] = $total_money;
        $arr_pur_resquest[] = count($arr_results);
        $arr_pur_resquest[] = $list_item;


        return $arr_pur_resquest;
    }


        /**
     * Gets the taxes.
     *
     * @return     <array>  The taxes.
     */
    public function get_taxe_value($id)
    {
        return $this->db->query('select id, name as label, taxrate from '.db_prefix().'taxes where id = '.$id)->row();
    }


    /**
     * add goods
     * @param array $data
     * @param boolean $id
     * return boolean
     */
    public function add_goods_receipt($data, $id = false) {

        $agent_id = get_sale_agent_user_id();

        $inventory_receipts = [];
        if (isset($data['newitems'])) {
            $inventory_receipts = $data['newitems'];
            unset($data['newitems']);
        }

        unset($data['item_select']);
        unset($data['commodity_name']);
        unset($data['warehouse_id']);
        unset($data['quantities']);
        unset($data['unit_price']);
        unset($data['tax']);
        unset($data['lot_number']);
        unset($data['date_manufacture']);
        unset($data['expiry_date']);
        unset($data['note']);
        unset($data['unit_name']);
        unset($data['sub_total']);
        unset($data['commodity_code']);
        unset($data['unit_id']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['tax_money']);
        unset($data['goods_money']);
        unset($data['serial_number']);

        if(isset($data['warehouse_id_m'])){
            $data['warehouse_id'] = $data['warehouse_id_m'];
            unset($data['warehouse_id_m']);
        }

        if(isset($data['expiry_date_m'])){
            $data['expiry_date'] = to_sql_date($data['expiry_date_m']);
            unset($data['expiry_date_m']);
        }
        
        if(isset($data['onoffswitch'])){
            if($data['onoffswitch'] == 'on'){
                $switch_barcode_scanners = true;
                unset($data['onoffswitch']);
            }
        }


        if(isset($data['save_and_send_request']) ){
            $save_and_send_request = $data['save_and_send_request'];
            unset($data['save_and_send_request']);
        }
 

        if (isset($data['hot_purchase'])) {
  
            unset($data['hot_purchase']);
        }

        $data['goods_receipt_code'] = $this->create_goods_code($agent_id);
        
        if(!$this->check_format_date($data['date_c'])){
            $data['date_c'] = to_sql_date($data['date_c']);
        }else{
            $data['date_c'] = $data['date_c'];
        }
        
        if(!$this->check_format_date($data['date_add'])){
            $data['date_add'] = to_sql_date($data['date_add']);
        }else{
            $data['date_add'] = $data['date_add'];
        }

        $data['addedfrom'] = get_sa_contact_user_id();
        $data['total_tax_money'] = sa_reformat_currency_j($data['total_tax_money']);
        $data['total_goods_money'] = sa_reformat_currency_j($data['total_goods_money']);
        $data['value_of_inventory'] = sa_reformat_currency_j($data['value_of_inventory']);
        $data['total_money'] = sa_reformat_currency_j($data['total_money']);
        $data['agent_id'] = $agent_id;

        $this->db->insert(db_prefix() . 'sa_goods_receipt', $data);
        $insert_id = $this->db->insert_id();

        /*insert detail*/
        if ($insert_id) {
            foreach ($inventory_receipts as $inventory_receipt) {
                $inventory_receipt['goods_receipt_id'] = $insert_id;
                if($inventory_receipt['date_manufacture'] != ''){
                    $inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
                }else{
                    $inventory_receipt['date_manufacture'] = null;
                }

                if($inventory_receipt['expiry_date'] != ''){
                    $inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
                }else{
                    $inventory_receipt['expiry_date'] = null;
                }

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($inventory_receipt['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($inventory_receipt['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
                    $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
                    $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
                }else{
                    $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
                    $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
                }

                $sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

                $inventory_receipt['tax_money'] = $tax_money;
                $inventory_receipt['tax'] = $tax_id;
                $inventory_receipt['goods_money'] = $goods_money;
                $inventory_receipt['tax_rate'] = $tax_rate;
                $inventory_receipt['sub_total'] = $sub_total;
                $inventory_receipt['tax_name'] = $tax_name;
                unset($inventory_receipt['order']);
                unset($inventory_receipt['id']);
                unset($inventory_receipt['tax_select']);

                $this->db->insert(db_prefix() . 'sa_goods_receipt_detail', $inventory_receipt);
            }
        }

        if (isset($insert_id)) {
            /*write log*/

            $next_number = get_sa_option('next_inventory_received_mumber', $agent_id) +1;
            $this->db->where('agent_id', $agent_id);
            $this->db->where('name', 'next_inventory_received_mumber');
            $this->db->update(db_prefix() . 'sa_options',['value' =>  $next_number,]);

        }

        //approval if not approval setting
        if (isset($insert_id)) {
            $this->update_approve_request_wh($insert_id, 1, 1, $agent_id);  
        }

        return $insert_id > 0 ? $insert_id : false;

    }


    /**
     * update goods receipt
     * @param  array  $data 
     * @param  boolean $id   
     * @return [type]        
     */
    public function update_goods_receipt($data, $id = false) {

        $agent_id = get_sale_agent_user_id();

        $inventory_receipts = [];
        $update_inventory_receipts = [];
        $remove_inventory_receipts = [];
        if(isset($data['isedit'])){
            unset($data['isedit']);
        }

        if (isset($data['newitems'])) {
            $inventory_receipts = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['items'])) {
            $update_inventory_receipts = $data['items'];
            unset($data['items']);
        }
        if (isset($data['removed_items'])) {
            $remove_inventory_receipts = $data['removed_items'];
            unset($data['removed_items']);
        }
        unset($data['item_select']);
        unset($data['commodity_name']);
        unset($data['warehouse_id']);
        unset($data['quantities']);
        unset($data['unit_price']);
        unset($data['tax']);
        unset($data['lot_number']);
        unset($data['date_manufacture']);
        unset($data['expiry_date']);
        unset($data['note']);
        unset($data['unit_name']);
        unset($data['sub_total']);
        unset($data['commodity_code']);
        unset($data['unit_id']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['tax_money']);
        unset($data['goods_money']);
        unset($data['serial_number']);

        if(isset($data['warehouse_id_m'])){
            $data['warehouse_id'] = $data['warehouse_id_m'];
            unset($data['warehouse_id_m']);
        }

        if(isset($data['expiry_date_m'])){
            $data['expiry_date'] = to_sql_date($data['expiry_date_m']);
            unset($data['expiry_date_m']);
        }


        if(isset($data['save_and_send_request'])){
            $save_and_send_request = $data['save_and_send_request'];
            unset($data['save_and_send_request']);
        }

        if (isset($data['hot_purchase'])) {
            $hot_purchase = $data['hot_purchase'];
            unset($data['hot_purchase']);
        }
        
        if(!$this->check_format_date($data['date_c'])){
            $data['date_c'] = to_sql_date($data['date_c']);
        }else{
            $data['date_c'] = $data['date_c'];
        }
        
        if(!$this->check_format_date($data['date_add'])){
            $data['date_add'] = to_sql_date($data['date_add']);
        }else{
            $data['date_add'] = $data['date_add'];
        }


        $data['addedfrom'] = get_staff_user_id();

        $data['total_tax_money'] = sa_reformat_currency_j($data['total_tax_money']);

        $data['total_goods_money'] = sa_reformat_currency_j($data['total_goods_money']);
        $data['value_of_inventory'] = sa_reformat_currency_j($data['value_of_inventory']);

        $data['total_money'] = sa_reformat_currency_j($data['total_money']);

        $goods_receipt_id = $data['id'];
        unset($data['id']);

        $results = 0;

        $this->db->where('id', $goods_receipt_id);
        $this->db->update(db_prefix() . 'sa_goods_receipt', $data);
        if ($this->db->affected_rows() > 0) {
            $results++;
        }

        /*update save note*/
        // update receipt note
        foreach ($update_inventory_receipts as $inventory_receipt) {
            if($inventory_receipt['date_manufacture'] != ''){
                $inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
            }else{
                $inventory_receipt['date_manufacture'] = null;
            }

            if($inventory_receipt['expiry_date'] != ''){
                $inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
            }else{
                $inventory_receipt['expiry_date'] = null;
            }

            $tax_money = 0;
            $tax_rate_value = 0;
            $tax_rate = null;
            $tax_id = null;
            $tax_name = null;
            if(isset($inventory_receipt['tax_select'])){
                $tax_rate_data = $this->sa_get_tax_rate($inventory_receipt['tax_select']);
                $tax_rate_value = $tax_rate_data['tax_rate'];
                $tax_rate = $tax_rate_data['tax_rate_str'];
                $tax_id = $tax_rate_data['tax_id_str'];
                $tax_name = $tax_rate_data['tax_name_str'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
                $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
                $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
            }else{
                $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
                $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
            }

            $sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

            $inventory_receipt['tax_money'] = $tax_money;
            $inventory_receipt['tax'] = $tax_id;
            $inventory_receipt['goods_money'] = $goods_money;
            $inventory_receipt['tax_rate'] = $tax_rate;
            $inventory_receipt['sub_total'] = $sub_total;
            $inventory_receipt['tax_name'] = $tax_name;
            unset($inventory_receipt['order']);
            unset($inventory_receipt['tax_select']);

            $this->db->where('id', $inventory_receipt['id']);
            if ($this->db->update(db_prefix() . 'sa_goods_receipt_detail', $inventory_receipt)) {
                $results++;
            }
        }

        // delete receipt note
        foreach ($remove_inventory_receipts as $receipt_detail_id) {
            $this->db->where('id', $receipt_detail_id);
            if ($this->db->delete(db_prefix() . 'sa_goods_receipt_detail')) {
                $results++;
            }
        }

        // Add receipt note
        foreach ($inventory_receipts as $inventory_receipt) {
            $inventory_receipt['goods_receipt_id'] = $goods_receipt_id;
            if($inventory_receipt['date_manufacture'] != ''){
                $inventory_receipt['date_manufacture'] = to_sql_date($inventory_receipt['date_manufacture']);
            }else{
                $inventory_receipt['date_manufacture'] = null;
            }

            if($inventory_receipt['expiry_date'] != ''){
                $inventory_receipt['expiry_date'] = to_sql_date($inventory_receipt['expiry_date']);
            }else{
                $inventory_receipt['expiry_date'] = null;
            }

            $tax_money = 0;
            $tax_rate_value = 0;
            $tax_rate = null;
            $tax_id = null;
            $tax_name = null;
            if(isset($inventory_receipt['tax_select'])){
                $tax_rate_data = $this->sa_get_tax_rate($inventory_receipt['tax_select']);
                $tax_rate_value = $tax_rate_data['tax_rate'];
                $tax_rate = $tax_rate_data['tax_rate_str'];
                $tax_id = $tax_rate_data['tax_id_str'];
                $tax_name = $tax_rate_data['tax_name_str'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] * (float)$tax_rate_value / 100;
                $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
                $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'] + (float)$tax_money;
            }else{
                $goods_money = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
                $amount = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];
            }

            $sub_total = (float)$inventory_receipt['unit_price'] * (float)$inventory_receipt['quantities'];

            $inventory_receipt['tax_money'] = $tax_money;
            $inventory_receipt['tax'] = $tax_id;
            $inventory_receipt['goods_money'] = $goods_money;
            $inventory_receipt['tax_rate'] = $tax_rate;
            $inventory_receipt['sub_total'] = $sub_total;
            $inventory_receipt['tax_name'] = $tax_name;
            unset($inventory_receipt['order']);
            unset($inventory_receipt['id']);
            unset($inventory_receipt['tax_select']);

            $this->db->insert(db_prefix() . 'sa_goods_receipt_detail', $inventory_receipt);
            if($this->db->insert_id()){
                $results++;
            }
        }
    

        //approval if not approval setting
        if (isset($goods_receipt_id)) {
            $this->update_approve_request_wh($goods_receipt_id, 1, 1, $agent_id);
        }

        return $results > 0 ? $goods_receipt_id : false;

    }



    /**
     * update approve request
     * @param  integer $rel_ids
     * @param  string $rel_type
     * @param  integer $status
     * @return boolean
     */
    public function update_approve_request_wh($rel_id, $rel_type, $status, $agent_id) {
        $data_update = [];

        switch ($rel_type) {
        //case 1: stock_import
            case '1':

            if((int)$status == 1){
            // //update history stock, inventoty manage after staff approved
                $goods_receipt_detail = $this->get_goods_receipt_detail($rel_id);

                /*check goods receipt from PO*/
                $flag_update_status_po = true;

                $from_po = false;
                $goods_receipt = $this->get_goods_receipt($rel_id);

                if($goods_receipt){
                    if(isset($goods_receipt->pr_order_id) && ($goods_receipt->pr_order_id != 0) ){
                        $from_po = true;
                    }
                }

                foreach ($goods_receipt_detail as $goods_receipt_detail_value) {

                    /*update Without checking warehouse*/       

                    if($this->check_item_without_checking_warehouse($goods_receipt_detail_value['commodity_code']) == true){

                        $this->add_goods_transaction_detail($goods_receipt_detail_value, 1, $agent_id);
                        $this->add_inventory_manage($goods_receipt_detail_value, 1, $agent_id);

                    //update po detail
                        if($from_po){
                            $update_status = $this->update_po_detail_quantity($goods_receipt->pr_order_id, $goods_receipt_detail_value);
                        //check total item from purchase order with receipt note

                            $this->db->where('pur_order', $goods_receipt->pr_order_id);
                            $pur_order_detail = $this->db->get(db_prefix().'sa_pur_order_detail')->result_array();
                            foreach ($pur_order_detail as $p_key => $value) {
                                if((float)$value['quantity'] != (float)$value['wh_quantity_received']){
                                    $flag_update_status_po = false;
                                }
                            }

                            if($update_status['flag_update_status'] == false){
                                $flag_update_status_po = false;
                            }

                        }

                    }

                }

                /*update status po*/
                if($from_po == true /*&& $flag_update_status_po == true*/){
                    if ($this->db->field_exists('delivery_status' ,db_prefix() . 'sa_pur_orders')) { 
                        $this->db->where('id', $goods_receipt->pr_order_id);
                        $this->db->update(db_prefix() . 'sa_pur_orders', ['status_goods' => 1, 'delivery_status' => 1]);
                    }
                }
            }



            return true;
            break;
            case '2':
            $data_update['approval'] = $status;
            $this->db->where('id', $rel_id);
            $this->db->update(db_prefix() . 'sa_goods_delivery', $data_update);

            if((int)$status == 1){
            //update status invoice or pur order for this inventory delivery
                $goods_delivery = $this->get_goods_delivery($rel_id);
                $goods_delivery_detail = $this->get_goods_delivery_detail($rel_id);

                if($goods_delivery){

                    if(is_numeric($goods_delivery->invoice_id) && $goods_delivery->invoice_id != 0){
                        $type = 'invoice';
                        $rel_type = $goods_delivery->invoice_id;
                    }elseif(is_numeric($goods_delivery->pr_order_id) && $goods_delivery->pr_order_id != 0){
                        $type = 'purchase_orders';
                        $rel_type = $goods_delivery->pr_order_id;

                    }
                
                    if(isset($type)){
                        if($type == 'invoice'){
                        //check delivery partial or total
                            $flag_update = true;
                            $type_of_delivery = 'total';

                            $this->db->where('id', $rel_id);
                            $this->db->update(db_prefix() . 'sa_goods_delivery', ['type_of_delivery' => $type_of_delivery, 'delivery_status' => 'ready_for_packing']);

                            if($flag_update == true){
                                $this->db->insert(db_prefix().'sa_goods_delivery_invoices_pr_orders', [
                                    'rel_id'    => $rel_id,
                                    'rel_type'  => $rel_type,
                                    'type'      => $type,
                                ]);
                            }
                        }else{

                            $this->db->insert(db_prefix().'sa_goods_delivery_invoices_pr_orders', [
                                'rel_id'    => $rel_id,
                                'rel_type'  => $rel_type,
                                'type'      => $type,
                            ]);
                        }

                    }


                }

            //update history stock, inventoty manage after staff approved

                foreach ($goods_delivery_detail as $goods_delivery_detail_value) {
                // add goods transaction detail (log) after update invetory number

                //update Without checking warehouse             
                    if($this->check_item_without_checking_warehouse($goods_delivery_detail_value['commodity_code']) == true){

                        $this->add_inventory_manage($goods_delivery_detail_value, 2, $agent_id);
                    }

                }

            }

            return true;
            break;

            default:
            return false;
            break;
        }
    }


    /**
     * check item without checking warehouse
     * @param  integer $id 
     * @return boolean     
     */
    public function check_item_without_checking_warehouse($id)
    {   
        $status =  true;
        $this->db->where('id', $id);
        $item_value = $this->db->get(db_prefix().'items')->row();
        if($item_value){
            $checking_warehouse = $item_value->without_checking_warehouse;
            if($checking_warehouse == 1){
                $status = false;
            }
        }

        return $status;


    }


    /**
     * add goods transaction detail
     * @param array $data
     * @param string $status
     */
    public function add_goods_transaction_detail($data, $status, $agent_id) {
        if ($status == '1') {
            $data_insert['goods_receipt_id'] = $data['goods_receipt_id'];
            $data_insert['purchase_price'] = $data['unit_price'];
            $data_insert['expiry_date'] = $data['expiry_date'];
            $data_insert['lot_number'] = $data['lot_number'];
            $data_insert['serial_number'] = $data['serial_number'];
            
        } elseif ($status == '2') {
            $data_insert['goods_receipt_id'] = $data['goods_delivery_id'];
            $data_insert['price'] = $data['unit_price'];
            $data_insert['expiry_date'] = $data['expiry_date'];
            $data_insert['lot_number'] = $data['lot_number'];
            $data_insert['serial_number'] = $data['serial_number'];

        }

        /*get old quantity by item, warehouse*/
        if(is_numeric($data['warehouse_id'])){
            $inventory_value = $this->get_quantity_inventory($data['warehouse_id'], $data['commodity_code']);
            $old_quantity =  null;
            if($inventory_value){
                $old_quantity = $inventory_value->inventory_number;
            }
        }else{
            $old_quantity =  (float)$data['available_quantity'];
        }

        $data_insert['goods_id'] = $data['id'];
        $data_insert['old_quantity'] = $old_quantity;

        $data_insert['commodity_id'] = $data['commodity_code'];
        $data_insert['quantity'] = $data['quantities'];
        $data_insert['date_add'] = date('Y-m-d H:i:s');
        $data_insert['warehouse_id'] = $data['warehouse_id'];
        $data_insert['note'] = $data['note'];
        $data_insert['status'] = $status;

        $data_insert['agent_id'] = $agent_id;
        // status '1:Goods receipt note 2:Goods delivery note',
        $this->db->insert(db_prefix() . 'sa_goods_transaction_detail', $data_insert);

        return true;
    }


    /**
     * get quantity inventory
     * @param  integer $warehouse_id
     * @param  integer $commodity_id
     * @return object
     */
    public function get_quantity_inventory($warehouse_id, $commodity_id) {

        $sql = 'SELECT warehouse_id, commodity_id, sum(inventory_number) as inventory_number from ' . db_prefix() . 'sa_inventory_manage where warehouse_id = ' . $warehouse_id . ' AND commodity_id = ' . $commodity_id .' group by warehouse_id, commodity_id';
        $result = $this->db->query($sql)->row();
        //if > 0 update, else insert
        return $result;

    }


    /**
     * add inventory manage
     * @param array $data
     * @param string $status
     */
    public function add_inventory_manage($data, $status, $agent_id) {
        // status '1:Goods receipt note 2:Goods delivery note',
        $affected_rows=0;

        if ($status == 1) {

            if(isset($data['lot_number']) && $data['lot_number'] != '0' && $data['lot_number'] != ''){
                /*have value*/
                $this->db->where('lot_number', $data['lot_number']);

            }else{

                /*lot number is 0 or ''*/
                $this->db->group_start();

                $this->db->where('lot_number', '0');
                $this->db->or_where('lot_number', '');
                $this->db->or_where('lot_number', null);

                $this->db->group_end();
            }

            if($data['expiry_date'] == ''){
                
                $this->db->where('expiry_date', null);
            }else{
                $this->db->where('expiry_date', $data['expiry_date']);
            }

            $this->db->where('warehouse_id', $data['warehouse_id']);
            $this->db->where('commodity_id', $data['commodity_code']);

            $total_rows = $this->db->count_all_results(db_prefix().'sa_inventory_manage');

            if ($total_rows > 0) {
                $status_insert_update = false;
            } else {
                $status_insert_update = true;
            }

            if (!$status_insert_update) {
                //update
                $this->db->where('warehouse_id', $data['warehouse_id']);
                $this->db->where('commodity_id', $data['commodity_code']);

                if(isset($data['lot_number']) && $data['lot_number'] != '0' && $data['lot_number'] != ''){
                    /*have value*/
                    $this->db->where('lot_number', $data['lot_number']);

                }else{

                    /*lot number is 0 or ''*/
                    $this->db->group_start();

                    $this->db->where('lot_number', '0');
                    $this->db->or_where('lot_number', '');
                    $this->db->or_where('lot_number', null);

                    $this->db->group_end();
                }

                if($data['expiry_date'] == ''){

                    $this->db->where('expiry_date', null);
                }else{
                    $this->db->where('expiry_date', $data['expiry_date']);
                }


                $result = $this->db->get(db_prefix().'sa_inventory_manage')->row();
                $inventory_number = $result->inventory_number;
                $update_id = $result->id;

                if ($status == 1) {
                    //Goods receipt
                    $data_update['inventory_number'] = (float) $inventory_number + (float) $data['quantities'];
                } elseif ($status == 2) {
                    // 2:Goods delivery note
                    $data_update['inventory_number'] = (float) $inventory_number - (float) $data['quantities'];
                }

                //update
                $this->db->where('id', $update_id);
                $this->db->update(db_prefix() . 'sa_inventory_manage', $data_update);

                if ($this->db->affected_rows() > 0) {
                    $affected_rows++;
                }

          

            } else {
                //insert
                $data_insert['warehouse_id'] = $data['warehouse_id'];
                $data_insert['commodity_id'] = $data['commodity_code'];
                $data_insert['inventory_number'] = $data['quantities'];
                $data_insert['date_manufacture'] = $data['date_manufacture'];
                $data_insert['expiry_date'] = $data['expiry_date'];
                $data_insert['lot_number'] = $data['lot_number'];
                $data_insert['agent_id'] = $agent_id;

                $this->db->insert(db_prefix() . 'sa_inventory_manage', $data_insert);
                $insert_id = $this->db->insert_id();

                if ($insert_id) {
                    $affected_rows++;
                }
               

            }

            if($affected_rows > 0){
                return true;
            }
            return false;

        } else {
            //status == 2 export
            //update
            $this->db->where('warehouse_id', $data['warehouse_id']);
            $this->db->where('commodity_id', $data['commodity_code']);
            $this->db->order_by('id', 'ASC');
            $result = $this->db->get(db_prefix().'sa_inventory_manage')->result_array();

            $temp_quantities = $data['quantities'];

            $expiry_date = '';
            $lot_number = '';
            $str_serial_number = '';
            foreach ($result as $result_value) {
                if (($result_value['inventory_number'] != 0) && ($temp_quantities != 0)) {

                    if ($temp_quantities >= $result_value['inventory_number']) {
                        $temp_quantities = (float) $temp_quantities - (float) $result_value['inventory_number'];

                        //log lot number
                        if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
                            if(strlen($lot_number) != 0){
                                $lot_number .=','.$result_value['lot_number'].','.$result_value['inventory_number'];
                            }else{
                                $lot_number .= $result_value['lot_number'].','.$result_value['inventory_number'];
                            }
                        }
                        
                        //log expiry date
                        if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
                            if(strlen($expiry_date) != 0){
                                $expiry_date .=','.$result_value['expiry_date'].','.$result_value['inventory_number'];
                            }else{
                                $expiry_date .= $result_value['expiry_date'].','.$result_value['inventory_number'];
                            }
                        }

                        //update inventory
                        $this->db->where('id', $result_value['id']);
                        $this->db->update(db_prefix() . 'sa_inventory_manage', [
                            'inventory_number' => 0,
                        ]);

                        if ($this->db->affected_rows() > 0) {
                            $affected_rows++;
                        }

                                               
                    } else {

                        //log lot number
                        if(($result_value['lot_number'] != null) && ($result_value['lot_number'] != '') ){
                            if(strlen($lot_number) != 0){
                                $lot_number .=','.$result_value['lot_number'].','.$temp_quantities;
                            }else{
                                $lot_number .= $result_value['lot_number'].','.$temp_quantities;
                            }
                        }

                        //log expiry date
                        if(($result_value['expiry_date'] != null) && ($result_value['expiry_date'] != '') ){
                            if(strlen($expiry_date) != 0){
                                $expiry_date .=','.$result_value['expiry_date'].','.$temp_quantities;
                            }else{
                                $expiry_date .= $result_value['expiry_date'].','.$temp_quantities;
                            }
                        }


                        //update inventory
                        $this->db->where('id', $result_value['id']);
                        $this->db->update(db_prefix() . 'sa_inventory_manage', [
                            'inventory_number' => (float) $result_value['inventory_number'] - (float) $temp_quantities,
                        ]);

                        if ($this->db->affected_rows() > 0) {
                            $affected_rows++;
                        }

                       

                        $temp_quantities = 0;

                    }

                }

            }

            //update good delivery detail
            $this->db->where('id', $data['id']);
            $this->db->update(db_prefix() . 'sa_goods_delivery_detail', [
                'expiry_date' => $expiry_date,
                'lot_number' => $lot_number,
                'serial_number' => $str_serial_number,
            ]);
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }

            //goods transaction detail log
            $data['expiry_date'] = $expiry_date;
            $data['lot_number'] = $lot_number;
            $data['serial_number'] = $str_serial_number;
            $this->add_goods_transaction_detail($data, 2, $agent_id);

            if($affected_rows > 0){
                return true;
            }
            return false;

        }


    }

    /**
     * update po detail quantity
     * @param  integer $po_id                
     * @param  array $goods_receipt_detail 
     *                        
     */
    public function update_po_detail_quantity($po_id, $goods_receipt_detail)
    {
        $flag_update_status = true;

        $this->db->where('pur_order', $po_id);
        $this->db->where('item_code', $goods_receipt_detail['commodity_code']);

        $pur_order_detail = $this->db->get(db_prefix().'sa_pur_order_detail')->row();

        if($pur_order_detail){
            //check quantity in purchase order detail = wh_quantity_received
            $wh_quantity_received = (float)($pur_order_detail->wh_quantity_received) + (float)$goods_receipt_detail['quantities'];

            if($pur_order_detail->quantity > $wh_quantity_received){
                $flag_update_status = false;
            }

            //wh_quantity_received in purchase order detail 

            $this->db->where('pur_order', $po_id);
            $this->db->where('item_code', $goods_receipt_detail['commodity_code']);
            $this->db->update(db_prefix() . 'sa_pur_order_detail', ['wh_quantity_received' => $wh_quantity_received]);

            if ($this->db->affected_rows() > 0) {
                $results_update = true;
            } else {
                $results_update = false;
                $flag_update_status =  false;

            }

        }

        $results=[];
        $results['flag_update_status']=$flag_update_status;
        return $results;

    }

    /**
     * check format date Y-m-d
     *
     * @param      String   $date   The date
     *
     * @return     boolean 
     */
    public function check_format_date($date){
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * delete goods receipt
     * @param  [integer] $id
     * @return [redirect]
     */
    public function delete_goods_receipt($id) {

        $affected_rows = 0;

        $this->db->where('goods_receipt_id', $id);
        $this->db->delete(db_prefix() . 'sa_goods_receipt_detail');
        if ($this->db->affected_rows() > 0) {

            $affected_rows++;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'sa_goods_receipt');
        if ($this->db->affected_rows() > 0) {

            $affected_rows++;
        }

        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * delivery status mark as
     * @param  [type] $status 
     * @param  [type] $id     
     * @param  [type] $type   
     * @return [type]         
     */
    public function delivery_status_mark_as($status, $id, $type)
    {

        $status_f = false;
        if($type == 'delivery'){
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'sa_goods_delivery', ['delivery_status' => $status]);
            if ($this->db->affected_rows() > 0) {
                $status_f = true;
               
            }
        }
     return $status_f;
    }


    /**
     * get invoices
     * @param  boolean $id 
     * @return array      
     */
    public function  get_invoices($id = false, $agent_id = '')
    {
        if($agent_id == ''){
            $agent_id = get_sale_agent_user_id();
        }


        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'sa_sale_invoices')->row();
        }
        if ($id == false) {
            $arr_invoice = $this->get_invoices_goods_delivery('invoice');

            if(count($arr_invoice) > 0){

                return $this->db->query('select *, iv.id as id from '.db_prefix().'sa_sale_invoices as iv left join '.db_prefix().'sa_clients as cl on cl.id = iv.clientid  where iv.id NOT IN ('.implode(", ", $arr_invoice).') AND iv.agent_id = '.$agent_id.' order by iv.id desc')->result_array();
            }
            return $this->db->query('select *, iv.id as id from '.db_prefix().'sa_sale_invoices as iv left join '.db_prefix().'sa_clients as cl on cl.id = iv.clientid where iv.agent_id = '.$agent_id.' order by iv.id desc')->result_array();
        }

    }


    /**
     * get invoices goods delivery
     * @return mixed 
     */
    public function get_invoices_goods_delivery($type)
    {
        $this->db->where('type', $type);
        $goods_delivery_invoices_pr_orders = $this->db->get(db_prefix().'sa_goods_delivery_invoices_pr_orders')->result_array();

        $array_id = [];
        foreach ($goods_delivery_invoices_pr_orders as $value) {
            array_push($array_id, $value['rel_type']);
        }

        return $array_id;

    }

    /**
     * create goods delivery code
     * @return string
     */
    public function create_goods_delivery_code($agent_id) {

        $goods_code = get_sa_option('inventory_delivery_number_prefix', $agent_id) . (get_sa_option('next_inventory_delivery_mumber', $agent_id));
        
        return $goods_code;
    }

    /**
     * get goods delivery
     * @param  integer $id
     * @return array or object
     */
    public function get_goods_delivery($id) {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'sa_goods_delivery')->row();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'sa_goods_delivery order by id desc')->result_array();
        }
    }

    /**
     * get goods delivery detail
     * @param  integer $id
     * @return array
     */
    public function get_goods_delivery_detail($id) {
        if (is_numeric($id)) {
            $this->db->where('goods_delivery_id', $id);

            return $this->db->get(db_prefix() . 'sa_goods_delivery_detail')->result_array();
        }
        if ($id == false) {
            return $this->db->query('select * from '.db_prefix().'sa_goods_delivery_detail')->result_array();
        }
    }


    /**
     * create goods delivery row template
     * @param  array   $warehouse_data       
     * @param  string  $name                 
     * @param  string  $commodity_name       
     * @param  string  $warehouse_id         
     * @param  string  $available_quantity   
     * @param  string  $quantities           
     * @param  string  $unit_name            
     * @param  string  $unit_price           
     * @param  string  $taxname              
     * @param  string  $commodity_code       
     * @param  string  $unit_id              
     * @param  string  $tax_rate             
     * @param  string  $total_money          
     * @param  string  $discount             
     * @param  string  $discount_money       
     * @param  string  $total_after_discount 
     * @param  string  $guarantee_period     
     * @param  string  $expiry_date          
     * @param  string  $lot_number           
     * @param  string  $note                 
     * @param  string  $sub_total            
     * @param  string  $tax_name             
     * @param  string  $tax_id               
     * @param  string  $item_key             
     * @param  boolean $is_edit              
     * @return [type]                        
     */
    public function create_goods_delivery_row_template($warehouse_data = [], $name = '', $commodity_name = '', $warehouse_id = '', $available_quantity = '', $quantities = '', $unit_name = '', $unit_price = '', $taxname = '',  $commodity_code = '', $unit_id = '', $tax_rate = '', $total_money = '', $discount = '', $discount_money = '', $total_after_discount = '', $guarantee_period = '', $expiry_date = '', $lot_number = '', $note = '',  $sub_total = '', $tax_name = '', $tax_id = '', $item_key = '',$is_edit = false, $is_purchase_order = false, $serial_number = '', $without_checking_warehouse = 0) {
        
        $this->load->model('invoice_items_model');
        $row = '';

        $name_commodity_code = 'commodity_code';
        $name_commodity_name = 'commodity_name';
        $name_warehouse_id = 'warehouse_id';
        $name_unit_id = 'unit_id';
        $name_unit_name = 'unit_name';
        $name_available_quantity = 'available_quantity';
        $name_quantities = 'quantities';
        $name_unit_price = 'unit_price';
        $name_tax_id_select = 'tax_select';
        $name_tax_id = 'tax_id';
        $name_total_money = 'total_money';
        $name_lot_number = 'lot_number';
        $name_expiry_date = 'expiry_date';
        $name_note = 'note';
        $name_tax_rate = 'tax_rate';
        $name_tax_name = 'tax_name';
        $array_attr = [];
        $array_attr_payment = ['data-payment' => 'invoice'];
        $name_sub_total = 'sub_total';
        $name_discount = 'discount';
        $name_discount_money = 'discount_money';
        $name_total_after_discount = 'total_after_discount';
        $name_guarantee_period = 'guarantee_period';
        $name_serial_number = 'serial_number';
        $name_without_checking_warehouse = 'without_checking_warehouse';

        $array_available_quantity_attr = [ 'min' => '0.0', 'step' => 'any', 'readonly' => true];
        $array_qty_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_rate_attr = [ 'min' => '0.0', 'step' => 'any'];
        $array_discount_attr = [ 'min' => '0.0', 'step' => 'any'];
        $str_rate_attr = 'min="0.0" step="any"';

        if(count($warehouse_data) == 0){
            $warehouse_data = $this->get_warehouse_by_agent(get_sale_agent_user_id());
        }

        if ($name == '') {
            $row .= '<tr class="main">
                  <td></td>';
            $vehicles = [];
            $array_attr = ['placeholder' => _l('unit_price')];
            $warehouse_id_name_attr = [];
            $manual             = true;
            $invoice_item_taxes = '';
            $amount = '';
            $sub_total = 0;

        } else {
            $row .= '<tr class="sortable item">
                    <td class="dragger"><input type="hidden" class="order" name="' . $name . '[order]"><input type="hidden" class="ids" name="' . $name . '[id]" value="' . $item_key . '"></td>';
            $name_commodity_code = $name . '[commodity_code]';
            $name_commodity_name = $name . '[commodity_name]';
            $name_warehouse_id = $name . '[warehouse_id]';
            $name_unit_id = $name . '[unit_id]';
            $name_unit_name = '[unit_name]';
            $name_available_quantity = $name . '[available_quantity]';
            $name_quantities = $name . '[quantities]';
            $name_unit_price = $name . '[unit_price]';
            $name_tax_id_select = $name . '[tax_select][]';
            $name_tax_id = $name . '[tax_id]';
            $name_total_money = $name . '[total_money]';
            $name_lot_number = $name . '[lot_number]';
            $name_expiry_date = $name . '[expiry_date]';
            $name_note = $name . '[note]';
            $name_tax_rate = $name . '[tax_rate]';
            $name_tax_name = $name .'[tax_name]';
            $name_sub_total = $name .'[sub_total]';
            $name_discount = $name .'[discount]';
            $name_discount_money = $name .'[discount_money]';
            $name_total_after_discount = $name .'[total_after_discount]';
            $name_guarantee_period = $name .'[guarantee_period]';
            $name_serial_number = $name .'[serial_number]';
            $name_without_checking_warehouse = $name .'[without_checking_warehouse]';

            $warehouse_id_name_attr = ["onchange" => "get_available_quantity('" . $name_commodity_code . "','" . $name_warehouse_id . "','" . $name_available_quantity . "');", "data-none-selected-text" => _l('warehouse_name'), 'data-from_stock_id' => 'invoice'];
            $array_available_quantity_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-available_quantity' => (float)$available_quantity, 'readonly' => true];
            if($is_purchase_order){
                $array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
            }elseif(strlen($serial_number) > 0){
                $array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities, 'readonly' => true];
            }else{
                $array_qty_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any',  'data-quantity' => (float)$quantities];
            }

            $array_rate_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('rate')];
            $array_discount_attr = ['onblur' => 'wh_calculate_total();', 'onchange' => 'wh_calculate_total();', 'min' => '0.0' , 'step' => 'any', 'data-amount' => 'invoice', 'placeholder' => _l('discount')];


            $manual             = false;

            $tax_money = 0;
            $tax_rate_value = 0;

            if($is_edit){
                $invoice_item_taxes = sa_convert_item_taxes($tax_id, $tax_rate, $tax_name);
                $arr_tax_rate = explode('|', $tax_rate);
                foreach ($arr_tax_rate as $key => $value) {
                    $tax_rate_value += (float)$value;
                }
            }else{
                $invoice_item_taxes = $taxname;
                $tax_rate_data = $this->sa_get_tax_rate($taxname);
                $tax_rate_value = $tax_rate_data['tax_rate'];
            }

            if((float)$tax_rate_value != 0){
                $tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
                $goods_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
                $amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
            }else{
                $goods_money = (float)$unit_price * (float)$quantities;
                $amount = (float)$unit_price * (float)$quantities;
            }

            $sub_total = (float)$unit_price * (float)$quantities;
            $amount = app_format_number($amount);

        }
        $clients_attr = ["onchange" => "get_vehicle('" . $name_commodity_code . "','" . $name_unit_id . "','" . $name_warehouse_id . "');", "data-none-selected-text" => _l(''), 'data-customer_id' => 'invoice'];

        $row .= '<td class="">' . render_textarea($name_commodity_name, '', $commodity_name, ['rows' => 2, 'placeholder' => _l('item_description_placeholder'), 'readonly' => true] ) . '</td>';


        $row .= '<td class="warehouse_select">' .
        sa_render_select($name_warehouse_id, $warehouse_data,array('warehouse_id','warehouse_name'),'',$warehouse_id, $warehouse_id_name_attr, ["data-none-selected-text" => _l('warehouse_name')], 'no-margin').
        render_input($name_note, '', $note, 'text', ['placeholder' => _l('commodity_notes')], [], 'no-margin', 'input-transparent text-left').
        '</td>';
        $row .= '<td class="available_quantity">' . 
        render_input($name_available_quantity, '', $available_quantity, 'number', $array_available_quantity_attr, [], 'no-margin') . 
        render_input($name_unit_name, '', $unit_name, 'text', ['placeholder' => _l('unit'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
        '</td>';
        $row .= '<td class="quantities">' . render_input($name_quantities, '', $quantities, 'number', $array_qty_attr, [], 'no-margin') .
        render_input($name_guarantee_period, '', $guarantee_period, 'text', ['placeholder' => _l('guarantee_period'), 'readonly' => true], [], 'no-margin', 'input-transparent text-right wh_input_none').
         '</td>';

        $row .= '<td class="rate">' . render_input($name_unit_price, '', $unit_price, 'number', $array_rate_attr) . '</td>';
        $row .= '<td class="taxrate">' . $this->get_taxes_dropdown_template($name_tax_id_select, $invoice_item_taxes, 'invoice', $item_key, true, $manual) . '</td>';
 
        $row .= '<td class="amount" align="right">' . $amount . '</td>';
        $row .= '<td class="discount">' . render_input($name_discount, '', $discount, 'number', $array_discount_attr) . '</td>';
        $row .= '<td class="label_discount_money" align="right">' . $amount . '</td>';
        $row .= '<td class="label_total_after_discount" align="right">' . $amount . '</td>';

        $row .= '<td class="hide commodity_code">' . render_input($name_commodity_code, '', $commodity_code, 'text', ['placeholder' => _l('commodity_code')]) . '</td>';
        $row .= '<td class="hide unit_id">' . render_input($name_unit_id, '', $unit_id, 'text', ['placeholder' => _l('unit_id')]) . '</td>';
        $row .= '<td class="hide discount_money">' . render_input($name_discount_money, '', $discount_money, 'number', []) . '</td>';
        $row .= '<td class="hide total_after_discount">' . render_input($name_total_after_discount, '', $total_after_discount, 'number', []) . '</td>';
        $row .= '<td class="hide serial_number">' . render_input($name_serial_number, '', $serial_number, 'text', []) . '</td>';
        $row .= '<td class="hide without_checking_warehouse">' . render_input($name_without_checking_warehouse, '', $without_checking_warehouse, 'text', []) . '</td>';

        if ($name == '') {
            $row .= '<td></td>';
            $row .= '<td><button type="button" onclick="wh_add_item_to_table(\'undefined\',\'undefined\'); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button></td>';
        } else {
            if(is_numeric($item_key) && strlen($serial_number) > 0 && get_option('sa_wh_products_by_serial') == 1){
                $row .= '<td><a href="#" class="btn btn-success pull-right" data-toggle="tooltip" data-original-title="'._l('wh_change_serial_number').'" onclick="wh_change_serial_number(\''. $name_commodity_code .'\',\''.$name_warehouse_id .'\',\''. $name_serial_number .'\',\''. $name_commodity_name .'\'); return false;"><i class="fa fa-refresh"></i></a></td>';
            }else{
                $row .= '<td></td>';
            }
            if($is_purchase_order){
                $row .= '<td></td>';
            }else{
                $row .= '<td><a href="#" class="btn btn-danger pull-right" onclick="wh_delete_item(this,' . $item_key . ',\'.invoice-item\'); return false;"><i class="fa fa-trash"></i></a></td>';
            }
        }
        $row .= '</tr>';
        return $row;
    }

    /**
     * get commodity id from barcode
     * @param  [type] $barcode 
     * @return [type]          
     */
    public function get_commodity_id_from_barcode($barcode)
    {
        $this->db->where('commodity_barcode', $barcode);
        $item_value = $this->db->get(db_prefix().'items')->row();
        if($item_value){
            return $item_value->id;
        }else{
            return 0;
        }
    }

    /**
     * Gets the list temporaty serial numbers.
     *
     * @param        $commodity_code  The commodity code
     * @param        $warehouse_id    The warehouse identifier
     * @param        $quantities      The quantities
     *
     * @return     array   The list temporaty serial numbers.
     */
    public function get_list_temporaty_serial_numbers($commodity_code = '', $warehouse_id = '', $quantities = ''){
        return [];
    }   

    /**
     * { copy_invoice }
     *
     * @param        $invoice_id  The invoice identifier
     */
    public function copy_invoice($invoice_id){
        $arr_pur_resquest = [];

        $_status = false;
        $subtotal = 0;
        $total_discount = 0;
        $total_payment = 0;
        $total_tax_money = 0;
        $additional_discount = 0;
        $pur_total_money = 0;
        $goods_delivery_row_template = '';
        $goods_delivery_row_template = $this->create_goods_delivery_row_template();

        $agent_id = get_sale_agent_user_id();


        $this->db->select('item_code as commodity_code, '.db_prefix().'items.description, ' .db_prefix().'items.unit_id , unit_price as rate, quantity as quantities, '.db_prefix().'sa_sale_invoice_details.tax as tax_id, '.db_prefix().'sa_sale_invoice_details.total as total_money, '.db_prefix().'sa_sale_invoice_details.total, '.db_prefix().'sa_sale_invoice_details.discount_percent as discount, '.db_prefix().'sa_sale_invoice_details.discount_money, '.db_prefix().'sa_sale_invoice_details.total_money as total_after_discount, '.db_prefix().'items.guarantee, '.db_prefix().'sa_sale_invoice_details.tax_rate');
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'sa_sale_invoice_details.item_code = ' . db_prefix() . 'items.id', 'left');
        $this->db->where(db_prefix().'sa_sale_invoice_details.sale_invoice = '. $invoice_id);
        $arr_results = $this->db->get(db_prefix() . 'sa_sale_invoice_details')->result_array();

        $this->db->where('id', $invoice_id);
        $get_pur_order = $this->db->get(db_prefix() . 'sa_sale_invoices')->row();

        $data['goods_delivery_code'] = $this->create_goods_delivery_code($agent_id);
        $data['customer_code']  = '';
        $data['invoice_id']     = '';
        $data['addedfrom']  = '';
        $data['description']    = '';
        $data['address']    = '';
        if($get_pur_order){
            $_status = true;
            $data['customer_code']  = $get_pur_order->clientid;
            $data['invoice_id']     = $invoice_id;
            $data['addedfrom']  = $get_pur_order->addedfrom;
            $data['description']    = $get_pur_order->adminnote;
            $data['address']    = $get_pur_order->shipping_street.', '.$get_pur_order->shipping_city.', '.$get_pur_order->shipping_state.', '.get_country_name($get_pur_order->shipping_country);
        }


        $index=0;
        $status = false;
        $item_index=0;

        if(count($arr_results) > 0){
            $status = false;

            foreach ($arr_results as $key => $value) {
                $tax_rate = null;
                $tax_name = null;
                $tax_id = null;
                $tax_rate_value = 0;
                $pur_total_money += (float)$value['total_after_discount'];

                /*caculatoe guarantee*/
                $guarantee_period = '';
                if($value){
                    if(($value['guarantee'] != '') && (($value['guarantee'] != null)))
                        $guarantee_period = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$value['guarantee'].' months'));
                }


                /*caculator subtotal*/
                /*total discount*/
                /*total payment*/

                $total_goods_money = (float)$value['quantities']*(float)$value['rate'];

                    //get tax value
                if($value['tax_id'] != null && $value['tax_id'] != '') {
                    $tax_id = $value['tax_id'];
                    $arr_tax = explode('|', $value['tax_id']);
                    $arr_tax_rate = explode('|', $value['tax_rate']);

                    foreach ($arr_tax as $key => $tax_id) {
                        $get_tax_name = $this->get_tax_name($tax_id);

                        if(isset($arr_tax_rate[$key])){
                            $get_tax_rate = $arr_tax_rate[$key];
                        }else{
                            $tax = $this->get_taxe_value($tax_id);
                            $get_tax_rate = (float)$tax->taxrate;
                        }

                        $tax_rate_value += (float)$get_tax_rate;

                        if(strlen($tax_rate) > 0){
                            $tax_rate .= '|'.$get_tax_rate;
                        }else{
                            $tax_rate .= $get_tax_rate;
                        }

                        if(strlen($tax_name) > 0){
                            $tax_name .= '|'.$get_tax_name;
                        }else{
                            $tax_name .= $get_tax_name;
                        }


                    }
                }

                
                $index++;
                $unit_name = sa_get_unit_name($value['unit_id']);
                $unit_id = $value['unit_id'];
                $taxname = '';
                $expiry_date = null;
                $lot_number = null;
                $note = null;
                $commodity_name = sa_get_item_variatiom($value['commodity_code']);
                $total_money = 0;
                $total_after_discount = 0;
                $quantities = (float)$value['quantities'];
                $unit_price = (float)$value['rate'];
                $commodity_code = $value['commodity_code'];
                $discount_money = $value['discount_money'];

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$unit_price * (float)$quantities * (float)$tax_rate_value / 100;
                    $total_money = (float)$unit_price * (float)$quantities + (float)$tax_money;
                    $amount = (float)$unit_price * (float)$quantities + (float)$tax_money;
                    $discount_money = (float)$amount*(float)$value['discount']/100;

                    $total_after_discount = (float)$unit_price * (float)$quantities + (float)$tax_money - (float)$discount_money;
                }else{
                    $total_money = (float)$unit_price * (float)$quantities;
                    $amount = (float)$unit_price * (float)$quantities;
                    $discount_money = (float)$amount*(float)$value['discount']/100;

                    $total_after_discount = (float)$unit_price * (float)$quantities - (float)$discount_money;
                }

                $sub_total = (float)$unit_price * (float)$quantities;

                if((float)$quantities > 0){
                    $temporaty_quantity = $quantities;
                    $inventory_warehouse_by_commodity = $this->get_inventory_warehouse_by_commodity($commodity_code, $agent_id);

                    foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
                        if($temporaty_quantity > 0){
                            $available_quantity = (float)$inventory_warehouse['inventory_number'];
                            $warehouse_id = $inventory_warehouse['warehouse_id'];

                            $temporaty_available_quantity = $available_quantity;
                            $list_temporaty_serial_numbers = $this->get_list_temporaty_serial_numbers($commodity_code, $inventory_warehouse['warehouse_id'], $quantities);
                            foreach ($list_temporaty_serial_numbers as $value) {

                                if($temporaty_available_quantity > 0){
                                    $temporaty_commodity_name = $commodity_name.' SN: '.$value['serial_number'];
                                    $quantities = 1;
                                    $name = 'newitems['.$item_index.']';

                                    $goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $temporaty_commodity_name, $warehouse_id, $temporaty_available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true, false, $value['serial_number'] );
                                    $temporaty_quantity--;
                                    $temporaty_available_quantity--;
                                    $item_index ++;
                                    $inventory_warehouse_by_commodity[$key]['inventory_number'] = $temporaty_available_quantity;
                                }
                            }
                        }

                    }

                    if($temporaty_quantity > 0){
                        $quantities = $temporaty_quantity;
                        $available_quantity = 0;
                        $name = 'newitems['.$item_index.']';

                        foreach ($inventory_warehouse_by_commodity as $key => $inventory_warehouse) {
                            if((float)$inventory_warehouse['inventory_number'] > 0 && $temporaty_quantity > 0){

                                $available_quantity = (float)$inventory_warehouse['inventory_number'];
                                $warehouse_id = $inventory_warehouse['warehouse_id'];
                                
                                if ($temporaty_quantity >= $inventory_warehouse['inventory_number']) {
                                    $temporaty_quantity = (float) $temporaty_quantity - (float) $inventory_warehouse['inventory_number'];
                                    $quantities = (float)$inventory_warehouse['inventory_number'];
                                } else {
                                    $quantities = (float)$temporaty_quantity;
                                    $temporaty_quantity = 0;
                                }

                                $goods_delivery_row_template .= $this->create_goods_delivery_row_template([], $name, $commodity_name, '', $available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', '', '', $total_after_discount, $guarantee_period, $expiry_date, $lot_number, $note, $sub_total, $tax_name, $tax_id, 'undefined', true);
                                $item_index ++;
                            }
                        }
                    }
                }

            }

            if($get_pur_order){
                if((float)$get_pur_order->discount_percent > 0){
                    $additional_discount = (float)$get_pur_order->discount_percent * (float)$pur_total_money/100;
                }
            }
        }

        $arr_pur_resquest['goods_delivery_detail'] = $goods_delivery_row_template;
        $data['additional_discount'] = $additional_discount;
        $arr_pur_resquest['goods_delivery'] = $data;
        $arr_pur_resquest['status'] = $_status;

        return $arr_pur_resquest;
    }


    /**
     * get inventory warehouse by commodity
     * @param  boolean $commodity_id 
     * @return [type]                
     */
    public function get_inventory_warehouse_by_commodity($commodity_id = false, $agent_id = '')
    {
        if($agent_id == ''){
            $agent_id = get_sale_agent_user_id();
        }

        $arr_inventory_number = [];
        $sql = 'SELECT ' . db_prefix() . 'sa_warehouse.warehouse_name, '.db_prefix().'sa_warehouse.warehouse_id, '.db_prefix().'sa_inventory_manage.inventory_number FROM ' . db_prefix() . 'sa_inventory_manage
        LEFT JOIN ' . db_prefix() . 'sa_warehouse on ' . db_prefix() . 'sa_inventory_manage.warehouse_id = ' . db_prefix() . 'sa_warehouse.warehouse_id
        where ' . db_prefix() . 'sa_inventory_manage.commodity_id = ' . $commodity_id.' and '. db_prefix() . 'sa_inventory_manage.agent_id = '.$agent_id.' order by '.db_prefix().'sa_inventory_manage.id asc';
        $inventory_number = $this->db->query($sql)->result_array();

        foreach ($inventory_number as $value) {
            if(isset($arr_inventory_number[$value['warehouse_id']])){
                $arr_inventory_number[$value['warehouse_id']]['inventory_number'] += $value['inventory_number'];
            }else{
                $arr_inventory_number[$value['warehouse_id']] = $value;
            }
        }
        return $arr_inventory_number;
    }


    /**
     * add goods delivery
     * @param array  $data
     * @param boolean $id
     * return boolean
     */
    public function add_goods_delivery($data, $id = false) {
        $agent_id = get_sale_agent_user_id();

        $goods_deliveries = [];
        if (isset($data['newitems'])) {
            $goods_deliveries = $data['newitems'];
            unset($data['newitems']);
        }

        unset($data['item_select']);
        unset($data['commodity_name']);
        unset($data['warehouse_id']);
        unset($data['available_quantity']);
        unset($data['quantities']);
        unset($data['unit_price']);
        unset($data['note']);
        unset($data['unit_name']);
        unset($data['commodity_code']);
        unset($data['unit_id']);
        unset($data['discount']);
        unset($data['guarantee_period']);
        unset($data['tax_rate']);
        unset($data['tax_name']);
        unset($data['discount_money']);
        unset($data['total_after_discount']);
        unset($data['serial_number']);
        unset($data['without_checking_warehouse']);

        if(isset($data['onoffswitch'])){
            if($data['onoffswitch'] == 'on'){
                $switch_barcode_scanners = true;
                unset($data['onoffswitch']);
            }
        }
                    

        if(isset($data['edit_approval'])){
            unset($data['edit_approval']);
        }

        if(isset($data['save_and_send_request'])){
          
            unset($data['save_and_send_request']);
        }

        if (isset($data['hot_purchase'])) {
            $hot_purchase = $data['hot_purchase'];
            unset($data['hot_purchase']);
        }
        $data['goods_delivery_code'] = $this->create_goods_delivery_code($agent_id);

        if(!$this->check_format_date($data['date_c'])){
            $data['date_c'] = to_sql_date($data['date_c']);
        }else{
            $data['date_c'] = $data['date_c'];
        }


        if(!$this->check_format_date($data['date_add'])){
            $data['date_add'] = to_sql_date($data['date_add']);
        }else{
            $data['date_add'] = $data['date_add'];
        }

        $data['total_money']    = sa_reformat_currency_j($data['total_money']);
        $data['total_discount'] = sa_reformat_currency_j($data['total_discount']);
        $data['after_discount'] = sa_reformat_currency_j($data['after_discount']);

        $data['addedfrom'] = get_sa_contact_user_id();
        $data['staff_id'] = get_sa_contact_user_id();
        $data['delivery_status'] = null;
        $data['agent_id'] = $agent_id;

        $this->db->insert(db_prefix() . 'sa_goods_delivery', $data);
        $insert_id = $this->db->insert_id();

        /*update save note*/

        if (isset($insert_id)) {
            foreach ($goods_deliveries as $goods_delivery) {
                $goods_delivery['goods_delivery_id'] = $insert_id;
                $goods_delivery['expiry_date'] = null;
                $goods_delivery['lot_number'] = null;

                $tax_money = 0;
                $tax_rate_value = 0;
                $tax_rate = null;
                $tax_id = null;
                $tax_name = null;
                if(isset($goods_delivery['tax_select'])){
                    $tax_rate_data = $this->sa_get_tax_rate($goods_delivery['tax_select']);
                    $tax_rate_value = $tax_rate_data['tax_rate'];
                    $tax_rate = $tax_rate_data['tax_rate_str'];
                    $tax_id = $tax_rate_data['tax_id_str'];
                    $tax_name = $tax_rate_data['tax_name_str'];
                }

                if((float)$tax_rate_value != 0){
                    $tax_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] * (float)$tax_rate_value / 100;
                    $total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
                    $amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'] + (float)$tax_money;
                }else{
                    $total_money = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
                    $amount = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];
                }

                $sub_total = (float)$goods_delivery['unit_price'] * (float)$goods_delivery['quantities'];

                $goods_delivery['tax_id'] = $tax_id;
                $goods_delivery['total_money'] = $total_money;
                $goods_delivery['tax_rate'] = $tax_rate;
                $goods_delivery['sub_total'] = $sub_total;
                $goods_delivery['tax_name'] = $tax_name;

                unset($goods_delivery['order']);
                unset($goods_delivery['id']);
                unset($goods_delivery['tax_select']);
                unset($goods_delivery['unit_name']);
                if(isset($goods_delivery['without_checking_warehouse'])){
                    unset($goods_delivery['without_checking_warehouse']);
                }

                $this->db->insert(db_prefix() . 'sa_goods_delivery_detail', $goods_delivery);
            }
 

            /*update next number setting*/
            $next_number = get_sa_option('next_inventory_delivery_mumber', $agent_id) +1;
            $this->db->where('agent_id', $agent_id);
            $this->db->where('name', 'next_inventory_delivery_mumber');
            $this->db->update(db_prefix() . 'sa_options',['value' =>  $next_number,]);

        }

        //approval if not approval setting
        if (isset($insert_id)) {
            
            $this->update_approve_request_wh($insert_id, 2, 1, $agent_id);
            
        }

        return $insert_id > 0 ? $insert_id : false;

    }


    /**
     * update goods delivery approval
     * @param  array  $data 
     * @param  boolean $id   
     *  
     */
    public function update_goods_delivery_approval($data, $id = false)
    {
        $results = 0;

        if(isset($data['isedit'])){
            unset($data['isedit']);
        }

        if (isset($data['newitems'])) {
            $goods_deliveries = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['items'])) {
            $update_goods_deliveries = $data['items'];
            unset($data['items']);
        }
        if (isset($data['removed_items'])) {
            $remove_goods_deliveries = $data['removed_items'];
            unset($data['removed_items']);
        }
        $agent_id = get_sale_agent_user_id();
        $data['agent_id'] = $agent_id;

        $arr_serial_numbers = [];
        $arr_data_update = [];
        $goods_delivery_details = $this->get_goods_delivery_detail($data['id']);

        foreach ($goods_delivery_details as $value) {
            $arr_serial_numbers[$value['id']] = $value;
        }

        if(isset($update_goods_deliveries)){
            foreach ($update_goods_deliveries as $update_goods_delivery) {
                if(isset($arr_serial_numbers[$update_goods_delivery['id']])){
                    if($arr_serial_numbers[$update_goods_delivery['id']]['serial_number'] != $update_goods_delivery['serial_number']){
                        $arr_data_update[] = [
                            'id' => $update_goods_delivery['id'],
                            'commodity_name' => $update_goods_delivery['commodity_name'],
                            'warehouse_id' => $update_goods_delivery['warehouse_id'],
                            'serial_number' => $update_goods_delivery['serial_number'],
                        ];
                    }
                }
            }
        }
        

        if(count($arr_data_update) > 0){
            $affected_rows = $this->db->update_batch(db_prefix().'sa_goods_delivery_detail', $arr_data_update, 'id');
            if($affected_rows > 0){
                $results++;
            }
        }

        $goods_delivery_id = $data['id'];
        unset($data['id']);

        $this->db->where('id', $goods_delivery_id);
        $this->db->update(db_prefix() . 'sa_goods_delivery', ['description' => $data['description']]);
        if ($this->db->affected_rows() > 0) {
            $results++;
        }

        if($results > 0){
            return true;
        }
        return false;
    }

    /**
     * delete goods delivery
     * @param  [integer] $id
     * @return [redirect]
     */
    public function delete_goods_delivery($id) {

        $affected_rows = 0;

        $this->db->where('goods_delivery_id', $id);
        $this->db->delete(db_prefix() . 'sa_goods_delivery_detail');
        if ($this->db->affected_rows() > 0) {

            $affected_rows++;
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'sa_goods_delivery');
        if ($this->db->affected_rows() > 0) {

            $affected_rows++;
        }

        if ($affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * get html tax delivery
     * @param  [type] $id 
     * @return [type]     
     */
    public function get_html_tax_delivery($id){
        $html = '';
        $html_currency = '';
        $preview_html = '';
        $pdf_html = '';
        $taxes = [];
        $t_rate = [];
        $tax_val = [];
        $tax_val_rs = [];
        $tax_name = [];
        $rs = [];

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        
        $this->db->where('goods_delivery_id', $id);
        $details = $this->db->get(db_prefix().'sa_goods_delivery_detail')->result_array();

        foreach($details as $row){
            if($row['tax_id'] != ''){
                $tax_arr = explode('|', $row['tax_id']);

                $tax_rate_arr = [];
                if($row['tax_rate'] != ''){
                    $tax_rate_arr = explode('|', $row['tax_rate']);
                }

                foreach($tax_arr as $k => $tax_it){
                    if(!isset($tax_rate_arr[$k]) ){
                        $tax_rate_arr[$k] = $this->tax_rate_by_id($tax_it);
                    }

                    if(!in_array($tax_it, $taxes)){
                        $taxes[$tax_it] = $tax_it;
                        $t_rate[$tax_it] = $tax_rate_arr[$k];
                        $tax_name[$tax_it] = $this->get_tax_name($tax_it).' ('.$tax_rate_arr[$k].'%)';
                    }
                }
            }
        }

        if(count($tax_name) > 0){
            foreach($tax_name as $key => $tn){
                $tax_val[$key] = 0;
                foreach($details as $row_dt){
                    if(!(strpos($row_dt['tax_id'], $taxes[$key]) === false)){
                        $tax_val[$key] += ($row_dt['quantities']*$row_dt['unit_price']*$t_rate[$key]/100);
                    }
                }
                $pdf_html .= '<tr id="subtotal"><td ></td><td></td><td></td><td class="text_left">'.$tn.'</td><td class="text_right">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $preview_html .= '<tr id="subtotal"><td>'.$tn.'</td><td>'.app_format_money($tax_val[$key], '').'</td><tr>';
                $html .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], '').'</td></tr>';
                $html_currency .= '<tr class="tax-area_pr"><td>'.$tn.'</td><td width="65%">'.app_format_money($tax_val[$key], $base_currency->symbol).'</td></tr>';
                $tax_val_rs[] = $tax_val[$key];
            }
        }
        
        $rs['pdf_html'] = $pdf_html;
        $rs['preview_html'] = $preview_html;
        $rs['html'] = $html;
        $rs['taxes'] = $taxes;
        $rs['taxes_val'] = $tax_val_rs;
        $rs['html_currency'] = $html_currency;
        return $rs;
    }

    /**
     * Counts the number of po by delivery status.
     *
     * @param        $agent_id  The agent identifier
     */
    public function count_po_by_delivery_status($agent_id){
        $chart = [];

        //0: undelivered
        //1: completely_delivered
        //2: pending_delivered
        //3: partially_delivered
        $delivery_status = [ 0, 1 , 2, 3];

        foreach($delivery_status as $status){

            $total = total_rows(db_prefix().'sa_pur_orders', ['agent_id' => $agent_id , 'delivery_status' => $status]);

            $name = '';
            if($status == 0){
                $name = _l('undelivered');
            }else if($status == 1){
                $name = _l('completely_delivered');
            }else if($status == 2){
                $name = _l('pending_delivered');
            }else if($status == 3){
                $name = _l('partially_delivered');
            }

            $chart[] = ['name' => $name, 'y' => $total, 'z'=>100]; 
        }

        return $chart;
    }

    /**
     * Counts the number of po by delivery status.
     *
     * @param        $agent_id  The agent identifier
     */
    public function count_invoice_by_status($agent_id){
        $chart = [];

        $inv_status = [ 'unpaid', 'paid' , 'partially_paid'];

        foreach($inv_status as $status){

            $total = total_rows(db_prefix().'sa_sale_invoices', ['agent_id' => $agent_id , 'status' => $status]);

            $name = _l($status);

            $chart[] = ['name' => $name, 'y' => $total, 'z'=>100]; 
        }

        return $chart;
    }

    /**
     * Gets the total po value.
     */
    public function get_total_po_value($agent_id){

        $base_currency = get_base_currency();

        $total = 0;

        $this->db->where('agent_id', $agent_id);
        $this->db->where('currency', $base_currency->id);
        $orders = $this->db->get(db_prefix().'sa_pur_orders')->result_array();

        foreach($orders as $order){
            $total += $order['total'];
        }

        return $total;
    }

    /**
     * Gets the this month po value.
     *
     * @param      string  $agent_id  The agent identifier
     *
     * @return     int     The this month po value.
     */
    public function get_this_month_po_value($agent_id){
        $base_currency = get_base_currency();
        $total = 0;

        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');

        $orders = $this->db->query('SELECT total FROM '.db_prefix().'sa_pur_orders where agent_id = '.$agent_id.' AND currency = '.$base_currency->id.' AND order_date >= "'.$start_date.'" AND order_date <= "'.$end_date.'"')->result_array();

        foreach($orders as $order){
            $total += $order['total'];
        }

        return $total;
    }

    /**
     * Gets the total invoice value.
     */
    public function get_total_invoice_value($agent_id){
        $base_currency = get_base_currency();

        $total = 0;

        $this->db->where('agent_id', $agent_id);
        $this->db->where('currency', $base_currency->id);
        $invoices =  $this->db->get(db_prefix().'sa_sale_invoices')->result_array();
        foreach($invoices as $inv){
            $total += $inv['total'];
        }

        return $total;
    }

    /**
     * Gets the this month invoice value.
     */
    public function get_this_month_invoice_value($agent_id){
        $base_currency = get_base_currency();
        $total = 0;

        $start_date = date('Y-m-1');
        $end_date = date('Y-m-t');

        $invoices = $this->db->query('SELECT total FROM '.db_prefix().'sa_sale_invoices where agent_id = '.$agent_id.' AND currency = '.$base_currency->id.' AND date >= "'.$start_date.'" AND date <= "'.$end_date.'"')->result_array();

        foreach($invoices as $inv){
            $total += $inv['total'];
        }

        return $total;
    }


    /**
     * get data Purchase statistics by cost
     *
     * @param      string  $year   The year
     *
     * @return     array
     */
    public function total_income_report($year = '', $currency = '', $agent_id = ''){
        if($year == ''){
            $year = date('Y');
        }

        if($agent_id == ''){
            $agent_id = get_sale_agent_user_id();
        }

        $base_currency = get_base_currency();
        $where = ' AND '.db_prefix().'sa_sale_invoices.agent_id = '.$agent_id;

        if($currency == $base_currency->id){
            $where .= ' AND '.db_prefix().'sa_sale_invoices.currency IN (0, '.$currency.')';
        }else{
            $where .=  ' AND '.db_prefix().'sa_sale_invoices.currency = '.$currency;
        }

        $query = $this->db->query('SELECT DATE_FORMAT('.db_prefix().'sa_sale_invoices.date, "%m") AS month, Sum((SELECT SUM(total_money) as total FROM '.db_prefix().'sa_sale_invoice_details where sale_invoice = '.db_prefix().'sa_sale_invoices.id)) as total 
            FROM '.db_prefix().'sa_sale_invoices where DATE_FORMAT('.db_prefix().'sa_sale_invoices.date, "%Y") = '.$year.' '. $where.'
            group by month')->result_array();
        $result = [];
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $cost = [];
        $rs = 0;
        foreach ($query as $value) {
            if($value['total'] > 0){
                $result[$value['month'] - 1] =  (double)$value['total'];
            }
        }
        return $result;
    }

    /**
     * get data Purchase statistics by cost
     *
     * @param      string  $year   The year
     *
     * @return     array
     */
    public function total_expense_report($year = '', $currency = '', $agent_id = ''){
        if($year == ''){
            $year = date('Y');
        }

        if($agent_id == ''){
            $agent_id = get_sale_agent_user_id();
        }

        $base_currency = get_base_currency();
        $where = ' AND '.db_prefix().'sa_pur_orders.agent_id = '.$agent_id;

        if($currency == $base_currency->id){
            $where .= ' AND '.db_prefix().'sa_pur_orders.currency IN (0, '.$currency.')';
        }else{
            $where .=  ' AND '.db_prefix().'sa_pur_orders.currency = '.$currency;
        }

        $query = $this->db->query('SELECT DATE_FORMAT('.db_prefix().'sa_pur_orders.order_date, "%m") AS month, Sum('.db_prefix().'sa_pur_orders.total) as total 
            FROM '.db_prefix().'sa_pur_orders where DATE_FORMAT('.db_prefix().'sa_pur_orders.order_date, "%Y") = '.$year.' '. $where.'
            group by month')->result_array();
        $result = [];
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $result[] = 0;
        $cost = [];
        $rs = 0;
        foreach ($query as $value) {
            if($value['total'] > 0){
                $result[$value['month'] - 1] =  (double)$value['total'];
            }
        }
        return $result;
    }

    /**
     * { delete_pur_order }
     */
    public function delete_pur_order($id){
        $rs = 0;

        $this->db->where('pur_order', $id);
        $this->db->delete(db_prefix().'sa_pur_order_detail');
        if($this->db->affected_rows() > 0){
            $rs++;
        }


        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'sa_pur_order');
        $this->db->delete(db_prefix().'files');
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }

        if (is_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id)) {
            delete_dir(SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/'. $id);
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'sa_pur_orders');
        if ($this->db->affected_rows() > 0) {
            $rs++;
        }


        if($rs > 0){
            return true;
        }
        return false;
    }


    /**
     * { change order status when product_ delivered }
     */
    public function change_order_status_when_product_delivered($log_id){
        $this->db->where('id', $log_id);
        $wh_log = $this->db->get(db_prefix().'wh_goods_delivery_activity_log')->row();

        if($wh_log){
            if($wh_log->rel_type == 'shipment'){
                $this->db->where('id', $wh_log->rel_id);
                $shipment = $this->db->get(db_prefix().'wh_omni_shipments')->row();
                if($shipment){
                    if($shipment->shipment_status == 'product_delivered'){
                        $this->db->where('id', $shipment->order_id);
                        $cart = $this->db->get(db_prefix().'sa_pur_orders')->row();
                        if($cart->order_status != 'delivered' || $cart->delivery_status != 1){
                            $this->db->where('id', $shipment->order_id);
                            $this->db->update(db_prefix().'sa_pur_orders', [ 'order_status' => 'delivered', 'delivery_status' => 1, 'delivery_date' => date('Y-m-d')] );
                        }
                    }
                }
            }else if($wh_log->rel_type == 'delivery'){
                $this->db->where('id',  $wh_log->rel_id);
                $delivery_voucher = $this->db->get(db_prefix().'goods_delivery')->row();

                if($delivery_voucher){
                    if($delivery_voucher->delivery_status == 'delivered'){
                        $this->db->where('stock_export_id', $wh_log->rel_id);
                        $cart = $this->db->get(db_prefix().'sa_pur_orders')->row();
                        if($cart->order_status != 'delivered' || $cart->delivery_status != 1){
                            $this->db->where('id', $cart->id);
                            $this->db->update(db_prefix().'sa_pur_orders', [ 'order_status' => 'delivered', 'delivery_status' => 1, 'delivery_date' => date('Y-m-d')] );
                        }
                    }
                }
            }   
        }
    }

    /**
    * Get contract types values for chart
    * @return array
    */
    public function get_contracts_types_chart_data()
    {
        $labels = [];
        $totals = [];
        $this->load->model('contract_types_model');
        $types  = $this->contract_types_model->get();
        foreach ($types as $type) {
            $total_rows_where = [
                'contract_type' => $type['id'],
                'trash'         => 0,
            ];
            if (is_sale_agent_logged_in()) {
                $total_rows_where['client']                = get_sale_agent_user_id();
                $total_rows_where['not_visible_to_client'] = 0;
            } else {
                if (!has_permission('contracts', '', 'view')) {
                    $total_rows_where['addedfrom'] = get_staff_user_id();
                }
            }
            $total_rows = total_rows(db_prefix().'contracts', $total_rows_where);
            if ($total_rows == 0 && is_sale_agent_logged_in()) {
                continue;
            }
            array_push($labels, $type['name']);
            array_push($totals, $total_rows);
        }
        $chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => _l('contract_summary_by_type'),
                    'backgroundColor' => 'rgba(3,169,244,0.2)',
                    'borderColor'     => '#03a9f4',
                    'borderWidth'     => 1,
                    'data'            => $totals,
                ],
            ],
        ];

        return $chart;
    }

}