<?php
/**
 * Blog archive / fallback index.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="archive">
	<header class="section-head reveal">
		<span class="mono">[posts]</span>
		<span class="mono"><?php esc_html_e( 'journal', 'threekg' ); ?></span>
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1><?php echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) ?: __( 'Journal', 'threekg' ) ); ?></h1>
		<?php else : ?>
			<h1><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<ul class="archive__list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li <?php post_class( 'archive__item reveal' ); ?>>
					<a class="archive__link" href="<?php the_permalink(); ?>">
						<span class="mono archive__date"><?php echo esc_html( get_the_date() ); ?></span>
						<span class="archive__title"><h2><?php the_title(); ?></h2></span>
						<?php if ( has_excerpt() ) : ?>
							<span class="mono archive__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endwhile; ?>
		</ul>

		<?php
		the_posts_pagination( array(
			'mid_size'  => 1,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
		) );
		?>
	<?php else : ?>
		<p class="mono archive__empty"><?php esc_html_e( 'Nothing here yet.', 'threekg' ); ?></p>
	<?php endif; ?>
</section>
<?php
get_footer();
