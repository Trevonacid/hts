-- Add admin column to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;

-- Note: To create the admin user, run create_admin.php in your browser
-- Or manually create an admin user with is_admin = 1
-- Default credentials will be:
-- Email: admin@admin.com
-- Password: Admin@123
-- Please change this after first login!
