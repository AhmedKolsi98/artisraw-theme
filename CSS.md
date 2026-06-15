# ArtisRaw — CSS Architecture & Concatenation

This document explains how the theme's CSS is organised, concatenated, and cached.

## Source files

Styles are authored as modular sheets in `css/`:

| File | Responsibility |
|------|----------------|
| `tokens.css` | Design tokens: colours, typography, spacing, motion. |
| `base.css` | Reset, base element styles, grid utilities, buttons. |
| `layout.css` | Header, nav, drawer, footer, breadcrumbs. |
| `components.css` | Reusable components: cards, tables, accordions, stats, docs. |
| `forms.css` | Form elements, validation states, buttons loading state. |
| `templates.css` | Page-level layouts: wholesale hub, trust pages, contact. |
| `phase5.css` | Design-parity components: steps, testimonials, world map, packs. |
| `art.css` | Art-direction layer: colour-block surfaces, statement hero, mosaic. |
| `figma.css` | Figma visual integration: photo hero, caption grid, value cards. |

`account.css` is loaded separately and only on `tpl-account.php`.

## Runtime concatenation

`functions.php` defines `artisraw_get_concat_css_url()`. At runtime it:

1. Reads the nine source files in the order above.
2. Rewrites relative font URLs (`url("../fonts/...")`) to absolute theme URLs so they still resolve from the cached file location.
3. Concatenates the files with section comments.
4. Writes the result to `wp-content/uploads/artisraw-cache/theme-{hash}.css`.
5. Returns the public URL of the cached file.

`artisraw_enqueue_assets()` then enqueues that single stylesheet. If concatenation fails (e.g. uploads directory is not writable), the function returns an empty string and the theme falls back to enqueueing the nine individual files.

## Cache invalidation

The `{hash}` is an md5 of each source file path plus its `filemtime`. Editing any CSS file changes the hash, so the next request generates and enqueues a new URL. Old cached files are deleted when a new file is written.

This gives the same instant cache-busting behaviour as the previous `filemtime` query strings, but with only one HTTP request.

## Development workflow

- Edit files in `css/` directly. There is no build step.
- Hard-refresh the browser after CSS changes to pick up the new hashed URL.
- To verify concatenation is working, open the browser network tab and confirm a single `theme-{hash}.css` request.
- To force regeneration, edit any CSS file or delete the `artisraw-cache/` folder in `wp-content/uploads/`.

## Fallback

If the uploads directory cannot be created or is not writable, the theme silently falls back to enqueueing the individual source files. This ensures the site still renders correctly on hosts with unusual permissions.
