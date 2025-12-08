# UB-LRC-DIMS API Documentation

## Base URL
```
/api/
```

All endpoints return JSON and require proper authentication via session.

## Authentication Endpoints

### POST `/auth/login.php`
Authenticate user with email and password.

**Request:**
```json
{
  "email": "student@ub.edu.ph",
  "password": "password123"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Login successful",
  "redirect": "/ub-lrc-dims/pages/dashboard.php"
}
```

---

## Reservation Endpoints

### GET `/api/get_reservations.php`
Retrieve reservations (student sees own, librarian sees all).

**Response:**
```json
{
  "success": true,
  "reservations": [
    {
      "id": 1,
      "reservation_date": "2025-12-15",
      "start_time": "09:00:00",
      "end_time": "11:00:00",
      "room_name": "Discussion Room 1",
      "capacity": 8,
      "status": "approved"
    }
  ]
}
```

### POST `/api/create_reservation.php`
Create a new reservation (students only).

**Request:**
```json
{
  "room_id": 1,
  "reservation_date": "2025-12-15",
  "start_time": "09:00:00",
  "end_time": "11:00:00",
  "purpose": "Group study",
  "student_ids": ["2021001", "2021002"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Reservation created successfully",
  "reservation_id": 15
}
```

### POST `/api/cancel_reservation.php`
Cancel an existing reservation.

**Request:**
```json
{
  "reservation_id": 15
}
```

**Response:**
```json
{
  "success": true,
  "message": "Reservation cancelled successfully"
}
```

### POST `/api/update_reservation_status.php`
Update reservation status (librarians only).

**Request:**
```json
{
  "reservation_id": 15,
  "status": "approved"
}
```

**Status Options:** `pending`, `approved`, `rejected`, `cancelled`

---

## Room Endpoints

### GET `/api/get_rooms.php`
Retrieve all rooms with status.

**Response:**
```json
{
  "success": true,
  "rooms": [
    {
      "id": 1,
      "name": "Discussion Room 1",
      "capacity": 8,
      "status": "available",
      "description": "Main discussion room with whiteboard"
    }
  ]
}
```

### GET `/api/get_room_status.php`
Check room availability and waitlist.

**Query:** `?room_id=1`

**Response:**
```json
{
  "success": true,
  "room": { /* room data */ },
  "currentReservations": [ /* today's reservations */ ],
  "waitlistCount": 2
}
```

### POST `/api/add_room.php`
Add new room (librarians only).

**Request:**
```json
{
  "name": "Meeting Room A",
  "capacity": 20,
  "description": "Large meeting space with projector"
}
```

---

## Feedback Endpoints

### GET `/api/get_feedback.php`
Retrieve feedback (student sees own, librarian sees all).

**Response:**
```json
{
  "success": true,
  "feedback": [
    {
      "id": 1,
      "message": "Great facility!",
      "status": "new",
      "created_at": "2025-12-08T10:30:00Z"
    }
  ]
}
```

### POST `/api/submit_feedback.php`
Submit new feedback.

**Request:**
```json
{
  "message": "Great facility!"
}
```

---

## User Endpoints

### GET `/api/get_profile.php`
Retrieve current user's profile.

**Response:**
```json
{
  "success": true,
  "profile": {
    "id": 1,
    "email": "student@ub.edu.ph",
    "full_name": "Juan Dela Cruz",
    "role": "student"
  }
}
```

### POST `/api/update_profile.php`
Update user profile.

**Request:**
```json
{
  "full_name": "Juan Dela Cruz Updated"
}
```

---

## Violations Endpoints

### GET `/api/get_violations.php`
Retrieve violations (librarians only).

**Response:**
```json
{
  "success": true,
  "violations": [
    {
      "id": 1,
      "user_name": "Student Name",
      "room_name": "Discussion Room 1",
      "description": "Late return",
      "created_at": "2025-12-08T10:30:00Z"
    }
  ]
}
```

### POST `/api/log_violation.php`
Log a violation (librarians only).

**Request:**
```json
{
  "user_id": 1,
  "room_id": 1,
  "description": "Late return without notice"
}
```

---

## Waitlist Endpoints

### GET `/api/get_waitlist.php`
Retrieve waitlist entries.

**Response:**
```json
{
  "success": true,
  "waitlist": [
    {
      "id": 1,
      "user_name": "Maria Santos",
      "room_name": "Discussion Room 2",
      "preferred_date": "2025-12-15",
      "preferred_time": "14:00:00",
      "status": "waiting"
    }
  ]
}
```

### POST `/api/add_to_waitlist.php`
Add user to room waitlist.

**Request:**
```json
{
  "room_id": 2,
  "preferred_date": "2025-12-15",
  "preferred_time": "14:00:00"
}
```

---

## Dashboard Endpoints

### GET `/api/dashboard_stats.php`
Get dashboard statistics.

**Response:**
```json
{
  "success": true,
  "stats": {
    "total_reservations": 15,
    "pending_approvals": 3,
    "approved_reservations": 10,
    "total_feedback": 8
  }
}
```

### GET `/api/get_recent_activity.php`
Get recent system activity.

**Response:**
```json
{
  "success": true,
  "activities": [
    {
      "type": "reservation_created",
      "description": "New reservation for Discussion Room 1",
      "user_name": "Student Name",
      "created_at": "2025-12-08T10:30:00Z"
    }
  ]
}
```

---

## Pending Reservations

### GET `/api/get_pending_reservations.php`
Get pending reservations (librarians only).

**Response:**
```json
{
  "success": true,
  "reservations": [
    {
      "id": 1,
      "user_name": "Juan Dela Cruz",
      "room_name": "Discussion Room 1",
      "reservation_date": "2025-12-15",
      "start_time": "09:00:00",
      "end_time": "11:00:00"
    }
  ]
}
```

---

## Error Responses

All endpoints return error responses in this format:

```json
{
  "success": false,
  "message": "Error description"
}
```

### HTTP Status Codes
- `200` - Success
- `400` - Bad Request (missing/invalid parameters)
- `401` - Unauthorized (not logged in)
- `403` - Forbidden (insufficient permissions)
- `500` - Server Error

---

## Authentication Requirements

All endpoints require:
- Active session (established via `/auth/login.php`)
- Valid user role (student or librarian)

Role-specific permissions:
- **Students**: Can only see their own reservations, feedback, and create bookings
- **Librarians**: Can see all data, approve reservations, and log violations

---

## Example Request (cURL)

```bash
# Login first
curl -b cookies.txt -c cookies.txt \
  -X POST http://localhost/ub-lrc-dims/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"student@ub.edu.ph","password":"password123"}'

# Then make authenticated requests
curl -b cookies.txt \
  http://localhost/ub-lrc-dims/api/get_reservations.php
```

---

## Rate Limiting

Currently not implemented. Consider adding for production use to prevent abuse.

## Caching

No caching headers implemented. Consider adding for GET endpoints if needed.
