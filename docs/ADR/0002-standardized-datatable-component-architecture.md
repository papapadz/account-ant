# ADR 0002: Standardized Data Table Component & Dual-Mode Responsive Architecture

## Status
Proposed (Under Grilling Review)

## Context
In AccountAnt, data tables are the primary interface for managing enterprise ledgers, project funds, journal transactions, and chart of accounts. Previously, tables were implemented inconsistently across pages (some using raw HTML `<table>` elements with horizontal scroll wrappers, others using partial custom components). 

On mobile devices, wide tables with 6-9 columns suffered from severe compression, truncated text, unreadable headers, and awkward horizontal scroll interactions. On desktop displays, dense cell padding and missing column width constraints caused layout squishing.

## Decision Drivers
1. **Uniform Visual & UX Language**: All data representations across AccountAnt must adhere to a single, high-contrast design system ([ui-ux-pro-max](file:///c:/laragon/www/account-ant/.agents/skills/ui-ux-pro-max/SKILL.md)).
2. **Mobile Readability**: Tables must automatically transform on mobile viewports (< 768px) into accessible, readable stacked cards without breaking column slots or action buttons.
3. **Desktop Space & Hierarchy**: Desktop tables require generous cell padding (`py-4.5 px-5 lg:px-6`), prominent high-contrast headers, explicit column width hints, and mono currency formatting.
4. **Developer Productivity & DRY**: Pages should declare columns and cell slots without re-inventing pagination, search, sorting, or mobile card transformations.

## Key Architectural Decisions Under Grilling Review

### 1. Mobile Responsive Rendering Strategy
- **Option A (Recommended — Dual-Mode Auto Transformation)**: `<UiDataTable>` automatically renders an HTML `<table>` on medium+ screens (`hidden md:table`) and transforms each row into a structured mobile card (`block md:hidden`) using slot fallback heuristics (`primaryColumn`, `badgeColumn`, `bodyColumns`, `actionColumn`).
- **Option B (Explicit Mobile Slot)**: Require pages to pass a custom `#mobile-item` template for every table.
- **Option C (Pure Overflow Scroll)**: Keep standard `<table>` with horizontal scroll wrapper `overflow-x-auto` across all screen sizes.

### 2. Data Processing & Pagination Strategy
- **Option A (Recommended — Hybrid Client/Server Interface)**: `<UiDataTable>` supports client-side array processing (default for current scale) while exposing `@page-change`, `@sort-change`, `@search` events for future backend pagination.
- **Option B (Strict Client-Only)**: Only accept full array in `items` prop.
- **Option C (Strict Server-Only)**: Force every page to manage pagination state externally via API calls.

### 3. Header Action & Filter Integration
- **Option A (Recommended — `#header-actions` Slot)**: Integrate a flexible header action slot beside the search bar for status pills, view toggles, and export buttons within `<UiDataTable>`'s top toolbar container.
- **Option B (External Header Container)**: Force pages to construct their own top toolbar wrapper above `<UiDataTable>`.

---

## Consequences

### Positive
- 100% of data tables across AccountAnt share the exact same responsive behavior, typography, and accessibility standard.
- Mobile users receive high-contrast, touch-friendly card lists without horizontal scroll friction.
- Desktop users enjoy readable typography, bold headers, and mono currency alignment.

### Negative / Trade-offs
- Automatic mobile card fallback heuristics assume the first column represents the primary entity title/avatar; complex multi-entity cells require slot customization.
