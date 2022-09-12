<?php

require_once 'sql_query.php';
require_once 'utils.php';

if(isset($_GET["user1"]) && isset($_GET["user2"])) 
{
    $common_contacts = [];
    $response = get_common_contacts($_GET["user1"], $_GET["user2"]);
    
    while($row = $response->fetch_assoc()) 
    {
        $common_contacts[] = $row;
    }
    send_response(200, "OK", $common_contacts);
}


?>