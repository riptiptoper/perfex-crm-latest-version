<?php

// Version 2.3.0 and above
hooks()->add_filter('before_get_task_statuses','my_add_custom_task_status');

// Prior to version 2.3.0
// Uncomment the code below and remove the code above if you are using version older then 2.3.0
// add_action('before_get_task_statuses','my_add_custom_task_status');


function my_add_custom_task_status($current_statuses){
    // Push new status to the current statuses
    $current_statuses[] = array(
           'id'=>46, // new status with id 46
           'color'=>'#0781ad',
           'name'=>'B Intrebare productie',
           'order'=>6,
           'filter_default'=>true, // true or false
        );
 $current_statuses[] = array(
           'id'=>47, // new status with id 47
           'color'=>'#f6b26b',
           'name'=>'1. Proiectare',
           'order'=>7,
           'filter_default'=>true, // true or false
        );

  $current_statuses[] = array(
           'id'=>48, // new status with id 48
           'color'=>'#e69138',
           'name'=>'2. Remasurari',
           'order'=>8,
           'filter_default'=>true, // true or false
        );
  $current_statuses[] = array(
           'id'=>49, // new status with id 49
           'color'=>'#b45f06',
           'name'=>'3. Verificare proiect',
           'order'=>9,
           'filter_default'=>true, // true or false
        );
  $current_statuses[] = array(
           'id'=>50, // new status with id 50
           'color'=>'#3d85c6',
           'name'=>'4. Lansare in Productie',
           'order'=>10,
           'filter_default'=>true, // true or false
        );

    // Push another status (delete this code if you need to add only 1 status)
    $current_statuses[] = array(
          'id'=>51, //new status with new id 51
          'color'=>'#0086ff',
          'name'=>'5. Productie',
          'order'=>11,
          'filter_default'=>true // true or false
        );
 	$current_statuses[] = array(
          'id'=>52, //new status with new id 52
          'color'=>'#038fad',
          'name'=>'6. Finalizare productie',
          'order'=>12,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>53, //new status with new id 53
          'color'=>'#d5a6bd',
          'name'=>'7. Montaj hala',
          'order'=>13,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>54, //new status with new id 54
          'color'=>'#c27ba0',
          'name'=>'8. Ambalare',
          'order'=>14,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>55, //new status with new id 55
          'color'=>'#f44336',
          'name'=>'9. Gata de livrare',
          'order'=>15,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>56, //new status with new id 56
          'color'=>'#93c47d',
          'name'=>'10. Livrat client',
          'order'=>16,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>57, //new status with new id 57
          'color'=>'#38761d',
          'name'=>'11. Montat 50%',
          'order'=>17,
          'filter_default'=>true // true or false
        );
 $current_statuses[] = array(
          'id'=>58, //new status with new id 58
          'color'=>'#be83d8',
          'name'=>'12. La semnare',
          'order'=>18,
          'filter_default'=>true // true or false
        );


    // Return the statuses
    return $current_statuses;
}