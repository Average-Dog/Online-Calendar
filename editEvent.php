<?php
header("Content-Type: application/json");
require 'database.php';

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$event_id = $json_obj['eid'];
$title = $json_obj['title'];
$date = $json_obj['date'];
$time = $json_obj['time'];
$token = $json_obj['token'];

$stmt = $mysqli->prepare("UPDATE Events SET title = ?, date = ?, time = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: " . $mysqli->error
    ));
    exit;
}

$stmt->bind_param('sssi', $title, $date, $time, $event_id);
session_start();
if($token != $_SESSION['token']){
    echo json_encode(array(
        "success" => false,
        "message" => "Token not working: " . $stmt->error
    ));
}

if ($stmt->execute()) {
    echo json_encode(array(
        "success" => true

    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Edit Failed: " . $stmt->error
    ));
}

$stmt->close();
?>
