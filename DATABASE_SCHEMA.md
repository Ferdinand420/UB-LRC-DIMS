# Database Schema Documentation

## Overview

The UB-LRC-DIMS database uses **MySQL 5.7+** with **InnoDB** engine for ACID compliance and foreign key support.

---

## Entity-Relationship Diagram (ERD)

```
┌──────────────────────────┐
│         USERS            │
├──────────────────────────┤
│ PK  id                   │
│     email (UNIQUE)       │
│     password_hash        │
│ FK  role                 │
│     full_name            │
│     created_at           │
└──────────────────────────┘
         ▲  │  ▲
         │  │  │
    (1)  │  │  │  (1)
         │  │  └─────────┐
         │  │            │
    ┌────┘  │            │
    │       │            │
    │   ┌───┴─────────────┴────────────┐
    │   │                              │
┌───┴───────────────┐          ┌──────┴──────────────────┐
│  RESERVATIONS     │          │    VIOLATIONS          │
├───────────────────┤          ├───────────────────────┤
│ PK  id            │          │ PK  id               │
│ FK  user_id       │          │ FK  user_id (violator)│
│ FK  room_id       │          │ FK  room_id          │
│     date          │          │ FK  logged_by        │
│     start_time    │          │     description      │
│     end_time      │          │     created_at       │
│ FK  approved_by   │          └───────────────────────┘
│     status        │
│     purpose       │
│     created_at    │
└───────────────────┘
    │         │
(N) │         │ (N)
    │         │
    │    ┌────┴─────────────────┐
    │    │                      │
    │  ┌─┴────────────────────┐ │
    │  │   RESERVATION_      │ │
    │  │   STUDENTS          │ │
    │  ├─────────────────────┤ │
    │  │ PK  id              │ │
    │  │ FK  reservation_id  │ │
    │  │     student_id_val  │ │
    │  │     created_at      │ │
    │  └─────────────────────┘ │
    │                          │
    └──────────┬───────────────┘
               │
          ┌────┴──────────┐
          │               │
        (N)             (1)
          │               │
    ┌─────┴──────────┐
    │    ROOMS       │
    ├────────────────┤
    │ PK  id         │
    │     name       │
    │     capacity   │
    │     status     │
    │     description│
    │     created_at │
    └────────────────┘

┌──────────────────────────┐          ┌───────────────────────┐
│      FEEDBACK            │          │     WAITLIST          │
├──────────────────────────┤          ├───────────────────────┤
│ PK  id                   │          │ PK  id               │
│ FK  user_id              │          │ FK  user_id          │
│     message              │          │ FK  room_id          │
│     status               │          │     preferred_date   │
│     created_at           │          │     preferred_time   │
└──────────────────────────┘          │     status           │
                                      │     created_at       │
                                      └───────────────────────┘
```

---

## Table Specifications

### 1. USERS Table

**Purpose:** Store user accounts (students and librarians)

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'librarian') NOT NULL DEFAULT 'student',
    full_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Constraint | Purpose |
|-------|------|-----------|---------|
| id | INT | AUTO_INCREMENT, PK | Unique identifier |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Login credential |
| password_hash | VARCHAR(255) | NOT NULL | bcrypt hash (60 chars) |
| role | ENUM | DEFAULT 'student' | Access control |
| full_name | VARCHAR(255) | NULL | Display name |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Audit trail |

**Indexes:**
- `idx_email`: Fast login lookups
- `idx_role`: Efficient role-based filtering

**Sample Data:**
```
id=1, email=student@ub.edu.ph, role=student, full_name=Juan Dela Cruz
id=2, email=staff@ub.edu.ph, role=librarian, full_name=Ms. Teresa Cruz
```

---

### 2. ROOMS Table

**Purpose:** Store room information and capacity

