<?php 
$path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
include($path . 'wp-load.php');

extract($_POST);

if (isset($_POST)){
    global $wpdb;
    $table_name = $wpdb->prefix . "form_register";
    $result_check = $wpdb->insert($table_name, array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'city' => $city,
    ));
      
    if($result_check){
        echo "<div class='alert alert-success'>Successfully inserted!</div>";
    }
    else{
        echo "<div class='alert alert-danger'>Something went wrong!</div>";
    }
}
?>
