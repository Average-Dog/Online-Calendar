<?php
session_start();
require "database.php";

header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$year = isset($_GET['year']) ? $_GET['year'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;
$tag = isset($_GET['tag']) ? $_GET['tag'] : null;

if (!$year || !$month) {
    echo json_encode(["success" => false, "message" => "Year and month required"]);
    exit;
}

// Prepare and execute query to fetch events
$stmt = $mysqli->prepare("SELECT title, date, time, tag FROM Events WHERE user_id = ? AND YEAR(date) = ? AND MONTH(date) = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error: " . $mysqli->error]);
    exit;
}

$stmt->bind_param("iii", $user_id, $year, $month, $tag);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode(["success" => true, "events" => $events]);
$stmt->close();
?>
