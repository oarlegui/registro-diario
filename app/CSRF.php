<?php
/**
 * CSRF Protection Class
 * 
 * Provides Cross-Site Request Forgery protection for forms
 */

class CSRF {
    /**
     * Generate a CSRF token
     * 
     * @return string The generated token
     */
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Get the current CSRF token
     * 
     * @return string|null The current token or null if not set
     */
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['csrf_token'] ?? null;
    }
    
    /**
     * Validate a CSRF token
     * 
     * @param string $token The token to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if token exists in session
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        // Token expires after 1 hour
        if (time() - $_SESSION['csrf_token_time'] > 3600) {
            return false;
        }
        
        // Compare tokens using timing-safe comparison
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get HTML input field for CSRF token
     * 
     * @return string HTML input field
     */
    public static function getTokenField() {
        $token = self::getToken();
        if ($token === null) {
            $token = self::generateToken();
        }
        
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
