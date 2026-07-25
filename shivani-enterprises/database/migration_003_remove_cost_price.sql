-- Rolls back migration_002_cost_price.sql.
-- Run this ONLY if you already applied migration_002_cost_price.sql on
-- this database - if you never ran that one, skip this file entirely.
ALTER TABLE products DROP COLUMN cost_price;
ALTER TABLE sale_items DROP COLUMN cost_price;
