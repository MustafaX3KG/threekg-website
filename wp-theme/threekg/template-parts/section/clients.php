<!-- ================= CLIENTS ================= -->
<section class="clients" id="clients">
  <div class="section-head reveal">
    <span class="mono">[04 &mdash; clients]</span>
    <span class="mono"><?php echo esc_html( (string) ( gmdate( 'Y' ) - 1 ) ); ?></span>
    <h2>Clients we helped<br /><em>in <?php echo esc_html( (string) ( gmdate( 'Y' ) - 1 ) ); ?>.</em></h2>
  </div>
  <ul class="clients__grid">
<?php
// Placeholder tiles - a client-list Customizer/ACF field replaces these
// in Phase 3; the tile markup below is the canonical parity target.
$threekg_tiles = array(
	array( 'Halcyon', 'hospitality' ),
	array( 'Meridian', 'creative studio' ),
	array( 'Ferro', 'retail' ),
	array( 'North Light', 'culture' ),
	array( 'Onto & Co.', 'editorial' ),
	array( 'Vellum', 'publishing' ),
	array( 'Aster & Pine', 'hospitality' ),
	array( 'Kernel Water', 'consumer' ),
	array( 'Dune Society', 'apparel' ),
	array( 'Arca', 'architecture' ),
	array( 'Fold Paper', 'stationery' ),
	array( 'Studio Mantle', 'software' ),
	array( 'Beacon Foods', 'food & drink' ),
	array( 'Marlowe Press', 'independent press' ),
	array( 'Summit Labs', 'technology' ),
);

foreach ( $threekg_tiles as $threekg_tile ) {
	printf(
		"<li class=\"clients__tile reveal\"><span class=\"clients__name\">%s</span><span class=\"mono clients__tag\">%s</span></li>\n",
		esc_html( $threekg_tile[0] ),
		esc_html( $threekg_tile[1] )
	);
}
?>
  </ul>
  <div class="work__foot reveal">
    <span class="mono">(<?php echo esc_html( str_pad( (string) count( $threekg_tiles ), 2, '0', STR_PAD_LEFT ) ); ?>) <?php esc_html_e( 'placeholder tiles', 'threekg' ); ?></span>
    <a class="mono" href="<?php echo esc_url( threekg_email_url() ); ?>"><?php esc_html_e( 'become a client &nearr;', 'threekg' ); ?></a>
  </div>
</section>
