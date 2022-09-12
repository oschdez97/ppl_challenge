<?php

include 'config.php';
require_once 'utils.php';

$create_user_table = "CREATE TABLE IF NOT EXISTS user (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    lastname VARCHAR(255),
    phone VARCHAR(25) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX(phone))";


$create_contact_table = "CREATE TABLE IF NOT EXISTS contact (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    contact_name VARCHAR(30) NOT NULL,
    contact_phone_number VARCHAR(25) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX(user_id),
    INDEX(contact_name),
    INDEX(contact_phone_number))";


function get_users() 
{
    global $mysqli;

    $query = "SELECT * FROM user";
    return $mysqli->query($query);
}

function get_user_by_id($user_id) 
{
    global $mysqli;

    $query = "SELECT * FROM user WHERE id=".$user_id;
    return $mysqli->query($query);
}

function get_user_by_phone($phone) 
{
    global $mysqli;

    $query = "SELECT * FROM user WHERE phone=".$phone;
    return $mysqli->query($query);
}


function insert_user($user) 
{
    global $mysqli;

    $query = sprintf("INSERT INTO user (name, lastname, phone, created_at) VALUES ('%s', '%s', '%s', now())", 
        $user["name"], $user["lastname"], $user["phone"]);

    if(!$mysqli->query($query)) 
    {
        return null;
    }
    return $mysqli->insert_id;
}


function insert_contact($contact) 
{
    global $mysqli;

    $query = sprintf("INSERT INTO contact (contact_name, contact_phone_number, user_id, created_at) VALUES ('%s', '%s', '%s', now())", 
        $contact["contact_name"], $contact["contact_phone_number"], $contact["user_id"]);

    if(!$mysqli->query($query))
    {
        return null;
    }
    return $mysqli->insert_id;
}

function get_contacts($user_id, $params=null) 
{
    global $mysqli;

    $query = sprintf("SELECT * FROM contact WHERE user_id='%s' ", $user_id);
    $filters = "";

    if(isset($params["id"])) 
    {
        $filters .= "AND id='".$params["id"]."'";
    }

    if(isset($params["name"])) 
    {
        $filters .= "AND contact_name='".$params["name"]."'";
    }

    if(isset($params["phone"])) 
    {
        $filters .= "AND contact_phone_number='".$params["phone"]."'";
    }
    $query .= $filters;
    return $mysqli->query($query);   
}

function update_contact($contact) 
{
    global $mysqli;

    $query = sprintf("UPDATE contact SET contact_name='%s', contact_phone_number='%s' WHERE id='%s'", 
        $contact["contact_name"], $contact["contact_phone_number"], $contact["contact_id"]);

    if(!$mysqli->query($query))
    {
        return null;
    }
    return 1;
}

function get_common_contacts($user_1, $user_2) 
{
    global $mysqli;

    $query = sprintf("SELECT * FROM contact WHERE user_id=%s AND contact_phone_number 
        IN (SELECT contact_phone_number FROM contact WHERE user_id=%s)", $user_1, $user_2);

    return $mysqli->query($query);
}


?>