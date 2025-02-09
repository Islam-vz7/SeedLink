<?php
// Include configuration
require 'config.php';

// Get sensor data from fetch_data.php
$sensor_data = file_get_contents('http://localhost/SeedLink/fetch_data.php');
$sensor_data = json_decode($sensor_data, true);

// Prepare data for the Flask API
$data = [
    "light" => $sensor_data["light"],
    "humidity" => $sensor_data["humidity"],
    "temperature" => $sensor_data["temperature"],
    "moisture" => $sensor_data["moisture"]
];

// Call the Flask API
$options = [
    "http" => [
        "header" => "Content-type: application/json\r\n",
        "method" => "POST",
        "content" => json_encode($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents(API_URL, false, $context);

if ($response === FALSE) {
    die("Error calling the API.");
}

// Decode the API response
$result = json_decode($response, true);

// Return the recommended plant or message
echo json_encode(["recommended_plant" => $result["recommended_plant"]]);
?>