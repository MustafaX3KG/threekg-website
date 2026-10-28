<?php
/** @var array $values — assigned by the renderer (Elementor) or omitted (ACF blocks). */
$values   = isset( $values ) && is_array( $values ) ? $values : array();
$id      = threekg_core_instance_id( $block, 'showreel' );
$video   = threekg_core_media_url( threekg_core_value( $values, 'video', threekg_core_field( 'video', 0 ) ), threekg_core_theme_asset( 'assets/showreel.mp4' ) );
$poster  = threekg_core_media_url( threekg_core_value( $values, 'poster', threekg_core_field( 'poster', 0 ) ) );
$year    = threekg_core_value( $values, 'year', threekg_core_field( 'year', gmdate( 'Y' ) ) );
$duration= threekg_core_value( $values, 'duration', threekg_core_field( 'duration', '0:10' ) );
$caption = threekg_core_value( $values, 'caption', threekg_core_field( 'caption', 'reel placeholder' ) );
$play    = threekg_core_value( $values, 'play_label', threekg_core_field( 'play_label', 'play reel' ) );
?>
<section class="showreel" data-threekg-showreel="<?php echo esc_attr( $id ); ?>">
  <div class="showreel__frame reveal">
    <?php if ( $video ) : ?><video class="showreel__video" data-showreel-video src="<?php echo esc_url( $video ); ?>"<?php echo $poster ? ' poster="' . esc_url( $poster ) . '"' : ''; ?> muted loop playsinline preload="metadata"></video><?php endif; ?>
    <div class="showreel__scrim"></div><div class="showreel__overlay">
      <span class="mono showreel__cap">[02 — showreel] <?php echo esc_html( $year ); ?></span>
      <button type="button" class="showreel__play" data-showreel-play aria-label="<?php echo esc_attr( $play ); ?>"><span class="showreel__play-icon">&#9654;</span><span class="mono"><?php echo esc_html( $play ); ?></span></button>
      <span class="mono showreel__dur hide-sm"><?php echo esc_html( $duration ); ?> — <?php echo esc_html( $caption ); ?></span>
    </div>
  </div>
</section>

