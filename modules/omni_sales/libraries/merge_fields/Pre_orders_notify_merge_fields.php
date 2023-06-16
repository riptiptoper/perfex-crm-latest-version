<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pre_orders_notify_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Seller name',
                'key'       => '{seller_name}',
                'available' => [
                    'omni_sales',
                ],
            ],
            
            [
                'name'      => 'Buyer name',
                'key'       => '{buyer_name}',
                'available' => [
                    'omni_sales',
                ],
            ],
            
            [
                'name'      => 'Create at',
                'key'       => '{create_at}',
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

        $fields['{seller_name}'] = $notification_info->seller_name;
        $fields['{buyer_name}'] = $notification_info->buyer_name;
        $fields['{create_at}'] = $notification_info->create_at;
        $fields['{link}'] = $notification_info->link;
        return $fields;
    }


}
