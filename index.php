<?php
// Set response header
header('Content-Type: application/json');

// Determine request method
$method = $_SERVER['REQUEST_METHOD'];

// Prepare response
$response = [
    "status" => "success",
    "method" => $method,
    "message" => "You made a $method request"
];

// Output JSON
echo json_encode($response);
?>
