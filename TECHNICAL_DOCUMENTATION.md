# Technical Documentation - UB-LRC-DIMS

## Table of Contents
1. [System Overview](#system-overview)
2. [Database Schema](#database-schema)
3. [Code Architecture](#code-architecture)
4. [Project Structure](#project-structure)
5. [Setup Instructions](#setup-instructions)

---

## System Overview

**UB Library Room Capacity & Inventory Management System (DIMS)** is a web-based application for managing room reservations in the University of Benguet Library.

### Key Features
- Role-based access control (Student/Librarian)
- Room reservation system with conflict detection
- Waitlist management
- Feedback and violation logging
- Real-time dashboard with statistics

### Technology Stack
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Server:** Apache (XAMPP)

---

## Database Schema

### Tables Overview

```
┌─────────────────────────────────────────┐
│              DATABASE SCHEMA             │
└─────────────────────────────────────────┘

users (Core)
├── id (PK)
├── email (UNIQUE)
├── password_hash
├── role (ENUM: student, librarian)
├── full_name
└── created_at

rooms (Core)
├── id (PK)
├── name
├── capacity
├── status (ENUM: available, maintenance, reserved)
├── description
└── created_at

reservations (Core)
├── id (PK)
├── user_id (FK → users)
├── room_id (FK → rooms)
├── reservation_date
├── start_time
├── end_time
├── purpose
├── status (ENUM: pending, approved, rejected, cancelled)
├── approved_by (FK → users, nullable)
├── approved_at (nullable)
├── created_at
└── updated_at

reservation_students (Supporting)
├── id (PK)
├── reservation_id (FK → reservations)
├── student_id_value
└── created_at

feedback (Core)
├── id (PK)
├── user_id (FK → users)
├── message
├── status (ENUM: new, reviewed, resolved)
└── created_at

violations (Core)
├── id (PK)
├── user_id (FK → users)
├── room_id (FK → rooms, nullable)
├── logged_by (FK → users)
├── description
└── created_at

waitlist (Supporting)
├── id (PK)
├── user_id (FK → users)
├── room_id (FK → rooms)
├── preferred_date
├── preferred_time
├── status (ENUM: waiting, notified, expired)
└── created_at
```

### Table Relationships

```
users (1) ──────── (N) reservations
  │                      │
  │                      └─── (N) reservation_students
  │
  ├─ (1) ────── (N) feedback
  │
  ├─ (1) ────── (N) violations (as user)
  │
  ├─ (1) ────── (N) violations (as logger)
  │
  └─ (1) ────── (N) waitlist

rooms (1) ──────── (N) reservations
  │
  └─ (1) ──────── (N) violations
```

### Key Indexes

```sql
-- Performance optimization indexes
users.email - UNIQUE (fast login)
users.role - INDEX (role-based filtering)
reservations.user_id - INDEX (user queries)
reservations.room_id - INDEX (room queries)
reservations.status - INDEX (status filtering)
reservations.reservation_date - INDEX (date queries)
reservation_students.reservation_id - INDEX (group queries)
feedback.user_id - INDEX (user feedback)
feedback.status - INDEX (status filtering)
violations.user_id - INDEX (user violations)
violations.logged_by - INDEX (logger queries)
waitlist.status - INDEX (status filtering)
```

### Data Types & Constraints

```
id: INT AUTO_INCREMENT PRIMARY KEY
email: VARCHAR(255) UNIQUE NOT NULL
password_hash: VARCHAR(255) NOT NULL (bcrypt hash)
role: ENUM('student', 'librarian') DEFAULT 'student'
full_name: VARCHAR(255)
name: VARCHAR(100) NOT NULL
capacity: INT NOT NULL
status: ENUM('available', 'maintenance', 'reserved')
description: TEXT
reservation_date: DATE NOT NULL
start_time: TIME NOT NULL
end_time: TIME NOT NULL
purpose: TEXT
message/description: TEXT NOT NULL
created_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

---

## Code Architecture

### Design Pattern: MVC-Inspired

```
REQUEST FLOW:
  1. User accesses page.php
  2. Page includes auth.php (checks permissions)
  3. Page includes header.php & sidebar.php (UI)
  4. JavaScript loads data from api/
  5. API validates, queries database, returns JSON
  6. JavaScript updates DOM dynamically
```

### Security Layers

```
LAYER 1: Authentication
├── includes/auth.php
└── Session-based with regeneration

LAYER 2: Authorization
├── Role checks: is_student(), is_librarian()
├── Resource ownership validation
└── HTTP status codes (401, 403)

LAYER 3: Data Validation
├── Input sanitization: htmlspecialchars()
├── Prepared statements (prevent SQL injection)
└── Type validation in API endpoints

LAYER 4: Session Management
├── session_regenerate_id() on login
├── Secure cookie handling
└── Proper logout with session destruction
```

### Error Handling

```
PHP BACKEND:
- Try-catch blocks in critical functions
- error_log() for server-side logging
- HTTP status codes returned (200, 400, 401, 403, 500)

JAVASCRIPT FRONTEND:
- Try-catch in async functions
- AbortController prevents race conditions
- User-friendly error messages
- No console output in production
```

---

## Project Structure

```
ub-lrc-dims/
│
├── index.php                           # Landing page (login modals)
│
├── auth/                               # Authentication (3 files)
│   ├── login.php                      # Form handler
│   ├── logout.php                     # Session destroyer
│   └── clear_session.php              # Helper utility
│
├── pages/                              # Main application pages (11 files)
│   ├── dashboard.php                  # Student/Librarian overview
│   ├── reservations.php               # Booking interface
│   ├── rooms.php                      # Room browser & management
│   ├── feedback.php                   # Feedback submission/view
│   ├── history.php                    # Activity timeline
│   ├── profile.php                    # User profile (students)
│   ├── librarian.php                  # Librarian dashboard
│   ├── approvals.php                  # Reservation approval queue
│   ├── violations.php                 # Violation logging (librarians)
│   ├── learn-more.php                 # Info page
│   ├── news.php                       # News page
│   └── support.php                    # Support page
│
├── api/                                # REST API endpoints (21 files)
│   ├── Reservations/
│   │   ├── create_reservation.php     # POST - Create booking
│   │   ├── get_reservations.php       # GET - List reservations
│   │   ├── cancel_reservation.php     # POST - Cancel booking
│   │   └── update_reservation_status.php  # POST - Approve/Reject
│   │
│   ├── Rooms/
│   │   ├── get_rooms.php              # GET - Room list
│   │   ├── add_room.php               # POST - Create room
│   │   └── get_room_status.php        # GET - Room availability
│   │
│   ├── Feedback/
│   │   ├── submit_feedback.php        # POST - Submit feedback
│   │   └── get_feedback.php           # GET - Feedback list
│   │
│   ├── Users/
│   │   ├── get_profile.php            # GET - User profile
│   │   ├── update_profile.php         # POST - Update profile
│   │   └── request_password_reset.php # POST - Password reset
│   │
│   ├── Violations/
│   │   ├── log_violation.php          # POST - Log violation
│   │   └── get_violations.php         # GET - Violation list
│   │
│   ├── Waitlist/
│   │   ├── add_to_waitlist.php        # POST - Add to waitlist
│   │   └── get_waitlist.php           # GET - Waitlist entries
│   │
│   ├── Dashboard/
│   │   ├── dashboard_stats.php        # GET - Statistics
│   │   ├── get_recent_activity.php    # GET - Activity feed
│   │   └── get_pending_reservations.php # GET - Pending approvals
│   │
│   └── Other/
│       ├── get_history.php            # GET - User history
│       └── reset_password.php         # POST - Password change
│
├── includes/                           # Shared utilities (3 files)
│   ├── auth.php                       # Authentication functions
│   │   ├── get_role()
│   │   ├── is_student()
│   │   ├── is_librarian()
│   │   ├── require_login()
│   │   ├── authenticate_user()
│   │   ├── login_user()
│   │   ├── get_user_id()
│   │   └── logout_user()
│   │
│   ├── header.php                     # Navigation bar
│   │   └── Site topbar with branding
│   │
│   └── sidebar.php                    # Navigation sidebar
│       └── render_sidebar(page_name)
│
├── config/                             # Configuration (1 file)
│   └── db.php                         # Database connection
│       ├── Environment variable support
│       ├── mysqli connection
│       └── db_query() helper
│
├── database/                           # Database scripts (3 files)
│   ├── schema.sql                     # Table definitions
│   ├── seed_demo_safe.sql             # Test data (transaction-safe)
│   └── README.md                      # Setup guide
│
├── assets/                             # Static files
│   ├── css/
│   │   └── style.css                  # 1133 lines (comprehensive styling)
│   │       ├── CSS variables (theme colors)
│   │       ├── Responsive grid system
│   │       ├── Component styles
│   │       └── Animations & transitions
│   │
│   ├── js/                            # 11 optimized JavaScript files
│   │   ├── dashboard.js               # Dashboard statistics & real-time updates
│   │   ├── reservations.js            # Booking interface logic
│   │   ├── rooms.js                   # Room management
│   │   ├── feedback.js                # Feedback submission
│   │   ├── history.js                 # History filtering
│   │   ├── profile.js                 # Profile management
│   │   ├── violations.js              # Violation logging
│   │   ├── approvals.js               # Reservation approval
│   │   ├── background.js              # Video background handling
│   │   ├── login-modal.js             # Login form interactions
│   │   └── sidebar.js                 # Mobile sidebar toggle
│   │
│   ├── img/
│   │   └── logo.svg                   # Brand logo
│   │
│   └── media/
│       ├── UB-Homepage-Video-w-text.mp4    # Background video (fallback)
│       ├── UB-Homepage-Video-w-text.webm   # Background video (optimized)
│       ├── DIMS_logo.png              # Application logo
│       └── UB_logo.png                # University logo
│
├── docs/                               # Documentation (3 files)
│   ├── README.md                      # Main documentation
│   ├── QUICK_START.md                 # Setup guide
│   ├── IMPLEMENTATION_COMPLETE.md     # Feature checklist
│   ├── IMPROVEMENTS_LOG.md            # Change history
│   └── Technical Documentation.md     # This file
│
├── .env.example                        # Environment template
├── .gitignore                          # Git ignore patterns
└── .git/                               # Version control
```

---

## Code Organization Principles

### Authentication (auth.php)
```php
// Location: includes/auth.php
// Purpose: Session management and role-based access

function get_role(): ?string
  └─ Returns: 'student', 'librarian', or null

function is_student(): bool
  └─ Quick permission check

function require_login(): void
  └─ Redirect if not authenticated

function authenticate_user(email, password): ?array
  └─ Validate credentials with bcrypt

function login_user(email, role, user_id): void
  └─ Create secure session with ID regeneration

function logout_user(): void
  └─ Destroy session and clear cookies
```

### API Endpoints
```php
// Pattern for all API files:
// 1. Set JSON header
// 2. Start session
// 3. Require auth includes
// 4. Check authorization (401, 403)
// 5. Validate input (400)
// 6. Execute query
// 7. Return JSON response

// Example: api/create_reservation.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!get_user_id()) { /* 401 */ }
if (!is_student()) { /* 403 */ }
$data = json_decode(file_get_contents('php://input'), true);
if (!$data['room_id']) { /* 400 */ }

// Validation & Database operations
$stmt = $conn->prepare("INSERT INTO reservations...");
$stmt->bind_param(...);
$stmt->execute();

echo json_encode(['success' => true, 'message' => '...']);
```

### JavaScript Patterns
```javascript
// Pattern for all frontend JS files:
// 1. Define constants
// 2. Auto-refresh with setInterval()
// 3. Async functions with AbortController
// 4. Error handling with try-catch
// 5. DOM updates with innerHTML/appendChild

// Example: assets/js/dashboard.js
const REFRESH_MS = 30000;
let currentController = null;

async function loadDashboardStats() {
    if (currentController) currentController.abort();
    currentController = new AbortController();
    const signal = currentController.signal;
    
    try {
        const response = await fetch('../api/dashboard_stats.php', { signal });
        if (!response.ok) throw new Error('Network error');
        const data = await response.json();
        updateText('stat-id', data.value);
    } catch (error) {
        if (error.name !== 'AbortError') {
            updateText('stat-id', 'Error');
        }
    }
}

setInterval(loadDashboardStats, REFRESH_MS);
```

---

## Database Relationships Explained

### Reservation Flow
```
User (student) 
  → creates Reservation
    → for Room
      → with Reservation_Students (group members)
      → approved by Librarian
```

### Violation Tracking
```
Student (violation_user) 
  ← logged by Librarian (logged_by)
  ← in Room (optional)
  ← with Description
```

### Feedback System
```
User (student/librarian)
  → submits Feedback
    → with Message
    → Status: new → reviewed → resolved
```

### Waitlist Logic
```
User
  → joins Waitlist for Room
    → on preferred_date & preferred_time
    → Status: waiting → notified → expired
```

---

## API Response Format

### Success Response (200)
```json
{
  "success": true,
  "message": "Operation completed",
  "data": { /* optional */ }
}
```

### Error Response (400, 401, 403, 500)
```json
{
  "success": false,
  "message": "Error description"
}
```

### HTTP Status Codes
- **200 OK** - Request successful
- **400 Bad Request** - Invalid parameters
- **401 Unauthorized** - Not logged in
- **403 Forbidden** - Insufficient permissions
- **500 Internal Server Error** - Server error

---

## Setup Instructions

### 1. Database Setup
```bash
# Import schema
mysql -u root < database/schema.sql

# Load test data
mysql -u root ub_lrc_dims < database/seed_demo_safe.sql
```

### 2. Environment Configuration
```bash
# Copy template
cp .env.example .env

# Edit .env with your values
# DB_HOST=localhost
# DB_USER=root
# DB_PASSWORD=
# DB_NAME=ub_lrc_dims
```

### 3. Access Application
```
http://localhost/ub-lrc-dims/index.php
```

### 4. Test Credentials
```
Student:
  Email: student@ub.edu.ph
  Password: password123

Librarian:
  Email: staff@ub.edu.ph
  Password: password123
```

---

## Key Features Implementation

### Real-Time Dashboard
- Uses `setInterval()` for 30-second auto-refresh
- `AbortController` prevents race conditions
- Parallel API calls with `Promise.all()`

### Conflict Detection
```php
// Check if room is already booked
SELECT COUNT(*) FROM reservations
WHERE room_id = ? 
  AND reservation_date = ?
  AND status IN ('approved', 'pending')
  AND (
    (start_time < ? AND end_time > ?)
    OR
    (start_time < ? AND end_time > ?)
  )
```

### Role-Based Access
```php
// Librarians see all, students see only theirs
if (is_librarian()) {
    $sql = "SELECT * FROM reservations";
} else {
    $sql = "SELECT * FROM reservations WHERE user_id = ?";
}
```

### Video Background
- Responsive design
- Accessibility: respects `prefers-reduced-motion`
- Fallback: static gradient
- Optimized formats: WebM + MP4

---

## Performance Considerations

### Database Optimization
- Indexes on frequently queried columns
- Prepared statements (prevent SQL injection + caching)
- Auto-increment for primary keys
- Foreign keys enforce referential integrity

### Frontend Optimization
- No console output (removed console.error)
- CSS variables for theme consistency
- Responsive images and videos
- Lazy loading potential

### API Optimization
- Specific SELECT columns (no SELECT *)
- Pagination ready (LIMIT implemented)
- Abort previous requests to prevent race conditions
- JSON compression-friendly

---

## Maintenance & Troubleshooting

### Common Issues

**"Database connection error"**
- Check .env file or config/db.php
- Verify MySQL is running
- Confirm database exists

**"Unauthorized" on login**
- Clear browser cookies
- Check password hash in database
- Verify user role is 'student' or 'librarian'

**"Room already booked"**
- Reservation conflict detection working
- User needs different time or room
- Check pending reservations too

### Logging
- PHP errors: `error_log()` writes to Apache logs
- Database queries: Check `LAST_INSERT_ID()` for inserts
- Frontend: Check browser console (currently empty in production)

---

## Security Checklist

- ✅ SQL Injection Prevention (prepared statements)
- ✅ Password Hashing (bcrypt)
- ✅ Session Regeneration (CSRF prevention)
- ✅ Authorization Checks (role-based)
- ✅ Input Sanitization (htmlspecialchars)
- ✅ HTTP Status Codes (proper error responses)
- ⚠️ Production: Add HTTPS, CSP headers, rate limiting

---

## Additional Resources

- Database: `database/README.md`
- API Reference: `api/README.md`
- Quick Start: `QUICK_START.md`
- Feature List: `IMPLEMENTATION_COMPLETE.md`
- Change Log: `IMPROVEMENTS_LOG.md`

