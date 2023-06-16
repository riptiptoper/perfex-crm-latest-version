<?php

defined('BASEPATH') or exit('No direct script access allowed');

$where = [];

$group_by = $this->ci->input->post('group_by');

    $flag_phone = false;
    $flag_date = false;
    $flag_staff = false;

if (isset($group_by)) {


    foreach ($group_by as $group_value) {
        if($group_value == 'group_by_phone'){
            $flag_phone = true;
        }

        if($group_value == 'group_by_date'){
            $flag_date = true;
        } 

        if($group_value == 'group_by_staff'){
            $flag_staff = true;
        } 
           
    }

    if($flag_phone == true && $flag_date == true && $flag_staff == true){
        $aColumns = [
 
            'phone',
             "DATE_FORMAT(trans_date, '%Y-%m-%d') as trans_date",
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",
            "staffname",

            ];

    }elseif($flag_phone == true && $flag_date == true){
        $aColumns = [
 
            'phone',
             "DATE_FORMAT(trans_date, '%Y-%m-%d') as trans_date",
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",
            ];

    }elseif($flag_phone == true && $flag_staff == true){
        $aColumns = [
 
            'phone',
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",
            "staffname",

            ];

    }elseif($flag_date == true && $flag_staff == true){
        $aColumns = [
 
             "DATE_FORMAT(trans_date, '%Y-%m-%d') as trans_date",
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",
            "staffname",

            ];

    }elseif($flag_staff == true){
        $aColumns = [
 
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",
            'staffname',

            ];

    }elseif($flag_phone == true){
        $aColumns = [
 
            'phone',
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",

            ];

    }else{
        //$flag == date
        $aColumns = [
 
             "DATE_FORMAT(trans_date, '%Y-%m-%d') as trans_date",
            "sum(trans_amount) as total_amount",
            "count(id) as total_trans",

            ];

    }
}

$phone_f ='';

if($this->ci->input->post('filter_by_phone')){
     $filter_by_phone = $this->ci->input->post('filter_by_phone');
     $phone_f = $filter_by_phone;

    $where[] = ' AND '.db_prefix().'mpesatransc.phone = '.$filter_by_phone;

}


$from_date_f = '';

if($this->ci->input->post('from_date')){
    $from_date = to_sql_date($this->ci->input->post('from_date'));
    $from_date_f = $from_date;

    $where[] = " AND date_format(trans_date, '%Y-%m-%d') >= '".$from_date."'"; 

}

$to_date_f = '';

if($this->ci->input->post('to_date')){
    $to_date = to_sql_date($this->ci->input->post('to_date'));
    $to_date_f = $to_date;

    $where[] = " AND date_format(trans_date, '%Y-%m-%d') <= '".$to_date."'"; 


}
// die;

if($flag_phone == false && $flag_date == false && $flag_staff == false){
$aColumns = [
 
    'id',
    'transc_id',
    'trans_type',
    'trans_time',
    'date_format(trans_date, "%Y-%m-%d") as trans_date',
    'trans_amount',
    'phone',
    'first_name',
    'middle_name',
    'last_name',
    'bill_ref_number',
    'short_code',
    'trans_id',
    'sale_id',
    'pumpId',
    'employee_name',
    'mpesaType',
    'mpesaType',
    'customer_id',
    'staffname',
     // "count(id) as total_trans",

    ];
}


if($flag_phone == true && $flag_date == true && $flag_staff == true){

    $sIndexColumn = 'phone';

}elseif($flag_phone == true && $flag_date == true){

    $sIndexColumn = 'phone';

}elseif($flag_phone == true && $flag_staff == true){

    $sIndexColumn = 'phone';

}elseif($flag_date == true && $flag_staff == true){

    $sIndexColumn = 'trans_date';

}elseif($flag_staff == true){

    $sIndexColumn = 'staffname';

}elseif($flag_phone == true){

    $sIndexColumn = 'phone';


}elseif($flag_date == true){

    $sIndexColumn = 'trans_date';


}else{

    $sIndexColumn = 'id';
}

