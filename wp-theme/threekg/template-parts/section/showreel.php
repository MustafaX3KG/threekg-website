<!-- ================= SHOWREEL ================= -->
<section class="showreel" id="showreel">
  <div class="showreel__frame reveal" tabindex="0" role="button" aria-label="Showreel &mdash; press Enter to play or pause">
    <video
      class="showreel__video"
      id="showreelVideo"
      src="<?php echo esc_url( get_theme_file_uri( 'assets/showreel.mp4' ) ); ?>"
      muted
      loop
      playsinline
      preload="metadata"
    ></video>
    <div class="showreel__scrim"></div>
    <div class="showreel__overlay">
      <span class="mono showreel__cap">[02 &mdash; <?php esc_html_e( 'showreel', 'threekg' ); ?>] <?php echo esc_html( gmdate( 'Y' ) ); ?></span>
      <button class="showreel__play" id="showreelPlay" aria-label="Play showreel">
        <span class="showreel__play-icon">&#9654;</span>
        <span class="mono"><?php esc_html_e( 'play reel', 'threekg' ); ?></span>
      </button>
      <span class="mono showreel__dur hide-sm">0:10 &mdash; <?php esc_html_e( 'reel placeholder', 'threekg' ); ?></span>
    </div>
  </div>
</section>
