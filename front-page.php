<?php
/**
 * Front page (EN). Thin wrapper around the shared homepage part so the French
 * homepage (tpl-home.php) renders the identical composition.
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
