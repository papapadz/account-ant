# 02 — Standardized SSR-Safe Currency & Date Formatting

**What to build:** Create an SSR-safe currency and date formatting utility that produces identical character output on server and client side during hydration.

**Blocked by:** 01 — Client-Side State Hydration Synchronization (`<ClientOnly>` Wrappers)

**Status:** completed

- [x] Standardize currency formatting function in composable (`toFixed(2)` regex character matching)
- [x] Ensure date string outputs match SSR and client environments
