# White Lotus Trading — Platform Change Requirements

**Purpose:** This document is a scoped requirements list for an AI coding
assistant to implement against the existing codebase (frontend site +
`/admin` panel described in `USER_GUIDE.md`). Each item includes context,
required behavior, and acceptance criteria so it can be implemented and
verified independently. Items are grouped by theme and ordered by priority
within each group.

**How to use this file:** Work top to bottom within a group. Do not start a
`P1` item in a later group until all `P0` items above it are done, unless
told otherwise. Re-read the relevant section of `USER_GUIDE.md` before
starting each item — the guide is the source of truth for current behavior.

---

## 1. Rate Limiting — Move from Session-Based to IP-Based

**Context:** The inquiry form currently limits submissions to 3 per 30
minutes *per session*. This is trivially bypassed by clearing cookies or
opening a private/incognito window, so it provides almost no real
protection — the honeypot field is doing more of the actual anti-spam work.

### 1.1 [P0] IP-based rate limiting on inquiry submission
- Track submission counts keyed by client IP address (not session ID).
- Limit: 3 submissions per 30 minutes per IP, matching the existing
  documented limit.
- Store counts in a fast, TTL-capable store (Redis if available; otherwise
  a DB table with a scheduled cleanup job / expiry timestamp column).
- Respect `X-Forwarded-For` / `X-Real-IP` correctly if the app sits behind a
  reverse proxy or load balancer — do not trust these headers unless the
  proxy is verified to strip/overwrite them from client input.
- On limit exceeded, return HTTP 429 with a user-facing message: "You've
  reached the submission limit. Please try again in a few minutes."
- **Acceptance:** Submitting 4 inquiries from the same IP within 30 minutes
  in 4 different browser sessions (cleared cookies each time) results in
  the 4th being rejected.

### 1.2 [P0] IP-based rate limiting on admin login
- Currently: 5 failed attempts locks the *account* for 15 minutes. Add a
  parallel IP-based limit: 10 failed attempts from a single IP across
  *any* username within 15 minutes triggers a temporary IP block/CAPTCHA
  requirement, independent of which account is being targeted.
- This prevents an attacker from cycling through many usernames to avoid
  the per-account lockout, or DoS-locking a known admin account by
  spraying failed logins from unrelated IPs is unaffected — but this stops
  the reverse case (one IP hammering many accounts).
- **Acceptance:** 10 failed logins from one IP against 10 different
  usernames within 15 minutes blocks further attempts from that IP,
  regardless of username tried.

### 1.3 [P1] Newsletter subscribe rate limiting
- The newsletter subscribe form (footer + inquiry page) currently has no
  documented rate limit at all. Add IP-based limiting: 5 subscribe attempts
  per hour per IP.
- **Acceptance:** 6th subscribe attempt from the same IP within an hour
  returns a 429 with a friendly message.

### 1.4 [P1] Configurable limits
- Move all rate-limit thresholds (attempt counts, time windows) into
  environment/config variables rather than hardcoding them, so they can be
  tuned without a code deploy.
- **Acceptance:** Changing `INQUIRY_RATE_LIMIT_MAX` and
  `INQUIRY_RATE_LIMIT_WINDOW_MINUTES` in config changes enforced behavior
  without a code change.

---

## 2. Audit Trail

**Context:** Deletes and edits across Products, Blog, Pages, Categories,
and Subscribers currently happen with no record of who did what, when.
Recovery after an accidental or malicious delete is currently impossible.

### 2.1 [P0] Audit log table
- Create an `audit_log` table: `id, admin_user_id, action
  (create|update|delete), entity_type (product|blog_post|page|category|
  subscriber|inquiry|home_settings), entity_id, changes (JSON diff or
  before/after snapshot), ip_address, created_at`.
- Write an entry on every create, update, and delete performed through the
  admin panel — including Home Settings saves.
- **Acceptance:** Editing a product's price and then checking the audit log
  shows an entry with the old and new price, the admin user, and a
  timestamp.

### 2.2 [P0] Soft-delete for Products, Blog Posts, and Pages
- Replace hard deletes with a `deleted_at` timestamp column for these three
  entity types. Deleted items are excluded from all public and admin list
  views by default but remain recoverable.
- Add a "Trash" or "Recently Deleted" view per section (Products, Blog,
  Pages) showing soft-deleted items from the last 30 days, with a
  **Restore** action.
- Add a scheduled job to hard-delete (purge) items soft-deleted more than
  30 days ago.
- **Acceptance:** Deleting a product removes it from `/admin/products` and
  the public site, but it appears in the Trash view and can be restored,
  reappearing in both.
- **Note:** Subscribers and Inquiries can remain hard-delete for now (lower
  recovery value, higher privacy sensitivity) — flag this as a deliberate
  exception in code comments.

### 2.3 [P1] Audit log viewer in admin
- Add `/admin/audit-log` — a read-only, paginated, filterable
  (by entity type, admin user, date range) view of the audit log.
