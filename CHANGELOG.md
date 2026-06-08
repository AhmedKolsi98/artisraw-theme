# ArtisRaw theme — Changelog

One line per shipped item (SPEC working rhythm). Newest first.

## Phase 12.9 — French homepage gets the real design

The `/fr/` home was the last page still on the prose `tpl-trust` template, so it didn't look like the site. `front-page.php` can only target the site's front page, so:
- Extracted the homepage composition into a shared **`template-parts/home.php`**. `front-page.php` (EN front page) and a new **`tpl-home.php`** (Template Name: Homepage) both render it — one source, identical design, localised by gettext + `artisraw_localized_url()`.
- Pointed the `/fr/` page at `tpl-home.php` (`ARTISRAW_FR_VER` → 5). The French home now shows the full Figma homepage — photo-collage hero, captioned grid, mosaic bands, bestsellers, differentiators, channel, proof band, feature testimonial + trio, Guide, quote block — all in French.
- Translated the **61 home-specific strings** (hero value-prop, quick answer, collections, "Why buyers choose ArtisRaw", "Built for your channel", stat labels, IG captions, etc.).
- Note: a few dozen dict keys are now duplicated (single-space vs aligned spacing from the merge tooling) but every duplicate has an **identical value**, so PHP resolves them to the same correct French — harmless, left as-is.

### Verified
- `php -l` clean. EN front page unchanged (full design, one H1). `/fr/` renders the full homepage, `lang="fr-FR"`, one H1, zero PHP notices. Visible-text scan across 12 FR pages incl. `/fr/`: **zero English**. Home switcher pairs both ways (`/` ↔ `/fr/`).

## Phase 12.8 — Language-aware internal links (no more English links on French pages)

Bug: on a French page the nav, footer, breadcrumbs and in-body CTAs all linked to **English** URLs (hardcoded `home_url('/wholesale/…')`), so clicking a menu item left French — and an English-structured `/fr/wholesale/olive-wood-cutting-boards/` 404s (French pages are flat: `/fr/planches-a-decouper-bois-olivier/`).
- New `artisraw_localized_url( $en_path )`: on English pages returns the English URL unchanged; on French pages returns the **paired French counterpart** (via `alt_pair`), falling back to English only when no translation exists. Site root → `/fr/`. Statically cached per request.
- Applied to all chrome + body link generation: **header nav (31)**, **footer (13)**, breadcrumb Home, and **68 template/component CTAs** (tpl-trust/category/catalogue/contact-faq/about/process/services/worldwide/account, components, single, front-page). The active-state `$current` line and all SEO/schema/canonical/REST/seeding files were deliberately left on absolute `home_url()`.

### Verified
- `php -l` clean. FR page nav links are 100% French (`/fr/gros/`, `/fr/ustensiles-bois-olivier/`, …) — **zero** English-leaking nav links, **zero** `/fr/wholesale/*` paths. FR page **bodies**: 0 English-leaking internal links across 7 sampled pages. EN pages unchanged — 0 French leaks. Switcher/hreflang unaffected.

## Phase 12.7 — Full French site (every nav page paired) + honest switcher

The language switcher was sending most pages to the `/fr/` landing because only ~9 pages had a French counterpart. Fixed by translating the **whole static site** and making the switcher honest.
- **24 new paired French pages** seeded (`ARTISRAW_FR_VER` → 4), each on its **real template** (full design parity, not prose downgrades): the catalogue index + 5 category pages + private-label (`tpl-category`, with the `cat_term`/`cat_mode` meta replicated so the SKU grids render), the full catalogue (`tpl-catalogue`), magazine (`tpl-magazine`), FAQ + request-quote (`tpl-contact-faq`), the wholesale account (`tpl-account`, noindex), privacy (default), and all proof pages — quality-control, shipping-logistics, how-to-order, references, sustainability, supplier-USA, supplier-Europe, the Guide pillar, compliance + Lacey + EUDR (`tpl-trust`). Each has full French title/SEO/quick-answer/body and is `alt_pair`-bound to its EN counterpart. Child EN pages (categories under `/wholesale/`, Lacey/EUDR under `/compliance/`) are paired via their full `parent/slug` path.
- **280 template chrome strings translated** — the entire portal, category pages, FAQ bank, trust pillars, forms, magazine and catalogue UI. Visible-text leakage scan across all 24 FR pages: **zero English**.
- **Honest switcher.** `artisraw_lang_links()` now reports `fr_exists`/`en_exists`; on a page with no counterpart (individual Guide articles, archives, 404) the unavailable language renders **disabled/greyed** with a tooltip instead of silently linking to the `/fr/` (or `/`) home. No more misleading jumps.

