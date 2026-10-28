-- Adds a per-product toggle to hide a product from the Rate List (PDF/PNG)
-- without deactivating it (an inactive product also disappears from Stock
-- Entry / Sale, which is not always wanted). Defaults to 1 so every
-- existing product keeps showing in the rate list exactly as before.
ALTER TABLE products ADD COLUMN show_in_rate_list TINYINT(1) NOT NULL DEFAULT 1 AFTER is_active;
