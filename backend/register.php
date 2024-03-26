<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Check if it's a preflight request (OPTIONS method): Need for React
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond with a 200 OK status
    http_response_code(200);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database configuration
    require_once 'config.php';

    // Extract registration data from the POST request
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];
    $email = $data['email'];

    // Establish database connection
    $conn = connectDB();

    // Prepare and execute the SQL query using prepared statements
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $email);

    $response = array(); // Initialize response array

    if ($stmt->execute()) {
        // Registration successful
        $response['success'] = true;
        $response['message'] = "User registered successfully";
    } else {
        // Registration failed
        $response['success'] = false;
        $response['message'] = "Error registering user: " . $stmt->error;
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();

    // Send JSON response
    echo json_encode($response);
} else {
    // Handle other HTTP methods
    http_response_code(405); // Method Not Allowed
    echo json_encode("Method Not Allowed");
}
?>
