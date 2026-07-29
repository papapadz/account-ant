# ADR 0014: State & City Address Selection and Projects Sidebar Navigation

## Context

The system previously embedded the complete Enterprise Projects List (with cards, table view, search, and status filters) inside the root Dashboard page (`/`). Additionally, the Create Project modal relied on plain text inputs for street and city, which caused inconsistencies with backend location models (`App\Models\Address\State`, `App\Models\Address\City`, and `App\Models\Accounting\Project`).

## Decision

1. **Normalized Location Selection**:
   - Integrated cascading **State** and **City** dropdown selection in the `ProjectFormModal` component.
   - Binds `state_id` to filter available `cities` from `/cities` API endpoint (`City::with(['state', 'country'])`).
   - Binds `city_id` on the `Project` model, while capturing `street`, `barangay`, and `zip` code via user input fields.

2. **Dedicated `/projects` Navigation**:
   - Moved the full filterable Enterprise Projects List into a dedicated Nuxt page at `/projects` (`src/frontend/app/pages/projects/index.vue`).
   - Added a top-level "Projects" link in the main drawer sidebar navigation (`layouts/default.vue`).
   - Streamlined `/` to serve as a focused executive Dashboard (high-level KPIs, financial charts, and quick active project preview cards).

## Consequences

- **Backend Data Consistency**: Project creation now populates normalized `city_id` references matching backend relational schemas.
- **Improved UX & Navigation**: Users can access a dedicated projects catalog page from the sidebar with cards and table view toggles while maintaining a clean dashboard layout.
