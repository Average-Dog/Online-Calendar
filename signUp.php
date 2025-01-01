<?php
header("Content-Type: application/json");
require 'database.php';

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$username = trim($json_obj['username']);
$password = trim($json_obj['password']);

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert new user
$stmt = $mysqli->prepare("INSERT INTO Users (username, password) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "SQL Error: " . $mysqli->error]);
    exit;
}

$stmt->bind_param('ss', $username, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User registered successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Username already taken"]);
}
$stmt->close();
?>
