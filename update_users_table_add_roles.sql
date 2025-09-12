-- SQL query to add role column to existing users table
-- Run this in phpMyAdmin or MySQL command line

-- Add role column to users table
ALTER TABLE `users` 
ADD COLUMN `role` ENUM('admin', 'customer', 'employee') NOT NULL DEFAULT 'customer' AFTER `profile_picture`;

-- Optional: Create an index on the role column for better performance
ALTER TABLE `users` 
ADD INDEX `idx_role` (`role`);

-- Optional: Insert a default admin user (change email and password as needed)
-- Password is 'admin123' - you should change this
INSERT INTO `users` (username, email, password, role, created_at) 
VALUES ('admin', 'admin@coconutshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW())
ON DUPLICATE KEY UPDATE role = 'admin';
