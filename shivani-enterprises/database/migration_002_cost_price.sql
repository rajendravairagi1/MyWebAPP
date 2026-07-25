-- Run this once via phpMyAdmin if you already installed the app before
-- cost-price / profit tracking was added.
ALTER TABLE products
  ADD COLUMN cost_price DECIMAL(12,2) NOT NULL DEFAULT 0
  COMMENT 'What this costs us to buy - used to work out profit, never shown to the customer'
  AFTER default_price;

ALTER TABLE sale_items
  ADD COLUMN cost_price DECIMAL(12,2) NOT NULL DEFAULT 0
  COMMENT 'Actual cost for this sale (decided at sale time, can differ from the product default) - internal only, never printed on the customer invoice'
  AFTER price;
