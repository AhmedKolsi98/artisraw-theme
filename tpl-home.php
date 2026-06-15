<?php
/**
 * Template Name: Homepage
 *
 * Lets a Page (the /fr/ French home) render the full homepage composition —
 * front-page.php can only target the site's front page, not a sub-page.
 *
 * @package ArtisRaw
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
artisraw_set_hero_preload( '/assets/ar-hero-logs', '100vw', array( 600, 1200, 1800 ) );
get_header();
get_template_part( 'template-parts/home' );
get_footer();