```sql
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('available', 'maintenance', 'reserved') NOT NULL DEFAULT 'available',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Constraint | Purpose |
|-------|------|-----------|---------|
| id | INT | AUTO_INCREMENT, PK | Unique identifier |
| name | VARCHAR(100) | NOT NULL | Room label |
| capacity | INT | NOT NULL | Max occupancy |
| status | ENUM | DEFAULT 'available' | Availability indicator |
| description | TEXT | NULL | Room features |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation date |

**Status Values:**
- `available` - Can be reserved
- `maintenance` - Temporarily unavailable
- `reserved` - Currently in use

**Sample Data:**
```
id=1, name=Discussion Room 1, capacity=10, status=available
id=2, name=Discussion Room 2, capacity=10, status=available
```

---

### 3. RESERVATIONS Table

**Purpose:** Core table for room booking requests

```sql
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    purpose TEXT,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_room (room_id),
    INDEX idx_status (status),
    INDEX idx_date (reservation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Constraint | Purpose |
|-------|------|-----------|---------|
| id | INT | AUTO_INCREMENT, PK | Unique booking ID |
| user_id | INT | FK → users | Who made the reservation |
| room_id | INT | FK → rooms | Which room |
| reservation_date | DATE | NOT NULL | Booking date |
| start_time | TIME | NOT NULL | Start time |
| end_time | TIME | NOT NULL | End time |
| status | ENUM | DEFAULT 'pending' | Booking state |
| purpose | TEXT | NULL | Reason for reservation |
| approved_by | INT | FK → users, NULL | Approving librarian |
| approved_at | TIMESTAMP | NULL | Approval timestamp |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation |
| updated_at | TIMESTAMP | AUTO UPDATE | Last modified |

**Status Values:**
- `pending` - Awaiting librarian approval
- `approved` - Confirmed booking
- `rejected` - Denied booking
- `cancelled` - Student cancelled

**Indexes:**
- `idx_user`: User's reservations
- `idx_room`: Room's bookings
- `idx_status`: Filter by status
- `idx_date`: Date-range queries

---

### 4. RESERVATION_STUDENTS Table

**Purpose:** Track which students are part of group reservations

```sql
CREATE TABLE reservation_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL,
    student_id_value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    INDEX idx_reservation (reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Purpose |
|-------|------|---------|
| reservation_id | INT | Links to reservation |
| student_id_value | VARCHAR(255) | Student ID number |

**Sample Query:**
```sql
-- Get all students in a group reservation
SELECT student_id_value FROM reservation_students
WHERE reservation_id = 5;
```

---

### 5. FEEDBACK Table

**Purpose:** Collect and track student feedback

```sql
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'reviewed', 'resolved') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Purpose |
|-------|------|---------|
| id | INT | Unique feedback ID |
| user_id | INT | Who submitted |
| message | TEXT | Feedback content |
| status | ENUM | Processing state |
| created_at | TIMESTAMP | Submission time |

**Status Workflow:**
```
new (unread) 
  ↓
reviewed (librarian acknowledged)
  ↓
resolved (action taken)
```

---

### 6. VIOLATIONS Table

**Purpose:** Log student code of conduct violations

```sql
CREATE TABLE violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NULL,
    logged_by INT NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (logged_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_logged_by (logged_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Purpose |
|-------|------|---------|
| user_id | INT | Student who violated |
| room_id | INT | Where it happened (optional) |
| logged_by | INT | Librarian who logged it |
| description | TEXT | Violation details |
| created_at | TIMESTAMP | When it was logged |

**Sample Violations:**
- Late room return
- Noise disturbance
- Room left untidy
- Capacity exceeded

---

### 7. WAITLIST Table

**Purpose:** Manage student requests for full rooms

```sql
CREATE TABLE waitlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    preferred_date DATE NOT NULL,
    preferred_time TIME NOT NULL,
    status ENUM('waiting', 'notified', 'expired') NOT NULL DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

| Field | Type | Purpose |
|-------|------|---------|
| user_id | INT | Student waiting |
| room_id | INT | Desired room |
| preferred_date | DATE | Desired date |
| preferred_time | TIME | Desired time |
| status | ENUM | Waitlist state |
| created_at | TIMESTAMP | When joined |

**Status Values:**
- `waiting` - On the list
- `notified` - Space became available
- `expired` - Request expired

---

