<?php
// Database credentials
$host = 'dpg-d0ti432dbo4c739nguq0-a';
$port = '5432';
$dbname = 'api_database_n9x1';
$user = 'api_database_n9x1_user';
$password = 'ZY2yoYoXypv0HYqJ6zwPxUcQhEBtQYT8';

// Get parameters from URL
$username = $_GET['username'] ?? null;
$password_input = $_GET['password'] ?? null;

// Set response header
header('Content-Type: application/json');

// Check for required parameters
if (!$username || !$password_input) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing username or password in the URL parameters.'
    ]);
    exit;
}

try {
    // Connect to PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create 'users' table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Insert user data
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password_input // ⚠️ Optional: Use password_hash() for security
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'User data inserted successfully.',
        'data' => [
            'username' => $username
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
