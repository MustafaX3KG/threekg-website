<!-- ================= SHOWREEL ================= -->
<section class="showreel" id="showreel" data-threekg-showreel="canonical">
  <div class="showreel__frame reveal">
    <video
      class="showreel__video"
      data-showreel-video
      src="<?php echo esc_url( get_theme_file_uri( 'assets/showreel.mp4' ) ); ?>"
      muted
      loop
      playsinline
      preload="metadata"
    ></video>
    <div class="showreel__scrim"></div>
    <div class="showreel__overlay">
      <span class="mono showreel__cap">[02 &mdash; <?php esc_html_e( 'showreel', 'threekg' ); ?>] <?php echo esc_html( gmdate( 'Y' ) ); ?></span>
      <button type="button" class="showreel__play" data-showreel-play aria-label="Play showreel">
        <span class="showreel__play-icon">&#9654;</span>
        <span class="mono"><?php esc_html_e( 'play reel', 'threekg' ); ?></span>
      </button>
      <span class="mono showreel__dur hide-sm">0:10 &mdash; <?php esc_html_e( 'reel placeholder', 'threekg' ); ?></span>
    </div>
  </div>
</section>
