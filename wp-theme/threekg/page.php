<?php
/**
 * Static page template.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'page' ); ?>>
		<header class="section-head reveal">
			<span class="mono">[page]</span>
			<span class="mono"><?php echo esc_html( get_the_date() ); ?></span>
			<h1><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="page__thumbnail">
				<?php the_post_thumbnail( 'full' ); ?>
			</div>
		<?php endif; ?>
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages( array(
				'before' => '<nav class="page-links mono">',
				'after'  => '</nav>',
			) );
			?>
		</div>
		<?php if ( get_edit_post_link() ) : ?>
			<footer class="mono page__edit">
				<?php edit_post_link( __( 'Edit page', 'threekg' ) ); ?>
			</footer>
		<?php endif; ?>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;
?>
<?php
get_footer();
