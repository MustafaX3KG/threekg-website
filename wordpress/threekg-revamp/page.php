<?php get_header(); ?>
<main class="wp-content-shell" id="main">
  <?php while (have_posts()) : the_post(); ?>
    <article <?php post_class('wp-content-page'); ?>>
      <header><span class="mono">(page)</span><h1><?php the_title(); ?></h1></header>
      <div class="entry-content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; ?>
</main>
<?php get_footer(); ?>

