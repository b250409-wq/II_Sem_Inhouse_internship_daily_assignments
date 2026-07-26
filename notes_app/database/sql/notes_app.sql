-- =============================================
-- Notes App with Tags - Database Schema
-- Database: notes_app
-- =============================================
CREATE DATABASE IF NOT EXISTS notes_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE notes_app;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  avatar VARCHAR(255) DEFAULT NULL,
  theme ENUM('light','dark','system') DEFAULT 'system',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB;

-- Notes
CREATE TABLE notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content LONGTEXT,
  is_pinned TINYINT(1) DEFAULT 0,
  is_favorite TINYINT(1) DEFAULT 0,
  is_archived TINYINT(1) DEFAULT 0,
  is_deleted TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user (user_id),
  INDEX idx_pinned (is_pinned),
  INDEX idx_favorite (is_favorite),
  INDEX idx_archived (is_archived),
  FULLTEXT idx_search (title, content)
) ENGINE=InnoDB;

-- Tags
CREATE TABLE tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(50) NOT NULL,
  color VARCHAR(20) DEFAULT '#6366f1',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_user_tag (user_id, name)
) ENGINE=InnoDB;

-- Note-Tag pivot
CREATE TABLE note_tags (
  note_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (note_id, tag_id),
  FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contact Messages
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Password resets
CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_token (token)
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message VARCHAR(255) NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sessions (optional persistent session store)
CREATE TABLE sessions (
  id VARCHAR(128) PRIMARY KEY,
  user_id INT DEFAULT NULL,
  payload TEXT,
  last_activity INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed admin (password: Admin@123)
INSERT INTO users (username, email, password, role) VALUES
('admin','admin@notesapp.local', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlWXPPn0M0kM6nCUdWyH4vN8P8kQ8u', 'admin');
