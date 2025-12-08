# Screenshots & Visual Documentation Guide

## How to Capture Screenshots

This guide explains which pages and features should be documented with screenshots for a complete visual reference.

---

## Screenshots to Capture

### 1. Landing Page
**File Name:** `01-landing-page.png`

**How to Capture:**
1. Go to: `http://localhost/ub-lrc-dims/index.php`
2. Take full-page screenshot showing:
   - Header with branding
   - Hero section with video background
   - Feature buttons (News, Support, Learn More)
   - Login modals at bottom

**Why Important:** First impression, entry point for both users

---

### 2. Student Login Modal
**File Name:** `02-student-login-modal.png`

**How to Capture:**
1. Click "Student Login" button on landing page
2. Screenshot shows:
   - Email input field
   - Password input field
   - Submit button
   - "Forgot password?" link
   - Modal styling

**Why Important:** Authentication entry point

---

### 3. Student Dashboard
**File Name:** `03-student-dashboard.png`

**How to Capture:**
1. Login as: student@ub.edu.ph / password123
2. You're redirected to dashboard
3. Screenshot shows:
   - Statistics cards (Reservations, Pending, Approved, Feedback)
   - Available rooms count
   - Waitlist status
   - Recent activity feed
   - Sidebar navigation

**Why Important:** Core interface, shows key metrics

---

### 4. Reservations Page - Student View
**File Name:** `04-reservations-student.png`

**How to Capture:**
1. Click "Reservations" in sidebar
2. Shows:
   - "New Reservation" button
   - Table with columns: Date, Time, Room, Purpose, Status
   - Action buttons (View, Cancel)
   - Empty state or sample reservations
   - Filtering options

**Why Important:** Main booking interface

---

### 5. Create Reservation Modal
**File Name:** `05-create-reservation-form.png`

**How to Capture:**
1. Click "New Reservation" button
2. Modal shows:
   - Room dropdown
   - Date picker
   - Time range (Start/End)
   - Purpose text area
   - Student IDs input
   - Submit button
   - Validation messages

**Why Important:** Core user interaction

---

### 6. Rooms Page
**File Name:** `06-rooms-page.png`

**How to Capture:**
1. Click "Rooms" in sidebar
2. Shows:
   - Room cards with:
     - Room name
     - Capacity indicator
     - Status badge (Available/Maintenance)
     - Description
   - Filter options
   - Search bar

**Why Important:** Room discovery and browsing

---

### 7. Feedback Page - Student
**File Name:** `07-feedback-student.png`

**How to Capture:**
1. Click "Feedback" in sidebar
2. Shows:
   - Feedback submission form
   - Text area for message
   - Submit button
   - Past feedback list with status
   - No console errors

**Why Important:** User satisfaction tracking

---

### 8. User Profile Page
**File Name:** `08-profile-page.png`

**How to Capture:**
1. Click "Profile" in sidebar (students only)
2. Shows:
   - User information form:
     - Email (read-only)
     - Full name (editable)
     - Role (read-only)
   - Save button
   - User statistics
   - Recent activity

**Why Important:** Account management

---

### 9. History Page
**File Name:** `09-history-page.png`

**How to Capture:**
1. Click "History" in sidebar
2. Shows:
   - Timeline of activities:
     - Reservation created/approved/rejected
     - Feedback submitted
     - Profile updated
   - Timestamps
   - Filter options

**Why Important:** Activity tracking and audit trail

---

### 10. Librarian Dashboard
**File Name:** `10-librarian-dashboard.png`

**How to Capture:**
1. Logout and login as: staff@ub.edu.ph / password123
2. Different dashboard shows:
   - Pending approvals count
   - Recent violations
   - System statistics
   - Quick actions

**Why Important:** Librarian overview

---

### 11. Approvals Page
**File Name:** `11-approvals-page.png`

**How to Capture:**
1. Click "Approvals" in sidebar
2. Shows:
   - Table of pending reservations:
     - Student name
     - Room
     - Date/Time
     - Purpose
   - Approve/Reject buttons
   - Empty state or pending items

