# Habit Tracker System

A modern, full-featured habit tracking web application built with PHP and MySQL. Track your daily habits, monitor progress with beautiful charts, and build better routines.

## Features

- ✅ User Registration & Authentication
- 📊 Habit Tracking with Statistics
- 📈 Interactive Charts (Bar, Pie, Line Charts)
- 📅 Mark Habits for Any Date (Backfill Support)
- ✏️ Full CRUD Operations (Create, Read, Update, Delete)
- 🔒 Secure Password Hashing
- 👨‍💼 Admin Panel for User Management
- 🎨 Modern, Responsive UI Design
- 📱 Mobile-Friendly Interface

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Apache Web Server (XAMPP, WAMP, or similar)
- Modern web browser

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Trevonacid/hts.git
cd hts
```

### 2. Database Setup

1. Open phpMyAdmin (or your MySQL client)
2. Import the database schema:
   - Go to phpMyAdmin → Import
   - Select `habit_tracker.sql`
   - Click "Go" to import

   Or run the SQL file manually:
   ```sql
   CREATE DATABASE habit_tracker;
   USE habit_tracker;
   -- Then copy and paste the contents of habit_tracker.sql
   ```

3. Add the admin column:
   ```sql
   ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;
   ```

### 3. Configuration

1. Open `db.php` and update the database credentials:
   ```php
   $host = 'localhost';
   $db   = 'habit_tracker';
   $user = 'root';        // Your MySQL username
   $pass = 'your_password'; // Your MySQL password
   ```

2. Make sure your web server is running (XAMPP, WAMP, etc.)

### 4. Create Admin User

1. Open your browser and navigate to:
   ```
   http://localhost/hts/create_admin.php
   ```

2. This will create the default admin account:
   - **Email:** admin@admin.com
   - **Password:** Admin@123
   
   ⚠️ **Important:** Change this password after first login!

3. After creating the admin, you can delete `create_admin.php` for security.

### 5. Access the Application

- **Home Page:** `http://localhost/hts/index.php`
- **User Registration:** `http://localhost/hts/register.php`
- **User Login:** `http://localhost/hts/login.php`
- **Admin Login:** `http://localhost/hts/admin_login.php`

## Project Structure

```
hts/
├── index.php              # Home page
├── register.php            # User registration
├── login.php              # User login
├── logout.php             # User logout
├── dashboard.php          # User dashboard
├── add_habit.php          # Add new habit
├── edit_habit.php         # Edit habit
├── delete_habit.php       # Delete habit
├── mark_done.php          # Mark habit as done
├── admin_login.php        # Admin login
├── admin_dashboard.php    # Admin dashboard
├── admin_view_user.php    # View user details
├── admin_delete_user.php  # Delete user
├── admin_delete_habit.php # Delete habit (admin)
├── admin_logout.php       # Admin logout
├── create_admin.php       # Admin setup script
├── db.php                 # Database connection
├── style.css              # Stylesheet
├── habit_tracker.sql      # Database schema
├── admin_setup.sql        # Admin column setup
└── README.md              # This file
```

## Usage

### For Regular Users

1. **Register:** Create a new account
2. **Login:** Access your dashboard
3. **Add Habits:** Click "Add Habit" to create new habits
4. **Track Progress:** Mark habits as done daily
5. **View Stats:** See your progress with charts and statistics
6. **Edit/Delete:** Manage your habits as needed

### For Administrators

1. **Login:** Use admin credentials at `admin_login.php`
2. **View Users:** See all registered users in the dashboard
3. **Manage Users:** View user details, delete users and their habits
4. **Delete Habits:** Remove individual habits from any user

## Security Features

- ✅ SQL Injection Protection (Prepared Statements)
- ✅ XSS Protection (Input Sanitization)
- ✅ Password Hashing (bcrypt)
- ✅ Session Management
- ✅ Admin Access Control
- ✅ Input Validation

## Default Admin Credentials

- **Email:** admin@admin.com
- **Password:** Admin@123

⚠️ **Change these immediately after setup!**

## Troubleshooting

### Database Connection Error
- Check your MySQL service is running
- Verify credentials in `db.php`
- Ensure database `habit_tracker` exists

### Admin Column Error
- Run: `ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;`
- Or run `admin_setup.sql`

### Permission Denied
- Check file permissions
- Ensure web server has read access to all files

## Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Charts:** Chart.js
- **Styling:** Custom CSS with modern design

## Contributing

Feel free to fork this project and submit pull requests!

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please open an issue on GitHub.

---

**Made with ❤️ for building better habits**

