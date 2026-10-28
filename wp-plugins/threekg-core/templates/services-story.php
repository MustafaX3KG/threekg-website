<?php
/** @var array $values — assigned by the renderer (Elementor) or omitted (ACF blocks). */
$values   = isset( $values ) && is_array( $values ) ? $values : array();
$id = threekg_core_instance_id( $block, 'services' );
$heading = threekg_core_value( $values, 'heading', threekg_core_field( 'heading', 'Six ways in.' ) );
$services = threekg_core_value( $values, 'services', threekg_core_field( 'services', array() ) );
if ( ! is_array( $services ) || 6 !== count( $services ) ) {
	$rows = array( array( 'Brand identity', 'naming · systems · guidelines', 'brand' ), array( 'Web design', 'strategy · UX · art direction', 'design' ), array( 'Creative development', 'canvas · motion · interaction', 'studio' ), array( 'Art direction', 'campaigns · editorial · image', 'campaigns' ), array( 'Production', 'film · shoots · content', 'production' ), array( 'Motion & interaction', 'micro-interactions · video', 'xperience' ) );
	$services = array_map( static function ( $row ) { return array( 'name' => $row[0], 'hint' => $row[1], 'symbol_key' => $row[2] ); }, $rows );
}
?>
<section class="services" data-threekg-services="<?php echo esc_attr( $id ); ?>"><div class="services__inner"><div class="services__stage"><div class="section-head reveal"><span class="mono">[06 — what we do]</span><span class="mono">capabilities</span><h2><?php echo esc_html( $heading ); ?></h2></div><div class="services__motion reveal" aria-live="polite"><div class="services__morph" aria-hidden="true"><svg class="services__morph-svg" data-services-svg viewBox="-120 -120 240 240" role="presentation"></svg></div><div class="services__motion-meta mono"><span class="services__motion-index" data-services-index>00 / 06</span><span class="services__motion-label" data-services-label>scroll to explore</span></div></div></div><ul class="services__list">
<?php foreach ( $services as $index => $service ) : $key = (string) ( $service['symbol_key'] ?? '' ); if ( ! in_array( $key, array( 'brand', 'design', 'studio', 'campaigns', 'production', 'xperience' ), true ) ) { continue; } ?><li class="svc-card services__card reveal" data-service="<?php echo esc_attr( $key ); ?>"><span class="mono services__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="svc-card__name"><?php echo esc_html( $service['name'] ?? '' ); ?></h3><p class="mono svc-card__hint"><?php echo esc_html( $service['hint'] ?? '' ); ?></p></li><?php endforeach; ?>
</ul></div></section>

