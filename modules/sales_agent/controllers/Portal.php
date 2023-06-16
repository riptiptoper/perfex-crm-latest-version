<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * This class describes a portal.
 */
class Portal extends App_Controller
{

    public $template = [];

    public $data = [];

    public $use_footer = true;

    public $use_submenu = true;

    public $use_navigation = true;

    public function __construct()
    {
        parent::__construct();

        hooks()->do_action('after_clients_area_init', $this);

        $this->load->library('app_sale_agent_area_constructor');

        $this->load->model('Sales_agent_model');
    }


    /**
     * layout
     * @param  boolean $notInThemeViewFiles
     * @return view                      
     */
    public function layout($notInThemeViewFiles = false)
    {
        /**
         * Navigation and submenu
         * @var boolean
         */

        $this->data['use_navigation'] = $this->use_navigation == true;
        $this->data['use_submenu']    = $this->use_submenu == true;

        /**
         * @since  2.3.2 new variables
         * @var array
         */
        $this->data['navigationEnabled'] = $this->use_navigation == true;
        $this->data['subMenuEnabled']    = $this->use_submenu == true;

        /**
         * Theme head file
         * @var string
         */
        $this->template['head'] = $this->load->view('portal/head', $this->data, true);

        $GLOBALS['customers_head'] = $this->template['head'];

        /**
         * Load the template view
         * @var string
         */
        $module                       = CI::$APP->router->fetch_module();
        $this->data['current_module'] = $module;

        $viewPath = !is_null($module) || $notInThemeViewFiles ? $this->view : 'portal/' . $this->view;

        $this->template['view']    = $this->load->view($viewPath, $this->data, true);
        $GLOBALS['customers_view'] = $this->template['view'];

        /**
         * Theme footer
         * @var string
         */
        $this->template['footer'] = $this->use_footer == true
        ? $this->load->view('portal/footer', $this->data, true)
        : '';
        $GLOBALS['customers_footer'] = $this->template['footer'];

        /**
         * Theme scripts.php file is no longer used since vresion 2.3.0, add app_customers_footer() in themes/[theme]/index.php
         * @var string
         */
        $this->template['scripts'] = '';
        if (file_exists(VIEWPATH . 'portal/scripts.php')) {
            if (ENVIRONMENT != 'production') {
                trigger_error(sprintf('%1$s', 'Clients area theme file scripts.php file is no longer used since version 2.3.0, add app_customers_footer() in themes/[theme]/index.php. You can check the original theme index.php for example.'));
            }

            $this->template['scripts'] = $this->load->view('portal/scripts', $this->data, true);
        }

        /**
         * Load the theme compiled template
         */
        $this->load->view('portal/index', $this->template);
    }

     /**
     * Sets view data
     * @param  array $data
     * @return core/ClientsController
     */
    public function data($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Set view to load
     * @param  string $view view file
     * @return core/ClientsController
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Sets view title
     * @param  string $title
     * @return core/ClientsController
     */
    public function title($title)
    {
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableNavigation()
    {
        $this->use_navigation = false;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableSubMenu()
    {
        $this->use_submenu = false;

        return $this;
    }

    /**
     * Disables theme footer
     * @return core/ClientsController
     */
    public function disableFooter()
    {
        $this->use_footer = false;

        return $this;
    }
    

    /**
     * { index }
     */
    public function index()
    {
        if (is_sale_agent_logged_in()) {
            $agent_id = get_sale_agent_user_id();
            $data['title']   = _l('als_dashboard');
            $data['is_home'] = true;

            $data['program_participated'] = count(get_programs_of_agent($agent_id));
            $data['total_items'] = count($this->Sales_agent_model->get_item_by_agent($agent_id));
            $data['total_purchase_orders'] = total_rows(db_prefix().'sa_pur_orders', ['agent_id' => $agent_id]);
            $data['total_sale_invoices'] = total_rows(db_prefix().'sa_sale_invoices', ['agent_id' => $agent_id]);

            $data['po_by_delivery_status'] = json_encode($this->Sales_agent_model->count_po_by_delivery_status($agent_id));
            $data['invoice_by_status'] = json_encode($this->Sales_agent_model->count_invoice_by_status($agent_id));

            $data['total_po_value'] = $this->Sales_agent_model->get_total_po_value($agent_id);
            $data['this_month_po_value'] = $this->Sales_agent_model->get_this_month_po_value($agent_id);
            $data['total_invoice_value'] = $this->Sales_agent_model->get_total_invoice_value($agent_id);
            $data['this_month_invoice_value'] = $this->Sales_agent_model->get_this_month_invoice_value($agent_id);
   
            $this->data($data);
            $this->view('portal/home');
            $this->layout();
        } else {
            redirect(site_url('sales_agent/authentication_sales_agent'));
        }
    }

    /**
     * { profile }
     */
     public function profile()
    {
        if ($this->input->post('profile')) {
            $this->form_validation->set_rules('firstname', _l('client_firstname'), 'required');
            $this->form_validation->set_rules('lastname', _l('client_lastname'), 'required');

            $this->form_validation->set_message('contact_email_profile_unique', _l('form_validation_is_unique'));
            $this->form_validation->set_rules('email', _l('clients_email'), 'required');

            $custom_fields = get_custom_fields('contacts', [
                'show_on_client_portal'  => 1,
                'required'               => 1,
                'disalow_client_to_edit' => 0,
            ]);
            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }
            if ($this->form_validation->run() !== false) {
                handle_contact_profile_image_upload(get_sa_contact_user_id());

                $data = $this->input->post();

                $contact = $this->clients_model->get_contact(get_sa_contact_user_id());

                if (has_contact_permission('invoices')) {
                    $data['invoice_emails']     = isset($data['invoice_emails']) ? 1 : 0;
                    $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;
                } else {
                    $data['invoice_emails']     = $contact->invoice_emails;
                    $data['credit_note_emails'] = $contact->credit_note_emails;
                }

                if (has_contact_permission('estimates')) {
                    $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;
                } else {
                    $data['estimate_emails'] = $contact->estimate_emails;
                }

                if (has_contact_permission('support')) {
                    $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;
                } else {
                    $data['ticket_emails'] = $contact->ticket_emails;
                }

                if (has_contact_permission('contracts')) {
                    $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;
                } else {
                    $data['contract_emails'] = $contact->contract_emails;
                }

                if (has_contact_permission('projects')) {
                    $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;
                    $data['task_emails']    = isset($data['task_emails']) ? 1 : 0;
                } else {
                    $data['project_emails'] = $contact->project_emails;
                    $data['task_emails']    = $contact->task_emails;
                }

                $success = $this->clients_model->update_contact([
                    'firstname'          => $this->input->post('firstname'),
                    'lastname'           => $this->input->post('lastname'),
                    'title'              => $this->input->post('title'),
                    'email'              => $this->input->post('email'),
                    'phonenumber'        => $this->input->post('phonenumber'),
                    'direction'          => $this->input->post('direction'),
                    'invoice_emails'     => $data['invoice_emails'],
                    'credit_note_emails' => $data['credit_note_emails'],
                    'estimate_emails'    => $data['estimate_emails'],
                    'ticket_emails'      => $data['ticket_emails'],
                    'contract_emails'    => $data['contract_emails'],
                    'project_emails'     => $data['project_emails'],
                    'task_emails'        => $data['task_emails'],
                    'custom_fields'      => isset($data['custom_fields']) && is_array($data['custom_fields']) ? $data['custom_fields'] : [],
                ], get_sa_contact_user_id(), true);

                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('sales_agent/portal/profile'));
            }
        } elseif ($this->input->post('change_password')) {
            $this->form_validation->set_rules('oldpassword', _l('clients_edit_profile_old_password'), 'required');
            $this->form_validation->set_rules('newpassword', _l('clients_edit_profile_new_password'), 'required');
            $this->form_validation->set_rules('newpasswordr', _l('clients_edit_profile_new_password_repeat'), 'required|matches[newpassword]');
            if ($this->form_validation->run() !== false) {
                $success = $this->clients_model->change_contact_password(
                    get_sa_contact_user_id(),
                    $this->input->post('oldpassword', false),
                    $this->input->post('newpasswordr', false)
                );

                if (is_array($success) && isset($success['old_password_not_match'])) {
                    set_alert('danger', _l('client_old_password_incorrect'));
                } elseif ($success == true) {
                    set_alert('success', _l('client_password_changed'));
                }

                redirect(site_url('sales_agent/portal/profile'));
            }
        }
        $data['title'] = _l('clients_profile_heading');
        $this->data($data);
        $this->view('portal/profile');
        $this->layout();
    }

    /**
     * Removes a profile image.
     */
    public function remove_profile_image()
    {
        $id = get_contact_user_id();

        hooks()->do_action('before_remove_contact_profile_image', $id);

        if (file_exists(get_upload_path_by_type('contact_profile_images') . $id)) {
            delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contacts', [
            'profile_image' => null,
        ]);

        if ($this->db->affected_rows() > 0) {
            redirect(site_url('sales_agent/profile'));
        }
    }

    /**
     * { programs }
     */
    public function programs(){

        $data['title'] = _l('sa_programs');
        $this->data($data);
        $this->view('portal/programs/agent_programs');
        $this->layout();
    }

