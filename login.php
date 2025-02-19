<?php
// login.php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

// Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
// This will store the data into an associative array
$json_obj = json_decode($json_str, true);

// Variables can be accessed as such:
$username = $json_obj['username'];
$password = $json_obj['password'];
// This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

require 'database.php';

// Prepare and execute the query
$stmt = $mysqli->prepare("SELECT username, password, id FROM Users WHERE username = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query Preparation Failed"
    ));
    exit;
}

$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->bind_result($db_username, $hashed_password, $user_id);
$stmt->fetch();
$stmt->close();

// Verify the password
if (password_verify($password, $hashed_password)) {
    ini_set("session.cookie_httponly", 1);
    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $db_username;
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); 

    echo json_encode(array(
        "success" => true,
        "username" => $username,
        "csrfToken" => $_SESSION['token']
    ));
    exit;
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Incorrect Username or Password"
    ));
    exit;
}
?>