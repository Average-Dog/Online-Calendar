<?php
// Database connection file
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'M5');

if($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
}
?>
