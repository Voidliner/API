<?php
// Database credentials
$host = 'dpg-d0ti432dbo4c739nguq0-a';
$port = '5432';
$dbname = 'api_database_n9x1';
$user = 'api_database_n9x1_user';
$password = 'ZY2yoYoXypv0HYqJ6zwPxUcQhEBtQYT8';

// Set response header
header('Content-Type: application/json');

// Get 'mode' from URL
$mode = $_GET['mode'] ?? 'insert';

// Connect to PostgreSQL
try {
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

    if ($mode === 'insert') {
        // Insert mode
        $username = $_GET['username'] ?? null;
        $password_input = $_GET['password'] ?? null;

        if (!$username || !$password_input) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing username or password in the URL parameters.'
            ]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute([
            ':username' => $username,
            ':password' => $password_input // ⚠️ Store as plain text; not secure!
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'User data inserted successfully.',
            'data' => [
                'username' => $username
            ]
        ]);
    } elseif ($mode === 'get') {
        // Get mode: fetch all credentials
        $stmt = $pdo->query("SELECT id, username, password, created_at FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'message' => 'Retrieved user data.',
            'count' => count($users),
            'data' => $users
        ]);
    } else {
        // Unknown mode
        echo json_encode([
            'status' => 'error',
            'message' => "Invalid mode: '$mode'. Use 'insert' or 'get'."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
