<?php
/**
 * Database Configuration
 * 
 * This file contains database connection settings.
 * Update these values with your cPanel MySQL credentials.
 */

// Database configuration - UPDATE THESE VALUES FOR YOUR cPANEL ENVIRONMENT
define('DB_HOST', 'localhost');
define('DB_NAME', 'registro_diario');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Check if using default credentials
if (DB_USER === 'root' && DB_PASS === '' && $_SERVER['SERVER_NAME'] !== 'localhost') {
    error_log("WARNING: Using default database credentials in production!");
    die("Database not configured. Please update config/database.php with your cPanel credentials.");
}

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
