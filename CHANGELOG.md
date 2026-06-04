# ArtisRaw theme — Changelog

One line per shipped item (SPEC working rhythm). Newest first.

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