### Verified
- `php -l` clean. All 24 new FR pages 200; every EN nav page pairs to its **distinct** French counterpart (none fall back to `/fr/`). FR pages render `lang="fr-FR"` with no visible English. Untranslated article page → Français shown disabled, zero `/fr/` fallback links. EN pages: one H1, zero PHP notices.
- (FR home `/fr/` stays on `tpl-trust` — `front-page.php` can't be assigned to a non-front page; its copy is fully French.)

## Phase 12.6 — i18n completion, language switcher, nav & palette refinements

Client-requested follow-ups on the Figma integration.
- **Language switcher → flag dropdown.** The EN/FR text toggle is now a native `<details>` dropdown showing the current language as `flag + name` (🇫🇷 Français / 🇬🇧 English), each option linking to its EN/FR counterpart. Inline SVG flags (FR tricolore + GB union, ID-free so they render twice safely) — reliable cross-platform, not emoji. Works **without JS** and is keyboard-accessible. `artisraw_lang_switch()` + `artisraw_flag_svg()` in `inc/i18n.php`; styles in `css/figma.css`; old `.lang-toggle` retired.
- **Nav primary action → "Client Login."** The "Request Quote" button is replaced by the wholesale portal link, reworded **Client Login** (→ `/wholesale-account/`); the separate "Wholesale Login" text link is removed (promoted into the button). Desktop order is now `language switcher · Client Login`.
- **Full French coverage.** ~120 new dictionary entries covering every new About/Process/Home string (heroes, value/stat cards, numbered steps, certification, founders, Stories band, QC timeline, process FAQ, caption grid, feature quote, trio band) + "Client Login." **FR About** (`/fr/a-propos/`) now uses `tpl-about.php` and a new **FR Process** page (`/fr/processus/`) uses `tpl-process.php` — both render the Figma design in French. `ARTISRAW_FR_VER` → 2. (FR home stays on `tpl-trust` — `front-page.php` can't be assigned to `/fr/`; its prose is already French.)
- **Leaf green removed.** Token `--c-leaf-400` (#C4D34A) deleted from the palette and every usage recolored to warm tones: the home sustainability mosaic band → sand; statement-hero & espresso-field eyebrows → amber; footer-trust icon → amber; styleguide demo → sand. The palette is now espresso / amber / sand / cream / tan / olive — no green.

### Verified
- `php -l` clean on all touched files. **#C4D34A absent** from all served CSS. EN home/about/process: one H1 each, switcher shows "English". FR `/fr/`, `/fr/a-propos/`, `/fr/processus/` all 200 with `lang="fr-FR"`; FR About/Process render the new templates with all strings in French and the switcher showing "Français"; **zero** PHP notices. Nav shows Client Login, no Request-Quote CTA, 3 SVG flags.

## Phase 12.2 — Figma Visual Integration: Home page

Home re-composed to the Figma rhythm — **merge, not replace**: adopt the Figma hero + its distinctive sections, keep our higher-value B2B/SEO blocks.
- **Hero decision → adopt the Figma hero** (your call). `statement_hero` ("wood that works.") swapped for `artisraw_photo_hero()`: photo collage (`ar-hero-collage`) + amber serif value-prop headline ("Premium Olive Wood for Wholesale Buyers") + primary/secondary CTA + re-skinned ISO badge, with our numeric trust strip folded in. LCP preload updated to `ar-hero-collage`.
- `photo_hero` extended with `trust` (numeric trust strip, on-dark chips) + `alt_label/alt_url` (secondary CTA).
- **Added** the Figma captioned image grid ("From tree, to workshop, to wholesale, to shelves") and the **feature testimonial + trio wayfinding band** (replaced the `buyer_voices` carousel — one testimonial treatment, no redundancy).
- **Kept** (not compromised): quick answer (SEO), the 3 color-block mosaic bands, real **Ready-to-Ship Bestsellers** SKU grid (better than the Figma placeholder product strip), differentiators, "Built for your channel", stat band + logo band, real Guide posts, Instagram strip, two-column quote block. Tri-strip stays in the footer (no duplicate section).

### Verified
- `php -l` clean. Home 200, **exactly one H1** (hero), hero preloads `ar-hero-collage`, ISO badge + trust strip render, caption grid + 3 mosaic bands + SKU grid + feature testimonial + trio band + quote block all present, `buyer_voices` removed, **zero** PHP notices/warnings, all 19 `ar-*` images resolve 200.

## Phase 12.4 — Figma Visual Integration: Process page

`/production-process/` rebuilt to the Figma composition on `tpl-process.php` (was `tpl-trust` + `trust_extras=process`). Quick-answer + prose intro stay editable; designed sections template-rendered and `__()`-wrapped.
- Composition: photo hero (`ar-worker-carry` + ISO badge + CTA) → quick answer → centered intro → **8-step numbered overview** (`steps--numbered`) → **3 alternating step-feature bands** (`color_block` amber/sand/espresso, alternating `field_left`) → **QC timeline** (6 checkpoints) → **FAQ** (8 Q&A) → quote block on a sand band.
- Reused existing components throughout (`color_block`, `faq_accordion`, `quote_block`, `qc_timeline`, `steps`, `photo_hero`) — no new components needed.
- **FAQ schema:** rendered with `emit_schema = false` here so the canonical FAQPage entity stays on `/faq/` (no duplicate FAQPage across URLs).
- Seeder: process page → `$T_PROC`; `ARTISRAW_PAGES_VER` → 8.

### Verified
- `php -l` clean. `/production-process/` 200, photo-hero active, **one H1**, **8** step cards, **3** step-feature bands (2 field-left + 1 photo-left), **6** QC cards, **8** FAQ items, quote block present, **zero** PHP notices/warnings, **zero** FAQPage schema on the page (owned by /faq/), all referenced `ar-*` images resolve 200.

## Phase 12.3 — Figma Visual Integration: About page

`/about/` rebuilt to the Figma composition on a dedicated `tpl-about.php` (was `tpl-trust` + `trust_extras=about`). Quick-answer + prose intro stay editable (post meta/content); the designed sections are template-rendered and `__()`-wrapped.
- **Asset ingestion (12.5 partial):** the Figma imagery brought into our responsive pipeline via PIL — `ar-lathe`, `ar-boards-drying`, `ar-warehouse`, `ar-whoweare`, `ar-certificate`, `ar-showroom`, `ar-artisan-bowl`, `ar-trade-fair`, `ar-worker-carry`, `ar-hero-collage`, `ar-world-map`, `ar-olive-tree`, `ar-chess2`, `ar-product-1..5`, `ar-brand-script` — each as `{base}-{width}.webp` (600/1200/1800, never upscaled).
- **`artisraw_photo_hero()`** — reusable inner-page hero (full-bleed photo + dark scrim + amber serif title + support + optional CTA and ISO badge). Separate from the home statement_hero (the home-hero concept is still an open decision).
- **`artisraw_testimonial_feature()`** gained `dark` + `button_label/url` (the espresso "Stories worth telling" band).
- About composition: photo hero (whoweare bg + ISO badge) → quick answer → centered intro (`.about-intro`) → caption grid (4 visuals) → 4 value pillars → facility stat cards → numbered 6-step process → certification block (`.cert-block`, real certificate image) → founders (Mohamed Bilel Cherif · Ihsen Triki · Ahmed Sakka) → dark Stories band.
- Seeder: about page switched to `$T_ABOUT`; `ARTISRAW_PAGES_VER` → 7 (template re-applied via the `update` path on bump).
- FR: strings are `__()`-wrapped (translation-ready); the FR dict entries + pointing `/fr/a-propos/` at the new template are deferred to the 12.6 FR/QA sweep to avoid shipping a half-translated FR design.

### Verified
- `php -l` clean. `/about/` 200, photo-hero active, **exactly one H1**, all sections present (caption grid, value/stat cards, numbered steps, cert block, founders, dark Stories band, hero ISO badge), **zero** PHP notices/warnings. All 11 referenced `ar-*` images resolve 200 through the pipeline.

## Phase 12.1 — Figma Visual Integration: component library

New reusable components ported from the Figma into `inc/components.php` (all through `artisraw_responsive_image()`, tokens-only styling in new `css/figma.css`, gettext-ready). Each added to `/styleguide/`.
- **`artisraw_caption_grid()`** — square photos with an overlaid paper caption label (Figma `.cap-grid`); distinct from `photo_mosaic` (which has variable tile sizes).
- **`artisraw_trio_band()`** — three colored wayfinding CTA tiles (espresso / olive / sand) with an animated arrow cue.
- **`artisraw_value_cards()`** — dark pillar cards (surfaces cycle espresso / olive / bark / espresso-800) for the About pillars.
- **`artisraw_stat_cards()`** — labelled metric cards (Location / Factory / Team / Capacity).
- **`artisraw_qc_timeline()`** — six checkpoint cards with an amber top rule (Process QC).
- **`artisraw_testimonial_feature()`** — one photo beside a paper field holding a single quote; complements the `buyer_voices` carousel (not a replacement).
- **`artisraw_product_strip()`** — product cutouts on sand cards, `mix-blend-multiply` so photos drop onto the surface without clipping paths.
- **`artisraw_steps()`** gained a `numbered` variant arg → `.steps--numbered` (large faded corner numeral, Figma 8-step overview look). Existing `.steps` was already the 4-col card grid.
- New `css/figma.css` enqueued after `art.css`; new tokens `--c-olive-600` / `--c-tan-300` used by the trio/value surfaces.

### Verified
- `php -l` clean on all touched PHP. `/styleguide/` 200, renders all eight components, `figma.css` loads (200), **zero** PHP notices/warnings/deprecations. Referenced placeholder assets resolve through the responsive pipeline.

## Phase 12.0 — Figma Visual Integration: foundations

First slice of the Figma merge. Adopt the Figma's visual language through our own pipelines (tokens, self-hosted fonts, gettext-ready, reduced-motion-safe) — no CDN fonts, no plain `<img>`, no inline styles, no flat brochure nav.
- **Sans font swap → Archivo** (the Figma design font), self-hosted as a latin-subset variable WOFF2 (`fonts/archivo-var.woff2`, wght 400–800, 35 KB — smaller than the retired Inter). `--ff-sans` updated in `tokens.css`; preload hint updated in `functions.php`; `inter-var.woff2` removed. **Self-hosted, not the Figma's Google Fonts CDN** (GDPR + performance; the site exports to the EU).
- **Palette tokens** added for the Figma editorial mosaic: `--c-tan-300` #C9A468 (espresso text only) and `--c-olive-600` #6B5A3A (cream text only), alongside the existing field colors.
- **ISO 9001 trust badge** `artisraw_iso_badge()` — the Figma motif **re-skinned to the brand**: espresso disc / amber ring / cream type. The Figma's corporate blue (#1D6FE0) was rejected as foreign to the palette. `place` arg pins it to a photo hero (`--hero`) or renders inline; exposed to AT as one labelled image; decorative spans hidden.
- **Reveal-on-scroll** utility (Figma `.reveal`): IntersectionObserver in `js/components.js` toggles `.is-visible`; CSS hides `.reveal` only under `.js` so non-JS visitors always see content, and `prefers-reduced-motion` shows everything unconditionally.
- Styleguide: badge + new surface tokens added for review.

### Verified
- `archivo-var.woff2` serves 200 (`application/font-woff2`, 34940 b). Home 200, Archivo preloaded once, **zero** PHP notices/warnings/fatals. `/styleguide/` renders the badge, no errors. `php -l` clean on all touched PHP. No stray `Inter`/`inter-var` references remain.

## Phase 11 — Art Direction (Addendum v1.1)

Olyfo visual language expressed in ArtisRaw tokens, on the existing B2B skeleton.
- Token: **`--c-leaf-400` #C4D34A** editorial accent (one per viewport; espresso text on leaf; never adjacent to amber). New `css/art.css`.
- **Statement hero** (§3) `artisraw_statement_hero()`: one full-bleed duotone image (CSS grayscale + espresso multiply), lowercase serif statement ("wood that works." — the home H1), cream support line, amber CTA, and the numeric trust strip pinned at the lower edge.
- **Color-block mosaic** (§4) `artisraw_color_block()`: 50/50 field↔photo bands, alternating sides, image-first on mobile, four field colors (sand/espresso/amber/leaf) with measured-contrast text. Home set: Who we are (sand) · How it's made (espresso) · One tree used, two planted (leaf) — consolidates the old founders/sustainability/plant sections; leaf used exactly once.
- **Block formula** (§5) `artisraw_arrow_link()`: "Label →" with 4px hover nudge; applied across differentiators, who-we-serve, Guide, collections.
- **Buyer voices** (§6) `artisraw_buyer_voices()`: one large serif quote per viewport (scroll-snap), type+market attribution.
- **Two-column quote block** (§9.1) `artisraw_quote_block()`: "Wholesale inquiries" eyebrow + serif heading + benefits checklist (Low MOQ · Custom branding · Worldwide shipping) + "Quote within 24 h" beside the two-step form as a white card. Adopted on home, /contact/, /request-quote/.
- **SKU strip** (§6): "Ready-to-Ship Bestsellers" + confident intro line + mobile scroll-snap. **Section openers** (serif H2). **Footer**: large wordmark + worded trust strip (Handmade · Sustainable · Export Ready).
- Home rebuilt to the editorial order (statement → quick answer → collections → mosaic → bestsellers → differentiators → channel → proof → voices → Guide → Instagram → quote block). French dictionary extended for the new strings.
- §9.2 corrections verified already-satisfied in our build (ISO **2015**, 6-item nav, no brush-logo-as-content, one-paragraph About, SVG map not raster text, attributed voices).

### Verified
- 40-URL crawl all 200, zero duplicate titles/metas/canonicals, one H1 each, zero JSON-LD/PHP errors.
- Home: statement H1 "wood that works.", 3 color-blocks, **exactly one leaf element**, pinned trust strip, SKU strip, one-per-viewport voices, two-column quote block, 14 arrow links. Hero preloads `ar-grove` (LCP). FR /contact/ quote-block translated. AD components added to /styleguide/.

## Phase 8 — Content expansion: full catalogue, Magazine & Guide

- `sku_category` expanded **5 → 15 families** via `artisraw_catalogue_families()` (idempotent term seeding); families link to their `/wholesale/` page where one exists.
- `tpl-catalogue.php` → `/catalogue/`: magazine-style index — featured families (real images) + the full 15-family grid + food-safe care note + **gated PDF catalogue & price-list** request (quote form).
- Article system: `single.php` for posts — quick-answer first, reviewer byline + Updated date, prose body, CTA, and **BlogPosting JSON-LD** with author/reviewer (E-E-A-T, §6.9). `inc/content.php` seeds 4 real Guide articles (Chemlali grain, Lacey Act, olive-wood care, EUDR) with `quick_answer` + `article_reviewer` meta, under a "Olive Wood Guide" category.
- `tpl-magazine.php` → `/magazine/`: editor's feature + latest-stories grid (WP posts) + fairs/participation band + newsletter CTA.
- Guide pillar `/olive-wood/`: `trust_articles` flag lists the latest Guide articles (shared `artisraw_post_to_card()`).
- Compliance pages: `/compliance/` index + `/compliance/lacey-act/` + `/compliance/eudr/`, plus `/olive-wood-supplier-europe/` (tpl-trust). Seeder `ARTISRAW_PAGES_VER` → 6.
- Nav: "Full Catalogue (PDF)" added to the Catalogue dropdown; Guide became a dropdown (Guide · Magazine · Compliance). FR dictionary + `.article-hero` styles added.

### Verified
- Full-site crawl (40 URLs): all HTTP-200, **zero duplicate titles/metas/canonicals**, one H1 each, zero JSON-LD parse errors, zero PHP notices.
- Catalogue shows 15 families + PDF request form; Magazine + Guide list the 4 seeded articles; single article renders quick-answer + byline + BlogPosting schema. Articles in the post sitemap; catalogue/magazine in the page sitemap.

### Pending (ongoing content)
- Remaining BLUEPRINT articles (2–4/week), per-family SKU population + real product photography, factory video on `/production-process/`, real Q4 documents in the download centre, segment pages (`/wholesale/for-…`).

## Phase 10 — French version, hardening & AI visibility

- `inc/i18n.php`: plugin-free French layer (SPEC §9). `/fr/` pages are real pages (meta `page_lang=fr`) reusing the prose templates; UI chrome is translated at runtime via a `gettext` PHP dictionary (no `.mo` tooling), rich body copy lives in `post_content`. Strings not in the dictionary fall back to English.
- `<html lang="fr-FR">` on French pages; `hreflang` alternates (en / fr / x-default) emitted from the bidirectional `alt_pair` meta; header EN/FR toggle wired to each page's counterpart (untranslated pages point FR → `/fr/`).
- Seeded 7 paired French pages (idempotent, `ARTISRAW_FR_VER`): `/fr/` (home), `/fr/fournisseur-bois-olivier-grossiste/` (hub), `/fr/services/`, `/fr/monde/`, `/fr/a-propos/`, `/fr/certifications/`, `/fr/contact/` (form labels + NAP localized) — each with French title/meta/quick-answer/body and EN↔FR pairing.
- `LAUNCH.md` §5: French + ongoing hardening/AI-visibility checklist.

### Verified
- All 7 French pages 200, one H1 each, `lang="fr-FR"`, 3 hreflang `<link>`s (en/fr/x-default), zero PHP notices.
- Nav, footer, CTAs, trust chips, trust CTA and the contact form render in French; EN pages stay English (no bleed); toggle resolves EN↔FR counterparts both directions; French pages indexable + in the sitemap.

### Pending (ongoing — live data / content)
- Translate the remaining top-15 pages (same pattern; extend `artisraw_fr_dict()`); field-CWV hardening; monthly AI share-of-answer dashboard; FR breadcrumb home link → `/fr/`.

## Phase 9 — Client Area: B2B ordering portal

- `inc/account.php`: account engine on the WordPress user system — registration (PENDING by default), email-based login, logout; per-IP rate limiting on register/login; nonce-protected PRG form handling.
- Manual approval gate: "Approved wholesale buyer" checkbox + "Production status note" on the user-edit screen (`edit_user_profile`); buyer is emailed on first approval. Team is emailed on each new registration.
- Order-list builder: add/update/remove SKUs (stored in user meta), "Request quote for this list" routes through the same email pipeline (team + autoresponder) and saves to order history with a status; reorder via history.
- `tpl-account.php` at `/wholesale-account/` (the "Wholesale Login" destination): routes by state — login/register → pending → dashboard (production status, order builder, catalogue picker, history). Always noindex + excluded from the sitemap.
- `css/account.css` (enqueued only on the portal template); page converted via the seeder (`ARTISRAW_PAGES_VER` → 5).

### Verified
- Full flow (cookie-jar integration test): register → logged-in pending view → admin approve → dashboard → add SKU (qty) → order list → submit → `notice=submitted`, order in history, cart cleared.
- Access control: pending users see no order builder; logged-out POSTs rejected (nonce); register/login rate-limited.
- Portal noindex + sitemap-excluded; all other pages unaffected by the init form-handler.

### Pending (needs action)
- Confirm portal-quote + approval emails in the live mailbox (wp_mail mirrors the proven quote pipeline); optionally add a lightweight admin list/bulk-approve and richer per-order production statuses.

## Phase 6 — Technical SEO layer & launch prep

- `inc/seo-tech.php`: robots.txt (virtual) with AI-bot allows (GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-Web, PerplexityBot, Google-Extended, Applebot-Extended, CCBot) + `Sitemap:` line per SPEC §6.8.
- `/llms.txt` virtual route (llmstxt.org format): company definition + key page links, served `text/plain`; canonical-redirect bypassed so no trailing slash.
- XML sitemap tuning (WordPress-native `/wp-sitemap.xml`): dropped users + sku_category/tag/category providers; excluded noindex pages (styleguide); `/sitemap_index.xml` 301-alias. Trashed default *Sample Page* + *Hello World*.
- GA4-via-GTM loader gated on `ARTISRAW_GTM_ID` / `ARTISRAW_GA4_ID` (inert until set); `js/components.js` adds `hero_view` (≥50% / 1 s) and distinct `linesheet_download` / `compliance_pack_download` events.
- Baseline security headers via `wp_headers` (X-Content-Type-Options, Referrer-Policy, X-Frame-Options, Permissions-Policy).
- `ARTISRAW_STAGING` constant forces sitewide noindex on staging (launch-day removal = the explicit go-live check); wired into `inc/seo-head.php`.
- `LAUNCH.md`: wp-config constants reference + the Phase-7 infra/launch-day checklist (Cloudflare, CSP, GSC/Bing, Lighthouse, DebugView).

### Verified (SPEC §11, locally testable)
- robots.txt live with AI-bot allows; `/llms.txt` 200 text/plain; sitemap index 200 (26 pages, styleguide excluded, no WP cruft); `/sitemap_index.xml` → 301.
- SSR H1 present; unknown URL → 404; styleguide noindex while real pages stay indexable; JSON-LD parses; security headers present.
- Analytics loader inert with no ID configured (zero googletagmanager refs).

### Pending (Phase 7 — infrastructure)
- Cloudflare AI-allow + CSP (grade-A), GA4/GTM property + DebugView verification, Search Console/Bing submission, Lighthouse/Rich-Results in CI, cross-browser, backups/monitoring. See `LAUNCH.md`.

## Phase 5 — Design parity: visible pages & home sections

- `tpl-services.php` → `/services/`: 6 core services, 12 buyer-profile chips, 3 service packs, 8-step process, selected clients, Services FAQ, quote form (mockup page 3).
- `tpl-worldwide.php` → `/worldwide/`: honest hub-and-spoke SVG map (Sfax → 5 regions, CSS-driven, no JS), per-market support blocks (USA/Canada · EU · GCC · Asia), transit/Incoterms table, CTA (mockup page 4).
- `/about/` enriched (component-rendered via `trust_extras=about`): four pillars, founders trio with bios, facility process strip, “Why Chemlali is superior” + founder quote.
- `/production-process/` enriched (`trust_extras=process`): 8-step process overview + 6-point QC timeline.
- `inc/components.php`: new reusable components — `artisraw_steps`, `artisraw_testimonials`, `artisraw_newsletter`, `artisraw_founders`, `artisraw_plant_a_tree`, `artisraw_instagram_strip`.
- Home (`front-page.php`): visual-collections band, “Who we are” founders teaser, testimonials, plant-a-tree program panel, Instagram strip. Newsletter moved site-wide into the footer.
- `inc/newsletter-endpoint.php`: REST `POST /artisraw/v1/newsletter` — honeypot + email validation, dedup store (`artisraw_newsletter_list`), team notify + autoresponder; `components.js` progressively enhances `[data-newsletter]` (no-JS falls back to `/contact/`).
- Header: nav reorganised — **Services** is now a top-level dropdown (absorbs Private Label + Wholesale Production), **Worldwide / Export** added under Wholesale (net-zero top-level count, bar still fits 1180px). EN/FR language toggle added to the header (FR disabled until Phase 10 — no 404).
- `inc/seed-pages.php`: `ARTISRAW_PAGES_VER` → 4; idempotent page **update** path added (`'update' => true`) so enrichment re-applies on version bumps; services + worldwide seeded with SEO/quick-answer.
- `css/phase5.css`: all new component + page styles (tokens only). `html { overflow-x: clip }` guard added so the fixed off-canvas drawer can never widen the layout (clip keeps sticky header + vertical scroll intact).

### Verified (Phase 5 QA gate)
- Crawl of 26 URLs (24 HTML pages): **zero duplicate titles / metas / canonicals**, exactly 1 H1 each, quick-answer first under every H1.
- **Zero broken links** across 67 internal links/assets; **no PHP warnings/notices** in output; JSON-LD valid (Organization + WebSite + BreadcrumbList) on the new pages.
- Mobile a11y/overflow audit clean on new pages: 0 img-without-alt, 0 unnamed buttons, 0 heading skips, **scrollWidth == clientWidth** (overflow fixed sitewide).
- Newsletter endpoint: valid → 200 + stored, invalid → 422, honeypot → silent 200. Desktop nav confirmed single-row with the new Services item + language toggle.

### Pending (needs action)
- Real photography for founders (initials-avatar placeholder in place), Instagram tiles (gradient placeholders) and lifestyle/collection imagery.
- Connect the newsletter list to a real ESP (Mailchimp/Brevo) in Phase 6; wire the EN/FR toggle to real `/fr/` pages in Phase 10.

## Phase 4 — All Phase-1 pages

- `front-page.php` → tpl-home (CONTENT page 1): 9-section home (hero · trust · quick answer · 5 category cards · differentiators · who-we-serve · proof band + reference buyers · sustainability · latest guide · quote form). Static front page wired with exact SEO title/meta.
- `tpl-category.php` → /wholesale/ index + category pages (cutting-boards, utensils, bowls-serveware, chess-sets, decor-bath) + /private-label-olive-wood/; `sku_category` taxonomy + filtered SKU grids + ItemList schema.
- `tpl-trust.php` → certifications, quality-control, shipping-logistics, how-to-order, references (download centre), about, olive-wood-supplier-usa, wholesale-account, production-process, sustainability, olive-wood (Guide stub) — quick answer + body + optional downloads + trust strip + CTA.
- `tpl-contact-faq.php` → /faq/ (16-Q FAQPage schema) + /contact/ (LocalBusiness schema + NAP) + /request-quote/ (trimmed header/footer).
- `inc/seed-pages.php`: idempotent, versioned seeding of all Phase-1 pages with templates, SEO meta, quick answers and body copy. `/privacy/` page added.
- Placeholder documents in `public/downloads/` (stable filenames) via `artisraw_doc_url()`; custom 404 with search + links; sticky CTA + nav cross-linking.

### Verified (Phase 4 QA gate)
- Crawl of 24 URLs: **zero duplicate titles / metas / canonicals**, 1 H1 each, quick-answer first under every H1.
- **Zero broken links** across 59 internal links/assets on 20 pages; 404 returns real 404 with search + links.
- Category SKU filtering (2/3 cards by term), FAQPage (16 Q), LocalBusiness, trimmed quote chrome all confirmed. Spot CWV/a11y clean (LCP 0.8 s, CLS 0 on hub/category).

### Pending (needs action)
- Replace `public/downloads/` placeholders with the real Q4 documents (same filenames); add real photography (hero + SKU + founder portraits).
- Titles follow the CONTENT-approved wording (several exceed the 60-char display guideline by design — copy precedence).

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
