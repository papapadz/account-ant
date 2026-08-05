# Spec: Flat White Borderless Modal Component Variant

**Date:** 2026-08-05  
**Status:** Proposed  
**Author:** Antigravity AI  

## Problem Statement

When viewing formal financial statement reports such as the **Project General Ledger Statement** in the preview modal (`src/frontend/app/pages/project/[id].vue`), the standard modal container introduces dark backgrounds, heavy drop shadows (`shadow-2xl`), and dark borders (`border-[var(--border-color)]`). This visual framing clashes with paper report previews and makes the statement look like a dark application popover rather than a clean, flat document view.

## Solution

Enhance `src/frontend/app/components/Modal.vue` with flexible props:
1. `variant`: `'default' | 'flat-white'` (default `'default'`).
2. `maxWidth`: `string` (default `'max-w-lg'`).

When `variant="flat-white"` is passed:
- The modal container renders with a **plain flat white background** (`bg-white`).
- Container borders and drop shadows are removed (`border-0 shadow-none`).
- Header text and close icon use clean dark text styling (`text-slate-900` / `text-slate-400 hover:text-slate-900`) with a subtle `border-slate-100` header divider.
- The `maxWidth` prop allows scaling the modal container (e.g. `max-w-4xl` for accounting reports).

## User Stories

1. As a project manager, I want the print ledger modal to display with a plain flat white background without borders or shadows, so that the statement preview feels like a paper document preview.
2. As a developer, I want `Modal.vue` to support `variant` and `maxWidth` props with backwards-compatible defaults, so that all existing modals continue working without changes.

## Implementation Decisions

### 1. Component Props in `Modal.vue`

```typescript
export interface ModalProps {
  isOpen: boolean
  title: string
  variant?: 'default' | 'flat-white'
  maxWidth?: string
}

const props = withDefaults(defineProps<ModalProps>(), {
  variant: 'default',
  maxWidth: 'max-w-lg'
})
```

### 2. Styling Rules in `Modal.vue`

- **Default Variant (`variant="default"`)**:
  - Outer card: `bg-[var(--bg-modal)] border border-[var(--border-color)] text-[var(--text-main)] shadow-2xl rounded-xl`
  - Header: `border-b border-[var(--border-color)]`
  - Title: `text-[var(--text-main)]`
  - Close button: `text-[var(--text-muted)] hover:text-[var(--text-main)]`

- **Flat White Variant (`variant="flat-white"`)**:
  - Outer card: `bg-white border-0 shadow-none text-slate-900 rounded-xl`
  - Header: `border-b border-slate-100 bg-white`
  - Title: `text-slate-900`
  - Close button: `text-slate-400 hover:text-slate-900`
  - Body: `bg-white`

### 3. Page Usage in `src/frontend/app/pages/project/[id].vue`

Update the Project General Ledger Statement modal invocation:

```html
<Modal
  :is-open="isPrintBalanceSheetModalOpen"
  title="Project General Ledger Statement"
  variant="flat-white"
  max-width="max-w-4xl"
  @close="isPrintBalanceSheetModalOpen = false"
>
```

Update internal `#printable-balance-sheet` container in `[id].vue` to omit redundant borders/shadows (`border-0 shadow-none bg-white`).

## Testing Decisions

- **Backward Compatibility Test:** Verify existing modals (`ProjectFormModal`, `funds.vue`, `accounts.vue`) retain dark theme styling when `variant` is omitted.
- **Flat White Variant Test:** Verify print ledger modal renders with flat white background, no drop shadows, no borders, and `max-w-4xl` width.
- **Type Safety Test:** Run `vue-tsc --noEmit` to ensure type checks pass cleanly.

## Out of Scope

- Modifying modal animation durations or backdrop blur levels on screen.
