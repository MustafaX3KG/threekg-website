# Font self-hosting (optional, deferred)

The theme currently loads **Inter Tight** (400, 500, 600, italic 400) and
**IBM Plex Mono** (400, 500) from Google Fonts, enqueued exactly once from
`inc/enqueue.php` (`threekg-fonts`) with `preconnect` resource hints.

If the production domain must not call Google at runtime (GDPR/self-hosted
requirement), drop the `woff2` files in this folder and create
`assets/fonts/fonts.css` in this shape:

```css
@font-face {
  font-family: "Inter Tight";
  src: url("inter-tight-400.woff2") format("woff2");
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
/* …repeat per weight/style, then IBM Plex Mono 400/500… */
```

Then in `inc/enqueue.php` replace the Google URL with:

```php
wp_enqueue_style(
    'threekg-fonts',
    get_theme_file_uri( 'assets/fonts/fonts.css' ),
    array(),
    threekg_asset_version( 'assets/fonts/fonts.css' )
);
```

No template edits are needed — the enqueue is the single font entry point.
