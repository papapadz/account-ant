# 03 — Laravel PHP 8.2 Platform Execution & Migration Fix (`person_id` Column)

**What to build:** Execute Artisan CLI commands using Laragon PHP 8.2 (`C:\laragon\bin\php\php-8.2.22-Win32-vs16-x64\php.exe`) instead of default CLI PHP 7.4.29 to satisfy Laravel 11/Composer requirement, and verify the `person_id` column addition in `2026_07_23_030316_create_person_affiliations_table.php`.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [x] PHP 8.2 CLI binary path verified
- [x] `$table->unsignedBigInteger('person_id')` defined before foreign key definition in `create_person_affiliations_table.php`
- [x] Migration executes cleanly via PHP 8.2 binary
