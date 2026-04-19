# 💧 Neer Nigrani - जल शक्ति Water Monitoring & Complaint Management System

## 🚀 Setup Instructions (XAMPP)

### Step 1: Copy Files
Extract this folder to: `C:\xampp\htdocs\neer-nigrani`

### Step 2: Start XAMPP
- Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 3: Create Database
- Open **phpMyAdmin**: http://localhost/phpmyadmin
- Click "Import" tab
- Select `database.sql` file from this folder
- Click "Go"

### Step 4: Open Website
- Open browser: **http://localhost/neer-nigrani/**

---

## 🔑 Admin Login Credentials
- **Email:** admin@neernigrani.com
- **Password:** Admin@123

---

## 📁 Folder Structure
```
neer-nigrani/
├── index.php              # Home page
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # User logout
├── database.sql           # MySQL database schema
├── css/style.css          # Main stylesheet
├── js/script.js           # JavaScript
├── images/logo.jpeg       # Logo
├── uploads/               # Complaint photos
├── includes/
│   └── config.php         # Database config & helpers
├── pages/
│   ├── user_dashboard.php # User dashboard
│   ├── new_complaint.php  # File complaint form
│   ├── my_complaints.php  # User's complaints list
│   └── track_complaint.php # Track by complaint ID
├── admin/
│   ├── login.php          # Admin login
│   ├── dashboard.php      # Admin panel
│   └── logout.php         # Admin logout
└── README.md
```

## ✨ Features
- ✅ User Registration/Login with password encryption
- ✅ Complaint filing with photo upload
- ✅ Unique complaint ID generation & tracking
- ✅ Admin panel with complaint management
- ✅ Water alert notification system
- ✅ Auto GPS location detection
- ✅ Bihar districts water supply timing
- ✅ Responsive mobile-friendly design
- ✅ Water wave animation & gradient theme
