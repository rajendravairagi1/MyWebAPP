-- HazriBook server database. Import once in phpMyAdmin (select your database
-- first, then Import). Do NOT upload this file to the website folder.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS agencies (
  id          CHAR(36)     NOT NULL PRIMARY KEY,
  name        VARCHAR(200) NOT NULL,
  plan_id     VARCHAR(20)  NOT NULL DEFAULT 'solo',
  seq         BIGINT       NOT NULL DEFAULT 0,         -- last change number handed out
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id                CHAR(36)     NOT NULL PRIMARY KEY,
  agency_id         CHAR(36)     NOT NULL,
  name              VARCHAR(150) NOT NULL,
  email             VARCHAR(190) NULL,
  mobile            VARCHAR(20)  NULL,
  password_hash     VARCHAR(255) NOT NULL,
  kind              ENUM('owner','staff','manager') NOT NULL DEFAULT 'staff',
  scope_company_id  CHAR(36)     NULL,                 -- manager: the one company they may see
  is_active         TINYINT(1)   NOT NULL DEFAULT 1,
  last_login        DATETIME     NULL,
  created_at        DATETIME     NOT NULL,
  UNIQUE KEY uq_email (email),
  UNIQUE KEY uq_mobile (mobile),
  KEY ix_agency (agency_id),
  CONSTRAINT fk_users_agency FOREIGN KEY (agency_id) REFERENCES agencies(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tokens (
  token_hash   CHAR(64)    NOT NULL PRIMARY KEY,       -- sha256 of the token; the token itself is never stored
  user_id      CHAR(36)    NOT NULL,
  agency_id    CHAR(36)    NOT NULL,
  device_name  VARCHAR(100) NOT NULL DEFAULT '',
  created_at   DATETIME    NOT NULL,
  last_used    DATETIME    NOT NULL,
  expires_at   DATETIME    NOT NULL,
  KEY ix_user (user_id),
  CONSTRAINT fk_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed / attempted sign-ins and sign-ups, used for rate limiting.
CREATE TABLE IF NOT EXISTS attempts (
  id        BIGINT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
  kind      VARCHAR(20) NOT NULL,                      -- login | register
  k         CHAR(64)    NOT NULL,                      -- sha256 of identifier or ip
  at        DATETIME    NOT NULL,
  KEY ix_attempt (kind, k, at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Every synced row of the app lives here as JSON, one row per record.
CREATE TABLE IF NOT EXISTS records (
  agency_id   CHAR(36)     NOT NULL,
  tbl         VARCHAR(40)  NOT NULL,
  rid         VARCHAR(64)  NOT NULL,
  data        LONGTEXT     NOT NULL,                   -- JSON text
  deleted     TINYINT(1)   NOT NULL DEFAULT 0,
  seq         BIGINT       NOT NULL,
  updated_by  CHAR(36)     NULL,
  updated_at  DATETIME     NOT NULL,
  PRIMARY KEY (agency_id, tbl, rid),
  KEY ix_pull (agency_id, seq)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
