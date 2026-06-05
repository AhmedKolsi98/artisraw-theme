# ArtisRaw theme — Changelog

One line per shipped item (SPEC working rhythm). Newest first.

## Phase 3 — First template end-to-end: the wholesale hub

- `tpl-wholesale-hub.php`: the money page `/olive-wood-wholesale-supplier/` per CONTENT page 2 — 9 sections, one idea each: hero (offer + terms + CTA) → trust strip → quick answer → ready-to-ship SKU grid → QC stats + US/EU import-confidence → mid quote form → core services → micro-FAQ → logistics/Incoterms table + download centre + trusted-client band → end quote form. Form mid + end; every claim → /references/.
- `inc/post-types.php`: `sku` CPT + helpers (`artisraw_get`, `artisraw_sku_to_array`, `artisraw_get_ready_skus`) reading post meta (ACF-compatible keys) with or without ACF; idempotent seed of 6 ready-to-ship SKUs.
- `inc/acf-fields.php`: SKU spec field group (admin UI once ACF is active).
- `inc/images.php`: `artisraw_responsive_image()` (WebP srcset 600/1200/1800, width/height, lazy/eager) + hero `<link rel=preload as=image imagesrcset>`. Hero WebP generated at 3 widths (1800w = 7 KB).
- `inc/schema.php`: `artisraw_product_itemlist()` — ItemList of Product (material/brand/countryOfOrigin/offers, price on request).
- `inc/seo-head.php`: SEO field reader now falls back to post meta (per-page title/meta work pre-ACF). Hub page created with exact title/meta from the SEO block.
- `css/templates.css`: hub hero grid, terms, confidence list, services, forms.
- Progressive enhancement: FAQ answers visible with JS off; quote form `<noscript>` fallback (email + contact).

### Verified (full SPEC §11 on the hub URL)
- SSR: `curl | grep h1` returns the H1; content usable JS-off. Title/meta/canonical exact; HTML 66 KB.
- JSON-LD valid: BreadcrumbList + ItemList (6 Products, required fields) + Organization + WebSite — zero parse errors.
- CWV (4× CPU, 4G throttle): **LCP 0.80 s** · **CLS 0** · JS 5.4 KB gz → INP fine. 1 H1, landmarks, labeled inputs, alt text, no overflow.
- Form → team email **and** autoresponder confirmed in Mailpit (both hub placements).

### Pending (needs action)
- Replace the placeholder hero WebP + add real SKU product photos (featured images) — they’ll flow into srcset + Product `image` automatically.
- Run Lighthouse in CI to confirm Perf ≥90 / SEO ≥95 / A11y ≥95 (all measured inputs are green).

## Phase 2 — Component library

- `inc/components.php`: render functions for every SPEC §4 block — quick-answer, SKU spec card (`<article>`+`<dl>`), data tables (semantic, stack <768px), trust strip/chips, reference-buyer logo band (type-only fallback), FAQ accordion (WAI-ARIA, multi-open, deep-link → FAQPage JSON-LD), stat band, doc/article/category cards (stretched-link), sticky mobile CTA, and the two-step quote form.
- `inc/quote-endpoint.php`: REST `POST /artisraw/v1/quote` — honeypot, optional Cloudflare Turnstile verify, team-inbox email + autoresponder (line-sheet + compliance-pack links), per-field validation (422).
- `css/forms.css` + `css/components.css`: 48px labeled inputs, validation error/success states, two-step layout, and all component styling (tokens only).
- `js/components.js`: accordion, stat count-up (IntersectionObserver, reduced-motion), sticky-CTA hide-while-form-in-view, GA4 delegation (cta_click, doc_download, whatsapp_click, faq_expand).
- `js/forms.js`: two-step quote — UTM capture, inline validation, AJAX submit, success + Step-2 reveal, GA4 form_submit step 1/2.
- `tpl-styleguide.php` + hidden noindex `/styleguide/` page rendering the full kit with sample data and every state.
- Enqueues wired (forms.js loads only where a form renders); sticky CTA in footer.
- Nav IA reorganised into intent-grouped B2B structure: **Catalogue** (categories) · **Wholesale** (hub, how-to-order, shipping, references) · **Private Label** · **Why ArtisRaw** (about, process, certifications, quality, sustainability) · **Olive Wood Guide** · **Contact** — resolving the old Products/Wholesale ambiguity.
- Dropdown chevron now sits inline inside the parent link (one clean focus target) on desktop; the separate toggle button is mobile-drawer-only. Desktop nav breakpoint raised to 1180px with `nowrap` + no-shrink so the bar never wraps; brand stays vertically centred. Nav label "Olive Wood Guide" → "Guide" to fit the bar (page H1 unchanged).
- Fixed dropdown closing before you could reach the submenu (the hover-gap bug): added a transparent hover bridge over the trigger→panel gap, plus a 200ms hover-intent close delay and Esc-to-close in nav.js. Verified with simulated cursor travel into the panel.
- Fixed data-table first row hidden by a broken sticky `thead` (removed; it fought the sticky site header inside an overflow container).

