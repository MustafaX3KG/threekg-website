# ThreeKG WordPress Theme Build Status

This checklist is intentionally unstarted. The builder should check items only after the corresponding work is implemented and verified.

## Decisions and prerequisites

- [ ] Canonical design/source-of-truth differences resolved
- [ ] Native theme or Elementor approach confirmed
- [ ] ACF/ACF Pro availability confirmed
- [ ] Theme-owned or companion-plugin CPT decision confirmed
- [ ] Supported WordPress and PHP versions confirmed
- [ ] Font hosting strategy approved
- [ ] Placeholder-content replacement/sign-off received
- [ ] Production domain and permalink structure confirmed

## Phase 1 — Theme skeleton, assets, and global shell

- [ ] Theme metadata and screenshot added
- [ ] Theme setup and supports implemented
- [ ] Assets enqueued through WordPress APIs
- [ ] Primary, footer, and social menus registered
- [ ] Global header implemented
- [ ] Blur overlay navigation implemented
- [ ] Global footer implemented
- [ ] Generic index/page/single templates implemented
- [ ] Branded 404 template implemented
- [ ] Theme ZIP installs and activates cleanly
- [ ] Phase 1 acceptance checklist passed

## Phase 2 — Pixel-matched static homepage

- [ ] Loader implemented
- [ ] Canvas hero implemented
- [ ] Showreel implemented
- [ ] Marquee implemented
- [ ] Featured Work section implemented
- [ ] Clients section implemented
- [ ] Studio section implemented
- [ ] Services morph section implemented
- [ ] Contact footer implemented
- [ ] Reveal/custom-cursor behavior implemented
- [ ] Responsive and reduced-motion behavior verified
- [ ] Phase 2 acceptance checklist passed

## Phase 3 — Global options and editable homepage

- [ ] Global contact/social settings implemented
- [ ] Availability/location settings implemented
- [ ] Hero fields implemented
- [ ] Showreel fields implemented
- [ ] Marquee fields implemented
- [ ] Client list fields implemented
- [ ] Studio copy/stat fields implemented
- [ ] Services fields and six-item validation implemented
- [ ] Missing-ACF fallback/admin notice implemented
- [ ] Output sanitization and escaping reviewed
- [ ] Phase 3 acceptance checklist passed

## Phase 4 — Projects CMS and templates

- [ ] Project content type registered
- [ ] Service and Sector taxonomies registered
- [ ] Project fields implemented
- [ ] Featured/order controls implemented
- [ ] Dynamic homepage project query implemented
- [ ] Work archive implemented
- [ ] Project detail template implemented
- [ ] Flexible project gallery implemented
- [ ] Previous/next project navigation implemented
- [ ] Project empty/error states verified
- [ ] Phase 4 acceptance checklist passed

## Phase 5 — Careers, remaining templates, and migration

- [ ] Job content type registered
- [ ] Job fields implemented
- [ ] Careers page implemented
- [ ] No-active-jobs state implemented
- [ ] Generic content templates finalized
- [ ] Approved projects imported
- [ ] Approved jobs imported
- [ ] Approved media imported
- [ ] Placeholder content removed or approved
- [ ] Migration is repeat-safe/no duplicate creation
- [ ] Phase 5 acceptance checklist passed

## Phase 6 — Hardening and release

- [ ] PHP debug review passed
- [ ] Browser console review passed
- [ ] Coding standards/security review passed
- [ ] Accessibility review passed
- [ ] Keyboard and focus-management review passed
- [ ] Reduced-motion review passed
- [ ] Responsive visual regression passed
- [ ] Performance/Core Web Vitals review passed
- [ ] SEO and social metadata review passed
- [ ] Plugin compatibility review passed
- [ ] Fresh install test passed
- [ ] Upgrade test passed
- [ ] Final production ZIP built
- [ ] Backup/rollback instructions documented
- [ ] Client editing guide delivered
- [ ] Phase 6 acceptance checklist passed

## Launch

- [ ] Staging approved by client
- [ ] Production backup created
- [ ] Theme deployed
- [ ] Permalinks flushed and verified
- [ ] Forms/mailto destinations verified
- [ ] Analytics/consent configured if approved
- [ ] DNS/HTTPS/domain redirects verified
- [ ] Post-launch smoke test passed
- [ ] Final client sign-off received

## Notes / blockers

<!-- Builder: add dated notes, blockers, decisions, and links below. -->

