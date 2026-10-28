<!-- ================= WORK ================= -->
<section class="work" id="work">
  <div class="section-head reveal">
    <span class="mono">[03 &mdash; featured]</span>
    <span class="mono">2019 &rarr; <?php echo esc_html( gmdate( 'Y' ) ); ?></span>
    <div class="work__titlerow">
      <h2>Featured<br /><em>work.</em></h2>
      <button type="button" class="symbol-btn work__symbol" data-category="selected-work" aria-label="Selected work" aria-pressed="false">
        <svg viewBox="-145 -145 290 290" aria-hidden="true"></svg>
      </button>
    </div>
  </div>

  <div class="work__editorial">
<?php
// Placeholder project grid - Phase 4 (Projects CMS) replaces these with
// real `project` posts queried in the exact same block order.
$threekg_blocks = array(
	array( 'style' => 'work-block--full',  'title' => 'Halcyon Hotels',      'tag' => 'brand identity', 'img' => 'halcyon.jpg' ),
	array( 'style' => 'work-block--right', 'title' => 'Meridian Motion',     'tag' => 'website',        'img' => 'meridian.jpg' ),
	array( 'style' => 'work-block--left',  'title' => 'North Light Archive', 'tag' => 'art direction',  'img' => 'northlight.jpg' ),
	array( 'style' => 'work-block--side',  'title' => 'Onto &amp; Co.',      'tag' => 'identity system','img' => 'onto.jpg' ),
	array( 'style' => 'work-block--full',  'title' => 'Studio Ferro',        'tag' => 'e-commerce',     'img' => 'ferro.jpg' ),
	array( 'style' => 'work-block--right', 'title' => 'Vellum Editions',     'tag' => 'website',        'img' => 'vellum.jpg' ),
);

foreach ( $threekg_blocks as $index => $threekg_block ) {
	$threekg_open_pair = ( 2 === $index ) ? "<div class=\"work-pair\">\n" : '';
	$threekg_close_pair = ( 4 === $index ) ? "</div>\n" : '';
	echo $threekg_open_pair; // variable markup, fully controlled above
	echo "\t<a class=\"work-block {$threekg_block['style']} reveal\" href=\"#\">\n";
	printf( "\t\t<span class=\"work-block__media\">\n\t\t\t<img src=\"%s\" alt=\"%s\" />\n\t\t</span>\n",
		esc_url( get_theme_file_uri( "assets/work/{$threekg_block['img']}" ) ),
		esc_attr( $threekg_block['title'] . ' - ' . wp_strip_all_tags( $threekg_block['tag'] . ', placeholder artwork' ) )
	);
	printf( "\t\t<span class=\"work-block__caption\">\n\t\t\t<span class=\"work-block__title\">%s</span>\n\t\t\t<span class=\"work-block__tag mono\">%s</span>\n\t\t</span>\n",
		wp_kses_post( $threekg_block['title'] ),
		wp_kses_post( $threekg_block['tag'] )
	);
	echo "\t</a>\n";
	echo $threekg_close_pair;
}
?>
  </div>

  <div class="work__foot reveal">
    <span class="mono">(<?php echo esc_html( str_pad( (string) count( $threekg_blocks ), 2, '0', STR_PAD_LEFT ) ); ?>) <?php esc_html_e( 'featured projects', 'threekg' ); ?></span>
    <a class="mono" href="<?php echo esc_url( threekg_email_url() ); ?>"><?php esc_html_e( 'full archive on request &nearr;', 'threekg' ); ?></a>
  </div>
</section>
