-- Registro-Diario Database Schema
-- Create database and tables for the daily visitor registration system

-- Create database (optional - may need to be done via cPanel)
-- CREATE DATABASE IF NOT EXISTS registro_diario;
-- USE registro_diario;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Records table for daily visitor logs
CREATE TABLE IF NOT EXISTS records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    name VARCHAR(255) NOT NULL,
    rut VARCHAR(20) NOT NULL,
    phone_number VARCHAR(20),
    classification ENUM('Cliente', 'Proveedor', 'Otro') NOT NULL,
    company_name VARCHAR(255),
    transport_company_name VARCHAR(255),
    visit_reason TEXT,
    entry_time TIME NOT NULL,
    exit_time TIME NULL,
    license_plate VARCHAR(20),
    invoice_or_guide_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_name (name),
    INDEX idx_company (company_name),
    INDEX idx_exit_time (exit_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Password hash for 'admin123' using PASSWORD_BCRYPT
INSERT INTO users (email, password, role) VALUES 
('admin@registro-diario.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
