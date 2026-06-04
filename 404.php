<?php
/**
 * 404 — real 404 status (WP sets it) with search + key links (SPEC §3).
 * Refined further in Phase 4.
 *
 * @package ArtisRaw
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
artisraw_breadcrumbs();
?>
<div class="container section">
	<h1><?php esc_html_e( 'Page not found', 'artisraw' ); ?></h1>
	<p class="lead"><?php esc_html_e( 'That page has moved or never existed. Try a search, or jump to one of these:', 'artisraw' ); ?></p>
	<?php get_search_form(); ?>
	<ul class="error-links" role="list">
		<li><a href="<?php echo esc_url( home_url( '/olive-wood-wholesale-supplier/' ) ); ?>"><?php esc_html_e( 'Wholesale hub', 'artisraw' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/wholesale/' ) ); ?>"><?php esc_html_e( 'Catalogue', 'artisraw' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'artisraw' ); ?></a></li>
	</ul>
</div>
<?php
get_footer();
