<?php
$heading = threekg_core_field( 'heading', 'Featured work.' );
$eyebrow = threekg_core_field( 'eyebrow', '[03 — featured]' );
$years   = threekg_core_field( 'year_range', '2019 → ' . gmdate( 'Y' ) );
$cards   = threekg_core_field( 'cards', array() );
if ( ! is_array( $cards ) || ! $cards ) {
	$names = array( array( 'Halcyon Hotels', 'brand identity', 'halcyon.jpg', 'full' ), array( 'Meridian Motion', 'website', 'meridian.jpg', 'right' ), array( 'North Light Archive', 'art direction', 'northlight.jpg', 'left' ), array( 'Onto & Co.', 'identity system', 'onto.jpg', 'side' ), array( 'Studio Ferro', 'e-commerce', 'ferro.jpg', 'full' ), array( 'Vellum Editions', 'website', 'vellum.jpg', 'right' ) );
	$cards = array_map( static function ( $row ) { return array( 'title' => $row[0], 'tag' => $row[1], 'fallback' => $row[2], 'layout' => $row[3] ); }, $names );
}
?>
<section class="work" data-threekg-work><div class="section-head reveal"><span class="mono"><?php echo esc_html( $eyebrow ); ?></span><span class="mono"><?php echo esc_html( $years ); ?></span><div class="work__titlerow"><h2><?php echo nl2br( esc_html( $heading ) ); ?></h2><button type="button" class="symbol-btn work__symbol" data-category="selected-work" aria-label="<?php esc_attr_e( 'Selected work', 'threekg-core' ); ?>" aria-pressed="false"><svg viewBox="-145 -145 290 290" aria-hidden="true"></svg></button></div></div>
<div class="work__editorial"><?php foreach ( $cards as $card ) :
	$title = trim( (string) ( $card['title'] ?? '' ) ); if ( '' === $title ) { continue; }
	$layout = in_array( $card['layout'] ?? '', array( 'full', 'right', 'left', 'side' ), true ) ? $card['layout'] : 'full';
	$fallback = ! empty( $card['fallback'] ) ? threekg_core_theme_asset( 'assets/work/' . basename( $card['fallback'] ) ) : '';
	$image = threekg_core_image( $card['image'] ?? 0, '', $fallback, $card['alt'] ?? $title );
	$url = ! empty( $card['url'] ) ? $card['url'] : '#'; ?>
<a class="work-block work-block--<?php echo esc_attr( $layout ); ?> reveal" href="<?php echo esc_url( $url ); ?>"><span class="work-block__media"><?php echo wp_kses_post( $image ); ?></span><span class="work-block__caption"><span class="work-block__title"><?php echo esc_html( $title ); ?></span><?php if ( ! empty( $card['tag'] ) ) : ?><span class="work-block__tag mono"><?php echo esc_html( $card['tag'] ); ?></span><?php endif; ?></span></a>
<?php endforeach; ?></div><div class="work__foot reveal"><span class="mono">(<?php echo esc_html( str_pad( (string) count( $cards ), 2, '0', STR_PAD_LEFT ) ); ?>) <?php esc_html_e( 'featured projects', 'threekg-core' ); ?></span></div></section>

