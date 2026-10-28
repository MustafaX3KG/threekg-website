<?php
/** @var array $values — assigned by the renderer (Elementor) or omitted (ACF blocks). */
$values  = isset( $values ) && is_array( $values ) ? $values : array();
$heading = threekg_core_value( $values, 'heading', threekg_core_field( 'heading', 'Clients we helped' ) );
$year = threekg_core_value( $values, 'year', threekg_core_field( 'year', (string) ( (int) gmdate( 'Y' ) - 1 ) ) );
$clients = threekg_core_value( $values, 'clients', threekg_core_field( 'clients', array() ) );
if ( ! is_array( $clients ) || ! $clients ) {
	$clients = array_map( static function ( $name ) { return array( 'name' => $name, 'sector' => 'placeholder' ); }, array( 'Halcyon', 'Meridian', 'Ferro', 'North Light', 'Onto & Co.', 'Vellum' ) );
}
?>
<section class="clients" data-threekg-clients><div class="section-head reveal"><span class="mono">[04 — clients]</span><span class="mono"><?php echo esc_html( $year ); ?></span><h2><?php echo esc_html( $heading ); ?><br><em><?php echo esc_html( 'in ' . $year . '.' ); ?></em></h2></div><ul class="clients__grid">
<?php foreach ( $clients as $client ) : $name = trim( (string) ( $client['name'] ?? '' ) ); if ( '' === $name ) { continue; } ?><li class="clients__tile reveal"><?php $logo = threekg_core_image( $client['logo'] ?? 0, 'clients__logo', '', $client['logo_alt'] ?? $name ); echo wp_kses_post( $logo ); ?><span class="clients__name"><?php echo esc_html( $name ); ?></span><?php if ( ! empty( $client['sector'] ) ) : ?><span class="mono clients__tag"><?php echo esc_html( $client['sector'] ); ?></span><?php endif; ?></li><?php endforeach; ?>
</ul></section>