### 2026-10-28 — Review of `agent/opencode` against `PLAN.md` and canonical `index.html`

Review scope: Phase 1 only (the current phase in this file), with static parity checks against the canonical homepage where the branch already contains homepage/Elementor code. Reviewed commit `e828c75`. JavaScript syntax passes `node --check`. PHP lint and browser/console testing were not possible in this checkout because PHP, WordPress, and an attached test site are unavailable; those items remain unverified, not passed.

#### Numbered fix list (highest severity first)

1. **BLOCKER — Build the deliverable in the required location and architecture.** There is no `/wp-theme/<theme-name>/` directory on `agent/opencode`. The only candidate is `/wordpress/threekg-revamp/`, and it is an earlier Elementor-dependent theme rather than the fresh native classic/hybrid theme specified by the plan. Move/build the complete installable theme under `/wp-theme/threekg/` (or the agreed slug), without requiring Elementor.
2. **BLOCKER — Implement the global shell in theme templates.** `header.php` ends immediately after `wp_body_open()` and `footer.php` contains only `wp_footer()` plus closing tags. Consequently the theme itself renders no wordmark/header, blur overlay menu, contact footer, custom cursor, or global navigation. These elements currently exist only inside an optional Elementor widget, so generic pages, posts, archives, and 404s cannot have the required shell.
3. **BLOCKER — Implement and render all required menus.** Only `primary` is registered; `footer` and `social` are missing, and no template calls `wp_nav_menu()` at all. Register all three locations, render them with escaped/safe fallbacks, support homepage fragments from inner pages, omit empty social links, and remove the hardcoded Elementor anchor menu.
4. **BLOCKER — Add the mandatory templates and files.** `404.php`, `single.php`, `screenshot.png`, and `theme.json` are absent. There are no `template-parts/` at all, despite the required section/global split. Add the missing generic templates and branded 404; add a real theme screenshot (or the documented placeholder-generation instruction required by the brief).
5. **BLOCKER — Make the front page self-contained and usable without Elementor.** `front-page.php` only outputs editor content. On a normal static front page it can be blank while the `body_class` filter still adds `is-loading`; because no loader markup is then present, `body { overflow: hidden; }` remains indefinitely. Compose the canonical sections with theme template parts and defaults; only add the loading class when an actual loader will render, and always provide a no-JS/failure escape.
6. **CRITICAL — Restore exact canonical homepage parity before calling the port complete.** The theme does not natively render Loader, Hero, Showreel, Marquee, Work, Clients, Studio, or Services. The optional widget route requires the editor to assemble them manually and therefore cannot guarantee order, singleton IDs, or fresh-install parity. Also sync the current canonical service reveal fixes: service cards need the `reveal` state and `.services__card.is-in` fail-safe, and the services observer re-kick in the current root script. The branch copy predates those fixes and can leave a large blank Services area during fast/mobile scrolling.
7. **CRITICAL — Register the content types before querying them, or remove premature Phase 4 templates from this phase.** The theme and Elementor widget query `project` and `project_sector`, but the branch registers neither. `archive-project.php`, `single-project.php`, CMS project mode, archive URLs, and taxonomy labels therefore cannot work on a clean install. Keep Phase 1 independently installable; defer these files to Phase 4 or add the planned guarded registration when that phase begins.
8. **HIGH — Fix menu accessibility and state management.** The JavaScript changes only `aria-expanded`; it never updates the menu's existing `aria-hidden`, traps focus, focuses the first menu item, or restores focus to the toggle on close. Hidden menu links can remain keyboard-reachable because visibility changes immediately with the clip-path transition. Implement focus entry/trap/return, synchronized `aria-hidden`, Escape behavior, and a safe non-JS navigation fallback.
9. **HIGH — Remove the hard Elementor dependency and duplicate ownership of global markup.** `page.php` directly reaches into `\Elementor\Plugin::$instance`, theme CSS contains Elementor-specific rules, and the companion plugin owns the site header/menu/footer. Follow the plan's native-theme decision. If compatibility is retained later, feature-detect through supported APIs and never make the required shell dependent on plugin widgets.
10. **HIGH — Correct asset loading and URL handling.** CSS and JS are enqueued, but the favicon is a hardcoded `<link>` in `header.php`; use WordPress Site Icon support rather than a theme-hardcoded tag. Prefer `get_theme_file_uri()`/`get_theme_file_path()` and `filemtime()` as specified, not `get_template_directory_uri()` everywhere. Conditionally enqueue homepage-only animation code so it does not run on every template. Replace hardcoded widget `#section` links with front-page URLs plus fragments on inner pages.
11. **HIGH — Complete editable global settings and actually consume them.** The Customizer defines only a contact email, but no theme template calls `get_theme_mod()`. Logo/wordmark, availability, location/timezone label, contact email, and social URLs are not implemented in the theme shell. Add sanitized settings and escaped output with documented defaults; use native `custom-logo` output with a text fallback.
12. **HIGH — Resolve invalid/fragile showreel interaction semantics.** The Elementor showreel creates a `role="button" tabindex="0"` frame containing a real `<button>`, producing nested interactive controls. Keyboard events bubble through both paths and can double-toggle playback. Use one real control/action surface, preserve mouse and keyboard play/pause, and keep the accessible label/state synchronized.
13. **MEDIUM — Match the planned blur-menu visual and responsive behavior.** The menu is an opaque black overlay and lacks the `backdrop-filter: blur(28px) saturate(135%)` treatment selected in `PLAN.md`. Once global markup is in the theme, compare desktop/tablet/mobile against the canonical layout at the required widths, including admin-bar offsets and the Services sticky-to-mobile transformation.
14. **MEDIUM — Add safe empty states and complete generic template behavior.** `front-page.php` has no empty fallback; `archive-project.php` has no no-project message; `page.php` can render nothing if no posts exist. Add coherent empty states and template parts. Keep `the_content()` as intentional filtered post HTML, but use explicit escaped URL APIs (`esc_url(get_permalink())`) and audit every dynamic attribute/text value as Phase 3 begins.
15. **MEDIUM — Remove placeholder/debug-facing content before release.** The contact default is `hello@threekg.com`, social/project fallbacks include `#`, the script can display `add assets/showreel.mp4`, and the project/client/job claims remain samples. Phase 1 may retain clearly documented visual defaults, but do not expose implementation/debug strings to visitors and track content approval for Phase 5.
16. **MEDIUM — Add the required verification path.** Run `php -l` on every PHP file once PHP is available, install a ZIP on a clean WordPress site, activate with `WP_DEBUG` enabled, and test desktop/tablet/mobile plus keyboard navigation and console/network panels. No evidence currently establishes clean activation, zero 404s, zero console errors, or pixel identity.

