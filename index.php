<?php
// Database credentials
$host = 'dpg-d0ti432dbo4c739nguq0-a';
$port = '5432';
$dbname = 'api_database_n9x1';
$user = 'api_database_n9x1_user';
$password = 'ZY2yoYoXypv0HYqJ6zwPxUcQhEBtQYT8';

// Get parameters from the URL
$username = $_GET['username'] ?? null;
$password_input = $_GET['password'] ?? null;

// Validate parameters
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
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Insert data into 'users' table
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $password_input  // ⚠️ Plain text - consider hashing!
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
