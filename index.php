<?php
// Allow CORS for browser requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database credentials
$host = 'dpg-d0ti432dbo4c739nguq0-a';
$port = '5432';
$dbname = 'api_database_n9x1';
$user = 'api_database_n9x1_user';
$password = 'ZY2yoYoXypv0HYqJ6zwPxUcQhEBtQYT8';

// Get 'mode' from URL
$mode = $_GET['mode'] ?? 'insert';

try {
    // Connect to database
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if ($mode === 'insert') {
        // Prepare your job list (could be from DB or static)
        $jobs = [
            "Enforcer",
            "Gatherers",
            "Researchers",
            "Manufacturers",
            "Generalist"
        ];

        // Full confirmation response:
        $response = [
            "status" => "success",
            "message" => "Database connected and job list retrieved successfully.",
            "jobs" => $jobs
        ];

        echo json_encode($response);
        exit;
    }

    // If mode is invalid
    echo json_encode([
        "status" => "error",
        "message" => "Invalid mode."
    ]);
    exit;

} catch (PDOException $e) {
    // On DB connection failure, return error message
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}
