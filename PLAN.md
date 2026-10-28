# ThreeKG WordPress Theme Conversion Plan

## Scope and source of truth

The repository contains three related implementations:

1. **Canonical static homepage:** root `index.html`, `assets/style.css`, `assets/script.js`, and root `assets/`. This is the most complete interaction implementation and is the visual source of truth for the homepage.
2. **Multi-page static concept:** `webflow-template/`. This adds Work, Project detail, and Careers page designs plus Project/Job CSV schemas. Its blur menu and multi-page information architecture should be incorporated into the WordPress theme where it does not conflict with the canonical homepage.
3. **Earlier WordPress work:** `wordpress/threekg-revamp/` and `wordpress/threekg-elementor/`. These are useful implementation references, but are incomplete: the theme header has no rendered site navigation, the footer is empty, the front page delegates entirely to post content, there is no `404.php`, and the inspected theme code does not register the Project post type it queries. This code should not be treated as the finished theme.

Build a fresh, installable classic/hybrid WordPress theme from the canonical static files. Preserve the current markup classes and interactions where practical so visual regression is minimized. Do not require Elementor. Use native WordPress templates, reusable block patterns, registered menus, Customizer/site options, and structured content types. ACF Pro is recommended for flexible project galleries and option pages; if ACF is not available, the theme must fail gracefully and retain basic title/content/featured-image editing.

### Approved editing architecture (2026-10-28)

The site will use the native WordPress Block Editor as its visual page builder. Do not add Elementor, Elementor Pro, Divi, WPBakery, Bricks, Beaver Builder, or another general-purpose page builder. Those tools can rewrite the carefully matched markup, duplicate IDs, enqueue competing CSS/JavaScript, and break the canvas/showreel/services interactions.

Required plugin stack:

1. **ThreeKG Core** — a repository-owned companion plugin delivered under `/wp-plugins/threekg-core/` and packaged as `/build/threekg-core.zip`. It owns the custom ThreeKG blocks, their `block.json` registrations, local ACF JSON/PHP field definitions, editor previews, block patterns, validation, and—during later phases—the Project and Job content types. Content types and reusable editing tools belong here rather than in the theme so content remains available after a theme switch.
2. **Advanced Custom Fields Pro** — client-supplied licensed plugin, using the current supported stable release. ACF Pro is required because the approved editor needs ACF Blocks, repeaters, flexible content/gallery fields, and global options pages. Do not bundle ACF Pro or a license key in the repository or release ZIP. Register blocks with `block.json` and `register_block_type()`; do not build new blocks with the legacy `acf_register_block_type()` API.
3. **WordPress Block Editor (core)** — not an extra plugin, but the required page-editing UI. Do not install the experimental Gutenberg plugin on production. The theme and ThreeKG Core plugin must target the editor included with the supported WordPress release.

Optional plugins, not dependencies of the theme or page builder:

- **Contact Form 7** only if the client replaces the current mailto CTA with a real form. The ThreeKG Core plugin may supply a styled pattern/shortcode wrapper, but form delivery and storage must remain plugin-owned.
- **Yoast SEO or Rank Math** (choose one at launch, never both) for SEO metadata and social previews. Templates must not require either plugin.
- A caching/security/backup plugin selected by the host may be added during Phase 6 after regression testing. It must not combine/defer the animation bundles without verification.

No other plugin is required to create pages, rearrange sections, or build a page similar to the homepage.

Editor behavior and content portability:

- Each designed section becomes a first-class `threekg/*` ACF Block with a live or representative editor preview and the same semantic classes as the front end: Hero, Showreel, Marquee, Featured Work, Clients, Studio, Services Story, and Contact CTA.
- A **ThreeKG Landing Page** block pattern inserts the approved homepage section order. A user can create another landing page by inserting this pattern, then editing, removing, or reordering its blocks.
- Global header, overlay-menu contact details, footer contact details, social URLs, availability, descriptor, and loader defaults live on one ThreeKG Settings options page. Navigation continues to use WordPress menu locations.
- The homepage template renders saved blocks when the assigned front page has ThreeKG block content. If the page is empty or ACF Pro is inactive, it renders the current canonical defaults. It must never render both paths at once.
- Block markup is server-rendered and escaped. Animation scripts initialize by scoped `data-*` roots, tolerate multiple valid block instances, and do not depend on editor-generated wrapper depth.
- Blocks that require singleton IDs or global behavior (Loader and global navigation) are not insertable page blocks. They remain global template components driven by options.
- The editor exposes only meaningful design controls. Users can change content, media, links, order, visibility, and approved variants; arbitrary colors, spacing, IDs, or class names are not exposed when they would break pixel parity.
- All field groups are version-controlled in the ThreeKG Core plugin. Production configuration must not depend on manually recreating fields in wp-admin.
- ACF Pro absence produces a dismissible administrator notice and preserves the canonical front end; it must not cause a fatal error or blank page.

