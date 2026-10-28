<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- custom cursor -->
<div class="cursor" aria-hidden="true"></div>

<?php if ( is_front_page() ) : ?>
<!-- ================= BRAND PRELOAD ================= -->
<div class="loader" id="loader" aria-hidden="true">
  <video class="loader__video" id="loaderVideo" src="<?php echo esc_url( get_theme_file_uri( 'assets/brand/motion/loader.mp4' ) ); ?>" autoplay muted playsinline></video>
  <span class="mono loader__skip"><?php esc_html_e( 'click to skip', 'threekg' ); ?></span>
</div>
<?php endif; ?>

<noscript>
  <style>
    /* no JS or blocked JS: any forced loading state must never trap the page */
    body.is-loading { overflow: auto !important; }
    .loader { display: none !important; }
    .cursor { display: none !important; }
    .reveal { opacity: 1 !important; transform: none !important; }
  </style>
</noscript>

<?php get_template_part( 'template-parts/global/overlay-menu' ); ?>

<header class="site-header">
  <?php threekg_the_brand(); ?>
  <div class="mono site-header__meta">
    <span class="avail"><i class="avail__dot"></i><?php echo esc_html( threekg_availability() ); ?></span>
    <span class="hide-sm"><?php echo esc_html( threekg_descriptor() ); ?></span>
    <span id="clock">--:--:--</span>
    <span id="tz"></span>
  </div>
  <button class="menu-toggle mono" id="menuToggle" aria-controls="menu" aria-expanded="false"><?php esc_html_e( 'menu', 'threekg' ); ?></button>
</header>

<main id="top">
