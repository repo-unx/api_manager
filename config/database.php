<?php
/**
 * Database Configuration
 * Handles connection to either MySQL or PostgreSQL based on environment settings
 */

require_once __DIR__ . '/constants.php';

// Load environment variables from .env file if they exist
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse line for variable assignment
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Handle variable substitution (e.g., ${VAR})
            if (preg_match('/\${([A-Za-z0-9_]+)}/', $value, $matches)) {
                $envVar = getenv($matches[1]);
                if ($envVar !== false) {
                    $value = str_replace($matches[0], $envVar, $value);
                }
            }
            
            // Set environment variable if not already set
            if (getenv($name) === false) {
                putenv("$name=$value");
            }
        }
    }
}

// Get database configuration from environment
$dbDriver = getenv('DB_DRIVER') ?: 'mysql'; // Default to MySQL if not specified
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = getenv('DB_PORT') ?: ($dbDriver == 'mysql' ? '3306' : '5432');
$dbName = getenv('DB_NAME') ?: 'api_manager';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';

// For PostgreSQL, also check for PG-specific environment variables
if ($dbDriver == 'pgsql') {
    $pgHost = getenv('PGHOST');
    $pgPort = getenv('PGPORT');
    $pgDatabase = getenv('PGDATABASE');
    $pgUser = getenv('PGUSER');
    $pgPassword = getenv('PGPASSWORD');
    
    if ($pgHost) $dbHost = $pgHost;
    if ($pgPort) $dbPort = $pgPort;
    if ($pgDatabase) $dbName = $pgDatabase;
    if ($pgUser) $dbUser = $pgUser;
    if ($pgPassword) $dbPass = $pgPassword;
    
    // Check for DATABASE_URL format (used by some hosting providers)
    $databaseUrl = getenv('DATABASE_URL');
    if ($databaseUrl) {
        $dbComponents = parse_url($databaseUrl);
        if (isset($dbComponents['host'])) $dbHost = $dbComponents['host'];
        if (isset($dbComponents['port'])) $dbPort = $dbComponents['port'];
        if (isset($dbComponents['path'])) $dbName = ltrim($dbComponents['path'], '/');
        if (isset($dbComponents['user'])) $dbUser = $dbComponents['user'];
        if (isset($dbComponents['pass'])) $dbPass = $dbComponents['pass'];
    }
}

// DSN format for each supported driver
$dsn = match($dbDriver) {
    'mysql' => "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4",
    'pgsql' => "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName",
    default => throw new Exception("Unsupported database driver: $dbDriver")
};

// Database connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Database connection function
function getDbConnection() {
    global $dsn, $dbUser, $dbPass, $options;
    
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log error and display user-friendly message
        error_log('Database Connection Error: ' . $e->getMessage());
        die('Database connection failed. Please check configuration or contact administrator.');
    }
}