Editable block contract:

| Block | Editable content | Constraints |
|---|---|---|
| Hero | Kicker, three headline lines, italic accent, supporting labels, optional background media | One per landing page; canvas remains theme-owned |
| Showreel | Video, poster, year, duration label, caption, play label | Uses attachment IDs and keyboard-safe single control |
| Marquee | Repeating text items | Minimum 2 items; duplicated track is render-only |
| Featured Work | Heading, eyebrow/year, source mode, selected projects/manual cards, card order | CMS mode becomes authoritative in Phase 4; manual fallback allowed before then |
| Clients | Heading, year, client name/logo, sector, tile order | Empty tiles are not rendered; logos require alt text |
| Studio | Heading, lead/body copy, optional image, statistics | Stats repeater has sensible maximum and empty-state handling |
| Services Story | Heading and six services with name, hint, stable symbol key | Exactly six published items; keys are unique and limited to the six supported geometries |
| Contact CTA | Label, CTA text, email/link, optional local footer columns | Global defaults inherited from ThreeKG Settings and overridable per page only when enabled |

Phase 3 is now the authorized next implementation phase. Its first deliverable is the ThreeKG Core plugin plus editable blocks/options and migration of the existing front page to the block-backed/fallback rendering contract. Do not begin Projects/Jobs CMS work from Phase 4 in the same change.

---

## 1. Complete repository inventory

### 1.1 Pages and templates represented by the static source

| Page | Source | Content/behavior | WordPress destination |
|---|---|---|---|
| Homepage | `/index.html` | Full canonical one-page studio experience | `front-page.php` |
| Work archive | `/webflow-template/work.html` | Editorial six-project archive | `archive-project.php` plus `/work/` rewrite |
| Project detail | `/webflow-template/project-template.html` | Hero, facts, gallery, next project | `single-project.php` |
| Careers | `/webflow-template/careers.html` | Intro, active vacancies, speculative application CTA | `page-careers.php` or a Careers page template querying Job posts |
| Generic page | Existing WP reference | Title and editor content | `page.php` |
| Generic single post | Not designed in static source | Standard title/content shell | `single.php` |
| Blog/index fallback | Not designed in static source | WordPress Loop fallback | `index.php` |
| Not found | Not present | Must be designed in the same visual language | `404.php` |
| Privacy/legal | Mentioned in handoff, no design | Standard page content | `page.php` |

There is no dedicated static Contact page. Contact is a footer section/CTA. There is no blog design in the current static website.

### 1.2 Canonical homepage sections, in order

1. **Brand preloader**
   - Full-screen paper-colored overlay.
   - Autoplay, muted, inline `assets/brand/motion/loader.mp4`.
   - Video playback rate set to 2×.
   - Click/tap, Escape, Enter, or Space skips it.
   - Ends automatically on video completion/error, with a six-second safety timeout.
   - Disabled for `prefers-reduced-motion`.

2. **Custom cursor**
   - Eight-pixel dot following pointer with eased `requestAnimationFrame` motion.
   - Enlarges over links, buttons, and project blocks.
   - Uses `mix-blend-mode: difference`.
   - Hidden for coarse pointers/touch devices.

3. **Fixed global header**
   - ThreeKG® wordmark.
   - Availability indicator with orange dot.
   - “minimalism · space · motion” descriptor.
   - Live local clock and calculated GMT offset.
   - Menu/close toggle.
   - Header uses difference blend mode.

4. **Full-screen overlay menu**
   - Canonical homepage anchors: Index, Showreel, Work, Clients, Studio, Contact.
   - Footer contact email and Instagram, LinkedIn, Behance placeholders.
   - Open/close animation; body scroll lock; closes on link click or Escape.
   - The `webflow-template` variant adds separate Work and Careers pages and a translucent `backdrop-filter: blur(28px) saturate(135%)` treatment. The WordPress version should combine the canonical menu content with this blur treatment and use real WordPress menu items.

5. **Hero**
   - Full viewport height.
   - Kicker: independent studio/established year.
   - Three-line editorial headline with italic/soft final phrase.
   - Footer labels for scrolling, working worldwide, and section count.
   - Full-section HTML canvas dot field.
   - Dot grid idly drifts, repels from pointer, changes size/color near the pointer, responds to resize, and pauses when offscreen.

6. **Showreel**
   - Responsive 16:9 frame using `assets/showreel.mp4`.
   - Muted, looped, inline video with metadata preload.
   - Poster-like CSS gradient while loading.
   - Scrim, section/year caption, play/pause control, duration label.
   - Click and keyboard activation.
   - Automatically pauses when scrolled out of view.

7. **Services marquee**
   - Continuous duplicated horizontal marquee.
   - Items: Brand Identity, Web Design, Creative Development, Art Direction, Motion.
   - Decorative star separators.
   - Hidden from assistive technology because content repeats elsewhere.

