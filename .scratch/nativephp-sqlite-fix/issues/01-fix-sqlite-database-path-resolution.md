# 01 — Fix NativePHP SQLite Database Path Resolution

**What to build:** Ensure `nativephp` SQLite database connection resolves a valid path (falling back to `database_path('nativephp.sqlite')`) when executing CLI commands such as `php artisan native:migrate:fresh` outside of the active Electron desktop window.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] Ensure `config('database.connections.nativephp.database')` does not evaluate to `null`.
- [x] Verify `php artisan native:migrate:fresh --seed` runs without `parseDatabasePath(): Argument #1 ($path) must be of type string, null given` TypeError.
