# Report: About / Process / Services pages updated to match PDF design comps

## What was done

The three templates were aligned with the pages extracted from `web site Wholesales artisraw  .pdf` (pages 6, 7 and 8).

### 1. About page (`tpl-about.php`)
- Replaced the hero image with `ar-olive-tree` (olive-tree sunset) to match the PDF’s “Discover our Story” hero.
- Hard-coded the hero headline to **“Discover our Story”** and the support line to **“Premium Olive Wood, Crafted in Sustainability”**.
- Switched the bottom “Stories worth telling” band image from `ar-showroom` to `ar-collection`.
- Updated the seeded page content in `inc/seed-pages.php` to **“Born in Tunisia, made for global wholesale partners”** and bumped `ARTISRAW_PAGES_VER` → 9 so the change applies on the next page load.

### 2. Process page (`tpl-process.php`)
- Replaced the hero image with `ar-olive-tree` and the headline with **“From olive tree to export ready order”**.
- Changed the hero CTA to **“Discover process”** and linked it to `#process-overview`.
- Added `id="process-overview"` to the process-overview section so the CTA scrolls correctly.
- Added the missing FAQ item from the PDF: **“Is your olive wood sustainably sourced?”**

### 3. Services page (`tpl-services.php`)
- Added a full-width photo hero using `ar-collection` with the PDF headline **“B2B services”**.
- Added the two PDF feature sections using `artisraw_color_block()`:
  - **Wholesale buyers** — amber field, `ar-collection` image.
  - **Private label** — sand field, `ar-lathe` image.
- Rebuilt the **Core B2B services** grid as 8 cards in a 4-column layout, matching the PDF cards (Wholesale Production, Private Label, Cutting and shaping, Hand finishing, Food-safe finish, Quality checks, Export packing, Documents and shipment).
- Kept the quote form at the bottom for lead generation.
- Added `.services-intro` styling in `css/figma.css` for the two lead paragraphs.

### 4. Images
- Used existing high-resolution theme assets that match the photos shown in the PDF (`ar-olive-tree`, `ar-collection`, `ar-lathe`).
- Extracted all PDF images and full-page renders to `/Users/missaoui/Local Sites/artisraw-local/extracted-pdf-images/` for reference.

### 5. French translations
- Added / updated 37 strings in `languages/fr_FR.po` for the new copy.
- Compiled `.po` → `.mo` (586 translated messages).

### 6. Cache
- Cleared the concatenated CSS cache so the new styles regenerate on the next request.

---

## Ambiguities found

1. **ISO badge date mismatch**: the PDF design mockup shows a blue badge reading **“ISO 9001:2025”**, but the actual Bureau Veritas certificate in the PDF (page 5) reads **ISO 9001:2015**. The live site already uses 2015, which is factually correct; the mockup badge should be corrected in the source design file.

2. **PDF images carry watermarks**: several photos in the PDF (olive grove, olive tree, etc.) have **Canva watermarks**. The theme already contains clean, unwatermarked versions of the same shots, so I used those instead of the watermarked extractions.

3. **Services page length**: the PDF shows only the hero, intro, two feature sections and the 8 core service cards. The previous Services template also contained buyer profiles, service packs, an 8-step process, client logos and an FAQ. I removed those sections to match the PDF but kept the quote form for conversion. If any of that content is still needed, it can be re-added or moved to other pages.

4. **Hero image resolution**: the PDF’s individual extracted images are 800 px wide at most, which is too small for full-bleed heroes. I used the theme’s existing 1200 px variants; if the client wants the exact PDF compositions, higher-resolution source files are required.

5. **Header navigation**: `header.php` already had uncommitted changes in the working tree that differ from both the previous nav and the PDF nav. I did not touch `header.php` because it was outside the scope of this request, so the live nav may not yet match the PDF labels (HOME / ABOUT US / SERVICES / PRODUCT / MAGAZINE / PROCESSUS / CERTIFICATION / LET’S WORK TOGETHER / FAQs / CONTACT).

---

## What still needs to be done

1. **Verify on the local site**: load `/about/`, `/production-process/` and `/services/` in the browser to confirm images, headings and the new Services layout render correctly.
2. **Trigger seeders**: visit any front-end page so `artisraw_seed_pages()` runs with version 9 and updates the About page content.
3. **Provide clean hero source files**: if the client wants the exact PDF hero compositions at full resolution, request unwatermarked, high-resolution exports of:
   - About & Process hero (olive tree / olive grove)
   - Services hero (product arrangement with utensils, basket and chess set)
4. **Review header nav**: decide whether to align the main menu with the PDF labels.
5. **Run `php -l`**: PHP is not available in this shell, so a syntax lint should be run locally on the changed templates.
6. **Regenerate permalinks** if any slug changes were made (none were made in this pass).

---

## Files changed

- `tpl-about.php`
- `tpl-process.php`
- `tpl-services.php`
- `inc/seed-pages.php`
- `css/figma.css`
- `languages/fr_FR.po`
- `languages/fr_FR.mo`
- `CHANGELOG.md`
