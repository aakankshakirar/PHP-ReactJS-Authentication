<?php
// uploadProfilePicture.php

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

    // Establish database connection
    $conn = connectDB();

    // Check if a file was uploaded
    if (isset($_FILES['profilePicture'])) {
        $file = $_FILES['profilePicture'];

        // File details
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        // File extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed file extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png');

        // Check if the uploaded file has an allowed extension
        if (in_array($fileExt, $allowedExtensions)) {
            // Generate a unique file name
            $fileNameNew = uniqid('', true) . '.' . $fileExt;

            // File destination
            $fileDestination = 'uploads/' . $fileNameNew;

            // Move the uploaded file to the destination
            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                // Update the user's profile picture path in the database using prepared statement
                $userId = $_POST['userId'];
                $stmt = $conn->prepare("UPDATE users SET profile_picture=? WHERE id=?");
                $stmt->bind_param("si", $fileDestination, $userId);
                if ($stmt->execute()) {
                    echo json_encode(array("success" => true, "message" => "Profile picture uploaded successfully"));
                } else {
                    echo json_encode(array("success" => false, "message" => "Error uploading profile picture"));
                }
                $stmt->close();
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to move uploaded file"));
            }
        } else {
            echo json_encode(array("success" => false, "message" => "Invalid file extension"));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "No file uploaded"));
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle other HTTP methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
}
?>
