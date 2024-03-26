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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';

    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'];
    $password = $data['password'];

    $conn = connectDB();
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        http_response_code(200);
        echo json_encode(array("success" => true, "userId" => $userId, "message" => "Login successful"));
    } else {
        http_response_code(401);
        echo json_encode(array("success" => false, "message" => "Incorrect username or password"));
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode("Method Not Allowed");
}

?>
