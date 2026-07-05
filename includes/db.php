<?php
// ============================================
// DB Connection Config
// Local testing uses SQLite if database.sqlite is present.
// Otherwise, it falls back to MySQL.
// ============================================
$sqlite_path = __DIR__ . '/../database.sqlite';
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    // Connect to PostgreSQL (Supabase / Render Database)
    $url_parsed = parse_url($db_url);
    $db_host = $url_parsed['host'] ?? '';
    $db_port = $url_parsed['port'] ?? 5432;
    $db_user = $url_parsed['user'] ?? '';
    $db_pass = $url_parsed['pass'] ?? '';
    $db_name = ltrim($url_parsed['path'] ?? '', '/');

    try {
        $pdo = new PDO(
            "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("PostgreSQL connection failed: " . $e->getMessage());
    }
} else if (file_exists($sqlite_path)) {
    try {
        $pdo = new PDO(
            "sqlite:" . $sqlite_path,
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $pdo->exec("PRAGMA foreign_keys = ON;");
    } catch (PDOException $e) {
        die("SQLite connection failed: " . $e->getMessage());
    }
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'your_database_name');
    define('DB_USER', 'your_database_user');
    define('DB_PASS', 'your_database_password');

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// site settings ekbar load kore rakhi, sob page e reuse
function getSettings($pdo) {
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function img_url($filename) {
    if (empty($filename)) {
        return 'uploads/default-share.jpg';
    }
    if (strpos($filename, 'http://') === 0 || strpos($filename, 'https://') === 0) {
        return $filename;
    }
    return 'uploads/' . $filename;
}
