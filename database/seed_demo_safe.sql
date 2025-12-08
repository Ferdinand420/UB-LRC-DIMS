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
DELETE FROM reservation_students;
DELETE FROM reservations;
DELETE FROM rooms;
DELETE FROM users;

-- Reset auto-increment counters
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE rooms AUTO_INCREMENT = 1;
ALTER TABLE reservations AUTO_INCREMENT = 1;
ALTER TABLE feedback AUTO_INCREMENT = 1;
ALTER TABLE violations AUTO_INCREMENT = 1;
ALTER TABLE waitlist AUTO_INCREMENT = 1;
ALTER TABLE reservation_students AUTO_INCREMENT = 1;

-- Insert test users (password for all: password123)
INSERT INTO users (email, password_hash, role, full_name) VALUES
('student@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'student', 'Juan Dela Cruz'),
('student2@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'student', 'Maria Santos'),
('student3@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'student', 'Pedro Reyes'),
('student4@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'student', 'Ana Garcia'),
('student5@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'student', 'Carlos Mendoza'),
('staff@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'librarian', 'Ms. Teresa Cruz'),
('lib@ub.edu.ph', '$2y$10$ksTAquK/y6AAoe6TWpbcLe.sTBPZ6obroaijAuCWsoHe1d1AwVH4K', 'librarian', 'Mr. Roberto Silva');

-- Insert rooms
INSERT INTO rooms (name, capacity, status, description) VALUES
('Discussion Room 1', 10, 'available', 'Main discussion room with whiteboard and projector'),
('Discussion Room 2', 10, 'available', 'Large discussion room suitable for team meetings and presentations');

-- Insert reservations with various statuses
INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, purpose, status, approved_by, approved_at) VALUES
-- Approved reservations
(1, 1, '2025-11-27', '09:00:00', '11:00:00', 'Group study for Calculus exam', 'approved', 6, '2025-11-26 10:30:00'),
(2, 2, '2025-11-27', '13:00:00', '15:00:00', 'Team project discussion for Software Engineering', 'approved', 6, '2025-11-26 11:00:00'),
(3, 1, '2025-11-28', '10:00:00', '12:00:00', 'Thesis consultation meeting', 'approved', 6, '2025-11-26 12:00:00'),
(4, 2, '2025-11-28', '14:00:00', '16:00:00', 'Review session for Midterm exams', 'approved', 7, '2025-11-26 13:00:00'),
(5, 1, '2025-11-29', '08:00:00', '10:00:00', 'Club meeting - Computer Society', 'approved', 7, '2025-11-26 14:00:00'),
-- Pending reservations (for approval queue demonstration)
(1, 2, '2025-11-30', '09:00:00', '11:00:00', 'Final exam study group', 'pending', NULL, NULL),
(2, 1, '2025-11-30', '13:00:00', '15:00:00', 'Project presentation practice', 'pending', NULL, NULL),
(3, 2, '2025-12-01', '10:00:00', '12:00:00', 'Research group meeting', 'pending', NULL, NULL),
(4, 1, '2025-12-01', '14:00:00', '16:00:00', 'Capstone project discussion', 'pending', NULL, NULL),
(5, 2, '2025-12-02', '09:00:00', '11:00:00', 'Study session for Finals', 'pending', NULL, NULL),
-- Past reservations
(1, 1, '2025-11-20', '09:00:00', '11:00:00', 'Quiz review session', 'approved', 6, '2025-11-19 15:00:00'),
(2, 2, '2025-11-22', '13:00:00', '15:00:00', 'Group assignment work', 'approved', 6, '2025-11-21 10:00:00'),
(3, 1, '2025-11-23', '10:00:00', '12:00:00', 'Lab report collaboration', 'approved', 7, '2025-11-22 14:00:00'),
-- Rejected reservation
(4, 1, '2025-11-25', '09:00:00', '11:00:00', 'Personal study time', 'rejected', 6, '2025-11-24 16:00:00'),
-- Cancelled reservation
(5, 2, '2025-11-26', '14:00:00', '16:00:00', 'Team meeting - cancelled by student', 'cancelled', NULL, NULL);

-- Insert reservation students (sample student ID lists)
INSERT INTO reservation_students (reservation_id, student_id_value) VALUES
(1, '2021001'), (1, '2021002'), (1, '2021003'),
(2, '2022001'), (2, '2022002'), (2, '2022003'), (2, '2022004'),
(3, '2023001'), (3, '2023002'),
(4, '2024001'), (4, '2024002'), (4, '2024003'),
(5, '2025001'), (5, '2025002'), (5, '2025003'), (5, '2025004'), (5, '2025005'),
(6, '2026001'), (6, '2026002'), (6, '2026003'),
(7, '2027001'), (7, '2027002'),
(8, '2028001'), (8, '2028002'), (8, '2028003'),
(9, '2029001'), (9, '2029002'), (9, '2029003'), (9, '2029004'),
(10, '2030001'), (10, '2030002'), (10, '2030003'),
(11, '2031001'),
(12, '2032001'), (12, '2032002'),
(13, '2033001'), (13, '2033002'), (13, '2033003'),
(14, '2034001'), (14, '2034002'),
(15, '2035001'), (15, '2035002'), (15, '2035003');

-- Insert feedback with various statuses
INSERT INTO feedback (user_id, message, status) VALUES
(1, 'The new discussion rooms are excellent! Very clean and well-equipped. Thank you!', 'resolved'),
(2, 'Could we have more power outlets in Discussion Room 2? Sometimes we run out when everyone brings laptops.', 'reviewed'),
(3, 'The air conditioning in Discussion Room 1 is too cold. Can it be adjusted?', 'new'),
(4, 'Suggestion: Add a booking calendar display at the entrance so students can see availability.', 'new'),
(5, 'The whiteboard markers in Discussion Room 2 need replacement. They are almost dried out.', 'reviewed'),
(1, 'Thank you for the quick response to my previous feedback! Much appreciated.', 'new'),
(2, 'The online reservation system is very convenient. Keep up the good work!', 'resolved'),
(3, 'Would it be possible to extend reservation times to 3 hours for thesis groups?', 'new');

-- Insert violations (librarian logs)
INSERT INTO violations (user_id, room_id, logged_by, description) VALUES
(3, 1, 6, 'Room left untidy after use. Food wrappers and drinks left on table.'),
(4, 2, 7, 'Exceeded reservation time by 30 minutes without prior notice.'),
(1, 1, 6, 'Noise complaint from adjacent area. Student reminded about noise policy.');

-- Insert waitlist entries
INSERT INTO waitlist (user_id, room_id, preferred_date, preferred_time, status) VALUES
(1, 2, '2025-11-27', '13:00:00', 'waiting'),
(2, 1, '2025-11-28', '09:00:00', 'waiting'),
(3, 1, '2025-11-29', '14:00:00', 'waiting');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
SET AUTOCOMMIT = 1;

-- Display summary
SELECT 'Database seeded successfully!' as Status;
SELECT COUNT(*) as 'Total Users' FROM users;
SELECT COUNT(*) as 'Total Rooms' FROM rooms;
SELECT COUNT(*) as 'Total Reservations' FROM reservations;
SELECT COUNT(*) as 'Pending Approvals' FROM reservations WHERE status = 'pending';
SELECT COUNT(*) as 'Total Feedback' FROM feedback;
SELECT COUNT(*) as 'Total Violations' FROM violations;
