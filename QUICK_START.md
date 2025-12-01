# 🚀 Quick Start Guide

## Import Test Data
```bash
# Option 1: Via phpMyAdmin
1. Open phpMyAdmin
2. Select 'ub_lrc_dims' database
3. Click 'Import'
4. Choose: database/seed_demo.sql
5. Click 'Go'

# Option 2: Via Command Line
mysql -u root ub_lrc_dims < C:/xampp/htdocs/ub-lrc-dims/database/seed_demo.sql
```

## Test Login Credentials
**All passwords: `password123`**

### Students
- student@ub.edu.ph
- student2@ub.edu.ph

### Librarians
- staff@ub.edu.ph
- lib@ub.edu.ph

## Quick Test Flow

### 1. Student Experience (5 mins)
```
1. Visit: http://localhost/ub-lrc-dims
2. Click "Student Login"
3. Login: student@ub.edu.ph / password123
4. Click "Reservations" → Create new reservation
5. Click "Feedback" → Submit feedback
6. Click "Profile" → View stats
7. Click "History" → See timeline
```

### 2. Librarian Experience (5 mins)
```
1. Visit: http://localhost/ub-lrc-dims
2. Click "Librarian Login"
3. Login: staff@ub.edu.ph / password123
4. Click "Approvals" → Approve a pending request
5. Click "Violations" → Log a violation
6. Click "Feedback" → Review submissions
7. Click "Rooms" → Add a new room
```

## Features Summary

### ✅ Student Pages (6)
- Dashboard, Reservations, Rooms, Feedback, History, Profile

### ✅ Librarian Pages (5)
- Dashboard, Approvals, Rooms, Violations, Feedback, History

### ✅ Core Functionality
- Role-based authentication
- Reservation system with approval workflow
- Feedback submission and tracking
- Violation logging
- Profile management
- Activity history

### ✅ Design Features
- Rounded corners on all elements
- Maroon & gold color scheme
- Video background landing page
- Status badges (pending/approved/rejected)
- Responsive layout
- Error/success messaging

## System is 100% Complete! 🎉

All pages are functional, database-integrated, and styled consistently.