8. **Featured Work**
   - Section label/year range and large “Featured work.” heading.
   - Procedurally drawn animated section symbol.
   - Six editorial project cards with alternating layouts: full, right, left/side pair, full, right.
   - Each card has image, project title, service/category pill, hover scale, and accent title transition.
   - Placeholder projects: Halcyon Hotels, Meridian Motion, North Light Archive, Onto & Co., Studio Ferro, Vellum Editions.
   - Current root links are `#`; WordPress must bind them to Project permalinks.

9. **Clients**
   - Section heading/year.
   - Fifteen fixed 153×153 text tiles with client name and sector.
   - Current names are explicitly placeholder content and must not be published as factual without approval.
   - Responsive five/four/three/two-column wrapping.

10. **Studio**
    - Large editorial heading.
    - Lead and secondary paragraphs.
    - Four statistics: projects, clients, awards, years.
    - Two-column copy collapsing to one column.

11. **Services scroll story**
    - Sticky left stage with heading and procedural SVG.
    - Six tall service cards on the right.
    - Services: Brand Identity, Web Design, Creative Development, Art Direction, Production, Motion & Interaction.
    - SVG contains generated/resampled line paths that morph between six geometries while scrolling.
    - Active service index and label update with scroll position.
    - Cards fade/translate into active state.
    - Separate symbol-button system supports hover, focus, click pinning, outside-click/Escape clearing, contraction to a dot, ping-pong animation, and one-shot construction on reveal.
    - Responsive transformation to a top sticky stage on tablet and a compact stage on mobile.

12. **Footer/contact**
    - Dark inverted background.
    - Large mailto CTA: “Start a project.”
    - Studio column with name, “working worldwide,” and live clock.
    - Contact column with email and three social placeholders.
    - Site column with back-to-top, copyright, and closing line.

13. **Global reveal system**
    - `.reveal` elements enter from 26px below with opacity transition.
    - Powered by `IntersectionObserver`.
    - Disabled for reduced motion.

14. **Global visual treatments**
    - Paper/off-white background, near-black ink, orange accent.
    - CSS film grain using an inline SVG data URI.
    - Responsive layout tokens and fluid `clamp()` typography/padding.
    - Smooth scrolling except in reduced-motion mode.

### 1.3 Additional multi-page static sections

#### Work archive

- Page hero: “Selected work.”
- Editorial Project card grid using the same six records.
- Fixed global header and blur menu.
- Footer project CTA.

#### Project detail template

- Project services/year eyebrow.
- Large two-line project title.
- Full-width hero media.
- Large summary.
- Project facts: client, sector, scope/services, year.
- Mixed landscape/portrait media gallery.
- Next-project navigation.
- Footer contact CTA.

#### Careers

- Hero: “Come make good work.”
- Open-positions list with title, employment type, location, and application link.
- General application section and email CTA.
- Starter roles in `cms/jobs.csv`: Senior Designer, Motion Designer, Creative Developer. These are placeholders.

### 1.4 Forms

- **No HTML forms exist anywhere in the static site.**
- Contact and career applications currently use `mailto:` links.
- If a real contact/application form is added later, use a WordPress form plugin or a small server-side handler with nonces, sanitization, spam protection, success/error states, and configurable recipients. Do not silently invent a form during visual conversion.

### 1.5 Menus

- Canonical overlay menu: six homepage anchors.
- Multi-page concept menu: Index, Work, Services, Careers, Contact.
- Footer contains back-to-top and social/contact navigation.
- WordPress should register `primary`, `footer`, and `social` locations. Homepage section links need intentional URL handling (`/#work`, etc.) when clicked from inner pages.

### 1.6 Sliders and carousels

- **No conventional slider/carousel exists.**
- The services area is a scroll-driven story, not a slider.
- The marquee is an infinite CSS track, not a content slider.
- Do not add Swiper/Slick or another carousel dependency.

### 1.7 Testimonials

- **No testimonial section or testimonial content exists.**
- Do not create a Testimonial CPT unless the client adds this requirement. If later requested, use a CPT or block pattern rather than hardcoding it.

### 1.8 Fonts

- **Inter Tight** from Google Fonts:
  - normal 400, 500, 600
  - italic 400
- **IBM Plex Mono** from Google Fonts:
  - normal 400, 500
- CSS fallbacks: `system-ui, sans-serif` and `ui-monospace, monospace`.
- No local font binaries are currently present.
- Production choice: enqueue Google Fonts exactly once or self-host licensed WOFF2 subsets under `assets/fonts/`; do not load both.

### 1.9 Scripts and browser APIs

#### Root `assets/script.js` (vanilla JavaScript, no jQuery)

