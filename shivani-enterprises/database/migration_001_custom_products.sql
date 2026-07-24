-- Run this once via phpMyAdmin if you already installed the app before
-- "Other / Custom Product" support was added. Allows a sale line item to
-- skip the product catalog (product_id becomes nullable).
ALTER TABLE sale_items
  MODIFY product_id INT UNSIGNED DEFAULT NULL,
  DROP FOREIGN KEY fk_items_product;

ALTER TABLE sale_items
  ADD CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;
