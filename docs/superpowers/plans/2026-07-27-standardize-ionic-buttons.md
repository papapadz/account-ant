# Standardize Frontend Buttons Component Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor and standardize all frontend buttons across AccountAnt into a unified, reusable `Button.vue` component (`src/frontend/app/components/ui/Button.vue`) with custom CSS theme bridging, active touch feedback micro-animations, and full semantic HTML support as specified in [ADR 0003](file:///c:/laragon/www/account-ant/docs/ADR/0003-standardized-button-component-architecture.md).

**Architecture:** Following our Nuxt + Ionic architecture rules (`nuxt-ionic-custom-ui`), we build visual elements as custom Vue 3 SFCs using semantic `<button>` elements styled with Tailwind CSS and CSS custom properties, while reserving Ionic (`IonPage`, `IonContent`, `IonModal`) for structural and navigation primitives.

**Tech Stack:** Vue 3 SFC, Nuxt 3/4 (`srcDir: 'app'`), Ionic Vue (`@ionic/vue`), Tailwind CSS v4, TypeScript.

## Global Constraints

- Implement `Button.vue` in `src/frontend/app/components/ui/Button.vue` using semantic HTML `<button>` to ensure proper form handling and Tailwind utility compatibility without Shadow DOM styling conflicts.
- Maintain dark/light mode compatibility using CSS custom properties (`--color-primary`, `--bg-surface`, `--border-color`, `--text-main`).
- Support 5 standard variants (`primary`, `secondary`, `outline`, `danger`, `ghost`) and 3 sizes (`sm`, `md`, `lg`).
- Retain all existing click handlers, loading spinners, form submission behaviors (`type="submit"`), and slot contents.
- Do NOT break existing page layouts or responsive alignments.

---

### Task 1: Refactor `Button.vue` and Enhance CSS Design Tokens

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/components/ui/Button.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/assets/css/main.css`
- Reference: `c:/laragon/www/account-ant/docs/ADR/0003-standardized-button-component-architecture.md`

**Interfaces:**
- Consumes: Tailwind utilities, CSS design tokens in `main.css`.
- Produces: `<Button>` component supporting `variant` ('primary' | 'secondary' | 'outline' | 'danger' | 'ghost'), `size` ('sm' | 'md' | 'lg'), `type` ('button' | 'submit' | 'reset'), `disabled`, `loading`, and default/icon slots (`icon-left`, `icon-right`).

- [ ] **Step 1: Update `main.css` with button variant classes and micro-animation utilities**

Enhance `.btn-primary`, `.btn-secondary`, `.btn-outline`, `.btn-danger`, and `.btn-ghost` classes in `src/frontend/app/assets/css/main.css` to bridge AccountAnt dark/light mode theme variables with active press scaling and focus ring styles.

- [ ] **Step 2: Update `Button.vue` component implementation**

Refactor `src/frontend/app/components/ui/Button.vue`:
- Ensure proper typed props (`variant`, `size`, `type`, `disabled`, `loading`).
- Add accessible loading state spinner with `animate-spin`.
- Support `icon-left` and `icon-right` slots.
- Forward `@click` events cleanly.

- [ ] **Step 3: Verify `Button.vue` in a sample component or build check**

Ensure `Button.vue` compiles cleanly without Vue/TypeScript errors.

---

### Task 2: Replace raw `<button>` elements in Layouts and Core UI Components

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/components/Modal.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/components/ui/DataTable.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/layouts/default.vue`

**Interfaces:**
- Consumes: `<Button>` from `~/components/ui/Button.vue`.
- Produces: Modal header close buttons, table pagination/action buttons, and sidebar/theme toggle buttons powered by `<Button>`.

- [ ] **Step 1: Update `Modal.vue` close button**

Replace raw HTML close `<button>` with `<Button variant="ghost" size="sm">`.

- [ ] **Step 2: Update `DataTable.vue` pagination & filter buttons**

Replace pagination Previous/Next raw buttons and quick action buttons with `<Button>`.

- [ ] **Step 3: Update `default.vue` layout buttons**

Replace sidebar toggle, mobile menu toggle, and theme switch buttons with `<Button variant="ghost">`.

---

### Task 3: Replace raw `<button>` elements in Core Management & Project Pages

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/index.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/projects/index.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/project/[id].vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/management/accounts.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/management/funds.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/management/items.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/management/journal.vue`

**Interfaces:**
- Consumes: `<Button>` component across all data management views.
- Produces: Consistent standardized buttons for modal triggers, form actions, and record mutations.

- [ ] **Step 1: Update `index.vue` dashboard buttons**

Replace header action buttons and filter/tab buttons with `<Button>`.

- [ ] **Step 2: Update `projects/index.vue` and `project/[id].vue` buttons**

Replace project creation trigger, fund allocation buttons, journal posting buttons, and modal submission buttons with `<Button>`.

- [ ] **Step 3: Update Management Pages (`accounts.vue`, `funds.vue`, `items.vue`, `journal.vue`)**

Replace header "New Entry" buttons, table action buttons, and modal submit/cancel buttons with `<Button>`.

---

### Task 4: Replace raw `<button>` elements in Auth & Settings Pages

**Files:**
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/login.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/register.vue`
- Modify: `c:/laragon/www/account-ant/src/frontend/app/pages/settings.vue`

- [ ] **Step 1: Update `login.vue` and `register.vue` buttons**

Replace sign-in, registration submit, and back buttons with `<Button variant="primary" :loading="..." type="submit">`.

- [ ] **Step 2: Update `settings.vue` form action buttons**

Replace profile update, company settings save, affiliation update, API settings save, and test connection buttons with `<Button>`.

---

### Task 5: End-to-End Verification & Build Testing

- [ ] **Step 1: Run Nuxt Build Validation**

Execute `npm run dev` or `npx nuxi prepare` to verify no compilation errors exist.

- [ ] **Step 2: Visual Inspection & Verification**

Verify button styling, hover feedback, active press feedback, and responsive layout across pages.
