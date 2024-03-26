<?php

function connectDB() {
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "dbname";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

