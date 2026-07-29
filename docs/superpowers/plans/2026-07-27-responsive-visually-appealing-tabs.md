# Responsive and Visually Appealing Tabs Component Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a standardized, highly responsive, visually appealing `<Tabs>` component (`src/frontend/app/components/ui/Tabs.vue`) and upgrade all tab navigation across page views and mobile bottom navigation layout as specified in [ADR 0004](file:///c:/laragon/www/account-ant/docs/ADR/0004-standardized-tabs-component-architecture.md).

**Architecture:** Following our Nuxt + Ionic architecture rules (`nuxt-ionic-custom-ui`) and UI/UX best practices (`ui-ux-pro-max`), we build a custom Vue 3 SFC `<Tabs>` component in `components/ui/` with active pill indicators, micro-animations, horizontal smooth touch scrolling, badge counts, and dark/light mode CSS design tokens across 3 visual variants (`segmented`, `pills`, `underline`).

**Tech Stack:** Vue 3 SFC, Nuxt 3/4 (`srcDir: 'app'`), Ionic Vue (`@ionic/vue`), Tailwind CSS v4, TypeScript.

## Global Constraints

- Create `Tabs.vue` in `src/frontend/app/components/ui/Tabs.vue` supporting `v-model` binding for seamless integration with reactive state (`activeTab`, `statusFilter`, `filterType`).
- Maintain dark/light mode compatibility using CSS custom properties (`--color-primary`, `--bg-surface`, `--bg-sidebar`, `--border-color`, `--text-main`, `--text-muted`).
- Ensure touch target heights are ≥40px with horizontal smooth scrolling and hidden scrollbars (`overflow-x-auto no-scrollbar`).
- Restrict mobile bottom navigation in `default.vue` to `sm:hidden`.
- Do NOT break existing page reactivity or state management.

---

### Task 1: Build the Standardized `<Tabs>` Component

**Files:**
- Create: `c:/laragon/www/account-ant/src/frontend/app/components/ui/Tabs.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/assets/css/main.css`
- Reference: `c:/laragon/www/account-ant/docs/ADR/0004-standardized-tabs-component-architecture.md`

**Interfaces:**
- Consumes: Tailwind utilities, CSS design tokens.
- Produces: `<Tabs>` component with props:
  - `modelValue`: string | number
  - `items`: `TabItem[]` (`{ value: string | number; label: string; icon?: string; badge?: string | number }`)
  - `variant`?: 'pills' | 'segmented' | 'underline'
  - `size`?: 'sm' | 'md'

- [ ] **Step 1: Add `.no-scrollbar` utility to `main.css`**

Add custom CSS helper in `src/frontend/app/assets/css/main.css` to hide webkit/firefox scrollbars while preserving touch panning.

- [ ] **Step 2: Create `Tabs.vue` SFC component**

Write `src/frontend/app/components/ui/Tabs.vue`:
- Implement `v-model` binding (`defineProps` & `defineEmits(['update:modelValue'])`).
- Support active indicator background animation with smooth transitions (`transition-all duration-200`).
- Support badge count pill rendering (e.g., item count, status count).
- Support ARIA tab roles (`role="tablist"`, `role="tab"`) and keyboard navigation.

- [ ] **Step 3: Verify `Tabs.vue` compilation**

Run `npx nuxi prepare` to verify type exports and auto-import.

---

### Task 2: Enhance Mobile Bottom Navigation in `default.vue`

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/layouts/default.vue`

- [ ] **Step 1: Upgrade bottom nav styling in `default.vue`**

Update `IonFooter` / bottom nav bar in `src/frontend/app/layouts/default.vue`:
- Add `sm:hidden` class so mobile bottom nav is hidden on tablet/desktop viewports (where sidebar/split pane is active).
- Add active indicator pill behind active tab icons with emerald glow and text contrast.
- Add backdrop blur glassmorphism effect (`backdrop-blur-xl bg-[var(--bg-sidebar)]/90`).

---

### Task 3: Upgrade Page Tabs across Frontend

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/settings.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/project/[id].vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/management/journal.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/index.vue`

- [ ] **Step 1: Upgrade `settings.vue` section tabs**

Replace ad-hoc tab buttons with `<Tabs v-model="activeTab" :items="settingsTabs" variant="underline" />`.

- [ ] **Step 2: Upgrade `project/[id].vue` section tabs**

Replace inline tab buttons with `<Tabs v-model="activeTab" :items="projectTabs" variant="underline" />` featuring live transaction & fund count badges.

- [ ] **Step 3: Upgrade `journal.vue` filter tabs**

Replace header action filter buttons with `<Tabs v-model="filterType" :items="journalFilterTabs" variant="segmented" size="sm" />`.

- [ ] **Step 4: Upgrade `index.vue` status filter tabs**

Replace status filter buttons with `<Tabs v-model="statusFilter" :items="statusFilterTabs" variant="segmented" size="sm" />`.

---

### Task 4: End-to-End Verification & Validation

- [ ] **Step 1: Run Nuxt Build Check**

Execute `npx nuxi prepare` in `src/frontend` to verify 0 compilation errors.

- [ ] **Step 2: Visual Inspection & Responsive Validation**

Verify smooth horizontal scrolling, active indicator transitions, badge counters, and responsive behavior across mobile (375px), tablet (768px), and desktop viewports.
