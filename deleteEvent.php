<?php
header("Content-Type: application/json");
require 'database.php';

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$event_id = $json_obj['id'];

$token = $json_obj['token'];
session_start();
if($token != $_SESSION['token']){
    echo json_encode(array(
        "success" => false,
        "message" => "Token not working: " . $stmt->error
    ));
}


$stmt = $mysqli->prepare("DELETE FROM Events WHERE id = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: " . $mysqli->error
    ));
    exit;
}

$stmt->bind_param('i', $event_id);

if ($stmt->execute()) {
    echo json_encode(array(
        "success" => true,
        "csrfToken" => $_SESSION['token']
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Execute Failed: " . $stmt->error
    ));
}

$stmt->close();
?>
