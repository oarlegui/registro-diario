<?php
/**
 * Authentication Class
 * 
 * Handles user authentication and session management
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    /**
     * Start session if not already started
     */
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Authenticate user with email and password
     * 
     * @param string $email User email
     * @param string $password User password
     * @return bool True if authentication successful, false otherwise
     */
    public static function login($email, $password) {
        self::startSession();
        
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Store user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log out the current user
     */
    public static function logout() {
        self::startSession();
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if logged in, false otherwise
     */
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null User ID or null if not logged in
     */
    public static function getUserId() {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user email
     * 
     * @return string|null User email or null if not logged in
     */
    public static function getUserEmail() {
        self::startSession();
        return $_SESSION['user_email'] ?? null;
    }
    
    /**
     * Get current user role
     * 
     * @return string|null User role or null if not logged in
     */
    public static function getUserRole() {
        self::startSession();
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Require authentication - redirect to login if not logged in
     * 
     * @param string $redirectUrl URL to redirect to if not logged in
     */
    public static function requireLogin($redirectUrl = '/public/login.php') {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}
