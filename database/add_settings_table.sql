-- Add settings table to store system configuration
USE flash_oversight;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) 
VALUES 
    ('site_name', 'Flash.Q'),
    ('admin_email', 'admin@flashq.com')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value);