- IIFE in strict mode.
- `matchMedia('(prefers-reduced-motion: reduce)')`.
- Live header/footer clock and GMT offset.
- Canvas 2D hero dot simulation.
- Pointer-driven custom cursor.
- Overlay navigation control.
- Showreel play/pause and viewport pausing.
- Brand loader lifecycle.
- Procedural SVG geometry helpers and renderer.
- Project/service symbol hover, focus, click, keyboard, and reveal states.
- Services scroll-story SVG morph.
- General IntersectionObserver reveal system.
- Uses `requestAnimationFrame`, `IntersectionObserver`, `setTimeout`, video APIs, pointer events, Canvas 2D, SVG DOM APIs, and `getBoundingClientRect()`.
- No external JavaScript libraries.

#### `webflow-template/assets/site.js`

- Simplified menu toggle.
- IntersectionObserver reveal.
- Dynamic copyright year.
- Useful only for the multi-page reference; the canonical script has the richer behavior.

### 1.10 Media and image assets

| Asset | Type/size | Use |
|---|---:|---|
| `assets/icon.svg` | SVG, 303 bytes | Favicon/source icon |
| `assets/brand/motion/loader.mp4` | MP4, ~657 KB | Brand preloader |
| `assets/showreel.mp4` | MP4, ~871 KB | Showreel placeholder |
| `assets/work/halcyon.jpg` | 1920×1080, ~253 KB | Project placeholder |
| `assets/work/meridian.jpg` | 1600×1000, ~199 KB | Project placeholder |
| `assets/work/northlight.jpg` | 1400×950, ~167 KB | Project placeholder |
| `assets/work/onto.jpg` | 1200×2000, ~300 KB | Portrait project placeholder |
| `assets/work/ferro.jpg` | 1920×1080, ~254 KB | Project placeholder |
| `assets/work/vellum.jpg` | 1600×1000, ~199 KB | Project placeholder |

The same project images are duplicated under `webflow-template/assets/work/` and the media/CSS/JS are duplicated in the existing WordPress reference. The new theme should keep one canonical copy of each bundled fallback asset.

---

## 2. Proposed theme file tree

```text
threekg/
├── style.css                         # Required WP theme header; minimal bootstrap styles/imports
├── functions.php                     # Loads modular setup files only
├── header.php                        # Document head, wp_body_open, global header/menu
├── footer.php                        # Global contact/footer, wp_footer
├── front-page.php                    # Canonical homepage composition
├── home.php                          # Optional posts index fallback
├── index.php                         # Required final fallback Loop
├── page.php                          # Generic pages
├── page-careers.php                  # Careers landing page and active Job query
├── single.php                        # Generic post fallback
├── single-project.php                # Dynamic project case study
├── archive-project.php               # Work archive
├── single-job.php                    # Optional public job detail page
├── 404.php                           # Branded not-found page
├── screenshot.png                    # WordPress Appearance preview
├── theme.json                        # Colors, type, spacing, editor alignment/settings
├── inc/
│   ├── setup.php                     # Theme supports, image sizes, menus
│   ├── enqueue.php                   # Versioned CSS/JS/font loading
│   ├── content-types.php             # Project and Job CPT/taxonomies
│   ├── customizer.php                # Global contact/social/availability settings
│   ├── acf.php                       # Local field groups/options integration
│   ├── template-tags.php             # Reusable safe render helpers
│   └── migration.php                 # Optional starter-content/import helpers, admin-only
├── template-parts/
│   ├── global/
│   │   ├── header-bar.php
│   │   ├── overlay-menu.php
│   │   └── contact-footer.php
│   ├── front-page/
│   │   ├── loader.php
│   │   ├── hero.php
│   │   ├── showreel.php
│   │   ├── marquee.php
│   │   ├── featured-work.php
│   │   ├── clients.php
│   │   ├── studio.php
│   │   └── services.php
│   ├── project/
│   │   ├── card.php
│   │   ├── hero.php
│   │   ├── facts.php
│   │   ├── gallery.php
│   │   └── next-project.php
│   ├── job/
│   │   └── row.php
│   └── content/
│       ├── content.php
│       ├── content-page.php
│       └── content-none.php
├── patterns/
│   ├── studio-intro.php              # Editable block-pattern fallback
│   ├── contact-cta.php
│   └── standard-content.php
├── assets/
│   ├── css/
│   │   ├── main.css                  # Port of canonical styles
│   │   ├── pages.css                 # Work/project/careers/generic pages
│   │   ├── editor.css                # Back-end editor visual parity
│   │   └── vendor/                   # Only if a real dependency becomes necessary
│   ├── js/
│   │   ├── main.js                   # Shared menu/cursor/clock/reveal behavior
│   │   ├── hero-canvas.js            # Canvas dot field
│   │   ├── services-morph.js         # Procedural SVG services story
│   │   └── showreel.js               # Video control
│   ├── img/
│   │   ├── icon.svg
│   │   └── work/                     # Bundled fallback/sample images only
│   ├── video/
│   │   ├── loader.mp4
│   │   └── showreel.mp4
│   └── fonts/
│       └── README.md                  # License/source notes; WOFF2 only if self-hosted
└── languages/
    └── threekg.pot
```

