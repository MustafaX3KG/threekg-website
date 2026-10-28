<!-- blur overlay menu (canonical markup, primary + social locations) -->
<div class="menu" id="menu" aria-hidden="true">
  <div class="menu__inner">
    <span class="mono menu__cap">(<?php esc_html_e( 'navigation', 'threekg' ); ?>)</span>
    <nav class="menu__nav">
      <?php threekg_the_menu( 'primary' ); ?>
    </nav>
    <div class="menu__foot">
      <a href="<?php echo esc_url( threekg_email_url() ); ?>"><?php echo esc_html( threekg_email() ); ?></a>
      <div class="menu__socials">
        <?php threekg_the_menu( 'social' ); ?>
      </div>
    </div>
  </div>
</div>
