<?php
// Allow CORS for browser requests
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Database credentials
$host = 'dpg-d0ti432dbo4c739nguq0-a';
$port = '5432';
$dbname = 'api_database_n9x1';
$user = 'api_database_n9x1_user';
$password = 'ZY2yoYoXypv0HYqJ6zwPxUcQhEBtQYT8';

// Get 'mode' from URL
$mode = $_GET['mode'] ?? 'insert';

// Return predefined items if mode is 'insert'
if ($mode === 'insert') {
    echo json_encode([
        "Enforcer",
        "Gatherers",
        "Researchers",
        "Manufacturers",
        "Generalist"
    ]);
    exit;
}

// Handle other modes (if needed)
echo json_encode(["error" => "Invalid mode."]);
exit;
?>
