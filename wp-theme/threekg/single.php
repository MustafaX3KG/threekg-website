<?php
/**
 * Single post template.
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
	<article <?php post_class( 'post' ); ?>>
		<header class="section-head reveal">
			<span class="mono">[post]</span>
			<span class="mono"><?php echo esc_html( get_the_date() ); ?></span>
			<h1><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post__thumbnail">
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
		<footer class="mono post__foot">
			<span><?php echo esc_html( get_the_author() ); ?></span>
			<?php if ( get_edit_post_link() ) : ?>
				<?php edit_post_link( __( 'Edit post', 'threekg' ) ); ?>
			<?php endif; ?>
		</footer>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;
?>
<?php
get_footer();
