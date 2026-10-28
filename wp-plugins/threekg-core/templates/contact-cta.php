<?php
/** @var array $values — assigned by the renderer (Elementor) or omitted (ACF blocks). */
$values   = isset( $values ) && is_array( $values ) ? $values : array();
$override = filter_var( threekg_core_value( $values, 'override_global', threekg_core_field( 'override_global', false ) ), FILTER_VALIDATE_BOOLEAN );
$label = threekg_core_value( $values, 'label', threekg_core_field( 'label', 'get in touch' ) );
$text = $override ? threekg_core_value( $values, 'cta_text', threekg_core_field( 'cta_text', threekg_core_option( 'cta_text', 'Start a project' ) ) ) : threekg_core_option( 'cta_text', 'Start a project' );
$email = sanitize_email( (string) threekg_core_option( 'email', 'hello@threekg.studio' ) );
$link = $override ? threekg_core_value( $values, 'cta_link', threekg_core_field( 'cta_link', 'mailto:' . $email ) ) : 'mailto:' . $email;
$columns = $override ? threekg_core_value( $values, 'columns', threekg_core_field( 'columns', array() ) ) : array();
?>
<section class="contact-cta" data-threekg-contact><span class="mono footer__cap reveal">(<?php echo esc_html( $label ); ?>)</span><a class="footer__cta reveal" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?><span class="footer__arrow">&rarr;</span></a><?php if ( is_array( $columns ) && $columns ) : ?><div class="footer__grid"><?php foreach ( $columns as $column ) : ?><div class="mono footer__col"><span class="footer__label"><?php echo esc_html( $column['heading'] ?? '' ); ?></span><span><?php echo wp_kses_post( $column['text'] ?? '' ); ?></span></div><?php endforeach; ?></div><?php endif; ?></section>

