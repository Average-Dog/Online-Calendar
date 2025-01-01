<?php
session_start();

header('Content-Type: application/json');

$response = array();

if (isset($_SESSION['user_id'])) {
    $response['user_id'] = $_SESSION['user_id'];
    $response['username'] = $_SESSION['username'];
} else {
    $response['error'] = 'No user ID found in session.';
}

echo json_encode($response);
?>