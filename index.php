<?php
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$response = [
    "status" => "success",
    "method" => $method,
    "message" => "You made a $method request"
];

echo json_encode($response);
?>
