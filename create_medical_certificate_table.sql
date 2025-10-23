-- Create medical_certificate_requests table for clinic management system

CREATE TABLE IF NOT EXISTS medical_certificate_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NULL,
    purpose VARCHAR(100) NOT NULL,
    notes TEXT NULL,
    contact_number VARCHAR(20) NOT NULL,
    reference_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by VARCHAR(50) NULL,
    notes_admin TEXT NULL,
    INDEX idx_student_id (student_id),
    INDEX idx_reference_number (reference_number),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing (optional)
-- INSERT INTO medical_certificate_requests (student_id, purpose, notes, contact_number, reference_number, status) VALUES
-- ('2021001', 'school_requirements', 'For enrollment', '09123456789', 'MC-20241201-1001', 'pending');