    /**
     * { program table }
     */
    public function program_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/programs/table_programs'));
    }

    /**
     * { program detail }
     */
    public function program_detail($id){
        $data['program'] = $this->Sales_agent_model->get_program($id);
        $data['program_detail'] = $this->Sales_agent_model->get_program_detail($id);
        $data['title'] = $data['program']->name;

        $this->data($data);
        $this->view('portal/programs/program_detail');
        $this->layout();
    }

    /**
     * { table program items }
     */
    public function table_program_items($program_id){
        $this->app->get_table_data(module_views_path('sales_agent', 'programs/table_program_items'), ['program_id' => $program_id]);
    }

    /**
     * { join program }
     */
    public function join_program($program_id){
        $agent_id = get_sale_agent_user_id();

        $request_id = $this->Sales_agent_model->join_program_request($program_id, $agent_id);
        if($request_id){
            set_alert('success', _l('request_sent_successfully'));
        }

        redirect(site_url('sales_agent/portal/programs'));
    }

    /**
     * { company }
     */
    public function company()
    {   

        if ($this->input->post() ) {
            if (get_option('company_is_required') == 1) {
                $this->form_validation->set_rules('company', _l('clients_company'), 'required');
            }

            if (active_clients_theme() == 'perfex') {
                // Fix for custom fields checkboxes validation
                $this->form_validation->set_rules('company_form', '', 'required');
            }

            $custom_fields = get_custom_fields('customers', [
                'show_on_client_portal'  => 1,
                'required'               => 1,
                'disalow_client_to_edit' => 0,
            ]);

            foreach ($custom_fields as $field) {
                $field_name = 'custom_fields[' . $field['fieldto'] . '][' . $field['id'] . ']';
                if ($field['type'] == 'checkbox' || $field['type'] == 'multiselect') {
                    $field_name .= '[]';
                }
                $this->form_validation->set_rules($field_name, $field['name'], 'required');
            }


            if ($this->input->post()) {
                $data['company'] = $this->input->post('company');

                if (!is_null($this->input->post('vat'))) {
                    $data['vat'] = $this->input->post('vat');
                }

                if (!is_null($this->input->post('default_language'))) {
                    $data['default_language'] = $this->input->post('default_language');
                }

                if (!is_null($this->input->post('custom_fields'))) {
                    $data['custom_fields'] = $this->input->post('custom_fields');
                }

                $data['phonenumber'] = $this->input->post('phonenumber');
                $data['website']     = $this->input->post('website');
                $data['country']     = $this->input->post('country');
                $data['city']        = $this->input->post('city');
                $data['address']     = $this->input->post('address');
                $data['zip']         = $this->input->post('zip');
                $data['state']       = $this->input->post('state');

                if (get_option('allow_primary_contact_to_view_edit_billing_and_shipping') == 1
                    && is_primary_contact()) {

                    // Dynamically get the billing and shipping values from $_POST
                    for ($i = 0; $i < 2; $i++) {
                        $prefix = ($i == 0 ? 'billing_' : 'shipping_');
                        foreach (['street', 'city', 'state', 'zip', 'country'] as $field) {
                            $data[$prefix . $field] = $this->input->post($prefix . $field);
                        }
                    }
                }
                $success = $this->clients_model->update_company_details($data, get_sale_agent_user_id());
                if ($success == true) {
                    set_alert('success', _l('clients_profile_updated'));
                }

                redirect(site_url('sales_agent/portal/company'));
            }
        }
        $data['title'] = _l('client_company_info');
        $this->data($data);
        $this->view('portal/company_profile');
        $this->layout();
    }

    /**
     * { clients }
     */
    public function clients(){
        $data['title'] = _l('sa_clients');
        $agent_id = get_sale_agent_user_id();

        $data['groups'] = $this->Sales_agent_model->get_agent_client_group($agent_id);
        $this->data($data);
        $this->view('portal/clients/manage');
        $this->layout();
    }

    /**
     * { clients }
     */
    public function client($id = ''){
        $agent_id = get_sale_agent_user_id();
        if($this->input->post()){
            $add_data = $this->input->post();
            if($id == ''){
                $insert_id = $this->Sales_agent_model->add_client($add_data);
                if($insert_id){
                    set_alert('success', _l('added_successfully'));
                }
            }else{
                $success = $this->Sales_agent_model->update_client($id, $add_data);
                if($success){
                    set_alert('success', _l('updated_successfully'));
                }
            }

            redirect(site_url('sales_agent/portal/clients'));
        }

        if($id == ''){
            $data['title'] = _l('add_client');
        }else{
            $data['_client'] = $this->Sales_agent_model->get_sa_client($id);
            $data['title'] = _l('edit_client');
        }

        $data['groups'] = $this->Sales_agent_model->get_agent_client_group($agent_id);

        $this->data($data);
        $this->view('portal/clients/client');
        $this->layout();
    }

    /**
     * { clients table }
     */
    public function clients_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/clients/table_clients'));
    }

    /**
     * { delete client }
     */
    public function delete_client($id){
        if(!$id){
            redirect(site_url('sales_agent/portal/clients'));
        }

        $success = $this->Sales_agent_model->delete_client($id);
        if($success){
            set_alert('success', _l('deleted_successfully'));
        }

        redirect(site_url('sales_agent/portal/clients'));
    }

    /**
     * { settings }
     */
    public function settings(){
        $data['title'] = _l('sa_settings');
        $agent_id = get_sale_agent_user_id();

        $data['group'] = $this->input->get('group');
        $data['unit_tab'] = $this->input->get('tab');
       
       $data['tab'][] = 'general_setting';
        $data['tab'][] = 'approval';
        $data['tab'][] = 'client_group';



        if($data['group'] == ''){ 
            $data['group'] = 'general_setting';
        }

        $data['tabs']['view'] = 'portal/settings/includes/'.$data['group'];
        $data['staffs'] = $this->sales_agent_model->get_contacts($agent_id); 

        $data['approval_setting'] = $this->Sales_agent_model->get_agent_approval_setting($agent_id);
        $data['client_groups'] = $this->Sales_agent_model->get_agent_client_group($agent_id);

        $this->data($data);
        $this->view('portal/settings/manage');
        $this->layout();
    }

    /**
     * Gets the html approval setting.
     *
     * @param      string  $id     The identifier
     */
    public function get_html_approval_setting($id = '')
    {
        $html = '';
        $agent_id = get_sale_agent_user_id();
        $staffs = $this->Sales_agent_model->get_contacts($agent_id);
        
        $approver = [
                0 => ['id' => 'direct_manager', 'name' => _l('direct_manager')],
                1 => ['id' => 'head_of_department', 'name' => _l('department_manager')],
                2 => ['id' => 'staff', 'name' => _l('staff')]];
        $action = [ 
                    1 => ['id' => 'approve', 'name' => _l('approve')],
                    
                ];

        $hr_record_status = 0; 
        
        if(is_numeric($id)){
            $approval_setting = $this->Sales_agent_model->get_agent_approval_setting($agent_id, $id);

            $setting = json_decode($approval_setting->setting);

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if($hr_record_status == 1){
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }
            
            foreach ($setting as $key => $value) {

                if($value->approver == 'staff'){
                    $staff_hide = '';
                }
                if($key == 0){

                    $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-'.$approver_md.' '.$hide_class.'"> '.
                                    sa_render_select('approver['.$key.']',$approver,array('id','name'),'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').'
                                    </div>
                                    <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_0">
                                    '. sa_render_select('staff['.$key.']',$staffs,array('id','full_name'),'staff', $value->staff).'
                                    </div>
                                    <div class="col-md-4">
                                        '. sa_render_select('action['.$key.']',$action,array('id','name'),'action', $value->action).' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                }else{
                     $html .= '<div id="item_approve">
                                    <div class="col-md-11">
                                    <div class="col-md-'.$approver_md.' '.$hide_class.'"">
                                        '.
                                    sa_render_select('approver['.$key.']',$approver,array('id','name'),'approver', $value->approver, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').' 
                                    </div>
                                    <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_'.$key.'">
                                        '. sa_render_select('staff['.$key.']',$staffs,array('id','full_name'),'staff', $value->staff).' 
                                    </div>
                                    <div class="col-md-4">
                                        '. sa_render_select('action['.$key.']',$action,array('id','name'),'action', $value->action).' 
                                    </div>
                                    </div>
                                    <div class="col-md-1 btn_apr">
                                    <span class="pull-bot">
                                        <button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                                        </span>
                                  </div>
                                </div>';
                }
            }
        }else{

            $approver_md = '1';
            $hide_class = 'hide';
            $staff_md = '8';
            $approver_default = 'staff';
            $staff_hide = '';
            if($hr_record_status == 1){
                $approver_md = '4';
                $staff_md = '4';
                $hide_class = '';
                $approver_default = '';
                $staff_hide = 'hide';
            }
            $html .= '<div id="item_approve">
                        <div class="col-md-11">
                        <div class="col-md-'.$approver_md.' '.$hide_class.' "> '.
                        sa_render_select('approver[0]',$approver,array('id','name'),'approver', $approver_default, array('data-id' => '0', 'required' => 'true'), [],'', 'approver_class').'
                        </div>
                        <div class="col-md-'.$staff_md.' '.$staff_hide.'" id="is_staff_0">
                        '. sa_render_select('staff[0]',$staffs,array('id','full_name'),'staff').'
                        </div>
                        <div class="col-md-4">
                            '. sa_render_select('action[0]',$action,array('id','name'),'action','approve').' 
                        </div>
                        </div>
                        <div class="col-md-1 btn_apr">
                        <span class="pull-bot">
                            <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                            </span>
                      </div>
                    </div>';
        }

        echo json_encode([
                    $html
                ]);
    }

    /**
     * { approval setting }
     * @return redirect
     */
    public function approval_setting()
    {
        $agent_id = get_sale_agent_user_id();
        if ($this->input->post()) {
            $data = $this->input->post();

            $data['agent_id'] = $agent_id;
            $data['type'] = 'agent';
            if ($data['approval_setting_id'] == '') {
                $message = '';
                $success = $this->Sales_agent_model->add_approval_setting($data);
                if ($success) {
                    $message = _l('added_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(site_url('sales_agent/portal/settings?group=approval'));
            } else {
                $message = '';
                $id = $data['approval_setting_id'];
                $success = $this->Sales_agent_model->edit_approval_setting($id, $data);
                if ($success) {
                    $message = _l('updated_successfully', _l('approval_setting'));
                }
                set_alert('success', $message);
                redirect(site_url('sales_agent/portal/settings?group=approval'));
            }
        }
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_approval_setting($id)
    {
        if (!$id) {
            redirect(site_url('sales_agent/portal/setting?group=approval'));
        }
        $response = $this->Sales_agent_model->delete_approval_setting($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('approval_setting')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('approval_setting')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('approval_setting')));
        }
        redirect(site_url('sales_agent/portal/settings?group=approval'));
    }

    /**
     * { delete approval setting }
     *
     * @param      <type>  $id     The identifier
     * @return redirect
     */
    public function delete_client_group($id)
    {
        if (!$id) {
            redirect(site_url('sales_agent/portal/setting?group=client_group'));
        }
        $response = $this->Sales_agent_model->delete_sa_client_group($id);
        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(site_url('sales_agent/portal/settings?group=client_group'));
    }

    /**
     * { approval setting }
     * @return redirect
     */
    public function client_group_form()
    {
        $agent_id = get_sale_agent_user_id();
        if ($this->input->post()) {
            $data = $this->input->post();

            $data['agent_id'] = $agent_id;

            if ($data['client_group_id'] == '') {
                $message = '';
                $success = $this->Sales_agent_model->add_client_group($data);
                if ($success) {
                    $message = _l('added_successfully', _l('client_group'));
                }
                set_alert('success', $message);
                redirect(site_url('sales_agent/portal/settings?group=client_group'));
            } else {
                $message = '';
                $id = $data['client_group_id'];
                $success = $this->Sales_agent_model->edit_client_group($id, $data);
                if ($success) {
                    $message = _l('updated_successfully', _l('client_group'));
                }
                set_alert('success', $message);
                redirect(site_url('sales_agent/portal/settings?group=client_group'));
            }
        }
    }

    /**
     * { sa_setting }
     */
    public function sa_setting(){
        $agent_id = get_sale_agent_user_id();
        if ($this->input->post()) {
            $data = $this->input->post();

            $success = $this->Sales_agent_model->update_sa_setting($agent_id, $data);
            if($success) {
                set_alert('success', _l('updated_successfully'));

            }

            redirect(site_url('sales_agent/portal/settings?group=general_setting'));
        }
    }

    /**
     * { purchase_orders }
     */
    public function purchase_orders(){
        $data['title'] = _l('purchase_orders');

        $this->data($data);
        $this->view('portal/purchase_orders/manage');
        $this->layout();
    }

    /**
     * { function_description }
     */
    public function products_list(){
        $agent_id = get_sale_agent_user_id();
        $data['title'] = _l('sa_products');
        $data['commodity_groups'] = $this->Sales_agent_model->get_item_groups();
        $data['programs'] = get_programs_of_agent($agent_id);
        
        $this->data($data);
        $this->view('portal/products/manage');
        $this->layout();
    }

    /**
     * { products_table }
     */
    public function products_table(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/products/table_products'));
    }

    /**
     * { table_purchase_orders }
     */
    public function table_purchase_orders(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/purchase_orders/table_purchase_orders'));
    }

    /**
     * { pur_order }
     */
    public function pur_order($id = ''){
        $agent_id = get_sale_agent_user_id();

        if ($this->input->post()) {
            $pur_order_data = $this->input->post();
            $pur_order_data['terms'] = nl2br($pur_order_data['terms']);
            if ($id == '') {
            

                $id = $this->Sales_agent_model->add_pur_order($pur_order_data, $agent_id);
                if ($id) {
                    set_alert('success', _l('added_successfully'));
                    
                    redirect(site_url('sales_agent/portal/purchase_orders'));
                }
            } else {
               
                $success = $this->Sales_agent_model->update_pur_order($pur_order_data, $id, $agent_id);
                if ($success) {
                    set_alert('success', _l('updated_successfully'));
                }
                redirect(site_url('sales_agent/portal/purchase_orders'));
                
            }
        }

        $data['base_currency'] = get_base_currency();

        $pur_order_row_template = $this->Sales_agent_model->create_purchase_order_row_template();

        if($id == ''){
            $data['title'] = _l('add_purchase_order');
        }else{
            $data['title'] = _l('edit_purchase_order');
            $data['pur_order_detail'] = $this->Sales_agent_model->get_pur_order_detail($id);
            $data['pur_order'] = $this->Sales_agent_model->get_pur_order($id);

            $currency_rate = 1;
            if($data['pur_order']->currency != 0 && $data['pur_order']->currency_rate != null){
                $currency_rate = $data['pur_order']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['pur_order']->currency != 0 && $data['pur_order']->to_currency != null) {
                $to_currency = $data['pur_order']->to_currency;
            }


            $data['tax_data'] = $this->Sales_agent_model->get_html_tax_pur_order($id);
            $title = _l('pur_order_detail');

            if (count($data['pur_order_detail']) > 0) { 
                $index_order = 0;
                foreach ($data['pur_order_detail'] as $order_detail) { 
                    $index_order++;
                    $unit_name = sa_get_unit_name($order_detail['unit_id']);
                    $taxname = $order_detail['tax_name'];
                    $item_name = $order_detail['item_name'];

                    if(strlen($item_name) == 0){
                        $item_name = sa_get_item_variatiom($order_detail['item_code']);
                    }

                    $pur_order_row_template .= $this->Sales_agent_model->create_purchase_order_row_template('items[' . $index_order . ']',  $item_name, $order_detail['description'], $order_detail['quantity'], $unit_name, $order_detail['unit_price'], $taxname, $order_detail['item_code'], $order_detail['unit_id'], $order_detail['tax_rate'],  $order_detail['total_money'], $order_detail['discount_%'], $order_detail['discount_money'], $order_detail['total'], $order_detail['into_money'], $order_detail['tax'], $order_detail['tax_value'], $order_detail['id'], true, $currency_rate, $to_currency, $order_detail['program_id'], $index_order);
                }
            }
        }
        

        $data['disount_programs'] = get_programs_of_agent($agent_id);

        $data['pur_order_row_template'] = $pur_order_row_template;

        $data['ajaxItems'] = false;
       
        $data['items'] = [];
       

        
        $data['currencies'] = $this->currencies_model->get();
        $data['staffs'] = $this->Sales_agent_model->get_contacts($agent_id);
        $this->data($data);
        $this->view('portal/purchase_orders/pur_order');
        $this->layout();
    }

    /**
     * Gets the item by program.
     *
     * @param        $program_id  The program identifier
     */
    public function get_item_by_program($program_id){
        $html = '';

        $items = $this->Sales_agent_model->get_items_of_program($program_id);
        $html .= '<option value=""></option>';
        foreach($items as $item){
            $html .= '<option value="'.$item['id'].'" data-subtext="'. strip_tags(mb_substr($item['long_description'],0,200)).'">'.$item['commodity_code'].' '.$item['description'].'</option>';
        }

        echo json_encode([
            'html' => $html,
        ]);
    }


    /**
     * Gets the item by identifier.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id($id, $program_id, $currency_rate = 1)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->Sales_agent_model->get_item_v2($id);
            $item->long_description   = nl2br($item->long_description);

            if($currency_rate != 1){
                $item->purchase_price = round(($item->purchase_price*$currency_rate), 2);
            }

            $program = $this->Sales_agent_model->get_program($program_id);

            $item->program_id = $program_id;
            $item->discount = '';
            $item->discount_money = '';
            if(isset($program->discount_type) && $program->discount_type == 'percentage'){

                $item->discount = get_discount_by_item_quantity(1, $program_id, $id);
            }else{
               
                $item->discount_money = get_discount_by_item_quantity(1, $program_id, $id);
            }

            
            $html = '<option value=""></option>';

            echo json_encode($item);
        }
    }

    /**
     * Gets the currency.
     *
     * @param        $id     The identifier
     */
    public function get_currency($id)
    {
        echo json_encode(get_currency($id));
    }

    /**
     * Gets the purchase order row template.
     */
    public function get_purchase_order_row_template(){
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');
        $program_id = $this->input->post('program_id');
        $index_key = $this->input->post('index_key');

        echo html_entity_decode($this->Sales_agent_model->create_purchase_order_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency, $program_id, $index_key));
    }

    /**
     * { order_detail }
     */
    public function order_detail($id){

        $agent_id = get_sale_agent_user_id();

        $data['estimate'] = $this->Sales_agent_model->get_pur_order($id);
        if(!$data['estimate']){
            show_404();
        }

        if($data['estimate']->agent_id != $agent_id){
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

        $data['check_appr'] = $this->Sales_agent_model->get_approve_setting('pur_order', $agent_id);
        $data['get_staff_sign'] = $this->Sales_agent_model->get_staff_sign($id,'pur_order', $agent_id);
        $data['check_approve_status'] = $this->Sales_agent_model->check_approval_details($id,'pur_order', $agent_id);
        $data['list_approve_status'] = $this->Sales_agent_model->get_list_approval_details($id,'pur_order');

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

        $this->data($data);
        $this->view('portal/purchase_orders/order_detail', $data);
        $this->layout();
    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function purchase_order_attachment($id){

        sa_handle_purchase_order_file($id);

        redirect(site_url('sales_agent/portal/order_detail/'.$id));
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
        $this->load->view('portal/purchase_orders/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_purorder_attachment($id)
    {

        echo html_entity_decode($this->Sales_agent_model->delete_purorder_attachment($id));
       
    }

    /**
     * { purchase order pdf }
     *
     * @param      <type>  $id     The identifier
     * @return pdf output
     */
    public function purorder_pdf($id)
    {
        if (!$id) {
            redirect(site_url('sales_agent/portal/purchase_request'));
        }

        $pur_request = $this->Sales_agent_model->get_purorder_pdf_html($id);

        try {
            $pdf = $this->Sales_agent_model->purorder_pdf($pur_request);
        } catch (Exception $e) {
            echo html_entity_decode($e->getMessage());
            die;
        }
        
        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('purchase_order.pdf', $type);
    }

     /**
     * Sends a request approve.
     * @return  json
     */
    public function send_request_approve(){
        $data = $this->input->post();
        $message = 'Send request approval fail';
        $success = $this->Sales_agent_model->send_request_approve($data);
        if ($success === true) {                
                $message = 'Send request approval success';
                $data_new = [];
                $data_new['send_mail_approve'] = $data;
                $this->session->set_userdata($data_new);
        }elseif($success === false){
            $message = _l('no_matching_process_found');
            $success = false;
            
        }else{
            $message = _l('could_not_find_approver_with', _l($success));
            $success = false;
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]); 
        die;
    }


     /**
     * { approve request }
     * @return json
     */
    public function approve_request(){
        $agent_id = get_sale_agent_user_id();

        $data = $this->input->post();
        $data['staff_approve'] = get_sa_contact_user_id();
        $success = false; 
        $code = '';
        $signature = '';

        if(isset($data['signature'])){
            $signature = $data['signature'];
            unset($data['signature']);
        }
        $status_string = 'status_'.$data['approve'];
        $check_approve_status = $this->Sales_agent_model->check_approval_details($data['rel_id'],$data['rel_type'], $agent_id);
        
        if(isset($data['approve']) && in_array(get_sa_contact_user_id(), $check_approve_status['staffid'])){

            $success = $this->Sales_agent_model->update_approval_details($check_approve_status['id'], $data);

            $message = _l('approved_successfully');

            if ($success) {
                if($data['approve'] == 2){
                    $message = _l('approved_successfully');
                    $data_log = [];

                    if($signature != ''){
                        $data_log['note'] = "signed_request";
                    }else{
                        $data_log['note'] = "approve_request";
                    }
                    if($signature != ''){
                        switch ($data['rel_type']) {
                         
                            case 'pur_order':
                                $path = SALES_AGENT_MODULE_UPLOAD_FOLDER .'/pur_order/signature/' .$data['rel_id'];
                                break;
                           
                            default:
                                $path = SALES_AGENT_MODULE_UPLOAD_FOLDER;
                                break;
                        }
                        sa_process_digital_signature_image($signature, $path, 'signature_'.$check_approve_status['id']);
                        $message = _l('sign_successfully');
                    }
                   


                    $check_approve_status = $this->Sales_agent_model->check_approval_details($data['rel_id'],$data['rel_type'] , $agent_id);
                    if ($check_approve_status === true){
                        $this->Sales_agent_model->update_approve_request($data['rel_id'],$data['rel_type'], 2);
                    }
                }else{
                    $message = _l('rejected_successfully');
                    
                    $this->Sales_agent_model->update_approve_request($data['rel_id'],$data['rel_type'], '3');
                }
            }
        }

        $data_new = [];
        $data_new['send_mail_approve'] = $data;
        $this->session->set_userdata($data_new);
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        die();      
    }

    /**
     * { item_detail }
     *
     * @param        $item_id  The item identifier
     */
    public function item_detail($item_id){
        $data['id'] = $item_id;
        $data['item'] = $this->Sales_agent_model->get_item($item_id);
        $data['item_file'] = $this->Sales_agent_model->get_item_attachments($item_id);
        $data['title'] =  $data['item']->description;

        $this->data($data);
        $this->view('portal/products/item_detail', $data);
        $this->layout();
    }

    /**
     * Gets the discount by item quatity.
     */
    public function get_discount_by_item_quatity($quantity, $program_id, $item_id){


        $program = $this->Sales_agent_model->get_program($program_id);

        $discount = '';
        $discount_money = '';
        if(isset($program->discount_type) && $program->discount_type == 'percentage'){

            $discount = get_discount_by_item_quantity($quantity, $program_id, $item_id);
        }else{
           
            $discount_money = get_discount_by_item_quantity($quantity, $program_id, $item_id);
        }

        echo json_encode([
            'discount' => $discount,
            'discount_money' => $discount_money,
        ]);

    }

    /**
     * { invoices }
     *
     * @param      bool  $status  The status
     */
    public function purchase_invoices($status = false)
    {

        $contact_id = get_sa_contact_user_id();
        if (!has_contact_permission('invoices', $contact_id)) {
            set_alert('warning', _l('access_denied'));
            redirect(site_url('sales_agent/portal'));
        }

        $sales_agent = get_sale_agent_user_id();

        $where = [
            'clientid' => $sales_agent,
        ];

        if (is_numeric($status)) {
            $where['status'] = $status;
        }

        if (isset($where['status'])) {
            if ($where['status'] == Invoices_model::STATUS_DRAFT
                && get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                unset($where['status']);
                $where['status !='] = Invoices_model::STATUS_DRAFT;
            }
        } else {
            if (get_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                $where['status !='] = Invoices_model::STATUS_DRAFT;
            }
        }

        $data['invoices'] = $this->invoices_model->get('', $where);
        $data['title']    = _l('clients_my_invoices');
        $this->data($data);
        $this->view('portal/invoices/invoices');
        $this->layout();
    }

    /**
     * { purchase_contracts }
     */
    public function purchase_contracts(){

        $data['contracts'] = $this->contracts_model->get('', [
            'client'                => get_sale_agent_user_id(),
            'not_visible_to_client' => 0,
            'trash'                 => 0,
        ]);

        $data['contracts_by_type_chart'] = json_encode($this->Sales_agent_model->get_contracts_types_chart_data());
        $data['title']                   = _l('clients_contracts');
        $this->data($data);
        $this->view('portal/contracts/manage');
        $this->layout();
    }

    /**
     * { tickets }
     */
    public function tickets($status = ''){

        $where = db_prefix() . 'tickets.userid=' . get_sale_agent_user_id();
        if (!can_logged_in_contact_view_all_tickets()) {
            $where .= ' AND ' . db_prefix() . 'tickets.contactid=' . get_sa_contact_user_id();
        }

        $data['show_submitter_on_table'] = show_ticket_submitter_on_clients_area_table();

        $defaultStatuses = hooks()->apply_filters('customers_area_list_default_ticket_statuses', [1, 2, 3, 4]);
        // By default only open tickets
        if (!is_numeric($status)) {
            $where .= ' AND status IN (' . implode(', ', $defaultStatuses) . ')';
        } else {
            $where .= ' AND status=' . $this->db->escape_str($status);
        }

        $data['list_statuses'] = is_numeric($status) ? [$status] : $defaultStatuses;
        $data['bodyclass']     = 'tickets';
        $data['tickets']       = $this->tickets_model->get('', $where);
        $data['title']         = _l('clients_tickets_heading');
        $this->data($data);
        $this->view('portal/tickets/tickets');
        $this->layout();
    }

    /**
     * Opens a ticket.
     */
    public function open_ticket()
    {
      
        if ($this->input->post()) {
            $this->form_validation->set_rules('subject', _l('customer_ticket_subject'), 'required');
            $this->form_validation->set_rules('department', _l('clients_ticket_open_departments'), 'required');
            $this->form_validation->set_rules('priority', _l('priority'), 'required');
            $custom_fields = get_custom_fields('tickets', [
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
            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                $id = $this->tickets_model->add([
                    'subject'    => $data['subject'],
                    'department' => $data['department'],
                    'priority'   => $data['priority'],
                    'service'    => isset($data['service']) && is_numeric($data['service'])
                    ? $data['service']
                    : null,
                    'project_id' => isset($data['project_id']) && is_numeric($data['project_id'])
                    ? $data['project_id']
                    : 0,
                    'custom_fields' => isset($data['custom_fields']) && is_array($data['custom_fields'])
                    ? $data['custom_fields']
                    : [],
                    'message'   => $data['message'],
                    'contactid' => get_sa_contact_user_id(),
                    'userid'    => get_sale_agent_user_id(),
                ]);

                if ($id) {
                    set_alert('success', _l('new_ticket_added_successfully', $id));
                    redirect(site_url('sales_agent/portal/ticket/' . $id));
                }
            }
        }
        $data             = [];

        $data['title']    = _l('new_ticket');
        $this->data($data);
        $this->view('portal/tickets/open_ticket');
        $this->layout();
    }

    /**
     * { ticket }
     *
     * @param        $id     The identifier
     */
    public function ticket($id)
    {
        
        if (!$id) {
            redirect(site_url());
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id, get_sale_agent_user_id());
        if (!$data['ticket'] || $data['ticket']->userid != get_sale_agent_user_id()) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('message', _l('ticket_reply'), 'required');

            if ($this->form_validation->run() !== false) {
                $data = $this->input->post();

                $replyid = $this->tickets_model->add_reply([
                    'message'   => $data['message'],
                    'contactid' => get_sa_contact_user_id(),
                    'userid'    => get_sale_agent_user_id(),
                ], $id);
                if ($replyid) {
                    set_alert('success', _l('replied_to_ticket_successfully', $id));
                }
                redirect(site_url('sales_agent/portal/ticket/' . $id));
            }
        }

        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($id);
        $data['title']          = $data['ticket']->subject;
        $this->data($data);
        $this->view('portal/tickets/single_ticket');
        $this->layout();
    }

    /**
     * { sale_invoices }
     */
    public function sale_invoices(){
        $data['title']    = _l('sa_invoices');

        $agent_id = get_sale_agent_user_id();

        $data['clients'] = $this->Sales_agent_model->get_sales_agent_clients($agent_id);

        $this->data($data);
        $this->view('portal/sales_invoices/manage');
        $this->layout();
    }

    /**
     * { table sale invoices }
     */
    public function table_sale_invoices(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/sales_invoices/table_sale_invoices'));
    }

    /**
     * { sale invoice }
     */
    public function sale_invoice($id = ''){

        $agent_id = get_sale_agent_user_id();

        $invoice_row_template = $this->Sales_agent_model->create_sale_invoice_row_template();

        if ($this->input->post()) {
            $invoice_data = $this->input->post();

            if($id == ''){
                $insert_id = $this->Sales_agent_model->add_sale_invoice($invoice_data);
                if($insert_id){
                    set_alert('success', _l('added_successfully'));
                }

            }else{
                $success = $this->Sales_agent_model->update_sale_invoice($invoice_data, $id);
                if($success){
                    set_alert('success', _l('updated_successfully'));
                }
            }

            redirect(site_url('sales_agent/portal/sale_invoices'));
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        if($id == ''){
            $data['title'] = _l('add_invoice');
        }else{
            $data['title'] = _l('edit_invoice');
            $data['invoice'] = $this->Sales_agent_model->get_sale_invoice($id);
            $data['invoice_details'] = $this->Sales_agent_model->get_sale_invoice_detail($id);

            $currency_rate = 1;
            if($data['invoice']->currency != 0 && $data['invoice']->currency_rate != null){
                $currency_rate = $data['invoice']->currency_rate;
            }

            $to_currency = $data['base_currency']->name;
            if($data['invoice']->currency != 0 && $data['invoice']->to_currency != null) {
                $to_currency = $data['invoice']->to_currency;
            }

             $index_order = 0;
            foreach ($data['invoice_details'] as $inv_detail) { 
                $index_order++;
                $unit_name = sa_get_unit_name($inv_detail['unit_id']);
                $taxname = $inv_detail['tax_name'];
                $item_name = $inv_detail['item_name'];

                if(strlen($item_name) == 0){
                    $item_name = sa_get_item_variatiom($inv_detail['item_code']);
                }

                $invoice_row_template .= $this->Sales_agent_model->create_sale_invoice_row_template('items[' . $index_order . ']',  $item_name, $inv_detail['description'], $inv_detail['quantity'], $unit_name, $inv_detail['unit_price'], $taxname, $inv_detail['item_code'], $inv_detail['unit_id'], $inv_detail['tax_rate'],  $inv_detail['total_money'], $inv_detail['discount_percent'], $inv_detail['discount_money'], $inv_detail['total'], $inv_detail['into_money'], $inv_detail['tax'], $inv_detail['tax_value'], $inv_detail['id'], true, $currency_rate, $to_currency);
            }
        }

        $data['invoice_row_template'] = $invoice_row_template;

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $data['clients'] = $this->Sales_agent_model->get_sales_agent_clients($agent_id);

        $this->load->model('clients_model');

        $data['staff'] = $this->clients_model->get_contacts($agent_id);

        $data['items'] = $this->Sales_agent_model->get_item_by_agent($agent_id);

        $this->data($data);
        $this->view('portal/sales_invoices/invoice');
        $this->layout();
    }

    /**
     * Gets the customer billing and shipping details.
     *
     * @param        $id     The identifier
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        echo json_encode($this->Sales_agent_model->get_customer_billing_and_shipping_details($id));
    }

    /**
     * { function_description }
     *
     * @param      <type>  $customer_id      The customer identifier
     * @param      string  $current_invoice  The current invoice
     */
    public function client_change_data($customer_id, $current_invoice = '')
    {
        if ($this->input->is_ajax_request()) {

            $data                     = [];
            $data['billing_shipping'] = $this->Sales_agent_model->get_customer_billing_and_shipping_details($customer_id);
 
            echo json_encode($data);
        }
    }

     /**
     * Gets the item by identifier.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id_sale_inv($id, $currency_rate = 1)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->Sales_agent_model->get_item_v2($id);
            $item->long_description   = nl2br($item->long_description);

            if($currency_rate != 1){
                $item->rate = round(($item->rate*$currency_rate), 2);
            }
            

            echo json_encode($item);
        }
    }


    /**
     * Gets the purchase order row template.
     */
    public function get_sale_invoice_row_template(){
        $name = $this->input->post('name');
        $item_name = $this->input->post('item_name');
        $item_description = $this->input->post('item_description');
        $quantity = $this->input->post('quantity');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $item_code = $this->input->post('item_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $item_key = $this->input->post('item_key');
        $currency_rate = $this->input->post('currency_rate');
        $to_currency = $this->input->post('to_currency');

        echo html_entity_decode($this->Sales_agent_model->create_sale_invoice_row_template($name, $item_name, $item_description, $quantity, $unit_name, $unit_price, $taxname, $item_code, $unit_id, $tax_rate, '', $discount, '', '', '', '', '', $item_key, false, $currency_rate, $to_currency ));
    }

    /**
     * { delete_sale_invoice }
     *
     * @param        $id     The identifier
     */
    public function delete_sale_invoice($id){
        $agent_id = get_sale_agent_user_id();

        $invoice = $this->Sales_agent_model->get_sale_invoice($id);

        if(!$invoice){
            redirect(site_url('sales_agent/portal/sale_invoices'));
        }

        if($invoice->agent_id != $agent_id){
            redirect(site_url('sales_agent/portal/sale_invoices'));
        }

        $success = $this->Sales_agent_model->delete_sale_invoice($id);
        if($success){
            set_alert('success', _l('deleted_successfully'));
        }

        redirect(site_url('sales_agent/portal/sale_invoices'));
    }

    /**
     * { sale_invoice_detail }
     */
    public function sale_invoice_detail($id){
        $agent_id = get_sale_agent_user_id();
        $invoice  = $this->Sales_agent_model->get_sale_invoice($id);

        if(!$invoice){
            show_404();
        }

        if($invoice->agent_id != $agent_id){
            show_404();
        }

        $data['invoice'] = $invoice;
        $data['invoice_details'] = $this->Sales_agent_model->get_sale_invoice_detail($id);
        $data['title'] = $invoice->inv_number;
        $data['payment'] = $this->Sales_agent_model->get_payment_sale_invoice($id);

        $data['tax_data'] = $this->Sales_agent_model->get_html_tax_sale_invoice($id);

        $data['sale_invoice_attachments'] = $this->Sales_agent_model->get_sale_invoice_attachments($id);

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);

        $this->data($data);
        $this->view('portal/sales_invoices/detail_invoice');
        $this->layout();
    }

    /**
     * Adds a payment for invoice.
     *
     * @param      <type>  $pur_order  The purchase order id
     * @return  redirect
     */
    public function add_invoice_payment($invoice){
         if ($this->input->post()) {
            $data = $this->input->post();
            $message = '';
            $success = $this->Sales_agent_model->add_invoice_payment($data, $invoice);
            if ($success) {
                $message = _l('added_successfully', _l('payment'));
            }
            set_alert('success', $message);
            redirect(site_url('sales_agent/portal/sale_invoice_detail/'.$invoice));
            
        }
    }

    /**
     * { function_description }
     */
    public function delete_payment_sale_invoice($id, $invoice_id){
        $agent_id = get_sale_agent_user_id();
        $invoice  = $this->Sales_agent_model->get_sale_invoice($invoice_id);

        if(!$invoice){
            show_404();
        }

        if($invoice->agent_id != $agent_id){
            show_404();
        }

        $success = $this->Sales_agent_model->delete_payment_sale_invoice($id, $invoice_id);
        if($success){
            set_alert('success', _l('deleted_successfully'));
        }

        redirect(site_url('sales_agent/portal/sale_invoice_detail/'. $invoice_id));

    }

    /**
     * Uploads a purchase order attachment.
     *
     * @param      string  $id  The purchase order
     * @return redirect
     */
    public function sale_invoice_attachment($id){

        sa_handle_sale_invoice_file($id);

        redirect(site_url('sales_agent/portal/sale_invoice_detail/'.$id));
    }

    /**
     * { file_purorder }
     */
    public function file_sale_invoice($id, $rel_id){


        $data['file'] = $this->Sales_agent_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('portal/sales_invoices/_file', $data);
    }

    /**
     * { delete purchase order attachment }
     *
     * @param      <type>  $id     The identifier
     */
    public function delete_sale_invoice_attachment($id)
    {

        echo html_entity_decode($this->Sales_agent_model->delete_sale_invoice_attachment($id));
       
    }

    /**
     * { payments }
     */
    public function payments(){
        $data['title'] = _l('payments');

        $this->data($data);
        $this->view('portal/sales_invoices/manage_payments');
        $this->layout();
    }

    /**
     * { function_description }
     */
    public function table_payments(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/sales_invoices/table_payments'));
    }

    /**
     * { warehouse }
     */
    public function warehouse($id = ''){
        $data['title'] = _l('warehouse_manage');
        $data['warehouse_types'] = $this->Sales_agent_model->get_warehouse();


        $data['proposal_id'] = $id;

        $this->data($data);
        $this->view('portal/warehouse/manage');
        $this->layout();

    }

    /**
     * { table warehouse name }
     */
    public function table_warehouse_name(){
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/warehouse/table_warehouse_name'));
    }


    /**
     * warehouse setting
     * @param  string $id 
     * @return [type]     
     */
    public function add_warehouse($id = '') {

        $agent_id = get_sale_agent_user_id();
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();
            if (!$this->input->post('id')) {
                $data['agent_id'] = $agent_id;
                $mess = $this->Sales_agent_model->add_one_warehouse($data);
                if ($mess) {
                    set_alert('success', _l('added_successfully') .' '. _l('warehouse'));

                } else {
                    set_alert('warning', _l('Add_warehouse_false'));
                }
                redirect(site_url('sales_agent/portal/warehouse'));

            } else {
                $id = $data['id'];
                unset($data['id']);
                $success = $this->Sales_agent_model->update_one_warehouse($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully') .' '. _l('warehouse'));
                } else {
                    set_alert('warning', _l('updated_warehouse_false'));
                }
                redirect(site_url('sales_agent/portal/warehouse'));
            }
        }
    }

    /**
     * get item by id ajax
     * @param  integer $id 
     * @return [type]     
     */
    public function get_warehouse_by_id($id)
    {
        if ($this->input->is_ajax_request()) {

            $warehouse_value                     = $this->Sales_agent_model->get_warehouse($id);

            $warehouse_value->warehouse_code    = $warehouse_value->warehouse_code;
            $warehouse_value->warehouse_name    = $warehouse_value->warehouse_name;
            $warehouse_value->warehouse_address   = nl2br($warehouse_value->warehouse_address);
            $warehouse_value->note   = nl2br($warehouse_value->note);

            echo json_encode($warehouse_value);
        }
    }


    /**
     * delete warehouse
     * @param  integer $id
     * @return redirect
     */
    public function delete_warehouse($id) {
        if (!$id) {
            redirect(site_url('sales_agent/portal/warehouse'));
        }

        $response = $this->Sales_agent_model->delete_warehouse($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('warehouse')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('warehouse')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('warehouse')));
        }
        redirect(site_url('sales_agent/portal/warehouse'));

    }

    /**
     * view warehouse detail
     * @param  integer $warehouse_id 
     * @return view               
     */
    public function view_warehouse_detail($warehouse_id) {
        $agent_id = get_sale_agent_user_id();
        $warehouse_item = $this->Sales_agent_model->get_warehouse($warehouse_id);

        if (!$warehouse_item || is_array($warehouse_item) || $warehouse_item->agent_id != $agent_id) {
            blank_page('Warehouse Not Found', 'danger');
        }

        $data['warehouse_item'] = $warehouse_item;
        $data['warehouse_inventory'] = $this->Sales_agent_model->get_inventory_by_warehouse($warehouse_id);

        $data['title'] = $warehouse_item->warehouse_code;

        $this->data($data);
        $this->view('portal/warehouse/warehouse_view_detail');
        $this->layout();

    }

    /**
     * { receiving vouchers }
     */
    public function receiving_vouchers(){
        $data['title'] = _l('stock_received_manage');

        $this->data($data);
        $this->view('portal/goods_receipt/manage');
        $this->layout();
    }

    /**
     * table manage goods receipt
     * @param  integer $id
     * @return array
     */
    public function table_manage_goods_receipt() {
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/goods_receipt/table_manage_goods_receipt'));
    }


    /**
     * manage goods receipt
     * @param  integer $id
     * @return view
     */
    public function goods_receipt($id = '') {
        $this->load->model('clients_model');
        $this->load->model('taxes_model');

        $agent_id = get_sale_agent_user_id();

        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $mess = $this->Sales_agent_model->add_goods_receipt($data);

                if ($mess) {
                
                    set_alert('success', _l('added_successfully'));

                } else {
                    set_alert('warning', _l('Add_stock_received_docket_false'));
                }
                redirect(site_url('sales_agent/portal/receiving_vouchers'));

            }else{

                $id = $this->input->post('id');
                $mess = $this->Sales_agent_model->update_goods_receipt($data);

                if ($mess) {
                    set_alert('success', _l('updated_successfully'));

                } else {
                    set_alert('warning', _l('update_stock_received_docket_false'));
                }
                redirect(site_url('sales_agent/portal/receiving_vouchers'));
            }

        }
        //get vaule render dropdown select
        $data['commodity_code_name'] = $this->Sales_agent_model->get_commodity_code_name();
        $data['units_code_name'] = $this->Sales_agent_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->Sales_agent_model->get_warehouse_code_name($agent_id);

        $data['title'] = _l('goods_receipt');

        $data['commodity_codes'] = $this->Sales_agent_model->get_commodity();

        $data['warehouses'] = $this->Sales_agent_model->get_warehouse_by_agent($agent_id);
     
        $this->load->model('purchase/purchase_model');
        $this->load->model('departments_model');
        $this->load->model('staff_model');
        $this->load->model('projects_model');

        $data['pr_orders'] = sa_get_pr_order();
        $data['pr_orders_status'] = true;

   
        $data['goods_code'] = $this->Sales_agent_model->create_goods_code($agent_id);
     
        $data['current_day'] = (date('Y-m-d'));

        $data['taxes'] = $this->taxes_model->get();

        $data['items'] = $this->Sales_agent_model->get_item_by_agent($agent_id);
       

        $warehouse_data = $this->Sales_agent_model->get_warehouse();
        //sample
        $goods_receipt_row_template = $this->Sales_agent_model->create_goods_receipt_row_template();

        //check status module purchase
        if($id != ''){
            $goods_receipt = $this->Sales_agent_model->get_goods_receipt($id);
            if (!$goods_receipt) {
                blank_page('Stock received Not Found', 'danger');
            }
            $data['goods_receipt_detail'] = $this->Sales_agent_model->get_goods_receipt_detail($id);
            $data['goods_receipt'] = $goods_receipt;
            $data['tax_data'] = $this->Sales_agent_model->get_html_tax_receip($id);
            $data['total_item'] = count($data['goods_receipt_detail']);

            if (count($data['goods_receipt_detail']) > 0) {
                $index_receipt = 0;
                foreach ($data['goods_receipt_detail'] as $receipt_detail) {
                    $index_receipt++;
                    $unit_name = wh_get_unit_name($receipt_detail['unit_id']);
                    $taxname = '';
                    $date_manufacture = null;
                    $expiry_date = null;
                    $commodity_name = $receipt_detail['commodity_name'];
                    if($receipt_detail['date_manufacture'] != null && $receipt_detail['date_manufacture'] != ''){
                        $date_manufacture = _d($receipt_detail['date_manufacture']);
                    }
                    if($receipt_detail['expiry_date'] != null && $receipt_detail['expiry_date'] != ''){
                        $expiry_date = _d($receipt_detail['expiry_date']);
                    }
                    if(strlen($commodity_name) == 0){
                        $commodity_name = sa_get_item_variatiom($receipt_detail['commodity_code']);
                    }

                    $goods_receipt_row_template .= $this->Sales_agent_model->create_goods_receipt_row_template($warehouse_data, 'items[' . $index_receipt . ']', $commodity_name, $receipt_detail['warehouse_id'], $receipt_detail['quantities'], $unit_name, $receipt_detail['unit_price'], $taxname, $receipt_detail['lot_number'], $date_manufacture, $expiry_date, $receipt_detail['commodity_code'], $receipt_detail['unit_id'] , $receipt_detail['tax_rate'], $receipt_detail['tax_money'], $receipt_detail['goods_money'], $receipt_detail['note'], $receipt_detail['id'], $receipt_detail['sub_total'], $receipt_detail['tax_name'], $receipt_detail['tax'], true, $receipt_detail['serial_number']);
                    
                }
            }

            $data['goods_receipt_detail'] = json_encode($this->Sales_agent_model->get_goods_receipt_detail($id));

        }

        $data['goods_receipt_row_template'] = $goods_receipt_row_template;
        $get_base_currency =  get_base_currency();
        if($get_base_currency){
            $data['base_currency_id'] = $get_base_currency->id;
        }else{
            $data['base_currency_id'] = 0;
        }

        $this->data($data);
        $this->view('portal/goods_receipt/purchase');
        $this->layout();

    }

    /**
     * wh get item by barcode
     * @param  [type] $barcode 
     * @return [type]          
     */
    public function wh_get_item_by_barcode($barcode)
    {
        if ($this->input->is_ajax_request()) {
            $id = 0;
            $status = false;
            $message = '';
            $value = $this->Sales_agent_model->get_commodity_hansometable_by_barcode($barcode);
            if(isset($value)){
                $id = $value->id;
                $status = true;
                $message = $value->commodity_barcode.': '.$value->commodity_code.' - '.$value->description;
            }
            echo json_encode([
                "id" => $id,
                "status" => $status,
                "message" => $message,
            ]);
        }
    }


    /**
     * get receipt note row template
     * @return [type] 
     */
    public function get_good_receipt_row_template()
    {
        $name = $this->input->post('name');
        $commodity_name = $this->input->post('commodity_name');
        $warehouse_id = $this->input->post('warehouse_id');
        $quantities = $this->input->post('quantities');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $lot_number = $this->input->post('lot_number');
        $date_manufacture = $this->input->post('date_manufacture');
        $expiry_date = $this->input->post('expiry_date');
        $commodity_code = $this->input->post('commodity_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $tax_money = $this->input->post('tax_money');
        $goods_money = $this->input->post('goods_money');
        $note = $this->input->post('note');
        $item_key = $this->input->post('item_key');

        echo html_entity_decode($this->Sales_agent_model->create_goods_receipt_row_template([], $name, $commodity_name, $warehouse_id, $quantities, $unit_name, $unit_price, $taxname, $lot_number, $date_manufacture, $expiry_date, $commodity_code, $unit_id, $tax_rate, $tax_money, $goods_money, $note, $item_key));

    }

    /**
     * Gets the item by identifier good receipt.
     *
     * @param          $id             The identifier
     * @param      bool|int  $get_warehouse  The get warehouse
     * @param      bool      $warehouse_id   The warehouse identifier
     */
    public function get_item_by_id_good_receipt($id, $get_warehouse = false, $warehouse_id = false)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->Sales_agent_model->get_item_v2($id);
            $item->long_description   = nl2br($item->long_description);
            $guarantee_new = '';
            if(($item->guarantee != '') && (($item->guarantee != null))){
                $guarantee_new = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$item->guarantee.' months'));
            }
            $item->guarantee_new = $guarantee_new;
            $html = '<option value=""></option>';
            if((int)$get_warehouse ==  1){
                $get_available_quantity = $this->Sales_agent_model->get_adjustment_stock_quantity($warehouse_id, $id, null, null);
                if($get_available_quantity){
                    $item->available_quantity = (float)$get_available_quantity->inventory_number;
                }else{
                    $item->available_quantity = 0;
                }
            }elseif($get_warehouse){
                $arr_warehouse_id = [];
                $warehouses = $this->Sales_agent_model->get_commodity_warehouse($id);
                if (count($warehouses) > 0) {
                    foreach ($warehouses as $warehouse) {
                        if(!in_array($warehouse['warehouse_id'], $arr_warehouse_id)){
                            $arr_warehouse_id[] = $warehouse['warehouse_id'];
                            if((float)$warehouse['inventory_number'] > 0){
                                $html .= '<option value="' . $warehouse['warehouse_id'] . '">' . $warehouse['warehouse_name'] . '</option>';
                            }
                        }
                    }
                }
            }
            $item->warehouses_html = $html;

            echo json_encode($item);
        }
    }

    /**
     * copy pur request
     * @param  integer $pur request
     * @return json encode
     */
    public function coppy_pur_request($pur_request = '') {
        if(is_numeric($pur_request)){
            $pur_request_detail = $this->Sales_agent_model->get_pur_request($pur_request);

            echo json_encode([

                'result' => $pur_request_detail[0] ? $pur_request_detail[0] : '',
                'total_tax_money' => $pur_request_detail[1] ? $pur_request_detail[1] : '',
                'total_goods_money' => $pur_request_detail[2] ? $pur_request_detail[2] : '',
                'value_of_inventory' => $pur_request_detail[3] ? $pur_request_detail[3] : '',
                'total_money' => $pur_request_detail[4] ? $pur_request_detail[4] : '',
                'total_row' => $pur_request_detail[5] ? $pur_request_detail[5] : '',
                'list_item' => $pur_request_detail[6] ? $pur_request_detail[6] : '',
            ]);
        }else{
            $list_item = $this->Sales_agent_model->create_goods_receipt_row_template();
            echo json_encode([
                'list_item' => $list_item,
            ]);
        }
    }

    /**
     * delete goods receipt
     * @param  [integer] $id
     * @return redirect
     */
    public function delete_goods_receipt($id) {

    
        $response = $this->Sales_agent_model->delete_goods_receipt($id);
        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(site_url('sales_agent/portal/receiving_vouchers'));
    }


    /**
     * view purchase
     * @param  integer $id
     * @return view
     */
    public function view_purchase($id) {
        $agent_id = get_sale_agent_user_id();
     
    

        //get vaule render dropdown select
        $data['commodity_code_name'] = $this->Sales_agent_model->get_commodity_code_name();
        $data['units_code_name'] = $this->Sales_agent_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->Sales_agent_model->get_warehouse_code_name($agent_id);

        $data['goods_receipt_detail'] = $this->Sales_agent_model->get_goods_receipt_detail($id);

        $data['goods_receipt'] = $this->Sales_agent_model->get_goods_receipt($id);

        $data['tax_data'] = $this->Sales_agent_model->get_html_tax_receip($id);

        $data['title'] = _l('stock_received_info');


        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['base_currency'] = $base_currency;


        $this->data($data);
        $this->view('portal/goods_receipt/view_purchase');
        $this->layout();

    }

    /**
     * { delivery vouchers }
     */
    public function delivery_vouchers(){

        $data['title'] = _l('stock_delivery_manage');
        
        $this->data($data);
        $this->view('portal/goods_delivery/manage_delivery');
        $this->layout();
    }


    /**
     * table manage delivery
     * @return array
     */
    public function table_manage_delivery() {
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/goods_delivery/table_manage_delivery'));
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
        $success = $this->Sales_agent_model->delivery_status_mark_as($status, $id, $type);
        $message = '';

        if ($success) {
            $message = _l('wh_change_delivery_status_successfully');
        }
        echo json_encode([
            'success'  => $success,
            'message'  => $message
        ]);
    }


    /**
     * goods delivery
     * @return view
     */
    public function goods_delivery($id ='', $edit_approval = false) {

        $agent_id = get_sale_agent_user_id();
        $this->load->model('clients_model');
        $this->load->model('taxes_model');
        if ($this->input->post()) {
            $message = '';
            $data = $this->input->post();



            if (!$this->input->post('id')) {
                $mess = $this->Sales_agent_model->add_goods_delivery($data);
                if ($mess) {
 
                    set_alert('success', _l('added_successfully'));

                } else {
                    set_alert('warning', _l('Add_stock_delivery_docket_false'));
                }
                redirect(site_url('sales_agent/portal/delivery_vouchers'));

            }else{
                $id = $this->input->post('id');
                $goods_delivery = $this->Sales_agent_model->get_goods_delivery($id);
              
                $mess = $this->Sales_agent_model->update_goods_delivery_approval($data);
                

                if ($mess) {
                    set_alert('success', _l('updated_successfully'));
                }
                redirect(site_url('sales_agent/portal/delivery_vouchers'));
            }

        }
   
        $data['commodity_code_name'] = $this->Sales_agent_model->get_commodity_code_name();
        $data['units_code_name'] = $this->Sales_agent_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->Sales_agent_model->get_warehouse_code_name($agent_id);
 

        $data['title'] = _l('goods_delivery');

        $data['commodity_codes'] = $this->Sales_agent_model->get_commodity();
        $data['warehouses'] = $this->Sales_agent_model->get_warehouse_by_agent($agent_id);

        $data['taxes'] = $this->taxes_model->get();
      
        $data['items'] = $this->Sales_agent_model->get_item_by_agent($agent_id);
        

        $warehouse_data = $this->Sales_agent_model->get_warehouse_by_agent($agent_id);
        //sample
        $goods_delivery_row_template = '';
        if(is_numeric($id)){
            $goods_delivery = $this->Sales_agent_model->get_goods_delivery($id);
            if($goods_delivery->approval == 0){
                $goods_delivery_row_template = $this->Sales_agent_model->create_goods_delivery_row_template();
            }
        }else{
            $goods_delivery_row_template = $this->Sales_agent_model->create_goods_delivery_row_template();
        }

        
        $data['pr_orders'] = [];
        $data['pr_orders_status'] = false;
        
        
        $data['customer_code'] = $this->Sales_agent_model->get_sa_client();
        if($edit_approval){
            $invoices_data = $this->db->query('select *, iv.id as id from '.db_prefix().'sa_sale_invoices as iv left join '.db_prefix().'sa_clients as cl on cl.id = iv.clientid where iv.agent_id = '.$agent_id.'  order by iv.id desc')->result_array();
            $data['invoices'] = $invoices_data;
        }else{
            $data['invoices'] = $this->Sales_agent_model->get_invoices();
        }
        $data['goods_code'] = $this->Sales_agent_model->create_goods_delivery_code($agent_id);
      
        $data['current_day'] = date('Y-m-d');

        if($id != ''){
            $is_purchase_order = false;
            $goods_delivery = $this->Sales_agent_model->get_goods_delivery($id);
            if (!$goods_delivery) {
                blank_page('Stock export Not Found', 'danger');
            }
            $data['goods_delivery_detail'] = $this->Sales_agent_model->get_goods_delivery_detail($id);
            $data['goods_delivery'] = $goods_delivery;

            if(isset($goods_delivery->pr_order_id ) && (float)$goods_delivery->pr_order_id > 0){
                $is_purchase_order = true;
            }

            if (count($data['goods_delivery_detail']) > 0) {
                $index_receipt = 0;
                foreach ($data['goods_delivery_detail'] as $delivery_detail) {
                    if($delivery_detail['commodity_code'] != null && is_numeric($delivery_detail['commodity_code'])){
                        $index_receipt++;
                        $unit_name = sa_get_unit_name($delivery_detail['unit_id']);
                        $taxname = '';
                        $expiry_date = null;
                        $lot_number = null;
                        $commodity_name = $delivery_detail['commodity_name'];
                        $without_checking_warehouse = 0;

                        if(strlen($commodity_name) == 0){
                            $commodity_name = sa_get_item_variatiom($delivery_detail['commodity_code']);
                        }

                        $get_commodity = $this->Sales_agent_model->get_commodity($delivery_detail['commodity_code']);
                        if($get_commodity){
                            $without_checking_warehouse = $get_commodity->without_checking_warehouse;
                        }

                        $goods_delivery_row_template .= $this->Sales_agent_model->create_goods_delivery_row_template($warehouse_data, 'items[' . $index_receipt . ']', $commodity_name, $delivery_detail['warehouse_id'], $delivery_detail['available_quantity'], $delivery_detail['quantities'], $unit_name, $delivery_detail['unit_price'], $taxname, $delivery_detail['commodity_code'], $delivery_detail['unit_id'] , $delivery_detail['tax_rate'], $delivery_detail['total_money'], $delivery_detail['discount'], $delivery_detail['discount_money'], $delivery_detail['total_after_discount'],$delivery_detail['guarantee_period'], $expiry_date, $lot_number, $delivery_detail['note'], $delivery_detail['sub_total'],$delivery_detail['tax_name'],$delivery_detail['tax_id'], $delivery_detail['id'], true, $is_purchase_order, $delivery_detail['serial_number'], $without_checking_warehouse);

                    }
                }
            }
        }

        //edit note after approval
        $data['edit_approval'] = $edit_approval;
        $data['goods_delivery_row_template'] = $goods_delivery_row_template;
        $get_base_currency =  get_base_currency();
        if($get_base_currency){
            $data['base_currency_id'] = $get_base_currency->id;
        }else{
            $data['base_currency_id'] = 0;
        }

        $this->data($data);
        $this->view('portal/goods_delivery/delivery');
        $this->layout();

    }


    /**
     * get quantity inventory
     * @return [type] 
     */
    public function get_quantity_inventory() {
        $data = $this->input->post();
        if ($data != 'null') {

            $value = $this->Sales_agent_model->get_quantity_inventory($data['warehouse_id'], $data['commodity_id']);

            $quantity = 0;
            if ($value != null) {

                $message = true;
                $quantity = get_object_vars($value)['inventory_number'];

            } else {
                $message = _l('Product_does_not_exist_in_stock');
            }

            
            echo json_encode([
                'message' => $message,
                'value' => $quantity,
            ]);
            die;
        }
    }

    /**
     * check quantity inventory
     * @return json
     */
    public function check_quantity_inventory() {
        $data = $this->input->post();
        if ($data != 'null') {

            //switch_barcode_scanners
            if($data['switch_barcode_scanners'] == 'true'){
                $data['commodity_id'] = $this->Sales_agent_model->get_commodity_id_from_barcode($data['commodity_id']);
            }

            /*check without checking warehouse*/
            if($this->Sales_agent_model->check_item_without_checking_warehouse($data['commodity_id']) == true){
                //checking

                $value = $this->Sales_agent_model->get_quantity_inventory($data['warehouse_id'], $data['commodity_id']);

                $quantity = 0;
                if ($value != null) {

                    if ((float) get_object_vars($value)['inventory_number'] < (float) $data['quantity']) {
                        $message = _l('in_stock');
                        $quantity = (float)get_object_vars($value)['inventory_number'];
                    } else {
                        $message = true;
                        $quantity = (float)get_object_vars($value)['inventory_number'];
                    }

                } else {
                    $message = _l('Product_does_not_exist_in_stock');
                }

            }else{
                //without checking
                $message = true;
                $quantity = 0;

            }

            echo json_encode([
                'message' => $message,
                'value' => $quantity,
            ]);
            die;
        }
    }

    /**
     * get good delivery row template
     * @return [type] 
     */
    public function get_good_delivery_row_template()
    {
        $name = $this->input->post('name');
        $commodity_name = $this->input->post('commodity_name');
        $warehouse_id = $this->input->post('warehouse_id');
        $available_quantity = $this->input->post('available_quantity');
        $quantities = $this->input->post('quantities');
        $unit_name = $this->input->post('unit_name');
        $unit_price = $this->input->post('unit_price');
        $taxname = $this->input->post('taxname');
        $lot_number = $this->input->post('lot_number');
        $expiry_date = $this->input->post('expiry_date');
        $commodity_code = $this->input->post('commodity_code');
        $unit_id = $this->input->post('unit_id');
        $tax_rate = $this->input->post('tax_rate');
        $discount = $this->input->post('discount');
        $note = $this->input->post('note');
        $guarantee_period = $this->input->post('guarantee_period');
        $item_key = $this->input->post('item_key');
        $item_index = $this->input->post('item_index');
        $formdata = $this->input->post('formdata');
        $without_checking_warehouse = $this->input->post('without_checking_warehouse');

        $goods_delivery_row_template = '';
        $temporaty_quantity = $quantities;
        $temporaty_available_quantity = $available_quantity;
        $list_temporaty_serial_numbers = [];

        if($without_checking_warehouse == 0 || $without_checking_warehouse == '0'){

            if(is_array($formdata) && count($formdata) > 1){

                foreach ( $formdata as $key => $form_value) {
                    if($form_value['name'] != 'csrf_token_name'){
                        $list_temporaty_serial_numbers[] = [
                            'serial_number' => $form_value['value'],
                        ];
                    }
                }
            }else{

                $list_temporaty_serial_numbers = $this->Sales_agent_model->get_list_temporaty_serial_numbers($commodity_code, $warehouse_id, $quantities);
            }
        }

        foreach ($list_temporaty_serial_numbers as $value) {
            $temporaty_commodity_name = $commodity_name.' SN: '.$value['serial_number'];
            $quantities = 1;
            $name = 'newitems['.$item_index.']';

            $goods_delivery_row_template .= $this->Sales_agent_model->create_goods_delivery_row_template([], $name, $temporaty_commodity_name, $warehouse_id, $temporaty_available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', $discount, '', '', $guarantee_period, $expiry_date, $lot_number, $note, '', '', '', $item_key, false, false, $value['serial_number'], $without_checking_warehouse );
            $temporaty_quantity--;
            $temporaty_available_quantity--;
            $item_index ++;
        }

        if($temporaty_quantity > 0){
            $quantities = $temporaty_quantity;
            $available_quantity = $temporaty_available_quantity;
            $name = 'newitems['.$item_index.']';

            $goods_delivery_row_template .= $this->Sales_agent_model->create_goods_delivery_row_template([], $name, $commodity_name, $warehouse_id, $available_quantity, $quantities, $unit_name, $unit_price, $taxname, $commodity_code, $unit_id, $tax_rate, '', $discount, '', '', $guarantee_period, $expiry_date, $lot_number, $note, '', '', '', $item_key, false, false, '', $without_checking_warehouse);
            $item_index ++;
        }

        echo html_entity_decode($goods_delivery_row_template);
    }


    /**
     * coppy invoices
     * @param  integer $invoice_id 
     * @return json              
     */
    public function copy_invoices($invoice_id = '') {

        $invoices_detail = $this->Sales_agent_model->copy_invoice($invoice_id);
        if($invoice_id != ''){
            $invoice_no = get_sa_invoice_number($invoice_id);
        }else{
            $invoice_no = '';
        }
        echo json_encode([

            'result' => $invoices_detail['goods_delivery_detail'],
            'goods_delivery' => $invoices_detail['goods_delivery'],
            'status' => $invoices_detail['status'],
            'invoice_no' => $invoice_no,
        ]);
    }


    /**
     * delete_goods_delivery
     * @param  [integer] $id
     * @return [redirect]
     */
    public function delete_goods_delivery($id) {
        $goods_delivery = $this->Sales_agent_model->get_goods_delivery($id);
        $agent_id = get_sale_agent_user_id();

        if(!$goods_delivery){
            show_404();
        }

        if($goods_delivery->agent_id != $agent_id){
            show_404();
        }

        $response = $this->Sales_agent_model->delete_goods_delivery($id);
        if ($response == true) {
            set_alert('success', _l('deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        redirect(site_url('sales_agent/portal/delivery_vouchers'));
    }


    /**
     * view delivery
     * @param  integer $id
     * @return view
     */
    public function view_delivery($id) {
        $agent_id = get_sale_agent_user_id();

        //get vaule render dropdown select
        $data['commodity_code_name'] = $this->Sales_agent_model->get_commodity_code_name();
        $data['units_code_name'] = $this->Sales_agent_model->get_units_code_name();
        $data['units_warehouse_name'] = $this->Sales_agent_model->get_warehouse_code_name($agent_id);

        $data['goods_delivery_detail'] = $this->Sales_agent_model->get_goods_delivery_detail($id);

        $data['goods_delivery'] = $this->Sales_agent_model->get_goods_delivery($id);
   

        $data['title'] = _l('stock_export_info');

        $data['tax_data'] = $this->Sales_agent_model->get_html_tax_delivery($id);
        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency();
        $data['base_currency'] = $base_currency;

        $this->data($data);
        $this->view('portal/goods_delivery/view_delivery');
        $this->layout();
    }


    /**
     * warehouse history
     *
     * @return view
     */
    public function inventory_history() {
        $data['title'] = _l('warehouse_history');

        $agent_id = get_sale_agent_user_id();

        $data['warehouse_filter'] = $this->Sales_agent_model->get_warehouse_by_agent($agent_id);
       
        $data['items'] = $this->Sales_agent_model->get_item_by_agent($agent_id);
        
        $this->data($data);
        $this->view('portal/warehouse/warehouse_history');
        $this->layout();
    }


    /**
     * table warehouse history
     *
     * @return array
     */
    public function table_warehouse_history() {
        $this->app->get_table_data(module_views_path('sales_agent', 'portal/warehouse/table_warehouse_history'));
    }


    /**
     * { Purchase reports }
     * 
     * @return view
     */
    public function reports(){
        $agent_id = get_sale_agent_user_id();


        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['title'] = _l('sa_reports');
        $data['agent_id'] = $agent_id;

        $this->data($data);
        $this->view('portal/reports/manage_report');
        $this->layout();
    }


    /**
     *  purchase inv report
     *  
     *  @return json
     */
    public function list_inv_report()
    {
        $agent_id = get_sale_agent_user_id();
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'inv_number',
                'clientid',
                'date',
                'duedate',
                'status',
                'subtotal',
                'total_tax',
                'total',
            ];
            $where =[];

            array_push($where, 'AND '.db_prefix().'sa_sale_invoices.agent_id = '.$agent_id);

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'sa_sale_invoices.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            

            $currency = $this->currencies_model->get_base_currency();

            if($this->input->post('report_currency')){
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency();

                if($report_currency == $base_currency->id){
                    array_push($where, 'AND '.db_prefix().'sa_sale_invoices.currency IN (0, '.$report_currency.')');
                }else{
                    array_push($where, 'AND '.db_prefix().'sa_sale_invoices.currency = '.$report_currency);
                }

                $currency = sa_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'sa_sale_invoices';
            $join         = [
               
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix().'sa_sale_invoices.id as id',
          

            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . site_url('sales_agent/portal/sale_invoice_detail/' . $aRow['id']) . '" target="_blank">' . $aRow['inv_number'] . '</a>';

                $row[] = get_sa_customer_name_by_id($aRow['clientid']);

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['duedate']);

                $class = '';
                if($aRow['status'] == 'unpaid'){
                    $class = 'danger';
                }elseif($aRow['status'] == 'paid'){
                    $class = 'success';
                }elseif ($aRow['status'] == 'partially_paid') {
                    $class = 'warning';
                }

                $row[] = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['status']).'</span>';

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['total_tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['total_tax'];
                $footer_data['total_value'] += $aRow['subtotal'];
              
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     *  po voucher report
     *  
     *  @return json
     */
    public function po_report()
    {
        $agent_id = get_sale_agent_user_id();
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');

            $select = [
                'order_number',
                'order_date',
                'approve_status',
                'subtotal',
                'total_tax',
                'total',
            ];
            $where =[];

            array_push($where, 'AND '.db_prefix().'sa_pur_orders.agent_id = '.$agent_id);

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'sa_pur_orders.order_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $currency = $this->currencies_model->get_base_currency();

            if($this->input->post('report_currency')){
                $report_currency = $this->input->post('report_currency');
                $base_currency = get_base_currency();

                if($report_currency == $base_currency->id){
                    array_push($where, 'AND '.db_prefix().'sa_pur_orders.currency IN (0, '.$report_currency.')');
                }else{
                    array_push($where, 'AND '.db_prefix().'sa_pur_orders.currency = '.$report_currency);
                }

                $currency = sa_get_currency_by_id($report_currency);
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'sa_pur_orders';
            $join         = [
               
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                db_prefix().'sa_pur_orders.id as id',
                'total',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'total_tax'       => 0,
                'total_value'     => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . site_url('sales_agent/portal/order_detail/' . $aRow['id']) . '" target="_blank">' . $aRow['order_number'] . '</a>';

                $row[] = _d($aRow['order_date']);


                $row[] = get_sa_status_approve($aRow['approve_status']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);

                $row[] = app_format_money($aRow['total_tax'], $currency->name);

                $row[] = app_format_money($aRow['total'], $currency->name);

                $footer_data['total'] += $aRow['total'];
                $footer_data['total_tax'] += $aRow['total_tax'];
                $footer_data['total_value'] += $aRow['subtotal'];
              
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    /**
     * Gets the where report period.
     *
     * @param      string  $field  The field
     *
     * @return     string  The where report period.
     */
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }

        return $custom_date_select;
    }

    /**
     * { total income report }
     */
    public function total_income_report(){
        $agent_id = get_sale_agent_user_id();
        $this->load->model('currencies_model');
        $year_report      = $this->input->post('year');
        $report_currency = $this->input->post('report_currency');
        $currency = sa_get_currency_by_id($report_currency);
        
        $currency_name = '';
        $currency_unit = '';
        if($currency){
            $currency_name = $currency->name;
            $currency_unit = $currency->symbol;
        }
        echo json_encode([
            'data' => $this->Sales_agent_model->total_income_report($year_report, $report_currency, $agent_id),
            'unit' => $currency_unit,
            'name' => $currency_name,
        ]);
        die();
    }

     /**
     * { total income report }
     */
    public function total_expense_report(){
        $agent_id = get_sale_agent_user_id();
        $this->load->model('currencies_model');
        $year_report      = $this->input->post('year');
        $report_currency = $this->input->post('report_currency');
        $currency = sa_get_currency_by_id($report_currency);
        
        $currency_name = '';
        $currency_unit = '';
        if($currency){
            $currency_name = $currency->name;
            $currency_unit = $currency->symbol;
        }
        echo json_encode([
            'data' => $this->Sales_agent_model->total_expense_report($year_report, $report_currency, $agent_id),
            'unit' => $currency_unit,
            'name' => $currency_name,
        ]);
        die();
    }

    /**
     * { delete_pur_order }
     */
    public function delete_pur_order($id){
        $agent_id = get_sale_agent_user_id();

        $order = $this->Sales_agent_model->get_pur_order($id);
        if(!$order){
            show_404();
        }

        if($order->agent_id != $agent_id){
            show_404();
        }

        $success = $this->Sales_agent_model->delete_pur_order($id);
        if($success){
            set_alert('success', _l('deleted_successfully'));
        }

        redirect(site_url('sales_agent/portal/purchase_orders'));
    }
}