Implementation notes:

- `style.css` must contain valid WordPress theme metadata, but the main design CSS should live under `assets/css/`.
- Split the 32 KB monolithic script by responsibility while retaining behavior. Enqueue each script only where needed.
- Use `filemtime()` versions in development and theme version in release builds.
- Use `wp_enqueue_script()`/`wp_enqueue_style()` only; do not hardcode asset tags in templates.
- Use `get_theme_file_uri()` and `get_theme_file_path()` so child themes can override assets.

---

## 3. Client-editable content model

### 3.1 Global navigation and branding

| Content | Editing method | Reason |
|---|---|---|
| Primary menu | WordPress Appearance → Menus / Navigation | Native ordering, labels, internal/external URLs |
| Footer menu | Registered footer menu | Avoid hardcoded legal/page links |
| Social links | Customizer or ACF Options page | Global values used in menu and footer |
| Logo/wordmark | `custom-logo` support with text fallback | Native WordPress behavior |
| Favicon/site icon | WordPress Site Icon | Avoid theme-hardcoded favicon |
| Availability text/status | Customizer or ACF Options | Global header value |
| Contact email | Customizer or ACF Options | Reused in CTA/menu/footer |
| Studio location/timezone label | Customizer or ACF Options | Avoid hardcoded “working worldwide” |

### 3.2 Homepage fields

Use the approved `threekg/*` ACF Blocks for page sections. Each block owns a version-controlled field group in ThreeKG Core; global-only fields live on the ThreeKG Settings options page. The assigned front page may use the ThreeKG Landing Page pattern, while an empty page retains the canonical template fallback.

| Section/content | Editing method |
|---|---|
| Loader video and enable/disable | ACF file field + true/false |
| Hero kicker and three headline lines | ACF text fields |
| Hero supporting labels | ACF text fields |
| Showreel video, year, duration/poster | ACF file/image/text fields |
| Marquee items | ACF repeater |
| Featured Work | Project CPT query filtered by `featured`; manual ordering via menu order/relationship field |
| Client heading/year | ACF text/number |
| Client tiles | ACF repeater on front page, or Client CPT only if clients need standalone reuse/filtering |
| Studio heading and paragraphs | ACF fields or a locked block pattern |
| Studio statistics | ACF repeater |
| Services heading and service items | ACF repeater, hard maximum of six because the SVG forms map by index |
| Contact CTA text | Global options/Customizer |

### 3.3 Projects

Register a public `project` custom post type with archive at `/work/` and support for title, editor, thumbnail, excerpt, revisions, page attributes, and REST API.

Project fields:

- Client name
- Portfolio/display title (optional override)
- Year
- Short summary
- Featured boolean
- Services (taxonomy or relationship)
- Sector (taxonomy)
- Hero image/video
- Flexible gallery rows: full image, two-image pair, portrait, video, rich text
- Credits
- SEO title/description and Open Graph image only if the SEO plugin does not manage these

Use native title/editor/featured image wherever they fit. Use ACF only for structured fields and gallery layout. Import the six CSV starter records only in a separate, explicit migration step; do not auto-create content on every theme activation.

### 3.4 Jobs/Careers

Register `job` CPT with title, editor, excerpt, revisions, and REST API. Fields: employment type, location, active boolean, application email/URL. Careers page queries only active jobs and shows the general application block when none are active.

### 3.5 Services

Use an ACF repeater on the front page rather than a Service CPT unless services later require their own pages. Limit to six rows and keep a stable `symbol_key` select value (`brand`, `design`, `studio`, `campaigns`, `production`, `xperience`) so text edits do not break SVG geometry mapping.

### 3.6 Testimonials

No testimonials exist. Do not build fields or a CPT in the initial theme. If the client supplies testimonials later, add a Testimonial CPT only if entries are reused across pages; otherwise use a page-level repeater/block pattern.

### 3.7 Contact forms

No form exists. Preserve mailto CTAs for exact parity. If the client requests a form, integrate a supported form plugin through a shortcode/block pattern and style it in the theme; keep submission/storage/email logic out of theme templates.

---

## 4. Implementation phases and acceptance checklists

Maximum six phases; each phase must be independently installable/testable.

### Phase 1 — Theme skeleton, assets, and global shell

Create the installable theme directory, metadata, setup/enqueue modules, `theme.json`, global header/menu/footer templates, and exact static asset copies. Do not introduce CMS fields yet.

Acceptance checklist:

