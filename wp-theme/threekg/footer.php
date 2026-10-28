</main>

<!-- ================= FOOTER / CONTACT ================= -->
<footer class="footer" id="contact">
  <?php if ( ! threekg_has_saved_block( 'contact-cta' ) ) : ?>
    <span class="mono footer__cap reveal">(<?php esc_html_e( 'get in touch', 'threekg' ); ?>)</span>
    <a class="footer__cta reveal" href="<?php echo esc_url( threekg_email_url() ); ?>"><?php echo esc_html( threekg_cta_text() ); ?><span class="footer__arrow">&rarr;</span></a>
  <?php endif; ?>

  <div class="footer__grid">
    <div class="mono footer__col">
      <span class="footer__label"><?php esc_html_e( 'studio', 'threekg' ); ?></span>
      <span>ThreeKG&reg;</span>
      <span><?php echo esc_html( threekg_location() ); ?></span>
      <span id="clockFooter">--:--:--</span>
    </div>
    <div class="mono footer__col">
      <span class="footer__label"><?php esc_html_e( 'contact', 'threekg' ); ?></span>
      <a href="<?php echo esc_url( threekg_email_url() ); ?>"><?php echo esc_html( threekg_email() ); ?></a>
      <?php
      // socials rendered through the registered `social` menu location;
      // nothing is output when every social URL is empty.
      threekg_the_menu( 'social' );
      ?>
    </div>
    <div class="mono footer__col">
      <span class="footer__label"><?php esc_html_e( 'site', 'threekg' ); ?></span>
      <?php
      // `footer` menu location; safe fallback keeps the core links.
      threekg_the_menu( 'footer' );
      ?>
      <span>&copy; 2021&ndash;<?php echo esc_html( gmdate( 'Y' ) ); ?> ThreeKG</span>
      <span><?php esc_html_e( 'made with quiet intent', 'threekg' ); ?></span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