- Each row links to the affected entity where still viewable (skip the
  link if the entity was hard-deleted/purged).
- **Acceptance:** An admin can filter to "all actions by user X in the last
  7 days" and see accurate results.

### 2.4 [P2] Reply history for inquiries
- Currently a reply overwrites/appends to a single admin-notes field.
  Change this to a `inquiry_replies` table (one row per reply) so multiple
  back-and-forth replies are preserved individually with timestamps.
- **Acceptance:** Sending two replies to the same inquiry shows both in the
  detail modal, in order, each with its own timestamp.

---

## 3. Architecture / Operational Gaps

### 3.1 [P0] Bulk product operations
- Add CSV import/export for products (columns matching the product form
  fields: name, SKU, unit, division, status, price, stock, description,
  category, meta description, technical specs as flattened key:value pairs).
- Add bulk actions to the product list view: select multiple rows via
  checkboxes, then bulk **Set Status** (Active/Draft) and bulk **Delete**.
- Import should validate rows and report per-row errors (e.g., "Row 14:
  missing required field 'Name'") rather than failing the whole import.
- **Acceptance:** A CSV of 50 products imports successfully, and a CSV with
  one intentionally broken row imports the other 49 and reports the one
  failure with its row number and reason.

### 3.2 [P1] Structured, filterable technical specs
- Currently technical specs are free-text key-value pairs with no
  structure, meaning the frontend product-search cannot filter by spec
  (e.g., "5 Tons capacity", "380V").
- Define a `spec_definitions` table per category (or globally) with typed
  fields: `key, label, data_type (text|number|enum), unit (optional)`.
- Product technical specs then reference these definitions instead of
  arbitrary key names, enabling:
  - Consistent labeling/units across products of the same category.
  - A "Filter by spec" UI on `/products` (e.g., a Capacity range filter for
    HVAC products).
- **Acceptance:** Two chillers with a "Capacity (Tons)" spec can both be
  found via a numeric range filter on `/products`, and the spec label is
  rendered consistently on both product detail pages.
- **Migration note:** Existing free-text specs should be migrated into a
  generic "Additional Notes" text field per product so no data is lost;
  new structured specs are opt-in going forward.

### 3.3 [P1] Product variants
- HVAC products commonly ship in multiple capacity/voltage configurations
  under one product line. Currently each variant would need to be a fully
  separate product with a duplicated name/description.
- Add an optional `product_variants` table: `product_id, variant_label,
  sku_suffix, price_override (nullable), stock`. If a product has
  variants, the detail page shows a variant selector instead of a single
  price/stock pair.
- **Acceptance:** A product with 3 variants (e.g., "3 Ton", "5 Ton",
  "10 Ton") shows one product card in the catalog but a variant dropdown
  on its detail page, each with its own price and stock.

### 3.4 [P2] Basic CRM linkage for inquiries
- Add a lightweight `customers` table keyed by normalized email. When an
  inquiry is submitted, link it to a customer record (create if new).
- On the inquiry detail view, show "Previous inquiries from this contact"
  as a list.
- **Acceptance:** Submitting two inquiries from the same email shows both
  linked together in the admin inquiry detail view.

### 3.5 [P2] Role-based access control
- Replace the single Super Admin role with at least: **Super Admin**
  (full access), **Editor** (Products, Blog, Pages, Media — no Settings,
  no Subscribers, no user management), **Support** (Inquiries only, read
  and reply, no delete).
- Add an `/admin/users` section (Super Admin only) to create/edit/deactivate
  admin accounts and assign roles.
- **Acceptance:** An account with the Editor role can create a blog post
  but gets a 403 (not just a hidden nav link) when hitting
  `/admin/settings` or `/admin/subscribers` directly by URL.

---

## 4. UX / Content

### 4.1 [P0] Autosave on Product and Blog Post forms
- The slide-out modals for Products and Blog Posts currently have no
  autosave — a crash or accidental navigation loses all unsaved input.
- Implement periodic (every 15–20 seconds of inactivity, or on field blur)
  autosave to a draft state, distinct from the published/active record.
  Restore the draft if the form is reopened before submission.
- **Acceptance:** Opening the product form, typing a description, then
  force-closing the browser tab and reopening `/admin/products` and the
  same product's edit form shows the typed description restored.

### 4.2 [P1] Direct upload in Media Library
- Currently files can only enter the Media Library via a TinyMCE editor or
  the product image field — there's no direct upload button on
  `/admin/media` itself.
- Add an **Upload** button and drag-and-drop zone directly on the Media
  Library page, using the same allowed-file-type and size validation as
  existing upload paths.
- **Acceptance:** An admin can upload an image directly from
  `/admin/media` without going through a product or blog form first, and
  it appears in the grid immediately.

### 4.3 [P1] Image handling on upload
- No cropping/resizing currently happens on upload, so inconsistent source
  image dimensions can break card/grid layouts on the frontend.
- On upload, generate at least two derivative sizes (e.g., thumbnail
  ~400px and full ~1600px, format-preserving) server-side, and serve the
  appropriate size per context (list/grid vs. detail page) rather than the
  original file everywhere.
- **Acceptance:** Uploading a 4000×3000px image results in a
  correctly-sized thumbnail being served on `/products`, not the original
  file, verified by checking the response size/dimensions of the image
  actually loaded on that page.

### 4.4 [P2] Confirmation dialogs replaced with proper modals
- Delete confirmations currently rely on the browser's native
  `confirm()` dialog (per the guide). Replace with an in-app confirmation
  modal that:
  - States what will be deleted (name/title of the item).
  - For Products/Blog/Pages (now soft-deletable per 2.2), makes clear the
    item is recoverable from Trash for 30 days.
  - For hard-delete actions (Subscribers, purge-from-Trash), clearly states
    the action is permanent.
- **Acceptance:** Clicking delete on a product shows an in-app modal naming
  the product and mentioning it can be restored from Trash, not a native
  browser `confirm()` popup.

### 4.5 [P2] Search-by-spec and "Clear Filters" parity
- Once 3.2 (structured specs) ships, ensure the `/products` search and
  filter UI is updated to expose spec filters alongside the existing
  Division filter, and that **Clear Filters** resets spec filters too.
- **Acceptance:** Applying a Division filter + a spec filter, then clicking
  Clear Filters, resets both.

---

## 5. Multi-Language Support

**Context:** The company markets "15+ Global Markets" and is based in the
UAE/MENA region, but the site is currently English-only. Priority is
Arabic, given the primary market.

### 5.1 [P0] i18n infrastructure
- Introduce a translation framework (e.g., locale JSON/YAML resource files
  per language) for all static UI strings (nav labels, buttons, form
  labels, error/success messages, footer text).
- Add a language switcher in the header, persisted via a cookie or
  `?lang=` query param, defaulting to browser `Accept-Language` on first
  visit.
- **Acceptance:** Switching the language selector changes all static UI
  text (nav, buttons, form labels) without a page-content change yet.

### 5.2 [P0] RTL layout support for Arabic
- Arabic requires right-to-left layout, not just translated strings. Add
  an `dir="rtl"` mode with a corresponding RTL stylesheet pass (flip
  paddings/margins/flex directions, mirror icons where directional,
  right-align text).
- **Acceptance:** With Arabic selected, the header nav, product grid, and
  forms render correctly mirrored, not just translated left-to-right text
  in a Latin layout.

### 5.3 [P1] Translatable content fields
- Product name/description, blog post title/content/excerpt, and static
  Page title/content need per-language versions, not just UI chrome.
- Add a `locale` column (or a parallel translations table keyed by
  `entity_type, entity_id, locale, field, value`) so admins can enter an
  Arabic name/description alongside the English one.
- Admin forms (Product, Blog Post, Page) get a language tab/toggle to edit
  each locale's content.
- Frontend renders the content in the active locale, falling back to
  English if no translation exists yet (with no broken/blank fields).
- **Acceptance:** A product with only an English description still
  displays correctly (in English) when Arabic is selected; a product with
  both shows the Arabic description when Arabic is selected.

### 5.4 [P1] Localized URLs and SEO
- Add `hreflang` tags for available locales on all localized pages.
- Support locale-prefixed URLs (e.g., `/ar/products`) so each language
  version is independently indexable and linkable, rather than only a
  client-side toggle with no URL change.
- **Acceptance:** `/ar/products` loads the Arabic version directly (no
  client-side flash of English content), and view-source shows correct
  `hreflang` links to the English equivalent.

### 5.5 [P2] Localized email notifications
- Inquiry confirmation emails and newsletter emails currently go out in
  English only. Send them in the locale the visitor was using when they
  submitted the form.
- **Acceptance:** Submitting the inquiry form while in Arabic mode sends
  the confirmation email in Arabic.

---

## Suggested Delivery Order

1. **Section 1** (rate limiting) — small, isolated, immediate security value.
2. **Section 2.1–2.2** (audit log + soft delete) — foundational, other
   features (Trash UI, RBAC context) build on this.
3. **Section 4.1–4.2** (autosave, direct media upload) — high-value,
   low-risk UX fixes, no schema dependencies on other sections.
4. **Section 3.1** (bulk product ops) — addresses the "200+ product lines"
   scale problem directly.
5. **Section 3.2–3.3** (structured specs, variants) — larger schema change,
   sequence after bulk import exists so re-imports aren't needed twice.
6. **Section 5** (i18n) — largest scope; start once the above stabilizes,
   since 5.3 touches every content model.
7. **Remaining P2 items** as capacity allows.

---

*This file should be updated (not replaced) as items are completed — move
finished items to a `## Done` section at the bottom with the completion
date, rather than deleting them, so there's a record of what changed and
when.*
