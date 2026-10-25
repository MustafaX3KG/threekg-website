<img src="assets/icon.svg" width="64" height="64" alt="">

# ThreeKG® — Studio Website

Minimal, canvas-driven studio portfolio. Deployed with **GitHub Pages**:

**Live:** https://mustafax3kg.github.io/threekg-website/

## Structure
- `index.html` — all content (work index, studio, services, contact)
- `assets/style.css` — design tokens (`:root` block has colors/fonts)
- `assets/script.js` — canvas dot-field, live clock, cursor, hover previews, menu
- `assets/icon.svg` — favicon

## Editing
- Replace the 6 placeholder projects in the `<ul class="work__list">` in `index.html`
- Swap the gradient preview classes (`wp-1`…`wp-6` in `style.css`) for real screenshots
- Update email, socials, and stats to match your studio

Every `git push` to `main` redeploys automatically (Pages is set to deploy from the `main` branch, root folder).

Created in [T3 Code](https://t3.codes).