- [ ] Theme installs from a ZIP without PHP warnings or fatal errors.
- [ ] Theme activates on the supported WordPress/PHP versions.
- [ ] `wp_head()`, `wp_body_open()`, and `wp_footer()` are present.
- [ ] Primary, footer, and social menus are registered and render safe fallbacks.
- [ ] CSS, JavaScript, images, and videos load with no 404s from a subdirectory install.
- [ ] Google Fonts load once, or approved local fonts load without external duplication.
- [ ] Header, blur overlay menu, and footer match the static design at desktop/mobile widths.
- [ ] Menu is keyboard operable, closes on Escape, updates `aria-expanded`, and returns focus.
- [ ] Generic `index.php`, `page.php`, `single.php`, and `404.php` render without errors.

### Phase 2 — Pixel-matched static homepage

Port all root homepage sections and global animation logic into `front-page.php` and template parts using temporary defaults/fallbacks.

Acceptance checklist:

- [ ] Loader, hero, showreel, marquee, Work, Clients, Studio, Services, and footer appear in the original order.
- [ ] Visual comparison matches spacing, typography, colors, responsive breakpoints, and editorial project layout.
- [ ] Canvas hero resizes, responds to pointer, and pauses offscreen.
- [ ] Showreel supports mouse and keyboard play/pause and pauses offscreen.
- [ ] Custom cursor is desktop-only and does not interfere with native controls.
- [ ] Reveal animations and reduced-motion fallbacks work.
- [ ] Services morph follows all six cards forward and backward without console errors.
- [ ] Homepage remains usable when JavaScript is disabled (content visible; advanced animation may be absent).

### Phase 3 — Global options and editable homepage

Build and package the ThreeKG Core companion plugin, register the block-based editing system and global options, then migrate the homepage to saved-block rendering with canonical fallbacks. Replace static text/media with escaped dynamic values while retaining defaults. Phase 3 must not register the Project or Job content types; those remain Phase 4/5 work.

Acceptance checklist:

- [ ] `/wp-plugins/threekg-core/` and `/build/threekg-core.zip` install and activate without warnings or fatal errors.
- [ ] The plugin declares/checks ACF Pro cleanly, shows an actionable admin notice when absent, and never exposes or bundles a license key.
- [ ] Hero, Showreel, Marquee, Featured Work, Clients, Studio, Services Story, and Contact CTA are registered as `threekg/*` blocks through `block.json`.
- [ ] A ThreeKG Landing Page pattern can create a similar page in the canonical section order without PHP/HTML editing.
- [ ] Editor previews are recognizable, fields are clearly labelled, and section blocks can be reordered or removed without duplicate IDs or broken front-end scripts.
- [ ] Client can edit hero copy, showreel, marquee, clients, studio text/stats, services, contact details, and social links without code.
- [ ] Empty optional fields do not leave broken markup or excessive gaps.
- [ ] Services repeater enforces/makes clear the six-item animation limit.
- [ ] All text, URLs, HTML, and media output is escaped appropriately.
- [ ] Revisions or field history are available for substantial page content.
- [ ] Homepage renders saved ThreeKG blocks when present and canonical defaults when empty; it never renders both paths.
- [ ] Theme works with ACF disabled using documented canonical defaults plus a clear admin dependency notice without breaking the front end.
- [ ] Existing Phase 1 design and animations remain pixel-matched at desktop/tablet/mobile widths after block migration.
- [ ] PHP lint, block metadata validation, JavaScript syntax checks, editor smoke tests, front-end console checks, and install tests pass for both theme and companion plugin.

### Phase 4 — Projects CMS, Work archive, and project pages

Register Project and taxonomies, add structured fields, build dynamic homepage cards, Work archive, Project detail, gallery, and adjacent-project navigation.

Acceptance checklist:

- [ ] Projects appear in WordPress admin and use `/work/{slug}/` URLs.
- [ ] Work archive matches the editorial six-card pattern and scales for more/fewer projects.
- [ ] Homepage shows only Featured projects in explicit client-controlled order.
- [ ] Every Project uses the shared detail template.
- [ ] Gallery supports landscape, portrait, paired images, video, and text without template edits.
- [ ] Previous/next project navigation skips drafts/private posts and handles the first/last item intentionally.
- [ ] Adding a Project requires no PHP/HTML editing.
- [ ] Permalinks, image alt text, responsive images, and empty states work.

### Phase 5 — Careers, remaining templates, and content migration

Register Job content, build Careers behavior, finish generic templates, and perform a one-time import of approved starter content/media.

Acceptance checklist:

- [ ] Careers lists only active/published jobs.
- [ ] Job title, type, location, description, and application destination are editable.
- [ ] No-active-jobs state shows the general application CTA.
- [ ] Generic Page, Single, Index, 404, and privacy/legal content are visually coherent.
- [ ] Approved project/job CSV data imports once without duplicate records.
- [ ] Placeholder names, claims, awards, jobs, emails, and social `#` links are removed or explicitly approved.
- [ ] Theme activation does not recreate deleted sample content.

