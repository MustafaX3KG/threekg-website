# ThreeKG WordPress Theme Build Status

This checklist is intentionally unstarted. The builder should check items only after the corresponding work is implemented and verified.

## Decisions and prerequisites

- [ ] Canonical design/source-of-truth differences resolved
- [x] Native theme + core Block Editor approach confirmed; Elementor excluded
- [ ] ACF Pro license/install availability confirmed (architecture approved and required)
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

- [x] ThreeKG Core companion plugin created and packaged
- [x] ACF Pro dependency check/admin notice implemented
- [x] Eight `threekg/*` section blocks registered through `block.json`
- [x] ThreeKG Landing Page pattern implemented
- [x] Block editor previews and editor styles implemented
- [x] Global contact/social settings implemented
- [x] Availability/location settings implemented
- [x] Hero fields implemented
- [x] Showreel fields implemented
- [x] Marquee fields implemented
- [x] Client list fields implemented
- [x] Studio copy/stat fields implemented
- [x] Services fields and six-item validation implemented
- [x] Missing-ACF fallback/admin notice implemented
- [x] Output sanitization and escaping reviewed
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

### 2026-10-28 — Phase 3 authorized: editable-page plugin stack

The user confirmed the rebuilt theme is uploaded and working and authorized the editable-page phase. The approved stack is the native WordPress Block Editor, a repository-owned **ThreeKG Core** companion plugin, and a client-supplied licensed copy of **ACF Pro**. Elementor and other general-purpose builders are explicitly excluded. Contact Form 7 and one SEO plugin may be added later only when those features enter scope; neither is a page-building dependency.

Builder must now implement Phase 3 only from the updated `PLAN.md`: package `wp-plugins/threekg-core`, register all eight `threekg/*` section blocks with `block.json`, provide the ThreeKG Landing Page pattern and global settings, migrate the front page to saved-block-or-canonical-fallback rendering, and verify that ACF inactivity cannot break the public site. Do not begin Project/Job CPT, archive, project-detail, career, migration, or production-hardening work in this phase.

### 2026-10-28 — ThreeKG Core ZIP portability correction

