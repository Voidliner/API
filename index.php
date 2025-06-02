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

    // Recreate users table with new ID system
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            custom_id VARCHAR(20) UNIQUE NOT NULL,
            username VARCHAR(255) NOT NULL,
            password TEXT NOT NULL
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

        // Generate timestamp-based custom ID (with random suffix to prevent collisions)
        $custom_id = date('YmdHis') . rand(10, 99);

        $stmt = $pdo->prepare("
            INSERT INTO users (custom_id, username, password)
            VALUES (:custom_id, :username, :password)
        ");
        $stmt->execute([
            ':custom_id' => $custom_id,
            ':username' => $username,
            ':password' => $password_input
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'User data inserted successfully.',
            'data' => [
                'custom_id' => $custom_id,
                'username' => $username
            ]
        ]);

    } elseif ($mode === 'get') {
        // Fetch all users
        $stmt = $pdo->query("SELECT id, custom_id, username, password FROM users ORDER BY id ASC");
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