### Phase 6 — Hardening, regression, and release package

Perform accessibility, performance, compatibility, security, SEO, and packaging review.

Acceptance checklist:

- [ ] No PHP notices/warnings with `WP_DEBUG` enabled.
- [ ] No browser console errors on any template.
- [ ] WordPress coding/escaping/sanitization checks pass.
- [ ] Keyboard-only and screen-reader smoke tests pass, including menu focus trap/return.
- [ ] WCAG AA contrast and visible focus states are verified.
- [ ] Reduced-motion mode removes nonessential movement.
- [ ] Pages are checked at 320, 375, 768, 1024, 1440, and wide desktop widths.
- [ ] Images use WordPress responsive markup and below-fold lazy loading.
- [ ] Video loading and animation CPU usage are acceptable on mobile.
- [ ] SEO plugin compatibility, document titles, canonicals, Open Graph fallback, sitemap, and 404 status are verified.
- [ ] Theme works with the current WordPress release and supported PHP versions.
- [ ] Final installable ZIP contains no build archives, duplicate source trees, OS files, or development-only fixtures.
- [ ] Fresh-site install test and upgrade-over-previous-version test both pass.

---

## 5. WordPress conversion risks and mitigations

### 5.1 Hardcoded asset paths

**Risk:** Static references such as `assets/work/halcyon.jpg`, CSS-relative URLs, and JS error text assume the site lives at the domain root. They break when WordPress is installed in a subdirectory, when templates have nested URLs, or under a child theme.

**Mitigation:** Use `get_theme_file_uri()`, WordPress media attachment URLs, localized script configuration where JS needs asset URLs, and CSS assets located relative to their final stylesheet. Never construct theme paths from `/wp-content/themes/...` manually.

### 5.2 Hardcoded anchors and placeholder links

**Risk:** `#work` works on the homepage but not from inner pages; many social/project links are `#` and project cards do not lead anywhere.

**Mitigation:** Use registered WordPress menus and Project permalinks. For section links, generate the front-page URL plus fragment when not already on the front page. Remove empty social links from output.

### 5.3 Duplicate IDs and scripts on builder/editor output

**Risk:** IDs such as `menu`, `menuToggle`, `field`, `showreelVideo`, and `services__morph-svg` assume one instance. Repeating sections through a builder or block pattern would produce invalid duplicate IDs and scripts would attach only to the first instance.

**Mitigation:** Keep global/animated sections as single template instances, or refactor scripts to use scoped `data-*` roots and unique IDs. Document sections that cannot be duplicated.

### 5.4 Monolithic script and global selectors

**Risk:** Root `assets/script.js` queries the whole document and runs every feature on every page. It also runs perpetual animation frames for the custom cursor and has complex geometry code in one bundle.

**Mitigation:** Split and conditionally enqueue scripts. Retain null guards. Stop animation loops when hidden/offscreen and on `visibilitychange`. Test inner pages that lack homepage elements.

### 5.5 Procedural services morph depends on order/count

**Risk:** Six SVG forms and six hardcoded service keys map by array index. Deleting, adding, or reordering services without maintaining keys can display the wrong form or produce undefined data.

**Mitigation:** Limit the field to six services, store stable symbol keys, validate values, and provide a static fallback. Explain the constraint in field instructions.

### 5.6 Browser feature support

**Risk:** `backdrop-filter`, `mix-blend-mode`, Canvas, `IntersectionObserver`, pointer events, `aspect-ratio`, and modern CSS functions may degrade in older browsers or in WordPress admin previews.

**Mitigation:** Define solid-color/fixed-layout fallbacks, use feature queries where helpful, and ensure all information remains present without effects. Do not polyfill animation-only features unless analytics prove necessary.

### 5.7 Loader behavior and caching

**Risk:** The loader runs on every full page load and can delay content; malformed/slow video may show a blank overlay until the safety timeout. WordPress caching/optimization plugins may alter autoplay or script order.

**Mitigation:** Restrict loader to the front page, retain the error/timeout path, consider a session-level “shown” flag, never block content for reduced motion, and test common optimization settings.

### 5.8 Video autoplay and media paths

**Risk:** Browsers may block autoplay; WordPress attachment URLs and MIME handling differ from bundled files; optimization/CDN plugins may rewrite sources.

**Mitigation:** Keep loader muted/playsinline, give showreel an explicit poster and manual control, use WordPress attachment IDs/URLs, and handle video errors without exposing internal paths to users.

### 5.9 Fonts and privacy

**Risk:** Google Fonts is an external request and may conflict with consent/privacy requirements or optimization plugins. Loading it in both theme and a builder causes duplicates.

