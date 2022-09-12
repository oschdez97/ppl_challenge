<?php

date_default_timezone_set('Europe/Madrid');

function get_date_time() 
{
    return date('Y-m-d H:i:s');
}

function validate_phone($phone) 
{
    global $NEUTRINO_USER_ID, $NEUTRINO_API_KEY;

    $data = [
        "user-id" => $NEUTRINO_USER_ID,
        "api-key" => $NEUTRINO_API_KEY,
        "number" => $phone
    ];
    $response = post("https://neutrinoapi.net/phone-validate", $data); 
    return json_decode($response, true);
}

function send_response($status_code, $message, $body=null) 
{
    header('Content-type: application/json');
    http_response_code($status_code);
    
    $response = [
        "status_code" => $status_code,
        "message" => $message, 
        "datetime" => get_date_time(),
    ];
    
    if(!is_null($body))
    {
        $response["body"] = $body;
    }
    echo json_encode($response);
    exit();
}


function get($url) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}

function post($url, $data) 
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Accept: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

function put($url, $data) 
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json','Accept: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}


?>