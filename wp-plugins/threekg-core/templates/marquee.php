<?php
$items = threekg_core_field( 'items', array() );
if ( ! is_array( $items ) || count( $items ) < 2 ) {
	$items = array( array( 'text' => 'Brand Identity' ), array( 'text' => 'Web Design' ), array( 'text' => 'Creative Development' ), array( 'text' => 'Art Direction' ), array( 'text' => 'Motion' ) );
}
?>
<section class="marquee" aria-hidden="true"><div class="marquee__track">
<?php for ( $track = 0; $track < 2; $track++ ) : ?><span class="marquee__set"><?php foreach ( $items as $item ) : $text = trim( (string) ( $item['text'] ?? '' ) ); if ( '' !== $text ) : ?><span><?php echo esc_html( $text ); ?></span><i>&#10035;</i><?php endif; endforeach; ?></span><?php endfor; ?>
</div></section>