### Verified
- Live endpoint: valid → 200 + email **and** autoresponder (Mailpit confirmed both); invalid → 422 per-field; honeypot → silent 200.
- Accordion toggles aria-expanded + deep-link hash; two-step form submits without reload (success panel + email shown).
- JS total **5.4 KB gz** (nav+components+forms; budget ≤30 KB). No horizontal overflow; 1 H1; zero unlabeled inputs/unnamed buttons; no heading skips; FAQPage JSON-LD valid.

### Pending (needs action)
- Configure Cloudflare Turnstile keys (`ARTISRAW_TURNSTILE_SITEKEY`/`_SECRET`) + quote inbox (`ARTISRAW_QUOTE_INBOX`) and asset URLs (`ARTISRAW_LINESHEET_URL`/`_COMPLIANCE_URL`) in wp-config before launch.
- GA4 events fire client-side; confirm in DebugView once the GA4 property/GTM is live (Phase 5).

## Phase 1 — Theme foundation & design system

- Self-hosted webfonts: Fraunces (display serif) + Inter (UI sans), variable WOFF2 latin subset, preloaded, `font-display: swap`.
- `css/tokens.css`: full design-token layer from SPEC §5.1 (type scale, color, 8px spacing, grid, radii, shadows, motion, focus). Single source of truth.
- `css/base.css`: reset, body 16/1.6, one-style-per-heading, links/focus, skip link, 12-col grid + 1180px container, section rhythm, 3-level buttons.
- `css/layout.css`: sticky ≤64px header, primary nav, accessible off-canvas drawer, "Why ArtisRaw" dropdown, breadcrumb, footer.
- `functions.php`: token-first asset enqueue with filemtime cache-busting, theme supports, 4 nav menus, image sizes 600/1200/1800, font preload, emoji/embed/bloat removal, core-canonical dedup.
- `header.php`: full `<head>` (charset/viewport/PE `js` flag + `wp_head`), skip link, sticky header, canonical 6-item nav with hardcoded fallback, Request Quote CTA, Wholesale Login.
- `footer.php`: NAP, ≤12 links, WhatsApp (aria-label), language-switcher placeholder.
- `js/nav.js` (1.3 KB gz): mobile drawer — aria-expanded, focus trap, Esc, scroll lock — plus dropdown disclosures.
- `inc/seo-head.php`: meta description, self-referencing canonical, robots (respects blog_public + ACF noindex), Open Graph + Twitter; ACF title/desc/og overrides with fallbacks.
- `inc/schema.php`: site-wide Organization + WebSite JSON-LD (SPEC §6.7).
- `inc/breadcrumbs.php`: visible breadcrumb nav + matching BreadcrumbList JSON-LD from one source.
- `inc/acf-fields.php`: global "SEO & Schema" field group registered in code (inert until ACF active).
- Templates: `index.php`, `page.php`, `front-page.php` (placeholder hero), `404.php` — all with `<main>` landmark.
- Theme activated; progressive enhancement so nav works fully with JS disabled.

### Verified
- HTML 20 KB · CSS 5.5 KB gz · JS 1.3 KB gz (budget ≤100 KB / ≤30 KB JS).
- One H1/page · landmarks · skip link · zero img-without-alt · zero unnamed buttons · no heading skips.
- Single canonical, meta description, OG/Twitter on every URL · valid JSON-LD.
- Zero non-token colors in CSS · nav usable with JS off (desktop + mobile).

### Pending (needs action)
- Install the Advanced Custom Fields plugin via WP admin (Plugins → Add New). Field group appears automatically.
- Run Lighthouse per template in CI (SPEC §7) to confirm Perf ≥95 / A11y ≥95.
