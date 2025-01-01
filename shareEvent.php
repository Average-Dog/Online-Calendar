<?php
header("Content-Type: application/json");
require 'database.php';

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$user_ids = $json_obj['shareWith'];
$title = $json_obj['title'];
$date = $json_obj['globalSelectedDate'];
$time = $json_obj['time'];

if(!is_array($user_ids)) {
    echo json_encode(array(
        "success" => false,
        "message" => "array No user IDs provided."
    ));
    exit;
}

if(empty($user_ids)) {
    echo json_encode(array(
        "success" => false,
        "message" => "emptyNo user IDs provided."
    ));
    exit;
}

foreach($user_ids as $user_id) {
    $stmt = $mysqli->prepare("INSERT INTO Events (user_id, title, date, time) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(array(
            "success" => false,
            "message" => "Query Prep Failed: " . $mysqli->error
        ));
        exit;
    }

    $stmt->bind_param('isss', $user_id, $title, $date, $time);

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
}
?>
