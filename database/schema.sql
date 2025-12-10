-- UB LRC-DIMS Database Schema
-- Run this in phpMyAdmin or MySQL CLI to create tables

CREATE DATABASE IF NOT EXISTS ub_lrc_dims;
USE ub_lrc_dims;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    ub_mail VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ub_mail (ub_mail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Librarians table
CREATE TABLE IF NOT EXISTS librarians (
    librarian_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    ub_mail VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ub_mail (ub_mail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Legacy users table (kept for backward compatibility with existing code)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'librarian') NOT NULL DEFAULT 'student',
    full_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(50) NOT NULL,
    status ENUM('available', 'occupied') NOT NULL DEFAULT 'available',
    capacity INT NOT NULL CHECK (capacity >= 1 AND capacity <= 10),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reservations table
CREATE TABLE IF NOT EXISTS reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    librarian_id INT NULL,
    room_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL CHECK (start_time >= '07:00:00' AND start_time <= '17:00:00'),
    end_time TIME NOT NULL CHECK (end_time >= '07:00:00' AND end_time <= '17:00:00'),
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    waitlist_status ENUM('none', 'queued', 'notified') NOT NULL DEFAULT 'none',
    purpose TEXT,
    group_members TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (librarian_id) REFERENCES librarians(librarian_id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_librarian (librarian_id),
    INDEX idx_room (room_id),
    INDEX idx_status (status),
    INDEX idx_date (reservation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    condition_status ENUM('clean', 'dirty', 'damaged') NOT NULL,
    feedback_text TEXT,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    reviewed_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES librarians(librarian_id) ON DELETE SET NULL,
    INDEX idx_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Violations table (librarian logs)
CREATE TABLE IF NOT EXISTS violations (
    violation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    librarian_id INT NOT NULL,
    room_id INT NULL,
    violation_type ENUM('no-show', 'late', 'damaged property', 'overcapacity') NOT NULL,
    status ENUM('pending', 'cleared') NOT NULL DEFAULT 'pending',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (librarian_id) REFERENCES librarians(librarian_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE SET NULL,
    INDEX idx_student (student_id),
    INDEX idx_librarian (librarian_id),
    INDEX idx_violation_type (violation_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Waitlist table
CREATE TABLE IF NOT EXISTS waitlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    preferred_date DATE NOT NULL,
    preferred_time TIME NOT NULL,
    status ENUM('waiting', 'notified', 'expired') NOT NULL DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_room (room_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
