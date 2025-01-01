<?php
header("Content-Type: application/json");
require 'database.php';

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$user_id = $json_obj['globalUserId'];
$title = $json_obj['title'];
$date = $json_obj['globalSelectedDate'];
$time = $json_obj['time'];
$tag = $json_obj['tag'];
$token = $json_obj['token'];

session_start();
if($token != $_SESSION['token']){
    echo json_encode(array(
        "success" => false,
        "message" => "Token not working: " . $stmt->error
    ));
}


$stmt = $mysqli->prepare("INSERT INTO Events (user_id, title, date, time, tag) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query Prep Failed: " . $mysqli->error
    ));
    exit;
}

$stmt->bind_param('issss', $user_id, $title, $date, $time, $tag);

if ($stmt->execute()) {
    echo json_encode(array(
        "success" => true
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Execute Failed: " . $stmt->error
    ));
}

$stmt->close();
?>
