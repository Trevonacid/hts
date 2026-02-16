# Habit Tracker System - Project Description

## Overview
A comprehensive web-based habit tracking application that allows users to create, manage, and monitor their daily habits. The system includes a full user management system, interactive data visualization, and an administrative panel for managing users and their habits.

## Core Functionality

### User Management System

#### User Registration
- Users can create accounts with:
  - Full name (2-100 characters, letters/spaces/hyphens/apostrophes only)
  - Email address (validated format, max 100 characters, unique)
  - Password (minimum 8 characters with complexity requirements:
    - At least one uppercase letter
    - At least one lowercase letter
    - At least one number
    - At least one special character
  - Password confirmation field
- Real-time client-side validation
- Server-side validation with comprehensive error messages
- Password visibility toggle (eye icon)
- Secure password hashing using bcrypt
- Email uniqueness check to prevent duplicate accounts

#### User Authentication
- Login system with email and password
- Session-based authentication
- Password visibility toggle
- Automatic redirect if already logged in
- Secure password verification
- Error messages for invalid credentials

#### User Dashboard
- Personalized welcome message with user's name
- Statistics cards showing:
  - Total number of habits
  - Habits completed today
  - Total completions across all habits
- Interactive charts:
  - Bar chart showing completion counts per habit
  - Pie chart showing distribution of completions
  - Line chart showing last 7 days activity trend
- Real-time data visualization using Chart.js

### Habit Management (CRUD Operations)

#### Create Habits
- Add new habits with name (2-100 characters)
- Character validation (letters, numbers, spaces, basic punctuation)
- Automatic date tracking (creation date)
- User-specific habits (each user sees only their habits)
- Success/error feedback messages

#### Read/View Habits
- Display all user's habits in a grid layout
- Show habit statistics:
  - Total completions
  - Completions this week
  - Whether completed today
- Habit cards with:
  - Habit name
  - Creation date
  - Visual statistics
  - Action buttons
- Empty state message when no habits exist

#### Update Habits
- Edit habit names
- Ownership verification (users can only edit their own habits)
- Same validation as creation
- Success feedback after update

#### Delete Habits
- Delete individual habits
- Cascading deletion of all related habit logs
- Confirmation dialog before deletion
- Ownership verification
- Success feedback

### Habit Tracking

#### Mark Habits as Done
- Mark habits as completed for today
- One completion per habit per day (prevents duplicates)
- Visual feedback (button changes to "Done Today" when completed)
- Success/error messages

#### Historical Tracking
- Mark habits for previous dates (backfill support)
- Date picker modal with calendar interface
- Can mark habits for dates up to 1 year in the past
- Can mark habits for dates before habit was created (for historical data entry)
- Prevents future dates
- Prevents duplicate entries for same date
- Date format validation
- Clear success messages showing which date was marked

### Data Visualization

#### Statistics Dashboard
- Total habits count
- Today's completions count
- Total completions across all habits
- Visual representation with gradient numbers

#### Interactive Charts
- **Bar Chart**: Shows total completions for each habit
  - Color-coded bars
  - Hover tooltips with exact counts
  - Responsive design
  
- **Pie Chart**: Shows completion distribution across habits
  - Percentage breakdown
  - Color-coded segments
  - Legend with habit names
  - Tooltips showing count and percentage
  
- **Line Chart**: Shows last 7 days activity
  - Daily completion trends
  - Smooth line with filled area
  - Day labels (Mon, Tue, Wed, etc.)
  - Interactive tooltips

### Administrative Panel

#### Admin Authentication
- Separate admin login system
- Admin-only access (checks is_admin flag in database)
- Session-based admin authentication
- Secure credential verification

#### Admin Dashboard
- Overview statistics:
  - Total number of users
  - Total number of habits
  - Total number of habit logs
- User management table showing:
  - User ID
  - User name
  - Email address
  - Number of habits per user
  - Number of logs per user
  - Registration date
  - Action buttons (View, Delete)

#### User Management
- View all registered users
- View individual user details:
  - User information (name, email, ID, join date)
  - All habits belonging to that user
  - Habit statistics (completion counts)
  - Ability to delete individual habits
- Delete users:
  - Cascading deletion (deletes user, all their habits, and all habit logs)
  - Confirmation dialog
  - Prevents admin from deleting themselves
  - Success feedback

#### Habit Management (Admin)
- View all habits for any user
- Delete individual habits from any user
- Delete all habits when deleting a user
- Statistics for each habit

### Security Features

#### Input Validation
- Server-side validation for all inputs
- Client-side validation for better UX
- SQL injection protection (prepared statements)
- XSS protection (input sanitization and output escaping)
- Email format validation
- Password strength requirements
- Input length limits

#### Authentication Security
- Secure password hashing (bcrypt)
- Session management
- Access control (users can only access their own data)
- Admin-only routes protection
- Ownership verification for all operations

#### Data Protection
- Prepared statements for all database queries
- Input sanitization (trim, htmlspecialchars)
- Type casting for IDs (intval)
- Foreign key constraints in database
- Cascading deletes properly handled

### User Interface

#### Design Features
- Modern gradient background
- Card-based layout
- Smooth animations and transitions
- Hover effects on interactive elements
- Responsive design (mobile, tablet, desktop)
- Custom scrollbar styling
- Professional color scheme
- Consistent spacing and typography

#### User Experience
- Clear navigation
- Intuitive forms with labels
- Real-time validation feedback
- Success/error message alerts
- Loading states
- Confirmation dialogs for destructive actions
- Empty states with helpful messages
- Accessible design

#### Responsive Design
- Mobile-friendly layout
- Adaptive grid systems
- Flexible forms
- Touch-friendly buttons
- Optimized for all screen sizes

### Technical Features

#### Database Structure
- Users table (id, name, email, password, is_admin, created_at)
- Habits table (id, user_id, habit_name, created_at)
- Habit_logs table (id, habit_id, log_date, status)
- Foreign key relationships
- Indexed columns for performance

#### Backend Architecture
- PHP 7.4+ compatible
- MySQL database
- Session-based authentication
- RESTful-like URL structure
- Separation of concerns (database, logic, presentation)

#### Frontend Technologies
- HTML5 semantic markup
- CSS3 with modern features (gradients, animations, flexbox, grid)
- Vanilla JavaScript (no frameworks)
- Chart.js for data visualization
- Responsive CSS media queries

## User Workflows

### New User Registration Flow
1. User visits registration page
2. Fills in name, email, password, confirm password
3. Real-time validation provides feedback
4. Submits form
5. Server validates all inputs
6. Checks email uniqueness
7. Hashes password
8. Creates account
9. Redirects to login page with success message

### User Login Flow
1. User enters email and password
2. System validates credentials
3. Creates session
4. Redirects to dashboard
5. Dashboard loads with user's habits and statistics

### Adding a Habit Flow
1. User enters habit name in dashboard
2. Clicks "Add Habit"
3. System validates input
4. Creates habit in database
5. Redirects to dashboard with success message
6. New habit appears in habits grid

### Marking Habit as Done Flow
1. User clicks "Mark as Done" on a habit
2. System checks if already done today
3. If not, creates log entry
4. Updates statistics
5. Button changes to "Done Today"
6. Charts update automatically

### Historical Entry Flow
1. User clicks "Mark for Date" button
2. Modal opens with date picker
3. User selects date (up to 1 year ago)
4. System validates date
5. Checks for duplicate entry
6. Creates log entry for selected date
7. Shows success message with date

### Admin User Management Flow
1. Admin logs in
2. Views dashboard with all users
3. Can click "View" to see user details and habits
4. Can delete individual habits
5. Can delete entire user (with all habits and logs)
6. Confirmation dialogs prevent accidental deletions

## Data Relationships

- One user can have many habits (one-to-many)
- One habit can have many logs (one-to-many)
- Habit logs are linked to specific habits
- All data is user-scoped (users only see their own data)
- Admins can see all users and their data

## Statistics and Analytics

- Per-habit statistics (total completions, weekly completions)
- Per-user statistics (total habits, today's completions)
- System-wide statistics (for admins)
- Visual representation through charts
- Historical data tracking (7-day trends)

## Error Handling

- Comprehensive validation error messages
- Database error handling
- Session expiration handling
- Invalid access attempts (redirects)
- User-friendly error messages
- Success confirmations

## Future Enhancement Possibilities

- Email notifications
- Habit streaks tracking
- Goal setting
- Social features (sharing habits)
- Mobile app
- Export data functionality
- Habit categories/tags
- Reminder system
- Achievement badges
- Weekly/monthly reports

---

This project demonstrates a complete full-stack web application with user authentication, CRUD operations, data visualization, administrative features, and modern UI/UX design.

