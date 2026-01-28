<?php
/**
 * Record Model
 * 
 * Handles database operations for visitor records
 */

require_once __DIR__ . '/../config/database.php';

class Record {
    /**
     * Create a new visitor record
     * 
     * @param array $data Record data
     * @return int|false The new record ID or false on failure
     */
    public static function create($data) {
        try {
            $pdo = getDBConnection();
            
            $sql = "INSERT INTO records (
                date, name, rut, phone_number, classification, 
                company_name, transport_company_name, visit_reason, 
                entry_time, license_plate, invoice_or_guide_number
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['date'],
                $data['name'],
                $data['rut'],
                $data['phone_number'],
                $data['classification'],
                $data['company_name'],
                $data['transport_company_name'],
                $data['visit_reason'],
                $data['entry_time'],
                $data['license_plate'],
                $data['invoice_or_guide_number']
            ]);
            
            return $result ? $pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Error creating record: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update exit time for a record
     * 
     * @param int $id Record ID
     * @param string $exitTime Exit time
     * @return bool True on success, false on failure
     */
    public static function updateExitTime($id, $exitTime) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE records SET exit_time = ? WHERE id = ?");
            return $stmt->execute([$exitTime, $id]);
        } catch (PDOException $e) {
            error_log("Error updating exit time: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get pending records (without exit time)
     * 
     * @param string $date Optional date filter
     * @return array Array of pending records
     */
    public static function getPending($date = null) {
        try {
            $pdo = getDBConnection();
            
            if ($date) {
                $stmt = $pdo->prepare("
                    SELECT * FROM records 
                    WHERE exit_time IS NULL AND date = ? 
                    ORDER BY entry_time DESC
                ");
                $stmt->execute([$date]);
            } else {
                $stmt = $pdo->query("
                    SELECT * FROM records 
                    WHERE exit_time IS NULL 
                    ORDER BY date DESC, entry_time DESC
                ");
            }
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting pending records: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all records for a specific date
     * 
     * @param string $date Date to filter by
     * @return array Array of records
     */
    public static function getByDate($date) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT * FROM records 
                WHERE date = ? 
                ORDER BY entry_time DESC
            ");
            $stmt->execute([$date]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting records by date: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get autocomplete suggestions for names
     * 
     * @param string $query Search query
     * @param int $limit Maximum number of results
     * @return array Array of unique names
     */
    public static function getNameSuggestions($query, $limit = 10) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT DISTINCT name 
                FROM records 
                WHERE name LIKE ? 
                ORDER BY name 
                LIMIT ?
            ");
            $stmt->execute([$query . '%', $limit]);
            return array_column($stmt->fetchAll(), 'name');
        } catch (PDOException $e) {
            error_log("Error getting name suggestions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get autocomplete suggestions for company names
     * 
     * @param string $query Search query
     * @param int $limit Maximum number of results
     * @return array Array of unique company names
     */
    public static function getCompanySuggestions($query, $limit = 10) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("
                SELECT DISTINCT company_name 
                FROM records 
                WHERE company_name LIKE ? AND company_name IS NOT NULL
                ORDER BY company_name 
                LIMIT ?
            ");
            $stmt->execute([$query . '%', $limit]);
            return array_column($stmt->fetchAll(), 'company_name');
        } catch (PDOException $e) {
            error_log("Error getting company suggestions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Validate Chilean RUT format
     * 
     * @param string $rut RUT to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateRUT($rut) {
        // Remove dots and hyphens
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        
        if (strlen($rut) < 2) {
            return false;
        }
        
        // Extract verification digit
        $verifier = strtoupper(substr($rut, -1));
        $number = substr($rut, 0, -1);
        
        // Calculate verification digit
        $sum = 0;
        $multiplier = 2;
        
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += intval($number[$i]) * $multiplier;
            $multiplier = $multiplier < 7 ? $multiplier + 1 : 2;
        }
        
        $expectedVerifier = 11 - ($sum % 11);
        
        if ($expectedVerifier == 11) {
            $expectedVerifier = '0';
        } elseif ($expectedVerifier == 10) {
            $expectedVerifier = 'K';
        } else {
            $expectedVerifier = strval($expectedVerifier);
        }
        
        return $verifier === $expectedVerifier;
    }
}
