<?php

require_once 'sql_query.php';
require_once 'utils.php';

$response = get_users();

$users = [];
while ($row = $response->fetch_assoc()) {
    $users[] = $row;
}
send_response(200, "OK", $users);


?>