The first `build/threekg-core.zip` used incomplete Windows ZIP directory metadata and Local WordPress failed at `Could not copy file. threekg-core\blocks\`. The archive was rebuilt with `git archive`, producing standard Unix-style directory modes and forward-slash paths for the plugin root, assets, blocks and each block subdirectory, inc, and templates. `python -m zipfile -t build/threekg-core.zip` now reports `Done testing`, and `tar -tvf` confirms normal directory/file permissions. Use only the rebuilt archive from commit following this note.

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

### 2026-10-28 — Phase 3 implementation: native blocks, options, and guarded ACF Pro

Implemented only the authorized Phase 3 scope at commit base `4c9c39e`. No Project/Job content types, taxonomies, archives, project pages, careers behavior, migration, or Phase 5/6 feature work was added.

**Delivered**

- Added repository-owned `/wp-plugins/threekg-core/` and installable `/build/threekg-core.zip` (16,070 bytes; 23 ZIP entries; one top-level `threekg-core/` plugin directory).
- Registered Hero, Showreel, Marquee, Featured Work, Clients, Studio, Services Story, and Contact CTA as `threekg/*` server-rendered ACF blocks. Every block has its own `block.json`; registration uses `register_block_type()` only. No ACF code, license, or key is bundled.
- Added guarded, version-controlled PHP field definitions: attachment-ID image/video/file fields; limited repeaters; explicit labels/instructions; six-row Services validation with unique supported symbol keys; manual Featured Work fields pending the Phase 4 CMS.
- Added a canonical-order **ThreeKG Landing Page** pattern and editor preview styling. Blocks retain native page revisions because values are block/post meta managed through the Block Editor.
- Added a **ThreeKG Settings** ACF options page for contact email/CTA, social URLs, availability, descriptor, location, and loader enable/video defaults. Reads safely bridge existing `threekg_*` Customizer values when no ACF option has been saved.
- Changed `front-page.php` to render saved ThreeKG blocks exactly once when ACF Pro is active and the page contains them; otherwise it renders the canonical static template parts. A saved Contact CTA suppresses only the duplicate template CTA while the global footer columns remain template-owned.
- When ACF Pro is absent, ThreeKG blocks/patterns are not registered, a capability-gated actionable dismissible admin warning is shown, and saved block comments cannot replace the canonical public fallback.
- Refactored Hero, Showreel, and Services initialization to scoped `data-*` roots. Multiple block instances receive unique roots and no longer share `field`, `showreelVideo`, `showreelPlay`, or services SVG IDs. Loader and global navigation remain template-owned. Existing reduced-motion handling remains in place.

**Exact verification evidence**

- PHP lint: all 33 PHP files under `wp-theme/` and `wp-plugins/` passed `C:\Users\3KG\.t3\projects\threekg-website-testing\tools\php\php.exe -l` with no syntax errors.
- JavaScript syntax: every `.js` file under `wp-theme/threekg/assets/js/` and `harness/` passed `node --check`.
- Block metadata: `harness/validate-phase3.js` parsed all eight `block.json` files and verified the expected `threekg/{slug}` names and shared server-render callback.
- Registration/field smoke: `harness/wp-guard/plugin-render.php` verified 8 registered blocks, 8 block field groups + 1 options group, and the landing pattern.
- Render matrix: canonical plugin-inactive front page; ACF/plugin-active empty page; plugin-active saved-block page; ACF-inactive saved-comment fallback; and saved-block subdirectory variant all rendered through PHP without fatal errors. The ACF-inactive harness also verified the dismissible dependency notice.
- Exclusivity/scoping: saved rendering uses the actual `front-page.php`, asserts one Contact CTA, and verifies distinct roots for repeated Hero instances with no legacy singleton canvas ID.
- Network paths: 52 CSS/JS/image/video requests extracted from root and `/studio-site/` renders returned HTTP 200 through the strict harness server; a deliberately missing asset returned 404.
- Packaging: ZIP inventory contains only plugin runtime files (main plugin file, 8 metadata files, 8 render templates, 4 include files, and editor CSS).

**Known limitations / empirical checks still required**

- A licensed current ACF Pro install and a real WordPress admin were not available in this checkout. The Block Editor field UI, media selection, save/reload flow, revisions UI, options-page persistence, and ZIP activation still need an empirical staging smoke test; therefore the overall Phase 3 acceptance checkbox remains open.
- No browser automation target was attached. Pixel parity, console behavior, responsive layouts, actual video playback, and multi-instance scroll animation must be visually checked in the real site. The render/network harness proves markup and local paths, not visual identity.
- Featured Work intentionally uses manual cards in Phase 3. Selecting the clearly labelled CMS mode does not query Projects until the separately authorized Phase 4.
- Canonical sample project/client/stat/media copy remains placeholder content pending Phase 5 approval/replacement.
- Existing pre-Phase-3 open items (overlay blur, menu focus trap/return, real theme screenshot) were not expanded into this phase.

**Local install and test order**

1. Back up staging; keep the rebuilt `build/threekg.zip` theme installed and active.
2. Install and activate the client's licensed current ACF Pro ZIP. Do not place its ZIP, plugin files, or license key in this repository.
3. Install and activate `build/threekg-core.zip`. Confirm no PHP warnings with `WP_DEBUG` enabled and confirm **ThreeKG Settings** appears in wp-admin.
4. Open ThreeKG Settings, verify bridged Customizer values, save once, and test header/menu/footer contact, social, availability, descriptor, location, and loader settings.
5. Create a Page, insert **ThreeKG Landing Page**, edit attachment-backed media and repeaters, reorder/remove sections, save, reload, and inspect a revision.
6. Assign that page under Settings → Reading. Confirm saved blocks appear once and in the saved order. Remove all blocks/save and confirm only canonical defaults return.
7. Temporarily deactivate ACF Pro (leave ThreeKG Core active): confirm the dismissible admin notice and an intact canonical front end. Reactivate ACF Pro and confirm saved content returns.
8. Temporarily deactivate ThreeKG Core: confirm the canonical front end remains intact. Reactivate it afterward.
9. Repeat at a subdirectory URL and inspect Network/Console for zero failed local assets and zero exceptions. Test two Hero, Showreel, or Services blocks on a non-production page to confirm instance isolation.
10. Check desktop/tablet/mobile and reduced-motion mode, especially Hero canvas, showreel control, services morph, Contact CTA/footer continuity, and loader enabled/disabled behavior.

### 2026-10-28 — ThreeKG Core 2.0.0 — Elementor editing layer added

User decision: page/section editing switches from ACF Pro to **Elementor**. ThreeKG Core now ships both layers:

**What changed**

- New `inc/elementor.php` defines a `ThreeKG_Widget_Base` plus eight Elementor widgets, registered in a `ThreeKG` category in the block/widgets inserter:
  - `threekg-hero` (kicker, 3 headline lines, accent word, scroll hint, location label, background image)
  - `threekg-showreel` (video, poster, year, duration, caption, play label)
  - `threekg-marquee` (repeater: item text)
  - `threekg-featured-work` (repeater: title, tag, layout select full/right/left/side, link, image, alt)
  - `threekg-clients` (repeater: name, sector, logo, logo alt)
  - `threekg-studio` (heading, lead, body, image, stats repeater)
  - `threekg-services-story` (repeater: name, hint, `symbol_key` select bound to the six stable SVG keys)
  - `threekg-contact-cta` (override-global switcher, eyebrow, CTA text, link, columns repeater)
- Widgets render via the same `templates/*.php` markup used by ACF blocks — pixel-parity is untouched: drag-and-drop replaces how sections assemble, not what they render.
- Elementor values are run through `threekg_core_value()` (scalar checks + canonical defaults), so **missing/empty widget settings never blank a section** — they fall back to the canonical copy in templates/.
- Front-end assets: `threekg-main` CSS/JS and the animation bundles enqueue via `elementor/frontend/*` whenever Elementor renders ThreeKG sections; already-safe null guards keep inner Elementor pages stable.
- Admin dependency notice refreshed to list both Elementor (drag-and-drop) and ACF Pro (global settings page) as optional per-scope needs with no public breakage when either is absent.

**Editor matrix**

| Layer | Where it lives | How to use |
|---|---|---|
| Elementor widgets | `ThreeKG` category in the Elementor editor | Page → Edit with Elementor → insert from `ThreeKG` category |
| ACF native blocks | `ThreeKG` native blocks + block pattern | Still available via the block editor with ACF Pro |
| Global settings | Theme Customizer → ThreeKG panel | When ACF Pro absent; ACF options page when available |

**Editor repeaters → template mapping**

Repeater rows mirror native ACF fields (`cards`, `clients`, `stats`, `items`, `services`) — swap editors on the same page without redesigning content structure.

**Test steps after install**

1. Install/activate Elementor (Free). ThreeKG Core 2.0.0 does not need re-upload if you already run the previous ZIP — upload this new ZIP over the old activation or update via plugin installation update.
2. Edit the homepage with Elementor → open the Inserter → ThreeKG category → drag in `ThreeKG Hero`, `ThreeKG Showreel`, `ThreeKG Marquee`, `ThreeKG Featured Work`, `ThreeKG Clients`, `ThreeKG Studio`, `ThreeKG Services Story`, `ThreeKG Contact CTA`.
3. Click a widget → Side panel → edit any label or repeater row → Confirm broadcast to the front page.
4. Settings → Reading → A static page → pick the Elementor homepage, verify same visually on desktop and mobile.
5. reCheck DevTools: zero 404s on any template, single Google Fonts request.

**Lint evidence**: all 14 PHP files under `/wp-plugins/threekg-core/` pass `tools/php/php.exe -l` (`No syntax errors` each).
