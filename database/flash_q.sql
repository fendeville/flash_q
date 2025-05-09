-- Database: flash_q

CREATE DATABASE IF NOT EXISTS flash_oversight;

USE flash_oversight;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone_number VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Organizations/Companies table
CREATE TABLE IF NOT EXISTS organizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Queues table
CREATE TABLE IF NOT EXISTS queues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    start_time TIME,
    end_time TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- Queue Tokens table
CREATE TABLE IF NOT EXISTS queue_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_id INT NOT NULL,
    user_id INT NOT NULL,
    token_number INT NOT NULL,
    status ENUM('waiting', 'serving', 'completed', 'cancelled') DEFAULT 'waiting',
    join_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    serving_time TIMESTAMP NULL,
    completion_time TIMESTAMP NULL,
    FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (token_id) REFERENCES queue_tokens(id) ON DELETE CASCADE
);

-- Statistics table
CREATE TABLE IF NOT EXISTS statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_id INT NOT NULL,
    date DATE NOT NULL,
    total_served INT DEFAULT 0,
    average_wait_time INT DEFAULT 0, -- in seconds
    FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (full_name, email, phone_number, password, is_admin) 
VALUES ('Elonge Neville', 'admin@flashq.com', '+237651990298', '$2y$10$LGl73BHmDFZT9lrXoLZ0JuFgY2h/dCIFgS.fJSZXbY.LS8CbVw.Ly', 1);

-- Insert sample organizations
INSERT INTO organizations (name, category, description) 
VALUES 
('City Hospital', 'Healthcare', 'Main city hospital services'),
('First National Bank', 'Banking', 'Banking services'),
('Electric Utility', 'Utility', 'Electric bill payment service'),
('Water Corporation', 'Utility', 'Water bill payment and services');

-- Insert sample queues
INSERT INTO queues (organization_id, name) 
VALUES 
(1, 'General Consultation'),
(1, 'Pharmacy'),
(2, 'Account Services'),
(3, 'Bill Payment'),
(4, 'Customer Service');

-- Insert MTN Office into the organizations table
INSERT INTO organizations (name, category, description)
VALUES ('MTN Office', 'Telecommunication', 'A mobile telecommunication service in Kumber Cameroon');

-- Get the ID of the newly added organization
SET @org_id = (SELECT id FROM organizations WHERE name = 'MTN Office');

-- Insert queues for MTN Office
INSERT INTO queues (name, organization_id, is_active)
VALUES 
('SIM Registration', @org_id, 1),
('Bill Payments', @org_id, 1),
('Customer Support', @org_id, 1),
('Technical Assistance', @org_id, 1);

-- Create admin user with specified credentials
CREATE USER IF NOT EXISTS 'Elonge_neville'@'localhost' IDENTIFIED BY '741074';
GRANT ALL PRIVILEGES ON flash_oversight.* TO 'Elonge_neville'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;