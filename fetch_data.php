<?php
// Include configuration
require 'config.php';

// Initialize variables
$light = $humidity = $temperature = $moisture = "N/A";

// Create database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest sensor data
$sql = "SELECT light, humidity, temperature, moisture FROM data ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $light = $row['light'];
    $humidity = $row['humidity'];
    $temperature = $row['temperature'];
    $moisture = $row['moisture'];
} else {
    echo "No sensor data found.";
}

// Close the connection
$conn->close();

// Return sensor data as JSON
echo json_encode([
    "light" => $light,
    "humidity" => $humidity,
    "temperature" => $temperature,
    "moisture" => $moisture
]);
?>