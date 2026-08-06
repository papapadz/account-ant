# 01 — Fix Backend Artisan Directory Context & NativePHP App Key

**What to build:** Resolve the `Could not open input file: artisan` error and `Unsupported cipher or incorrect key length` PHP exception during NativePHP startup by ensuring `artisan` is executed within `src/backend/` and `src/backend/.env` contains a valid generated `APP_KEY`.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Ensure `artisan` commands are executed inside `src/backend/` where the `artisan` script resides.
- [ ] Generate and set a valid 32-byte base64 `APP_KEY` in `src/backend/.env` using `php artisan key:generate`.
- [ ] Verify `php artisan native:run` executes without encrypter or key length error messages.
