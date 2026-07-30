# Feature Specification Addition: ISO Date String (`YYYY-MM-DD`) helper in `useDate.ts`

## Problem Statement
Form inputs (`<input type="date">`), API payload params, and system date fields require dates in strict `YYYY-MM-DD` ISO format. Standard localized formatting routines like `formatDate` output locale-specific strings (e.g. `"Oct 24, 2024"`), which are unsuitable for HTML date inputs or ISO API query params.

## Solution
Add a dedicated `formatISODate` / `toISODateString` helper function to `src/frontend/app/composables/useDate.ts` that safely converts any `DateInput` (Date, string, number) into a standardized `YYYY-MM-DD` string format using native zero-padded date component extraction or `en-CA` `Intl.DateTimeFormat` configuration.

## User Stories
1. As a developer, I want a `formatISODate(value)` function in `useDate`, so that I can bind date values directly to `<input type="date">` elements.
2. As a developer, I want `formatISODate(value)` to handle `null`, `undefined`, and invalid dates gracefully, returning `'N/A'` or empty string without crashing.

## Implementation Decisions
- **Module**: `src/frontend/app/composables/useDate.ts`.
- **API Signature**: `formatISODate(val: DateInput, fallback = 'N/A'): string`.
- **Implementation**: Parses date via `parseDate(val)`. If date is valid, constructs `YYYY-MM-DD` using `getFullYear()`, `getMonth() + 1` (zero padded), and `getDate()` (zero padded).
- **Return Type**: `string` (`"2026-07-30"` or `fallback`).

## Testing Decisions
- **Testing Seam**: Direct composable invocation seam (`const { formatISODate } = useDate()`).
- **Test Scenarios**: Valid ISO string (`"2026-07-30T13:50:00Z"` -> `"2026-07-30"`), Date object, timestamp integer, null/undefined -> fallback value.

## Out of Scope
- Timezone conversions for ISO strings (assumes local date representation unless UTC flag is specified).
