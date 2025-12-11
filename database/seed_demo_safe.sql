-- Safe seed data that disables FK checks for entire session
-- Run this after schema.sql

USE ub_lrc_dims;

SET FOREIGN_KEY_CHECKS = 0;
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- Clear existing data
DELETE FROM waitlist;
DELETE FROM violations;
DELETE FROM feedback;
DELETE FROM reservations;
DELETE FROM rooms;
DELETE FROM librarians;
DELETE FROM students;

-- Reset auto-increment counters
ALTER TABLE students AUTO_INCREMENT = 1;
ALTER TABLE librarians AUTO_INCREMENT = 1;
ALTER TABLE rooms AUTO_INCREMENT = 1;
ALTER TABLE reservations AUTO_INCREMENT = 1;
ALTER TABLE feedback AUTO_INCREMENT = 1;
ALTER TABLE violations AUTO_INCREMENT = 1;
ALTER TABLE waitlist AUTO_INCREMENT = 1;

-- Insert test students (password for all: password123)
INSERT INTO students (full_name, ub_mail, password) VALUES
('Juan Dela Cruz', 'student@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K'),
('Maria Santos', 'student2@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K'),
('Pedro Reyes', 'student3@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K'),
('Ana Garcia', 'student4@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K'),
('Carlos Mendoza', 'student5@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K');

-- Insert test librarians (password for all: password123)
INSERT INTO librarians (full_name, ub_mail, password) VALUES
('Ms. Teresa Cruz', 'staff@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K'),
('Mr. Roberto Silva', 'lib@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K');

-- Insert rooms
INSERT INTO rooms (room_name, capacity, status, description) VALUES
('Discussion Room 1', 10, 'available', 'Main discussion room with whiteboard and projector'),
('Discussion Room 2', 10, 'available', 'Large discussion room suitable for team meetings and presentations');

-- Insert reservations with various statuses
INSERT INTO reservations (student_id, room_id, reservation_date, start_time, end_time, purpose, status, librarian_id, approved_at, group_members) VALUES
-- Approved reservations
(1, 1, '2025-12-15', '09:00:00', '11:00:00', 'Group study for Calculus exam', 'approved', 1, '2025-12-10 10:30:00', '2021-00001,2021-00002,2021-00003'),
(2, 2, '2025-12-15', '13:00:00', '15:00:00', 'Team project discussion for Software Engineering', 'approved', 1, '2025-12-10 11:00:00', '2021-00004,2021-00005'),
(3, 1, '2025-12-16', '10:00:00', '12:00:00', 'Thesis consultation meeting', 'approved', 1, '2025-12-10 12:00:00', '2021-00006,2021-00007'),
(4, 2, '2025-12-16', '14:00:00', '16:00:00', 'Review session for Midterm exams', 'approved', 2, '2025-12-10 13:00:00', '2021-00008'),
(5, 1, '2025-12-17', '08:00:00', '10:00:00', 'Club meeting - Computer Society', 'approved', 2, '2025-12-10 14:00:00', '2021-00009,2021-00010,2021-00011'),
-- Pending reservations (for approval queue demonstration)
(1, 2, '2025-12-18', '09:00:00', '11:00:00', 'Final exam study group', 'pending', NULL, NULL, '2021-00001,2021-00002'),
(2, 1, '2025-12-18', '13:00:00', '15:00:00', 'Project presentation practice', 'pending', NULL, NULL, '2021-00003,2021-00004'),
(3, 2, '2025-12-19', '10:00:00', '12:00:00', 'Research group meeting', 'pending', NULL, NULL, '2021-00005'),
(4, 1, '2025-12-19', '14:00:00', '16:00:00', 'Capstone project discussion', 'pending', NULL, NULL, '2021-00006,2021-00007,2021-00008'),
(5, 2, '2025-12-20', '09:00:00', '11:00:00', 'Study session for Finals', 'pending', NULL, NULL, '2021-00009'),
-- Past completed reservations
(1, 1, '2025-12-05', '09:00:00', '11:00:00', 'Quiz review session', 'completed', 1, '2025-12-04 15:00:00', '2021-00001'),
(2, 2, '2025-12-06', '13:00:00', '15:00:00', 'Group assignment work', 'completed', 1, '2025-12-05 10:00:00', '2021-00002,2021-00003'),
(3, 1, '2025-12-07', '10:00:00', '12:00:00', 'Lab report collaboration', 'completed', 2, '2025-12-06 14:00:00', '2021-00004'),
-- Rejected reservation
(4, 1, '2025-12-08', '09:00:00', '11:00:00', 'Personal study time', 'rejected', 1, '2025-12-07 16:00:00', NULL),
-- Cancelled reservation
(5, 2, '2025-12-09', '14:00:00', '16:00:00', 'Team meeting - cancelled by student', 'cancelled', NULL, NULL, '2021-00005,2021-00006');

-- Insert feedback with various statuses
INSERT INTO feedback (student_id, condition_status, feedback_text, reviewed_at, reviewed_by) VALUES
(1, 'clean', 'The new discussion rooms are excellent! Very clean and well-equipped. Thank you!', '2025-12-10 14:00:00', 1),
(2, 'clean', 'Could we have more power outlets in Discussion Room 2? Sometimes we run out when everyone brings laptops.', '2025-12-10 15:00:00', 1),
(3, 'clean', 'The air conditioning in Discussion Room 1 is too cold. Can it be adjusted?', NULL, NULL),
(4, 'clean', 'Suggestion: Add a booking calendar display at the entrance so students can see availability.', NULL, NULL),
(5, 'dirty', 'The whiteboard markers in Discussion Room 2 need replacement. They are almost dried out.', '2025-12-10 16:00:00', 2),
(1, 'clean', 'Thank you for the quick response to my previous feedback! Much appreciated.', NULL, NULL),
(2, 'clean', 'The online reservation system is very convenient. Keep up the good work!', '2025-12-11 09:00:00', 1),
(3, 'clean', 'Would it be possible to extend reservation times to 3 hours for thesis groups?', NULL, NULL);

-- Insert violations (librarian logs)
INSERT INTO violations (student_id, room_id, librarian_id, violation_type, description, status) VALUES
(3, 1, 1, 'damaged property', 'Room left untidy after use. Food wrappers and drinks left on table.', 'cleared'),
(4, 2, 2, 'late', 'Exceeded reservation time by 30 minutes without prior notice.', 'pending'),
(1, 1, 1, 'overcapacity', 'Noise complaint from adjacent area. Student reminded about noise policy.', 'cleared');

-- Insert waitlist entries
INSERT INTO waitlist (student_id, room_id, preferred_date, preferred_time, status) VALUES
(1, 2, '2025-12-18', '13:00:00', 'waiting'),
(2, 1, '2025-12-19', '09:00:00', 'waiting'),
(3, 1, '2025-12-20', '14:00:00', 'waiting');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
SET AUTOCOMMIT = 1;

-- Display summary
SELECT 'Database seeded successfully!' as Status;
SELECT COUNT(*) as 'Total Students' FROM students;
SELECT COUNT(*) as 'Total Librarians' FROM librarians;
SELECT COUNT(*) as 'Total Rooms' FROM rooms;
SELECT COUNT(*) as 'Total Reservations' FROM reservations;
SELECT COUNT(*) as 'Pending Approvals' FROM reservations WHERE status = 'pending';
SELECT COUNT(*) as 'Total Feedback' FROM feedback;
SELECT COUNT(*) as 'Total Violations' FROM violations;
SELECT COUNT(*) as 'Total Waitlist' FROM waitlist;
