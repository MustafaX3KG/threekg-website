<?php
$id       = threekg_core_instance_id( $block, 'hero' );
$kicker   = threekg_core_field( 'kicker', 'ThreeKG — independent design studio, est. 2021' );
$line1    = threekg_core_field( 'line_1', 'We build quiet,' );
$line2    = threekg_core_field( 'line_2', 'confident work' );
$line3    = threekg_core_field( 'line_3', 'for brands that whisper' );
$accent   = threekg_core_field( 'accent', 'loudly.' );
$scroll   = threekg_core_field( 'scroll_label', 'scroll to explore ↓' );
$location = threekg_core_field( 'location_label', threekg_core_option( 'location', 'working worldwide' ) );
$bg       = threekg_core_media_url( threekg_core_field( 'background_image', 0 ) );
?>
<section class="hero" data-threekg-hero="<?php echo esc_attr( $id ); ?>"<?php echo $bg ? ' style="background-image:url(' . esc_url( $bg ) . ')"' : ''; ?>>
  <canvas class="hero__canvas" data-hero-canvas aria-hidden="true"></canvas>
  <div class="hero__inner">
    <p class="mono hero__kicker reveal">(<?php echo esc_html( $kicker ); ?>)</p>
    <h1 class="hero__title"><span class="reveal"><?php echo esc_html( $line1 ); ?></span><span class="reveal"><?php echo esc_html( $line2 ); ?></span><span class="reveal hero__title--soft"><?php echo esc_html( $line3 ); ?><em>&nbsp;<?php echo esc_html( $accent ); ?></em></span></h1>
    <div class="hero__foot"><span class="mono"><?php echo esc_html( $scroll ); ?></span><span class="mono hide-sm"><?php echo esc_html( $location ); ?></span><span class="mono hide-sm">[01/07]</span></div>
  </div>
</section>

