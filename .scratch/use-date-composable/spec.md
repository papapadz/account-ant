# Feature Specification: Date, Time, and DateTime Composable (`useDate.ts`)

## Problem Statement

Currently, date, time, and datetime formatting across the application relies on custom string manipulation or inconsistent formatting methods. The application needs a unified, locale-aware, high-performance composable that uses `Intl.DateTimeFormat` for standard parsing and formatting.

## Solution

Build a `useDate` composable in `src/frontend/app/composables/useDate.ts` that provides centralized formatting and parsing helpers for dates, times, and datetimes utilizing native browser `Intl.DateTimeFormat` APIs.

## User Stories

1. As a user, I want dates throughout the app to be formatted consistently using locale settings, so that I can read project and accounting timelines clearly.
2. As a user, I want time values formatted consistently (12-hour / 24-hour options via `Intl.DateTimeFormat`), so that financial entry timestamps are clear.
3. As a user, I want datetime values (date + time) formatted cleanly with short/medium/full styles, so that I can audit transaction histories.
4. As a developer, I want helper methods like `formatDate`, `formatTime`, `formatDateTime`, `relativeTime`, and `parseDate` in a reactive Nuxt composable (`useDate`), so that I can format date values easily in templates.
5. As a developer, I want fallback handling for invalid date strings or `null`/`undefined` values, so that UI rendering never crashes.

## Implementation Decisions

- **Module**: New composable file `src/frontend/app/composables/useDate.ts`.
- **Locale Reactive State**: Use Nuxt state (`useState('app_locale', () => 'en-US')`) to maintain active locale configuration.
- **Intl.DateTimeFormat Instance Caching**: Cache `Intl.DateTimeFormat` instances by locale and format options to maximize formatting performance.
- **Formatting APIs Provided**:
  - `formatDate(value: Date | string | number, options?: Intl.DateTimeFormatOptions): string`
  - `formatTime(value: Date | string | number, options?: Intl.DateTimeFormatOptions): string`
  - `formatDateTime(value: Date | string | number, options?: Intl.DateTimeFormatOptions): string`
  - `formatRelative(value: Date | string | number): string` (using `Intl.RelativeTimeFormat`)
  - `parseDate(value: string | number | Date): Date | null`
  - `setLocale(locale: string): void`
- **Graceful Fallbacks**: If invalid or empty inputs are passed, return `'N/A'` or empty string gracefully without throwing runtime errors.

## Testing Decisions

- **Testing Seam**: Direct composable invocation seam (`useDate()`).
- **Target Module**: `src/frontend/app/composables/useDate.ts`.
- **Prior Art**: Composable unit tests in `src/frontend`. Tests verify date formatting with valid ISO strings, numeric timestamps, `Date` objects, custom options, invalid inputs, and locale changes.

## Out of Scope

- Third-party heavy date libraries (e.g. moment.js, dayjs). All logic must use native `Intl` standards.
- Timezone selector UI components (only formatting composable logic is in scope).

## Further Notes

- Leverages SSR and hydration compatibility in Nuxt 3/4.
