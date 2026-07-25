-- Shivani Enterprises - Sales Ledger & CRM
-- Import this file once via phpMyAdmin (or `mysql -u user -p dbname < schema.sql`)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Users (super_admin + admin). Super admin creates admin accounts.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
  name VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  email VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Products (coolers etc.) - super admin managed, with a default price.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  unit VARCHAR(30) NOT NULL DEFAULT 'Piece',
  default_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  cost_price DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'What this costs us to buy - used to work out profit, never shown to the customer',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Customers - each belongs to exactly one admin (allotted by super admin
-- or self-created by the admin). Mobile number is unique (duplicate check).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  shop_name VARCHAR(150) DEFAULT NULL,
  place VARCHAR(150) DEFAULT NULL,
  mobile VARCHAR(20) NOT NULL UNIQUE,
  alt_mobile VARCHAR(20) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  admin_id INT UNSIGNED NOT NULL,
  created_by INT UNSIGNED NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_customers_admin FOREIGN KEY (admin_id) REFERENCES users(id),
  CONSTRAINT fk_customers_created_by FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Documents uploaded against a customer (agreements, ID proof, etc.)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS customer_documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  uploaded_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_doc_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  CONSTRAINT fk_doc_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Sales (invoice header). One sale can have many products (sale_items).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(30) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NOT NULL,
  admin_id INT UNSIGNED NOT NULL,
  sale_date DATE NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  notes VARCHAR(255) DEFAULT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_sales_admin FOREIGN KEY (admin_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Sale line items - price is manually editable per line (bargaining).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sale_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sale_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED DEFAULT NULL COMMENT 'NULL for a one-off "Other / Custom Product" line not in the catalog',
  product_name VARCHAR(150) NOT NULL,
  qty DECIMAL(10,2) NOT NULL DEFAULT 1,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  cost_price DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Actual cost for this sale (decided at sale time, can differ from the product default) - internal only, never printed on the customer invoice',
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Payments received against a customer (optionally tagged to a sale).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  sale_id INT UNSIGNED DEFAULT NULL,
  admin_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  payment_date DATE NOT NULL,
  mode ENUM('cash','upi','bank_transfer','cheque','other') NOT NULL DEFAULT 'cash',
  note VARCHAR(255) DEFAULT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pay_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
  CONSTRAINT fk_pay_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
  CONSTRAINT fk_pay_admin FOREIGN KEY (admin_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Follow-ups / commitments (what product & qty was promised, and when
-- to follow up). Visible per-admin, and rolled up for super admin.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS followups (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  admin_id INT UNSIGNED NOT NULL,
  commitment TEXT DEFAULT NULL,
  follow_up_date DATE NOT NULL,
  follow_up_time TIME DEFAULT NULL,
  status ENUM('pending','done','cancelled') NOT NULL DEFAULT 'pending',
  remarks VARCHAR(255) DEFAULT NULL,
  created_by INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_fu_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  CONSTRAINT fk_fu_admin FOREIGN KEY (admin_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Simple audit trail (who did what).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_log (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  action VARCHAR(150) NOT NULL,
  details VARCHAR(255) DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Company / app settings (single row key-value).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(60) PRIMARY KEY,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES
  ('company_name', 'Shivani Enterprises'),
  ('company_phone', ''),
  ('company_address', ''),
  ('invoice_prefix', 'SE')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Default super admin login: username = superadmin / password = Admin@123
-- CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN.
-- Hash below is password_hash('Admin@123', PASSWORD_DEFAULT) at install time;
-- regenerate with includes/hash_password.php if you prefer a different one.
-- ---------------------------------------------------------------------
INSERT INTO users (role, name, username, email, password_hash, status)
VALUES ('super_admin', 'Super Admin', 'superadmin', 'admin@example.com',
  '$2y$12$FJzBq3oUU1uhJ9632AG9DeUPFa71IE6DUC7ZdO.boFa0e4jTeCij6', 'active')
ON DUPLICATE KEY UPDATE username = username;
