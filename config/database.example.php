<?php
/**
 * Database Configuration Example
 * 
 * Copy this file to config/database.php and update with your cPanel credentials
 */

// Database configuration - UPDATE THESE VALUES FOR YOUR cPANEL ENVIRONMENT
define('DB_HOST', 'localhost');              // Usually 'localhost' in cPanel
define('DB_NAME', 'your_database_name');     // Your database name
define('DB_USER', 'your_mysql_username');    // Your MySQL username
define('DB_PASS', 'your_mysql_password');    // Your MySQL password
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * 
 * @return PDO Database connection object
 * @throws PDOException If connection fails
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new PDOException("Could not connect to database. Please check your configuration.");
        }
    }
    
    return $pdo;
}
