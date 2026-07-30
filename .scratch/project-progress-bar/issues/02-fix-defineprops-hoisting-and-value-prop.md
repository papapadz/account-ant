# 02 — Fix defineProps hoisting error and restore value prop compatibility in ProgressBar.vue

**What to build:** Move module-level constants (`DEFAULT_THRESHOLDS` and `SIZE_CLASSES`) into a normal `<script lang="ts">` block to fix Vue SFC compilation, and restore `value` prop support as an alias for `percentage`.

**Blocked by:** None — can start immediately

**Status:** completed

- [x] Move `DEFAULT_THRESHOLDS` and `SIZE_CLASSES` to `<script lang="ts">`.
- [x] Add `value?: number` back to `defineProps` and compute effective percentage from `percentage` or `value`.
- [x] Ensure SFC compiles without errors and both `projects/index.vue` (`:value`) and `project/[id].vue` (`:percentage`) render properly.