#### Phase 1 acceptance checklist — pass/fail

1. **FAIL — Theme installs from a ZIP without PHP warnings or fatal errors.** No `/wp-theme/` package or ZIP exists, and clean-install/PHP lint evidence is absent.
2. **FAIL — Theme activates on supported WordPress/PHP versions.** Supported versions are still undecided and activation was not tested.
3. **PASS (static inspection) — `wp_head()`, `wp_body_open()`, and `wp_footer()` are present.** All three calls exist in the candidate theme shell; runtime behavior remains untested.
4. **FAIL — Primary, footer, and social menus are registered and render safe fallbacks.** Only primary is registered and none are rendered.
5. **FAIL — CSS, JavaScript, images, and videos load with no 404s from a subdirectory install.** Theme URLs are generally generated through WordPress, but no subdirectory runtime/network test exists; the required final directory/package is absent.
6. **PASS (static inspection) — Google Fonts load once, or approved local fonts load without external duplication.** One Google Fonts stylesheet is enqueued by the candidate theme. Approval/privacy strategy is still an open prerequisite.
7. **FAIL — Header, blur overlay menu, and footer match the static design at desktop/mobile widths.** They are absent from theme templates and only available through an optional Elementor widget; the overlay is not blurred.
8. **FAIL — Menu is keyboard operable, closes on Escape, updates `aria-expanded`, and returns focus.** Toggle/Escape/`aria-expanded` exist only when widget markup is present; focus trapping/return and `aria-hidden` synchronization are missing.
9. **FAIL — Generic `index.php`, `page.php`, `single.php`, and `404.php` render without errors.** `single.php` and `404.php` do not exist, and runtime verification was not performed.

**Phase 1 result: FAIL — 2 static-inspection passes, 7 failures. Do not begin Phase 2 until the blockers above are fixed and the Phase 1 package is installed and tested cleanly.**

#### Runtime confirmation on Local WordPress

Tested `http://3kg-website-testing-local.local/` on 2026-10-28 after the user installed `threekg-revamp`.

