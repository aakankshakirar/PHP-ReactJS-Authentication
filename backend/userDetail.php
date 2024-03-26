<?php

// Allow requests from the frontend origin (http://localhost:5173)
header("Access-Control-Allow-Origin: http://localhost:5173");

// Allow the following methods from the frontend
header("Access-Control-Allow-Methods: POST");

// Allow the following headers from the frontend
header("Access-Control-Allow-Headers: Content-Type");

// Check if it's a preflight request (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with a 200 OK status
    http_response_code(200);
    exit;
}

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Include the database configuration
    require_once 'config.php';

    // Establish database connection
    $conn = connectDB();

    // Get user ID from the request parameters
    $userId = $_GET['id'];

    // Validate user ID
    if (empty($userId)) {
        echo json_encode(array("success" => false, "message" => "User ID not found"));
        $conn->close();
        exit;
    }

    // Prepare and execute the SQL query using prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();
        // Respond with success message and user data
        echo json_encode(array("success" => true, "data" => $row, "message" => "User data retrieved successfully"));
    } else {
        // Respond with error message if user not found
        http_response_code(404);
        echo json_encode(array("success" => false, "message" => "User not found"));
    }

    // Close prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle other HTTP methods
    http_response_code(405); // Method Not Allowed
    echo json_encode("Method Not Allowed");
}

?>
