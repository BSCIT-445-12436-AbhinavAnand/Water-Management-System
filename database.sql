-- Neer Nigrani Database Setup
CREATE DATABASE IF NOT EXISTS neer_nigrani;
USE neer_nigrani;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    area VARCHAR(200) NOT NULL,
    district VARCHAR(100) NOT NULL,
    issue_type ENUM('No Water','Dirty Water','Leakage','Low Pressure','Other') NOT NULL,
    description TEXT,
    photo VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending','In Progress','Resolved') DEFAULT 'Pending',
    admin_remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    district VARCHAR(100) NOT NULL,
    alert_type ENUM('Emergency','Warning','Info') DEFAULT 'Info',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE water_supply (
    id INT AUTO_INCREMENT PRIMARY KEY,
    district VARCHAR(100) NOT NULL,
    area VARCHAR(200) NOT NULL,
    morning_time VARCHAR(50),
    evening_time VARCHAR(50),
    status ENUM('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB;

-- Default Admin (Email: admin@neernigrani.com / Password: Admin@123)
INSERT INTO admins (name, email, password) VALUES 
('Super Admin', 'admin@neernigrani.com', '$2y$10$CB9kaLe2NHA0I5njzxlRse0cVlqXGoZEI/.MbAl6HOSi.f.P1LGa6');

-- Bihar Districts Water Supply Data
INSERT INTO water_supply (district, area, morning_time, evening_time) VALUES
('Patna', 'Kankarbagh', '6:00 AM - 8:00 AM', '5:00 PM - 7:00 PM'),
('Patna', 'Boring Road', '6:30 AM - 8:30 AM', '5:30 PM - 7:30 PM'),
('Gaya', 'Civil Lines', '7:00 AM - 9:00 AM', '4:00 PM - 6:00 PM'),
('Muzaffarpur', 'Mithanpura', '6:00 AM - 8:00 AM', '5:00 PM - 7:00 PM'),
('Bhagalpur', 'Adampur', '7:00 AM - 9:00 AM', '4:30 PM - 6:30 PM'),
('Darbhanga', 'Laheriasarai', '6:30 AM - 8:30 AM', '5:00 PM - 7:00 PM'),
('Purnia', 'Line Bazar', '7:00 AM - 9:00 AM', '4:00 PM - 6:00 PM'),
('Nalanda', 'Bihar Sharif', '6:00 AM - 8:00 AM', '5:30 PM - 7:30 PM'),
('Vaishali', 'Hajipur', '6:30 AM - 8:30 AM', '5:00 PM - 7:00 PM'),
('Samastipur', 'Town Area', '7:00 AM - 9:00 AM', '4:30 PM - 6:30 PM');

-- Sample Alert
INSERT INTO alerts (title, message, district, alert_type, is_active, created_by) VALUES
('पानी की आपूर्ति बाधित', 'Patna Kankarbagh area mein kal subah 6 AM se 12 PM tak pani ki supply band rahegi. Pipeline repair ka kaam chal raha hai.', 'Patna', 'Emergency', 1, 1);

-- OTP Table for Forgot Password
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    otp VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_used TINYINT(1) DEFAULT 0,
    INDEX idx_email_otp (email, otp)
) ENGINE=InnoDB;