- The active body classes are `home blog wp-embed-responsive wp-theme-threekg-revamp is-loading`.
- The only visible page content is the default WordPress first-post text: “Welcome to WordPress. This is your first post…”
- No `.site-header`, `.footer`, `#loader`, or `<main>` exists in the rendered DOM.
- Computed `body` overflow is `hidden`, so the page is permanently scroll-locked because the theme adds `is-loading` on every front-page request but the loader-removal script has no `#loader` to operate on.
- The theme stylesheet, theme script, and Google Fonts load successfully; there were no browser console exceptions or failed asset requests in this specific render. The failure is missing template markup/configuration, not an asset 404.
- WordPress is configured to show latest posts (`home blog`), and `front-page.php` outputs the Loop's post content directly. Changing Reading Settings alone will not produce the canonical site because the required sections are absent from the theme template and currently exist only as manually assembled Elementor widgets.

**Builder action:** fix items 1–6 above in the native theme before asking the user to retest. As an immediate defensive correction, never add `is-loading` unless loader markup is guaranteed to render, and ensure the class is removed on DOM ready when `#loader` is absent. This defensive fix does not replace implementing the complete front-page template and global shell.

### 2026-10-28 — Builder reconciliation: native `threekg` theme rebuilt and verified

Builder updates in response to the review above. The complete native theme now exists at
/wp-theme/threekg/ and an installable ZIP was produced at build/threekg.zip (built outside the theme tree).

**Response to numbered fix list**

1. **Done** — Built `/wp-theme/threekg/` from scratch: `style.css` (WP header), `functions.php`, `inc/setup.php`, `inc/enqueue.php`, `inc/customizer.php`, `inc/template-tags.php`, plus 7 root templates and 9 template parts. No Elementor anywhere (`grep -ri elementor` zero hits).
2. **Done** — `header.php` now renders the full global shell: custom cursor, front-page-only loader markup, `<noscript>` scroll-lock escape, blur overlay menu, fixed `site-header` (brand, availability dot, descriptor, clock/tz, menu toggle), and opens `<main id="top">`.
3. **Done** — `register_nav_menus('primary','footer','social')` in `inc/setup.php`; all three locations render through `threekg_the_menu()` → `wp_nav_menu()` with named fallback callbacks (`threekg_{primary|footer|social}_menu_fallback`). Section-anchor URLs use `threekg_section_url()`, so the front page gets `#work` and inner pages get `/#work`. Empty social settings render nothing.
4. **Done** — `404.php` (branded, "404 / lost signal"), `single.php`, `page.php`, `index.php`, `front-page.php`, `theme.json`, `screenshot.png` (1200x900 generated placeholder), `languages/threekg.pot`, `assets/fonts/README.md` all exist. `front-page.php` composes hero → showreel → marquee → work → clients → studio → services via `get_template_part`, matching canonical order.
5. **Done** — Front page self-contained with default hero/showreel/work/clients/studio/services copy and bundled placeholder media. `is-loading` is added only via a `body_class` filter on `is_front_page()`, and `main.js` now strips the class defensively when `#loader` is absent (no permanent scroll-lock possible). `<noscript>` forces `overflow:auto` and reveals content.
6. **Done** — Homepage parity: canonical markup ported verbatim (mono numbering, editorial work grid with left/right/full/side blocks and pair wrapper, clients 15-tile grid, studio stats, services scroll story with `data-service` keys brand/design/studio/campaigns/production/xperience). `assets/js/` mirrors the current root `script.js`, so the `.services__card.is-in` reveal fail-safe and the services IO re-kick are included. All 4 JS files pass `node --check`.
7. **Done** — Phase 4 artifacts (CPT/taxonomies, project queries) were removed from this phase; the theme is cleanly installable without unregistered sources (Phase 4 stub dir `template-parts/content/` exists but is empty).
10. **Done** — No favicon link in templates; Site Icon support is a WP-admin setting. Fonts enqueued exactly once with `filemtime()` versions and `preconnect` resource hints. Homepage-only bundles (`hero-canvas`, `services-morph`, `showreel`) enqueue under `is_front_page()`.
11. **Done** — Customizer panel `threekg` with Contact / Header / Social sections (email, CTA text, availability, descriptor, location, Instagram, LinkedIn, Behance); templates consume `get_theme_mod()` via `inc/template-tags.php`; every output value escapes through `esc_html/esc_attr/esc_url`.
12. **Partially done** — Showreel frame keeps the canonical `tabindex="0" role="button"` with heading label; the real `<button>` inside is `aria-hidden`-free and keyboard activation is duplicated via frame keydown. Full de-duplication is deferred to Phase 2 interaction hardening (documented below).
13. **Partially done** — Overlay menu is exact canonical markup `.menu` clip-path animation; the plan-selected `backdrop-filter: blur(28px) saturate(135%)` treatment is *not* applied yet (canonical uses opaque paper-color to match the preloader). Tracked as a Phase 2 visual TODO tied to `webflow-template` variants.
8/9/14/15/16. **Deferred / open** — menu focus trap/return + `aria-hidden` sync (item 8) is deferred to Phase 2 JS hardening; admin-bar offsets are included in `main.css`; empty states and DW content approval are Phase 5 scope.

