# ArtisRaw — Launch checklist (Phase 6 → Phase 7)

Phase 6 builds the technical-SEO + analytics + security layer in the theme. The
items below that need a real server, DNS, or third-party accounts can't be done
on Local — they're the Phase-7 launch-day tasks.

## 1. wp-config.php constants (set before launch)

```php
// Analytics — set ONE (GTM preferred). Loader is inert until defined.
define( 'ARTISRAW_GTM_ID', 'GTM-XXXXXXX' );      // or:
define( 'ARTISRAW_GA4_ID', 'G-XXXXXXXXXX' );

// Force sitewide noindex on staging. REMOVE (or set false) on production.
define( 'ARTISRAW_STAGING', true );

// Quote + newsletter delivery
define( 'ARTISRAW_QUOTE_INBOX', 'sales@artisraw.com' );
define( 'ARTISRAW_NEWSLETTER_INBOX', 'sales@artisraw.com' );

// Spam protection (Cloudflare Turnstile)
define( 'ARTISRAW_TURNSTILE_SITEKEY', '...' );
define( 'ARTISRAW_TURNSTILE_SECRET', '...' );

// Autoresponder asset links
define( 'ARTISRAW_LINESHEET_URL',  'https://artisraw.com/downloads/line-sheet.pdf' );
define( 'ARTISRAW_COMPLIANCE_URL', 'https://artisraw.com/downloads/compliance-pack.zip' );
```

## 2. Built in the theme (done — verify on staging)

- [x] **robots.txt** — virtual, AI-bot allows (GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, Claude-Web, PerplexityBot, Google-Extended, Applebot-Extended, CCBot) + `Sitemap:` line. (`inc/seo-tech.php`)
- [x] **/llms.txt** — company definition + key links (llmstxt.org format), served `text/plain`.
- [x] **XML sitemaps** — WordPress-native `/wp-sitemap.xml`; users + sku_category/tags/categories providers removed; noindex pages (styleguide) excluded; `/sitemap_index.xml` 301-aliases to it. Default *Sample Page* / *Hello World* trashed.
- [x] **GA4 via GTM** loader — gated on the constants above; `dataLayer` events already fire from `js/components.js` (`cta_click`, `form_submit`, `faq_expand`, `doc_download`, `linesheet_download`, `compliance_pack_download`, `whatsapp_click`, `newsletter_signup`, `hero_view`).
- [x] **Security headers** (front-end): `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options`, `Permissions-Policy`.
- [x] **Staging noindex toggle** — `ARTISRAW_STAGING` forces noindex sitewide.

## 3. Infrastructure (Phase 7 — needs production host / accounts)

- [ ] Production hosting + **Cloudflare**: cache rules, Brotli, **AI-bots = ALLOW** for the agents above.
- [ ] **CSP** header — tune to the live GTM + asset origins, then add at the edge (not auto-emitted; would break inline GTM if guessed). Target `securityheaders.com` grade A.
- [ ] HTTPS + valid cert; confirm **no mixed content**.
- [ ] **GA4 property + GTM container** created; publish container; verify every event in **DebugView** with params.
- [ ] AI-referral channel group in GA4 (chatgpt.com, chat.openai.com, perplexity.ai, gemini.google.com, copilot.microsoft.com).
- [ ] **Search Console + Bing Webmaster**: verify property, submit `/wp-sitemap.xml`.
- [ ] **Lighthouse mobile** per template in CI: Perf ≥ 90 · SEO ≥ 95 · A11y ≥ 95 · Best-Practices ≥ 95 (external run — blocked on Local).
- [ ] **Rich Results Test** per template (JSON-LD validated locally; confirm eligibility in Google's tool).
- [ ] Single-hop 301s for any legacy URLs; backups + uptime/CWV monitoring.
- [ ] Cross-browser pass: Chrome/Safari/Firefox/Edge + iOS Safari / Android Chrome @ 390 px.

## 4. Launch day (Phase 7)

- [ ] **Remove `ARTISRAW_STAGING`** on production — confirm `<meta name="robots" content="index, follow…">` via view-source and `curl -I`.
- [ ] Submit sitemaps (GSC + Bing); request indexing of the 10 priority URLs.
- [ ] Smoke test: quote form → inbox + autoresponder; newsletter → inbox + confirm; downloads; GA4 real-time; redirects; 404.
- [ ] Baseline snapshot: rankings for the §3 keyword set + run the 15 AI prompts (SPEC §6.10) — day-zero numbers.

## 5. Phase 10 — French + ongoing hardening & AI visibility

**Built (theme):**
- French layer (`inc/i18n.php`): `/fr/` pages on the prose templates, runtime `gettext` French dictionary, `<html lang="fr-FR">`, `hreflang` (en/fr/x-default) from the `alt_pair` meta, header EN/FR toggle wired to counterparts. Add/extend French pages by bumping `ARTISRAW_FR_VER`; untranslated UI strings fall back to English (extend the dictionary in `artisraw_fr_dict()`).

**Ongoing (need live data / accounts — not buildable on Local):**
- [ ] Translate the remaining top-15 pages (catalogue, guide, compliance) — same pattern.
- [ ] Performance hardening from **field** CWV (Search Console → Core Web Vitals).
- [ ] **AI-visibility dashboard** (SPEC §6.10): monthly run of the 15 prompts across ChatGPT/Perplexity/Gemini; log mentions/citations + AI-referral sessions; refresh gaps.
- [ ] Conversion review: quote-form step drop-off, heatmaps on hub + contact; quarterly stat/date refresh.
- [ ] Minor: FR breadcrumb "Accueil" currently links to the EN home — point it at `/fr/` when localizing breadcrumbs.
