<?php
/**
 * Branded 404 template.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="error404">
	<p class="mono error404__kicker">(<?php esc_html_e( 'lost signal', 'threekg' ); ?>)</p>
	<h1 class="error404__code">404</h1>
	<p class="error404__copy">
		<?php esc_html_e( 'The page you asked for is not here — but the studio is.', 'threekg' ); ?>
	</p>
	<p>
		<a class="mono" href="<?php echo esc_url( threekg_section_url( 'top' ) ); ?>"><?php echo esc_html( __( '← back to index', 'threekg' ) ); ?></a>
	</p>
</section>
<?php
get_footer();
