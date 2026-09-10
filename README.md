# WP Newspaper Theme

Hand-coded WordPress theme base for community newspaper / local-publication
sites — no page builder (Divi/Elementor/etc.), no plugin dependency for core
layout, navigation, or article listing. Fork this per project rather than
building from scratch each time, the same way `wp-starter-theme` works for
non-newspaper small-business sites.

Forked from `wp-starter-theme`. First used to build thefmextra.com (2026-09).

## Using this for a new project

1. Copy this theme into the new site's `wp-content/themes/`, rename the
   folder and the `Theme Name` in `style.css`.
2. Update the design tokens at the top of `style.css` (`--color-primary`,
   fonts, etc.) to match the project's brand.
3. Set the site logo under Appearance → Customize → Site Identity —
   `header.php` falls back to the site name as text if none is set.
4. Set up the actual WordPress menu under Appearance → Menus and assign it to
   "Primary Menu" — `np_fallback_menu()` in `functions.php` is just a
   placeholder shown before that's configured.
5. Add ads/social widgets to the "Sidebar" area under Appearance → Widgets
   (stock Image/Custom HTML widgets — there's no custom ad-widget system).
6. `page.php` is a plain content template. For pages that need a distinct
   layout (a masthead/staff page, a subscription page, etc.), add a
   `page-{slug}.php` template rather than hardcoding site-specific content
   into this base theme.

## Known gotchas (learned the hard way, don't re-learn these)

- **Cache-bust `style.css` and `nav.js` via `filemtime()`** (already done in
  `functions.php`'s `np_assets()`) — without it, browsers keep serving a
  stale copy after every edit and you'll waste time debugging changes that
  "aren't taking effect" when they actually are.
- **The mobile submenu's `display: block !important` is deliberate**, not
  sloppiness — a `.menu-item-has-children.submenu-open > .sub-menu { display:
  block; }` override that should have won on specificity alone wasn't
  reliably taking effect during the original (`wp-starter-theme`) build.
  `!important` was the pragmatic fix. If you ever remove it, retest the
  mobile submenu toggle carefully before shipping.
- **Don't nest custom markup inside `the_custom_logo()`'s output.** It
  renders its own `<a><img></a>`; wrapping it in another `<a>` produces
  invalid nested anchors that browsers silently "fix" by hoisting the image
  out of your wrapper, breaking any CSS that targets it as a descendant.
  Wrap in a `<div>`, not an `<a>` (see `header.php`).
- **If deploying to a host with an object cache (Redis, etc.),** content
  updates made via `wp_update_post()`, `wp option update`, or raw SQL can
  appear "not to have happened" when read back immediately via `wp post get`
  or in a browser — the cache, not the database, is stale. Flush it
  (`wp cache flush`, or the host's own cache-purge command) before assuming
  an update failed.
- **`category.php`/`tag.php` don't exist on purpose.** `archive.php` already
  covers category, tag, and date archives via WordPress's template hierarchy
  and reuses the same `np_article_card()` list markup — don't duplicate it
  into a separate file unless a specific archive type genuinely needs
  different markup.

## Files

- `style.css` — design tokens + all CSS (reset, top bar, nav incl. mobile
  hamburger, article list/cards, single article, sidebar, pagination, plain
  pages, generic section/hero/card-grid utilities for custom page layouts)
- `functions.php` — theme setup, sidebar registration, asset enqueue, nav
  fallback, excerpt length/"Read more" marker
- `header.php` / `footer.php` — masthead + nav / footer
- `sidebar.php` — renders the "Sidebar" widget area
- `js/nav.js` — mobile hamburger toggle + tap-to-expand submenus (vanilla JS)
- `inc/template-tags.php` — `np_article_card()`, the shared article-list row
- `inc/pagination.php` — `np_pagination()`, shared numbered pagination
- `front-page.php` — homepage article list + sidebar
- `archive.php` — category/tag/date archives, same list layout
- `search.php` — search results, same list layout
- `single.php` — single article view
- `page.php` — plain content page (About, Staff, Subscription, etc.)
- `404.php`, `index.php` — fallbacks
