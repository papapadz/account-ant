# 01 — Node Engine Version & Compatibility Configuration

**What to build:** Configure `package.json` `engines` field and `.npmrc` / `.nvmrc` to align Node engine requirements (`>=20.17.0`) and suppress EBADENGINE warnings during `npm install` and `npm run dev`.

**Blocked by:** None — can start immediately.

**Status:** completed

- [x] Add `engines` specification in `package.json`
- [x] Add `.npmrc` configuration `engine-strict=false`
