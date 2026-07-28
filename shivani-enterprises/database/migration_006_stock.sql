-- Multi-godam stock: warehouses, per-product per-godam stock,
-- movement history, and a godam link on each sale line item.

CREATE TABLE IF NOT EXISTS godams (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    address VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_godam_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_stock (
    product_id INT UNSIGNED NOT NULL,
    godam_id INT UNSIGNED NOT NULL,
    qty DECIMAL(12,2) NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, godam_id),
    KEY idx_ps_godam (godam_id),
    CONSTRAINT fk_ps_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_ps_godam   FOREIGN KEY (godam_id)   REFERENCES godams(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS stock_movements (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED NOT NULL,
    godam_id INT UNSIGNED NOT NULL,
    movement_type ENUM('opening','purchase','sale','adjustment') NOT NULL,
    qty_change DECIMAL(12,2) NOT NULL,
    ref_type VARCHAR(30) DEFAULT NULL,
    ref_id INT UNSIGNED DEFAULT NULL,
    admin_id INT UNSIGNED DEFAULT NULL,
    note VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_sm_product (product_id),
    KEY idx_sm_godam (godam_id),
    KEY idx_sm_ref (ref_type, ref_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE sale_items ADD COLUMN godam_id INT UNSIGNED DEFAULT NULL AFTER product_name;
ALTER TABLE sale_items ADD KEY idx_si_godam (godam_id);