## Data Flow Examples

### Example 1: Making a Reservation

```
1. Student submits form (reservations.php)
   ↓
2. JavaScript calls api/create_reservation.php
   ├─ Validate user is logged in (users table)
   ├─ Check room exists (rooms table)
   └─ Check for conflicts (reservations table)
   ↓
3. INSERT into reservations table
   ├ user_id = 1 (student's ID)
   ├ room_id = 2 (chosen room)
   ├ status = 'pending' (awaits approval)
   └ created_at = NOW()
   ↓
4. INSERT into reservation_students table
   ├ reservation_id = 15 (new reservation)
   └ student_id_value = '2021001' (student number)
   ↓
5. Response to frontend: success, show in list
   ↓
6. Librarian approves (approvals.php)
   ├ UPDATE reservations SET status = 'approved'
   ├ SET approved_by = 6 (librarian's ID)
   └ SET approved_at = NOW()
   ↓
7. Dashboard updates show approved reservation
```

### Example 2: Logging a Violation

```
1. Librarian submits violation form (violations.php)
   ↓
2. JavaScript calls api/log_violation.php
   ├ Validate logged_by is librarian
   ├ Validate user_id exists
   └ Validate description not empty
   ↓
3. INSERT into violations table
   ├ user_id = 4 (student who violated)
   ├ room_id = 1 (where it happened)
   ├ logged_by = 6 (librarian logging)
   ├ description = 'Late return without notice'
   └ created_at = NOW()
   ↓
4. Response: violation logged
   ↓
5. Report shows on violations.php with:
   - Student name (JOIN with users)
   - Room name (JOIN with rooms)
   - Librarian name (JOIN with users as logger)
```

---

## Query Examples

### Find all pending reservations for a student
```sql
SELECT r.*, rm.name as room_name
FROM reservations r
JOIN rooms rm ON r.room_id = rm.id
WHERE r.user_id = 1 AND r.status = 'pending'
ORDER BY r.reservation_date DESC;
```

### Check if a room is available at a specific time
```sql
SELECT COUNT(*) FROM reservations
WHERE room_id = 2
  AND reservation_date = '2025-12-15'
  AND status IN ('approved', 'pending')
  AND (
    (start_time < '10:00:00' AND end_time > '09:00:00')
    OR
    (start_time < '11:00:00' AND end_time > '10:00:00')
  );
-- Returns > 0 if conflict exists
```

### Get feedback for dashboard
```sql
SELECT f.*, u.full_name
FROM feedback f
JOIN users u ON f.user_id = u.id
WHERE f.status = 'new'
ORDER BY f.created_at DESC
LIMIT 10;
```

### Get violation history for a student
```sql
SELECT v.*, l.full_name as logged_by_name, rm.name as room_name
FROM violations v
JOIN users l ON v.logged_by = l.id
LEFT JOIN rooms rm ON v.room_id = rm.id
WHERE v.user_id = 4
ORDER BY v.created_at DESC;
```

---

## Normalization

The schema follows **Third Normal Form (3NF)**:

✅ **No redundant data** - Each fact stored once
✅ **Atomic values** - No repeating groups (reservation_students handles multiple students)
✅ **Functional dependencies** - Non-key fields depend on primary key
✅ **Referential integrity** - Foreign keys ensure consistency

---

## Performance Optimization

### Indexes
- Primary keys auto-indexed
- Foreign keys indexed for joins
- Status, date, user fields indexed for filtering

### Query Optimization
- Use `INDEX` for WHERE clauses
- Prepared statements prevent query recompilation
- Specific SELECT columns (no SELECT *)

### Capacity Planning
- Current: ~50 rooms, 500 students, 10,000 reservations
- Scalable: Can handle 1M+ records with proper indexing

---

## Backup & Recovery

### Regular Backup
```bash
mysqldump -u root ub_lrc_dims > backup.sql
```

### Restore from Backup
```bash
mysql -u root ub_lrc_dims < backup.sql
```

### Test Data Reset
```bash
mysql -u root ub_lrc_dims < database/seed_demo_safe.sql
```

