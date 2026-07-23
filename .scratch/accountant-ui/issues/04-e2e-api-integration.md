# 04 — End-to-End API Integration Verification (Nuxt + Laravel Backend)

**What to build:** Validate live API requests between Nuxt composables (`useApi.ts`, `useAuth.ts`, `useAccounting.ts`) and the Laravel REST API server.

**Blocked by:** 01 — Nuxt 4 Source Directory Restructuring, 03 — Laravel PHP 8.2 Platform Execution & Migration Fix

**Status:** ready-for-agent

- [x] Nuxt `apiBase` runtime config targets active Laravel API URL
- [x] REST requests handle authentication token cookies
- [x] Fallback demo mode engages seamlessly if backend server is offline