**Why Important:** Librarian workflow

---

### 12. Rooms Management Page - Librarian
**File Name:** `12-rooms-management-librarian.png`

**How to Capture:**
1. Click "Rooms" in sidebar (as librarian)
2. Shows:
   - "Add Room" button (not visible to students)
   - Room list with edit/delete options
   - Room details editable
   - Status management

**Why Important:** Admin functionality

---

### 13. Add Room Modal
**File Name:** `13-add-room-form.png`

**How to Capture:**
1. Click "Add Room" button
2. Shows:
   - Room name input
   - Capacity input
   - Description textarea
   - Submit button
   - Validation

**Why Important:** Content management

---

### 14. Violations Page
**File Name:** `14-violations-page.png`

**How to Capture:**
1. Click "Violations" in sidebar (librarians only)
2. Shows:
   - "Log Violation" form or button
   - Violations table with:
     - Student name
     - Room
     - Description
     - Date logged
   - History of violations

**Why Important:** Enforcement tool

---

### 15. Log Violation Modal
**File Name:** `15-log-violation-form.png`

**How to Capture:**
1. Click "Log Violation" button
2. Shows:
   - Student selector
   - Room selector (optional)
   - Description textarea
   - Submit button

**Why Important:** Admin action

---

### 16. Mobile View - Sidebar
**File Name:** `16-mobile-sidebar.png`

**How to Capture:**
1. Open browser on mobile or use DevTools responsive mode
2. Click hamburger menu (☰)
3. Sidebar slides in showing:
   - Navigation links
   - Student name
   - Logout button

**Why Important:** Responsive design

---

### 17. Mobile View - Dashboard
**File Name:** `17-mobile-dashboard.png`

**How to Capture:**
1. On mobile/responsive view
2. Shows dashboard layout:
   - Stacked cards (not side-by-side)
   - Touch-friendly buttons
   - Readable text
   - Proper padding/spacing

**Why Important:** Mobile usability

---

### 18. Error Message Example
**File Name:** `18-error-message.png`

**How to Capture:**
1. Try to create reservation with invalid time
2. Shows error message styling:
   - Red background
   - Clear message
   - Proper positioning

**Why Important:** Error UX

---

### 19. Success Message Example
**File Name:** `19-success-message.png`

**How to Capture:**
1. Successfully create/update something
2. Shows success toast/message:
   - Green background
   - Checkmark icon
   - Clear message

**Why Important:** Feedback UX

---

### 20. Learn More Page
**File Name:** `20-learn-more-page.png`

**How to Capture:**
1. Click "Learn More" from landing page
2. Shows informational content page

**Why Important:** Marketing/documentation page

---

## Directory Structure for Screenshots

```
screenshots/
├── 01-landing-page.png
├── 02-student-login-modal.png
├── 03-student-dashboard.png
├── 04-reservations-student.png
├── 05-create-reservation-form.png
├── 06-rooms-page.png
├── 07-feedback-student.png
├── 08-profile-page.png
├── 09-history-page.png
├── 10-librarian-dashboard.png
├── 11-approvals-page.png
├── 12-rooms-management-librarian.png
├── 13-add-room-form.png
├── 14-violations-page.png
├── 15-log-violation-form.png
├── 16-mobile-sidebar.png
├── 17-mobile-dashboard.png
├── 18-error-message.png
├── 19-success-message.png
└── 20-learn-more-page.png
```

---

## Tools for Capturing Screenshots

### Browser Built-in Tools
- **Firefox:** Right-click → "Take Screenshot"
- **Chrome:** Ctrl+Shift+S → Select area
- **Edge:** Same as Chrome

### Specialized Tools
- **Snagit** - Professional tool with annotations
- **ShareX** - Free, open-source, customizable
- **Lightshot** - Quick online/offline screenshot tool

### For Animated Gifs
- **ScreenToGif** - Record and convert to GIF
- **Gifcam** - Simple GIF recording

