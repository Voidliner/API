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

    if ($mode === 'insert') {
        // Insert user
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
            ':password' => $password_input // ⚠️ Storing as plain text (consider hashing in real apps)
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'User data inserted successfully.',
            'data' => ['username' => $username]
        ]);

    } elseif ($mode === 'get') {
        // Fetch all users
        $stmt = $pdo->query("SELECT id, username, password, created_at FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'message' => 'Retrieved user data.',
            'count' => count($users),
            'data' => $users
        ]);

    } elseif ($mode === 'delete_all') {
        // Delete all users
        $pdo->exec("DELETE FROM users");

        echo json_encode([
            'status' => 'success',
            'message' => 'All users have been deleted.'
        ]);

    } elseif ($mode === 'drop_all_tables') {
        // ⚠️ Danger: Drop all tables (requires secret)
        $secret = $_GET['secret'] ?? '';
        if ($secret !== 'my_super_secret_key') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing secret key.'
            ]);
            exit;
        }

        // Get all public tables
        $tablesStmt = $pdo->query("
            SELECT tablename FROM pg_tables WHERE schemaname = 'public'
        ");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS \"$table\" CASCADE");
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'All tables have been dropped from the database.'
        ]);

    } else {
        // Invalid mode
        echo json_encode([
            'status' => 'error',
            'message' => "Invalid mode: '$mode'. Use 'insert', 'get', 'delete_all', or 'drop_all_tables'."
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
