# Hide Navbars/Sidebars when Logged Out & Fix Input Icon Styling Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Hide the sidebar drawer, top header navbar, and bottom mobile navigation bar when a user is logged out or visiting unauthenticated routes (`/login`, `/register`), and fix icon alignment/padding in input boxes across the app.

**Architecture:** 
1. Set `definePageMeta({ layout: 'auth' })` in `login.vue` (and reinforce auth route detection in `default.vue`).
2. Update `default.vue` layout to conditionally render sidebar, header toolbar, and mobile bottom bar only when `auth.isAuthenticated` is true and current route is not an auth page (`/login`, `/register`).
3. Refine `UiInput.vue` and global input CSS styles to support left/right icon slots, fixing padding (`pl-10` / `pr-10`) so typed text never overlaps icons.

**Tech Stack:** Vue 3, Nuxt 3, Tailwind CSS, TypeScript.

## Global Constraints

- Do not break authenticated layout navigation or mobile drawer behavior.
- Ensure light/dark mode compatibility for all input icons and focus rings.

---

### Task 1: Hide Sidebar & Navbars when Logged Out or on Auth Routes

**Files:**
- Modify: `src/frontend/app/pages/login.vue:185-200`
- Modify: `src/frontend/app/layouts/default.vue:1-35`

- [ ] **Step 1: Set `layout: 'auth'` on `login.vue`**

Add `definePageMeta({ layout: 'auth' })` to `<script setup>` in `login.vue`.

- [ ] **Step 2: Add authentication & route condition checks to `default.vue`**

In `default.vue`, compute `showNavigation = computed(() => auth.isAuthenticated.value && !['/login', '/register'].includes(route.path))`. Wrap `<aside>`, `<header>`, and `<footer>` in `v-if="showNavigation"`.

---

### Task 2: Enhance `UiInput.vue` and Input Icon Styling

**Files:**
- Modify: `src/frontend/app/components/ui/Input.vue:1-55`
- Modify: `src/frontend/app/assets/css/main.css:50-100`

- [ ] **Step 1: Upgrade `UiInput.vue` with `icon-left` and `icon-right` slots**

Support icon slots in `UiInput.vue`, automatically adding `pl-10` when a left icon/prefix is present and `pr-10` when a right icon is present.

- [ ] **Step 2: Verify `input-field` CSS utility class in `main.css`**

Ensure `.input-field` has `relative` positioning support and proper SVG icon vertically centered alignments (`top-1/2 -translate-y-1/2`).

- [ ] **Step 3: Run Frontend Build Verification**

Run `cd src/frontend && npm run build`
Expected: Build passes with 0 errors.