---

## Screenshot Best Practices

### Resolution & Size
- Capture at **1920x1080** (full HD) for desktop
- **375x667** for mobile (iPhone size)
- Keep file size under 500KB (compress if needed)

### Content Guidelines
- ✅ Include full page if possible
- ✅ Show realistic data (test user records)
- ✅ Include UI chrome (headers, sidebars)
- ❌ Don't include sensitive data (real emails if possible)
- ❌ Don't show debug information

### Naming Convention
- Use sequential numbers: `01-`, `02-`, etc.
- Use descriptive names: `03-student-dashboard.png`
- Use lowercase with hyphens

### Annotation Tips
- Add red boxes around important features
- Add numbers to steps in workflows
- Add arrows pointing to key elements
- Use 12pt font for readability

---

## How to Use Screenshots in Documentation

### Markdown Example
```markdown
## Student Dashboard

The main interface for students showing key statistics and recent activity.

![Student Dashboard](screenshots/03-student-dashboard.png)

**Features shown:**
- Total reservations count
- Pending approvals
- Approved bookings
- Recent feedback submissions
- Activity timeline
```

### HTML Example
```html
<figure>
  <img src="screenshots/03-student-dashboard.png" 
       alt="Student Dashboard" 
       width="800">
  <figcaption>Figure 3: Student Dashboard with statistics</figcaption>
</figure>
```

---

## Workflow Documentation (Step-by-Step)

### Making a Reservation Workflow
```
Step 1: Click "New Reservation"
  📸 05-create-reservation-form.png
  
Step 2: Select room and time
  (Same modal, show filled form)
  
Step 3: Enter student IDs
  (Show group members)
  
Step 4: Click Submit
  (Show success message)
  
Step 5: View in My Reservations
  📸 04-reservations-student.png
  (Shows new pending reservation)
```

### Approval Workflow
```
Step 1: Librarian views pending approvals
  📸 11-approvals-page.png
  
Step 2: Click Approve or Reject
  (Show action)
  
Step 3: Student sees updated status
  📸 04-reservations-student.png
  (Shows approved reservation)
```

---

## Visual Elements to Highlight

### Color Scheme
- **Primary:** Maroon #7d0920
- **Accent:** Gold #EAA851
- **Success:** Green
- **Error:** Red
- **Neutral:** Gray

### Key UI Patterns
- Status badges (color-coded)
- Modal dialogs
- Toast notifications
- Responsive grid layout
- Sidebar navigation
- Data tables with sorting

---

## Creating an Image Gallery HTML

```html
<!DOCTYPE html>
<html>
<head>
  <title>UB-LRC-DIMS Screenshots</title>
  <style>
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .gallery-item {
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
    }
    .gallery-item img {
      width: 100%;
      height: auto;
    }
    .gallery-item figcaption {
      padding: 10px;
      background: #f5f5f5;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <h1>UB-LRC-DIMS Screenshot Gallery</h1>
  <div class="gallery">
    <figure class="gallery-item">
      <img src="screenshots/01-landing-page.png" alt="Landing Page">
      <figcaption>Landing Page</figcaption>
    </figure>
    <figure class="gallery-item">
      <img src="screenshots/03-student-dashboard.png" alt="Dashboard">
      <figcaption>Student Dashboard</figcaption>
    </figure>
    <!-- More figures... -->
  </div>
</body>
</html>
```

---

## Maintenance

- Update screenshots when UI changes
- Keep 1-2 recent versions for comparison
- Archive old screenshots with version number
- Update references in documentation

---

## Summary

**Key Screenshots to Capture (Top 10):**
1. Landing page (entry point)
2. Student login (authentication)
3. Student dashboard (overview)
4. Reservations list (core feature)
5. Create reservation (user action)
6. Rooms browser (search feature)
7. Librarian dashboard (admin view)
8. Approvals queue (admin action)
9. Mobile view (responsive design)
10. Error message (error handling)

These 10 cover the main workflows and demonstrate all major features and functionality.