$sTable       = db_prefix().'mpesatransc';
$join         = [ ];




    if($flag_phone == true && $flag_date == true && $flag_staff == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by phone, date_format(trans_date, "%Y-%m-%d"), staffname');

    }elseif($flag_phone == true && $flag_date == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by phone, date_format(trans_date, "%Y-%m-%d")');

    }elseif($flag_phone == true && $flag_staff == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by phone, staffname');

    }elseif($flag_staff == true && $flag_date == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by staffname, date_format(trans_date, "%Y-%m-%d")');

    }elseif($flag_staff == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by staffname');

    }elseif($flag_phone == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by phone');

    }elseif($flag_date == true){
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'group by date_format(trans_date, "%Y-%m-%d")');

    }else{

        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [] );
    }
// die;

$output  = $result['output'];
$rResult = $result['rResult'];


if($flag_phone == true && $flag_date == true && $flag_staff ==true){
       foreach ($rResult as $aRow) {
/*1-2-3*/
        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => $aRow['phone'], 'date' => $aRow['trans_date'], 'staffname' => $aRow['staffname'], 'phone_f' => $phone_f, 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_phone').' - '._l('group_by_date').' - '._l('group_by_staff');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = $aRow['trans_date'];
    $row[] = $aRow['total_amount'];
    $row[] = $aRow['phone'];
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
    $row[] = '';
    $row[] = get_staff_full_name($aRow['staffname']);


        $output['aaData'][] = $row;

    }

}elseif($flag_phone == true && $flag_date == true){
       foreach ($rResult as $aRow) {
/*1-2*/
        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => $aRow['phone'], 'date' => $aRow['trans_date'], 'staffname' => '', 'phone_f' => $phone_f, 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_phone').' - '._l('group_by_date');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = $aRow['trans_date'];
    $row[] = $aRow['total_amount'];
    $row[] = $aRow['phone'];
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
    $row[] = '';
    $row[] = '';


        $output['aaData'][] = $row;

    }

}elseif($flag_phone == true && $flag_staff == true){
       foreach ($rResult as $aRow) {
/*1-3*/

        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => $aRow['phone'], 'date' => '', 'staffname' => $aRow['staffname'], 'phone_f' => $phone_f, 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_phone').' - '._l('group_by_staff');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = '';
    $row[] = $aRow['total_amount'];
    $row[] = $aRow['phone'];
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
    $row[] = '';
    $row[] = get_staff_full_name($aRow['staffname']);


        $output['aaData'][] = $row;

    }

}elseif($flag_staff == true && $flag_date == true){
       foreach ($rResult as $aRow) {
/*2-3*/

        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => '', 'date' => $aRow['trans_date'], 'staffname' => $aRow['staffname'], 'phone_f' => $phone_f, 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_date').' - '._l('group_by_staff');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = $aRow['trans_date'];
    $row[] = $aRow['total_amount'];
    $row[] = '';
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
    $row[] = '';
    $row[] = get_staff_full_name($aRow['staffname']);


        $output['aaData'][] = $row;

    }

}elseif($flag_staff == true){
       foreach ($rResult as $aRow) {
/*3*/

        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => '', 'date' => '', 'staffname' => $aRow['staffname'], 'phone_f' => $phone_f, 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_staff');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = '';
    $row[] = $aRow['total_amount'];
    $row[] = '';
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
    $row[] = '';
    $row[] = get_staff_full_name($aRow['staffname']);


        $output['aaData'][] = $row;

    }

}elseif($flag_phone == true){

    foreach ($rResult as $aRow) {
/*1*/

        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => $aRow['phone'], 'date' => '', 'phone_f' => $phone_f, 'staffname' => '', 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f]);

    $row = [];
    $row[] =  '';
        
    $row[] = _l('group_by_phone');
    $row[] = '';
    $row[] = $arr_data['trans_type'];
    $row[] = $arr_data['trans_time'];
    $row[] = '';
    $row[] = $aRow['total_amount'];
    $row[] = $aRow['phone'];
    $row[] = $arr_data['first_name'];
    $row[] = $arr_data['middle_name'];
    $row[] = $arr_data['last_name'];
    $row[] = $arr_data['bill_ref_number'];
    $row[] = $arr_data['short_code'];
    $row[] = '';
    $row[] = $arr_data['sale_id'];
    $row[] = $arr_data['pumpId'];
    $row[] = $arr_data['employee_name'];
    $row[] = $arr_data['mpesaType'];
    $row[] = $aRow['total_trans'];
     $row[] = '';
    $row[] = '';


        $output['aaData'][] = $row;

    }

}elseif($flag_date == true){
    foreach ($rResult as $aRow) {
/*2*/
        //get value 
        $arr_data = $this->ci->omni_sales_model->get_mpesatransc_grouped(['phone' => '', 'date' => $aRow['trans_date'], 'phone_f' => $phone_f, 'staffname' => '', 'from_date_f' => $from_date_f, 'to_date_f' => $to_date_f, 'flag_date' => $flag_date]);

        $row = [];
        $row[] =  '';
            
        $row[] = _l('group_by_date');
        $row[] = '';
        $row[] = $arr_data['trans_type'];
        $row[] = $arr_data['trans_time'];
        $row[] = $aRow['trans_date'];
        $row[] = $aRow['total_amount'];
        $row[] = '';
        $row[] = $arr_data['first_name'];
        $row[] = $arr_data['middle_name'];
        $row[] = $arr_data['last_name'];
        $row[] = $arr_data['bill_ref_number'];
        $row[] = $arr_data['short_code'];
        $row[] = '';
        $row[] = $arr_data['sale_id'];
        $row[] = $arr_data['pumpId'];
        $row[] = $arr_data['employee_name'];
        $row[] = $arr_data['mpesaType'];
        $row[] = $aRow['total_trans'];
         $row[] = '';
        $row[] = '';


        $output['aaData'][] = $row;

    }

}else{

    foreach ($rResult as $aRow) {
        $CI           = & get_instance();
        $row = [];
        $row[] =  '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
        
            $id_value = $aRow['id'];

                if(has_permission('omni_sales', '', 'edit') || is_admin() ){

                    $id_value .= ' <a href="#" onclick="edit_mpesatransc(this, '.$aRow['id'].'); return false;"  >' . _l('edit') . '</a>';
                }

        $row[] = $id_value;

        $row[] = $aRow['transc_id'];
        $row[] = $aRow['trans_type'];
        $row[] = $aRow['trans_time'];
        $row[] = $aRow['trans_date'];
        $row[] = $aRow['trans_amount'];
        $row[] = $aRow['phone'];
        $row[] = $aRow['first_name'];
        $row[] = $aRow['middle_name'];
        $row[] = $aRow['last_name'];
        $row[] = $aRow['bill_ref_number'];
        $row[] = $aRow['short_code'];
        $row[] = $aRow['trans_id'];
        $row[] = $aRow['sale_id'];
        $row[] = $aRow['pumpId'];
        $row[] = $aRow['employee_name'];
        $row[] = $aRow['mpesaType'];
        $row[] = '';

        if($aRow['customer_id']){
                $CI->db->where(db_prefix() . 'clients.userid', $aRow['customer_id']);
                $client = $CI->db->get(db_prefix() . 'clients')->row();
                if($client){
                    $row[] = $client->company;
                }else{
                    $row[] = '';

                }

            }else{
                $row[] = '';

            }

        $row[] = get_staff_full_name($aRow['staffname']);


        $output['aaData'][] = $row;

    }
}
