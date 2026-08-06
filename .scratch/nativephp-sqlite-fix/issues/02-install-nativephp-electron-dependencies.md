# 02 — Install NativePHP Electron Dependencies & Info.plist

**What to build:** Ensure Electron binaries and `Info.plist` are properly installed and accessible under `vendor/nativephp/desktop/resources/electron/node_modules/electron/dist/Electron.app/Contents/Info.plist` so `php artisan native:run` can patch the application bundle without throwing `file_get_contents(...) Failed to open stream` errors.

**Blocked by:** 01 — Fix NativePHP SQLite Database Path Resolution

**Status:** completed

- [x] Ensure Electron npm dependencies are installed inside NativePHP desktop resources directory.
- [x] Verify `Electron.app/Contents/Info.plist` exists before patching in `RunCommand.php`.
