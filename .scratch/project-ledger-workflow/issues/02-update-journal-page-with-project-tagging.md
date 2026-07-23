# 02 — Update Main Journal Ledger Page with Optional Project Tagging

**What to build:** Extend the main journal entry page (`/management/journal.vue`) to allow accountants to optionally tag journal entries to a specific Project, automatically validating that the selected Fund Account belongs to that project when a project is selected.

**Blocked by:** 01 — Fix Nuxt 3 Dynamic Project Routing & Workbench Page

**Status:** ready-for-agent

- [ ] Add optional "Associated Project" dropdown to the main journal posting modal on `/management/journal.vue`
- [ ] Display Project Name badge on journal entries table when an entry is tagged to a project
- [ ] Enforce fund source filtering so selecting a project restricts fund choices to assigned project funds
