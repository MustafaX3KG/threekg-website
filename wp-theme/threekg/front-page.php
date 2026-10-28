<?php
/**
 * Front page: renders the canonical one-page studio layout.
 *
 * @package ThreeKG
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/section/hero' );
get_template_part( 'template-parts/section/showreel' );
get_template_part( 'template-parts/section/marquee' );
get_template_part( 'template-parts/section/work' );
get_template_part( 'template-parts/section/clients' );
get_template_part( 'template-parts/section/studio' );
get_template_part( 'template-parts/section/services' );

get_footer();