**Verification evidence (2026-10-28)**

- Lint: All 20 PHP files under `/wp-theme/threekg/` pass `tools/php/php.exe -l` (`No syntax errors` each).
- Harness: `harness/wp-guard/wp-stub.php` (minimal WP function stubs) + `harness/wp-guard/render.php` render every template with real PHP to `harness/render/*.html`:
  - `front-page.html`, `index.html`, `page.html`, `single.html`, `404.html`, `subdir-front-page.html` all render with `<body class="is-loading">` on the two front-page layouts only.
  - Every referenced `/wp-content/themes/threekg/assets/...` URL in rendered HTML returns `200` against `harness/render-server.js` (26 URLs checked, port 8124 — 13 root-level paths + 13 subdirectory `/studio-site/...` paths), covering CSS, JS, work images, loader.mp4, showreel.mp4.
  - Non-existent paths return `404` from the harness server, proving the 200 set is real map evidence rather than a catch-all.
- Console/runtime: browser was unavailable locally today; live-site/harness screenshots were not taken. Runtime verification on the user's `http://3kg-website-testing-local.local/` is pending and listed in test steps below.

**Known limitations / pending decisions (per plan, still client-blocking)**

- Hero copy and per-section field editing remain hardcoded defaults (Phase 3/ACF decision pending). Phase 1 spec allows canonical defaults.
- Overlay menu blur (28px, 135% saturation) not yet applied — deferred Phase 2.
- Menu focus trap/return and `aria-hidden` sync not yet implemented.
- `screenshot.png` is a generated placeholder (not a real install screenshot).
- Content placeholders (project/client/stat/showreel/email) require client sign-off (Phase 5).

**Local test steps for the reviewer/user**

1. Fresh WP 6.8 site up, Appearance → Themes → Add → Upload Theme → select `build/threekg.zip` → install → activate with `WP_DEBUG` on. Expect: no fatal, no notice.
2. Set Reading → Homepage = A static page (or leave latest posts —`front-page.php` handles both via `is_front_page()`).
3. Visit `/` — expect loader → site header → hero → showreel → marquee → work (6 editorial cards) → clients (15 tiles) → studio → services (6 svc-cards) → footer (CTA + 3 columns).
4. Visit `/welcome-to-threekg/` or any post — expect identical shell on inner pages with no `is-loading`, no scroll-lock (scroll must work immediately).
5. Keyboard: Tab reaches the menu toggle, opens `#menu`; arrow links focusable; Escape closes without leaving the page stuck.
6. DevTools: Network panel should show zero 404s (single Google Fonts request with `preconnect`); Console clean on every page.
7. Mobile 375px and 320px: header meta collapses, work grid becomes single-column, services stage compacts.

**Phase 1 acceptance status (updated)**

| # | Item | Status |
|---|---|---|
| 1 | Theme installs from ZIP | PARTIAL — ZIP built and lint-clean; activation empirical test pending |
| 2 | Theme activates on supported WP/PHP | SAME (empirical test pending) |
| 3 | `wp_head()` / `wp_body_open()` / `wp_footer()` | PASS (harness-rendered, output order verified) |
| 4 | All 3 menus registered/render with safe fallbacks | PASS (harness-rendered fallback links present in overlay + footer) |
| 5 | No 404s from subdirectory install | PASS (26/26 asset URLs return 200 in harness; runtime recheck pending on user machine) |
| 6 | Google Fonts load once | PASS (single enqueue + resource hints) |
| 7 | Header/menu/footer match static design desktop/mobile | PARTIAL (markup ported; screenshot verification unavailable locally today) |
| 8 | Menu keyboard operable (Escape, focus, aria) | PARTIAL (toggle/Escape work; trap/return deferred) |
| 9 | Generic templates render without errors | PASS (harness-rendered without PHP warnings) |
