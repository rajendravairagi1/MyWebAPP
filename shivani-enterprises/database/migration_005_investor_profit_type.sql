-- Run this ONLY if you already applied migration_004_investors.sql on this
-- database (i.e. investor_transactions already exists with a 2-value
-- type column: 'investment','payout'). If you're setting up fresh or
-- haven't run migration_004 yet, skip this file - migration_004 already
-- has the corrected 3-value type.
--
-- Splits the old "payout" (which mixed profit and payment together) into
-- two separate entries: "profit" (credited/owed to the investor, adds to
-- balance) and "payment" (actual cash handed over, subtracts from
-- balance). Existing "payout" rows are treated as "payment" (money that
-- was actually paid out) - re-enter any unpaid accrued profit separately
-- as a "profit" entry if needed.
ALTER TABLE investor_transactions MODIFY COLUMN type ENUM('investment','payout','profit','payment') NOT NULL;
UPDATE investor_transactions SET type = 'payment' WHERE type = 'payout';
ALTER TABLE investor_transactions MODIFY COLUMN type ENUM('investment','profit','payment') NOT NULL;

-- If you added "Profit Credited" or "Payment Paid" entries through the app
-- BEFORE running this migration, the database didn't have those types yet,
-- so (on a non-strict MySQL config) it silently saved them with a blank
-- type instead of erroring - they'll show as blank/₹0 in totals. Find them
-- with:
--   SELECT * FROM investor_transactions WHERE type = '';
-- then fix each one by its amount/note (adjust the WHERE to match your
-- row), e.g.:
--   UPDATE investor_transactions SET type = 'profit' WHERE type = '' AND amount = 15000;
