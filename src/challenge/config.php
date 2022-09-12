<?php


$host = "mysql";
$user = "root";
$password = "123456"; 
$db_name = "dev";

$NEUTRINO_USER_ID = "Hellscream97";
$NEUTRINO_API_KEY = "BfGyIr5ppb9PpbUB0gT1mYr0JTYIw6TuujJkpNJncdLehIEM";

$mysqli = new mysqli($host, $user, $password, $db_name, 3306);

if ($mysqli->connect_errno) {
    printf("Falló la conexión: %s\n", $mysqli->connect_error);
    exit();
}

?>