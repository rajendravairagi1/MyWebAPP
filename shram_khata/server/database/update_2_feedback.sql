-- Run this ONCE in phpMyAdmin (database selected -> Import / SQL) if you already imported schema.sql earlier.
SET NAMES utf8mb4;

-- Messages from a company manager to the agency (owner and supervisors read them).
CREATE TABLE IF NOT EXISTS feedback (
  id          CHAR(36)     NOT NULL PRIMARY KEY,
  agency_id   CHAR(36)     NOT NULL,
  user_id     CHAR(36)     NOT NULL,                   -- the manager who wrote it
  company_id  CHAR(36)     NULL,
  labour_id   CHAR(36)     NULL,                       -- optional: about one worker
  message     TEXT         NOT NULL,
  created_at  DATETIME     NOT NULL,
  KEY ix_fb_agency (agency_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Which people have seen which message (so the unread badge is per person).
CREATE TABLE IF NOT EXISTS feedback_reads (
  feedback_id CHAR(36) NOT NULL,
  user_id     CHAR(36) NOT NULL,
  read_at     DATETIME NOT NULL,
  PRIMARY KEY (feedback_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
