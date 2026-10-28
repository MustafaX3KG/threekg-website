<!-- ================= SERVICES ================= -->
<section class="services" id="services">
  <div class="services__inner">
    <div class="services__stage">
      <div class="section-head reveal">
        <span class="mono">[06 &mdash; <?php esc_html_e( 'what we do', 'threekg' ); ?>]</span>
        <span class="mono"><?php esc_html_e( 'capabilities', 'threekg' ); ?></span>
        <h2><?php esc_html_e( 'Six ways in.', 'threekg' ); ?></h2>
      </div>
      <div class="services__motion reveal" aria-live="polite">
        <div class="services__morph" aria-hidden="true">
          <svg class="services__morph-svg" viewBox="-120 -120 240 240" role="presentation"></svg>
        </div>
        <div class="services__motion-meta mono">
          <span class="services__motion-index">00 / 06</span>
          <span class="services__motion-label"><?php esc_html_e( 'scroll to explore', 'threekg' ); ?></span>
        </div>
      </div>
    </div>
    <ul class="services__list">
<?php
$threekg_services = array(
	'01' => array( 'brand',      'Brand identity',        'naming · systems · guidelines' ),
	'02' => array( 'design',     'Web design',            'strategy · UX · art direction' ),
	'03' => array( 'studio',     'Creative development',  'canvas · motion · interaction' ),
	'04' => array( 'campaigns',  'Art direction',         'campaigns · editorial · image' ),
	'05' => array( 'production', 'Production',            'film · shoots · content' ),
	'06' => array( 'xperience',  'Motion & interaction',  'micro-interactions · video' ),
);

foreach ( $threekg_services as $threekg_number => $threekg_service ) {
	printf(
		"\t\t<li class=\"svc-card services__card reveal\" data-service=\"%s\">\n\t\t\t<span class=\"mono services__number\">%s</span>\n\t\t\t<h3 class=\"svc-card__name\">%s</h3>\n\t\t\t<p class=\"mono svc-card__hint\">%s</p>\n\t\t</li>\n",
		esc_attr( $threekg_service[0] ),
		esc_html( $threekg_number ),
		esc_html( $threekg_service[1] ),
		esc_html( $threekg_service[2] )
	);
}
?>
    </ul>
  </div>
</section>
