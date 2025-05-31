<?php
// Set the response content type to JSON
header('Content-Type: application/json');

// Sample data to return
$data = [
    "status" => "success",
    "message" => "Hello from the PHP API!",
    "timestamp" => date("Y-m-d H:i:s"),
    "data" => [
        "name" => "ChatGPT",
        "version" => "1.0",
        "features" => ["json response", "easy integration", "fast"]
    ]
];

// Encode data as JSON and output it
echo json_encode($data);
?>
