<?php
session_start();
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

require 'database.php';

// Set error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "Please log in to view events."]);
        exit;
    }

    // Retrieve and sanitize user input
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);

    $user_id = isset($json_obj['user_id']) ? (int)$json_obj['user_id'] : null;
    $month = isset($json_obj['month']) ? (int)$json_obj['month'] : null;
    $year = isset($json_obj['year']) ? (int)$json_obj['year'] : null;

    // Check for missing values
    if (empty($user_id) || empty($month) || empty($year)) {
        echo json_encode([
            "success" => false,
            "message" => "User ID, month, and year are required."
        ]);
        exit;
    }

    // Prepare SQL statement with error checking
    $stmt = $mysqli->prepare("SELECT id, title, date, time, tag FROM Events WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error (prepare): " . $mysqli->error]);
        exit;
    }

    // Bind parameters and execute, with detailed error output
    $stmt->bind_param("iii", $user_id, $month, $year);
    $stmt->execute();
    $stmt->bind_result($id, $title, $date, $time, $tag);

    $events = [];
    while ($stmt->fetch()) {
        $events[] = ["eid"=> $id, "title" => $title, "date" => $date, "time" => $time, "tag" => $tag];
    }

    echo json_encode(["success" => true, "events" => $events]);
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "An unexpected error occurred.",
        "error" => $e->getMessage()
    ]);
}
?>