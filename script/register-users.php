<?php

// API endpoint URL
$url = "nginx/api/v1/register";

// Path to JSON file
$jsonFile = __DIR__ . "/register-data.json";

// Check if the JSON file exists
if (!file_exists($jsonFile)) {
    die("JSON file not found: $jsonFile\n");
}

// Read and decode JSON file
$jsonData = file_get_contents($jsonFile);
$dataArray = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg() . "\n");
}

// Send each dataset to the API endpoint
foreach ($dataArray as $item) {
    $jsonItem = json_encode($item);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonItem);

    // Execute cURL request
    $response = curl_exec($ch);

    if ($response === false) {
        echo "cURL Error: " . curl_error($ch) . "\n";
    } else {
        echo "Response: " . $response . "\n";
    }

    // Close cURL session
    curl_close($ch);
}
