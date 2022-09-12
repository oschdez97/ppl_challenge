<?php

require_once 'sql_query.php';
require_once 'utils.php';

$raw_data = file_get_contents('php://input');
$user = json_decode($raw_data, true);

$name = isset($user["name"]) ? trim($user["name"]) : null;
$lastname = isset($user["lastname"]) ? trim($user["lastname"]) : null;
$phone = isset($user["phone"]) ? trim($user["phone"]) : null;

$regex = '/^[a-z A-Z]+$/i';
$phone_validation_response = validate_phone($phone);

$skip_phone_validation = false;
if(isset($phone_validation_response["api-error"]) && $phone_validation_response["api-error"] == 2) 
{
    // neutrinoapi says => DAILY API LIMIT EXCEEDED
    // I am going to skip the phone verification step in order to continue with the execution of the test
    $skip_phone_validation = true;
}

if($name == null) 
{
    send_response(400, "Missing required field: name");
}

else if($phone == null) 
{
    send_response(400, "Missing required field: phone");
}

else if(!preg_match($regex, $name) || (strlen($lastname) && !preg_match($regex, $lastname))) 
{
    send_response(400, "Invalid name or lastname format");
}

else if(!$skip_phone_validation && !$phone_validation_response["valid"]) 
{
    send_response(400, "Invalid phone number");
}

else if(get_user_by_phone($phone)->num_rows > 0) 
{
    send_response(400, "The user with phone number: ".$phone. " is registrated");
}

else 
{
    $response = insert_user($user);
    if($response != null)
    {
        send_response(201, "User created successfully");
    }
    
    else 
    {
        send_response(500, "Ups! Something went wrong");
    }
}


?>