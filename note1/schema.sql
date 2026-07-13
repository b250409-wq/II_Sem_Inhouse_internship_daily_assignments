-- =============================================================================
-- Notes App — Database Schema
-- Import this file into MySQL before using the app, e.g.:
--   mysql -u root -p < schema.sql
-- =============================================================================

CREATE DATABASE IF NOT EXISTS notes_app
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE notes_app;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100)  NOT NULL,
  email         VARCHAR(150)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- notes  (each note belongs to exactly one user)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notes (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  title      VARCHAR(60)  NOT NULL,
  body       VARCHAR(300) NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME     NULL,
  CONSTRAINT fk_notes_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  INDEX idx_notes_user (user_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------------
-- contact_messages  (submissions from the public contact form)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL,
  message    TEXT         NOT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
