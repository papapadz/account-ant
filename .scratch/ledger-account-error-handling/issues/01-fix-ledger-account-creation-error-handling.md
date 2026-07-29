# 01 — Fix Ledger Account Creation Silence & Add UI Error Handling

**What to build:** 
When creating a new ledger account in `accounts.vue`, form submission must asynchronously await the API call, display a loading state on the submit button, display human-readable error messages in an error alert banner inside the modal if creation fails (preserving entered form data), and only close the modal and clear inputs upon successful creation.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] `handleCreateAccount` in `accounts.vue` is updated to be `async` and awaits `accounting.addLedgerAccount`.
- [x] Form submit button shows a loading/disabled state (`isSubmitting`) during request execution.
- [x] API or validation errors are caught in a `try...catch` block, setting `errorMessage` and displaying an error alert banner inside the modal.
- [x] User input fields remain intact on failure so the user can correct errors without re-typing.
- [x] Modal closes and form resets only when the account creation API request succeeds.
- [x] End-to-end flow is verified for both successful creation and error handling scenarios.
