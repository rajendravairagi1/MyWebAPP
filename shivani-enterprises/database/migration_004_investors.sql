-- Adds the Investor profit-share module to an existing database.
-- Safe to run once (CREATE TABLE IF NOT EXISTS).
CREATE TABLE IF NOT EXISTS investors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  owner_id INT UNSIGNED NOT NULL COMMENT 'The admin/super admin this investor belongs to - never shared across users',
  name VARCHAR(150) NOT NULL,
  mobile VARCHAR(20) DEFAULT NULL,
  notes TEXT DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_investors_owner FOREIGN KEY (owner_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS investor_transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  investor_id INT UNSIGNED NOT NULL,
  type ENUM('investment','profit','payment') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  txn_date DATE NOT NULL,
  note VARCHAR(255) DEFAULT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_itxn_investor FOREIGN KEY (investor_id) REFERENCES investors(id) ON DELETE CASCADE,
  CONSTRAINT fk_itxn_created_by FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
