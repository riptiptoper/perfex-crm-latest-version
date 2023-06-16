<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pre_orders_handover_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'To name',
                'key'       => '{to_name}',
                'available' => [
                    'omni_sales',
                ],
            ],
            
            [
                'name'      => 'From name',
                'key'       => '{from_name}',
                'available' => [
                    'omni_sales',
                ],
            ],

            [
                'name'      => 'Link',
                'key'       => '{link}',
                'available' => [
                    'omni_sales',
                ],
            ]
            
        ];
    }


    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($notification_info)
    {
        $fields = [];

        if (!$notification_info) {
            return $fields;
        }

        $fields['{to_name}'] = $notification_info->to_name;

        $fields['{from_name}'] = $notification_info->from_name;

        $fields['{link}'] = $notification_info->link;

        return $fields;
    }


}
