<?php

require_once 'sql_query.php';
require_once 'utils.php';


$method = $_SERVER['REQUEST_METHOD'];
$raw_data = file_get_contents('php://input');
$contact_data = json_decode($raw_data, true);

if($method == "GET") 
{
    if(!isset($_GET["user_id"])) 
    {
        send_response(400, "Missing required query string parameter: user_id");
    }
    $user_contacts = array();
    $response = get_contacts($_GET["user_id"]);
    while($row = $response->fetch_assoc()) 
    {
        $user_contacts[] = $row;
    }
    send_response(200, "OK", $user_contacts);
}


$user_id = isset($contact_data["user_id"]) ? trim($contact_data["user_id"]) : null;
$contact_name = isset($contact_data["contact_name"]) ? trim($contact_data["contact_name"]) : null;
$contact_phone_number = isset($contact_data["contact_phone_number"]) ? trim($contact_data["contact_phone_number"]) : null;

$regex = '/^[a-z A-Z]+$/i';
$phone_validation_response = validate_phone($contact_phone_number);

$skip_phone_validation = false;
if(isset($phone_validation_response["api-error"]) && $phone_validation_response["api-error"] == 2) 
{
    // neutrinoapi says => DAILY API LIMIT EXCEEDED
    // I am going to skip the phone verification step in order to continue with the execution of the test
    $skip_phone_validation = true;
}

if($user_id == null) 
{
    send_response(400, "Missing required field: user_id");
}

else if(!get_user_by_id($user_id)->num_rows) 
{
    send_response(400, "Invalid field the user with id: ".$user_id. " does not exist");
}


else if(!preg_match($regex, $contact_name)) 
{
    send_response(400, "Invalid format contact_name");
}

else if(!$skip_phone_validation && !$phone_validation_response["valid"]) 
{
    send_response(400, "Invalid phone number");
}

else if($method == "POST" && get_contacts($user_id, ["name" => $contact_name, "phone" => $contact_phone_number])->num_rows > 0) 
{
    send_response(400, "The contact is already registrated");
}


if($method == "POST") 
{
    $response = insert_contact($contact_data);
    $status_code = 201;
    $msg = "Contact added successfully";
}


else if($method == "PUT")
{
    if(isset($contact_data["contact_id"]) && strlen($contact_data["contact_id"])) 
    {
        if(!get_contacts($user_id, ["id" => $contact_data["contact_id"]])->num_rows) 
        {
            send_response(400, "Invalid field the user with id: ".$user_id. " does not have a contact with id: " . $contact_data["contact_id"]);
        }

        $response = update_contact($contact_data);
        $status_code = 200;
        $msg = "Contact updated successfully";
    }

    else 
    {
        send_response(400, "Missing required field: contact_id");
    }
}


if($response != null)
{
    send_response($status_code, $msg);
}

send_response(500, "Ups! Something went wrong");



?>