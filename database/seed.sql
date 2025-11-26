-- Sample seed data for testing
USE ub_lrc_dims;

-- Insert sample users (passwords are 'password123' hashed with bcrypt cost 10)
INSERT INTO users (email, password_hash, role, full_name) VALUES
('student@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Juan Dela Cruz'),
('student2@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Maria Santos'),
('staff@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', 'Librarian Admin'),
('lib@ub.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', 'Anna Reyes');

-- Insert sample rooms
INSERT INTO rooms (name, capacity, status, description) VALUES
('Room A', 6, 'available', 'Small discussion room with whiteboard'),
('Room B', 10, 'available', 'Medium room with projector'),
('Room C', 4, 'available', 'Quiet study room'),
('Room D', 8, 'maintenance', 'Large conference room (under maintenance)');

-- Insert sample reservations
INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, status, purpose) VALUES
(1, 1, '2025-11-27', '10:00:00', '12:00:00', 'approved', 'Group study session'),
(1, 2, '2025-11-28', '14:00:00', '16:00:00', 'pending', 'Project discussion'),
(2, 1, '2025-11-26', '09:00:00', '11:00:00', 'approved', 'Research collaboration'),
(2, 3, '2025-11-29', '13:00:00', '15:00:00', 'pending', 'Thesis preparation');

-- Insert sample feedback
INSERT INTO feedback (user_id, message, status) VALUES
(1, 'Room A needs better lighting', 'new'),
(2, 'Great facility, very helpful staff', 'reviewed'),
(1, 'Suggestion: add more power outlets in Room B', 'new');

-- Insert sample violations
INSERT INTO violations (user_id, room_id, logged_by, description) VALUES
(2, 1, 3, 'Food found in restricted area'),
(1, 2, 3, 'No-show after confirmed reservation');
