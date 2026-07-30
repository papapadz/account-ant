# 01 — Fix foreign key constraint error in AccountItemController and AccountingSeeder

**What to build:** Fix `SQLSTATE[23000]: Integrity constraint violation: 19 FOREIGN KEY constraint failed` when calling `GET /api/account-items` by replacing hardcoded `ledger_account_id` integers (40, 20, 10, 30) in `AccountItemController.php`, `AccountingSeeder.php`, and `LedgerItemSeeder.php` with dynamic `LedgerAccount` model lookups by `account_code`.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Update `AccountItemController.php` index fallback to resolve `LedgerAccount` by `account_code` before creating fallback `AccountItem` records.
- [x] Update `AccountingSeeder.php` to resolve `LedgerAccount` by `account_code` for `AccountItem` foreign key associations.
- [x] Update `LedgerItemSeeder.php` fallback foreign keys to use dynamic model resolution.