**Mitigation:** Make one font-loading strategy authoritative. Prefer properly licensed local WOFF2 files when required, with preload limited to essential subsets.

### 5.10 jQuery/plugin conflicts

**Risk:** The current scripts do not use jQuery, so `$` conflicts are absent. However, plugins/builders can inject styles, alter DOM structure, defer scripts, or add another smooth-scroll/menu system.

**Mitigation:** Keep vanilla JS namespaced inside modules/IIFEs, avoid globals, use distinctive `threekg-` handles and classes where new names are introduced, declare true dependencies, and test with the selected SEO/cache/form/ACF plugins. Do not dequeue WordPress core jQuery globally.

### 5.11 Inline scripts and CSP

**Risk:** The canonical HTML has no inline JavaScript, but CSS includes an inline SVG data URI. Future ACF/Customizer values must not be interpolated directly into executable scripts. Strict Content Security Policy may restrict data URIs or Google Fonts.

**Mitigation:** Keep scripts in enqueued files; pass data with `wp_add_inline_script()` using `wp_json_encode()` only when necessary, or with data attributes. Document required CSP sources or replace the grain data URI with a local asset.

### 5.12 Form handling

**Risk:** There are no forms today; mailto links depend on a configured email client. Adding raw form markup would not send or store submissions in WordPress.

**Mitigation:** Preserve mailto for parity. If forms enter scope, use a vetted plugin/integration, style its semantic output, and verify nonce/spam/delivery/privacy behavior.

### 5.13 CPT ownership and theme switching

**Risk:** Registering Projects/Jobs inside the theme makes their admin UI disappear if the theme is changed, although database content remains.

**Mitigation:** For long-term portability, put CPT/taxonomy registration in a small required companion plugin. If the delivery requirement is strictly one installable theme, register them in `inc/content-types.php` and document the tradeoff plus migration path.

### 5.14 ACF dependency

**Risk:** ACF field calls can fatal or produce blank templates when the plugin is missing; ACF Pro may be required for repeaters, gallery, and flexible content.

**Mitigation:** Guard all ACF calls with `function_exists()`, provide core-content fallbacks, ship local JSON field definitions rather than plugin code, and state the exact plugin/license requirement before build approval.

### 5.15 Existing WordPress code is incomplete

**Risk:** Extending `wordpress/threekg-revamp` as if production-ready would preserve missing templates/global markup and hidden dependencies. The Elementor plugin queries a `project` CPT not registered in the inspected theme files.

**Mitigation:** Start from the proposed clean tree, selectively reuse proven escaped template logic, and add explicit migration tests. Do not install both the old Elementor widget plugin and the new native theme unless compatibility is intentionally supported.

### 5.16 Placeholder and legal content

**Risk:** Project names, client list, awards, statistics, jobs, emails, showreel, and art are labeled or evidently used as placeholders. Publishing them can create factual, legal, or recruiting issues.

**Mitigation:** Maintain a content sign-off list. Seed sample content only on explicit approval, label drafts, and block production launch until placeholders are replaced or accepted.

### 5.17 Accessibility gaps in current code

**Risk:** Current overlay menu lacks a focus trap/focus return and does not update `aria-hidden`; the custom cursor runs continuously; some icon buttons rely on animation; work archive reference images have empty alt text; a clickable showreel frame contains a nested button.

**Mitigation:** Correct semantics during porting without changing appearance: one real control per action, focus management, synchronized ARIA state, meaningful CMS alt text, visible focus, pause controls where required, and reduced-motion/static alternatives.

### 5.18 CSS collisions with WordPress output

**Risk:** Broad selectors such as `a`, `ul`, `button`, `body::after`, and generic `.entry-content` styles can affect admin bars, plugin forms, embedded blocks, and generated navigation.

**Mitigation:** Preserve required global reset carefully, scope component rules under theme classes, explicitly accommodate `#wpadminbar`, test logged-in layout, and style core blocks/form plugin markup deliberately.

---

## Planning decisions required before implementation

- **Resolved:** root homepage interactions/content order are authoritative, plus the multi-page Work/Careers architecture and blur-menu direction where it does not conflict.
- **Resolved:** native classic/hybrid theme plus the WordPress Block Editor; Elementor and other general-purpose builders are excluded.
- **Resolved architecture:** ACF Pro is the required licensed editing dependency, supplied by the client; the build must retain guarded canonical fallbacks when it is inactive. Installation/license availability still needs operational confirmation on staging.
- Confirm whether Projects/Jobs must live in a companion plugin. Default recommendation: companion plugin for portability, unless one-ZIP theme delivery is mandatory.
- Approve or replace all placeholder projects, clients, statistics, jobs, emails, social URLs, videos, and imagery before migration.
- Confirm external Google Fonts vs approved self-hosting.

No theme implementation should begin until these decisions and the Phase 1 acceptance criteria are acknowledged.
